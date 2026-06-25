<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../vendor/autoload.php';

class Auth {
    private $conn;

    public function __construct($connection) {
        $this->conn = $connection;
    }

    private function hashPassword($password) {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    private function generateToken() {
        return bin2hex(random_bytes(32));
    }

    /* ================= LOGIN ================= */
    public function login($username, $password) {
        $stmt = $this->conn->prepare("
            SELECT * FROM admin_users 
            WHERE username = ? AND status = 'active'
            LIMIT 1
        ");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$user || !password_verify($password, $user['password'])) {
            return false;
        }

        session_regenerate_id(true);

        $_SESSION['admin_id'] = $user['id'];
        $_SESSION['admin_username'] = $user['username'];
        $_SESSION['admin_nama'] = $user['nama'];

        return true;
    }

    /* ================= REGISTER ================= */
    public function register($data) {
        $username = trim($data['username']);
        $email = trim($data['email']);
        $no_telp = trim($data['no_telp']);
        $nama = trim($data['nama']);
        $password = $data['password'];

        if (strlen($password) < 6) {
            return ['success' => false, 'message' => 'Password minimal 6 karakter'];
        }

        $stmt = $this->conn->prepare("
            SELECT id FROM admin_users 
            WHERE username=? OR email=? OR no_telp=?
            LIMIT 1
        ");
        $stmt->bind_param("sss", $username, $email, $no_telp);
        $stmt->execute();
        
        if ($stmt->get_result()->num_rows > 0) {
            $stmt->close();
            return ['success' => false, 'message' => 'Data sudah terdaftar'];
        }
        $stmt->close();

        $hashed = $this->hashPassword($password);

        $stmt = $this->conn->prepare("
            INSERT INTO admin_users (username, email, no_telp, password, nama, status)
            VALUES (?, ?, ?, ?, ?, 'active')
        ");
        $stmt->bind_param("sssss", $username, $email, $no_telp, $hashed, $nama);
        $success = $stmt->execute();
        $stmt->close();

        return $success 
            ? ['success' => true, 'message' => 'Registrasi berhasil']
            : ['success' => false, 'message' => 'Gagal sistem'];
    }

    /* ================= FORGOT PASSWORD ================= */
    // Diperketat dengan validasi ganda: Email DAN Nomor Telepon wajib cocok
    public function forgotPassword($email, $no_telp) {
        $stmt = $this->conn->prepare("
            SELECT id FROM admin_users WHERE email=? AND no_telp=? LIMIT 1
        ");
        $stmt->bind_param("ss", $email, $no_telp);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$user) {
            return ['success' => false, 'message' => 'Kombinasi email dan nomor telepon tidak ditemukan'];
        }

        $token = $this->generateToken();
        // Menggunakan current_timestamp via PHP untuk waktu kadaluarsa (+15 menit)
        $expires = date('Y-m-d H:i:s', strtotime('+15 minutes'));

        $stmt = $this->conn->prepare("
            INSERT INTO password_resets (email, token, expires)
            VALUES (?, ?, ?)
            ON DUPLICATE KEY UPDATE token=?, expires=?
        ");
        $stmt->bind_param("sssss", $email, $token, $expires, $token, $expires);
        $stmt->execute();
        $stmt->close();

        return $this->sendResetEmail($email, $token)
            ? ['success' => true, 'message' => 'Link reset password berhasil dikirim ke email Anda.']
            : ['success' => false, 'message' => 'Gagal mengirim email. Periksa konfigurasi SMTP server Anda.'];
    }

    /* ================= EMAIL SMTP CONFIGURATION ================= */
    private function sendResetEmail($email, $token) {
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            
            // ⚠️ GANTI DENGAN KREDENSIAL REAL AKUN GOOGLE ANDA
            $mail->Username   = ''; 
            $mail->Password   = ''; // Gunakan 16 digit App Password dari Google, BUKAN password akun biasa
            
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            $mail->setFrom('KREDE_EMAIL_KAMU@gmail.com', 'PT Cipta Unggul - Admin');
            $mail->addAddress($email);

            // Sesuaikan domain aplikasi web lokal Anda
            $link = "http://localhost/KPAI/reset.php?token=$token";

            $mail->isHTML(true);
            $mail->Subject = "Reset Password Request - PT Cipta Unggul";
            $mail->Body    = "
                <div style='font-family: Arial, sans-serif; padding: 20px; color: #1e293b;'>
                    <h2 style='color: #0b1f3a;'>Permintaan Reset Password</h2>
                    <p>Kami menerima permintaan untuk mereset password akun admin Anda.</p>
                    <p>Silakan klik tombol atau link di bawah ini untuk mengatur ulang password Anda:</p>
                    <div style='margin: 25px 0;'>
                        <a href='$link' style='background-color: #1a56db; color: white; padding: 12px 24px; text-decoration: none; border-radius: 8px; font-weight: bold;'>Reset Password</a>
                    </div>
                    <p style='color: #64748b; font-size: 13px;'>Link ini berlaku selama 15 menit. Jika Anda tidak merasa melakukan tindakan ini, abaikan email ini.</p>
                </div>
            ";

            $mail->send();
            return true;

        } catch (Exception $e) {
            // Log error ditulis ke file server logs agar tidak bocor ke client screen
            error_log("PHPMailer Error: " . $mail->ErrorInfo);
            return false;
        }
    }

    public function getUser() {
        if (empty($_SESSION['admin_id'])) {
            return null;
        }

        $stmt = $this->conn->prepare("
            SELECT * FROM admin_users 
            WHERE id = ? AND status = 'active'
        ");
        $stmt->bind_param("i", $_SESSION['admin_id']);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $user;
    }

    /* ================= FORGOT PASSWORD SIMPLIFIED ================= */
    public function verifyAdminBeforeReset($email, $no_telp) {
        $stmt = $this->conn->prepare("
            SELECT id FROM admin_users WHERE email = ? AND no_telp = ? LIMIT 1
        ");
        $stmt->bind_param("ss", $email, $no_telp);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if ($user) {
            return ['success' => true, 'message' => 'Data cocok'];
        } else {
            return ['success' => false, 'message' => 'Kombinasi Email dan No. Telpon salah!'];
        }
    }

    public function isLoggedIn() {
        return isset($_SESSION['admin_id']);
    }

    public function logout() {
        session_destroy();
    }
}
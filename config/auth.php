<?php
/**
 * Class Auth — Autentikasi & RBAC
 * PT Cipta Unggul Lintas Samudra
 *
 * Session yang digunakan:
 *   $_SESSION['admin_id']       — ID admin yang login
 *   $_SESSION['admin_username'] — Username admin
 *   $_SESSION['admin_nama']     — Nama lengkap admin
 *   $_SESSION['admin_role']     — Role: 'superadmin' | 'admin'
 */
class Auth
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    /* -------------------------------------------------------
       LOGIN
    ------------------------------------------------------- */
    public function login(string $username, string $password): bool
    {
        $stmt = $this->conn->prepare(
            "SELECT id, nama, username, password, role, status
             FROM admin_users
             WHERE username = ?
             LIMIT 1"
        );
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $user   = $result->fetch_assoc();
        $stmt->close();

        if (!$user || $user['status'] !== 'active') {
            return false;
        }

        if (!password_verify($password, $user['password'])) {
            return false;
        }

        // Simpan data ke session
        $_SESSION['admin_id']       = $user['id'];
        $_SESSION['admin_username'] = $user['username'];
        $_SESSION['admin_nama']     = $user['nama'];
        $_SESSION['admin_role']     = $user['role'];

        return true;
    }

    /* -------------------------------------------------------
       LOGOUT
    ------------------------------------------------------- */
    public function logout(): void
    {
        session_unset();
        session_destroy();
    }

    /* -------------------------------------------------------
       CEK STATUS LOGIN
    ------------------------------------------------------- */
    public function isLoggedIn(): bool
    {
        return !empty($_SESSION['admin_id']);
    }

    /* -------------------------------------------------------
       CEK ROLE
    ------------------------------------------------------- */
    public function isSuperadmin(): bool
    {
        return isset($_SESSION['admin_role']) && $_SESSION['admin_role'] === 'superadmin';
    }

    public function hasRole(string $role): bool
    {
        return isset($_SESSION['admin_role']) && $_SESSION['admin_role'] === $role;
    }

    /* -------------------------------------------------------
       REQUIRE LOGIN — redirect jika belum login
    ------------------------------------------------------- */
    public function requireLogin(): void
    {
        if (!$this->isLoggedIn()) {
            header("Location: login.php");
            exit;
        }
    }

    /* -------------------------------------------------------
       REQUIRE SUPERADMIN — redirect jika bukan superadmin
    ------------------------------------------------------- */
    public function requireSuperadmin(): void
    {
        $this->requireLogin();
        if (!$this->isSuperadmin()) {
            $_SESSION['error'] = 'Akses ditolak. Halaman ini hanya untuk Superadmin.';
            header("Location: admin.php");
            exit;
        }
    }

    /* -------------------------------------------------------
       GET USER — data admin yang sedang login
    ------------------------------------------------------- */
    public function getUser(): array
    {
        if (!$this->isLoggedIn()) {
            return [];
        }

        return [
            'id'       => $_SESSION['admin_id'],
            'username' => $_SESSION['admin_username'],
            'nama'     => $_SESSION['admin_nama'],
            'role'     => $_SESSION['admin_role'],
        ];
    }

    /* -------------------------------------------------------
       REGISTER — digunakan oleh halaman register publik
       (role default: admin, hanya superadmin yg bisa set superadmin)
    ------------------------------------------------------- */
    public function register(array $data): array
    {
        $nama     = trim($data['nama']     ?? '');
        $username = trim($data['username'] ?? '');
        $email    = trim($data['email']    ?? '');
        $no_telp  = trim($data['no_telp']  ?? '');
        $password = $data['password']      ?? '';
        // Role hanya bisa superadmin jika yang mendaftarkan adalah superadmin
        $role     = ($this->isSuperadmin() && isset($data['role']) && $data['role'] === 'superadmin')
                    ? 'superadmin'
                    : 'admin';

        if (empty($nama) || empty($username) || empty($email) || empty($password)) {
            return ['success' => false, 'message' => 'Semua field wajib diisi!'];
        }

        if (strlen($password) < 6) {
            return ['success' => false, 'message' => 'Password minimal 6 karakter!'];
        }

        // Cek duplikat username
        $stmt = $this->conn->prepare("SELECT id FROM admin_users WHERE username = ? LIMIT 1");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $stmt->close();
            return ['success' => false, 'message' => 'Username sudah digunakan!'];
        }
        $stmt->close();

        // Cek duplikat email
        $stmt = $this->conn->prepare("SELECT id FROM admin_users WHERE email = ? LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $stmt->close();
            return ['success' => false, 'message' => 'Email sudah terdaftar!'];
        }
        $stmt->close();

        $hashed = password_hash($password, PASSWORD_BCRYPT);

        $stmt = $this->conn->prepare(
            "INSERT INTO admin_users (nama, username, email, no_telp, password, role, status)
             VALUES (?, ?, ?, ?, ?, ?, 'active')"
        );
        $stmt->bind_param("ssssss", $nama, $username, $email, $no_telp, $hashed, $role);

        if ($stmt->execute()) {
            $stmt->close();
            return ['success' => true, 'message' => 'Akun berhasil dibuat!'];
        }

        $err = $stmt->error;
        $stmt->close();
        return ['success' => false, 'message' => 'Gagal mendaftar: ' . $err];
    }
}

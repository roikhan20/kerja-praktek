<?php
session_start();
include_once __DIR__ . '/../config/koneksi.php';
include_once __DIR__ . '/../config/auth.php';

$auth = new Auth($conn);
$message = '';
$type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $no_telp = trim($_POST['no_telp']);

    $result = $auth->verifyAdminBeforeReset($email, $no_telp);
    
    if ($result['success']) {
        // Jika data valid, simpan email di session dan langsung alihkan ke halaman reset password
        $_SESSION['reset_email_verified'] = $email;
        header("Location: reset.php");
        exit;
    } else {
        $message = $result['message'];
        $type = 'error';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reset Password - PT Cipta Unggul</title>
  <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;600;700;800&family=DM+Sans:wght@400;500&display=swap" rel="stylesheet">
  <style>
    :root {
      --navy: #0B1F3A; --blue: #1A56DB; --text: #1E293B; --muted: #64748B; --white: #FFFFFF; --radius: 12px; --radius-lg: 20px;
    }
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: 'DM Sans', sans-serif; background: linear-gradient(135deg, #F8FAFF 0%, #E0E7FF 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 1rem; }
    .login-container { background: var(--white); border-radius: var(--radius-lg); box-shadow: 0 25px 50px -12px rgba(0,0,0,0.15); overflow: hidden; max-width: 480px; width: 100%; }
    .login-header { background: linear-gradient(135deg, var(--navy), #122B52); color: white; padding: 2.5rem 2rem; text-align: center; }
    .login-header h1 { font-family: 'Sora', sans-serif; font-size: 26px; font-weight: 800; margin-bottom: 0.5rem; }
    .login-body { padding: 2.5rem; }
    .form-group { margin-bottom: 1.5rem; }
    .form-label { display: block; font-weight: 600; color: var(--text); margin-bottom: 0.5rem; font-size: 14px; }
    .form-input { width: 100%; padding: 14px 16px; border: 2px solid #E2E8F0; border-radius: var(--radius); font-size: 15px; }
    .btn-login { width: 100%; background: var(--blue); color: white; border: none; padding: 16px; border-radius: var(--radius); font-weight: 700; font-size: 16px; cursor: pointer; box-shadow: 0 6px 20px rgba(26,86,219,0.3); }
    .divider { text-align: center; margin: 2rem 0; position: relative; color: var(--muted); font-size: 14px; }
    .divider::before { content: ''; position: absolute; top: 50%; left: 0; right: 0; height: 1px; background: #E2E8F0; }
    .divider span { background: var(--white); padding: 0 1rem; position: relative; }
    .auth-links { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
    .auth-link { text-align: center; padding: 14px 12px; border: 2px solid #E2E8F0; border-radius: var(--radius); text-decoration: none; font-weight: 600; color: var(--text); font-size: 14px; }
    .error { background: #FEE2E2; color: #991B1B; padding: 1rem; border-radius: var(--radius); margin-bottom: 1.5rem; font-size: 14px; border-left: 4px solid #DC2626; }
  </style>
</head>
<body>
  <div class="login-container">
    <div class="login-header">
      <h1>🔐 Verifikasi Admin</h1>
      <p>Masukkan data Anda untuk ganti password</p>
    </div>
    
    <div class="login-body">
      <?php if ($message): ?>
        <div class="error">❌ <?= htmlspecialchars($message) ?></div>
      <?php endif; ?>
      
      <form method="POST">
        <div class="form-group">
          <label class="form-label">📧 Email Terdaftar</label>
          <input type="email" name="email" class="form-input" placeholder="nama@ciptaunggul.com" required>
        </div>
        
        <div class="form-group">
          <label class="form-label">📱 No. Telpon Terdaftar</label>
          <input type="tel" name="no_telp" class="form-input" placeholder="081234567xx" required>
        </div>
        
        <button type="submit" class="btn-login">
          🛡️ Verifikasi Data Saya
        </button>
      </form>
      
      <div class="divider"><span>Menu Lain</span></div>
      <div class="auth-links">
        <a href="login.php" class="auth-link">🔐 Login</a>
        <a href="register.php" class="auth-link">📝 Daftar</a>
      </div>
    </div>
  </div>
</body>
</html>
<?php
session_start();
include_once __DIR__ . '/../config/koneksi.php';
include_once __DIR__ . '/../config/auth.php';

$auth = new Auth($conn);
$error = null;

// Jika admin sudah login, langsung alihkan ke dashboard (Good Practice)
if ($auth->isLoggedIn()) {
    header("Location: admin.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if ($auth->login($username, $password)) {
        header("Location: admin.php");
        exit;
    } else {
        $error = "Username atau password salah!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login Admin - PT Cipta Unggul</title>
  <link rel="icon" href="../logo2.png" type="image/png">
  <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;600;700;800&family=DM+Sans:wght@400;500;700&display=swap" rel="stylesheet">
  <style>
    :root {
      --navy: #0B1F3A; 
      --blue: #1A56DB; 
      --blue-focus: #2563EB;
      --accent: #F59E0B; 
      --green: #16A34A;
      --text: #1E293B; 
      --muted: #64748B; 
      --white: #FFFFFF; 
      --radius: 14px;
      --radius-lg: 24px; /* FIX: Variabel yang sebelumnya hilang */
    }

    * { box-sizing: border-box; margin: 0; padding: 0; }

    body { 
      font-family: 'DM Sans', sans-serif; 
      background: radial-gradient(circle at top right, #E0E7FF 0%, #F8FAFF 50%, #EEF2FF 100%);
      min-height: 100vh; 
      display: flex; 
      align-items: center; 
      justify-content: center;
      padding: 1.5rem;
      position: relative;
      overflow: hidden;
    }

    /* Dekorasi Background Abstrak Modern */
    body::before, body::after {
      content: '';
      position: absolute;
      border-radius: 50%;
      filter: blur(80px);
      z-index: -1;
      opacity: 0.5;
    }
    body::before { width: 300px; height: 300px; background: #3B82F6; top: -50px; right: -50px; }
    body::after { width: 250px; height: 250px; background: #60A5FA; bottom: -50px; left: -50px; }

    .login-container {
      background: rgba(255, 255, 255, 0.9);
      backdrop-filter: blur(10px);
      -webkit-backdrop-filter: blur(10px);
      border-radius: var(--radius-lg);
      box-shadow: 0 20px 40px -15px rgba(11, 31, 58, 0.1), 0 0 0 1px rgba(255, 255, 255, 0.6);
      overflow: hidden; 
      max-width: 440px; 
      width: 100%;
      transform: translateY(0);
      transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
      animation: slideUp 0.6s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .login-container:hover {
      transform: translateY(-4px);
      box-shadow: 0 30px 60px -15px rgba(11, 31, 58, 0.15);
    }

    .login-header {
      background: linear-gradient(135deg, var(--navy) 0%, #162E52 100%);
      color: white; 
      padding: 3rem 2rem 2.5rem 2rem; 
      text-align: center;
      position: relative;
    }

    .login-header h1 { 
      font-family: 'Sora', sans-serif; 
      font-size: 26px; 
      font-weight: 800; 
      margin-bottom: 0.5rem;
      letter-spacing: -0.5px;
    }

    .login-header p { 
      opacity: 0.85; 
      font-size: 14px; 
      font-weight: 500;
      color: #93C5FD;
    }

    .login-body { padding: 2.5rem 2.25rem; }

    .form-group { 
      margin-bottom: 1.5rem; 
      position: relative;
    }

    .form-label { 
      display: block; 
      font-weight: 700; 
      color: var(--text); 
      margin-bottom: 0.6rem; 
      font-size: 13px;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .input-wrapper { position: relative; }

    .form-input { 
      width: 100%; 
      padding: 15px 16px; 
      border: 2px solid #E2E8F0; 
      border-radius: var(--radius);
      font-size: 15px; 
      font-weight: 500;
      transition: all 0.25s ease; 
      background: #F8FAFF;
      color: var(--text);
    }

    .form-input:focus { 
      outline: none; 
      border-color: var(--blue); 
      background: var(--white);
      box-shadow: 0 0 0 4px rgba(26, 86, 219, 0.12); 
    }

    .btn-login {
      width: 100%; 
      background: linear-gradient(135deg, var(--blue) 0%, #2563EB 100%);
      color: white; 
      border: none; 
      padding: 16px; 
      border-radius: var(--radius);
      font-weight: 700; 
      font-size: 16px; 
      cursor: pointer; 
      transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
      box-shadow: 0 6px 20px rgba(26, 86, 219, 0.25);
      margin-top: 0.5rem;
    }

    .btn-login:hover { 
      background: linear-gradient(135deg, #2563EB 0%, #1D4ED8 100%);
      transform: translateY(-2px); 
      box-shadow: 0 10px 25px rgba(26, 86, 219, 0.35); 
    }

    .btn-login:active {
      transform: translateY(0);
    }

    .divider { 
      text-align: center; 
      margin: 2.25rem 0 1.75rem 0; 
      position: relative; 
      color: var(--muted);
      font-size: 13px;
      font-weight: 500;
    }

    .divider::before {
      content: ''; 
      position: absolute; 
      top: 50%; 
      left: 0; 
      right: 0; 
      height: 1px;
      background: #E2E8F0;
      z-index: 1;
    }

    .divider span { 
      background: #FDFDFD; 
      padding: 0 1.25rem; 
      position: relative;
      z-index: 2;
    }

    /* FIX: Penyeimbangan layouting tombol navigasi di tengah menggunakan Flexbox */
    .auth-links { 
      display: flex; 
      justify-content: center; 
      gap: 1rem; 
    }

    .auth-link {
      display: inline-block;
      text-align: center; 
      padding: 12px 24px; 
      border: 2px solid #E2E8F0;
      border-radius: var(--radius); 
      text-decoration: none; 
      font-weight: 700;
      color: var(--muted); 
      transition: all 0.2s ease; 
      font-size: 13px;
      background: var(--white);
    }

    .auth-link:hover { 
      border-color: var(--blue); 
      color: var(--blue); 
      background: #EFF6FF;
      transform: translateY(-1px); 
    }

    .error { 
      background: #FEE2E2; 
      color: #DC2626; 
      padding: 1rem 1.25rem; 
      border-radius: var(--radius); 
      margin-bottom: 1.5rem; 
      border-left: 4px solid #DC2626; 
      font-size: 14px;
      font-weight: 500;
      display: flex;
      align-items: center;
      gap: 0.5rem;
      animation: shake 0.4s linear;
    }

    /* Keyframes Animasi */
    @keyframes slideUp {
      from { opacity: 0; transform: translateY(30px); }
      to { opacity: 1; transform: translateY(0); }
    }

    @keyframes shake {
      0%, 100% { transform: translateX(0); }
      25% { transform: translateX(-6px); }
      75% { transform: translateX(6px); }
    }

    @media (max-width: 480px) { 
      .login-body { padding: 2rem 1.5rem; } 
      .login-header { padding: 2.5rem 1.5rem 2rem 1.5rem; }
    }
  </style>
</head>
<body>

  <div class="login-container">
    <div class="login-header">
      <h1>Admin Panel</h1>
      <p>PT Cipta Unggul Lintas Samudra</p>
    </div>
    
    <div class="login-body">
      <?php if (!empty($error)): ?>
        <div class="error">
          <span>❌</span> <?= htmlspecialchars($error) ?>
        </div>
      <?php endif; ?>
      
      <form method="POST" action="">
        <div class="form-group">
          <label class="form-label">Username</label>
          <div class="input-wrapper">
            <input type="text" name="username" class="form-input" required autocomplete="username">
          </div>
        </div>
        
        <div class="form-group">
          <label class="form-label">Password</label>
          <div class="input-wrapper">
            <input type="password" name="password" class="form-input" required autocomplete="current-password">
          </div>
        </div>
        
        <button type="submit" class="btn-login">Masuk Admin</button>
      </form>
      
    </div>
  </div>
</body>
</html>
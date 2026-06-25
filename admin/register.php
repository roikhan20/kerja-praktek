<?php
session_start();
include_once __DIR__ . '/../config/koneksi.php';
include_once __DIR__ . '/../config/auth.php';

$auth = new Auth($conn);

$message = '';
$type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = $auth->register($_POST);
    $message = $result['message'];
    $type = $result['success'] ? 'success' : 'error';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register Admin - PT Cipta Unggul</title>
  <link rel="icon" href="../logo2.png" type="image/png">

  <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;600;700;800&family=DM+Sans:wght@400;500;700&display=swap" rel="stylesheet">
  <style>
    :root {
      --navy: #0B1F3A; 
      --blue: #1A56DB; 
      --accent: #F59E0B; 
      --green: #16A34A;
      --text: #1E293B; 
      --muted: #64748B; 
      --white: #FFFFFF; 
      --radius: 14px;
      --radius-lg: 24px;
    }

    * { box-sizing: border-box; margin: 0; padding: 0; }

    /* FIX: Menyembunyikan scrollbar untuk standar browser modern (Firefox) */
    html, body { 
      font-family: 'DM Sans', sans-serif; 
      background: radial-gradient(circle at top right, #E0E7FF 0%, #F8FAFF 50%, #EEF2FF 100%);
      min-height: 100vh;
      color: var(--text);
      scrollbar-width: none; /* Menyembunyikan scrollbar di Firefox */
    }

    /* FIX: Menyembunyikan scrollbar untuk Chrome, Safari, dan Opera */
    html::-webkit-scrollbar, 
    body::-webkit-scrollbar,
    .page-wrapper::-webkit-scrollbar {
      display: none; /* Bar gulir dihilangkan dari visual layar */
    }

    .page-wrapper {
      position: relative;
      min-height: 100vh;
      display: flex; 
      align-items: center; 
      justify-content: center;
      padding: 3rem 1.5rem; 
      overflow-x: hidden; 
      overflow-y: auto;   /* Fungsi internal scroll tetap aktif */
      z-index: 1;
      -ms-overflow-style: none; /* Menyembunyikan scrollbar di IE dan Edge lama */
    }

    /* Dekorasi Background Eksklusif */
    .page-wrapper::before, .page-wrapper::after {
      content: '';
      position: absolute;
      border-radius: 50%;
      filter: blur(80px);
      z-index: -1;
      opacity: 0.5;
    }
    .page-wrapper::before { width: 350px; height: 350px; background: #3B82F6; top: -70px; right: -70px; }
    .page-wrapper::after { width: 300px; height: 300px; background: #10B981; bottom: -70px; left: -70px; }

    .register-container {
      background: rgba(255, 255, 255, 0.85);
      backdrop-filter: blur(12px);
      -webkit-backdrop-filter: blur(12px);
      border-radius: var(--radius-lg);
      box-shadow: 0 20px 40px -15px rgba(11, 31, 58, 0.1), 0 0 0 1px rgba(255, 255, 255, 0.6);
      overflow: hidden; 
      max-width: 520px; 
      width: 100%;
      transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
      animation: slideUp 0.6s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .register-container:hover {
      transform: translateY(-4px);
      box-shadow: 0 30px 60px -15px rgba(11, 31, 58, 0.15);
    }

    .register-header {
      background: linear-gradient(135deg, var(--navy) 0%, #162E52 100%);
      color: white; 
      padding: 2.5rem 2rem; 
      text-align: center;
    }

    .register-header h1 { 
      font-family: 'Sora', sans-serif; 
      font-size: 26px; 
      font-weight: 800; 
      margin-bottom: 0.5rem;
      letter-spacing: -0.5px;
    }

    .register-header p { 
      opacity: 0.85; 
      font-size: 14px; 
      color: #93C5FD;
    }

    .register-body { padding: 2.25rem 2.25rem; }

    .form-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 1.25rem;
    }

    .form-group { 
      margin-bottom: 1.25rem; 
    }

    .full-width {
      grid-column: span 2;
    }

    .form-label { 
      display: block; 
      font-weight: 700; 
      color: var(--text); 
      margin-bottom: 0.5rem; 
      font-size: 12px;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .form-input { 
      width: 100%; 
      padding: 13px 16px; 
      border: 2px solid #E2E8F0; 
      border-radius: var(--radius);
      font-size: 14px; 
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

    .btn-register {
      width: 100%; 
      background: linear-gradient(135deg, var(--green) 0%, #22C55E 100%);
      color: white; 
      border: none; 
      padding: 15px; 
      border-radius: var(--radius);
      font-weight: 700; 
      font-size: 16px; 
      cursor: pointer; 
      transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
      box-shadow: 0 6px 20px rgba(22, 163, 74, 0.25);
      margin-top: 0.5rem;
    }

    .btn-register:hover { 
      background: linear-gradient(135deg, #22C55E 0%, #15803D 100%);
      transform: translateY(-2px); 
      box-shadow: 0 10px 25px rgba(22, 163, 74, 0.35); 
    }

    .divider { 
      text-align: center; 
      margin: 2rem 0 1.5rem 0; 
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

    .auth-links { 
      display: flex; 
      justify-content: center; 
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

    .flash-success { 
      background: #D1FAE5; 
      color: #065F46; 
      padding: 1rem 1.25rem; 
      border-radius: var(--radius); 
      margin-bottom: 1.5rem; 
      border-left: 4px solid #10B981;
      font-size: 14px;
      font-weight: 500;
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
      animation: shake 0.4s linear;
    }

    @keyframes slideUp {
      from { opacity: 0; transform: translateY(30px); }
      to { opacity: 1; transform: translateY(0); }
    }

    @keyframes shake {
      0%, 100% { transform: translateX(0); }
      25% { transform: translateX(-6px); }
      75% { transform: translateX(6px); }
    }

    @media (max-width: 560px) { 
      .form-grid { grid-template-columns: 1fr; gap: 0; }
      .full-width { grid-column: span 1; }
      .register-body { padding: 1.75rem 1.25rem; } 
    }
  </style>
</head>
<body>

  <div class="page-wrapper">
    <div class="register-container">
      <div class="register-header">
        <h1>📝 Daftar Admin Baru</h1>
        <p>Lengkapi formulir di bawah untuk membuat akses</p>
      </div>
      
      <div class="register-body">
        <?php if ($message): ?>
          <div class="<?= $type == 'success' ? 'flash-success' : 'error' ?>">
            <span><?= $type == 'success' ? '✅' : '❌' ?></span> <?= htmlspecialchars($message) ?>
            <?php if ($type == 'success'): ?>
              <br><style>.register-body form { display:none; }</style>
              <small style="display:block; margin-top:8px;">Silakan <a href="login.php" style="color:#065F46; font-weight:700; text-decoration:underline;">Login di Sini</a></small>
            <?php endif; ?>
          </div>
        <?php endif; ?>
        
        <form method="POST" action="">
          <div class="form-grid">
            
            <div class="form-group full-width">
              <label class="form-label">Nama Lengkap <span class="required">*</span></label>
              <input type="text" name="nama" class="form-input" required>
            </div>
            
            <div class="form-group">
              <label class="form-label">Username <span class="required">*</span></label>
              <input type="text" name="username" class="form-input" required autocomplete="username">
            </div>
            
            <div class="form-group">
              <label class="form-label">No. Telpon <span class="required">*</span></label>
              <input type="tel" name="no_telp" class="form-input" required>
            </div>
            
            <div class="form-group full-width">
              <label class="form-label">Email  <span class="required">*</span></label>
              <input type="email" name="email" class="form-input" required>
            </div>
            
            <div class="form-group full-width">
              <label class="form-label">Password <span class="required">*</span> (Minimal 6 Karakter)</label>
              <input type="password" name="password" class="form-input" minlength="6" required>
            </div>
            
          </div>
          
          <button type="submit" class="btn-register">💾 Daftarkan Akun</button>
        </form>
        
        <div class="divider">
          <span>Sudah Terdaftar?</span>
        </div>
        
        <div class="auth-links">
          <a href="login.php" class="auth-link">Masuk Ke Login Admin</a>
        </div>
      </div>
    </div>
  </div>

</body>
</html>
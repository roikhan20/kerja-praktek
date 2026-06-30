<?php
/**
 * admin/tambah-admin.php — Form Tambah Admin Baru
 * Hanya dapat diakses oleh Superadmin
 */
session_start();

include_once __DIR__ . '/../config/koneksi.php';
include_once __DIR__ . '/../config/auth.php';

$auth = new Auth($conn);
$auth->requireSuperadmin();

$user = $auth->getUser();

$errors  = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nama     = trim($_POST['nama']     ?? '');
    $username = trim($_POST['username'] ?? '');
    $email    = trim($_POST['email']    ?? '');
    $no_telp  = trim($_POST['no_telp']  ?? '');
    $password = $_POST['password']      ?? '';
    $konfirm  = $_POST['konfirmasi']    ?? '';
    $role     = in_array($_POST['role'] ?? '', ['superadmin', 'admin']) ? $_POST['role'] : 'admin';
    $status   = in_array($_POST['status'] ?? '', ['active', 'inactive']) ? $_POST['status'] : 'active';

    // Validasi
    if (empty($nama))     $errors[] = 'Nama lengkap wajib diisi.';
    if (empty($username)) $errors[] = 'Username wajib diisi.';
    if (empty($email))    $errors[] = 'Email wajib diisi.';
    if (empty($no_telp))  $errors[] = 'No. telepon wajib diisi.';
    if (strlen($password) < 6) $errors[] = 'Password minimal 6 karakter.';
    if ($password !== $konfirm) $errors[] = 'Konfirmasi password tidak cocok.';

    // Cek duplikat username
    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT id FROM admin_users WHERE username = ? LIMIT 1");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) $errors[] = 'Username sudah digunakan, pilih yang lain.';
        $stmt->close();
    }

    // Cek duplikat email
    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT id FROM admin_users WHERE email = ? LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) $errors[] = 'Email sudah terdaftar.';
        $stmt->close();
    }

    // Simpan
    if (empty($errors)) {
        $hashed = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $conn->prepare(
            "INSERT INTO admin_users (nama, username, email, no_telp, password, role, status)
             VALUES (?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->bind_param("sssssss", $nama, $username, $email, $no_telp, $hashed, $role, $status);

        if ($stmt->execute()) {
            $_SESSION['success'] = "Admin <strong>" . htmlspecialchars($nama) . "</strong> berhasil ditambahkan!";
            header("Location: kelola-admin.php");
            exit;
        } else {
            $errors[] = 'Gagal menyimpan: ' . $stmt->error;
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Tambah Admin - PT Cipta Unggul</title>
  <link rel="icon" href="../logo2.png" type="image/png">
  <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;600;700;800&family=DM+Sans:wght@400;500&display=swap" rel="stylesheet">
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    :root {
      --navy: #0B1F3A; --navy-2: #122B52; --blue: #1A56DB;
      --green: #16A34A; --green-d: #15803D; --red: #DC2626;
      --text: #1E293B; --muted: #64748B; --white: #FFFFFF;
      --radius: 12px; --radius-lg: 20px;
    }
    body { font-family: 'DM Sans', sans-serif; background: #F8FAFF; color: var(--text); line-height: 1.6; }
    h1, h2, h3 { font-family: 'Sora', sans-serif; }

    .nav {
      position: sticky; top: 0; z-index: 100;
      background: rgba(11,31,58,0.97); backdrop-filter: blur(12px);
      border-bottom: 1px solid rgba(255,255,255,0.08);
    }
    .nav-inner {
      max-width: 1200px; margin: 0 auto;
      display: flex; align-items: center; justify-content: space-between;
      padding: 0 1.5rem; height: 68px;
    }
    .nav-left { display: flex; align-items: center; gap: 1.5rem; }
    .nav-brand { display: flex; flex-direction: column; text-decoration: none; color: white; font-weight: 700; }
    .nav-brand strong { font-size: 15px; }
    .nav-brand span   { font-size: 11px; opacity: 0.8; }
    .nav-link {
      color: rgba(255,255,255,0.85); text-decoration: none; font-weight: 600;
      font-size: 13px; padding: 8px 16px; border-radius: 8px;
      border: 1px solid rgba(255,255,255,0.2);
      background: rgba(255,255,255,0.06); transition: all .2s;
    }
    .nav-link:hover { background: rgba(255,255,255,0.15); }
    .nav-right { display: flex; gap: 1rem; align-items: center; }

    .role-badge {
      display: inline-block; font-size: 10px; font-weight: 700;
      padding: 2px 8px; border-radius: 999px;
      text-transform: uppercase; letter-spacing: 0.5px;
    }
    .role-badge.superadmin { background: #F59E0B; color: #78350F; }

    .main-content { max-width: 600px; margin: 2rem auto; padding: 0 1.5rem; }

    .breadcrumb { display: flex; align-items: center; gap: 8px; margin-bottom: 1.5rem; font-size: 13px; color: var(--muted); }
    .breadcrumb a { color: var(--blue); text-decoration: none; font-weight: 600; }
    .breadcrumb a:hover { text-decoration: underline; }
    .breadcrumb span { color: var(--muted); }

    .card { background: white; border-radius: var(--radius-lg); box-shadow: 0 4px 24px rgba(11,31,58,0.07); overflow: hidden; }
    .card-header {
      background: linear-gradient(135deg, var(--navy) 0%, var(--navy-2) 100%);
      color: white; padding: 1.5rem 2rem;
    }
    .card-header h2 { font-size: 22px; font-weight: 800; }
    .card-header p  { font-size: 13px; opacity: 0.8; margin-top: 4px; }

    .card-body { padding: 2rem; }

    .alert-error {
      background: #FEE2E2; color: #991B1B; border-left: 4px solid #DC2626;
      padding: 1rem 1.25rem; border-radius: var(--radius);
      margin-bottom: 1.5rem; font-size: 14px;
    }
    .alert-error ul { margin: 0.5rem 0 0 1.25rem; }
    .alert-error li { margin-bottom: 4px; }

    .form-group { margin-bottom: 1.25rem; }
    .form-label {
      display: block; font-weight: 700; color: var(--text);
      margin-bottom: 0.5rem; font-size: 12px;
      text-transform: uppercase; letter-spacing: 0.5px;
    }
    .required { color: var(--red); }
    .form-input, .form-select {
      width: 100%; padding: 13px 16px;
      border: 2px solid #E2E8F0; border-radius: var(--radius);
      font-size: 14px; font-weight: 500;
      transition: all 0.25s; background: #F8FAFF; color: var(--text);
      font-family: inherit;
    }
    .form-input:focus, .form-select:focus {
      outline: none; border-color: var(--blue);
      background: white; box-shadow: 0 0 0 4px rgba(26,86,219,0.12);
    }
    .form-hint { font-size: 12px; color: var(--muted); margin-top: 5px; }

    .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; }

    .role-info {
      background: #FFF7ED; border: 1px solid #FED7AA;
      border-radius: var(--radius); padding: 1rem 1.25rem;
      font-size: 13px; color: #92400E; margin-bottom: 1.5rem;
    }
    .role-info strong { display: block; margin-bottom: 4px; }

    .btn-actions { display: flex; gap: 1rem; margin-top: 0.5rem; }
    .btn-primary {
      flex: 1; background: linear-gradient(135deg, var(--green) 0%, #22C55E 100%);
      color: white; border: none; padding: 14px;
      border-radius: var(--radius); font-weight: 700; font-size: 15px;
      cursor: pointer; transition: all .3s;
      box-shadow: 0 6px 20px rgba(22,163,74,0.25);
    }
    .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 10px 25px rgba(22,163,74,0.35); }
    .btn-cancel {
      padding: 14px 28px; border-radius: var(--radius);
      background: white; border: 2px solid #E2E8F0;
      color: var(--muted); font-weight: 700; font-size: 15px;
      text-decoration: none; transition: all .2s;
      display: flex; align-items: center; justify-content: center;
    }
    .btn-cancel:hover { border-color: var(--blue); color: var(--blue); }

    @media (max-width: 560px) { .form-row { grid-template-columns: 1fr; } }
  </style>
</head>
<body>

<nav class="nav">
  <div class="nav-inner">
    <div class="nav-left">
      <a href="admin.php" class="nav-brand">
        <strong>
          <?= htmlspecialchars($user['username']); ?>
          <span class="role-badge superadmin">Superadmin</span>
        </strong>
        <span>PT Cipta Unggul</span>
      </a>
      <a href="admin.php" class="nav-link">Alat Berat</a>
      <a href="kelola-admin.php" class="nav-link">Kelola Admin</a>
    </div>
    <div class="nav-right">
      <a href="admin.php?logout=1"
         style="color:rgba(255,255,255,0.9);padding:10px 20px;border-radius:8px;border:1px solid rgba(255,255,255,0.3);text-decoration:none;font-weight:600;font-size:13px;transition:all .2s;"
         onclick="return confirm('Yakin logout?')">Logout</a>
    </div>
  </div>
</nav>

<div class="main-content">

  <div class="breadcrumb">
    <a href="kelola-admin.php">Kelola Admin</a>
    <span>›</span>
    <span>Tambah Admin Baru</span>
  </div>

  <div class="card">
    <div class="card-header">
      <h2>Tambah Admin Baru</h2>
      <p>Buat akun admin untuk akses panel</p>
    </div>
    <div class="card-body">

      <?php if (!empty($errors)): ?>
        <div class="alert-error">
          <strong>❌ Terdapat kesalahan:</strong>
          <ul>
            <?php foreach ($errors as $e): ?>
              <li><?= htmlspecialchars($e); ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>

      <div class="role-info">
        <strong>Informasi Role</strong>
        <b>Admin</b> — hanya dapat mengelola data alat berat.<br>
        <b>Superadmin</b> — dapat mengelola data alat berat DAN manajemen akun admin.
      </div>

      <form method="POST">
        <div class="form-group">
          <label class="form-label">Nama Lengkap <span class="required">*</span></label>
          <input type="text" name="nama" class="form-input" required
                 value="<?= htmlspecialchars($_POST['nama'] ?? ''); ?>">
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Username <span class="required">*</span></label>
            <input type="text" name="username" class="form-input" required autocomplete="off"
                   value="<?= htmlspecialchars($_POST['username'] ?? ''); ?>">
          </div>
          <div class="form-group">
            <label class="form-label">No. Telepon <span class="required">*</span></label>
            <input type="tel" name="no_telp" class="form-input" required
                   value="<?= htmlspecialchars($_POST['no_telp'] ?? ''); ?>">
          </div>
        </div>

        <div class="form-group">
          <label class="form-label">Email <span class="required">*</span></label>
          <input type="email" name="email" class="form-input" required autocomplete="off"
                 value="<?= htmlspecialchars($_POST['email'] ?? ''); ?>">
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Password <span class="required">*</span></label>
            <input type="password" name="password" class="form-input" required minlength="6" autocomplete="new-password">
            <p class="form-hint">Minimal 6 karakter</p>
          </div>
          <div class="form-group">
            <label class="form-label">Konfirmasi Password <span class="required">*</span></label>
            <input type="password" name="konfirmasi" class="form-input" required autocomplete="new-password">
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Role <span class="required">*</span></label>
            <select name="role" class="form-select">
              <option value="admin"       <?= ($_POST['role'] ?? '') === 'admin'       ? 'selected' : ''; ?>>Admin</option>
              <option value="superadmin"  <?= ($_POST['role'] ?? '') === 'superadmin'  ? 'selected' : ''; ?>>Superadmin</option>
            </select>
          </div>
          <div class="form-group">
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
              <option value="active"   <?= ($_POST['status'] ?? '') !== 'inactive' ? 'selected' : ''; ?>>Aktif</option>
              <option value="inactive" <?= ($_POST['status'] ?? '') === 'inactive'  ? 'selected' : ''; ?>>Nonaktif</option>
            </select>
          </div>
        </div>

        <div class="btn-actions">
          <a href="kelola-admin.php" class="btn-cancel">Batal</a>
          <button type="submit" class="btn-primary">Simpan Admin</button>
        </div>
      </form>

    </div>
  </div>
</div>
</body>
</html>

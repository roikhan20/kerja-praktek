<?php
/**
 * admin/edit-admin.php — Edit Data & Reset Password Admin
 * Hanya dapat diakses oleh Superadmin
 *
 * GET  ?id=X          → tab edit data
 * GET  ?id=X&tab=password → tab reset password
 */
session_start();

include_once __DIR__ . '/../config/koneksi.php';
include_once __DIR__ . '/../config/auth.php';

$auth = new Auth($conn);
$auth->requireSuperadmin();

$user = $auth->getUser();

// Ambil id target
$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    header("Location: kelola-admin.php");
    exit;
}

// Fetch data admin target
$stmt = $conn->prepare("SELECT * FROM admin_users WHERE id = ? LIMIT 1");
$stmt->bind_param("i", $id);
$stmt->execute();
$target = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$target) {
    $_SESSION['error'] = 'Admin tidak ditemukan.';
    header("Location: kelola-admin.php");
    exit;
}

$tab    = ($_GET['tab'] ?? '') === 'password' ? 'password' : 'data';
$errors = [];

/* =========================
   PROSES EDIT DATA
========================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['aksi']) && $_POST['aksi'] === 'edit_data') {

    $nama     = trim($_POST['nama']     ?? '');
    $username = trim($_POST['username'] ?? '');
    $email    = trim($_POST['email']    ?? '');
    $no_telp  = trim($_POST['no_telp']  ?? '');
    $role     = in_array($_POST['role']   ?? '', ['superadmin', 'admin']) ? $_POST['role'] : 'admin';
    $status   = in_array($_POST['status'] ?? '', ['active', 'inactive'])  ? $_POST['status'] : 'active';

    if (empty($nama))     $errors[] = 'Nama lengkap wajib diisi.';
    if (empty($username)) $errors[] = 'Username wajib diisi.';
    if (empty($email))    $errors[] = 'Email wajib diisi.';

    // Cek tidak boleh downgrade superadmin terakhir
    if ($target['role'] === 'superadmin' && $role === 'admin') {
        $cek = $conn->query("SELECT COUNT(*) as jml FROM admin_users WHERE role = 'superadmin'");
        if ($cek->fetch_assoc()['jml'] <= 1) {
            $errors[] = 'Tidak dapat mengubah role. Harus ada minimal 1 superadmin!';
        }
    }

    // Cek duplikat username (kecuali milik sendiri)
    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT id FROM admin_users WHERE username = ? AND id != ? LIMIT 1");
        $stmt->bind_param("si", $username, $id);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) $errors[] = 'Username sudah digunakan akun lain.';
        $stmt->close();
    }

    // Cek duplikat email
    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT id FROM admin_users WHERE email = ? AND id != ? LIMIT 1");
        $stmt->bind_param("si", $email, $id);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) $errors[] = 'Email sudah digunakan akun lain.';
        $stmt->close();
    }

    if (empty($errors)) {
        $stmt = $conn->prepare(
            "UPDATE admin_users SET nama=?, username=?, email=?, no_telp=?, role=?, status=? WHERE id=?"
        );
        $stmt->bind_param("ssssssi", $nama, $username, $email, $no_telp, $role, $status, $id);

        if ($stmt->execute()) {
            // Update session jika user mengedit akunnya sendiri
            if ($id === (int)$user['id']) {
                $_SESSION['admin_username'] = $username;
                $_SESSION['admin_nama']     = $nama;
                $_SESSION['admin_role']     = $role;
            }
            $_SESSION['success'] = "Data admin <strong>" . htmlspecialchars($nama) . "</strong> berhasil diperbarui.";
            header("Location: kelola-admin.php");
            exit;
        } else {
            $errors[] = 'Gagal menyimpan: ' . $stmt->error;
        }
        $stmt->close();
    }

    // Refresh target setelah error (agar form menampilkan nilai yang diinput)
    $target['nama']     = $nama;
    $target['username'] = $username;
    $target['email']    = $email;
    $target['no_telp']  = $no_telp;
    $target['role']     = $role;
    $target['status']   = $status;
    $tab = 'data';
}

/* =========================
   PROSES RESET PASSWORD
========================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['aksi']) && $_POST['aksi'] === 'reset_password') {

    $pw_baru   = $_POST['password_baru'] ?? '';
    $pw_konfirm = $_POST['konfirmasi']   ?? '';

    if (strlen($pw_baru) < 6) $errors[] = 'Password baru minimal 6 karakter.';
    if ($pw_baru !== $pw_konfirm) $errors[] = 'Konfirmasi password tidak cocok.';

    if (empty($errors)) {
        $hashed = password_hash($pw_baru, PASSWORD_BCRYPT);
        $stmt = $conn->prepare("UPDATE admin_users SET password=? WHERE id=?");
        $stmt->bind_param("si", $hashed, $id);

        if ($stmt->execute()) {
            $_SESSION['success'] = "Password admin <strong>" . htmlspecialchars($target['nama']) . "</strong> berhasil direset.";
            header("Location: kelola-admin.php");
            exit;
        } else {
            $errors[] = 'Gagal mereset password: ' . $stmt->error;
        }
        $stmt->close();
    }

    $tab = 'password';
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Admin - PT Cipta Unggul</title>
  <link rel="icon" href="../logo2.png" type="image/png">
  <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;600;700;800&family=DM+Sans:wght@400;500&display=swap" rel="stylesheet">
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    :root {
      --navy: #0B1F3A; --navy-2: #122B52; --blue: #1A56DB;
      --green: #16A34A; --red: #DC2626; --accent: #F59E0B;
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
    .role-badge.admin       { background: #3B82F6; color: #EFF6FF; }

    .main-content { max-width: 640px; margin: 2rem auto; padding: 0 1.5rem; }

    .breadcrumb { display: flex; align-items: center; gap: 8px; margin-bottom: 1.5rem; font-size: 13px; color: var(--muted); }
    .breadcrumb a { color: var(--blue); text-decoration: none; font-weight: 600; }
    .breadcrumb a:hover { text-decoration: underline; }

    /* TARGET INFO */
    .target-info {
      background: white; border-radius: var(--radius-lg);
      box-shadow: 0 4px 24px rgba(11,31,58,0.07);
      padding: 1.25rem 1.5rem; margin-bottom: 1.5rem;
      display: flex; align-items: center; gap: 1rem;
    }
    .target-avatar {
      width: 48px; height: 48px; border-radius: 50%;
      background: linear-gradient(135deg, var(--navy) 0%, var(--blue) 100%);
      display: flex; align-items: center; justify-content: center;
      color: white; font-weight: 800; font-size: 18px;
      flex-shrink: 0;
    }
    .target-detail { flex: 1; }
    .target-name { font-weight: 700; font-size: 16px; }
    .target-sub  { font-size: 13px; color: var(--muted); margin-top: 2px; }

    /* TAB */
    .tabs { display: flex; gap: 0; margin-bottom: 1.5rem; border-radius: var(--radius); overflow: hidden; background: white; box-shadow: 0 2px 12px rgba(11,31,58,0.07); }
    .tab-btn {
      flex: 1; padding: 14px; text-align: center;
      font-weight: 700; font-size: 14px; cursor: pointer;
      text-decoration: none; color: var(--muted);
      background: white; border-bottom: 3px solid transparent;
      transition: all .2s;
    }
    .tab-btn.active { color: var(--blue); border-bottom-color: var(--blue); background: #EFF6FF; }
    .tab-btn:hover:not(.active) { background: #F8FAFF; color: var(--text); }

    /* CARD */
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

    .password-warning {
      background: #FFFBEB; border: 1px solid #FDE68A;
      border-radius: var(--radius); padding: 1rem 1.25rem;
      font-size: 13px; color: #92400E; margin-bottom: 1.5rem;
    }

    .btn-actions { display: flex; gap: 1rem; margin-top: 0.5rem; }
    .btn-primary {
      flex: 1; border: none; padding: 14px;
      border-radius: var(--radius); font-weight: 700; font-size: 15px;
      cursor: pointer; transition: all .3s; color: white;
    }
    .btn-save {
      background: linear-gradient(135deg, var(--blue) 0%, #2563EB 100%);
      box-shadow: 0 6px 20px rgba(26,86,219,0.25);
    }
    .btn-save:hover { transform: translateY(-2px); box-shadow: 0 10px 25px rgba(26,86,219,0.35); }
    .btn-reset-pw {
      background: linear-gradient(135deg, #DC2626 0%, #EF4444 100%);
      box-shadow: 0 6px 20px rgba(220,38,38,0.25);
    }
    .btn-reset-pw:hover { transform: translateY(-2px); box-shadow: 0 10px 25px rgba(220,38,38,0.35); }
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
      <a href="admin.php" class="nav-link">🏗️ Alat Berat</a>
      <a href="kelola-admin.php" class="nav-link">👥 Kelola Admin</a>
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
    <a href="kelola-admin.php">👥 Kelola Admin</a>
    <span>›</span>
    <span>Edit: <?= htmlspecialchars($target['nama']); ?></span>
  </div>

  <!-- Info admin yang diedit -->
  <div class="target-info">
    <div class="target-avatar"><?= mb_strtoupper(mb_substr($target['nama'], 0, 1)); ?></div>
    <div class="target-detail">
      <div class="target-name"><?= htmlspecialchars($target['nama']); ?></div>
      <div class="target-sub">
        @<?= htmlspecialchars($target['username']); ?> &nbsp;·&nbsp;
        <span class="role-badge <?= $target['role']; ?>">
          <?= $target['role'] === 'superadmin' ? 'Superadmin' : 'Admin'; ?>
        </span>
      </div>
    </div>
  </div>

  <!-- Tab -->
  <div class="tabs">
    <a href="?id=<?= $id; ?>" class="tab-btn <?= $tab === 'data' ? 'active' : ''; ?>">✏️ Edit Data</a>
    <a href="?id=<?= $id; ?>&tab=password" class="tab-btn <?= $tab === 'password' ? 'active' : ''; ?>">🔑 Reset Password</a>
  </div>

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

  <!-- ===== TAB EDIT DATA ===== -->
  <?php if ($tab === 'data'): ?>
  <div class="card">
    <div class="card-header">
      <h2>✏️ Edit Data Admin</h2>
      <p>Perbarui informasi akun admin</p>
    </div>
    <div class="card-body">
      <form method="POST">
        <input type="hidden" name="aksi" value="edit_data">

        <div class="form-group">
          <label class="form-label">Nama Lengkap <span class="required">*</span></label>
          <input type="text" name="nama" class="form-input" required
                 value="<?= htmlspecialchars($target['nama']); ?>">
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Username <span class="required">*</span></label>
            <input type="text" name="username" class="form-input" required autocomplete="off"
                   value="<?= htmlspecialchars($target['username']); ?>">
          </div>
          <div class="form-group">
            <label class="form-label">No. Telepon <span class="required">*</span></label>
            <input type="tel" name="no_telp" class="form-input"
                   value="<?= htmlspecialchars($target['no_telp']); ?>">
          </div>
        </div>

        <div class="form-group">
          <label class="form-label">Email <span class="required">*</span></label>
          <input type="email" name="email" class="form-input" required autocomplete="off"
                 value="<?= htmlspecialchars($target['email']); ?>">
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Role <span class="required">*</span></label>
            <select name="role" class="form-select">
              <option value="admin"      <?= $target['role'] === 'admin'      ? 'selected' : ''; ?>>Admin</option>
              <option value="superadmin" <?= $target['role'] === 'superadmin' ? 'selected' : ''; ?>>Superadmin</option>
            </select>
          </div>
          <div class="form-group">
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
              <option value="active"   <?= $target['status'] === 'active'   ? 'selected' : ''; ?>>Aktif</option>
              <option value="inactive" <?= $target['status'] === 'inactive' ? 'selected' : ''; ?>>Nonaktif</option>
            </select>
          </div>
        </div>

        <div class="btn-actions">
          <a href="kelola-admin.php" class="btn-cancel">Batal</a>
          <button type="submit" class="btn-primary btn-save">💾 Simpan Perubahan</button>
        </div>
      </form>
    </div>
  </div>

  <!-- ===== TAB RESET PASSWORD ===== -->
  <?php else: ?>
  <div class="card">
    <div class="card-header">
      <h2>🔑 Reset Password</h2>
      <p>Atur ulang password untuk akun ini</p>
    </div>
    <div class="card-body">

      <div class="password-warning">
        ⚠️ Password lama akan diganti permanen. Pastikan Anda menginformasikan password baru kepada admin yang bersangkutan.
      </div>

      <form method="POST">
        <input type="hidden" name="aksi" value="reset_password">

        <div class="form-group">
          <label class="form-label">Password Baru <span class="required">*</span></label>
          <input type="password" name="password_baru" class="form-input" required minlength="6" autocomplete="new-password">
          <p class="form-hint">Minimal 6 karakter</p>
        </div>

        <div class="form-group">
          <label class="form-label">Konfirmasi Password Baru <span class="required">*</span></label>
          <input type="password" name="konfirmasi" class="form-input" required autocomplete="new-password">
        </div>

        <div class="btn-actions">
          <a href="kelola-admin.php" class="btn-cancel">Batal</a>
          <button type="submit" class="btn-primary btn-reset-pw"
                  onclick="return confirm('Yakin ingin mereset password admin <?= htmlspecialchars(addslashes($target['nama'])); ?>?')">
            🔑 Reset Password
          </button>
        </div>
      </form>
    </div>
  </div>
  <?php endif; ?>

</div>
</body>
</html>

<?php
/**
 * admin/kelola-admin.php — Manajemen Akun Admin
 * Hanya dapat diakses oleh Superadmin
 */
session_start();

include_once __DIR__ . '/../config/koneksi.php';
include_once __DIR__ . '/../config/auth.php';

$auth = new Auth($conn);
$auth->requireSuperadmin(); // Blok akses jika bukan superadmin

$user = $auth->getUser();

/* =========================
   HAPUS ADMIN
========================= */
if (isset($_GET['hapus'])) {
    $hapus_id = (int)$_GET['hapus'];

    // Tidak boleh hapus diri sendiri
    if ($hapus_id === (int)$user['id']) {
        $_SESSION['error'] = 'Anda tidak dapat menghapus akun Anda sendiri!';
        header("Location: kelola-admin.php");
        exit;
    }

    // Hitung jumlah superadmin yang tersisa
    $cek = $conn->query("SELECT COUNT(*) as jml FROM admin_users WHERE role = 'superadmin'");
    $jml_superadmin = $cek->fetch_assoc()['jml'];

    // Cek apakah yang akan dihapus adalah superadmin
    $stmt_role = $conn->prepare("SELECT role FROM admin_users WHERE id = ?");
    $stmt_role->bind_param("i", $hapus_id);
    $stmt_role->execute();
    $target_role = $stmt_role->get_result()->fetch_assoc()['role'] ?? '';
    $stmt_role->close();

    if ($target_role === 'superadmin' && $jml_superadmin <= 1) {
        $_SESSION['error'] = 'Tidak dapat menghapus superadmin terakhir!';
        header("Location: kelola-admin.php");
        exit;
    }

    $stmt = $conn->prepare("DELETE FROM admin_users WHERE id = ?");
    $stmt->bind_param("i", $hapus_id);

    if ($stmt->execute()) {
        $_SESSION['success'] = 'Admin berhasil dihapus.';
    } else {
        $_SESSION['error'] = 'Gagal menghapus admin.';
    }

    $stmt->close();
    header("Location: kelola-admin.php");
    exit;
}

/* =========================
   DATA ADMIN
========================= */
$admins = $conn->query("SELECT id, nama, username, email, no_telp, role, status, created_at FROM admin_users ORDER BY id ASC");
$total_admin = $admins->num_rows;
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kelola Admin - PT Cipta Unggul</title>
  <link rel="icon" href="../logo2.png" type="image/png">
  <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;600;700;800&family=DM+Sans:wght@400;500&display=swap" rel="stylesheet">
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    :root {
      --navy: #0B1F3A; --navy-2: #122B52; --blue: #1A56DB; --blue-lt: #3B82F6;
      --green: #16A34A; --green-d: #15803D; --red: #DC2626; --red-d: #B91C1C;
      --accent: #F59E0B; --text: #1E293B; --muted: #64748B;
      --white: #FFFFFF; --radius: 12px; --radius-lg: 20px;
    }

    body { font-family: 'DM Sans', sans-serif; background: #F8FAFF; color: var(--text); line-height: 1.6; }
    h1, h2, h3 { font-family: 'Sora', sans-serif; }

    /* NAV */
    .nav {
      position: sticky; top: 0; z-index: 100;
      background: rgba(11,31,58,0.97);
      backdrop-filter: blur(12px);
      border-bottom: 1px solid rgba(255,255,255,0.08);
    }
    .nav-inner {
      max-width: 1200px; margin: 0 auto;
      display: flex; align-items: center; justify-content: space-between;
      padding: 0 1.5rem; height: 68px;
    }
    .nav-left { display: flex; align-items: center; gap: 1.5rem; }
    .nav-brand { display: flex; align-items: center; gap: 12px; text-decoration: none; color: white; font-weight: 700; }
    .nav-brand-text { display: flex; flex-direction: column; }
    .nav-brand-text strong { font-size: 15px; }
    .nav-brand-text span { font-size: 11px; opacity: 0.8; }
    .nav-link {
      color: rgba(255,255,255,0.85); text-decoration: none; font-weight: 600;
      font-size: 13px; padding: 8px 16px; border-radius: 8px;
      border: 1px solid rgba(255,255,255,0.2);
      background: rgba(255,255,255,0.06); transition: all .2s;
    }
    .nav-link:hover { background: rgba(255,255,255,0.15); color: white; }
    .nav-link.active { background: rgba(26,86,219,0.4); border-color: rgba(26,86,219,0.6); color: white; }
    .nav-right { display: flex; gap: 1rem; align-items: center; }

    .role-badge {
      display: inline-block; font-size: 10px; font-weight: 700;
      padding: 2px 8px; border-radius: 999px;
      text-transform: uppercase; letter-spacing: 0.5px;
    }
    .role-badge.superadmin { background: #F59E0B; color: #78350F; }
    .role-badge.admin       { background: #3B82F6; color: #EFF6FF; }

    /* MAIN */
    .main-content { max-width: 1200px; margin: 0 auto; padding: 2rem 1.5rem; }

    /* ALERT */
    .alert {
      padding: 1rem 1.25rem; border-radius: var(--radius);
      margin-bottom: 1.5rem; font-weight: 600; font-size: 14px;
      display: flex; align-items: center; gap: 0.5rem;
    }
    .alert-success { background: #D1FAE5; color: #065F46; border-left: 4px solid #10B981; }
    .alert-error   { background: #FEE2E2; color: #991B1B; border-left: 4px solid #DC2626; }

    /* CARD */
    .card {
      background: white; border-radius: var(--radius-lg);
      box-shadow: 0 4px 24px rgba(11,31,58,0.07); overflow: hidden;
    }
    .card-header {
      background: linear-gradient(135deg, var(--navy) 0%, var(--navy-2) 100%);
      color: white; padding: 1.5rem 2rem;
      display: flex; align-items: center; justify-content: space-between;
    }
    .card-header h2 { font-size: 22px; font-weight: 800; }
    .card-header p  { font-size: 13px; opacity: 0.8; margin-top: 4px; }

    /* TABLE */
    .table-wrap { overflow-x: auto; }

    table { width: 100%; border-collapse: collapse; }
    thead { background: #F8FAFC; }
    thead th {
      padding: 12px 20px; text-align: left;
      font-size: 11px; font-weight: 700;
      text-transform: uppercase; letter-spacing: 0.5px;
      color: var(--muted); border-bottom: 2px solid #E2E8F0;
    }
    tbody tr { border-bottom: 1px solid #F1F5F9; transition: background .15s; }
    tbody tr:hover { background: #F8FAFF; }
    tbody td { padding: 14px 20px; font-size: 14px; vertical-align: middle; }

    .admin-name { font-weight: 700; color: var(--text); }
    .admin-sub  { font-size: 12px; color: var(--muted); margin-top: 2px; }

    .status-aktif { background: #D1FAE5; color: #065F46; }
    .status-inactive { background: #F1F5F9; color: var(--muted); }
    .status-pill {
      display: inline-block; padding: 3px 10px;
      border-radius: 999px; font-size: 11px; font-weight: 700;
    }

    .me-badge {
      display: inline-block; font-size: 10px; font-weight: 700;
      padding: 1px 7px; border-radius: 999px;
      background: #EDE9FE; color: #5B21B6; margin-left: 6px;
    }

    /* ACTION BUTTONS */
    .actions { display: flex; gap: 0.5rem; }
    .btn-sm {
      display: inline-flex; align-items: center; gap: 4px;
      padding: 6px 14px; border-radius: 8px;
      font-weight: 700; font-size: 12px;
      text-decoration: none; transition: all 0.2s;
      border: none; cursor: pointer;
    }
    .btn-edit-sm   { background: #FEF3C7; color: #92400E; border: 1px solid #FCD34D; }
    .btn-edit-sm:hover { background: #FCD34D; transform: translateY(-1px); }
    .btn-reset-sm  { background: #DBEAFE; color: #1E40AF; border: 1px solid #BFDBFE; }
    .btn-reset-sm:hover { background: #BFDBFE; transform: translateY(-1px); }
    .btn-del-sm    { background: #FEE2E2; color: #991B1B; border: 1px solid #FECACA; }
    .btn-del-sm:hover { background: #FECACA; transform: translateY(-1px); }
    .btn-disabled  { opacity: 0.4; cursor: not-allowed; pointer-events: none; }

    /* ADD BUTTON */
    .btn-add {
      display: inline-flex; align-items: center; gap: 6px;
      padding: 10px 20px; border-radius: var(--radius);
      background: linear-gradient(135deg, var(--green) 0%, #22C55E 100%);
      color: white; font-weight: 700; font-size: 13px;
      text-decoration: none; transition: all .2s;
      box-shadow: 0 4px 12px rgba(22,163,74,0.25);
      border: none; cursor: pointer;
    }
    .btn-add:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(22,163,74,0.35); }

    @media (max-width: 768px) {
      thead th:nth-child(3), thead th:nth-child(4), tbody td:nth-child(3), tbody td:nth-child(4) { display: none; }
    }
  </style>
</head>
<body>

<nav class="nav">
  <div class="nav-inner">
    <div class="nav-left">
      <a href="admin.php" class="nav-brand">
        <div class="nav-brand-text">
          <strong>
            <?= htmlspecialchars($user['username']); ?>
            <span class="role-badge superadmin">Superadmin</span>
          </strong>
          <span>PT Cipta Unggul</span>
        </div>
      </a>
      <a href="admin.php"       class="nav-link">🏗️ Alat Berat</a>
      <a href="kelola-admin.php" class="nav-link active">👥 Kelola Admin</a>
    </div>
    <div class="nav-right">
      <a href="admin.php?logout=1"
         style="color:rgba(255,255,255,0.9);padding:10px 20px;border-radius:8px;border:1px solid rgba(255,255,255,0.3);text-decoration:none;font-weight:600;font-size:13px;transition:all .2s;"
         onclick="return confirm('Yakin logout?')">Logout</a>
    </div>
  </div>
</nav>

<div class="main-content">

  <?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success">✅ <?= htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?></div>
  <?php endif; ?>

  <?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-error">❌ <?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></div>
  <?php endif; ?>

  <div class="card">
    <div class="card-header">
      <div>
        <h2>👥 Daftar Admin</h2>
        <p><?= $total_admin; ?> akun terdaftar</p>
      </div>
      <a href="tambah-admin.php" class="btn-add">➕ Tambah Admin</a>
    </div>

    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>#</th>
            <th>Nama & Username</th>
            <th>Email</th>
            <th>Telepon</th>
            <th>Role</th>
            <th>Status</th>
            <th>Terdaftar</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php $no = 1; while ($row = $admins->fetch_assoc()): ?>
          <?php
            $is_me = ((int)$row['id'] === (int)$user['id']);
          ?>
          <tr>
            <td style="color:var(--muted);font-weight:600;"><?= $no++; ?></td>
            <td>
              <div class="admin-name">
                <?= htmlspecialchars($row['nama']); ?>
                <?php if ($is_me): ?>
                  <span class="me-badge">Anda</span>
                <?php endif; ?>
              </div>
              <div class="admin-sub">@<?= htmlspecialchars($row['username']); ?></div>
            </td>
            <td style="color:var(--muted);"><?= htmlspecialchars($row['email']); ?></td>
            <td style="color:var(--muted);"><?= htmlspecialchars($row['no_telp']); ?></td>
            <td>
              <span class="role-badge <?= $row['role']; ?>">
                <?= $row['role'] === 'superadmin' ? 'Superadmin' : 'Admin'; ?>
              </span>
            </td>
            <td>
              <span class="status-pill status-<?= $row['status']; ?>">
                <?= $row['status'] === 'active' ? 'Aktif' : 'Nonaktif'; ?>
              </span>
            </td>
            <td style="color:var(--muted);font-size:13px;">
              <?= date('d M Y', strtotime($row['created_at'])); ?>
            </td>
            <td>
              <div class="actions">
                <a href="edit-admin.php?id=<?= $row['id']; ?>" class="btn-sm btn-edit-sm">✏️ Edit</a>
                <a href="edit-admin.php?id=<?= $row['id']; ?>&tab=password" class="btn-sm btn-reset-sm">🔑 Reset</a>
                <a href="?hapus=<?= $row['id']; ?>"
                   class="btn-sm btn-del-sm <?= $is_me ? 'btn-disabled' : ''; ?>"
                   <?php if (!$is_me): ?>
                     onclick="return confirm('Hapus admin <?= htmlspecialchars(addslashes($row['nama'])); ?>?')"
                   <?php endif; ?>>
                   🗑️ Hapus
                </a>
              </div>
            </td>
          </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>

</div>
</body>
</html>

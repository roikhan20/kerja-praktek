<?php
/**
 * admin/admin.php — Dashboard Kelola Alat Berat
 * Perubahan dari versi sebelumnya:
 *   - Tambah link "Kelola Admin" di navbar (hanya tampil untuk superadmin)
 *   - Gunakan requireLogin() dari Auth
 */
session_start();

include_once __DIR__ . '/../config/koneksi.php';
include_once __DIR__ . '/../config/auth.php';

$auth = new Auth($conn);
$auth->requireLogin(); // Redirect ke login.php jika belum login

$user = $auth->getUser();

if (isset($_GET['logout'])) {
    $auth->logout();
    header("Location: login.php");
    exit;
}

function sanitize($data) {
    global $conn;
    return $conn->real_escape_string(trim($data));
}

/* =========================
   TAMBAH DATA
========================= */
if (isset($_POST['submit'])) {

    $nama        = sanitize($_POST['nama']);
    $deskripsi   = sanitize($_POST['deskripsi']);
    $spesifikasi = sanitize($_POST['spesifikasi']);
    $lokasi      = sanitize($_POST['lokasi']);
    $status      = sanitize($_POST['status']);

    if (empty($nama) || empty($lokasi)) {
        $_SESSION['error'] = 'Nama dan lokasi wajib diisi!';
        header("Location: admin.php");
        exit;
    }

    /* Upload gambar */
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {

        $gambar      = $_FILES['gambar']['name'];
        $tmp         = $_FILES['gambar']['tmp_name'];
        $file_size   = $_FILES['gambar']['size'];
        $file_ext    = strtolower(pathinfo($gambar, PATHINFO_EXTENSION));

        $allowed_types = ['jpg', 'jpeg', 'png', 'webp'];

        if (!in_array($file_ext, $allowed_types)) {
            $_SESSION['error'] = 'Tipe file tidak diizinkan!';
            header("Location: admin.php");
            exit;
        }

        if ($file_size > 5 * 1024 * 1024) {
            $_SESSION['error'] = 'Ukuran file terlalu besar (Maksimal 5MB)!';
            header("Location: admin.php");
            exit;
        }

        $namaFile = time() . '_' . uniqid() . '.' . $file_ext;

        if (!move_uploaded_file($tmp, "../uploads/" . $namaFile)) {
            $_SESSION['error'] = 'Upload gambar gagal!';
            header("Location: admin.php");
            exit;
        }

    } else {
        $_SESSION['error'] = 'Gambar wajib diupload!';
        header("Location: admin.php");
        exit;
    }

    /* Insert alat */
    $stmt = $conn->prepare("
        INSERT INTO alat_berat 
        (nama, deskripsi, spesifikasi, lokasi, gambar, status) 
        VALUES (?, ?, ?, ?, ?, ?)
    ");

    $stmt->bind_param("ssssss", $nama, $deskripsi, $spesifikasi, $lokasi, $namaFile, $status);

    if ($stmt->execute()) {

        $alat_id = $conn->insert_id;

        /* Insert variasi harga */
        if (isset($_POST['berat']) && isset($_POST['harga_variasi'])) {

            $berat          = $_POST['berat'];
            $harga_variasi  = $_POST['harga_variasi'];

            for ($i = 0; $i < count($berat); $i++) {

                $b = sanitize($berat[$i]);
                $h = sanitize($harga_variasi[$i]);

                if (!empty($b) && !empty($h)) {
                    $stmt2 = $conn->prepare("
                        INSERT INTO harga_alat (alat_id, berat, harga) 
                        VALUES (?, ?, ?)
                    ");

                    $stmt2->bind_param("iss", $alat_id, $b, $h);
                    $stmt2->execute();
                    $stmt2->close();
                }
            }
        }

        $_SESSION['success'] = 'Alat berhasil ditambahkan!';
    } else {
        $_SESSION['error'] = 'Error: ' . $stmt->error;
    }

    $stmt->close();
    header("Location: admin.php");
    exit;
}

/* =========================
   HAPUS DATA
========================= */
if (isset($_GET['hapus'])) {

    $id = (int)$_GET['hapus'];

    $conn->begin_transaction();

    $stmt = $conn->prepare("DELETE FROM harga_alat WHERE alat_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();

    $stmt = $conn->prepare("DELETE FROM alat_berat WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $conn->commit();
        $_SESSION['success'] = 'Alat berhasil dihapus!';
    } else {
        $conn->rollback();
        $_SESSION['error'] = 'Error hapus: ' . $stmt->error;
    }

    $stmt->close();
    header("Location: admin.php");
    exit;
}

/* =========================
   DATA LISTING
========================= */
$data = mysqli_query($conn, "
    SELECT ab.*, COUNT(ha.id) as total_variasi 
    FROM alat_berat ab 
    LEFT JOIN harga_alat ha ON ab.id = ha.alat_id 
    GROUP BY ab.id 
    ORDER BY ab.id DESC
");

$total_alat = mysqli_num_rows($data);

$res_tersedia = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT COUNT(*) as count 
    FROM alat_berat 
    WHERE status='tersedia'
"));

$total_tersedia = $res_tersedia['count'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Panel - PT Cipta Unggul</title>
  <link rel="icon" href="../logo2.png" type="image/png">
  <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;600;700;800&family=DM+Sans:wght@400;500&display=swap" rel="stylesheet">
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    :root {
      --navy: #0B1F3A; --navy-2: #122B52; --blue: #1A56DB; --blue-lt: #3B82F6;
      --accent: #F59E0B; --accent-d: #D97706; --green: #16A34A; --green-d: #15803D;
      --red: #DC2626; --red-d: #B91C1C; --yellow: #F59E0B; --yellow-d: #D97706;
      --text: #1E293B; --muted: #64748B; --surface: #F1F5F9; --white: #FFFFFF;
      --radius: 12px; --radius-lg: 20px;
    }

    html { scroll-behavior: smooth; }

    body { font-family: 'DM Sans', sans-serif; background: #F8FAFF; color: var(--text); line-height: 1.6; }

    h1, h2, h3, h4 { font-family: 'Sora', sans-serif; line-height: 1.2; }

    /* ---- NAVBAR ---- */
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

    .nav-left { display: flex; align-items: center; gap: 2rem; }

    .nav-brand { display: flex; align-items: center; gap: 12px; text-decoration: none; color: white; font-weight: 700; }

    .nav-brand-text { display: flex; flex-direction: column; }
    .nav-brand-text span:first-child { font-size: 15px; }
    .nav-brand-text span:last-child  { font-size: 11px; opacity: 0.8; }

    /* Link menu Kelola Admin */
    .nav-menu-link {
      color: rgba(255,255,255,0.85);
      text-decoration: none;
      font-weight: 600;
      font-size: 13px;
      padding: 8px 16px;
      border-radius: 8px;
      border: 1px solid rgba(255,255,255,0.2);
      background: rgba(255,255,255,0.06);
      transition: all .2s;
      display: flex;
      align-items: center;
      gap: 6px;
    }

    .nav-menu-link:hover {
      background: rgba(255,255,255,0.15);
      border-color: rgba(255,255,255,0.4);
      color: white;
    }

    /* Badge role */
    .role-badge {
      display: inline-block;
      font-size: 10px;
      font-weight: 700;
      padding: 2px 8px;
      border-radius: 999px;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      margin-left: 6px;
    }

    .role-badge.superadmin { background: #F59E0B; color: #78350F; }
    .role-badge.admin       { background: #3B82F6; color: #1E3A8A; }

    .nav-right { display: flex; gap: 1.5rem; align-items: center; }

    .nav-stats { display: flex; gap: 1.5rem; align-items: center; }

    .stats-item { display: flex; flex-direction: column; align-items: center; }
    .stats-number { font-family: 'Sora', sans-serif; font-size: 18px; font-weight: 800; color: white; }
    .stats-item span:last-child { font-size: 10px; color: rgba(255,255,255,0.6); text-transform: uppercase; }

    /* ---- MAIN CONTENT ---- */
    .main-content { max-width: 1200px; margin: 0 auto; padding: 2rem 1.5rem; }

    .form-card, .table-card {
      background: white; border-radius: var(--radius-lg);
      box-shadow: 0 4px 24px rgba(11,31,58,0.07);
      margin-bottom: 2rem; overflow: hidden;
    }

    .form-header, .table-header {
      background: linear-gradient(135deg, var(--navy) 0%, var(--navy-2) 100%);
      color: white; padding: 1.5rem 2rem;
    }

    .form-body { padding: 2rem; }

    .form-group { margin-bottom: 1.5rem; }

    .form-label { display: block; font-weight: 700; color: var(--text); margin-bottom: 0.5rem; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px; }

    .form-input, .form-select, .form-textarea {
      width: 100%; padding: 13px 16px;
      border: 2px solid #E2E8F0; border-radius: var(--radius);
      font-size: 14px; font-weight: 500;
      transition: all 0.25s ease;
      background: #F8FAFF; color: var(--text);
      font-family: inherit;
    }

    .form-input:focus, .form-select:focus, .form-textarea:focus {
      outline: none; border-color: var(--blue);
      background: var(--white); box-shadow: 0 0 0 4px rgba(26,86,219,0.12);
    }

    .form-textarea { resize: vertical; min-height: 80px; }

    .variasi-group { display: flex; flex-direction: column; gap: 0.75rem; }

    .variasi-row { display: grid; grid-template-columns: 1fr 1fr auto; gap: 0.75rem; align-items: start; }

    .btn-add-variasi {
      padding: 13px 16px; background: var(--green); color: white;
      border: none; border-radius: var(--radius); cursor: pointer;
      font-size: 16px; transition: all 0.2s;
    }

    .btn-add-variasi:hover { background: var(--green-d); }

    .btn-primary {
      background: linear-gradient(135deg, var(--blue) 0%, #2563EB 100%);
      color: white; border: none; padding: 14px 28px;
      border-radius: var(--radius); font-weight: 700; font-size: 15px;
      cursor: pointer; transition: all 0.3s;
      box-shadow: 0 6px 20px rgba(26,86,219,0.25);
    }

    .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 10px 25px rgba(26,86,219,0.35); }

    .table-header { padding: 1.5rem 2rem; }

    .alat-list {
      display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
      gap: 1.5rem; padding: 2rem;
    }

    .alat-card {
      background: white; border-radius: var(--radius-lg);
      box-shadow: 0 2px 12px rgba(11,31,58,0.08);
      overflow: hidden; display: flex; flex-direction: column;
      border: 1px solid #F1F5F9;
      transition: all 0.25s;
    }

    .alat-card:hover { transform: translateY(-3px); box-shadow: 0 8px 24px rgba(11,31,58,0.13); }

    .alat-card-image { position: relative; height: 180px; background: #F1F5F9; overflow: hidden; }

    .alat-card-image img { width: 100%; height: 100%; object-fit: cover; }

    .no-image { display: flex; align-items: center; justify-content: center; height: 100%; font-size: 48px; }

    .status-badge {
      position: absolute; top: 0.75rem; right: 0.75rem;
      padding: 4px 12px; border-radius: 999px;
      font-size: 11px; font-weight: 700; text-transform: uppercase;
    }

    .status-tersedia { background: #D1FAE5; color: #065F46; }
    .status-tidak    { background: #FEE2E2; color: #991B1B; }

    .alat-card-content { padding: 1.25rem; display: flex; flex-direction: column; flex: 1; }

    .alat-title { font-size: 17px; font-weight: 700; margin-bottom: 0.5rem; }

    .alat-lokasi { font-size: 13px; color: var(--muted); margin-bottom: 1rem; display: flex; align-items: center; gap: 4px; }

    .alat-prices { background: #F8FAFC; border-radius: 10px; padding: 0.75rem 1rem; margin-bottom: 1rem; }

    .price-header { font-size: 11px; font-weight: 700; color: var(--muted); text-transform: uppercase; letter-spacing: 0.5px; display: block; margin-bottom: 0.5rem; }

    .price-item { display: flex; justify-content: space-between; align-items: center; padding: 4px 0; font-size: 14px; }
    .price-berat { color: var(--text); font-weight: 500; }
    .price-angka { font-weight: 700; color: var(--blue); }

    .alat-actions { margin-top: auto; display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; }

    .alat-actions a {
      display: flex; align-items: center; justify-content: center; gap: 6px;
      padding: 10px 16px; border-radius: 10px; font-weight: 600;
      font-size: 13px; text-decoration: none; transition: all 0.2s;
    }

    .btn-edit   { background: #FEF3C7; color: #92400E; border: 1px solid #FCD34D; }
    .btn-edit:hover   { background: #FCD34D; transform: translateY(-1px); }
    .btn-delete { background: #FEE2E2; color: #991B1B; border: 1px solid #FECACA; }
    .btn-delete:hover { background: #FECACA; transform: translateY(-1px); }

    .empty-state { grid-column: 1 / -1; text-align: center; padding: 3rem; color: var(--muted); }

    @media (max-width: 768px) {
      .nav-right { gap: 1rem; }
      .nav-stats  { display: none; }
      .nav-menu-link span.label { display: none; }
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
            <span class="role-badge <?= $user['role']; ?>">
              <?= $user['role'] === 'superadmin' ? 'Superadmin' : 'Admin'; ?>
            </span>
          </strong>
          <span>PT Cipta Unggul</span>
        </div>
      </a>

      <?php if ($auth->isSuperadmin()): ?>
        <a href="kelola-admin.php" class="nav-menu-link">
          <span class="label">Kelola Admin</span>
        </a>
      <?php endif; ?>
    </div>
    
    <div class="nav-right">
      <div class="nav-stats">
        <div class="stats-item">
          <span class="stats-number"><?= $total_alat; ?></span>
          <span>Total Alat</span>
        </div>
        <div class="stats-item">
          <span class="stats-number" style="color:#10B981;"><?= $total_tersedia; ?></span>
          <span>Tersedia</span>
        </div>
      </div>
        
      <a href="?logout=1"
         style="color:rgba(255,255,255,0.9);padding:10px 20px;border-radius:8px;border:1px solid rgba(255,255,255,0.3);text-decoration:none;font-weight:600;font-size:13px;backdrop-filter:blur(10px);transition:all .2s;"
         onclick="return confirm('Yakin logout, <?= htmlspecialchars($user['nama']); ?>?')">
         Logout
      </a>
    </div>
  </div>
</nav>

<div class="main-content">

  <?php if (isset($_SESSION['success'])): ?>
  <div style="background:#10B981;color:white;padding:1rem;border-radius:12px;margin-bottom:1.5rem;text-align:center;font-weight:600;">
     <?= $_SESSION['success']; unset($_SESSION['success']); ?>
  </div>
  <?php endif; ?>

  <?php if (isset($_SESSION['error'])): ?>
  <div style="background:#EF4444;color:white;padding:1rem;border-radius:12px;margin-bottom:1.5rem;text-align:center;font-weight:600;">
    <?= $_SESSION['error']; unset($_SESSION['error']); ?>
  </div>
  <?php endif; ?>

  <div class="form-card">
    <div class="form-header">
      <h2 style="font-size:24px;font-weight:800;margin:0;">Tambah Alat Baru</h2>
    </div>
    <div class="form-body">
      <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
          <label class="form-label">Nama Alat</label>
          <input type="text" name="nama" placeholder="Contoh: Forklift 3 Ton" class="form-input" required>
        </div>

        <div class="form-group">
          <label class="form-label">Variasi Harga</label>
          <div class="variasi-group" id="variasiContainer">
            <div class="variasi-row">
              <input type="text" name="berat[]" placeholder="Berat (Contoh: 3 Ton)" class="form-input" required>
              <input type="number" name="harga_variasi[]" placeholder="Harga (Rp)" class="form-input" required>
              <button type="button" class="btn-add-variasi" onclick="addVariasi()">➕</button>
            </div>
          </div>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;">
          <div class="form-group">
            <label class="form-label">Deskripsi</label>
            <textarea name="deskripsi" class="form-textarea" placeholder="Deskripsi singkat alat..." rows="3"></textarea>
          </div>
          <div class="form-group">
            <label class="form-label">Spesifikasi</label>
            <textarea name="spesifikasi" class="form-textarea" placeholder="Spesifikasi teknis..." rows="3"></textarea>
          </div>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;">
          <div class="form-group">
            <label class="form-label">Lokasi</label>
            <input type="text" name="lokasi" class="form-input" placeholder="Pamulang, Tangerang Selatan" required>
          </div>
          <div class="form-group">
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
              <option value="tersedia">Tersedia</option>
              <option value="tidak">Tidak Tersedia</option>
            </select>
          </div>
        </div>

        <div class="form-group">
          <label class="form-label">Foto Alat</label>
          <input type="file" name="gambar" class="form-input" accept="image/*" required>
        </div>

        <button type="submit" name="submit" class="btn-primary">Simpan Alat Baru</button>
      </form>
    </div>
  </div>

  <div class="table-card">
    <div class="table-header">
      <h2 style="font-size:24px;font-weight:800;margin:0;">Daftar Alat Berat</h2>
      <p style="font-size:14px;opacity:0.8;margin-top:4px;"><?= $total_alat; ?> total alat</p>
    </div>
    
    <div class="alat-list">
      <?php if(mysqli_num_rows($data) > 0): ?>
        <?php while($row = mysqli_fetch_assoc($data)) : ?>
          <?php
            $alat_id = (int)$row['id'];
            $variasi = mysqli_query($conn, "SELECT * FROM harga_alat WHERE alat_id = $alat_id");
          ?>
          <div class="alat-card">
            <div class="alat-card-image">
              <?php if(!empty($row['gambar']) && file_exists("../uploads/" . $row['gambar'])): ?>
                <img src="../uploads/<?= htmlspecialchars($row['gambar']); ?>" alt="<?= htmlspecialchars($row['nama']); ?>">
              <?php else: ?>
                <div class="no-image"></div>
              <?php endif; ?>
              <span class="status-badge status-<?= htmlspecialchars($row['status']); ?>">
                <?= $row['status'] == 'tersedia' ? 'Tersedia' : 'Tidak'; ?>
              </span>
            </div>
            <div class="alat-card-content">
              <h3 class="alat-title"><?= htmlspecialchars($row['nama']); ?></h3>
              <p class="alat-lokasi"><?= htmlspecialchars($row['lokasi']); ?></p>
              <div class="alat-prices">
                <span class="price-header">Harga:</span>
                <?php while($v = mysqli_fetch_assoc($variasi)) : ?>
                  <div class="price-item">
                    <span class="price-berat"><?= htmlspecialchars($v['berat']); ?></span>
                    <span class="price-angka">Rp <?= number_format($v['harga'], 0, ',', '.'); ?></span>
                  </div>
                <?php endwhile; ?>
              </div>
              <div class="alat-actions">
                <a href="edit.php?id=<?= $row['id']; ?>" class="btn-edit">Edit</a>
                <a href="?hapus=<?= $row['id']; ?>"
                   onclick="return confirm('Yakin hapus alat ini?')"
                   class="btn-delete">Hapus</a>
              </div>
            </div>
          </div>
        <?php endwhile; ?>
      <?php else: ?>
        <div class="empty-state">
          <span style="font-size:48px;display:block;margin-bottom:1rem;">📭</span>
          <p>Belum ada alat berat yang ditambahkan.</p>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<script>
function addVariasi() {
  const container = document.getElementById('variasiContainer');
  const row = document.createElement('div');
  row.className = 'variasi-row';
  row.innerHTML = `
    <input type="text" name="berat[]" placeholder="Berat (Contoh: 5 Ton)" class="form-input" required>
    <input type="number" name="harga_variasi[]" placeholder="Harga (Rp)" class="form-input" required>
    <button type="button" class="btn-add-variasi" style="background:var(--red);" onclick="this.parentElement.remove()">🗑️</button>
  `;
  container.appendChild(row);
}
</script>

</body>
</html>

<?php
session_start();

include_once __DIR__ . '/../config/koneksi.php';
include_once __DIR__ . '/../config/auth.php';

$auth = new Auth($conn);
$user = $auth->getUser();

if (!$auth->isLoggedIn()) {
    header("Location: login.php");
    exit;
}

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
    *, *::before, *::after {
  box-sizing: border-box;
  margin: 0;
  padding: 0;
}

:root {
  --navy: #0B1F3A;
  --navy-2: #122B52;
  --blue: #1A56DB;
  --blue-lt: #3B82F6;
  --accent: #F59E0B;
  --accent-d: #D97706;
  --green: #16A34A;
  --green-d: #15803D;
  --red: #DC2626;
  --red-d: #B91C1C;
  --yellow: #F59E0B;
  --yellow-d: #D97706;
  --text: #1E293B;
  --muted: #64748B;
  --surface: #F1F5F9;
  --white: #FFFFFF;
  --radius: 12px;
  --radius-lg: 20px;
}

html {
  scroll-behavior: smooth;
}

body {
  font-family: 'DM Sans', sans-serif;
  background: #F8FAFF;
  color: var(--text);
  line-height: 1.6;
}

h1, h2, h3, h4 {
  font-family: 'Sora', sans-serif;
  line-height: 1.2;
}

.nav {
  position: sticky;
  top: 0;
  z-index: 100;
  background: rgba(11,31,58,0.97);
  backdrop-filter: blur(12px);
  border-bottom: 1px solid rgba(255,255,255,0.08);
}

.nav-inner {
  max-width: 1200px;
  margin: 0 auto;
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0 1.5rem;
  height: 68px;
}

.nav-left {
  display: flex;
  align-items: center;
  gap: 2rem;
}

.nav-brand {
  display: flex;
  align-items: center;
  gap: 12px;
  text-decoration: none;
  color: white;
  font-weight: 700;
}

.nav-brand-text {
  display: flex;
  flex-direction: column;
}

.nav-brand-text span:first-child {
  font-size: 15px;
}

.nav-brand-text span:last-child {
  font-size: 11px;
  opacity: 0.8;
}

.nav-right {
  display: flex;
  gap: 2rem;
  align-items: center;
}

.nav-stats {
  display: flex;
  gap: 1.5rem;
}

.stats-item {
  display: flex;
  flex-direction: column;
  align-items: center;
  color: rgba(255,255,255,0.8);
  font-size: 12px;
}

.stats-number {
  font-size: 16px;
  font-weight: 700;
  color: white;
}

.main-content {
  max-width: 1200px;
  margin: 0 auto;
  padding: 2rem 1.5rem;
}

.form-card {
  background: var(--white);
  border-radius: var(--radius-lg);
  box-shadow: 0 10px 40px rgba(0,0,0,0.08);
  overflow: hidden;
  margin-bottom: 2rem;
}

.form-header {
  background: linear-gradient(135deg, var(--navy), var(--navy-2));
  color: white;
  padding: 1.5rem 2rem;
}

.form-body {
  padding: 2rem;
}

.form-group {
  margin-bottom: 1.5rem;
}

.form-label {
  display: block;
  font-weight: 600;
  color: var(--navy);
  margin-bottom: 0.5rem;
  font-size: 14px;
}

.form-input,
.form-textarea,
.form-select {
  width: 100%;
  padding: 12px 16px;
  border: 2px solid #E2E8F0;
  border-radius: var(--radius);
  font-size: 15px;
  transition: border-color .2s, box-shadow .2s;
  background: var(--white);
  font-family: 'DM Sans', sans-serif;
}

.form-input:focus,
.form-textarea:focus,
.form-select:focus {
  outline: none;
  border-color: var(--blue);
  box-shadow: 0 0 0 3px rgba(26,86,219,0.1);
}

.variasi-group {
  background: #F8FAFC;
  border: 2px solid #E2E8F0;
  border-radius: var(--radius);
  padding: 1.5rem;
  margin-bottom: 1.5rem;
}

.variasi-row {
  display: grid;
  grid-template-columns: 1fr 1fr auto;
  gap: 1rem;
  align-items: end;
  margin-bottom: 0.75rem;
}

.variasi-row:last-child {
  margin-bottom: 0;
}

.btn-add-variasi {
  background: var(--blue);
  color: white;
  border: none;
  padding: 12px 16px;
  border-radius: 8px;
  cursor: pointer;
  font-weight: 600;
  transition: background .2s;
}

.btn-add-variasi:hover {
  background: #1D4ED8;
}

.btn-primary {
  background: linear-gradient(135deg, var(--blue), var(--blue-lt));
  color: white;
  border: none;
  padding: 14px 32px;
  border-radius: var(--radius);
  font-weight: 700;
  font-size: 15px;
  cursor: pointer;
  transition: all .2s;
  box-shadow: 0 6px 20px rgba(26,86,219,0.3);
}

.btn-primary:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 25px rgba(26,86,219,0.4);
}

.table-card {
  background: var(--white);
  border-radius: var(--radius-lg);
  box-shadow: 0 10px 40px rgba(0,0,0,0.08);
  overflow: hidden;
  margin-bottom: 2rem;
}

.table-header {
  background: linear-gradient(135deg, var(--navy), var(--navy-2));
  color: white;
  padding: 1.5rem 2rem;
}

.alat-list {
  padding: 1.5rem;
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
  gap: 1.5rem;
}

.alat-card {
  background: #fff;
  border: 1px solid #E2E8F0;
  border-radius: 16px;
  overflow: hidden;
  display: flex;
  flex-direction: column;
  transition: all 0.3s ease;
  box-shadow: 0 2px 8px rgba(0,0,0,0.04);
}

.alat-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 12px 24px rgba(0,0,0,0.1);
  border-color: var(--blue-lt);
}

.alat-card-image {
  position: relative;
  height: 180px;
  background: #F1F5F9;
  overflow: hidden;
}

.alat-card-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.alat-card-image .no-image {
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 3rem;
  color: #CBD5E1;
}

.status-badge {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 4px 12px;
  border-radius: 20px;
  font-size: 12px;
  font-weight: 600;
  text-transform: uppercase;
}

.alat-card-image .status-badge {
  position: absolute;
  top: 12px;
  right: 12px;
}

.status-tersedia {
  background: #DCFCE7;
  color: #16A34A;
}

.status-tidak {
  background: #FEF2F2;
  color: #DC2626;
}

.alat-card-content {
  padding: 1.25rem;
  display: flex;
  flex-direction: column;
  flex-grow: 1;
}

.alat-title {
  font-size: 18px;
  font-weight: 800;
  color: var(--navy);
  margin-bottom: 0.5rem;
  line-height: 1.3;
}

.alat-lokasi {
  font-size: 13px;
  color: var(--muted);
  margin-bottom: 1rem;
  display: flex;
  align-items: center;
  gap: 4px;
}

.alat-prices {
  background: #F8FAFC;
  border-radius: 10px;
  padding: 0.75rem 1rem;
  margin-bottom: 1rem;
}

.price-header {
  font-size: 11px;
  font-weight: 700;
  color: var(--muted);
  text-transform: uppercase;
  letter-spacing: 0.5px;
  display: block;
  margin-bottom: 0.5rem;
}

.price-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 4px 0;
  font-size: 14px;
}

.price-berat {
  color: var(--text);
  font-weight: 500;
}

.price-angka {
  font-weight: 700;
  color: var(--blue);
}

.alat-actions {
  margin-top: auto;
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 0.75rem;
}

.alat-actions a {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
  padding: 10px 16px;
  border-radius: 10px;
  font-weight: 600;
  font-size: 13px;
  text-decoration: none;
  transition: all 0.2s;
}

.btn-edit {
  background: #FEF3C7;
  color: #92400E;
  border: 1px solid #FCD34D;
}

.btn-edit:hover {
  background: #FCD34D;
  transform: translateY(-1px);
}

.btn-delete {
  background: #FEE2E2;
  color: #991B1B;
  border: 1px solid #FECACA;
}

.btn-delete:hover {
  background: #FECACA;
  transform: translateY(-1px);
}

.empty-state {
  grid-column: 1 / -1;
  text-align: center;
  padding: 3rem;
  color: var(--muted);
}

@media (max-width: 768px) {
  .nav-right {
    gap: 1rem;
  }

  .nav-stats {
    display: none;
  }
}
  </style>
</head>
<body>

<nav class="nav">
  <div class="nav-inner">
    <div class="nav-left">
      <a href="admin.php" class="nav-brand">
        <div class="nav-brand-text" >
          <strong><?= htmlspecialchars($user['username']); ?></strong>
          <span>PT Cipta Unggul</span>
        </div>
      </a>
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
            <textarea name="deskripsi" class="form-textarea" placeholder="Deskripsi singkat alat..." rows="3" ></textarea>
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

        <button type="submit" name="submit" class="btn-primary">💾 Simpan Alat Baru</button>
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
            // Tetap jalankan query aman untuk mengambil data variasi berelasi
            $variasi = mysqli_query($conn, "SELECT * FROM harga_alat WHERE alat_id = $alat_id");
          ?>
          <div class="alat-card">
            <div class="alat-card-image">
              <?php if(!empty($row['gambar']) && file_exists("../uploads/" . $row['gambar'])): ?>
                <img src="../uploads/<?= htmlspecialchars($row['gambar']); ?>" alt="<?= htmlspecialchars($row['nama']); ?>">
              <?php else: ?>
                <div class="no-image">📦</div>
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
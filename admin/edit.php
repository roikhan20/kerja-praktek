<?php
include_once __DIR__ . '/../config/koneksi.php';
session_start();

function sanitize($data)
{
    global $conn;
    return mysqli_real_escape_string($conn, trim($data));
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error'] = 'ID tidak valid!';
    header("Location: admin.php");
    exit;
}

$id = (int)$_GET['id'];

$stmt = $conn->prepare("SELECT * FROM alat_berat WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if (!$row) {
    $_SESSION['error'] = 'Data tidak ditemukan!';
    header("Location: admin.php");
    exit;
}

$stmt = $conn->prepare("SELECT * FROM harga_alat WHERE alat_id = ? ORDER BY id");
$stmt->bind_param("i", $id);
$stmt->execute();
$variasi_result = $stmt->get_result();

$variasi_data = [];
while ($v = $variasi_result->fetch_assoc()) {
    $variasi_data[] = $v;
}

if (isset($_POST['update'])) {

    $nama        = sanitize($_POST['nama']);
    $deskripsi   = sanitize($_POST['deskripsi']);
    $spesifikasi = sanitize($_POST['spesifikasi']);
    $lokasi      = sanitize($_POST['lokasi']);
    $status      = sanitize($_POST['status']);

    if (empty($nama) || empty($lokasi)) {
        $_SESSION['error'] = 'Nama dan lokasi wajib diisi!';
        header("Location: edit.php?id=$id");
        exit;
    }

    $gambar_lama = $row['gambar'];
    $gambar_baru = $gambar_lama;

    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {

        $gambar     = $_FILES['gambar']['name'];
        $tmp        = $_FILES['gambar']['tmp_name'];
        $file_size  = $_FILES['gambar']['size'];
        $file_ext   = strtolower(pathinfo($gambar, PATHINFO_EXTENSION));

        $allowed_types = ['jpg', 'jpeg', 'png', 'webp'];

        if (!in_array($file_ext, $allowed_types)) {
            $_SESSION['error'] = 'Tipe file tidak diizinkan!';
            header("Location: edit.php?id=$id");
            exit;
        }

        if ($file_size > 5 * 1024 * 1024) {
            $_SESSION['error'] = 'Ukuran file maksimal 5MB!';
            header("Location: edit.php?id=$id");
            exit;
        }

        $namaFile = time() . '_' . uniqid() . '.' . $file_ext;

        if (move_uploaded_file($tmp, "../uploads/" . $namaFile)) {
            $gambar_baru = $namaFile;

            if (file_exists("../uploads/" . $gambar_lama)) {
                unlink("../uploads/" . $gambar_lama);
            }
        } else {
            $_SESSION['error'] = 'Gagal upload gambar!';
            header("Location: edit.php?id=$id");
            exit;
        }
    }

    $conn->begin_transaction();

    $stmt = $conn->prepare("
        UPDATE alat_berat 
        SET nama=?, deskripsi=?, spesifikasi=?, lokasi=?, gambar=?, status=? 
        WHERE id=?
    ");

    $stmt->bind_param(
        "ssssssi",
        $nama,
        $deskripsi,
        $spesifikasi,
        $lokasi,
        $gambar_baru,
        $status,
        $id
    );

    if ($stmt->execute()) {

        $variasi_id = $_POST['variasi_id'] ?? [];
        $berat      = $_POST['berat'] ?? [];
        $harga      = $_POST['harga'] ?? [];

        for ($i = 0; $i < count($variasi_id); $i++) {

            $vid = (int)$variasi_id[$i];
            $b   = sanitize($berat[$i]);
            $h   = sanitize($harga[$i]);

            if (!empty($b) && !empty($h)) {
                $stmt2 = $conn->prepare("
                    UPDATE harga_alat 
                    SET berat=?, harga=? 
                    WHERE id=?
                ");

                $stmt2->bind_param("sii", $b, $h, $vid);
                $stmt2->execute();
            }
        }

        $conn->commit();

        $_SESSION['success'] = 'Data berhasil diupdate!';
        header("Location: admin.php");
        exit;
    } else {
        $conn->rollback();
        $_SESSION['error'] = 'Error update: ' . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit Alat - Admin Panel</title>
<link rel="icon" href="../logo2.png">
<link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;600;700;800&family=DM+Sans:wght@400;500&display=swap" rel="stylesheet">

<style>
*, *::before, *::after {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
}

:root {
    --navy:#0B1F3A;
    --navy-2:#122B52;
    --blue:#1A56DB;
    --blue-lt:#3B82F6;
    --red:#DC2626;
    --text:#1E293B;
    --muted:#64748B;
    --white:#FFFFFF;
    --radius:12px;
    --radius-lg:20px;
}

body {
    font-family: 'DM Sans', sans-serif;
    background: #F8FAFF;
    color: var(--text);
    line-height: 1.6;
}

h1,h2,h3,h4 {
    font-family: 'Sora', sans-serif;
    line-height: 1.2;
}

/* NAV */
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

/* MAIN */
.main-content {
    max-width: 1000px;
    margin: 0 auto;
    padding: 2rem 1.5rem;
}

/* FORM */
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
    transition: all .2s;
    background: var(--white);
}

.form-input:focus,
.form-textarea:focus,
.form-select:focus {
    outline: none;
    border-color: var(--blue);
    box-shadow: 0 0 0 3px rgba(26,86,219,0.1);
}

/* VARIASI */
.variasi-group {
    background: #F8FAFC;
    border: 2px solid #E2E8F0;
    border-radius: var(--radius);
    padding: 1.5rem;
}

.variasi-row {
    display: grid;
    grid-template-columns: 1fr 1fr auto;
    gap: 1rem;
    margin-bottom: 1rem;
}

.btn-add-variasi {
    background: var(--blue);
    color: white;
    border: none;
    padding: 8px 16px;
    border-radius: 8px;
    cursor: pointer;
}

.btn-remove-variasi {
    background: var(--red);
    color: white;
}

/* BUTTON */
.btn-primary {
    background: linear-gradient(135deg, var(--blue), var(--blue-lt));
    color: white;
    border: none;
    padding: 14px 32px;
    border-radius: var(--radius);
    font-weight: 700;
    cursor: pointer;
}

.btn-secondary {
    background: #6B7280;
    color: white;
    padding: 14px 32px;
    border-radius: var(--radius);
    text-decoration: none;
    text-align: center;
}

/* IMAGE */
.image-preview {
    max-width: 200px;
    height: 150px;
    object-fit: cover;
    border-radius: 12px;
    margin-bottom: 1rem;
}

/* FLASH */
.flash-message {
    padding: 1rem;
    border-radius: 12px;
    margin-bottom: 1.5rem;
    font-weight: 600;
    text-align: center;
}

.flash-error {
    background: #EF4444;
    color: white;
}

/* RESPONSIVE */
@media (max-width:768px) {
    .variasi-row {
        grid-template-columns: 1fr;
    }
}
</style>
</head>

<body>

<nav class="nav">
<div class="nav-inner">
<a href="admin.php" class="nav-brand">
<div>←</div>
<div class="nav-brand-text">
<span>Edit Alat</span>
<span><?= htmlspecialchars($row['nama']); ?></span>
</div>
</a>
</div>
</nav>

<div class="main-content">

<?php if (isset($_SESSION['error'])): ?>
<div class="flash-message flash-error">
    <?= $_SESSION['error']; unset($_SESSION['error']); ?>
</div>
<?php endif; ?>

<div class="form-card">
<div class="form-header">
<h2>Edit <?= htmlspecialchars($row['nama']); ?></h2>
</div>

<form method="POST" enctype="multipart/form-data" class="form-body">

<div class="form-group">
<label class="form-label">Gambar</label>

<?php if (!empty($row['gambar'])): ?>
<img src="../uploads/<?= $row['gambar']; ?>" class="image-preview">
<?php endif; ?>

<input type="file" name="gambar" class="form-input">
</div>

<div class="form-group">
<label class="form-label">Nama</label>
<input type="text" name="nama" value="<?= htmlspecialchars($row['nama']); ?>" class="form-input" required>
</div>

<div class="form-group">
<label class="form-label">Lokasi</label>
<input type="text" name="lokasi" value="<?= htmlspecialchars($row['lokasi']); ?>" class="form-input" required>
</div>

<div class="form-group">
<label class="form-label">Variasi Harga</label>

<div class="variasi-group">
<?php foreach ($variasi_data as $v): ?>
<div class="variasi-row">
<input type="hidden" name="variasi_id[]" value="<?= $v['id']; ?>">
<input type="text" name="berat[]" value="<?= $v['berat']; ?>" class="form-input">
<input type="number" name="harga[]" value="<?= $v['harga']; ?>" class="form-input">
<button type="button" class="btn-remove-variasi" onclick="this.parentElement.remove()">🗑</button>
</div>
<?php endforeach; ?>
</div>

</div>

<div class="form-group">
    <label class="form-label">Status Alat</label>

    <div style="display:flex; gap:15px; margin-top:10px;">
        <label style="
            padding:12px 20px;
            border:2px solid #16A34A;
            border-radius:10px;
            cursor:pointer;
            display:flex;
            align-items:center;
            gap:8px;
        ">
            <input
                type="radio"
                name="status"
                value="Tersedia"
                <?= ($row['status'] == 'Tersedia') ? 'checked' : ''; ?>
            >
            ✅ Tersedia
        </label>

        <label style="
            padding:12px 20px;
            border:2px solid #DC2626;
            border-radius:10px;
            cursor:pointer;
            display:flex;
            align-items:center;
            gap:8px;
        ">
            <input
                type="radio"
                name="status"
                value="Tidak Tersedia"
                <?= ($row['status'] == 'Tidak Tersedia') ? 'checked' : ''; ?>
            >
            ❌ Tidak Tersedia
        </label>
    </div>
</div>

<div class="btn-group">
<a href="admin.php" class="btn-secondary">Batal</a>



<button type="submit" name="update" class="btn-primary">Update</button>
</div>

</form>
</div>

</div>

</body>
</html>
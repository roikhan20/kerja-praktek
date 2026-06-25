<?php
include 'config/koneksi.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$data = mysqli_query($conn, "SELECT * FROM alat_berat WHERE id=$id");
$row = mysqli_fetch_assoc($data);

if (!$row) {
    header("Location: alat.php");
    exit;
}

$alat_id = $row['id'];
$variasi = mysqli_query($conn, "SELECT * FROM harga_alat WHERE alat_id = $alat_id");
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($row['nama']); ?> | PT Cipta Unggul Lintas Samudra</title>
  <link rel="icon" href="logo2.png">
  <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;600;700;800&family=DM+Sans:wght@400;500&display=swap" rel="stylesheet">
  <meta name="description" content="<?= htmlspecialchars($row['nama']); ?> - Sewa alat berat berkualitas di Pamulang dengan operator berpengalaman.">
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    :root {
      --navy:    #0B1F3A;
      --navy-2:  #122B52;
      --blue:    #1A56DB;
      --blue-lt: #3B82F6;
      --accent:  #F59E0B;
      --accent-d:#D97706;
      --green:   #16A34A;
      --green-d: #15803D;
      --text:    #1E293B;
      --muted:   #64748B;
      --surface: #F1F5F9;
      --white:   #FFFFFF;
      --radius:  12px;
      --radius-lg: 20px;
    }

    html { scroll-behavior: smooth; }
    body {
      font-family: 'DM Sans', sans-serif;
      background: #F8FAFF;
      color: var(--text);
      line-height: 1.6;
    }

    h1,h2,h3,h4 { font-family: 'Sora', sans-serif; line-height: 1.2; }

    /* ── NAVBAR ── */
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
    .nav-brand { display: flex; align-items: center; gap: 12px; text-decoration: none; }
    .nav-brand img { height: 40px; width: 40px; object-fit: contain; }
    .nav-brand-text { display: flex; flex-direction: column; }
    .nav-brand-text span:first-child {
      font-family: 'Sora', sans-serif; font-weight: 700; font-size: 15px;
      color: #fff; line-height: 1.2;
    }
    .nav-brand-text span:last-child { font-size: 11px; color: rgba(255,255,255,0.5); letter-spacing: 0.05em; }

    .nav-links { display: flex; gap: 2rem; }
    .nav-links a {
      color: rgba(255,255,255,0.75); text-decoration: none;
      font-size: 14px; font-weight: 500; letter-spacing: 0.02em;
      transition: color .2s;
    }
    .nav-links a:hover { color: var(--accent); }
    .nav-links a.active { color: var(--accent); font-weight: 600; }

    .nav-cta {
      display: flex; align-items: center; gap: 10px;
      background: var(--green); color: #fff;
      padding: 9px 18px; border-radius: 8px;
      font-weight: 600; font-size: 13.5px; text-decoration: none;
      transition: background .2s, transform .15s;
    }
    .nav-cta:hover { background: var(--green-d); transform: translateY(-1px); }
    .nav-cta svg { width: 16px; height: 16px; flex-shrink: 0; }

    .menu-btn {
      display: none; background: none; border: none; cursor: pointer;
      color: #fff; padding: 4px;
    }

    /* ── HERO DETAIL ── */
    .hero-detail {
      background: linear-gradient(135deg, var(--navy) 0%, var(--navy-2) 60%, #1A3A6E 100%);
      position: relative; overflow: hidden;
      padding: 140px 1.5rem 100px;
    }
    .hero-detail::before {
      content: '';
      position: absolute; inset: 0;
      background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.03'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
    }
    .hero-glow {
      position: absolute; top: -120px; right: -120px;
      width: 500px; height: 500px; border-radius: 50%;
      background: radial-gradient(circle, rgba(26,86,219,0.25) 0%, transparent 70%);
      pointer-events: none;
    }
    .hero-inner {
      position: relative; max-width: 1200px; margin: 0 auto;
      text-align: center;
    }
    .hero-badge {
      display: inline-flex; align-items: center; gap: 6px;
      background: rgba(245,158,11,0.15); color: var(--accent);
      border: 1px solid rgba(245,158,11,0.3);
      padding: 5px 14px; border-radius: 100px; font-size: 12.5px; font-weight: 600;
      letter-spacing: 0.04em; text-transform: uppercase; margin-bottom: 20px;
    }
    .hero-badge::before { content: '●'; font-size: 8px; animation: pulse 2s infinite; }
    @keyframes pulse { 0%,100%{opacity:1} 50%{opacity:0.4} }

    .hero-detail h1 {
      font-size: clamp(2.8rem, 6vw, 4.2rem); font-weight: 800;
      color: #fff; margin-bottom: 20px;
    }
    .hero-detail h1 span { color: var(--accent); }
    .hero-sub {
      font-size: 18px; color: rgba(255,255,255,0.85);
      margin-bottom: 40px; line-height: 1.7; max-width: 600px;
      margin-left: auto; margin-right: auto;
    }

    /* ── DETAIL CONTENT ── */
    .section { max-width: 1200px; margin: 0 auto; padding: 80px 1.5rem; }
    .detail-grid {
      display: grid; grid-template-columns: 1fr 1fr; gap: 40px; align-items: start;
    }
    .detail-image {
      position: relative; border-radius: var(--radius-lg); overflow: hidden;
      box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25);
    }
    .detail-image img {
      width: 100%; height: 100%px; object-fit: cover; display: block;
    }
    .detail-image-placeholder {
      width: 100%; height: 450px; background: linear-gradient(135deg, #E2E8F0, #CBD5E1);
      display: flex; align-items: center; justify-content: center;
    }
    .detail-content { }
    .detail-status {
      display: inline-flex; align-items: center; gap: 5px; font-size: 12px; font-weight: 600;
      color: #16A34A; background: #DCFCE7; padding: 4px 12px; border-radius: 100px;
      margin-bottom: 20px;
    }
    .detail-status::before { content: '●'; font-size: 8px; }
    .detail-prices { margin-bottom: 32px; }
    .detail-price-row {
      display: flex; align-items: center; justify-content: space-between;
      padding: 12px 0; border-bottom: 1px solid #F1F5F9;
    }
    .detail-price-row:last-child { border-bottom: none; }
    .detail-price-label { 
      font-size: 14px; color: var(--text); font-weight: 500;
    }
    .detail-price-value {
      font-size: 16px; font-weight: 700; color: var(--blue);
      font-family: 'Sora', sans-serif;
    }
    .detail-section {
      margin-bottom: 32px; padding-bottom: 24px;
      border-bottom: 1px solid #F1F5F9;
    }
    .detail-section:last-child { border-bottom: none; margin-bottom: 0; }
    .detail-section h3 {
      font-family: 'Sora', sans-serif; font-size: 18px; font-weight: 700;
      color: var(--navy); margin-bottom: 16px; display: flex; align-items: center; gap: 8px;
    }
    .detail-desc { 
      font-size: 15px; color: var(--text); line-height: 1.7;
    }
    .detail-specs {
      display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 12px;
    }
    .detail-spec-item {
      display: flex; flex-direction: column;
      padding: 16px; background: #F8FAFC; border-radius: var(--radius);
      border: 1px solid #E2E8F0;
    }
    .detail-spec-label { 
      font-size: 12px; color: var(--muted); font-weight: 500; 
      margin-bottom: 4px; text-transform: uppercase; letter-spacing: 0.05em;
    }
    .detail-spec-value { 
      font-size: 14px; font-weight: 600; color: var(--navy);
    }
    .detail-location {
      font-size: 14px; color: var(--muted); margin-bottom: 32px;
      display: flex; align-items: center; gap: 8px;
    }
    .detail-actions { display: flex; gap: 16px; flex-wrap: wrap; }
    .btn-primary {
      flex: 1; min-width: 160px;
      display: flex; align-items: center; justify-content: center; gap: 10px;
      background: var(--green); color: #fff; padding: 16px 24px;
      border-radius: var(--radius); font-weight: 700; font-size: 15px; text-decoration: none;
      transition: all .2s; box-shadow: 0 8px 25px rgba(22,163,74,0.3);
    }
    .btn-primary:hover { 
      background: var(--green-d); transform: translateY(-2px);
      box-shadow: 0 12px 35px rgba(22,163,74,0.4);
    }
    .btn-secondary {
      flex: 1; min-width: 160px;
      display: flex; align-items: center; justify-content: center; gap: 10px;
      background: transparent; color: var(--blue);
      padding: 16px 24px; border-radius: var(--radius); border: 2px solid var(--blue);
      font-weight: 700; font-size: 15px; text-decoration: none;
      transition: all .2s;
    }
    .btn-secondary:hover {
      background: var(--blue); color: #fff; transform: translateY(-2px);
    }

    /* ── CTA ── */
    .cta-section {
      background: linear-gradient(135deg, var(--navy) 0%, #1A3A6E 100%);
      padding: 80px 1.5rem; text-align: center; position: relative; overflow: hidden;
    }
    .cta-section::before {
      content:''; position: absolute; top: -150px; left: 50%; transform: translateX(-50%);
      width: 600px; height: 600px; border-radius: 50%;
      background: radial-gradient(circle, rgba(26,86,219,0.2) 0%, transparent 70%);
    }
    .cta-section h3 { 
      font-size: clamp(1.8rem, 3.5vw, 2.5rem); font-weight: 800; color: #fff; 
      margin-bottom: 14px; position: relative; 
    }
    .cta-section p { 
      font-size: 16px; color: rgba(255,255,255,0.6); margin-bottom: 36px; position: relative; 
    }
    .btn-cta-wa {
      display: inline-flex; align-items: center; gap: 10px;
      background: var(--green); color: #fff; padding: 16px 36px;
      border-radius: 12px; font-weight: 700; font-size: 16px; text-decoration: none;
      transition: background .2s, transform .15s; box-shadow: 0 6px 28px rgba(22,163,74,0.4);
    }
    .btn-cta-wa:hover { background: var(--green-d); transform: translateY(-2px); }

    /* ── FOOTER ── */
    .footer { background: #060F1E; padding: 36px 1.5rem; text-align: center; }
    .footer p { color: rgba(255,255,255,0.35); font-size: 13px; }

    /* ── MOBILE SIDEBAR ── */
    .overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.6); z-index: 200; }
    .sidebar {
      position: fixed; top: 0; left: -280px; width: 280px; height: 100%; z-index: 201;
      background: var(--navy); padding: 28px 24px;
      transition: left .3s cubic-bezier(.4,0,.2,1);
      border-right: 1px solid rgba(255,255,255,0.08);
    }
    .sidebar-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 36px; }
    .sidebar-close { background: none; border: none; color: rgba(255,255,255,0.5); cursor: pointer; font-size: 22px; }
    .sidebar a { display: block; padding: 14px 0; color: rgba(255,255,255,0.75); text-decoration: none; font-size: 16px; font-weight: 500; border-bottom: 1px solid rgba(255,255,255,0.07); }
    .sidebar a:hover { color: var(--accent); }
    .sidebar-wa {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
      margin-top: 28px;
      background: var(--green);
      color: #fff;
      padding: 20px;
      border-radius: 30px;
      font-weight: 700;
      text-decoration: none;
      text-align: center;
      width: 100%;
    }
    /* ── RESPONSIVE ── */
    @media (max-width: 900px) {
      .nav-links, .nav-cta { display: none; }
      .menu-btn { display: block; }
      .detail-grid { grid-template-columns: 1fr; gap: 32px; }
      .detail-image img { height: 300px; }
      .detail-actions { flex-direction: column; }
    }
    @media (max-width: 640px) {
      .hero-detail { padding: 100px 1.5rem 60px; }
      .hero-detail h1 { font-size: 2.4rem; }
      .section { padding: 40px 1.5rem; }
    }
  </style>
</head>

<body>

<!-- OVERLAY & SIDEBAR -->
<div class="overlay" id="overlay" onclick="closeSidebar()"></div>
<div class="sidebar" id="sidebar">
  <div class="sidebar-header">
    <span style="font-family:'Sora',sans-serif;font-weight:700;color:#fff;font-size:15px;">Menu</span>
    <button class="sidebar-close" onclick="closeSidebar()">✕</button>
  </div>
  <a href="homepage.php">Home</a>
  <a href="alat.php" class="active">Alat Berat</a>
  <a href="lokasi.php">Lokasi</a>
  <a href="kontak.php">Kontak</a>
  <a href="https://wa.me/628111804218" class="sidebar-wa">
    <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
    WhatsApp Sekarang
  </a>
</div>

<!-- NAVBAR -->
<nav class="nav">
  <div class="nav-inner">
    <a href="homepage.php" class="nav-brand">
      <img src="logo2.png" alt="Logo PT Cipta Unggul Lintas Samudra">
      <div class="nav-brand-text">
        <span>PT Cipta Unggul</span>
        <span>LINTAS SAMUDRA</span>
      </div>
    </a>
    <div class="nav-links">
      <a href="homepage.php">Home</a>
      <a href="alat.php" class="active">Alat Berat</a>
      <a href="lokasi.php">Lokasi</a>
      <a href="kontak.php">Kontak</a>
    </div>
    <a href="https://wa.me/628111804218" class="nav-cta">
      <svg viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
      WhatsApp
    </a>
    <button class="menu-btn" id="menu-btn" onclick="openSidebar()" aria-label="Buka menu">
      <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
    </button>
  </div>
</nav>

<!-- HERO DETAIL -->
<section class="hero-detail">
  <div class="hero-glow"></div>
  <div class="hero-inner">
    <div class="hero-badge">Detail Alat Berat</div>
    <h1><?= htmlspecialchars($row['nama']); ?></h1>
    <p class="hero-sub">Spesifikasi lengkap dan harga sewa terjangkau untuk proyek konstruksi Anda.</p>
  </div>
</section>

<!-- DETAIL CONTENT -->
<section class="section">
  <div class="detail-grid">
    <!-- IMAGE -->
    <div>
      <?php if(!empty($row['gambar'])): ?>
        <div class="detail-image">
          <img src="uploads/<?= htmlspecialchars($row['gambar']); ?>" alt="<?= htmlspecialchars($row['nama']); ?>">
        </div>
      <?php else: ?>
        <div class="detail-image">
          <div class="detail-image-placeholder">
            <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="#94A3B8" stroke-width="1.5"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/></svg>
          </div>
        </div>
      <?php endif; ?>
    </div>

    <!-- CONTENT -->
    <div class="detail-content">
      <div class="detail-status">Tersedia</div>

      <!-- HARGA -->
      <div class="detail-prices">
        <h3 style="font-family:'Sora',sans-serif;font-size:20px;font-weight:700;color:var(--navy);margin-bottom:20px;">
          Harga Sewa
        </h3>
        <?php 
        mysqli_data_seek($variasi, 0); // Reset pointer
        while($v = mysqli_fetch_assoc($variasi)) : 
        ?>
        <div class="detail-price-row">
          <span class="detail-price-label">
            <?= htmlspecialchars($v['berat']); ?> / <?= $v['jam']; ?> jam 
            <?php if(!empty($v['keterangan'])): ?>(<?= htmlspecialchars($v['keterangan']); ?>)<?php endif; ?>
          </span>
          <span class="detail-price-value">Rp <?= number_format($v['harga']); ?></span>
        </div>
        <?php endwhile; ?>
      </div>

      <!-- DESKRIPSI -->
      <div class="detail-section">
        <h3>
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--navy)" stroke-width="2">
            <circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line>
          </svg>
          Deskripsi
        </h3>
        <p class="detail-desc"><?= nl2br(htmlspecialchars($row['deskripsi'])); ?></p>
      </div>

      <!-- SPESIFIKASI -->
      <div class="detail-section">
        <h3>
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--navy)" stroke-width="2">
            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="9" y1="9" x2="15" y2="15"></line><line x1="15" y1="9" x2="9" y2="15"></line>
          </svg>
          Spesifikasi
        </h3>
        <div class="detail-specs">
          <div class="detail-spec-item">
            <span class="detail-spec-label">Lokasi</span>
            <span class="detail-spec-value"><?= htmlspecialchars($row['lokasi']); ?></span>
          </div>
          <?php if(!empty($row['spesifikasi'])): ?>
          <div class="detail-spec-item">
            <span class="detail-spec-label">Detail Teknis</span>
            <span class="detail-spec-value"><?= htmlspecialchars($row['spesifikasi']); ?></span>
          </div>
          <?php endif; ?>
        </div>
      </div>

      <!-- LOKASI -->
      <div class="detail-location">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
          <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
          <circle cx="12" cy="10" r="3"/>
        </svg>
        <?= htmlspecialchars($row['lokasi']); ?>
      </div>

      <!-- ACTIONS -->
      <div class="detail-actions">
        <a href="https://wa.me/628111804218?text=Saya%20ingin%20sewa%20<?= urlencode($row['nama']); ?>%20-%20<?= urlencode($row['lokasi']); ?>" class="btn-primary">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
          </svg>
          WhatsApp
        </a>
        <a href="alat.php" class="btn-secondary">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12,19 5,12 12,5"></polyline>
          </svg>
          Kembali
        </a>
      </div>
    </div>
  </div>
</section>

<!-- CTA -->
<section class="cta-section">
  <h3>Siap Memulai Proyek Anda?</h3>
  <p>Hubungi tim kami sekarang untuk penawaran terbaik dan jadwal ketersediaan.</p>
  <a href="https://wa.me/628111804218?text=Saya%20ingin%20tanya%20tentang%20sewa%20alat%20berat" class="btn-cta-wa">
    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
    Chat WhatsApp Sekarang
  </a>
</section>

<!-- FOOTER -->
<footer class="footer">
  <p>© 2026 PT Cipta Unggul Lintas Samudra · Sewa Alat Berat Pamulang & Tangerang Selatan</p>
</footer>

<script>
  function openSidebar() {
    document.getElementById('sidebar').style.left = '0';
    document.getElementById('overlay').style.display = 'block';
    document.body.style.overflow = 'hidden';
  }
  function closeSidebar() {
    document.getElementById('sidebar').style.left = '-280px';
    document.getElementById('overlay').style.display = 'none';
    document.body.style.overflow = '';
  }

  // Set active nav link
  document.querySelectorAll('.nav-links a').forEach(link => {
    if (link.href === window.location.href || window.location.href.includes('detail.php')) {
      link.classList.add('active');
    }
  });
</script>

</body>
</html>
<?php
include 'config/koneksi.php';
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Lokasi & Area Layanan | Sewa Alat Berat Pamulang</title>
  <link rel="icon" href="logo2.png">
  <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;600;700;800&family=DM+Sans:wght@400;500&display=swap" rel="stylesheet">
  <meta name="description" content="Lokasi cabang PT Cipta Unggul Lintas Samudra di Pamulang, Tangerang Selatan. Melayani Ciputat, BSD, Serpong dan area Jabodetabek.">
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

    /* ── HERO ── */
    .hero {
      background: linear-gradient(135deg, var(--navy) 0%, var(--navy-2) 60%, #1A3A6E 100%);
      position: relative; overflow: hidden;
      padding: 140px 1.5rem 100px;
    }
    .hero::before {
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

    .hero h1 {
      font-size: clamp(2.8rem, 6vw, 4.2rem); font-weight: 800;
      color: #fff; margin-bottom: 20px;
    }
    .hero h1 span { color: var(--accent); }
    .hero-sub {
      font-size: 18px; color: rgba(255,255,255,0.85);
      margin-bottom: 40px; line-height: 1.7; max-width: 600px;
      margin-left: auto; margin-right: auto;
    }
    .hero-buttons { display: flex; gap: 16px; justify-content: center; flex-wrap: wrap; }

    /* ── SECTION ── */
    .section { max-width: 1200px; margin: 0 auto; padding: 20px 1.5rem; }
    .section h2 {
      text-align: center; font-size: clamp(2rem, 4vw, 2.5rem); font-weight: 800;
      color: var(--navy); margin-bottom: 12px;
    }
    .section p.subtitle {
      text-align: center; font-size: 16px; color: var(--muted); max-width: 600px; margin: 0 auto 60px;
    }

    /* ── MAP ── */
    .map-container {
      background: var(--white); border-radius: var(--radius-lg); 
      overflow: hidden; box-shadow: 0 20px 60px rgba(11,31,58,0.12);
      margin-bottom: 3rem;
    }
    .map-container iframe {
      width: 100%; height: 450px; border: none; display: block;
    }

    /* ── INFO GRID ── */
    .info-grid {
      display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); 
      gap: 24px; margin-bottom: 4rem;
    }
    .info-card {
      background: var(--white); padding: 32px 28px; border-radius: var(--radius-lg);
      border: 1px solid #F1F5F9; text-align: center; transition: all .2s;
      box-shadow: 0 4px 20px rgba(11,31,58,0.08);
    }
    .info-card:hover {
      transform: translateY(-4px); box-shadow: 0 20px 60px rgba(26,86,219,0.12);
      border-color: var(--blue);
    }
    .info-card h3 {
      font-size: 18px; font-weight: 700; color: var(--blue);
      margin-bottom: 16px;
    }
    .info-card p { color: var(--text); font-size: 15px; margin-bottom: 8px; }
    .info-card .info-muted {
      color: var(--muted); font-size: 14px;
    }

    /* ── BRANCH GRID ── */
    .branch-grid {
      display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); 
      gap: 20px; margin-top: 2rem;
    }
    .branch-card {
      background: var(--white); padding: 24px; border-radius: var(--radius-lg);
      border: 1px solid #E2E8F0; transition: all .2s; text-align: center;
      box-shadow: 0 4px 20px rgba(11,31,58,0.08);
    }
    .branch-card:hover {
      transform: translateY(-4px); box-shadow: 0 16px 48px rgba(11,31,58,0.12);
      border-color: var(--blue);
    }
    .branch-card h4 {
      font-size: 16px; font-weight: 700; color: var(--blue);
      margin-bottom: 8px;
    }
    .branch-card p { color: var(--muted); font-size: 14px; }

    /* ── CTA ── */
    .cta-section {
      background: linear-gradient(135deg, var(--navy) 0%, #1A3A6E 100%);
      padding: 80px 1.5rem; text-align: center; position: relative; overflow: hidden;
    }
    .cta-section::before {
      content:''; 
      position: absolute; 
      top: -150px; 
      left: 50%; 
      transform: translateX(-50%);
      width: 600px; 
      height: 600px; 
      border-radius: 50%;
      background: radial-gradient(circle, rgba(26,86,219,0.2) 0%, transparent 70%);
      
      /* PERBAIKAN: */
      pointer-events: none; /* Mouse akan "menembus" elemen ini */
      z-index: 1;           /* Letakkan di bawah teks dan tombol */
    }
    .cta-section h3 { 
      font-size: clamp(1.8rem, 3.5vw, 2.5rem); font-weight: 800; color: #fff; 
      margin-bottom: 14px; position: relative; 
    }
    .cta-section p { 
      font-size: 16px; color: rgba(255,255,255,0.6); margin-bottom: 36px; position: relative; 
    }
    .btn-cta-primary {
      display: inline-flex; 
      align-items: center; 
      gap: 10px;
      background: var(--green); 
      color: #fff; 
      padding: 16px 36px;
      border-radius: 12px; 
      font-weight: 700; 
      font-size: 16px; 
      text-decoration: none;
      transition: background .2s, transform .15s; 
      box-shadow: 0 6px 28px rgba(22,163,74,0.4);

      /* PERBAIKAN: */
      position: relative;    /* Syarat agar z-index berfungsi */
      z-index: 10;           /* Angka tinggi agar pasti di atas ::before */
      cursor: pointer;       /* Indikator visual wajib */
    }
    .btn-cta-primary:hover { background: var(--green-d); transform: translateY(-2px); }

    /* ── BUTTONS ── */
    .btn-maps {
      display: inline-flex; align-items: center; gap: 8px;
      background: var(--white); color: var(--blue); padding: 14px 28px;
      border-radius: 12px; font-weight: 600; font-size: 15px; text-decoration: none;
      border: 2px solid var(--blue); transition: all .2s;
    }
    .btn-maps:hover { background: var(--blue); color: #fff; transform: translateY(-1px); }

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
      .info-grid, .branch-grid { grid-template-columns: 1fr; }
      .hero { padding: 120px 1.5rem 80px; }
      .hero-buttons { flex-direction: column; align-items: center; }
      .section { padding: 60px 1.5rem; }
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
  <a href="alat.php">Alat Berat</a>
  <a href="lokasi.php" class="active">Lokasi</a>
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
      <a href="alat.php">Alat Berat</a>
      <a href="lokasi.php" class="active">Lokasi</a>
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

<!-- HERO -->
<section class="hero">
  <div class="hero-glow"></div>
  <div class="hero-inner">
    <div class="hero-badge">Lokasi Terdekat</div>
    <h1><span>Lokasi</span> & Area Layanan</h1>
    <p class="hero-sub">Kami siap melayani kebutuhan sewa alat berat di Pamulang, Ciputat, BSD, Serpong & seluruh Tangerang Selatan.</p>
    <div class="hero-buttons">
      <a href="#map" class="btn-cta-primary" style="background: var(--blue); box-shadow: 0 6px 28px rgba(26,86,219,0.4);">
        Lihat Peta Lokasi
      </a>
      <a href="https://wa.me/628111804218?text=Saya%20dapat%20informasi%20dari%20website.%20Saya%20ingin%20sewa%20alat%20berat%20sekarang!" class="btn-cta-primary">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
        Chat WhatsApp
      </a>
    </div>
  </div>
</section>

<!-- MAP SECTION -->
<section class="section" id="map">
  <div class="map-container">
    <iframe 
      src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3963.7207166594076!2d106.8097371743036!3d-6.556897564096675!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69c543b7fc2ca5%3A0x15cbfd78026b8ad7!2sJasa%20Sewa%20Crane%20%26%20Sewa%20Forklift%20%7C%20PT%20Cipta%20Unggul%20Lintas%20Samudra!5e0!3m2!1sid!2sid!4v1782265308370!5m2!1sid!2sid" 
      allowfullscreen="" 
      loading="lazy" 
      referrerpolicy="no-referrer-when-downgrade">
    </iframe>
  </div>
  <div style="text-align: center; max-width: 600px; margin: 0 auto;">
    <a href="https://maps.google.com" target="_blank" class="btn-maps">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
        <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
      </svg>
      Lihat di Google Maps
    </a>
  </div>
</section>

<!-- INFO GRID -->
<section class="section">
  <h2>Informasi Kontak</h2>
  <p class="subtitle">Detail lengkap alamat dan jam operasional kami</p>
  
  <div class="info-grid">
    <div class="info-card">
      <div style="font-size: 48px; margin-bottom: 20px;"></div>
      <h3>Alamat Operasional</h3>
      <p>Jl. Sodetan Buaran no 37</p>
      <p>Kel. Buaran, Kec. Serpong</p>
      <p>Kota Tangerang Selatan, Banten 15310</p>
      <p class="info-muted">Melayani Ciputat, BSD, Serpong & sekitarnya</p>
    </div>

    <div class="info-card">
      <div style="font-size: 48px; margin-bottom: 20px;"></div>
      <h3>Jam Operasional</h3>
      <p>Senin - Minggu</p>
      <p>24 hours ready</p>
      <p class="info-muted">Selalu siap saat Anda butuhkan, hubungi kami kapan saja</p>
    </div>

    <div class="info-card">
      <div style="font-size: 48px; margin-bottom: 20px;"></div>
      <h3>Layanan Kami</h3>
      <p>Sewa alat berat harian & proyek</p>
      <p>Siap melayani kebutuhan proyek Anda</p>
      <p class="info-muted">Tersedia dengan operator & pengiriman ke lokasi</p>
    </div>
  </div>
</section>

<!-- BRANCHES -->
<section class="section">
  <h2>Jaringan Cabang Kami</h2>
  <p class="subtitle">Mencakup seluruh Jabodetabek dan Jawa Barat untuk kebutuhan proyek Anda</p>
  
  <div class="branch-grid">
    <div class="branch-card">
      <h4>Bogor (Pusat)</h4>
      <p>Kantor utama & pengelolaan operasional</p>
    </div>
    <div class="branch-card">
      <h4>Tangerang Kota dan Tangsel</h4>
      <p>Melayani Daerah Tangerang Sekitarnya</p>
    </div>
    <div class="branch-card">
      <h4>Jakarta Utara</h4>
      <p>Area distribusi Jakarta Utara & sekitarnya</p>
    </div>
    <div class="branch-card">
      <h4>Bandung</h4>
      <p>Melayani proyek wilayah Jawa Barat</p>
    </div>
    <div class="branch-card">
      <h4>Jakarta Selatan</h4>
      <p>Melayani Jakarta Selatan dan sekitarnya</p>
    </div>
    <div class="branch-card">
      <h4>Jakarta Timur</h4>
      <p>Melayani Jakarta Timur dan sekitarnya</p>
    </div>
    <div class="branch-card">
      <h4>Bekasi</h4>
      <p>Melayani Bekasi dan sekitarnya</p>
    </div>
  </div>
</section>

<!-- CTA -->
<section class="cta-section">
  <h3>Butuh Alat Berat Sekarang?</h3>
  <p>Hubungi kami langsung untuk respon cepat dan penawaran terbaik di lokasi terdekat Anda.</p>
  <a href="https://wa.me/628111804218?text=Saya%20dapat%20informasi%20dari%20website.%20Saya%20ingin%20sewa%20alat%20berat%20sekarang!" class="btn-cta-primary">
    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
    Chat WhatsApp Sekarang
  </a>
</section>

<!-- FOOTER -->
<footer class="footer">
  <p>© 2026 PT Cipta Unggul Lintas Samudra · Sewa Alat Berat Terpercaya Pamulang & Tangerang Selatan</p>
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
  document.querySelectorAll('.nav-links a, .sidebar a').forEach(link => {
    if (link.href === window.location.href || (window.location.pathname === '/lokasi.php' && link.textContent.trim() === 'Lokasi')) {
      link.classList.add('active');
    }
  });

  // Smooth scroll for anchor links
  document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
      e.preventDefault();
      const target = document.querySelector(this.getAttribute('href'));
      if (target) {
        target.scrollIntoView({ behavior: 'smooth', block: 'start' });
      }
    });
  });
</script>

</body>
</html>
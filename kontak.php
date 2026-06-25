<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sewa Alat Berat Pamulang | PT Cipta Unggul Lintas Samudra</title>
  <link rel="icon" href="logo2.png">
  <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;600;700;800&family=DM+Sans:wght@400;500&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <meta name="description" content="Sewa alat berat forklift, crane, excavator di Pamulang Tangerang Selatan. Siap pakai dengan operator berpengalaman. Harga kompetitif, respon cepat 24/7.">
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
      padding: 140px 1.5rem 106px;
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
      font-size: clamp(2.8rem, 6vw, 4.5rem); font-weight: 800;
      color: #fff; margin-bottom: 20px;
    }
    .hero h1 span { color: var(--accent); }
    .hero-sub {
      font-size: 18px; color: rgba(255,255,255,0.85);
      margin-bottom: 24px; line-height: 1.7; max-width: 600px;
      margin-left: auto; margin-right: auto;
    }
    .hero-features {
      display: flex; flex-wrap: wrap; gap: 12px; justify-content: center;
      margin-bottom: 40px; font-size: 14px;
    }
    .hero-feature {
      display: flex; align-items: center; gap: 4px;
      background: rgba(255,255,255,0.15); padding: 6px 12px;
      border-radius: 20px; backdrop-filter: blur(10px);
    }
    .hero-buttons { display: flex; gap: 16px; justify-content: center; flex-wrap: wrap; }

    /* ── FEATURES ── */
    .features-section {
      background: var(--white); padding: 80px 1.5rem;
      position: relative;
    }
    .features-section::before {
      content: ''; position: absolute; top: 0; left: 0; right: 0;
      height: 1px; background: linear-gradient(90deg, transparent, #E2E8F0, transparent);
    }
    .features-grid {
      max-width: 1200px; margin: 0 auto;
      display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 32px;
    }
    .feature-card {
      display: flex; flex-direction: column; align-items: center; text-align: center;
      padding: 40px 24px; border-radius: var(--radius-lg);
      background: var(--white); border: 1px solid #F1F5F9;
      transition: all .2s; position: relative;
    }
    .feature-card::before {
      content: ''; position: absolute; top: 0; left: 50%; transform: translateX(-50%);
      width: 60px; height: 4px; background: linear-gradient(90deg, var(--blue), var(--accent));
      border-radius: 0 0 4px 4px;
    }
    .feature-card:hover {
      transform: translateY(-8px); box-shadow: 0 20px 60px rgba(26,86,219,0.12);
      border-color: var(--blue);
    }
    .feature-icon { 
      width: 72px; height: 72px; border-radius: 50%; 
      background: linear-gradient(135deg, var(--blue), var(--blue-lt));
      display: flex; align-items: center; justify-content: center;
      margin-bottom: 20px; color: #fff; font-size: 28px;
    }
    .feature-card h3 {
      font-size: 20px; font-weight: 700; color: var(--navy); margin-bottom: 12px;
    }
    .feature-card p { color: var(--muted); font-size: 15px; }

    /* ── SOCIAL ── */
    .social-section {
      background: var(--surface); padding: 60px 1.5rem; text-align: center;
    }
    .social-grid {
      max-width: 1200px; margin: 0 auto;
      display: flex; justify-content: center; gap: 24px; flex-wrap: wrap;
      margin-top: 32px;
    }
    .social-link {
      width: 64px; height: 64px; border-radius: 50%;
      display: flex; align-items: center; justify-content: center;
      font-size: 24px; text-decoration: none; transition: all .2s;
      box-shadow: 0 8px 24px rgba(0,0,0,0.12);
    }
    .social-link:hover { transform: translateY(-4px) scale(1.05); }

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
    .btn-cta-primary, .btn-cta-secondary {
      display: inline-flex; align-items: center; gap: 10px;
      padding: 16px 36px; border-radius: 12px; font-weight: 700; font-size: 16px; 
      text-decoration: none; transition: all .2s; box-shadow: 0 6px 28px rgba(0,0,0,0.2);
    }
    .btn-cta-primary {
      background: var(--green); color: #fff; 
      box-shadow: 0 6px 28px rgba(22,163,74,0.4);
    }
    .btn-cta-primary:hover { background: var(--green-d); transform: translateY(-2px); }
    .btn-cta-secondary {
      background: var(--white); color: var(--blue); border: 2px solid var(--white);
    }
    .btn-cta-secondary:hover { background: rgba(255,255,255,0.9); transform: translateY(-2px); }

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
      .features-grid { grid-template-columns: 1fr; }
      .hero { padding: 120px 1.5rem 80px; }
      .hero-buttons { flex-direction: column; align-items: center; }
      .social-grid { gap: 16px; }
    }
    @media (max-width: 640px) {
      .hero h1 { font-size: 2.5rem; }
      .features-section, .social-section { padding: 60px 1.5rem; }
      .hero-features { flex-direction: column; align-items: center; gap: 8px; }
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
  <a href="homepage.php" class="active">Home</a>
  <a href="alat.php">Alat Berat</a>
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
      <a href="alat.php">Alat Berat</a>
      <a href="lokasi.php">Lokasi</a>
      <a href="kontak.php" class="active">Kontak</a>
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
    <div class="hero-badge">Respon 24/7</div>
    <h1>Butuh <span>Alat Berat?</span></h1>
    <p class="hero-sub">Hubungi kami sekarang juga! Tim profesional siap membantu kebutuhan proyek Anda dengan respon super cepat.</p>

    <div class="hero-buttons">
      <a href="https://wa.me/628111804218?text=Saya%20dapat%20informasi%20dari%20website.%20Saya%20ingin%20sewa%20alat%20berat%20sekarang!" class="btn-cta-primary">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
        Chat WhatsApp Sekarang
      </a>
      <a href="tel:628111804218" class="btn-cta-secondary">📞 Telepon Langsung</a>
    </div>
  </div>
</section>

<!-- KAPAN MEMBUTUHKAN -->
<section class="features-section">
  <div class="features-grid">
    <div class="feature-card">
      <div class="feature-icon">🏗️</div>
      <h3>Proyek Konstruksi</h3>
      <p>Pembangunan gedung, jalan, jembatan, atau renovasi besar-besaran membutuhkan alat berat yang handal.</p>
    </div>
    <div class="feature-card">
      <div class="feature-icon">📦</div>
      <h3>Pemindahan Barang Berat</h3>
      <p>Memindahkan mesin industri, material konstruksi, atau barang berat lainnya dengan aman dan cepat.</p>
    </div>
    <div class="feature-card">
      <div class="feature-icon">🌾</div>
      <h3>Pekerjaan Lapangan</h3>
      <p>Proyek di area terpencil, lokasi sulit dijangkau, atau pekerjaan outdoor lainnya.</p>
    </div>
  </div>
</section>

<!-- KENAPA MEMILIH KAMI -->
<section class="features-section" style="background: var(--surface);">
  <div style="max-width: 1200px; margin: 0 auto;">
    <h2 style="text-align: center; font-size: clamp(2rem, 4vw, 2.5rem); font-weight: 800; color: var(--navy); margin-bottom: 12px;">
      Kenapa Memilih Layanan Kami?
    </h2>
    <p style="text-align: center; font-size: 16px; color: var(--muted); max-width: 600px; margin: 0 auto 60px;">
      Proses mudah, alat siap pakai, dan didukung tim profesional berpengalaman
    </p>
    
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 32px;">
      <div style="background: var(--white); padding: 32px; border-radius: var(--radius-lg); border: 1px solid #F1F5F9; text-align: center;">
        <h3 style="font-size: 20px; font-weight: 700; color: var(--blue); margin-bottom: 16px;">Keunggulan Kami</h3>
        <div style="font-size: 14px; color: var(--muted); line-height: 1.8;">
          <p>✔ Alat berat siap pakai</p>
          <p>✔ Respon cepat 24/7</p>
          <p>✔ Operator bersertifikat</p>
        </div>
      </div>
      <div style="background: var(--white); padding: 32px; border-radius: var(--radius-lg); border: 1px solid #F1F5F9; text-align: center;">
        <h3 style="font-size: 20px; font-weight: 700; color: var(--blue); margin-bottom: 16px;">Cara Pemesanan</h3>
        <div style="font-size: 14px; color: var(--muted); line-height: 1.8;">
          <p>1. Hubungi via WhatsApp</p>
          <p>2. Konsultasi kebutuhan</p>
          <p>3. Alat dikirim ke lokasi</p>
        </div>
      </div>
      <div style="background: var(--white); padding: 32px; border-radius: var(--radius-lg); border: 1px solid #F1F5F9; text-align: center;">
        <h3 style="font-size: 20px; font-weight: 700; color: var(--blue); margin-bottom: 16px;">Layanan Lengkap</h3>
        <p style="font-size: 15px; color: var(--text);">Sewa harian & proyek panjang</p>
        <p style="font-size: 15px; color: var(--text);">Pengiriman ke seluruh lokasi</p>
        <p style="font-size: 13px; color: var(--muted);">Operator terlatih tersedia</p>
      </div>
    </div>
  </div>
</section>

<!-- SOCIAL MEDIA -->
<section class="social-section">
  <h2 style="text-align: center; font-size: clamp(2rem, 4vw, 2.5rem); font-weight: 800; color: var(--navy); margin-bottom: 12px;">
    Ikuti Update Kami
  </h2>
  <p style="text-align: center; font-size: 16px; color: var(--muted); max-width: 600px; margin: 0 auto 40px;">
    Lihat aktivitas proyek terbaru dan tips sewa alat berat
  </p>
  
  <div class="social-grid">
    <!-- TIKTOK -->
  <a href="https://www.tiktok.com/@ciptaunggulsamudra" target="_blank" rel="noopener noreferrer" 
     class="social-link" style="
       background: linear-gradient(135deg, #000000, #010101);
       color: #fff; width: 52px; height: 52px; border-radius: 14px;
       display: flex; align-items: center; justify-content: center;
       text-decoration: none; font-size: 22px;
       box-shadow: 0 6px 20px rgba(0,0,0,0.2); transition: all 0.3s cubic-bezier(0.4,0,0.2,1);
     "
     onmouseover="this.style.transform='translateY(-4px) scale(1.08)'; this.style.boxShadow='0 12px 35px rgba(0,0,0,0.35)'"
     onmouseout="this.style.transform='translateY(0) scale(1)'; this.style.boxShadow='0 6px 20px rgba(0,0,0,0.2)'">
    <i class="fab fa-tiktok"></i>
  </a>

  <!-- YOUTUBE -->
  <a href="https://www.youtube.com/@ciptaunggulsamudra" target="_blank" rel="noopener noreferrer" 
     class="social-link" style="
       background: #FF0000; color: #fff; width: 52px; height: 52px; border-radius: 14px;
       display: flex; align-items: center; justify-content: center;
       text-decoration: none; font-size: 22px;
       box-shadow: 0 6px 20px rgba(255,0,0,0.3); transition: all 0.3s cubic-bezier(0.4,0,0.2,1);
     "
     onmouseover="this.style.transform='translateY(-4px) scale(1.08)'; this.style.boxShadow='0 12px 35px rgba(255,0,0,0.45)'"
     onmouseout="this.style.transform='translateY(0) scale(1)'; this.style.boxShadow='0 6px 20px rgba(255,0,0,0.3)'">
    <i class="fab fa-youtube"></i>
  </a>

  <!-- INSTAGRAM -->
  <a href="https://www.instagram.com/ciptaunggulsamudra/" target="_blank" rel="noopener noreferrer" 
     class="social-link" style="
       background: linear-gradient(135deg, #E4405F, #F77737, #FDCF00);
       color: #fff; width: 52px; height: 52px; border-radius: 14px;
       display: flex; align-items: center; justify-content: center;
       text-decoration: none; font-size: 22px;
       box-shadow: 0 6px 20px rgba(228,64,95,0.4); transition: all 0.3s cubic-bezier(0.4,0,0.2,1);
     "
     onmouseover="this.style.transform='translateY(-4px) scale(1.08)'; this.style.boxShadow='0 12px 35px rgba(228,64,95,0.5)'"
     onmouseout="this.style.transform='translateY(0) scale(1)'; this.style.boxShadow='0 6px 20px rgba(228,64,95,0.4)'">
    <i class="fab fa-instagram"></i>
  </a>

  <!-- FACEBOOK -->
  <a href="https://www.facebook.com/sarana.c.unggul" target="_blank" rel="noopener noreferrer" 
     class="social-link" style="
       background: #1877F2; color: #fff; width: 52px; height: 52px; border-radius: 14px;
       display: flex; align-items: center; justify-content: center;
       text-decoration: none; font-size: 22px;
       box-shadow: 0 6px 20px rgba(24,119,242,0.4); transition: all 0.3s cubic-bezier(0.4,0,0.2,1);
     "
     onmouseover="this.style.transform='translateY(-4px) scale(1.08)'; this.style.boxShadow='0 12px 35px rgba(24,119,242,0.5)'"
     onmouseout="this.style.transform='translateY(0) scale(1)'; this.style.boxShadow='0 6px 20px rgba(24,119,242,0.4)'">
    <i class="fab fa-facebook-f"></i>
  </a>
  </div>
</section>

<!-- FINAL CTA -->
<section class="cta-section">
  <h3>Siap Memulai Proyek Anda?</h3>
  <p>Hubungi sekarang untuk penawaran terbaik dan ketersediaan terbaru!</p>
  <div style="display: flex; gap: 16px; justify-content: center; flex-wrap: wrap;">
    <a href="https://wa.me/628111804218?text=Saya%20dapat%20informasi%20dari%20website.%20Saya%20ingin%20sewa%20alat%20berat%20sekarang!" class="btn-cta-primary">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
      Chat Sekarang
    </a>
    <a href="tel:628111804218" class="btn-cta-secondary">📞 Telepon</a>
  </div>
</section>
<!-- FOOTER -->
<footer class="footer">
  <p>© 2026 PT Cipta Unggul Lintas Samudra · Sewa Alat Berat Terpercaya #1 Pamulang Tangerang Selatan</p>
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
    if (link.href === window.location.href) {
      link.classList.add('active');
    }
  });
</script>

</body>
</html>
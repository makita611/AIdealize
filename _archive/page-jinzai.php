<?php /* Template Name: Blank - Jinzai LP */ ?>
<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>人材定着サービス｜AI × 360度評価で離職率を下げる｜AIidealize</title>
<meta name="description" content="目標・自己他者・顧客・実績の4軸360度評価とAIを組み合わせ、介護・看護・中小企業の離職率改善をサポート。まず無料診断から。">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@300;400;500;700;900&family=Playfair+Display:wght@700;900&display=swap" rel="stylesheet">
<style>
:root {
  --blue: #0284c7;
  --blue-dark: #0369a1;
  --blue-light: #e0f2fe;
  --teal: #0d9488;
  --teal-dark: #0f766e;
  --ink: #1a1a1a;
  --text: #333333;
  --mid: #555555;
  --gray: #888888;
  --light: #f8fafc;
  --light2: #e2e8f0;
  --white: #ffffff;
  --border: #e2e8f0;
  --accent: #f59e0b;
}

* { margin: 0; padding: 0; box-sizing: border-box; }
html { scroll-behavior: smooth; }
body {
  background: var(--white);
  color: var(--text);
  font-family: 'Noto Sans JP', sans-serif;
  font-weight: 400;
  line-height: 1.85;
  overflow-x: hidden;
}

/* ===== NAV ===== */
#mainNav {
  position: fixed; top: 0; left: 0; right: 0; z-index: 1000;
  display: flex; align-items: center; justify-content: space-between;
  padding: 0 60px; height: 68px;
  background: rgba(255,255,255,0.97);
  border-bottom: 1px solid var(--border);
  backdrop-filter: blur(12px);
}
.nav-logo { display: flex; align-items: center; gap: 12px; text-decoration: none; }
.nav-logo-text { font-size: 16px; font-weight: 700; color: var(--ink); }
.nav-logo-text span { color: var(--blue); }
.nav-links { display: flex; align-items: center; gap: 36px; list-style: none; }
.nav-links a { font-size: 13px; font-weight: 500; color: var(--mid); text-decoration: none; transition: color 0.2s; }
.nav-links a:hover { color: var(--blue); }
.nav-cta {
  background: var(--blue) !important; color: var(--white) !important;
  padding: 10px 22px !important; border-radius: 4px;
  font-size: 12px !important; font-weight: 700 !important;
}
.nav-cta:hover { background: var(--blue-dark) !important; }
.nav-hamburger {
  display: none; flex-direction: column; justify-content: center; gap: 5px;
  width: 40px; height: 40px; background: none; border: none; cursor: pointer;
}
.nav-hamburger span { display: block; width: 24px; height: 2px; background: var(--ink); transition: all 0.3s; }
.nav-hamburger.open span:nth-child(1) { transform: translateY(7px) rotate(45deg); }
.nav-hamburger.open span:nth-child(2) { opacity: 0; }
.nav-hamburger.open span:nth-child(3) { transform: translateY(-7px) rotate(-45deg); }
.nav-drawer {
  display: none; position: fixed; top: 68px; left: 0; right: 0;
  background: rgba(255,255,255,0.98); border-bottom: 2px solid var(--blue);
  padding: 24px 32px 32px; z-index: 999;
}
.nav-drawer.open { display: block; }
.nav-drawer ul { list-style: none; display: flex; flex-direction: column; }
.nav-drawer ul li a {
  display: block; padding: 14px 0; font-size: 15px; font-weight: 500;
  color: var(--ink); text-decoration: none; border-bottom: 1px solid var(--border);
}
.nav-drawer ul li:last-child a {
  margin-top: 20px; text-align: center;
  background: var(--blue); color: var(--white) !important;
  padding: 14px; border-radius: 4px; border-bottom: none; font-weight: 700 !important;
}
@media (max-width: 1024px) {
  #mainNav { padding: 0 32px; }
  .nav-links { display: none; }
  .nav-hamburger { display: flex; }
}
@media (max-width: 480px) {
  #mainNav { padding: 0 20px; height: 60px; }
  .nav-drawer { top: 60px; }
}

/* ===== HERO ===== */
.hero {
  min-height: 100vh;
  display: flex; align-items: center;
  padding: 120px 60px 80px;
  background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 60%, #f0fdf4 100%);
  position: relative; overflow: hidden;
}
.hero::before {
  content: '';
  position: absolute; inset: 0;
  background-image: linear-gradient(rgba(2,132,199,0.06) 1px, transparent 1px),
    linear-gradient(90deg, rgba(2,132,199,0.06) 1px, transparent 1px);
  background-size: 50px 50px;
  mask-image: radial-gradient(ellipse 80% 80% at 60% 40%, black 0%, transparent 75%);
}
.hero-inner {
  max-width: 1100px; margin: 0 auto; width: 100%; position: relative; z-index: 1;
  display: grid; grid-template-columns: 1fr 1fr; gap: 60px; align-items: center;
}
.hero-text { }
.hero-img { border-radius: 16px; overflow: hidden; box-shadow: 0 24px 60px rgba(0,0,0,0.15); }
.hero-img img { width: 100%; height: 420px; object-fit: cover; display: block; }
@media (max-width: 900px) {
  .hero-inner { grid-template-columns: 1fr; }
  .hero-img { display: none; }
}
.hero-badge {
  display: inline-flex; align-items: center; gap: 8px;
  background: var(--blue-light); color: var(--blue-dark);
  padding: 6px 16px; border-radius: 100px;
  font-size: 12px; font-weight: 700; letter-spacing: 0.08em;
  margin-bottom: 24px;
}
.hero-badge::before { content: '◆'; font-size: 8px; }
.hero h1 {
  font-size: clamp(28px, 5vw, 52px);
  font-weight: 900; line-height: 1.2;
  color: var(--ink); margin-bottom: 12px;
  letter-spacing: -0.02em;
}
.hero h1 em { font-style: normal; color: var(--blue); }
.hero-sub {
  font-size: clamp(15px, 2vw, 18px);
  color: var(--mid); margin-bottom: 16px; font-weight: 400;
}
.hero-desc {
  font-size: 14px; color: var(--gray);
  max-width: 560px; margin-bottom: 40px; line-height: 1.8;
}
.hero-btns { display: flex; gap: 16px; flex-wrap: wrap; }
.btn-primary {
  background: var(--blue); color: var(--white);
  padding: 16px 36px; border-radius: 4px;
  font-size: 15px; font-weight: 700; text-decoration: none;
  transition: background 0.2s, transform 0.2s;
  display: inline-block;
}
.btn-primary:hover { background: var(--blue-dark); transform: translateY(-2px); }
.btn-secondary {
  background: var(--white); color: var(--blue);
  padding: 16px 36px; border-radius: 4px;
  font-size: 15px; font-weight: 700; text-decoration: none;
  border: 2px solid var(--blue);
  transition: all 0.2s; display: inline-block;
}
.btn-secondary:hover { background: var(--blue); color: var(--white); }
.hero-stats {
  display: flex; gap: 40px; margin-top: 56px;
  padding-top: 40px; border-top: 1px solid var(--border);
  flex-wrap: wrap;
}
.stat-item { display: flex; flex-direction: column; gap: 4px; }
.stat-num { font-size: 32px; font-weight: 900; color: var(--blue); line-height: 1; }
.stat-label { font-size: 12px; color: var(--gray); letter-spacing: 0.04em; }

@media (max-width: 768px) {
  .hero { padding: 100px 24px 60px; }
  .hero-stats { gap: 24px; }
  .stat-num { font-size: 24px; }
}

/* ===== SECTION COMMON ===== */
section { padding: 96px 60px; }
.section-inner { max-width: 1100px; margin: 0 auto; }
.section-label {
  font-size: 11px; font-weight: 700; letter-spacing: 0.12em;
  color: var(--blue); text-transform: uppercase; margin-bottom: 12px;
}
.section-title {
  font-size: clamp(22px, 4vw, 38px); font-weight: 900;
  color: var(--ink); line-height: 1.3; margin-bottom: 16px;
}
.section-desc { font-size: 15px; color: var(--mid); max-width: 600px; line-height: 1.8; }
@media (max-width: 768px) {
  section { padding: 64px 24px; }
}

/* ===== TROUBLE ===== */
.trouble { background: var(--ink); }
.trouble .section-label { color: var(--accent); }
.trouble .section-title { color: var(--white); }
.trouble .section-desc { color: #aaa; }
.trouble-grid {
  display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 20px; margin-top: 48px;
}
.trouble-card {
  background: rgba(255,255,255,0.05);
  border: 1px solid rgba(255,255,255,0.1);
  border-radius: 8px; padding: 28px;
  display: flex; gap: 16px; align-items: flex-start;
}
.trouble-icon {
  width: 40px; height: 40px; min-width: 40px;
  background: rgba(245,158,11,0.15); border-radius: 8px;
  display: flex; align-items: center; justify-content: center;
  font-size: 18px;
}
.trouble-card h3 { font-size: 14px; font-weight: 700; color: var(--white); margin-bottom: 6px; }
.trouble-card p { font-size: 13px; color: #888; line-height: 1.7; }

/* ===== 360 EVALUATION ===== */
.evaluation { background: var(--light); }
.eval-intro {
  display: grid; grid-template-columns: 1fr 1fr; gap: 60px;
  align-items: center; margin-bottom: 72px;
}
.eval-visual {
  position: relative; display: flex; align-items: center; justify-content: center;
}
.eval-circle {
  width: 220px; height: 220px; border-radius: 50%;
  background: linear-gradient(135deg, var(--blue), var(--teal));
  display: flex; flex-direction: column; align-items: center; justify-content: center;
  color: var(--white); text-align: center; box-shadow: 0 20px 60px rgba(2,132,199,0.3);
}
.eval-circle-num { font-size: 52px; font-weight: 900; line-height: 1; }
.eval-circle-text { font-size: 13px; font-weight: 500; margin-top: 4px; }
.eval-circle-sub { font-size: 11px; opacity: 0.8; margin-top: 4px; }
.eval-grid {
  display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-top: 48px;
}
.eval-card {
  background: var(--white); border-radius: 12px; padding: 32px;
  box-shadow: 0 4px 24px rgba(0,0,0,0.06);
  border-top: 4px solid var(--blue);
  transition: transform 0.2s, box-shadow 0.2s;
}
.eval-card:hover { transform: translateY(-4px); box-shadow: 0 12px 40px rgba(0,0,0,0.12); }
.eval-card:nth-child(2) { border-top-color: var(--teal); }
.eval-card:nth-child(3) { border-top-color: var(--accent); }
.eval-card:nth-child(4) { border-top-color: #8b5cf6; }
.eval-num {
  font-size: 11px; font-weight: 700; letter-spacing: 0.1em;
  color: var(--blue); margin-bottom: 8px;
}
.eval-card:nth-child(2) .eval-num { color: var(--teal); }
.eval-card:nth-child(3) .eval-num { color: var(--accent); }
.eval-card:nth-child(4) .eval-num { color: #8b5cf6; }
.eval-card h3 { font-size: 18px; font-weight: 900; color: var(--ink); margin-bottom: 12px; }
.eval-card p { font-size: 13px; color: var(--mid); line-height: 1.8; }
.eval-tag {
  display: inline-block; margin-top: 12px;
  background: var(--blue-light); color: var(--blue-dark);
  padding: 3px 10px; border-radius: 100px; font-size: 11px; font-weight: 700;
}
.eval-card:nth-child(2) .eval-tag { background: #ccfbf1; color: var(--teal-dark); }
.eval-card:nth-child(3) .eval-tag { background: #fef3c7; color: #92400e; }
.eval-card:nth-child(4) .eval-tag { background: #ede9fe; color: #5b21b6; }

@media (max-width: 1024px) {
  .eval-intro { grid-template-columns: 1fr; gap: 40px; }
  .eval-visual { display: none; }
}
@media (max-width: 600px) {
  .eval-grid { grid-template-columns: 1fr; }
}

/* ===== AI SECTION ===== */
.ai-section { background: linear-gradient(135deg, #0c4a6e 0%, #164e63 100%); }
.ai-section .section-label { color: #67e8f9; }
.ai-section .section-title { color: var(--white); }
.ai-section .section-desc { color: #94a3b8; }
.ai-features {
  display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
  gap: 24px; margin-top: 48px;
}
.ai-card {
  background: rgba(255,255,255,0.08);
  border: 1px solid rgba(255,255,255,0.12);
  border-radius: 10px; padding: 28px;
}
.ai-icon { font-size: 28px; margin-bottom: 12px; }
.ai-card h3 { font-size: 15px; font-weight: 700; color: var(--white); margin-bottom: 8px; }
.ai-card p { font-size: 13px; color: #94a3b8; line-height: 1.7; }

/* ===== INDUSTRY ===== */
.industry { background: var(--white); }
.industry-grid {
  display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 20px; margin-top: 48px;
}
.industry-card {
  background: var(--light); border-radius: 10px; padding: 28px 20px;
  text-align: center; border: 1px solid var(--border);
  transition: border-color 0.2s, box-shadow 0.2s;
}
.industry-card:hover { border-color: var(--blue); box-shadow: 0 4px 20px rgba(2,132,199,0.12); }
.industry-emoji { font-size: 36px; margin-bottom: 12px; }
.industry-card h3 { font-size: 15px; font-weight: 700; color: var(--ink); margin-bottom: 6px; }
.industry-card p { font-size: 12px; color: var(--gray); line-height: 1.6; }
.industry-hot {
  display: inline-block; margin-top: 8px;
  background: #fef2f2; color: #dc2626;
  padding: 2px 8px; border-radius: 100px; font-size: 10px; font-weight: 700;
}

/* ===== FLOW ===== */
.flow { background: var(--light); }
.flow-steps {
  display: flex; flex-direction: column; gap: 0; margin-top: 48px;
  max-width: 700px; margin-left: auto; margin-right: auto;
}
.flow-step {
  display: flex; gap: 24px; align-items: flex-start;
  padding: 32px 0; border-bottom: 1px solid var(--border);
  position: relative;
}
.flow-step:last-child { border-bottom: none; }
.flow-num {
  width: 48px; height: 48px; min-width: 48px;
  background: var(--blue); border-radius: 50%;
  display: flex; align-items: center; justify-content: center;
  color: var(--white); font-size: 18px; font-weight: 900;
}
.flow-content h3 { font-size: 16px; font-weight: 700; color: var(--ink); margin-bottom: 6px; }
.flow-content p { font-size: 13px; color: var(--mid); line-height: 1.7; }
.flow-free {
  display: inline-block; margin-left: 8px;
  background: #fef3c7; color: #92400e;
  padding: 1px 8px; border-radius: 100px; font-size: 11px; font-weight: 700;
}

/* ===== CONTACT ===== */
.contact { background: var(--blue); }
.contact .section-title { color: var(--white); text-align: center; }
.contact .section-desc { color: rgba(255,255,255,0.8); text-align: center; margin: 0 auto 48px; }
.contact-box {
  max-width: 600px; margin: 0 auto;
  background: var(--white); border-radius: 12px; padding: 48px;
}
.form-group { margin-bottom: 20px; }
.form-group label {
  display: block; font-size: 13px; font-weight: 700; color: var(--ink);
  margin-bottom: 8px;
}
.form-group label span { color: #dc2626; font-size: 11px; margin-left: 4px; }
.form-group input,
.form-group select,
.form-group textarea {
  width: 100%; padding: 12px 16px;
  border: 1px solid var(--border); border-radius: 6px;
  font-size: 14px; font-family: 'Noto Sans JP', sans-serif;
  color: var(--ink); background: var(--white);
  transition: border-color 0.2s;
}
.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
  outline: none; border-color: var(--blue);
  box-shadow: 0 0 0 3px rgba(2,132,199,0.12);
}
.form-group textarea { height: 120px; resize: vertical; }
.form-submit {
  width: 100%; padding: 16px;
  background: var(--blue); color: var(--white);
  border: none; border-radius: 6px;
  font-size: 16px; font-weight: 700; cursor: pointer;
  font-family: 'Noto Sans JP', sans-serif;
  transition: background 0.2s;
}
.form-submit:hover { background: var(--blue-dark); }
.contact-info {
  text-align: center; margin-top: 32px;
  color: rgba(255,255,255,0.8); font-size: 14px;
}
.contact-info a { color: var(--white); font-weight: 700; }

@media (max-width: 600px) {
  .contact-box { padding: 32px 24px; }
}

/* ===== FOOTER ===== */
footer {
  background: var(--ink); color: #666; padding: 40px 60px;
  display: flex; justify-content: space-between; align-items: center;
  flex-wrap: wrap; gap: 16px;
}
.footer-logo { font-size: 16px; font-weight: 700; color: var(--white); }
.footer-links { display: flex; gap: 24px; }
.footer-links a { font-size: 12px; color: #666; text-decoration: none; }
.footer-links a:hover { color: var(--white); }
.footer-copy { font-size: 12px; width: 100%; text-align: center; margin-top: 8px; }
@media (max-width: 600px) {
  footer { padding: 32px 24px; flex-direction: column; text-align: center; }
  .footer-links { flex-wrap: wrap; justify-content: center; }
}

/* ===== ENTERPRISE ===== */
.enterprise { background: var(--light); border-top: 3px solid var(--blue); }
.enterprise-inner {
  display: grid; grid-template-columns: 1fr 1fr; gap: 60px; align-items: center;
}
.enterprise-badge {
  display: inline-flex; align-items: center; gap: 8px;
  background: #dbeafe; color: var(--blue-dark);
  padding: 5px 14px; border-radius: 100px;
  font-size: 11px; font-weight: 700; letter-spacing: 0.08em;
  margin-bottom: 16px;
}
.enterprise h3 { font-size: 22px; font-weight: 900; color: var(--ink); margin-bottom: 12px; line-height: 1.4; }
.enterprise p { font-size: 14px; color: var(--mid); line-height: 1.85; margin-bottom: 20px; }
.enterprise-list { list-style: none; display: flex; flex-direction: column; gap: 10px; margin-bottom: 24px; }
.enterprise-list li {
  display: flex; gap: 10px; align-items: flex-start;
  font-size: 13px; color: var(--mid);
}
.enterprise-list li::before { content: '✓'; color: var(--blue); font-weight: 900; min-width: 16px; }
.enterprise-cards { display: flex; flex-direction: column; gap: 16px; }
.ent-card {
  background: var(--white); border-radius: 10px; padding: 20px 24px;
  border-left: 4px solid var(--blue); box-shadow: 0 2px 12px rgba(0,0,0,0.06);
}
.ent-card:nth-child(2) { border-left-color: var(--teal); }
.ent-card:nth-child(3) { border-left-color: #8b5cf6; }
.ent-card h4 { font-size: 14px; font-weight: 700; color: var(--ink); margin-bottom: 4px; }
.ent-card p { font-size: 12px; color: var(--gray); line-height: 1.6; }
@media (max-width: 900px) {
  .enterprise-inner { grid-template-columns: 1fr; gap: 32px; }
}

/* ===== CF7 FORM ===== */
.cf7-wrap { max-width: 600px; margin: 0 auto; background: var(--white); border-radius: 12px; padding: 48px; }
.cf7-wrap .wpcf7-form p { margin-bottom: 16px; }
.cf7-wrap .wpcf7-form label { font-size: 13px; font-weight: 700; color: var(--ink); display: block; margin-bottom: 6px; }
.cf7-wrap .wpcf7-form input[type="text"],
.cf7-wrap .wpcf7-form input[type="email"],
.cf7-wrap .wpcf7-form select,
.cf7-wrap .wpcf7-form textarea {
  width: 100%; padding: 12px 16px; border: 1px solid var(--border); border-radius: 6px;
  font-size: 14px; font-family: 'Noto Sans JP', sans-serif; color: var(--ink);
}
.cf7-wrap .wpcf7-form input[type="submit"] {
  width: 100%; padding: 16px; background: var(--blue); color: var(--white);
  border: none; border-radius: 6px; font-size: 16px; font-weight: 700;
  cursor: pointer; font-family: 'Noto Sans JP', sans-serif;
}
.cf7-wrap .wpcf7-form input[type="submit"]:hover { background: var(--blue-dark); }

/* ===== FADE IN ===== */
.fade-in { opacity: 0; transform: translateY(24px); transition: opacity 0.6s, transform 0.6s; }
.fade-in.visible { opacity: 1; transform: none; }
</style>
</head>
<body>

<!-- NAV -->
<nav id="mainNav">
  <a href="https://aidealize.com" class="nav-logo">
    <span class="nav-logo-text">AI<span>idealize</span></span>
  </a>
  <ul class="nav-links">
    <li><a href="#trouble">課題</a></li>
    <li><a href="#evaluation">360度評価</a></li>
    <li><a href="#ai">AI活用</a></li>
    <li><a href="#flow">導入の流れ</a></li>
    <li><a href="#contact" class="nav-cta">無料診断を受ける</a></li>
  </ul>
  <button class="nav-hamburger" id="hamburger" aria-label="メニュー">
    <span></span><span></span><span></span>
  </button>
</nav>
<div class="nav-drawer" id="drawer">
  <ul>
    <li><a href="#trouble" onclick="closeDrawer()">課題</a></li>
    <li><a href="#evaluation" onclick="closeDrawer()">360度評価</a></li>
    <li><a href="#ai" onclick="closeDrawer()">AI活用</a></li>
    <li><a href="#flow" onclick="closeDrawer()">導入の流れ</a></li>
    <li><a href="#contact" onclick="closeDrawer()">無料診断を受ける</a></li>
  </ul>
</div>

<!-- HERO -->
<section class="hero" id="top">
  <div class="hero-inner">
  <div class="hero-text">
    <div class="hero-badge">AI × 360度評価</div>
    <h1>人材が定着する組織を<br><em>科学的に</em>つくる</h1>
    <p class="hero-sub">目標・自己他者・顧客・実績の4軸評価で、<br>離職の根本原因を可視化・解決します</p>
    <p class="hero-desc">
      従業員の「本音」を数値化し、公平な評価制度を設計。<br>
      介護・看護・中小企業の慢性的な人材課題をAIとコンサルティングで解決します。
    </p>
    <div class="hero-btns">
      <a href="#contact" class="btn-primary">無料診断を受ける</a>
      <a href="#evaluation" class="btn-secondary">サービス詳細を見る</a>
    </div>
    <div class="hero-stats">
      <div class="stat-item">
        <span class="stat-num">4軸</span>
        <span class="stat-label">360度評価システム</span>
      </div>
      <div class="stat-item">
        <span class="stat-num">AI</span>
        <span class="stat-label">データ分析・改善提案</span>
      </div>
      <div class="stat-item">
        <span class="stat-num">介護<br>看護</span>
        <span class="stat-label">業界特化サポート</span>
      </div>
    </div>
  </div><!-- /.hero-text -->
  <div class="hero-img">
    <img src="https://aidealize.com/wp-content/uploads/2026/03/mainvisual-scaled.jpg"
         alt="人材定着サービス - チームで働く人々" loading="lazy">
  </div>
  </div><!-- /.hero-inner -->
</section>

<!-- TROUBLE -->
<section class="trouble" id="trouble">
  <div class="section-inner">
    <p class="section-label">Trouble</p>
    <h2 class="section-title">こんな悩みはありませんか？</h2>
    <p class="section-desc">人材の問題は放置するほど採用・研修コストが膨らみ、組織が疲弊していきます。</p>
    <div class="trouble-grid fade-in">
      <div class="trouble-card">
        <div class="trouble-icon">😰</div>
        <div>
          <h3>離職率が高く、人が定着しない</h3>
          <p>採用してもすぐ辞める。理由がわからないまま繰り返す負のサイクル。</p>
        </div>
      </div>
      <div class="trouble-card">
        <div class="trouble-icon">💸</div>
        <div>
          <h3>採用・研修コストが膨大</h3>
          <p>1名採用するたびに数十万〜数百万円のコストが発生。利益を圧迫している。</p>
        </div>
      </div>
      <div class="trouble-card">
        <div class="trouble-icon">🙈</div>
        <div>
          <h3>従業員の本音がわからない</h3>
          <p>アンケートを取っても表面的な回答しか返ってこない。不満の本質が見えない。</p>
        </div>
      </div>
      <div class="trouble-card">
        <div class="trouble-icon">⚖️</div>
        <div>
          <h3>評価制度が不公平・曖昧</h3>
          <p>「頑張っても評価されない」という不満が蓄積。モチベーション低下を招いている。</p>
        </div>
      </div>
      <div class="trouble-card">
        <div class="trouble-icon">🏥</div>
        <div>
          <h3>介護・看護現場の人手不足</h3>
          <p>慢性的な人手不足でスタッフへの負担が増大。疲弊のスパイラルから抜け出せない。</p>
        </div>
      </div>
      <div class="trouble-card">
        <div class="trouble-icon">📉</div>
        <div>
          <h3>頑張った人が報われない</h3>
          <p>売上・利益に貢献しても、評価制度がなければ優秀な人材から辞めていく。</p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- 360 EVALUATION -->
<section class="evaluation" id="evaluation">
  <div class="section-inner">
    <p class="section-label">360° Evaluation</p>
    <h2 class="section-title">4軸の360度評価で<br>人材課題を根本解決</h2>
    <p class="section-desc">
      一方向の上司評価では見えない「本当の実力と課題」を、4つの視点から多角的に評価します。<br>
      公平・透明な評価が、従業員の納得感と定着率を高めます。
    </p>

    <div class="eval-intro fade-in">
      <div>
        <p style="font-size:15px; color:var(--mid); line-height:1.9; margin-bottom:24px;">
          上司だけが評価する従来型の人事評価は「えこひいき」「不透明」という不満を生みやすく、
          優秀な人材の離職原因になります。<br><br>
          AIidealize の360度評価は、<strong>目標管理・自己他者評価・顧客評価・実績評価</strong>の
          4軸を組み合わせることで、誰もが納得できる評価制度を実現します。
        </p>
        <a href="#contact" class="btn-primary" style="font-size:14px; padding:12px 28px;">詳細を相談する</a>
      </div>
      <div class="eval-visual">
        <div class="eval-circle">
          <div class="eval-circle-num">360°</div>
          <div class="eval-circle-text">多角的評価</div>
          <div class="eval-circle-sub">4つの視点から分析</div>
        </div>
      </div>
    </div>

    <div class="eval-grid fade-in">
      <div class="eval-card">
        <div class="eval-num">EVALUATION 01</div>
        <h3>🎯 目標管理評価</h3>
        <p>
          個人・チームの目標を設定・管理し、達成度を可視化。
          「何を目指すのか」を明確にすることで、行動が変わります。
          上司との定期1on1でフィードバックも充実。
        </p>
        <span class="eval-tag">MBO / OKR対応</span>
      </div>
      <div class="eval-card">
        <div class="eval-num">EVALUATION 02</div>
        <h3>🤝 自己他者評価</h3>
        <p>
          自己評価だけでなく、同僚・部下・上司からの多方向評価を実施。
          「自分がどう見られているか」を客観的に知ることで、
          チームワークと利他的な行動が育まれます。
        </p>
        <span class="eval-tag">ピア評価・360度フィードバック</span>
      </div>
      <div class="eval-card">
        <div class="eval-num">EVALUATION 03</div>
        <h3>💬 顧客評価</h3>
        <p>
          お客様・利用者からの口コミ・満足度評価を人事評価に反映。
          「外部からの評価」を組み込むことで、サービス品質向上と
          顧客満足度が同時に高まります。介護・看護業界に特に有効。
        </p>
        <span class="eval-tag">CS連動評価</span>
      </div>
      <div class="eval-card">
        <div class="eval-num">EVALUATION 04</div>
        <h3>📊 実績評価</h3>
        <p>
          売上・利益などの数値成果を出した方をプラス評価。
          頑張りが数字に出る職種では、成果に見合った正当な評価で
          モチベーションと定着率を高めます。
        </p>
        <span class="eval-tag">KPI連動・成果報酬</span>
      </div>
    </div>
  </div>
</section>

<!-- AI SECTION -->
<section class="ai-section" id="ai">
  <div class="section-inner">
    <p class="section-label">AI Integration</p>
    <h2 class="section-title">AIが評価データを分析し<br>改善アクションを提案</h2>
    <p class="section-desc">
      蓄積した評価データをAIが解析。離職リスクの高い従業員を早期発見し、
      具体的な改善アクションを自動提案します。
    </p>
    <div class="ai-features fade-in">
      <div class="ai-card">
        <div class="ai-icon">🔍</div>
        <h3>離職リスク予測</h3>
        <p>評価データのパターンからAIが離職リスクの高いメンバーを早期検出。手遅れになる前に対策できます。</p>
      </div>
      <div class="ai-card">
        <div class="ai-icon">📈</div>
        <h3>評価レポート自動生成</h3>
        <p>膨大な評価データを自動集計・分析。マネージャーの負担を大幅に削減し、質の高いフィードバックを実現。</p>
      </div>
      <div class="ai-card">
        <div class="ai-icon">💡</div>
        <h3>改善アクション提案</h3>
        <p>「このチームはコミュニケーション強化が必要」など、データに基づいた具体的な改善策をAIが提案。</p>
      </div>
      <div class="ai-card">
        <div class="ai-icon">🗣️</div>
        <h3>AIヒアリング支援</h3>
        <p>テキスト・音声での従業員ヒアリングをAIがサポート。本音を引き出しやすい環境を整えます。</p>
      </div>
    </div>
  </div>
</section>

<!-- INDUSTRY -->
<section class="industry" id="industry">
  <div class="section-inner">
    <p class="section-label">Industry</p>
    <h2 class="section-title">対応業界</h2>
    <p class="section-desc">人材定着の課題は業界を問いません。特に離職率が高い業界に特化したサポートを提供します。</p>
    <div class="industry-grid fade-in">
      <div class="industry-card">
        <div class="industry-emoji">🏥</div>
        <h3>介護・福祉</h3>
        <p>離職率が高く慢性的な人手不足。ケアの質と従業員定着を両立。</p>
        <span class="industry-hot">特に高需要</span>
      </div>
      <div class="industry-card">
        <div class="industry-emoji">👨‍⚕️</div>
        <h3>看護・医療</h3>
        <p>夜勤・重労働による疲弊を評価制度改善でサポート。</p>
        <span class="industry-hot">特に高需要</span>
      </div>
      <div class="industry-card">
        <div class="industry-emoji">🏭</div>
        <h3>製造・物流</h3>
        <p>現場スタッフの定着率改善と生産性向上を同時に実現。</p>
      </div>
      <div class="industry-card">
        <div class="industry-emoji">🏪</div>
        <h3>小売・飲食</h3>
        <p>アルバイト・パートの定着率向上で採用コストを削減。</p>
      </div>
      <div class="industry-card">
        <div class="industry-emoji">🏢</div>
        <h3>中小企業全般</h3>
        <p>人事部がなくても導入できる、シンプルな評価制度設計。</p>
      </div>
    </div>
  </div>
</section>

<!-- FLOW -->
<section class="flow" id="flow">
  <div class="section-inner">
    <p class="section-label">Flow</p>
    <h2 class="section-title">導入の流れ</h2>
    <p class="section-desc">無料診断から始めて、最短1ヶ月で評価制度の運用をスタートできます。</p>
    <div class="flow-steps fade-in">
      <div class="flow-step">
        <div class="flow-num">1</div>
        <div class="flow-content">
          <h3>無料診断・ヒアリング <span class="flow-free">無料</span></h3>
          <p>現状の課題・離職状況・評価制度の有無をヒアリング。最適なプランをご提案します。オンラインでも対応可能。</p>
        </div>
      </div>
      <div class="flow-step">
        <div class="flow-num">2</div>
        <div class="flow-content">
          <h3>評価制度の設計</h3>
          <p>4軸評価のうち、貴社に必要な評価項目をカスタマイズ設計。業界・規模・職種に合わせた制度を構築します。</p>
        </div>
      </div>
      <div class="flow-step">
        <div class="flow-num">3</div>
        <div class="flow-content">
          <h3>システム導入・説明会</h3>
          <p>評価システムを導入し、管理者・従業員向けに使い方を説明。スムーズな運用開始をサポートします。</p>
        </div>
      </div>
      <div class="flow-step">
        <div class="flow-num">4</div>
        <div class="flow-content">
          <h3>運用・改善サポート</h3>
          <p>定期的なデータレビューと改善提案を継続実施。評価結果をもとに、組織の問題を継続的に解決します。</p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ENTERPRISE -->
<section class="enterprise">
  <div class="section-inner">
    <div class="enterprise-inner fade-in">
      <div>
        <span class="enterprise-badge">Enterprise</span>
        <h3>大企業・M365環境向け<br>AIマネジメント支援</h3>
        <p>
          M365・Teamsをすでに導入している中大企業向けに、
          Power BIダッシュボードやAI 1on1分析など、
          より高度なデータ活用型マネジメント支援も提供しています。
        </p>
        <ul class="enterprise-list">
          <li>勤怠・M365データを活用した管理職マネジメントダッシュボード</li>
          <li>AI音声分析による1on1フィードバック支援（Copilot非依存）</li>
          <li>Power BI × 人事データの離職リスク可視化</li>
          <li>Microsoft Entra ID / SharePoint 権限・データ設計</li>
        </ul>
        <a href="#contact" class="btn-primary" style="font-size:14px; padding:12px 28px;">エンタープライズ案件を相談する</a>
      </div>
      <div class="enterprise-cards">
        <div class="ent-card">
          <h4>AI 1on1 分析</h4>
          <p>音声文字起こし＋AI分析で、1on1の質を可視化・改善提案。Copilot非依存で柔軟に対応。</p>
        </div>
        <div class="ent-card">
          <h4>Power BI マネジメントダッシュボード</h4>
          <p>勤怠・M365・人事データを統合し、管理職が自組織を即把握できるダッシュボードを構築。</p>
        </div>
        <div class="ent-card">
          <h4>離職リスクスコアリング</h4>
          <p>評価・勤怠・コミュニケーションデータをAIが分析し、離職リスクの高いメンバーを早期検知。</p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- CONTACT -->
<section class="contact" id="contact">
  <div class="section-inner">
    <h2 class="section-title">まず無料診断を受ける</h2>
    <p class="section-desc">現状の課題をお聞かせください。最適なプランをご提案します。</p>
    <div class="contact-box fade-in">
      <form action="https://aidealize.com/jinzai-thanks/" method="post">
        <div class="form-group">
          <label>事業所名・会社名 <span>必須</span></label>
          <input type="text" name="company" placeholder="株式会社〇〇 / 〇〇介護施設" required>
        </div>
        <div class="form-group">
          <label>お名前 <span>必須</span></label>
          <input type="text" name="name" placeholder="山田 太郎" required>
        </div>
        <div class="form-group">
          <label>メールアドレス <span>必須</span></label>
          <input type="email" name="email" placeholder="info@example.com" required>
        </div>
        <div class="form-group">
          <label>業種</label>
          <select name="industry">
            <option value="">選択してください</option>
            <option>介護・福祉</option>
            <option>看護・医療</option>
            <option>製造・物流</option>
            <option>小売・飲食</option>
            <option>中小企業（その他）</option>
          </select>
        </div>
        <div class="form-group">
          <label>従業員数</label>
          <select name="employees">
            <option value="">選択してください</option>
            <option>1〜10名</option>
            <option>11〜30名</option>
            <option>31〜100名</option>
            <option>101名以上</option>
          </select>
        </div>
        <div class="form-group">
          <label>お困りの内容・ご質問</label>
          <textarea name="message" placeholder="離職率が高く困っている、評価制度を整備したい、など"></textarea>
        </div>
        <button type="submit" class="form-submit">無料診断を申し込む →</button>
      </form>
    </div>
    <div class="contact-info">
      <p>お電話でのお問い合わせ：<a href="tel:0368703486">03-6870-3486</a>（平日 10:00〜18:00）</p>
      <p style="margin-top:8px; font-size:12px; opacity:0.7;">東京都中央区日本橋3-4-15 AIidealize株式会社</p>
    </div>
  </div>
</section>

<!-- FOOTER -->
<footer>
  <div class="footer-logo">AIidealize</div>
  <div class="footer-links">
    <a href="https://aidealize.com">ホーム</a>
    <a href="https://aidealize.com/service/">サービス一覧</a>
    <a href="https://aidealize.com/company/">会社概要</a>
    <a href="https://aidealize.com/lp/hanamaruai/">はなまるAI</a>
  </div>
  <div class="footer-copy">© 2025 AIidealize株式会社 All Rights Reserved.</div>
</footer>

<script>
// ハンバーガーメニュー
const hamburger = document.getElementById('hamburger');
const drawer = document.getElementById('drawer');
hamburger.addEventListener('click', () => {
  hamburger.classList.toggle('open');
  drawer.classList.toggle('open');
});
function closeDrawer() {
  hamburger.classList.remove('open');
  drawer.classList.remove('open');
}

// スクロールNav
window.addEventListener('scroll', () => {
  document.getElementById('mainNav').classList.toggle('scrolled', window.scrollY > 20);
});

// Fade in
const observer = new IntersectionObserver((entries) => {
  entries.forEach(e => { if (e.isIntersecting) e.target.classList.add('visible'); });
}, { threshold: 0.1 });
document.querySelectorAll('.fade-in').forEach(el => observer.observe(el));
</script>
</body>
</html>

<?php
session_start();
require_once __DIR__ . '/../config/products.php';
use function Vault\cartCount;
$cartCount = cartCount();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>VAULT — About</title>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300;1,400&family=DM+Mono:wght@300;400;500&display=swap" rel="stylesheet">
<style>
  :root {
    --ink: #0d0d0d;
    --paper: #f5f0e8;
    --cream: #ede8dc;
    --gold: #c9a84c;
    --muted: #8a8070;
    --border: rgba(13,13,13,0.12);
  }

  * { margin: 0; padding: 0; box-sizing: border-box; }

  body {
    background: var(--paper);
    color: var(--ink);
    font-family: 'DM Mono', monospace;
    font-size: 14px;
    min-height: 100vh;
  }

  /* ─── NOISE TEXTURE ─── */
  body::before {
    content: '';
    position: fixed; inset: 0;
    background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)' opacity='0.04'/%3E%3C/svg%3E");
    pointer-events: none; z-index: 0; opacity: 0.6;
  }

  /* ─── HEADER ─── */
  header {
    position: sticky; top: 0; z-index: 100;
    background: var(--ink); color: var(--paper);
    display: flex; align-items: center; justify-content: space-between;
    padding: 0 40px; height: 64px;
    border-bottom: 1px solid rgba(255,255,255,0.08);
  }

  .logo {
    font-family: 'Cormorant Garamond', serif;
    font-size: 28px; font-weight: 300;
    letter-spacing: 0.3em; color: var(--gold);
    text-decoration: none;
  }
  .logo span { font-style: italic; color: var(--paper); }

  nav { display: flex; gap: 32px; align-items: center; }
  nav a {
    color: rgba(245,240,232,0.55);
    text-decoration: none; letter-spacing: 0.12em;
    font-size: 13px; text-transform: uppercase; transition: color 0.2s;
  }
  nav a:hover, nav a.active { color: var(--gold); }

  .cart-btn {
    display: flex; align-items: center; gap: 10px;
    background: none; border: 1px solid rgba(255,255,255,0.2);
    color: var(--paper); padding: 8px 20px;
    font-family: 'DM Mono', monospace;
    font-size: 13px; letter-spacing: 0.12em; text-transform: uppercase;
    cursor: pointer; text-decoration: none; transition: all 0.2s;
  }
  .cart-btn:hover { background: var(--gold); border-color: var(--gold); color: var(--ink); }

  .cart-count {
    background: var(--gold); color: var(--ink);
    width: 20px; height: 20px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 11px; font-weight: 600;
  }

  /* ─── HERO ─── */
  .hero {
    position: relative; padding: 100px 40px 80px;
    overflow: hidden; border-bottom: 1px solid var(--border);
    display: flex; flex-direction: column; align-items: center; text-align: center;
  }

  .hero-bg {
    position: absolute; inset: 0;
    background: radial-gradient(ellipse 80% 60% at 50% 50%, rgba(201,168,76,0.08) 0%, transparent 70%);
    pointer-events: none;
  }

  .hero-label {
    font-size: 11px; letter-spacing: 0.25em;
    text-transform: uppercase; color: var(--gold);
    margin-bottom: 20px;
    opacity: 0; animation: fadeUp 0.6s 0.1s forwards;
  }

  .hero-title {
    font-family: 'Cormorant Garamond', serif;
    font-size: clamp(52px, 7vw, 96px); font-weight: 300;
    line-height: 0.95; margin-bottom: 28px;
    opacity: 0; animation: fadeUp 0.6s 0.2s forwards;
  }
  .hero-title em { font-style: italic; color: var(--muted); }

  .hero-sub {
    max-width: 520px; color: var(--muted);
    line-height: 1.8; font-size: 14px;
    opacity: 0; animation: fadeUp 0.6s 0.3s forwards;
  }

  /* ─── MAIN CONTENT ─── */
  .container {
    max-width: 1100px; margin: 0 auto;
    padding: 80px 40px 100px;
    position: relative; z-index: 1;
  }

  /* ─── MANIFESTO ─── */
  .manifesto {
    display: grid; grid-template-columns: 1fr 1fr;
    gap: 80px; align-items: center;
    border-bottom: 1px solid var(--border);
    padding-bottom: 80px; margin-bottom: 80px;
  }

  .manifesto-text .eyebrow {
    font-size: 11px; letter-spacing: 0.25em;
    text-transform: uppercase; color: var(--gold);
    margin-bottom: 20px;
  }

  .manifesto-text h2 {
    font-family: 'Cormorant Garamond', serif;
    font-size: clamp(36px, 4vw, 56px); font-weight: 300;
    line-height: 1.05; margin-bottom: 28px;
  }
  .manifesto-text h2 em { font-style: italic; color: var(--muted); }

  .manifesto-text p {
    color: var(--muted); line-height: 1.9;
    font-size: 13px; margin-bottom: 16px;
  }

  .manifesto-aside {
    background: var(--cream); border: 1px solid var(--border);
    padding: 48px 40px; text-align: center;
  }

  .manifesto-aside-num {
    font-family: 'Cormorant Garamond', serif;
    font-size: 96px; font-weight: 300;
    color: var(--gold); line-height: 1;
    margin-bottom: 8px;
  }

  .manifesto-aside-label {
    font-size: 11px; letter-spacing: 0.2em;
    text-transform: uppercase; color: var(--muted);
    margin-bottom: 32px;
  }

  .manifesto-aside-divider {
    width: 1px; height: 40px; background: var(--border);
    margin: 0 auto 32px;
  }

  .manifesto-aside-quote {
    font-family: 'Cormorant Garamond', serif;
    font-size: 22px; font-weight: 300; font-style: italic;
    color: var(--ink); line-height: 1.4;
  }

  /* ─── PILLARS ─── */
  .pillars-header {
    text-align: center; margin-bottom: 48px;
  }
  .pillars-header .eyebrow {
    font-size: 11px; letter-spacing: 0.25em;
    text-transform: uppercase; color: var(--gold);
    margin-bottom: 14px;
  }
  .pillars-header h2 {
    font-family: 'Cormorant Garamond', serif;
    font-size: clamp(32px, 3.5vw, 48px); font-weight: 300;
  }

  .pillars {
    display: grid; grid-template-columns: repeat(3, 1fr);
    gap: 1px; background: var(--border);
    border: 1px solid var(--border);
    margin-bottom: 80px;
  }

  .pillar {
    background: var(--cream);
    padding: 40px 32px;
  }

  .pillar-icon {
    font-size: 32px; margin-bottom: 20px;
  }

  .pillar-title {
    font-family: 'Cormorant Garamond', serif;
    font-size: 22px; font-weight: 400;
    margin-bottom: 12px;
  }

  .pillar-desc {
    font-size: 12px; color: var(--muted);
    line-height: 1.8;
  }

  /* ─── SECURITY SECTION ─── */
  .security-section {
    background: var(--ink); color: var(--paper);
    padding: 64px 56px;
    margin-bottom: 80px;
    position: relative; overflow: hidden;
  }

  .security-section::before {
    content: '';
    position: absolute; inset: 0;
    background: radial-gradient(ellipse 60% 80% at 90% 50%, rgba(201,168,76,0.06) 0%, transparent 70%);
    pointer-events: none;
  }

  .security-grid {
    display: grid; grid-template-columns: 1fr 1fr;
    gap: 64px; align-items: center;
    position: relative; z-index: 1;
  }

  .security-text .eyebrow {
    font-size: 11px; letter-spacing: 0.25em;
    text-transform: uppercase; color: var(--gold);
    margin-bottom: 16px;
  }

  .security-text h2 {
    font-family: 'Cormorant Garamond', serif;
    font-size: clamp(32px, 3.5vw, 48px); font-weight: 300;
    line-height: 1.05; margin-bottom: 20px;
    color: var(--paper);
  }
  .security-text h2 em { font-style: italic; color: var(--gold); }

  .security-text p {
    color: rgba(245,240,232,0.5); line-height: 1.9;
    font-size: 13px; margin-bottom: 14px;
  }
  .thesis-meta {
    margin-top: 24px;
    padding-top: 20px;
    border-top: 1px solid rgba(245,240,232,0.14);
  }
  .thesis-meta-intro > div:first-of-type {
    font-size: 13px;
    letter-spacing: 0.12em;
    text-transform: uppercase;
    color: var(--gold);
    margin-bottom: 14px;
  }
  .thesis-meta-intro {
    color: rgba(245,240,232,0.65);
    font-size: 13px;
    line-height: 1.9;
    margin-bottom: 22px;
  }
  .thesis-meta-intro strong { color: var(--gold); font-weight: 500; }
  .thesis-meta-grid {
    display: flex;
    flex-wrap: wrap;
    align-items: flex-start;
    gap: 28px 48px;
  }
  .thesis-meta-col--id {
    flex: 0 0 auto;
    min-width: 7rem;
  }
  .thesis-meta-col--programme {
    flex: 1 1 200px;
    min-width: 0;
  }
  .thesis-meta-item-label {
    display: block;
    font-size: 11px;
    letter-spacing: 0.12em;
    text-transform: uppercase;
    color: rgba(245,240,232,0.45);
    margin-bottom: 6px;
  }
  .thesis-meta-item-value {
    font-size: 12px;
    color: rgba(245,240,232,0.8);
    line-height: 1.5;
  }
  .thesis-meta-programme {
    display: flex;
    flex-wrap: wrap;
    align-items: baseline;
    gap: 0 0.15em;
    row-gap: 4px;
    line-height: 1.45;
  }
  .thesis-meta-programme .seg {
    white-space: nowrap;
  }
  .thesis-meta-programme .sep {
    opacity: 0.65;
    user-select: none;
  }

  .security-badges {
    display: flex; flex-direction: column; gap: 16px;
  }

  .security-badge-row {
    display: flex; align-items: flex-start; gap: 16px;
    padding: 18px 20px;
    background: rgba(255,255,255,0.04);
    border: 1px solid rgba(255,255,255,0.08);
    border-left: 3px solid var(--gold);
  }

  .security-badge-icon { font-size: 22px; flex-shrink: 0; margin-top: 2px; }

  .security-badge-text h4 {
    font-size: 13px; letter-spacing: 0.08em;
    text-transform: uppercase; color: var(--gold);
    margin-bottom: 4px;
  }

  .security-badge-text p {
    font-size: 12px; color: rgba(245,240,232,0.45);
    line-height: 1.6; margin: 0;
  }

  /* ─── CTA ─── */
  .cta-section {
    text-align: center; padding: 64px 40px;
    border: 1px solid var(--border); background: var(--cream);
  }

  .cta-section .eyebrow {
    font-size: 11px; letter-spacing: 0.25em;
    text-transform: uppercase; color: var(--gold);
    margin-bottom: 16px;
  }

  .cta-section h2 {
    font-family: 'Cormorant Garamond', serif;
    font-size: clamp(32px, 4vw, 52px); font-weight: 300;
    margin-bottom: 16px;
  }
  .cta-section h2 em { font-style: italic; color: var(--muted); }

  .cta-section p {
    font-size: 13px; color: var(--muted);
    line-height: 1.7; margin-bottom: 32px;
  }

  .btn-primary {
    display: inline-flex; align-items: center; gap: 10px;
    background: var(--ink); color: var(--paper);
    padding: 16px 36px; text-decoration: none;
    font-family: 'DM Mono', monospace;
    font-size: 12px; letter-spacing: 0.15em; text-transform: uppercase;
    transition: background 0.2s, color 0.2s;
  }
  .btn-primary:hover { background: var(--gold); color: var(--ink); }

  /* ─── FOOTER ─── */
  .site-footer {
    background: var(--ink); color: rgba(245,240,232,0.35);
    font-size: 13px; text-align: center; padding: 22px;
    letter-spacing: 0.08em;
  }
  .site-footer strong { color: var(--gold); }

  /* ─── ANIMATIONS ─── */
  @keyframes fadeUp {
    from { opacity: 0; transform: translateY(16px); }
    to   { opacity: 1; transform: translateY(0); }
  }

  /* ─── RESPONSIVE ─── */
  @media (max-width: 768px) {
    header { padding: 0 20px; }
    nav { display: none; }
    .hero { padding: 60px 20px 50px; }
    .container { padding: 48px 20px 64px; }
    .manifesto { grid-template-columns: 1fr; gap: 40px; }
    .pillars { grid-template-columns: 1fr; }
    .security-grid { grid-template-columns: 1fr; gap: 40px; }
    .security-section { padding: 40px 24px; }
    .thesis-meta-grid {
      flex-direction: column;
      gap: 20px;
    }
  }
</style>
</head>
<body>

<header>
  <a href="shop.php" class="logo">V<span>au</span>LT</a>
  <nav>
    <a href="shop.php">Shop</a>
    <a href="about.php" class="active">About</a>
  </nav>
  <a href="cart.php" class="cart-btn">
    <span>Cart</span>
    <span class="cart-count"><?= $cartCount ?></span>
  </a>
</header>

<section class="hero">
  <div class="hero-bg"></div>
  <div class="hero-label">VAULT — Our Story</div>
  <h1 class="hero-title">Made to<br><em>endure.</em></h1>
  <p class="hero-sub">Fragrance is the only luxury you wear invisibly. VAULT exists to ensure what you carry on your skin is worth remembering.</p>
</section>

<div class="container">

  <div class="manifesto">
    <div class="manifesto-text">
      <div class="eyebrow">The Manifesto</div>
      <h2>Against the <em>forgettable.</em></h2>
      <p>VAULT was founded on the belief that fragrance is the most intimate form of self-expression — and the most neglected. The market is saturated with celebrity launches and synthetic shortcuts. We built something different.</p>
      <p>Every fragrance in our collection is built around single-origin raw materials: Bulgarian rose harvested at dawn, Hindi oud aged for decades, Calabrian bergamot pressed same-day. We don't dilute. We don't rush maceration.</p>
      <p>When you wear VAULT, you are not wearing a trend. You are wearing a composition that took years to perfect and will outlast every season that follows.</p>
    </div>
    <div class="manifesto-aside">
      <div class="manifesto-aside-num">12</div>
      <div class="manifesto-aside-label">Compositions in the collection</div>
      <div class="manifesto-aside-divider"></div>
      <div class="manifesto-aside-quote">"Perfume is the art that makes memory speak." — Annick Goutal</div>
    </div>
  </div>

  <div class="pillars-header">
    <div class="eyebrow">What We Stand For</div>
    <h2>Three principles, no shortcuts.</h2>
  </div>
  <div class="pillars">
    <div class="pillar">
      <div class="pillar-icon">◈</div>
      <div class="pillar-title">Ingredient Purity</div>
      <p class="pillar-desc">We source only single-origin naturals from named growers and distillers. No synthetic replacements for costly materials. Every note in every formula is exactly what we say it is.</p>
    </div>
    <div class="pillar">
      <div class="pillar-icon">◇</div>
      <div class="pillar-title">The Nose's Art</div>
      <p class="pillar-desc">Each composition is the work of an independent perfumer — not a committee, not a brief, not a focus group. We give our creators full creative authority and credit them by name.</p>
    </div>
    <div class="pillar">
      <div class="pillar-icon">○</div>
      <div class="pillar-title">No Dilution</div>
      <p class="pillar-desc">Our Eau de Parfum concentrations start at 20%. We don't cut with fillers to widen margins. Longer maceration, higher concentration — your fragrance opens differently on hour one versus hour six.</p>
    </div>
  </div>

  <div class="security-section">
    <div class="security-grid">
      <div class="security-text">
        <div class="eyebrow">How We Protect You</div>
        <h2>Checkout with <em>confidence.</em></h2>
        <p>VAULT was built as a demonstration of secure e-commerce architecture. Every transaction flows through a stack designed around one principle: your data never touches our server unprotected.</p>
        <div class="thesis-meta">
          <div class="thesis-meta-intro">
            <div>Crafted by Daniel Njama</div>
            A Bachelor&rsquo;s thesis exploring secure web payment systems, built with industry-grade security practices for the <strong>Computer Science</strong> programme at <strong>The John Paul II Catholic University of Lublin</strong>.
          </div>
          <div class="thesis-meta-grid">
            <div class="thesis-meta-col--id">
              <span class="thesis-meta-item-label">Student ID</span>
              <div class="thesis-meta-item-value">162439</div>
            </div>
            <div class="thesis-meta-col--programme">
              <span class="thesis-meta-item-label">Programme</span>
              <div class="thesis-meta-item-value thesis-meta-programme">
                <span class="seg">Computer Science</span><span class="sep"> · </span>
                <span class="seg">English</span><span class="sep"> · </span>
                <span class="seg">First-cycle</span><span class="sep"> · </span>
                <span class="seg">2025/2026</span>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="security-badges">
        <div class="security-badge-row">
          <div class="security-badge-icon">🔒</div>
          <div class="security-badge-text">
            <h4>TLS Encryption</h4>
            <p>All traffic is encrypted in transit. Your browser never sends sensitive data over plain HTTP.</p>
          </div>
        </div>
        <div class="security-badge-row">
          <div class="security-badge-icon">⚡</div>
          <div class="security-badge-text">
            <h4>Stripe Tokenization</h4>
            <p>Card numbers are tokenized client-side by Stripe. Raw card data never reaches our server.</p>
          </div>
        </div>
        <div class="security-badge-row">
          <div class="security-badge-icon">🛡</div>
          <div class="security-badge-text">
            <h4>CSP Nonces & SRI</h4>
            <p>Content Security Policy nonces and Subresource Integrity hashes prevent script injection attacks.</p>
          </div>
        </div>
        <div class="security-badge-row">
          <div class="security-badge-icon">✓</div>
          <div class="security-badge-text">
            <h4>PCI DSS Aligned</h4>
            <p>Our checkout flow is designed to meet PCI DSS SAQ-A requirements by delegating card handling entirely to Stripe.</p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="cta-section">
    <div class="eyebrow">Ready to Browse</div>
    <h2>Find a scent <em>worth wearing.</em></h2>
    <p>Twelve compositions. Each one formulated to leave a mark long after you've left the room.</p>
    <a href="shop.php" class="btn-primary"><span>View the Collection</span><span>→</span></a>
  </div>

</div>

<footer class="site-footer">
  <strong>VauLT</strong> Premium Store · © 2025 · All rights reserved
</footer>

</body>
</html>

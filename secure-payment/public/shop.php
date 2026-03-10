<?php
session_start();
require_once __DIR__ . '/../config/products.php';

use function Vault\getProduct;
use function Vault\cartCount;
use function Vault\getCart;

// Handle AJAX add-to-cart POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'], $_POST['product_id'])) {
    $id = $_POST['product_id'];
    if ($_POST['action'] === 'add' && getProduct($id)) {
        if (!isset($_SESSION['cart'])) { $_SESSION['cart'] = []; }
        $_SESSION['cart'][$id] = ($_SESSION['cart'][$id] ?? 0) + 1;
    }
    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'cartCount' => cartCount()]);
    exit;
}

$cartCount = cartCount();
$sessionCart = getCart();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>VAULT — Premium Store</title>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300;1,400&family=DM+Mono:wght@300;400;500&display=swap" rel="stylesheet">
<style>
  :root {
    --ink: #0d0d0d;
    --paper: #f5f0e8;
    --cream: #ede8dc;
    --gold: #c9a84c;
    --gold-light: #e8d5a0;
    --rust: #b85c38;
    --muted: #8a8070;
    --border: rgba(13,13,13,0.12);
  }

  * { margin: 0; padding: 0; box-sizing: border-box; }

  body {
    background: var(--paper);
    color: var(--ink);
    font-family: 'DM Mono', monospace;
    font-size: 13px;
    min-height: 100vh;
  }

  /* ── HEADER ── */
  header {
    position: sticky; top: 0; z-index: 100;
    background: var(--ink);
    color: var(--paper);
    display: flex; align-items: center; justify-content: space-between;
    padding: 0 40px;
    height: 64px;
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
    font-size: 13px; text-transform: uppercase;
    transition: color 0.2s;
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

  /* ── HERO ── */
  .hero {
    position: relative; padding: 100px 40px 80px;
    overflow: hidden; border-bottom: 1px solid var(--border);
    display: flex; flex-direction: column; align-items: center;
    text-align: center;
  }

  .hero-bg {
    position: absolute; inset: 0;
    background: radial-gradient(ellipse 80% 60% at 70% 50%, rgba(201,168,76,0.08) 0%, transparent 70%);
    pointer-events: none;
  }

  .hero-label {
    font-size: 10px; letter-spacing: 0.25em;
    text-transform: uppercase; color: var(--gold); margin-bottom: 20px;
  }

  .hero-title {
    font-family: 'Cormorant Garamond', serif;
    font-size: clamp(52px, 7vw, 96px); font-weight: 300;
    line-height: 0.95; letter-spacing: -0.01em; margin-bottom: 32px;
  }
  .hero-title em { font-style: italic; color: var(--muted); }

  .hero-sub {
    max-width: 560px; color: var(--muted);
    line-height: 1.7; font-size: 12px;
  }

  .hero-line {
    position: absolute; right: 40px; top: 0; bottom: 0;
    width: 1px; background: var(--border);
  }

  .hero-aside {
    position: absolute; right: 80px; top: 50%;
    transform: translateY(-50%); text-align: right;
  }
  .hero-aside-num {
    font-family: 'Cormorant Garamond', serif;
    font-size: 80px; font-weight: 300;
    color: rgba(13,13,13,0.06); line-height: 1;
  }
  .hero-aside-label { font-size: 10px; letter-spacing: 0.2em; color: var(--muted); text-transform: uppercase; }

  /* ── FILTER BAR ── */
  .filter-bar {
    display: flex; align-items: center; gap: 0;
    padding: 0 40px; border-bottom: 1px solid var(--border);
    background: rgba(245,240,232,0.8);
    overflow-x: auto;
  }

  .filter-btn {
    background: none; border: none; padding: 16px 24px;
    font-family: 'DM Mono', monospace;
    font-size: 11px; letter-spacing: 0.1em; text-transform: uppercase;
    color: var(--muted); cursor: pointer;
    border-bottom: 2px solid transparent;
    transition: all 0.2s; white-space: nowrap;
  }
  .filter-btn:hover { color: var(--ink); }
  .filter-btn.active { color: var(--ink); border-bottom-color: var(--gold); }

  .filter-count {
    display: inline-block; background: var(--gold-light); color: var(--muted);
    padding: 1px 6px; border-radius: 2px; font-size: 9px; margin-left: 6px;
  }

  /* ── MAIN ── */
  .main-content { padding: 48px 40px; max-width: 1400px; margin: 0 auto; }

  .section-header {
    display: flex; justify-content: space-between; align-items: flex-end;
    margin-bottom: 40px;
  }
  .section-title { font-family: 'Cormorant Garamond', serif; font-size: 36px; font-weight: 300; letter-spacing: 0.02em; }
  .section-meta { font-size: 11px; color: var(--muted); letter-spacing: 0.1em; }

  /* ── PRODUCT GRID ── */
  .product-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 2px;
  }

  .product-card {
    background: var(--cream); position: relative;
    overflow: hidden; cursor: pointer;
    border: 1px solid var(--border);
    transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1);
  }
  .product-card:hover { transform: translateY(-4px); z-index: 2; box-shadow: 0 20px 60px rgba(13,13,13,0.12); }

  .product-img {
    aspect-ratio: 4/5; position: relative;
    overflow: hidden; background: var(--cream);
  }
  .product-img img {
    width: 100%; height: 100%;
    object-fit: cover; object-position: center top;
    display: block;
    transition: transform 0.6s cubic-bezier(0.16, 1, 0.3, 1);
  }
  .product-card:hover .product-img img { transform: scale(1.06); }

  .product-emoji {
    position: absolute; inset: 0;
    display: flex; align-items: center; justify-content: center;
    font-size: 72px; background: var(--cream);
  }

  .product-badge {
    position: absolute; top: 12px; left: 12px;
    background: var(--ink); color: var(--gold);
    padding: 4px 10px; font-size: 9px; letter-spacing: 0.15em; text-transform: uppercase;
  }
  .product-badge.sale       { background: var(--rust); color: white; }
  .product-badge.new        { background: var(--gold); color: var(--ink); }
  .product-badge.bestseller { background: var(--ink);  color: var(--gold); }

  .product-info { padding: 20px 20px 24px; border-top: 1px solid var(--border); }

  .product-category { font-size: 9px; letter-spacing: 0.2em; text-transform: uppercase; color: var(--muted); margin-bottom: 6px; }

  .product-name { font-family: 'Cormorant Garamond', serif; font-size: 20px; font-weight: 400; line-height: 1.2; margin-bottom: 4px; }

  .product-desc { font-size: 11px; color: var(--muted); line-height: 1.5; margin-bottom: 16px; }

  .product-footer { display: flex; align-items: center; justify-content: space-between; }

  .product-price { font-family: 'Cormorant Garamond', serif; font-size: 22px; font-weight: 600; }
  .product-price-old { font-size: 13px; color: var(--muted); text-decoration: line-through; margin-left: 6px; font-weight: 300; }

  .add-btn {
    background: var(--ink); color: var(--paper); border: none;
    padding: 10px 18px; font-family: 'DM Mono', monospace;
    font-size: 10px; letter-spacing: 0.1em; text-transform: uppercase;
    cursor: pointer; transition: all 0.2s; min-width: 60px; text-align: center;
  }
  .add-btn:hover  { background: var(--gold); color: var(--ink); }
  .add-btn.added  { background: var(--gold); color: var(--ink); }

  /* ── OVERLAY ── */
  .overlay {
    position: fixed; inset: 0; background: rgba(13,13,13,0.5);
    z-index: 200; opacity: 0; pointer-events: none; transition: opacity 0.3s;
  }
  .overlay.open { opacity: 1; pointer-events: all; }

  /* ── CART DRAWER ── */
  .cart-drawer {
    position: fixed; right: 0; top: 0; bottom: 0;
    width: 420px; max-width: 95vw; background: var(--paper);
    z-index: 201; display: flex; flex-direction: column;
    transform: translateX(100%);
    transition: transform 0.35s cubic-bezier(0.16, 1, 0.3, 1);
    border-left: 1px solid var(--border);
  }
  .cart-drawer.open { transform: translateX(0); }

  .drawer-header {
    padding: 24px 28px; border-bottom: 1px solid var(--border);
    display: flex; align-items: center; justify-content: space-between;
  }
  .drawer-title { font-family: 'Cormorant Garamond', serif; font-size: 24px; font-weight: 300; }

  .close-btn {
    background: none; border: none; width: 32px; height: 32px;
    cursor: pointer; font-size: 18px; color: var(--muted);
    display: flex; align-items: center; justify-content: center; transition: color 0.2s;
  }
  .close-btn:hover { color: var(--ink); }

  .cart-items { flex: 1; overflow-y: auto; padding: 20px 28px; }

  .cart-empty { text-align: center; padding: 60px 20px; color: var(--muted); }
  .cart-empty-icon { font-size: 48px; margin-bottom: 16px; opacity: 0.3; }
  .cart-empty p { font-size: 12px; letter-spacing: 0.1em; text-transform: uppercase; }

  .cart-item {
    display: flex; gap: 16px; padding: 16px 0;
    border-bottom: 1px solid var(--border);
  }

  .cart-item-img {
    width: 72px; height: 72px; flex-shrink: 0;
    border: 1px solid var(--border); overflow: hidden;
    background: var(--cream);
  }
  .cart-item-img img {
    width: 100%; height: 100%; object-fit: cover; object-position: center top;
    display: block;
  }
  .cart-item-img-fb {
    width: 100%; height: 100%;
    display: flex; align-items: center; justify-content: center;
    font-size: 28px;
  }

  .cart-item-info { flex: 1; }
  .cart-item-name { font-family: 'Cormorant Garamond', serif; font-size: 17px; margin-bottom: 4px; }
  .cart-item-cat { font-size: 10px; color: var(--muted); letter-spacing: 0.1em; text-transform: uppercase; }

  .cart-item-controls { display: flex; align-items: center; gap: 12px; margin-top: 10px; }

  .qty-btn {
    background: none; border: 1px solid var(--border);
    width: 24px; height: 24px; cursor: pointer; font-size: 14px; color: var(--ink);
    display: flex; align-items: center; justify-content: center; transition: all 0.15s;
  }
  .qty-btn:hover { background: var(--ink); color: var(--paper); border-color: var(--ink); }

  .qty-display { font-size: 13px; min-width: 20px; text-align: center; }

  .cart-item-price { font-family: 'Cormorant Garamond', serif; font-size: 18px; font-weight: 600; align-self: center; }

  .remove-btn {
    background: none; border: none; color: var(--muted);
    cursor: pointer; font-size: 11px; text-decoration: underline;
    margin-left: auto; font-family: 'DM Mono', monospace; transition: color 0.15s;
  }
  .remove-btn:hover { color: var(--rust); }

  /* ── DRAWER FOOTER ── */
  .drawer-footer {
    padding: 24px 28px; border-top: 1px solid var(--border); background: var(--cream);
  }

  .subtotal-row { display: flex; justify-content: space-between; align-items: baseline; margin-bottom: 6px; }
  .subtotal-label { font-size: 11px; letter-spacing: 0.1em; text-transform: uppercase; color: var(--muted); }
  .subtotal-val { font-family: 'Cormorant Garamond', serif; font-size: 28px; font-weight: 300; }

  .subtotal-note { font-size: 10px; color: var(--muted); margin-bottom: 20px; }

  .checkout-btn {
    width: 100%; background: var(--ink); color: var(--paper); border: none;
    padding: 16px; font-family: 'DM Mono', monospace;
    font-size: 12px; letter-spacing: 0.15em; text-transform: uppercase;
    cursor: pointer; transition: all 0.2s;
    display: flex; align-items: center; justify-content: center; gap: 10px;
    text-decoration: none;
  }
  .checkout-btn:hover:not(.disabled) { background: var(--gold); color: var(--ink); }
  .checkout-btn.disabled { opacity: 0.4; pointer-events: none; }

  .secure-badges { display: flex; justify-content: center; gap: 20px; margin-top: 14px; }
  .secure-badge {
    display: flex; align-items: center; gap: 5px;
    font-size: 9px; letter-spacing: 0.1em; text-transform: uppercase; color: var(--muted);
  }

  /* ── TOAST ── */
  .toast {
    position: fixed; bottom: 32px; left: 50%;
    transform: translateX(-50%) translateY(20px);
    background: var(--ink); color: var(--paper);
    padding: 12px 28px; font-size: 11px; letter-spacing: 0.1em; text-transform: uppercase;
    opacity: 0; pointer-events: none; transition: all 0.3s;
    z-index: 400; border-left: 3px solid var(--gold);
  }
  .toast.show { opacity: 1; transform: translateX(-50%) translateY(0); }

  /* ── FOOTER ── */
  .site-footer {
    background: var(--ink); color: rgba(245,240,232,0.35);
    font-size: 11px; text-align: center; padding: 20px; letter-spacing: 0.08em;
  }
  .site-footer strong { color: var(--gold); }

  /* ── RESPONSIVE ── */
  @media (max-width: 640px) {
    header { padding: 0 20px; }
    nav { display: none; }
    .hero { padding: 60px 20px 50px; }
    .hero-line, .hero-aside { display: none; }
    .main-content { padding: 32px 20px; }
    .filter-bar { padding: 0 20px; }
    .product-grid { grid-template-columns: repeat(2, 1fr); }
  }
</style>
</head>
<body>

<!-- HEADER -->
<header>
  <a href="shop.php" class="logo">V<span>au</span>LT</a>
  <nav>
    <a href="shop.php">Shop</a>
    <a href="about.php">About</a>
  </nav>
  <button class="cart-btn" onclick="openCart()">
    <span>Cart</span>
    <span class="cart-count" id="cart-count"><?= $cartCount ?></span>
  </button>
</header>

<!-- HERO -->
<section class="hero">
  <div class="hero-bg"></div>
  <div class="hero-label">VAULT — SS 2025 Collection</div>
  <h1 class="hero-title">Scent <em>as</em><br>memory.</h1>
  <p class="hero-sub">Fragrances, body oils and ritual soaps — each composed to outlast the moment. Worn once, remembered always.</p>
  <div class="hero-line"></div>
  <div class="hero-aside">
    <div class="hero-aside-num"><?= count(PRODUCTS) ?></div>
    <div class="hero-aside-label">Pieces</div>
  </div>
</section>

<!-- FILTER BAR -->
<div class="filter-bar">
  <button class="filter-btn active" onclick="filterProducts('all', this)">
    All <span class="filter-count"><?= count(PRODUCTS) ?></span>
  </button>
  <?php
  $cats = [];
  foreach (PRODUCTS as $p) {
    $cat = $p['category'];
    if (!isset($cats[$cat])) { $cats[$cat] = 0; }
    $cats[$cat]++;
  }
  ksort($cats);
  foreach ($cats as $cat => $count) { ?>
  <button class="filter-btn" onclick="filterProducts('<?= htmlspecialchars(strtolower($cat)) ?>', this)">
    <?= htmlspecialchars($cat) ?> <span class="filter-count"><?= $count ?></span>
  </button>
  <?php } ?>
</div>

<!-- PRODUCTS -->
<main class="main-content">
  <div class="section-header">
    <h2 class="section-title">The Collection</h2>
    <span class="section-meta" id="product-count"><?= count(PRODUCTS) ?> products</span>
  </div>
  <div class="product-grid" id="product-grid">
    <?php foreach (PRODUCTS as $p) { ?>
    <div class="product-card" data-category="<?= htmlspecialchars(strtolower($p['category'])) ?>">
      <div class="product-img">
        <img src="<?= htmlspecialchars($p['image']) ?>" alt="<?= htmlspecialchars($p['name']) ?>" loading="lazy" class="shop-img">
        <div class="product-emoji" style="display:none"><?= $p['emoji'] ?></div>
        <?php if (!empty($p['badge'])): ?>
          <div class="product-badge <?= htmlspecialchars($p['badge']) ?>"><?= htmlspecialchars($p['badge']) ?></div>
        <?php endif; ?>
      </div>
      <div class="product-info">
        <div class="product-category"><?= htmlspecialchars($p['category']) ?></div>
        <div class="product-name"><?= htmlspecialchars($p['name']) ?></div>
        <div class="product-desc"><?= htmlspecialchars($p['desc']) ?></div>
        <div class="product-footer">
          <div>
            <span class="product-price">$<?= number_format($p['price'], 2) ?></span>
            <?php if (!empty($p['original_price'])): ?>
              <span class="product-price-old">$<?= number_format($p['original_price'], 2) ?></span>
            <?php endif; ?>
          </div>
          <button
            class="add-btn"
            id="btn-<?= htmlspecialchars($p['id']) ?>"
            onclick="addToCart('<?= htmlspecialchars($p['id']) ?>', '<?= htmlspecialchars(addslashes($p['name'])) ?>', <?= (float)$p['price'] ?>, '<?= $p['emoji'] ?>', '<?= htmlspecialchars(addslashes($p['category'])) ?>', '<?= htmlspecialchars($p['image']) ?>')"
          >Add</button>
        </div>
      </div>
    </div>
    <?php } ?>
  </div>
</main>

<!-- OVERLAY -->
<button class="overlay" id="overlay" onclick="closeCart()" onkeydown="if(event.key==='Escape')closeCart()" aria-label="Close cart"></button>

<!-- CART DRAWER -->
<div class="cart-drawer" id="cart-drawer">
  <div class="drawer-header">
    <h2 class="drawer-title">Your Cart</h2>
    <button class="close-btn" onclick="closeCart()">✕</button>
  </div>
  <div class="cart-items" id="cart-items">
    <div class="cart-empty">
      <div class="cart-empty-icon">◯</div>
      <p>Your cart is empty</p>
    </div>
  </div>
  <div class="drawer-footer">
    <div class="subtotal-row">
      <span class="subtotal-label">Subtotal</span>
      <span class="subtotal-val" id="subtotal">$0.00</span>
    </div>
    <p class="subtotal-note">Shipping and taxes calculated at checkout</p>
    <a href="cart.php" class="checkout-btn disabled" id="checkout-btn">
      <span>Proceed to Checkout</span>
      <span>→</span>
    </a>
    <div class="secure-badges">
      <span class="secure-badge">🔒 TLS Encrypted</span>
      <span class="secure-badge">🔑 Tokenized</span>
      <span class="secure-badge">✓ PCI DSS</span>
    </div>
  </div>
</div>

<!-- TOAST -->
<div class="toast" id="toast"></div>

<!-- FOOTER -->
<footer class="site-footer">
  <strong> VauLT Premium Store</strong>
</footer>

<script>
let cart = {};
<?php foreach ($sessionCart as $id => $qty):
    $p = getProduct($id);
    if (!$p) { continue; } ?>
cart[<?= json_encode($id) ?>] = {
  id: <?= json_encode($p['id']) ?>,
  name: <?= json_encode($p['name']) ?>,
  price: <?= (float)$p['price'] ?>,
  emoji: <?= json_encode($p['emoji']) ?>,
  image: <?= json_encode($p['image'] ?? '') ?>,
  category: <?= json_encode($p['category']) ?>,
  qty: <?= (int)$qty ?>
};
<?php endforeach; ?>

async function addToCart(id, name, price, emoji, category, image = '') {
  if (cart[id]) {
    cart[id].qty++;
  } else {
    cart[id] = { id, name, price, emoji, image, category, qty: 1 };
  }
  updateCartUI();
  showToast(name + ' added to cart');

  const btn = document.getElementById('btn-' + id);
  if (btn) {
    btn.textContent = '✓ Added';
    btn.classList.add('added');
    setTimeout(() => { btn.textContent = 'Add'; btn.classList.remove('added'); }, 1500);
  }

  const form = new FormData();
  form.append('action', 'add');
  form.append('product_id', id);
  fetch('shop.php', { method: 'POST', body: form }).catch(() => {});
}

async function changeQty(id, delta) {
  if (!cart[id]) return;
  cart[id].qty += delta;
  if (cart[id].qty <= 0) delete cart[id];
  updateCartUI();
  const form = new FormData();
  form.append('action', 'update');
  form.append('product_id', id);
  form.append('qty', cart[id] ? cart[id].qty : 0);
  fetch('cart.php', { method: 'POST', body: form }).catch(() => {});
}

async function removeFromCart(id) {
  delete cart[id];
  updateCartUI();
  const form = new FormData();
  form.append('action', 'remove');
  form.append('product_id', id);
  fetch('cart.php', { method: 'POST', body: form }).catch(() => {});
}

function updateCartUI() {
  const items    = Object.values(cart);
  const total    = items.reduce((s, i) => s + i.price * i.qty, 0);
  const count    = items.reduce((s, i) => s + i.qty, 0);

  document.getElementById('cart-count').textContent = count;
  document.getElementById('subtotal').textContent = '$' + total.toFixed(2);

  const btn = document.getElementById('checkout-btn');
  btn.classList.toggle('disabled', items.length === 0);

  const container = document.getElementById('cart-items');
  if (items.length === 0) {
    container.innerHTML = `
      <div class="cart-empty">
        <div class="cart-empty-icon">◯</div>
        <p>Your cart is empty</p>
      </div>`;
    return;
  }

  container.innerHTML = items.map(item => `
    <div class="cart-item">
      <div class="cart-item-img">${item.image ? `<img src="${item.image}" alt="${item.name}">` : `<div class="cart-item-img-fb">${item.emoji}</div>`}</div>
      <div class="cart-item-info">
        <div class="cart-item-name">${item.name}</div>
        <div class="cart-item-cat">${item.category}</div>
        <div class="cart-item-controls">
          <button class="qty-btn" onclick="changeQty('${item.id}', -1)">−</button>
          <span class="qty-display">${item.qty}</span>
          <button class="qty-btn" onclick="changeQty('${item.id}', 1)">+</button>
          <button class="remove-btn" onclick="removeFromCart('${item.id}')">Remove</button>
        </div>
      </div>
      <div class="cart-item-price">$${(item.price * item.qty).toFixed(2)}</div>
    </div>
  `).join('');
}

function filterProducts(filter, btn) {
  document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');
  let count = 0;
  document.querySelectorAll('.product-card').forEach(card => {
    const show = filter === 'all' || card.dataset.category === filter;
    card.style.display = show ? '' : 'none';
    if (show) count++;
  });
  document.getElementById('product-count').textContent = count + ' product' + (count !== 1 ? 's' : '');
}

function openCart() {
  document.getElementById('cart-drawer').classList.add('open');
  document.getElementById('overlay').classList.add('open');
  document.body.style.overflow = 'hidden';
}
function closeCart() {
  document.getElementById('cart-drawer').classList.remove('open');
  document.getElementById('overlay').classList.remove('open');
  document.body.style.overflow = '';
}

function showToast(msg) {
  const t = document.getElementById('toast');
  t.textContent = msg;
  t.classList.add('show');
  setTimeout(() => t.classList.remove('show'), 2500);
}

updateCartUI();

document.addEventListener('error', function(e) {
  if (e.target.classList.contains('shop-img')) {
    e.target.style.display = 'none';
    var fb = e.target.nextElementSibling;
    if (fb) { fb.style.display = 'flex'; }
  }
}, true);
</script>
</body>
</html>

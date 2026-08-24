# Subfolder & Subdirectory Installation

You can install the reverse proxy inside a subfolder under an existing website (for example, `https://marketbaz.herminal.com/marketapp/`).

---

## How It Works

The proxy's `Request.php` component automatically inspects `$_SERVER['SCRIPT_NAME']` and determines its execution directory. 

When a client sends a request to:
`https://marketbaz.herminal.com/marketapp/v1/orders`

The proxy automatically:
1. Detects that it is running in `/marketapp`.
2. Strips `/marketapp` from the incoming path.
3. Forwards `/v1/orders` to `https://api.marketapp.org/v1/orders`.

---

## Installation Steps

1. Create a directory named `marketapp` inside your website root (e.g. `/public_html/marketapp/`).
2. Upload all project files directly into that directory:
   ```
   /public_html/marketapp/
   ├── .htaccess
   ├── index.php
   ├── config.php
   └── src/
   ```
3. Keep `config.php` default:
   ```php
   'target_url' => 'https://api.marketapp.org',
   ```
4. Now your proxy endpoint is ready at:
   - **Base URL:** `https://marketbaz.herminal.com/marketapp/`
   - **API Docs:** `https://marketbaz.herminal.com/marketapp/docs/`

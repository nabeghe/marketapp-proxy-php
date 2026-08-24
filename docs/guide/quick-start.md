# Quick Start

Get your reverse proxy up and running locally or on a server in just a few simple steps.

---

## 1. Requirements

- **PHP:** 7.4 or newer (supports PHP 8.0, 8.1, 8.2, 8.3, 8.4, 8.5)
- **PHP Extensions:** `curl`, `json`
- **Web Server:** Apache (with `mod_rewrite`), Nginx, LiteSpeed, or PHP Built-in Server

---

## 2. Local Testing (PHP Built-in Server)

You can spin up an instance instantly without installing any external web server:

```bash
# Clone the repository
git clone https://github.com/nabeghe/marketapp-proxy-php.git
cd marketapp-proxy-php

# Start the PHP built-in server
php -S 127.0.0.1:8080 index.php
```

### Verify with cURL

Open another terminal and test:

```bash
# Fetch the Swagger documentation through your proxy
curl -i http://127.0.0.1:8080/docs/
```

You will receive the full Swagger UI HTML page forwarded directly from MarketApp API!

---

## 3. Deployment to Web Host

1. Upload all project files (including `.htaccess` and the `src/` directory) to your web server (e.g. `public_html` or a subfolder like `public_html/marketapp/`).
2. Verify that `config.php` has `'target_url' => 'https://api.marketapp.org'`.
3. In your client application or bot, change your API `base_url` to your server URL (e.g. `https://your-domain.com/` or `https://your-domain.com/marketapp/`).

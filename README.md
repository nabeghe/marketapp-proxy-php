# MarketApp API Reverse Proxy (PHP)

A fast, lightweight, and secure PHP reverse proxy designed to seamlessly forward and relay requests directly to the **MarketApp API** (`https://api.marketapp.org/`).

This project intercepts all incoming HTTP requests (methods, headers, payload/body, query parameters, multipart uploads, and binary streams), forwards them transparently to the MarketApp upstream API, and returns the response back to the client while preserving the HTTP status code, headers, and content intact.

---

## Features

- ⚡ **Full PHP Version Compatibility:** Compatible with PHP 7.4 through PHP 8.5.
- 🚀 **Zero-Dependency:** Runs standalone out-of-the-box by simply uploading files to any shared hosting (cPanel, DirectAdmin, Plesk) or VPS without requiring Composer.
- 🔄 **Comprehensive HTTP Method Support:** Full support for `GET`, `POST`, `PUT`, `DELETE`, `PATCH`, `OPTIONS`, and `HEAD`.
- 📦 **All Data Formats Supported:** Seamlessly handles `JSON`, `multipart/form-data`, `application/x-www-form-urlencoded`, and raw body / binary streams.
- 🔐 **Header & Authentication Handling:** Full support for `Authorization` headers (`Bearer` token, `Basic` auth), custom headers, and automatic filtering of hop-by-hop headers.
- 🌐 **Automatic CORS Management:** Standard responses for preflight (`OPTIONS`) requests with configurable `Access-Control-*` headers.
- 🛠️ **Ready-to-use Web Server Configurations:** Includes `.htaccess` for Apache / LiteSpeed and sample configuration for Nginx.
- 🔍 **Debug Mode:** Inspect execution time, target URL, and network errors directly in headers and error responses.

---

## Project Structure

```
marketapp-proxy-php/
├── .agents/
│   └── skills/
│       └── marketapp-proxy/
│           └── SKILL.md          # Agent guidelines & skill specification
├── src/
│   ├── Config.php                # Configuration manager
│   ├── Http/
│   │   ├── Request.php           # Client request capture and parsing
│   │   └── Response.php          # Upstream response construction and emitting
│   └── ProxyEngine.php           # cURL request forwarding engine
├── config.example.php            # Default configuration template
├── config.php                    # Active local configuration
├── autoload.php                  # Standalone built-in PSR-4 autoloader
├── index.php                     # Main application entry point
├── .htaccess                     # Apache/LiteSpeed rewrite and authorization rules
├── nginx.conf.example            # Sample Nginx server block configuration
├── composer.json                 # Composer definition & PSR-4 autoloading
├── AGENTS.md                     # AI agent guidelines and architecture notes
└── README.md                     # Comprehensive documentation
```

---

## Requirements

- **PHP:** Version 7.4 or higher (including 8.0, 8.1, 8.2, 8.3, 8.4, 8.5)
- **PHP Extensions:** `curl`, `json`
- **Web Server:** Apache (with `mod_rewrite`), LiteSpeed, Nginx, or PHP built-in web server

---

## Installation & Setup

### Method 1: Shared Hosting (cPanel / DirectAdmin / Plesk)
1. Upload the contents of this repository to your web root (`public_html`) or target subdomain folder.
2. Ensure the `.htaccess` file is uploaded and not hidden by your file manager.
3. If needed, customize settings in `config.php`.
4. Your domain or subdomain now operates as a reverse proxy for MarketApp API.

### Method 2: Linux Server with Nginx
1. Place the project files into your target directory (e.g. `/var/www/marketapp-proxy`).
2. Review `nginx.conf.example` and add the `location` directive to your Nginx configuration:
   ```nginx
   location / {
       try_files $uri $uri/ /index.php?$query_string;
   }
   ```
3. Reload Nginx and PHP-FPM:
   ```bash
   sudo systemctl reload nginx
   ```

### Method 3: Local Development with PHP Built-in Server
For quick testing without requiring an external web server:
```bash
php -S 127.0.0.1:8080 index.php
```

---

## Configuration

Settings are defined in `config.php` and can also be overridden via Environment Variables:

```php
<?php

return [
    // Target MarketApp API base URL
    'target_url' => 'https://api.marketapp.org',

    // Connection timeout in seconds
    'connect_timeout' => 15,

    // Total request execution timeout in seconds
    'timeout' => 60,

    // Verify upstream SSL certificate
    'ssl_verify' => true,

    // Enable debug mode to inspect proxy headers and detailed errors
    'debug' => false,

    // Forward client real IP via X-Forwarded-* headers
    'forward_client_ip' => true,

    // Automatic CORS management
    'enable_cors' => true,
    'cors_origin' => '*',

    // Optional custom headers to inject into upstream requests
    'custom_headers' => [
        // 'X-Custom-Token' => '...',
    ],
];
```

### Supported Environment Variables
- `MARKETAPP_TARGET_URL`
- `MARKETAPP_TIMEOUT`
- `MARKETAPP_CONNECT_TIMEOUT`
- `MARKETAPP_SSL_VERIFY` (`true` / `false`)
- `MARKETAPP_DEBUG` (`true` / `false`)
- `MARKETAPP_ENABLE_CORS` (`true` / `false`)
- `MARKETAPP_CORS_ORIGIN`

---

## Usage & Testing

Once the proxy server is running, all endpoints documented in the MarketApp API Docs (`https://api.marketapp.org/docs/`) are accessible on your domain with identical paths:

### 1. Test Viewing Docs
```bash
curl -i http://your-proxy-domain.com/docs/
```

### 2. Test GET Request with Authorization Token
```bash
curl -i -X GET http://your-proxy-domain.com/api/v1/user/profile \
  -H "Authorization: Bearer YOUR_API_TOKEN" \
  -H "Accept: application/json"
```

### 3. Test POST Request with JSON Body
```bash
curl -i -X POST http://your-proxy-domain.com/api/v1/orders \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_API_TOKEN" \
  -d '{
    "product_id": 123,
    "quantity": 2
  }'
```

## Automated Testing

The project includes an automated test suite with zero external dependencies:

```bash
# Run tests directly with PHP
php tests/run.php

# Or via Composer
composer test
```

---

## Troubleshooting

| Issue | Possible Cause | Solution |
| :--- | :--- | :--- |
| **Missing `Authorization` Header** | Apache does not pass the Authorization header to PHP in CGI/FastCGI mode | Ensure `CGIPassAuth On` and `RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]` are present in `.htaccess`. |
| **404 Not Found for Endpoints** | Apache `mod_rewrite` is disabled or `.htaccess` is ignored | Enable `mod_rewrite` (`a2enmod rewrite`), verify `AllowOverride All` in your virtual host config, and restart Apache. |
| **502 / 504 Gateway Errors** | MarketApp server unreachable or connection timed out | Verify server internet connectivity, DNS resolution, and increase `timeout` in `config.php`. |
| **SSL Verification Failure (Localhost)** | Missing valid CA bundle in local PHP environment | Set `'ssl_verify' => false` in local development environments (keep it `true` in production). |

---

## License
This project is open-source software licensed under the [MIT License](LICENSE.md).

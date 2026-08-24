---
name: marketapp-proxy
description: Guides development, configuration, testing, and debugging of the MarketApp API PHP reverse proxy project.
---

# MarketApp Proxy Skill

This skill provides comprehensive instructions for inspecting, configuring, developing, and verifying the PHP Reverse Proxy for MarketApp API.

## Project Scope
- **Target Upstream API:** `https://api.marketapp.org/` (Documentation: `https://api.marketapp.org/docs/`)
- **Compatibility:** PHP 7.4 to PHP 8.5
- **Architecture:** Zero-dependency PSR-4 / Standalone cURL HTTP forwarder

## Key Tasks & Procedures

### 1. Running Automated Tests
Run the comprehensive zero-dependency unit test suite:

```bash
php tests/run.php
```

### 2. Validating PHP Syntax & Files
Whenever you modify or add PHP files, run a lint check on all PHP files:

```bash
php -l index.php
php -l config.php
php -l src/Config.php
php -l src/Http/Request.php
php -l src/Http/Response.php
php -l src/ProxyEngine.php
```

### 2. Local Testing with PHP Built-in Server
You can launch a local instance for manual or automated verification:

```bash
php -S 127.0.0.1:8085 index.php
```

Then test with cURL requests:

```bash
# Test GET docs endpoint
curl -i http://127.0.0.1:8085/docs/

# Test POST or JSON endpoints
curl -i -X POST http://127.0.0.1:8085/v1/endpoint \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer test_token" \
  -d '{"key":"value"}'
```

### 3. Configuration Management
Configuration can be adjusted in `config.php` or via environment variables:
- `target_url` / `MARKETAPP_TARGET_URL`: Upstream base URL (default: `https://api.marketapp.org`)
- `timeout` / `MARKETAPP_TIMEOUT`: Execution timeout in seconds (default: `60`)
- `connect_timeout` / `MARKETAPP_CONNECT_TIMEOUT`: Connection timeout in seconds (default: `15`)
- `ssl_verify` / `MARKETAPP_SSL_VERIFY`: SSL certificate verification (default: `true`)
- `debug` / `MARKETAPP_DEBUG`: Enables debug response headers (`X-Proxy-*`) and detailed error outputs.
- `enable_cors` / `MARKETAPP_ENABLE_CORS`: Automatically handles CORS preflight `OPTIONS` and response headers.

### 4. Common Troubleshooting
- **Missing Authorization Header:** Verify `.htaccess` has `CGIPassAuth On` and `RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]`. On Nginx, verify `fastcgi_param HTTP_AUTHORIZATION $http_authorization;`.
- **SSL Certificate Verification Failures:** If upstream SSL validation fails in local environments without updated CA bundles, check `ssl_verify` in `config.php`.
- **Large Request Payloads:** Adjust `client_max_body_size` in Nginx or `post_max_size` / `upload_max_filesize` in `php.ini`.

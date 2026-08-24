# Architecture & Design

## Codebase Structure

The project follows a clean, modular, and PSR-4 compliant structure while maintaining zero mandatory external dependencies:

```
marketapp-proxy-php/
├── src/
│   ├── Config.php             # Centralized configuration manager
│   ├── Http/
│   │   ├── Request.php        # HTTP Request representation & SAPI normalizer
│   │   └── Response.php       # HTTP Response representation & emitter
│   └── ProxyEngine.php        # Core cURL forwarding engine
├── tests/
│   ├── TestCase.php           # Base assertion framework
│   ├── Unit/                  # Unit test cases
│   └── run.php                # Standalone test runner
├── config.example.php         # Configuration template
├── config.php                 # Active configuration
├── index.php                  # Single entry point
├── .htaccess                  # Apache / LiteSpeed rewrite rules
└── nginx.conf.example         # Nginx server block reference
```

---

## Core Components

### 1. `Request.php`
- Captures HTTP method, raw path, query string, headers, client IP, and raw request payload stream (`php://input`).
- Normalizes subfolder paths automatically (e.g. stripping `/marketapp/` if deployed inside a subfolder).
- Resolves Apache CGI/FastCGI Authorization header quirks (`CGIPassAuth`, `HTTP_AUTHORIZATION`, `REDIRECT_HTTP_AUTHORIZATION`).

### 2. `ProxyEngine.php`
- Constructs the destination URL using `target_url` + normalized path + query string.
- Strips hop-by-hop headers (`Transfer-Encoding`, `Connection`, `Keep-Alive`, `Upgrade`, etc.).
- Appends forwarding headers (`X-Forwarded-For`, `X-Forwarded-Proto`, `X-Forwarded-Host`).
- Executes non-blocking streaming cURL transfer.
- Encapsulates network failures (timeouts, DNS failures) into standard `502 Bad Gateway` or `504 Gateway Timeout` JSON responses.

### 3. `Response.php`
- Emits exact HTTP response status codes.
- Streams response headers and response body back to the client.

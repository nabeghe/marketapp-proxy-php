# AGENTS.md

## Repository Overview
This repository contains a high-performance, lightweight PHP reverse proxy for the **MarketApp API** (`https://api.marketapp.org/`).
Its primary purpose is to receive all client HTTP requests (methods, headers, path, query parameters, payload/body) and forward them transparently to the target MarketApp API, returning the upstream response back to the client.

## Technical Requirements & Guidelines for AI Agents

### 1. PHP Version Compatibility
- **Target Compatibility:** PHP 7.4 up to PHP 8.5.
- **Rule:** Do NOT use language features exclusive to PHP 8.0+ (such as Constructor Property Promotion, Union Types without PHP 7.4 fallback, Enums, Match expressions, Named arguments) in core codebase files unless backwards-compatibility is maintained.
- Use explicit typed properties (PHP 7.4 compatible: `protected string $var;`).
- Always specify `declare(strict_types=1);` at the top of PHP files.

### 2. Zero-Dependency & Autoloading
- The project must run out-of-the-box on shared hosting (cPanel, DirectAdmin, Plesk) without mandatory Composer installation.
- A built-in fallback autoloader is provided in `index.php` alongside optional PSR-4 Composer autoloading (`Nabeghe\MarketAppProxy\`).
- Rely strictly on standard PHP extensions: `ext-curl` and `ext-json`.

### 3. Header & Request/Response Forwarding
- Preserve client headers while removing hop-by-hop headers (e.g. `Connection`, `Transfer-Encoding`, `Keep-Alive`, `Upgrade`).
- Retain exact HTTP status codes from upstream (e.g. 200, 201, 400, 401, 403, 404, 422, 500).
- Forward `Authorization` header properly across all server SAPIs.
- Respect streaming/binary payloads, multipart/form-data, and JSON formats without altering the payload.

### 4. Directory Structure
```
marketapp-proxy-php/
├── .agents/
│   └── skills/
│       └── marketapp-proxy/
│           └── SKILL.md       # Agent skill specification
├── src/
│   ├── Config.php             # Configuration manager
│   ├── Http/
│   │   ├── Request.php        # HTTP Request abstraction
│   │   └── Response.php       # HTTP Response abstraction & sender
│   └── ProxyEngine.php        # cURL reverse proxy forwarding engine
├── config.example.php         # Default configuration template
├── config.php                 # Active local configuration
├── index.php                  # Single entry point
├── .htaccess                  # Apache rewrite & headers rule
├── nginx.conf.example         # Nginx server block configuration
├── composer.json              # Package definition
├── AGENTS.md                  # Agent guidelines (this file)
└── README.md                  # Comprehensive documentation
```

### 5. Verification Commands
When modifying code, always test syntax validity and proxy functionality:
- Run automated test suite: `php tests/run.php`
- Validate PHP syntax: `php -l <filename>`
- Run built-in test server: `php -S 127.0.0.1:8080 index.php`

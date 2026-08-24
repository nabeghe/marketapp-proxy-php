# Configuration

The proxy can be configured via the `config.php` file or via **Environment Variables**.

---

## Configuration File (`config.php`)

```php
<?php

declare(strict_types=1);

return [
    /**
     * Target base URL of MarketApp API.
     * Default: https://api.marketapp.org
     */
    'target_url' => 'https://api.marketapp.org',

    /**
     * Connection timeout in seconds.
     * Default: 15
     */
    'connect_timeout' => 15,

    /**
     * Maximum request execution timeout in seconds.
     * Default: 60
     */
    'timeout' => 60,

    /**
     * Enable SSL certificate verification for HTTPS targets.
     * Default: true
     */
    'ssl_verify' => true,

    /**
     * Enable debug mode (adds proxy debugging headers and exposes detailed errors).
     * Default: false
     */
    'debug' => false,

    /**
     * Forward client IP address via X-Forwarded-* headers.
     * Default: true
     */
    'forward_client_ip' => true,

    /**
     * Enable CORS (Cross-Origin Resource Sharing) support.
     * Default: true
     */
    'enable_cors' => true,

    /**
     * Allowed origin for CORS headers.
     * Default: '*'
     */
    'cors_origin' => '*',

    /**
     * Optional custom headers to inject into requests sent to MarketApp API.
     */
    'custom_headers' => [
        // 'X-Partner-Id' => 'your-partner-id',
    ],
];
```

---

## Environment Variables

Every setting can be overridden at runtime using environment variables:

| Setting | Environment Variable | Default Value | Description |
| :--- | :--- | :--- | :--- |
| Target URL | `MARKETAPP_TARGET_URL` | `https://api.marketapp.org` | Upstream MarketApp API base URL |
| Timeout | `MARKETAPP_TIMEOUT` | `60` | Execution timeout in seconds |
| Connect Timeout | `MARKETAPP_CONNECT_TIMEOUT` | `15` | TCP connection timeout in seconds |
| SSL Verify | `MARKETAPP_SSL_VERIFY` | `true` | Verify upstream SSL certificate |
| Debug Mode | `MARKETAPP_DEBUG` | `false` | Expose execution metrics & debug headers |
| Enable CORS | `MARKETAPP_ENABLE_CORS` | `true` | Handle preflight and response CORS headers |
| CORS Origin | `MARKETAPP_CORS_ORIGIN` | `*` | Allowed CORS origin value |

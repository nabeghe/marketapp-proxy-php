# CORS & Security

## Cross-Origin Resource Sharing (CORS)

By default, the proxy comes with built-in CORS handling enabled (`enable_cors => true`).

### How CORS is Handled

1. **Preflight Requests (`OPTIONS`):**
   - Automatically answered with `204 No Content`.
   - Returns allowed headers and methods mirroring the client's request.
   - Sets `Access-Control-Max-Age: 86400` to cache preflight decisions in client browsers.

2. **Standard API Requests:**
   - Injects `Access-Control-Allow-Origin: *` (or your configured domain in `cors_origin`).
   - Injects `Access-Control-Allow-Credentials: true`.

---

## Restricting Allowed Origins

In production environments where you only want your specific web app to communicate through the proxy, set `cors_origin` in `config.php`:

```php
return [
    'enable_cors' => true,
    'cors_origin' => 'https://app.yourdomain.com',
];
```

---

## Security Best Practices

1. **Keep `.htaccess` Active:** Prevents direct access to `.env`, `composer.json`, `AGENTS.md`, and hidden directories.
2. **Turn Off Debug in Production:** Set `'debug' => false` in production to prevent leaking internal execution timings and raw network stack traces.
3. **Enforce SSL Verification:** Keep `'ssl_verify' => true` to protect against Man-in-the-Middle (MitM) attacks.

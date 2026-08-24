# Authentication & Headers

## Forwarding the Authorization Header

MarketApp API relies on standard Bearer Token or API Key authentication via the `Authorization` header.

The proxy guarantees 100% transparent forwarding of authentication credentials across different server environments.

---

## Server SAPI Compatibility

In Apache environments, the `Authorization` header is often stripped by default before reaching PHP. The proxy handles this automatically via:

1. `.htaccess` configuration:
   ```apache
   <IfModule mod_auth.c>
       CGIPassAuth On
   </IfModule>
   RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]
   ```

2. PHP normalizer fallback in `Request.php`:
   - Checks `getallheaders()`
   - Checks `$_SERVER['HTTP_AUTHORIZATION']`
   - Checks `$_SERVER['REDIRECT_HTTP_AUTHORIZATION']`
   - Checks Basic auth `$_SERVER['PHP_AUTH_USER']` & `$_SERVER['PHP_AUTH_PW']`

---

## Injecting Custom Headers

If you wish to inject server-side credentials or partner tokens to all outgoing upstream requests, configure `custom_headers` in `config.php`:

```php
'custom_headers' => [
    'X-Partner-Id' => 'my-partner-identifier',
    'X-App-Client' => 'MarketBaz-Web',
],
```

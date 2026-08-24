# Troubleshooting & FAQ

## Frequently Encountered Issues

### 1. Missing Authorization Header
- **Symptom:** Upstream MarketApp returns `401 Unauthorized` even though client sends `Authorization: Bearer ...`.
- **Cause:** Apache FastCGI / PHP-FPM strips the `Authorization` header by default.
- **Solution:** Ensure `.htaccess` is uploaded and contains:
  ```apache
  <IfModule mod_auth.c>
      CGIPassAuth On
  </IfModule>
  RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]
  ```

---

### 2. 404 Not Found on API Routes
- **Symptom:** Calling `/v1/orders` returns an Apache/Nginx 404 page rather than being forwarded.
- **Cause:** URL rewriting is not functioning.
- **Solution:**
  - On Apache: Ensure `mod_rewrite` is enabled (`sudo a2enmod rewrite && sudo systemctl restart apache2`) and `AllowOverride All` is set in your Apache VirtualHost.
  - On Nginx: Ensure `try_files $uri $uri/ /index.php?$query_string;` is present in your `location /` block.

---

### 3. 502 Bad Gateway / 504 Gateway Timeout
- **Symptom:** Proxy returns JSON with `"error": "GatewayError"`.
- **Cause:** The server cannot reach `https://api.marketapp.org` due to DNS failure, firewall, or connection timeout.
- **Solution:**
  - Verify outbound internet connectivity and DNS resolution on your server (`curl -I https://api.marketapp.org`).
  - Increase `'timeout'` and `'connect_timeout'` in `config.php`.
  - Enable `'debug' => true` temporarily in `config.php` to see detailed cURL error messages.

---

### 4. SSL Certificate Verification Failure in Local Development
- **Symptom:** cURL error `SSL certificate problem: unable to get local issuer certificate`.
- **Cause:** Local PHP installation lacks an updated CA bundle.
- **Solution:** Set `'ssl_verify' => false` in `config.php` during local testing (keep `true` in production).

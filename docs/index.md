# MarketApp API Reverse Proxy

Welcome to the official documentation for **MarketApp API Reverse Proxy (PHP)**.

This project is a high-performance, lightweight, and zero-dependency PHP reverse proxy designed to seamlessly forward all incoming HTTP requests to the **MarketApp API** (`https://api.marketapp.org/`) and transparently return the upstream response to your clients.

---

## 🌟 Key Highlights

- **Broad PHP Compatibility:** Works seamlessly across **PHP 7.4 through PHP 8.5** without deprecation notices.
- **Zero Mandatory Dependencies:** Runs out-of-the-box on standard shared hosting (cPanel, DirectAdmin, Plesk) without needing Composer.
- **Full HTTP Semantics:** Transparently forwards all HTTP verbs (`GET`, `POST`, `PUT`, `DELETE`, `PATCH`, `OPTIONS`, `HEAD`), query parameters, headers, and payloads (JSON, Multipart, Form, Binary).
- **Subfolder Friendly:** Can be deployed in the root directory or any nested subdirectory (e.g. `https://your-domain.com/marketapp/`).
- **Built-in CORS Support:** Handles preflight `OPTIONS` requests and origin headers automatically.
- **Automated Test Suite:** Includes an integrated standalone test suite with 100% pass rate.

---

## 🚀 Quick Navigation

<div class="grid cards" markdown>

-   :material-rocket-launch:{ .lg .middle } __[Quick Start Guide](guide/quick-start.md)__

    ---

    Get your proxy instance up and running in less than 2 minutes.

-   :material-tune:{ .lg .middle } __[Configuration](guide/configuration.md)__

    ---

    Learn how to customize target URLs, timeouts, SSL verification, and CORS.

-   :material-server:{ .lg .middle } __[Deployment Guides](deployment/overview.md)__

    ---

    Step-by-step guides for cPanel, Subfolders, Nginx, Apache, and Docker.

-   :material-link-variant:{ .lg .middle } __[Client Integration](guide/client-integration.md)__

    ---

    How to switch your API client's `base_url` to your proxy URL.

</div>

---

## 📖 Upstream API Documentation

This proxy is designed to act as an intermediary for MarketApp API. For upstream endpoint specifications and schemas, refer to the official [MarketApp API Documentation](https://api.marketapp.org/docs/).

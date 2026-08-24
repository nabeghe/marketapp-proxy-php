# Deployment Overview

The MarketApp Reverse Proxy can be hosted in various environments ranging from simple cPanel shared hosting to containerized Docker clusters.

---

## Deployment Options

| Environment | Recommended Guide | Complexity | Notes |
| :--- | :--- | :--- | :--- |
| **cPanel / DirectAdmin** | [cPanel Guide](cpanel.md) | Very Easy | Just upload files and verify `.htaccess` |
| **Nested Subfolder** | [Subfolder Guide](subfolder.md) | Very Easy | Automatic path stripping |
| **Linux VPS (Nginx + PHP-FPM)** | [Nginx Guide](nginx.md) | Medium | High concurrency & production performance |
| **Docker Container** | [Docker Guide](docker.md) | Easy | Reproducible isolated containers |

# Nginx Deployment Guide

For high-performance, high-concurrency production deployments on Linux servers (Ubuntu/Debian, CentOS/AlmaLinux), Nginx paired with PHP-FPM is recommended.

---

## Server Block Configuration

Create or update your server configuration file (e.g. `/etc/nginx/sites-available/marketapp-proxy.conf`):

```nginx
server {
    listen 80;
    listen [::]:80;
    server_name proxy.yourdomain.com;
    root /var/www/marketapp-proxy;
    index index.php;

    charset utf-8;

    # Maximum payload size for JSON payloads and uploads
    client_max_body_size 64M;

    # Route all requests to index.php
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    # Block access to hidden files and metadata
    location ~ /\.(?!well-known).* {
        deny all;
    }

    location ~ /(composer\.(json|lock)|AGENTS(\.md)?)$ {
        deny all;
    }

    # Pass PHP scripts to PHP-FPM
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock; # Adjust to your PHP socket or 127.0.0.1:9000
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;

        # Ensure Authorization header reaches PHP
        fastcgi_param HTTP_AUTHORIZATION $http_authorization;

        # Timeouts for long requests
        fastcgi_read_timeout 120;
        fastcgi_send_timeout 120;
    }
}
```

---

## Reloading Nginx

```bash
# Test configuration
sudo nginx -t

# Reload Nginx
sudo systemctl reload nginx
```

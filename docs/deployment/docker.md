# Docker & Container Deployment

Deploying with Docker allows containerized, reproducible deployment across any cloud provider.

---

## 1. `Dockerfile` Example

```dockerfile
FROM php:8.2-apache

# Enable mod_rewrite for Apache
RUN a2enmod rewrite

# Copy project files
COPY . /var/www/html/

# Set proper permissions
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80
```

---

## 2. `docker-compose.yml` Example

```yaml
version: '3.8'

services:
  marketapp-proxy:
    build: .
    container_name: marketapp-proxy
    ports:
      - "8080:80"
    environment:
      - MARKETAPP_TARGET_URL=https://api.marketapp.org
      - MARKETAPP_TIMEOUT=60
      - MARKETAPP_SSL_VERIFY=true
      - MARKETAPP_ENABLE_CORS=true
      - MARKETAPP_DEBUG=false
    restart: unless-stopped
```

---

## 3. Running the Container

```bash
docker compose up -d
```

The proxy will be accessible at `http://localhost:8080/`.

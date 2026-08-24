# Deploying on cPanel & Shared Hosting

Deploying the proxy on shared hosting (cPanel, DirectAdmin, Plesk) requires zero CLI access and no Composer installation.

---

## Step-by-Step Instructions

1. **Download the Repository:**
   - Download the project zip archive or clone the repository.

2. **Upload Files via File Manager:**
   - Open cPanel **File Manager**.
   - Navigate to your document root (e.g. `public_html` or a subdomain folder).
   - Upload and extract the project files.

3. **Verify Hidden Files:**
   - In cPanel File Manager Settings, enable **Show Hidden Files (dotfiles)**.
   - Confirm that `.htaccess` is present in the folder.

4. **Verify PHP Version:**
   - Navigate to **MultiPHP Manager** or **Select PHP Version** in cPanel.
   - Select **PHP 7.4, 8.0, 8.1, 8.2, 8.3, 8.4, or 8.5**.
   - Ensure the `curl` and `json` extensions are enabled (enabled by default on all hosting providers).

5. **Test Your Installation:**
   - Visit `https://your-domain.com/docs/` in your browser.
   - You should see the MarketApp API Swagger documentation page!

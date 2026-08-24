# MarketApp API Reverse Proxy (PHP)

اسکریپت واسط (Reverse Proxy) سریع، سبک و امن برای ارجاع و بازگردانی مستقیم درخواست‌ها به API مارکت‌اپ (`https://api.marketapp.org/`).

این پروژه تمامی درخواست‌های ورودی (متدها، هدرها، بدنه، پارامترهای کوئری، فایل‌ها و استریم‌ها) را دریافت کرده، به سرور MarketApp فوروارد می‌کند و پاسخ دریافتی را بدون تغییر در وضعیت HTTP، هدرها و محتوا به کلاینت بازمی‌گرداند.

---

## ویژگی‌ها (Features)

- ⚡ **سازگاری کامل با نسخه‌های مختلف PHP:** سازگار با PHP 7.4 تا PHP 8.5.
- 🚀 **بدون نیاز اجباری به کامپوزر (Zero-Dependency):** قابلیت اجرا به صورت Standalone تنها با آپلود فایل‌ها در هاست اشتراکی (cPanel, DirectAdmin, Plesk).
- 🔄 **پشتیبانی کامل از تمامی متدهای HTTP:** `GET`, `POST`, `PUT`, `DELETE`, `PATCH`, `OPTIONS`, `HEAD`.
- 📦 **پشتیبانی از انواع فرمت‌های داده:** `JSON`, `multipart/form-data`, `application/x-www-form-urlencoded`, `Raw Body / Binary Stream`.
- 🔐 **مدیریت کامل هدرها و احراز هویت:** پشتیبانی از هدر `Authorization` (Bearer Token, Basic Auth)، هدرهای اختصاصی و فیلتر خودکار هدرهای Hop-by-Hop.
- 🌐 **مدیریت خودکار CORS:** پاسخ‌دهی استاندارد به درخواست‌های Preflight (`OPTIONS`) و هدرهای `Access-Control-*`.
- 🛠️ **پیکربندی وب‌سرورهای محبوب:** شامل فایل `.htaccess` برای Apache / LiteSpeed و نمونه کانفیگ برای Nginx.
- 🔍 **قابلیت Debug Mode:** مشاهده جزئیات زمان اجرا، آدرس مقصد و خطاهای شبکه در هدرها و پاسخ‌ها.

---

## ساختار فایل‌ها (Project Structure)

```
marketapp-proxy-php/
├── .agents/
│   └── skills/
│       └── marketapp-proxy/
│           └── SKILL.md          # دستورالعمل‌های اختصاصی ایجنت
├── src/
│   ├── Config.php                # مدیریت تنظیمات
│   ├── Http/
│   │   ├── Request.php           # دریافت و پردازش درخواست کلاینت
│   │   └── Response.php          # ساخت و ارسال پاسخ به کلاینت
│   └── ProxyEngine.php           # موتور فوروارد درخواست‌ها با cURL
├── config.example.php            # نمونه فایل تنظیمات
├── config.php                    # فایل تنظیمات فعال
├── index.php                     # نقطه ورود اصلی پروژه
├── .htaccess                     # کانفیگ ریرایت و احراز هویت برای آپاچی
├── nginx.conf.example            # نمونه کانفیگ برای Nginx
├── composer.json                 # تنظیمات Composer و PSR-4
├── AGENTS.md                     # راهنمای ایجنت‌های هوش مصنوعی
└── README.md                     # راهنمای کامل پروژه
```

---

## پیش‌نیازها (Requirements)

- **PHP:** نسخه 7.4 یا بالاتر (شامل 8.0, 8.1, 8.2, 8.3, 8.4, 8.5)
- **PHP Extensions:** `curl`, `json`
- **Web Server:** Apache (با `mod_rewrite`), LiteSpeed, Nginx یا وب‌سرور داخلی PHP

---

## نحوه نصب و راه‌اندازی (Installation & Setup)

### روش ۱: هاست اشتراکی (cPanel / DirectAdmin / Plesk)
1. محتویات این مخزن را در ریشه هاست یا پوشه ساب‌دامین مورد نظر خود آپلود کنید.
2. مطمئن شوید فایل `.htaccess` آپلود شده و مخفی نمانده است.
3. در صورت نیاز، تنظیمات فایل `config.php` را ویرایش کنید.
4. اکنون تمامی اندپوینت‌های شما به عنوان پروکسی مارکت‌اپ عمل می‌کنند.

### روش ۲: سرور لینوکس با Nginx
1. فایل‌های پروژه را در دایرکتوری مورد نظر (مثلاً `/var/www/marketapp-proxy`) قرار دهید.
2. فایل `nginx.conf.example` را بررسی و تنظیمات `server` آن را به کانفیگ Nginx خود اضافه کنید:
   ```nginx
   location / {
       try_files $uri $uri/ /index.php?$query_string;
   }
   ```
3. سرویس Nginx و PHP-FPM را ریلود کنید:
   ```bash
   sudo systemctl reload nginx
   ```

### روش ۳: اجرای لوکال با سرور داخلی PHP (Local Development)
برای تست سریع بدون نیاز به وب‌سرور خارجی:
```bash
php -S 127.0.0.1:8080 index.php
```

---

## پیکربندی (Configuration)

تنظیمات در فایل `config.php` قرار دارند و همچنین از طریق Environment Variables نیز قابل بازنویسی هستند:

```php
<?php

return [
    // آدرس مقصد API مارکت‌اپ
    'target_url' => 'https://api.marketapp.org',

    // تایم‌اوت برقراری اتصال (ثانیه)
    'connect_timeout' => 15,

    // حداکثر زمان اجرای درخواست (ثانیه)
    'timeout' => 60,

    // بررسی گواهی SSL سرور مقصد
    'ssl_verify' => true,

    // فعال‌سازی حالت دیباگ و مشاهده هدرهای پروکسی
    'debug' => false,

    // ارسال IP واقعی کاربر با هدرهای X-Forwarded-*
    'forward_client_ip' => true,

    // مدیریت خودکار CORS
    'enable_cors' => true,
    'cors_origin' => '*',

    // هدرهای سفارشی اختیاری
    'custom_headers' => [
        // 'X-Custom-Token' => '...',
    ],
];
```

### متغیرهای محیطی پشتیبانی‌شده (Environment Variables)
- `MARKETAPP_TARGET_URL`
- `MARKETAPP_TIMEOUT`
- `MARKETAPP_CONNECT_TIMEOUT`
- `MARKETAPP_SSL_VERIFY` (`true` / `false`)
- `MARKETAPP_DEBUG` (`true` / `false`)
- `MARKETAPP_ENABLE_CORS` (`true` / `false`)
- `MARKETAPP_CORS_ORIGIN`

---

## نحوه استفاده و تست (Usage & Testing)

پس از بالا آمدن سرور پروکسی، تمامی اندپوینت‌های مستندات MarketApp (`https://api.marketapp.org/docs/`) دقیقاً با همان مسیرها در دامنه شما در دسترس خواهند بود:

### ۱. تست مشاهده مستندات (Docs)
```bash
curl -i http://your-proxy-domain.com/docs/
```

### ۲. تست ارسال درخواست GET با توکن احراز هویت
```bash
curl -i -X GET http://your-proxy-domain.com/api/v1/user/profile \
  -H "Authorization: Bearer YOUR_API_TOKEN" \
  -H "Accept: application/json"
```

### ۳. تست ارسال درخواست POST همراه با بدنه JSON
```bash
curl -i -X POST http://your-proxy-domain.com/api/v1/orders \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_API_TOKEN" \
  -d '{
    "product_id": 123,
    "quantity": 2
  }'
```

## اجرای تست‌های خودکار (Automated Testing)

پروژه دارای یک مجموعه کامل تست یونیت بدون هیچ‌گونه وابستگی خارجی است:

```bash
# اجرای مستقیم تست‌ها با PHP
php tests/run.php

# یا از طریق کامپوزر
composer test
```

---

## عیب‌یابی (Troubleshooting)

| مشکل | علت احتمالی | راه‌حل |
| :--- | :--- | :--- |
| **هدر Authorization دریافت نمی‌شود** | آپاچی هدر را به PHP پاس نمی‌دهد | مطمئن شوید خطوط `CGIPassAuth On` و `RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]` در `.htaccess` وجود دارند. |
| **خطای 404 برای اندپوینت‌ها** | ماژول `mod_rewrite` در آپاچی فعال نیست | دستور `a2enmod rewrite` را در سرور اجرا کرده و آپاچی را ری‌استارت کنید. |
| **خطای 502 / 504** | عدم دسترسی به سرور مارکت‌اپ یا تایم‌اوت | بررسی اتصال اینترنت سرور، DNS و تنظیم `timeout` در `config.php`. |
| **خطای اعتبارسنجی SSL در لوکال** | نبود CA Bundle معتبر در PHP سیستم محلی | مقدار `'ssl_verify' => false` را در محیط توسعه قرار دهید (در محیط پروداکشن حتماً `true` باشد). |

---

## لایسنس (License)
این پروژه تحت مجوز [MIT](LICENSE) منتشر شده است.

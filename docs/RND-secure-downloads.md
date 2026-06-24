# RND — Secure Downloads Module
**Plugin:** woo-digital-downloads
**Module:** Secure Downloads
**Phase:** 1 (MVP)
**Standalone:** Yes — works without any other WDD module

---

## Overview

WooCommerce's built-in downloadable file system exposes direct file URLs after purchase. These URLs can be shared, scraped, or accessed without authorization. The Secure Downloads module replaces WooCommerce's download URLs with signed, expiring, count-limited tokens. Files are served through PHP — no direct URL is ever exposed to the customer.

### Problem It Solves
- WooCommerce download URLs are guessable or shareable
- No IP logging or geo-blocking on native WooCommerce downloads
- No download attempt counting beyond a basic WooCommerce setting
- No ability to block downloads by country

---

## Standalone Usage

Enable this module alone (without Licensing or Plugin Updates) to:
- Protect any WooCommerce downloadable product (PDFs, videos, templates, assets)
- Track every download event with IP address and timestamp
- Set per-order expiry windows and maximum download counts
- Optionally block downloads from specific countries

No license key system is required. The module works with standard WooCommerce downloadable products.

---

## Architecture

### Classes

| Class | File | Responsibility |
|---|---|---|
| `TokenManager` | `includes/Downloads/TokenManager.php` | Create, validate, and expire download tokens |
| `DownloadDispatcher` | `includes/Downloads/DownloadDispatcher.php` | Rewrite URL, validate token, stream file |
| `DownloadLogger` | `includes/Downloads/DownloadLogger.php` | Write IP, user-agent, country, timestamp log |
| `GeoBlocker` | `includes/Downloads/GeoBlocker.php` | Country-level allow/block list |

### How the Token Flow Works

```
Order Completed
    │
    └── TokenManager::create_for_order_item()
            ├── Reads: wdd_download_expiry_seconds (default: DAY_IN_SECONDS)
            ├── Reads: wdd_download_max_count (default: 3)
            ├── Inserts row in wp_wdd_downloads
            └── Returns: 64-char hex token

Customer clicks Download link → /wdd-download/{token}
    │
    └── DownloadDispatcher::maybe_dispatch()
            ├── TokenManager::validate(token)
            │       ├── Check: expires_at > NOW()
            │       └── Check: download_count < max_downloads
            ├── GeoBlocker::check(ip_address) [if enabled]
            ├── DownloadLogger::record(token_id, ip, user_agent)
            ├── TokenManager::record_download(token_id, ip, user_agent)
            └── stream_file(file_path)
                    ├── ob_end_clean()
                    ├── header: Content-Type, Content-Disposition, Content-Length
                    └── readfile(file_path)
```

### Rewrite Rule

```php
// Registered in DownloadDispatcher::__construct()
add_rewrite_rule(
    '^wdd-download/([a-f0-9]{64})/?$',
    'index.php?wdd_download_token=$matches[1]',
    'top'
);
```

---

## Database Tables

### wp_wdd_downloads — Token Registry
```sql
CREATE TABLE wp_wdd_downloads (
    id              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    order_id        BIGINT UNSIGNED NOT NULL,
    user_id         BIGINT UNSIGNED NOT NULL,
    product_id      BIGINT UNSIGNED NOT NULL,
    file_id         BIGINT UNSIGNED NOT NULL,
    token           VARCHAR(128) NOT NULL UNIQUE,
    download_count  INT UNSIGNED DEFAULT 0,
    max_downloads   INT UNSIGNED DEFAULT 3,
    expires_at      DATETIME NOT NULL,
    ip_address      VARCHAR(45),      -- IP at token creation
    country_code    VARCHAR(2),
    created_at      DATETIME NOT NULL,
    PRIMARY KEY (id),
    KEY idx_token (token),
    KEY idx_user_id (user_id),
    KEY idx_order_id (order_id)
);
```

### wp_wdd_download_logs — Per-Event Audit Log
```sql
CREATE TABLE wp_wdd_download_logs (
    id              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    download_id     BIGINT UNSIGNED NOT NULL,  -- FK: wp_wdd_downloads.id
    ip_address      VARCHAR(45),
    user_agent      TEXT,
    country_code    VARCHAR(2),
    downloaded_at   DATETIME NOT NULL,
    PRIMARY KEY (id),
    KEY idx_download_id (download_id),
    KEY idx_downloaded_at (downloaded_at)
);
```

---

## REST API Endpoints

| Method | Endpoint | Auth | Description |
|---|---|---|---|
| `GET` | `/wdd/v1/download/{token}` | Token (public) | Validate token and stream file |
| `POST` | `/wdd/v1/download/generate` | manage_woocommerce | Generate a new token manually (admin) |

Note: The primary download URL is a WordPress rewrite rule (`/wdd-download/{token}`), not a REST endpoint, to allow streaming with full header control.

---

## Configuration Options

All stored in `wp_options`:

| Option Key | Default | Description |
|---|---|---|
| `wdd_download_expiry_seconds` | `86400` (1 day) | How long a token is valid after order completion |
| `wdd_download_max_count` | `3` | Maximum number of times a token can be used |
| `wdd_geo_block_enabled` | `false` | Enable country-level blocking |
| `wdd_geo_block_mode` | `block` | `block` = deny listed countries; `allow` = only allow listed |
| `wdd_geo_blocked_countries` | `[]` | Array of 2-letter ISO country codes |
| `wdd_geo_detection_api` | `ip-api` | `ip-api` (free) or `maxmind` |
| `wdd_maxmind_license_key` | `''` | MaxMind license key (if using GeoLite2) |

---

## WooCommerce Integration

```php
// Replace WooCommerce native download URL with WDD token URL
add_filter(
    'woocommerce_downloadable_file_download_url',
    [ DownloadDispatcher::class, 'secure_download_url' ],
    10, 2
);

// Generate tokens when order completes
add_action(
    'woocommerce_order_status_completed',
    [ OrderHandler::class, 'on_order_completed' ]
);
```

---

## Token Security Properties

| Property | Implementation |
|---|---|
| Token format | 64 hex chars (`bin2hex(random_bytes(32))`) |
| Uniqueness | `UNIQUE` DB constraint; re-generates on collision |
| Expiry | Hard-coded `expires_at` in DB; checked server-side every request |
| Count limit | `download_count < max_downloads` enforced server-side |
| Direct URL exposure | Never — file path resolved server-side from product meta |
| File path resolution | `_wdd_file_path` product meta, then WooCommerce downloadable files |

---

## Geo-Blocking

GeoBlocker checks the requesting IP against the configured country list on every download attempt. Detection options:

**ip-api.com (free, no key):**
- `GET http://ip-api.com/json/{ip}?fields=countryCode`
- Rate limit: 45 requests/min from a single IP (sufficient for most stores)
- Results cached in WordPress transients for 24 hours per IP

**MaxMind GeoLite2 (free with registration):**
- Local database file — no external API call
- Requires periodic database update (GeoLite2-Country.mmdb)
- `composer require maxmind-db/reader` adds the PHP reader

---

## My Account Integration

The Downloads tab (`/my-account/wdd-downloads/`) shows:
- Product name
- Downloads used / max downloads
- Expiry date
- Download button (active token) or "Expired" badge (invalid token)

---

## Developer Hooks

```php
// Fired before token is created
apply_filters( 'wdd_download_expiry_seconds', $seconds, $order_id, $product_id );
apply_filters( 'wdd_download_max_count', $count, $order_id, $product_id );

// Fired after token is created
do_action( 'wdd_download_token_created', $token_id, $order_id, $product_id );

// Fired before geo-block check (return true to allow regardless)
apply_filters( 'wdd_geo_block_bypass', false, $ip, $country_code, $token_id );

// Fired when file is successfully downloaded
do_action( 'wdd_file_downloaded', $token_id, $ip, $country_code, $user_agent );

// Fired when download is rejected (expired/exhausted)
do_action( 'wdd_download_rejected', $token_id, $reason, $ip );
```

---

## Competitive Context

| | WooCommerce Native | EDD Core | woo-digital-downloads |
|---|---|---|---|
| Signed expiring tokens | Partial (nonce-based) | Yes | **Yes (64-char hex)** |
| IP logging per download | No | Yes | **Yes** |
| Geo-blocking | No | No | **Yes** |
| Country detection | No | No | **Yes (ip-api/MaxMind)** |
| Count limit enforcement | WC setting only | Yes | **Yes (server-side)** |
| Direct URL exposure | Yes (if WC setting off) | No | **Never** |
| Customer download portal | Basic | Yes | **Yes (My Account tab)** |
| Works with existing WC products | Native | No (EDD products) | **Yes** |

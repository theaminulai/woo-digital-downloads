# RND — License Key Management Module
**Plugin:** woo-digital-downloads
**Module:** Licensing
**Phase:** 1 (MVP)
**Standalone:** Yes — works with any WooCommerce product

---

## Overview

The Licensing module generates cryptographically random license keys on order completion, tracks which domains have activated each key, enforces activation limits, and provides a REST API for your plugin to validate licenses at runtime. It includes a remote kill-switch for instant revocation and automatic staging/localhost exemption so developer environments don't consume activation slots.

### Problem It Solves
- WooCommerce has no concept of software licenses
- EDD's Software Licensing costs $199/yr
- WC Serial Numbers requires pre-generating keys and importing them manually
- No existing free WooCommerce solution tracks domain-level activations with staging exemption

---

## Standalone Usage

Enable this module alone to:
- Generate license keys for any WooCommerce product
- Track domain activations via REST API calls from your plugin
- Enforce per-site activation limits (e.g., "use on up to 3 sites")
- Exempt staging and local environments from activation limits
- Revoke licenses instantly via admin or REST API

The Plugin Updates module is optional — you can issue license keys without providing update delivery.

---

## Architecture

### Classes

| Class | File | Responsibility |
|---|---|---|
| `LicenseGenerator` | `includes/Licensing/LicenseGenerator.php` | Create keys, store in DB |
| `LicenseActivator` | `includes/Licensing/LicenseActivator.php` | Activate/deactivate domains, check limits |
| `LicenseValidator` | `includes/Licensing/LicenseValidator.php` | Validate key + return plan features |
| `LicenseExpiry` | `includes/Licensing/LicenseExpiry.php` | Cron-based expiry enforcement |
| `LicenseRevoke` | `includes/Licensing/LicenseRevoke.php` | Instant kill-switch |

### License Key Format

```
XXXXXXXX-XXXXXXXX-XXXXXXXX-XXXXXXXX-XXXXXXXX
```

40 hex chars split into 5 groups of 8, joined by hyphens.

```php
// LicenseGenerator::generate_key()
$raw = strtoupper( bin2hex( random_bytes( 20 ) ) ); // 40 hex chars
return implode( '-', str_split( $raw, 8 ) );
// Example: A1B2C3D4-E5F6A7B8-C9D0E1F2-A3B4C5D6-E7F8A9B0
```

### Activation Flow

```
Customer's plugin calls: POST /wp-json/wdd/v1/license/activate
    Body: { license_key, domain, environment }
    │
    └── LicenseActivator::activate()
            ├── get_by_key(license_key)           → 404 if not found
            ├── check status === 'active'          → 403 if revoked/expired/suspended
            ├── is_staging_or_local(domain)        → skip limit check if true
            ├── check activated_count < limit      → 403 if exceeded
            ├── INSERT wp_wdd_license_activations  → record domain + IP
            ├── UPDATE wp_wdd_licenses activated_count++
            └── return { success: true, data: { expiry, plan_type, activations_remaining } }
```

---

## Database Tables

### wp_wdd_licenses — License Registry
```sql
CREATE TABLE wp_wdd_licenses (
    id               BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    order_id         BIGINT UNSIGNED NOT NULL,
    user_id          BIGINT UNSIGNED NOT NULL,
    product_id       BIGINT UNSIGNED NOT NULL,
    license_key      VARCHAR(64) NOT NULL UNIQUE,
    plan_type        ENUM('single','multi','unlimited','lifetime') DEFAULT 'single',
    status           ENUM('active','expired','revoked','suspended') DEFAULT 'active',
    activation_limit INT UNSIGNED DEFAULT 1,
    activated_count  INT UNSIGNED DEFAULT 0,
    expires_at       DATETIME NULL,   -- NULL = lifetime
    created_at       DATETIME NOT NULL,
    updated_at       DATETIME NOT NULL,
    PRIMARY KEY (id),
    KEY idx_license_key (license_key),
    KEY idx_user_id (user_id),
    KEY idx_order_id (order_id),
    KEY idx_status (status),
    KEY idx_expires_at (expires_at)
);
```

### wp_wdd_license_activations — Domain Activation Records
```sql
CREATE TABLE wp_wdd_license_activations (
    id           BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    license_id   BIGINT UNSIGNED NOT NULL,
    domain       VARCHAR(255) NOT NULL,
    ip_address   VARCHAR(45),
    environment  ENUM('production','staging','local') DEFAULT 'production',
    activated_at DATETIME NOT NULL,
    last_check   DATETIME,            -- updated on each /license/check call
    PRIMARY KEY (id),
    KEY idx_license_id (license_id),
    KEY idx_domain (domain),
    UNIQUE KEY idx_license_domain (license_id, domain)
);
```

---

## REST API Endpoints

| Method | Endpoint | Auth | Description |
|---|---|---|---|
| `POST` | `/wdd/v1/license/activate` | License key | Activate key on a domain |
| `POST` | `/wdd/v1/license/deactivate` | License key | Remove a domain activation |
| `GET` | `/wdd/v1/license/check` | License key | Validate key, return plan info |
| `POST` | `/wdd/v1/license/revoke` | manage_woocommerce | Instantly revoke a license (admin) |

### Request / Response Examples

**Activate:**
```json
POST /wp-json/wdd/v1/license/activate
{
  "license_key": "A1B2C3D4-E5F6A7B8-C9D0E1F2-A3B4C5D6-E7F8A9B0",
  "domain": "example.com",
  "environment": "production"
}

Response 200:
{
  "success": true,
  "message": "License activated successfully.",
  "data": {
    "expires_at": "2027-06-24 00:00:00",
    "plan_type": "single",
    "activation_limit": 1,
    "activations_remaining": 0
  }
}
```

**Check:**
```json
GET /wp-json/wdd/v1/license/check?license_key=...&domain=example.com

Response 200:
{
  "success": true,
  "data": {
    "status": "active",
    "plan_type": "multi",
    "expires_at": "2027-06-24 00:00:00",
    "is_lifetime": false,
    "activated_count": 2,
    "activation_limit": 5
  }
}
```

---

## Plan Types

| Plan Type | Activation Limit | Expiry |
|---|---|---|
| `single` | 1 site | Based on `_wdd_license_duration_days` product meta |
| `multi` | Configured per product | Same |
| `unlimited` | No limit enforced | Same |
| `lifetime` | Configured per product | Never expires (expires_at = NULL) |

---

## Staging / Localhost Exemption

Domains matching these patterns are automatically classified as `local` or `staging` environment and **do not count against the activation limit**:

```php
$exempt_patterns = [
    'localhost',
    '127.0.0.1',
    '::1',
    '.local',
    '.test',
    '.staging.',
    'staging.',
    '.dev',
];
```

Environment is still recorded in `wp_wdd_license_activations` with `environment = 'local'` or `'staging'` for audit purposes.

---

## License Expiry (Cron)

`LicenseExpiry` hooks into WP-Cron via Action Scheduler:

```php
// Scheduled daily via Activator::activate()
add_action( 'wdd_check_expired_licenses', [ LicenseExpiry::class, 'run' ] );
```

On each run:
1. `SELECT * FROM wp_wdd_licenses WHERE status = 'active' AND expires_at < NOW()`
2. Bulk-update `status = 'expired'`
3. Fire `do_action( 'wdd_licenses_expired', $expired_ids )` — Subscriptions module listens to this

---

## Remote Kill-Switch (Revocation)

```php
POST /wp-json/wdd/v1/license/revoke
Authorization: WordPress nonce (manage_woocommerce required)
{
  "license_key": "A1B2C3D4-..."
}
```

- Sets `status = 'revoked'` in `wp_wdd_licenses`
- Does NOT delete activation records (audit trail preserved)
- Next call to `/license/check` from the customer's site returns `{ success: false, code: 'license_revoked' }`
- Customer's plugin should respond by deactivating features or showing upgrade notice

---

## Product Meta Fields

Set via WDD meta box on each WooCommerce product:

| Meta Key | Type | Description |
|---|---|---|
| `_wdd_license_type` | string | `single`, `multi`, `unlimited`, `lifetime` |
| `_wdd_activation_limit` | int | Max domains (ignored for `unlimited`) |
| `_wdd_license_duration_days` | int | Days until expiry; empty = lifetime |
| `_wdd_plugin_slug` | string | Plugin slug (used by Update module) |

---

## My Account Integration

The Licenses tab (`/my-account/wdd-licenses/`) shows:
- Product name
- License key (click to copy)
- Status badge (active / expired / revoked / suspended)
- Sites used / limit (or ∞ for unlimited)
- Expiry date (or "Lifetime")

---

## Developer Hooks

```php
// Fired after license key is created
do_action( 'wdd_license_created', $license_id, $order_id, $product_id );

// Before activation — return WP_Error to reject
apply_filters( 'wdd_pre_license_activate', null, $license_key, $domain );

// After activation
do_action( 'wdd_license_activated', $license_id, $domain, $environment );

// After deactivation
do_action( 'wdd_license_deactivated', $license_id, $domain );

// After revocation
do_action( 'wdd_license_revoked', $license_id );

// After expiry batch run
do_action( 'wdd_licenses_expired', $expired_license_ids );

// Filter staging/local exempt patterns
apply_filters( 'wdd_staging_exempt_patterns', $patterns );
```

---

## Competitor Comparison

| Feature | WC Serial Numbers (free) | EDD Software Licensing ($199/yr) | woo-digital-downloads |
|---|---|---|---|
| Key generation | Import required (auto-gen = Pro) | Yes | **Yes (dynamic, no import)** |
| Domain activation tracking | Basic via API | Yes | **Yes, per-domain DB record** |
| Staging/localhost exemption | No | Basic | **Yes, pattern-based** |
| Activation limit enforcement | Yes | Yes | **Yes** |
| Remote kill-switch | No | Yes | **Yes** |
| Lifetime licenses | No | Yes | **Yes** |
| Multi-site plans | Partial | Yes | **Yes** |
| REST API | Yes | Yes | **Yes** |
| WooCommerce native | Yes | No (EDD only) | **Yes** |
| Plugin Update delivery | No | Yes (add-on) | **Yes (separate module)** |
| HPOS compatible | Yes | No | **Yes** |
| Price | Free + Pro | $199/yr | **Included in WDD** |

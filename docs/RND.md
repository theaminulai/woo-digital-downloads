# R&D Document — woo-digital-downloads
**Project:** `woo-digital-downloads`  
**Path:** `D:\wampserver\www\wp-plugin\digital-downloads\wp-content\plugins\woo-digital-downloads`  
**Date:** 2026-06-24  
**Goal:** A WooCommerce extension that sells and manages WordPress Plugins and SaaS Products from a single platform — with licensing, auto-updates, secure downloads, subscription billing, SaaS provisioning, and anti-piracy built in.

---

## 1. Competitive Landscape

### 1.1 Easy Digital Downloads (EDD)
**URL:** https://easydigitaldownloads.com  
**Model:** Self-hosted WordPress plugin. Free core + paid extensions.  
**Trusted by:** 50,000+ businesses, 187 countries, 30M+ orders processed, 13+ years.

**What's free in EDD core:**
- Unlimited products and orders
- Cart & checkout
- Secure download links with expiry & download limits (built-in)
- Basic PayPal/Stripe integration
- Basic discount codes
- Customer account page
- Basic sales reports
- Email notifications

**What costs extra in EDD (paid extensions):**
| Feature | Annual Cost |
|---|---|
| Software Licensing | ~$199/yr |
| Subscriptions / Recurring Billing | ~$209/yr |
| Affiliate Management | ~$199/yr |
| Abandoned Cart Recovery | paid add-on |
| EU VAT Compliance | $40–$80/mo (3rd party) |
| Invoices & Receipts | ~$29/yr |
| Instalment Payments | paid extension |
| Physical Products | paid extension |
| Advanced Reporting | higher plan |
| AWS S3 File Storage | extra cost |

**EDD Pricing (2026):**
- Free core: 3% transaction fee
- $99/yr – 1 site (transaction fee on cheapest plan)
- $199/yr – 1 site, more features
- $299/yr – 2 sites
- $499/yr – 3 sites
- No lifetime plans

**EDD Strengths:**
- Most mature digital-download solution for WordPress (13 years)
- Purpose-built: expiring download links, download attempt limits, IP tracking included free
- Huge ecosystem and documentation
- REST API included
- Used by major WordPress companies (WPForms, MonsterInsights, etc.)

**EDD Weaknesses:**
- Add-on cost stacks up fast ($499–$999/yr for full features on 3 sites)
- No lifetime license option
- No built-in order bumps or post-purchase upsells
- No cart abandonment built-in
- No affiliate system built-in
- UI uses shortcodes, no visual checkout builder
- You manage your own server, backups, and email deliverability

---

### 1.2 SureCart
**URL:** https://surecart.com  
**Model:** Managed eCommerce platform (SaaS backend + WordPress frontend plugin).  
**Ratings:** 4.8/5 WordPress, 4.7/5 G2, 4.3/5 Trustpilot

**Key differentiator:** Managed infrastructure — SureCart handles uptime, auto-scaling, backups, security, and email deliverability. You own the data.

**Pricing:**
| Plan | 1 Store | 5 Stores | Unlimited |
|---|---|---|---|
| Free (Launch) | $0 + 2.9% fee | $0 + 2.9% fee | $0 + 2.9% fee |
| Pro Yearly | $179/yr (renews $199) | $249/yr (renews $299) | $399/yr (renews $499) |
| Pro Lifetime | $599 one-time | $999 one-time | $1,699 one-time |

All features available on all plans (no feature gating). Only difference is transaction fee (free plan) and number of stores.

**SureCart vs EDD — Key Differences:**

| Feature | SureCart | EDD |
|---|---|---|
| Managed infrastructure | Yes — 24/7 monitoring, auto-scaling | No — you manage |
| Checkout builder | Visual drag-and-drop | Shortcodes only |
| Checkout types | Instant, Store, Custom | Store only |
| Affiliate management | Built-in | $199/yr paid plugin |
| Real-time VAT/tax | Built-in | Manual only (~$40–80/mo 3rd party) |
| Order bumps | Yes | No |
| Post-purchase upsells | Yes | No |
| Cart abandonment | Yes | Paid plugin |
| Subscription saver (dunning) | Yes, customizable | Auto only, no customization |
| Name your own price | Yes | No |
| AWS secure file storage | Included | Extra cost |
| Backups | Automatic | You handle |
| Email deliverability | Managed by SureCart | You configure SMTP |
| Physical + digital + SaaS | All supported | Primarily digital |
| Agency suite | Yes (centralized multi-store) | No |
| Lifetime plans | Yes | No |

**SureCart Strengths:**
- Truly all-in-one with zero add-on cost for core features
- Managed infrastructure removes server management overhead
- Modern checkout with visual builder
- Real-time VAT/tax compliance included
- Lifetime pricing option

**SureCart Weaknesses:**
- Backend is SaaS (data lives on SureCart servers even though you own it)
- Less WordPress-native than self-hosted alternatives
- No dedicated WordPress plugin software licensing extension
- Relatively newer, smaller ecosystem

---

### 1.3 FluentCart
**URL:** https://fluentcart.com  
**By:** WPManageNinja (same team as FluentCRM, FluentForms, FluentSupport)  
**Model:** Self-hosted WordPress plugin. Free core + Pro license.  

**Interesting context:** WPManageNinja built FluentCart because they outgrew EDD themselves ("we've used EDD since 2017 — but as we scaled, add-on overload and feature gaps made things complex").

**FluentCart vs EDD — Key Differences:**

| Feature | FluentCart | EDD |
|---|---|---|
| Subscriptions | Built-in | $209/yr paid extension |
| Software licensing | Built-in | $199/yr paid extension |
| Hybrid products (plugin + SaaS) | Native support | Limited |
| Physical products | Supported | Paid extension |
| Invoices | Built-in | $29/yr paid extension |
| Instalment payments | Built-in | Paid extension |
| Custom receipts | Built-in | Paid extension |
| AWS file storage | Built-in | Extra cost |
| Customer portal | Full dashboard | History via shortcode |
| Checkout UI | Modern, streamlined | Basic, less flexible |
| REST API | Yes | Yes |
| Headless support | Yes | Yes |
| Refund management | Built-in | Varies by gateway |
| Product variations | Native | Partial via extension |
| Email marketing | FluentCRM integration | Paid extensions |

**FluentCart Real Cost Comparison vs EDD:**
| Feature | FluentCart | EDD (annual cost) |
|---|---|---|
| License management | Included | $199/yr |
| Subscriptions | Included | $209/yr |
| Email marketing integration | FluentCRM (separate tool) | $77/yr |
| Invoices & receipts | Included | $29/yr |
| Total | One flat fee | $499–$999/yr for 3 sites |

**FluentCart Strengths:**
- Self-hosted (full control, data on your server)
- Built by team who knows the pain of scaling on EDD
- Licensing and subscriptions built-in from day one
- Headless REST API support
- Deep integration with Fluent ecosystem (CRM, Affiliate, Support, Community)
- 16+ Elementor widgets, Gutenberg blocks
- Earlybird lifetime deal starting from $249

**FluentCart Weaknesses:**
- Newer product — smaller ecosystem and user base than EDD
- Fluent ecosystem integrations are best-in-class but can create platform lock-in
- No managed infrastructure (you still host yourself)

---

## 2. Competitor Feature Gap Analysis

Based on all three platforms, here are the features they either miss or charge extra for — which your plugin can provide natively as a WooCommerce extension:

### Features None of Them Handle Well Out-of-the-Box for WooCommerce:
- Licensing + WooCommerce integration (native hooks)
- Automatic plugin update delivery via WooCommerce order
- GitHub/Bitbucket private repo → auto ZIP build → update API
- JWT-based SaaS account provisioning from WooCommerce checkout
- Geo-blocking and IP logging on downloads
- Remote license kill-switch
- Staging/localhost exemption for site-limit licenses
- WooCommerce webhook → SaaS account creation flow

---

## 3. Project Architecture

### 3.1 Core Philosophy
**WooCommerce handles:** Checkout, cart, orders, coupons, payment gateways, customer accounts.  
**woo-digital-downloads handles:** Everything else — licensing, downloads, updates, SaaS, subscriptions, security, and analytics.

### 3.2 Module Structure

```
woo-digital-downloads/
├── woo-digital-downloads.php        # Main plugin file
├── includes/
│   ├── Commerce/                    # WooCommerce integration layer
│   │   ├── OrderHandler.php         # woocommerce_order_status_completed
│   │   ├── ProductTypes.php         # Custom product type registration
│   │   └── WebhookHandler.php       # Outgoing webhooks
│   │
│   ├── Licensing/                   # License system
│   │   ├── LicenseGenerator.php     # Key generation (UUID-based)
│   │   ├── LicenseActivator.php     # Domain activation / deactivation
│   │   ├── LicenseValidator.php     # API endpoint for plugin check
│   │   ├── LicenseExpiry.php        # Cron-based expiry enforcement
│   │   └── LicenseRevoke.php        # Kill-switch / instant revocation
│   │
│   ├── Downloads/                   # Secure file delivery
│   │   ├── TokenManager.php         # Signed, expiring download tokens
│   │   ├── DownloadDispatcher.php   # Serve file via PHP (no direct URL)
│   │   ├── DownloadLogger.php       # IP, user-agent, geo, count log
│   │   └── GeoBlocker.php           # Country-level block list
│   │
│   ├── Updates/                     # WordPress plugin auto-update API
│   │   ├── UpdateServer.php         # /wp-json/wdd/v1/plugin/update
│   │   ├── VersionManager.php       # Upload/manage plugin ZIP versions
│   │   ├── ChangelogManager.php     # Version notes storage
│   │   └── GitHubSync.php           # GitHub webhook → auto ZIP import
│   │
│   ├── Subscriptions/               # Recurring billing
│   │   ├── SubscriptionManager.php  # Create/renew/cancel
│   │   ├── DunningManager.php       # Failed payment retry + emails
│   │   └── PlanUpgrade.php          # Proration + upgrade/downgrade
│   │
│   ├── SaaS/                        # SaaS product provisioning
│   │   ├── AccountProvisioner.php   # Create SaaS user on order complete
│   │   ├── PlanSyncer.php           # Sync WooCommerce plan to SaaS
│   │   ├── ApiKeyManager.php        # Generate/revoke API keys
│   │   └── JwtIssuer.php            # Issue JWT for SaaS login
│   │
│   ├── CustomerDashboard/           # My Account portal
│   │   ├── DashboardController.php  # WooCommerce My Account tabs
│   │   ├── LicenseTab.php           # View, activate, deactivate sites
│   │   ├── DownloadTab.php          # Version history + re-download
│   │   ├── ApiKeyTab.php            # Manage API keys
│   │   └── UsageTab.php             # Usage stats, plan status
│   │
│   ├── Marketing/                   # Emails and growth
│   │   ├── EmailAutomation.php      # Transactional email triggers
│   │   ├── AbandonedCart.php        # Abandoned cart capture + follow-up
│   │   └── AffiliateConnector.php   # Webhook bridge to affiliate tools
│   │
│   ├── Analytics/                   # Reporting
│   │   ├── RevenueReport.php        # MRR, ARR, LTV
│   │   ├── LicenseReport.php        # Active/expired/abused
│   │   └── DownloadReport.php       # Downloads per product/version
│   │
│   ├── Security/                    # Anti-piracy and fraud
│   │   ├── RateLimiter.php          # Throttle activation/download attempts
│   │   ├── AbuseDetector.php        # Shared license / multi-country detection
│   │   └── ChecksumVerifier.php     # SHA-256 file integrity
│   │
│   └── API/                         # REST API endpoints
│       ├── LicenseEndpoints.php
│       ├── UpdateEndpoints.php
│       ├── DownloadEndpoints.php
│       └── SaaSEndpoints.php
│
├── admin/                           # WordPress admin UI
│   ├── views/
│   └── assets/
│
├── assets/                          # Frontend JS/CSS
└── templates/                       # Email templates, dashboard templates
```

---

## 4. Database Schema

### Table: `wp_wdd_licenses`
```sql
CREATE TABLE wp_wdd_licenses (
    id              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    order_id        BIGINT UNSIGNED NOT NULL,
    user_id         BIGINT UNSIGNED NOT NULL,
    product_id      BIGINT UNSIGNED NOT NULL,
    license_key     VARCHAR(64) NOT NULL UNIQUE,
    plan_type       ENUM('single','multi','unlimited','lifetime') DEFAULT 'single',
    status          ENUM('active','expired','revoked','suspended') DEFAULT 'active',
    activation_limit INT UNSIGNED DEFAULT 1,
    activated_count  INT UNSIGNED DEFAULT 0,
    expires_at      DATETIME NULL,
    created_at      DATETIME NOT NULL,
    updated_at      DATETIME NOT NULL,
    PRIMARY KEY (id),
    KEY idx_license_key (license_key),
    KEY idx_user_id (user_id),
    KEY idx_order_id (order_id)
);
```

### Table: `wp_wdd_license_activations`
```sql
CREATE TABLE wp_wdd_license_activations (
    id              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    license_id      BIGINT UNSIGNED NOT NULL,
    domain          VARCHAR(255) NOT NULL,
    ip_address      VARCHAR(45),
    environment     ENUM('production','staging','local') DEFAULT 'production',
    activated_at    DATETIME NOT NULL,
    last_check      DATETIME,
    PRIMARY KEY (id),
    KEY idx_license_id (license_id),
    KEY idx_domain (domain)
);
```

### Table: `wp_wdd_downloads`
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
    ip_address      VARCHAR(45),
    country_code    VARCHAR(2),
    created_at      DATETIME NOT NULL,
    PRIMARY KEY (id),
    KEY idx_token (token)
);
```

### Table: `wp_wdd_download_logs`
```sql
CREATE TABLE wp_wdd_download_logs (
    id              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    download_id     BIGINT UNSIGNED NOT NULL,
    ip_address      VARCHAR(45),
    user_agent      TEXT,
    country_code    VARCHAR(2),
    downloaded_at   DATETIME NOT NULL,
    PRIMARY KEY (id)
);
```

### Table: `wp_wdd_product_versions`
```sql
CREATE TABLE wp_wdd_product_versions (
    id              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    product_id      BIGINT UNSIGNED NOT NULL,
    version         VARCHAR(20) NOT NULL,
    file_path       TEXT NOT NULL,
    checksum_sha256 VARCHAR(64),
    requires_wp     VARCHAR(10),
    tested_wp       VARCHAR(10),
    requires_php    VARCHAR(10),
    channel         ENUM('stable','beta') DEFAULT 'stable',
    changelog       LONGTEXT,
    released_at     DATETIME NOT NULL,
    PRIMARY KEY (id),
    KEY idx_product_version (product_id, version)
);
```

### Table: `wp_wdd_subscriptions`
```sql
CREATE TABLE wp_wdd_subscriptions (
    id              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id         BIGINT UNSIGNED NOT NULL,
    product_id      BIGINT UNSIGNED NOT NULL,
    order_id        BIGINT UNSIGNED NOT NULL,
    license_id      BIGINT UNSIGNED,
    status          ENUM('active','cancelled','paused','expired','past_due') DEFAULT 'active',
    billing_cycle   ENUM('monthly','yearly') DEFAULT 'yearly',
    starts_at       DATETIME NOT NULL,
    expires_at      DATETIME,
    renewal_at      DATETIME,
    cancelled_at    DATETIME NULL,
    PRIMARY KEY (id),
    KEY idx_user_id (user_id),
    KEY idx_renewal (renewal_at)
);
```

### Table: `wp_wdd_saas_accounts`
```sql
CREATE TABLE wp_wdd_saas_accounts (
    id              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id         BIGINT UNSIGNED NOT NULL,
    order_id        BIGINT UNSIGNED NOT NULL,
    product_id      BIGINT UNSIGNED NOT NULL,
    plan            VARCHAR(50),
    api_key         VARCHAR(128) UNIQUE,
    status          ENUM('active','suspended','cancelled') DEFAULT 'active',
    provisioned_at  DATETIME NOT NULL,
    PRIMARY KEY (id),
    KEY idx_user_product (user_id, product_id)
);
```

---

## 5. Key API Endpoints

All endpoints registered under `/wp-json/wdd/v1/`

### License API
```
POST   /license/activate         # Activate license on a domain
POST   /license/deactivate       # Remove a domain activation
GET    /license/check            # Validate license + return plan features
POST   /license/revoke           # Admin: instantly revoke a license
```

### Plugin Update API
```
GET    /plugin/update-check      # Check if update available for version + license
GET    /plugin/download/{token}  # Serve update ZIP (token-gated)
GET    /plugin/changelog/{slug}  # Return changelog for product
```

### Secure Download API
```
GET    /download/{token}         # Serve file if token valid, not expired, count ok
POST   /download/generate        # Generate a new download token (internal)
```

### SaaS API
```
POST   /saas/provision           # Triggered by order webhook: create SaaS account
POST   /saas/suspend             # Suspend on subscription failure
POST   /saas/activate            # Re-activate after payment recovery
GET    /saas/usage/{api_key}     # Usage stats for a customer
```

---

## 6. WooCommerce Integration Points

### Action Hooks Used
```php
// Core order lifecycle
add_action('woocommerce_order_status_completed',    [$this, 'on_order_complete']);
add_action('woocommerce_order_status_refunded',     [$this, 'on_order_refunded']);
add_action('woocommerce_subscription_status_active', [$this, 'on_subscription_active']);
add_action('woocommerce_subscription_status_cancelled', [$this, 'on_subscription_cancelled']);
add_action('woocommerce_subscription_renewal_payment_complete', [$this, 'on_renewal']);

// Download access
add_filter('woocommerce_customer_get_downloadable_products', [$this, 'get_downloads']);
add_filter('woocommerce_downloadable_file_download_url',     [$this, 'secure_download_url']);
```

### Order → License → SaaS Flow
```
WooCommerce Order Completed
        │
        ├─ Is product type "wdd_plugin"?
        │       ├─ Generate license key
        │       ├─ Store in wp_wdd_licenses
        │       ├─ Send email with license + download link
        │       └─ Log to wp_wdd_downloads
        │
        └─ Is product type "wdd_saas"?
                ├─ Call SaaS provisioner
                ├─ Create account (via API or internal)
                ├─ Generate API key
                ├─ Store in wp_wdd_saas_accounts
                └─ Send login credentials email
```

---

## 7. Feature Roadmap & Build Priority

### Phase 1 — MVP (Must Ship First)
- [ ] WooCommerce product type: `wdd_plugin` and `wdd_saas`
- [ ] License generation on order complete
- [ ] License activation / deactivation REST API
- [ ] Secure download token system (expiry + count limit)
- [ ] Download log (IP, country, timestamp)
- [ ] Plugin update API (`/wdd/v1/plugin/update-check`)
- [ ] ZIP version upload and management (Admin UI)
- [ ] Customer My Account: License tab + Download tab
- [ ] Email notifications: purchase, license key, download link

### Phase 2 — Subscription & SaaS
- [ ] Subscription tracking (link to license expiry)
- [ ] Failed payment dunning emails
- [ ] Grace period on expired license
- [ ] SaaS account provisioning on order complete
- [ ] API key generation and management
- [ ] JWT issuer for SaaS login
- [ ] Customer dashboard: API Keys tab, Usage tab

### Phase 3 — Security & Anti-Piracy
- [ ] Geo-blocking (country-level block list)
- [ ] IP rate limiting on activation endpoint
- [ ] Shared license / multi-country abuse detection
- [ ] Concurrent download detection
- [ ] SHA-256 checksum on update ZIPs
- [ ] VPN detection (optional, 3rd party API)
- [ ] Remote kill-switch (instant license revocation)
- [ ] Staging/localhost environment auto-exemption

### Phase 4 — Git Integration & Auto-Updates
- [ ] GitHub webhook receiver (on tag push → import ZIP)
- [ ] Bitbucket webhook receiver
- [ ] Auto-version bump on new release
- [ ] Auto-changelog generation from commit messages
- [ ] Release channel management (stable / beta)
- [ ] Rollback to previous version (admin + customer)

### Phase 5 — Marketing & Growth
- [ ] Abandoned cart recovery (email capture + follow-up)
- [ ] Post-purchase upsell funnel hooks
- [ ] Affiliate program connector (webhook bridge)
- [ ] Bulk discount / coupon generation
- [ ] Customer loyalty points system
- [ ] Referral system

### Phase 6 — Analytics & Reporting
- [ ] Revenue dashboard (MRR, ARR, churn, LTV)
- [ ] License statistics (active, expired, abused)
- [ ] Download statistics (per product, per version)
- [ ] Subscription statistics
- [ ] Export to CSV/Excel

### Phase 7 — Enterprise & Advanced
- [ ] White-label licensing
- [ ] Agency license (multi-site, team-managed)
- [ ] SSO / SAML connector
- [ ] Audit logs
- [ ] REST API for all data (full developer access)
- [ ] Webhooks (license events, subscription events)
- [ ] CLI commands (WP-CLI integration)

---

## 8. Tech Stack

| Layer | Technology |
|---|---|
| Core platform | WordPress + WooCommerce |
| Plugin language | PHP 8.1+ |
| Admin UI | React + WordPress Scripts (Block Editor compatible) |
| REST API | WordPress REST API framework |
| Database | WordPress `$wpdb` + custom tables |
| Background jobs | Action Scheduler (bundled with WooCommerce) |
| Email | WordPress `wp_mail()` + HTML templates |
| File serving | PHP stream (no direct file URL exposure) |
| Token generation | `random_bytes()` + SHA-256 signing |
| JWT | `firebase/php-jwt` or custom HMAC |
| GitHub integration | GitHub Webhooks → PHP endpoint |
| Geo-detection | MaxMind GeoLite2 (free) or ip-api.com |
| Cron | WP-Cron + Action Scheduler for reliability |

---

## 9. What Makes woo-digital-downloads Different

| Capability | EDD | SureCart | FluentCart | woo-digital-downloads |
|---|---|---|---|---|
| WooCommerce native | No | No | No | **Yes** |
| Self-hosted | Yes | No (managed) | Yes | **Yes** |
| Licensing built-in | $199/yr add-on | Yes | Yes | **Yes** |
| WP Plugin auto-update API | Yes (extension) | No | Partial | **Yes** |
| GitHub → update sync | No | No | No | **Yes** |
| SaaS provisioning via webhook | No | Partial | Partial | **Yes** |
| Geo-blocking on downloads | No | No | No | **Yes** |
| JWT for SaaS auth | No | No | No | **Yes** |
| Staging/localhost exemption | Basic | No | No | **Yes** |
| Remote kill-switch | Partial | No | No | **Yes** |
| Extends WooCommerce (not replaces) | No | No | No | **Yes** |

**Core value proposition:** Every other solution either replaces WooCommerce entirely or requires expensive add-ons. `woo-digital-downloads` is a WooCommerce extension — it plugs into the store you already have and adds everything needed to sell software, plugins, and SaaS products professionally.

---

## 10. Mandatory Features Checklist

### Must Have Before Launch
- [x] Product Management (via WooCommerce)
- [ ] License Generation & Management
- [ ] Site Activation Limits (X sites / unlimited)
- [ ] Staging & Localhost Exemption
- [ ] Secure Download URLs (signed tokens + expiry)
- [ ] Download Limits (max clicks per purchase)
- [ ] Automatic Plugin Update API
- [ ] SHA-256 Checksum on ZIPs
- [ ] Subscription Tracking & Renewal
- [ ] SaaS Account Provisioning
- [ ] JWT / API Key Issuance
- [ ] Customer Dashboard (licenses, downloads, API keys)
- [ ] Email Automation (purchase, renewal reminder, expiry)
- [ ] IP Logging on Downloads
- [ ] Rate Limiting on License Endpoint
- [ ] Activity Logs (admin side)

### Should Have (Post-Launch v1.1)
- [ ] Geo-Blocking
- [ ] Abandoned Cart Recovery
- [ ] GitHub/Bitbucket Auto-Update Sync
- [ ] Dunning Management (failed payment retry)
- [ ] Affiliate Connector Webhooks
- [ ] Revenue Analytics Dashboard
- [ ] Remote Kill-Switch
- [ ] Rollback to Previous Version

---

## 11. References

- Easy Digital Downloads: https://easydigitaldownloads.com
- SureCart Pricing: https://surecart.com/pricing
- SureCart vs EDD: https://surecart.com/surecart-vs-easy-digital-downloads
- FluentCart vs EDD: https://fluentcart.com/fluentcart-vs-edd
- WooCommerce: https://woocommerce.com
- Plugin Update Checker Library: https://github.com/YahnisElsts/plugin-update-checker
- Action Scheduler: https://actionscheduler.org
- MaxMind GeoLite2: https://dev.maxmind.com/geoip/geolite2-free-geolocation-data
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

### 1.4 SUMO Subscriptions for WooCommerce
**URL:** https://codecanyon.net/item/sumo-subscriptions-woocommerce-subscription-system/16486054  
**By:** FantasticPlugins (Power Elite Author — $1M+ sold on Envato Market)  
**Model:** CodeCanyon one-time purchase (Regular $49 / Extended $490). WooCommerce add-on.  
**Sales:** 5,063 sales | **Rating:** 4.38/5 (114 reviews) | **Comments:** 1,300+  
**Latest Version:** 17.5.0 (updated February 23, 2026) | **First Released:** June 2016 (10 years old)

**What it does:** A comprehensive WooCommerce subscription plugin covering simple, variable, and grouped product subscriptions, plus "order subscriptions" that can attach recurring billing to any product regardless of type.

**Supported Product & Billing Types:**
- Simple, Variable, and Grouped product subscriptions
- Order Subscriptions — attach billing to any non-subscription product
- Customer-selectable renewal frequency and instalment count for order subscriptions
- Billing synchronization to fixed calendar dates (with prorated charge)
- Free Trial, Paid Trial, optional Trial/Signup Fee
- Subscription duration up to 10 years, fixed or unlimited instalments

**Key Features:**
- Automatic renewal via Stripe (requires WooCommerce Stripe Plugin) and PayPal (requires WooCommerce PayPal Payments)
- Manual renewal via any WooCommerce-supported gateway
- Multiple subscriptions in a single checkout
- Mixed cart (subscription + non-subscription products)
- Upgrade/Downgrade for variable and grouped subscriptions with prorate or full-price charge
- Subscribers can pause, cancel, resubscribe, and update quantity from the My Account page
- Drip downloadable content (content released on a schedule)
- Additional digital downloads tied to subscription
- Admin can manually create and assign subscriptions to any user
- Include/exclude shipping and tax from renewal charges
- Configurable overdue and suspend periods
- Multiple payment reminder, overdue, and suspend notification emails
- Signup Fee Coupons and Recurring Fee Coupons
- Automatic fallback to manual renewal if automatic renewal is cancelled
- Bulk settings update via WooCommerce Bulk Actions
- CSV export of subscriptions
- GDPR compliance (Data Access, Erasure, Privacy Policy)
- Assign user roles to active subscribers
- Customers can add/update payment method from the View Subscription page
- Cancel subscription if product is out of stock at renewal
- Subscription Activation Delay
- Shortcode `[sumo_my_subscriptions]` for a custom My Subscriptions page
- REST API support (since v14.4)
- HPOS (High Performance Order Storage) compatible (since v14.8)
- WooCommerce Cart and Checkout Blocks compatible (since v15.4)
- WPML compatible, translation ready (Japanese and Polish included)
- Action Scheduler for background jobs

**Pricing:**
| License | Price | Usage |
|---|---|---|
| Regular License | $49 one-time | Single end product; end users not charged |
| Extended License | $490 one-time | Single end product; end users can be charged |
| Support extension | +$16.50 | Extends from 6 to 12 months |

Note: both licenses cover a **single site**. No multi-site or unlimited license tier exists.

**SUMO Subscriptions Strengths:**
- Extremely mature (10 years, 5,000+ sales, Power Elite author)
- Most comprehensive WooCommerce-native subscription feature set in its price tier at $49 one-time
- Supports subscription + non-subscription products in the same cart natively
- REST API and HPOS compatibility included
- Order Subscriptions feature is unique — any product can become a subscription without changing its type
- Action Scheduler for reliable background processing

**SUMO Subscriptions Weaknesses:**
- **Single-site only** — no multi-site license; buying for multiple stores requires multiple purchases
- **No lifetime multi-site tier** — no option comparable to SureCart or FluentCart's lifetime deals
- **Limited gateway support for automatic renewal** — only Stripe (via WooCommerce Stripe Plugin), PayPal (via WooCommerce PayPal Payments), and PayPal Subscriptions API; everything else is manual
- **Membership and Donation features require separate paid plugins** (SUMO Memberships, SUMO Donations)
- **No Gutenberg optimization** — flagged "No" in CodeCanyon item attributes
- **No software licensing** — no license key generation, activation limits, or plugin update API (this is purely a billing/subscription tool, not a software delivery tool)
- **No SaaS provisioning** — no webhook-based account creation, no JWT/API key issuance
- **Recurring instability** in changelog: repeated Stripe duplicate charge fixes, PayPal rounding bugs, synchronized subscription edge cases, and coupon calculation issues across multiple major versions
- **Version lock warning** — upgrading from v13.8 to v14.0+ is one-way; downgrading is not supported

**Our Position vs SUMO Subscriptions:**  
SUMO Subscriptions solves recurring billing inside WooCommerce well, but it is purely a payment scheduler — it has no concept of software licensing, plugin update delivery, SaaS provisioning, or secure file distribution. `woo-digital-downloads` handles the full post-payment lifecycle that SUMO does not touch. For stores selling WordPress plugins or SaaS products, SUMO Subscriptions would need to be combined with multiple other tools to cover what WDD provides natively.

---

### 1.5 Abandoned Cart Recovery — Ecosystem Research

WDD Phase 5 includes abandoned cart recovery. Rather than building from scratch, this section evaluates the best existing solutions for integration or reference.

#### 1.5.1 Cart Abandonment Recovery for WooCommerce (Brainstorm Force)
**URL:** https://wordpress.org/plugins/woo-cart-abandonment-recovery/  
**Model:** Free + Pro | **Installs:** 300,000+ | **Rating:** 4.8/5 (609 reviews) | **Updated:** June 2026

The market leader in free abandoned cart recovery. Made by the Astra/CartFlows team — well-funded and actively maintained.

Key features (free): email capture at checkout the moment a customer types their email (no submit required), automated recovery sequences at configurable intervals, 1-click prefilled checkout recovery link, unique time-sensitive coupon code generation, reports dashboard with real-time abandoned/recovered stats, admin notifications, GDPR consent, WooCommerce Blocks support, CSV export, template import/export.

Pro adds: advanced email analytics (open/click rate), SMS and WhatsApp, rule engine for conditional logic by product/category/cart value.

**WDD Integration Decision:** For Phase 5, WDD should integrate with this plugin via webhooks rather than rebuilding — its 300K install base means many WDD customers already use it. WDD will fire `wdd_cart_abandoned` and `wdd_cart_recovered` action hooks so Cart Abandonment Recovery (and any WP plugin) can hook in.

#### 1.5.2 WooCommerce Recover Abandoned Cart (FantasticPlugins / CodeCanyon)
**URL:** https://codecanyon.net/item/woocommerce-recover-abandoned-cart/7715167  
**Model:** $49 one-time | **Sales:** 4,984 | **Rating:** 4.70/5 (158 reviews) | **Updated:** May 2026

Premium alternative from the same author as SUMO Subscriptions. Notable differentiators over the free option: manual mailing (admin can send individual follow-up), Add to Cart popup for early guest email capture, full segmentation by product/category/user role, include/exclude rules, multi-currency (WPML, Aelia, Price Based on Country), BCC option, full HPOS/Blocks support. Uses WP-Cron with custom DB tables; active security patching (Privilege Escalation fixed in v24.8.0).

#### 1.5.3 Abandoned Cart Lite for WooCommerce (Tychesoftwares)
**URL:** https://wordpress.org/plugins/woocommerce-abandoned-cart/  
**Model:** Free + Pro | **Installs:** 20,000+ | **Rating:** 4.1/5 (86 reviews)

Solid free option but lower install count and rating than Brainstorm Force's. Free tier includes webhooks to send abandonment/recovery data to external URLs — useful for WDD event pipeline. Uses Action Scheduler (not raw WP-Cron) — more reliable. GDPR block included.

#### 1.5.4 CartBounty (Streamline)
**URL:** https://wordpress.org/plugins/woo-save-abandoned-carts/  
**Model:** Free + Pro | **Installs:** 10,000+ | **Rating:** 4.8/5 (84 reviews)

Most developer-friendly: 15+ general filters, 8+ email actions/filters, fully overridable templates, Exit Intent popup in the free version. Unique: captures carts from the moment a product is added — not just at checkout — enabling true anonymous cart analytics. "Remember checkout fields" feature reduces abandonment proactively. Coupon generation is Pro-only. Anonymous cart table can grow very large without auto-cleanup (performance concern at scale).

**Summary for WDD:**
| Plugin | Price | Installs | Best For |
|---|---|---|---|
| Cart Abandonment Recovery (Brainstorm) | Free | 300,000+ | Default recommendation — install base, free coupons |
| WC Recover Abandoned Cart (FantasticPlugins) | $49 | 4,984 sales | Segmentation, manual mailing, multi-currency |
| Abandoned Cart Lite (Tychesoftwares) | Free | 20,000+ | Webhooks in free tier |
| CartBounty | Free | 10,000+ | Developer hooks, anonymous cart capture, Exit Intent free |

---

### 1.6 Affiliate Management — Ecosystem Research

WDD Phase 5 includes "Affiliate Connector Webhooks." Decision: build a webhook bridge to existing affiliate systems rather than a full affiliate engine.

#### 1.6.1 SUMO Affiliates for WooCommerce (FantasticPlugins / CodeCanyon)
**URL:** https://codecanyon.net/item/sumo-affiliates-woocommerce-affiliate-system/18273930  
**Model:** $39 one-time | **Sales:** 620 | **Rating:** 4.25/5 (16 reviews) | **Updated:** March 2026

WooCommerce-only affiliate system. Core features: affiliate registration, manual/auto approval, commission per purchase, MLM multi-level support, affiliate frontend dashboard, referral URL generator, creatives/banners management, PayPal payout requests, CSV export, GDPR, HPOS compatible. Commission can be tied to SUMO Subscriptions renewal orders. Author actively upsells to the Pro version.

#### 1.6.2 SUMO Affiliates Pro — WordPress Affiliate Plugin (FantasticPlugins / CodeCanyon)
**URL:** https://codecanyon.net/item/sumo-affiliates-pro-wordpress-affiliate-plugin/22795996  
**Model:** $49 one-time | **Sales:** 954 | **Rating:** 4.53/5 (45 reviews) | **Updated:** April 2026

Works with or without WooCommerce — broader platform. Adds commission triggers beyond purchases: Contact Form 7 / WPForms / Formidable Forms submissions, MailChimp / ActiveCampaign email sign-ups, page/post landing pages, account signups. Modular: Affiliate Wallet, PayPal Payouts, Pushover Notifications, Landing Pages, QR Codes, Leaderboard all as separate modules. Affiliate Wallet allows deferred payouts. Per-affiliate product commission rates. Compatible with SUMO Subscriptions (commission on renewal orders).

**WDD Integration Strategy:** WDD will fire webhook events on `license_activated`, `order_completed`, `subscription_renewed` so both SUMO Affiliates Pro (and AffiliateWP, which Sublium supports) can consume commission data without WDD needing to manage affiliate tracking itself.

#### 1.6.3 MWB HubSpot for WooCommerce (MakeWebBetter)
**URL:** https://wordpress.org/plugins/makewebbetter-hubspot-for-woocommerce/  
**Model:** Free + paid add-ons | **Installs:** 7,000+ | **Rating:** 3.9/5 (54 reviews) | **Updated:** April 2026

Not strictly an affiliate tool — a HubSpot CRM integration for WooCommerce. Relevant for WDD because: abandoned cart tracking (via HubSpot workflows), customer segmentation by purchase history, email automation, multi-currency support, ROI tracking for ad campaigns. HubSpot free account is sufficient to get started. Critical caveat: HPOS compatibility requires a separately paid add-on — major frustration for users on modern WooCommerce. 14 one-star reviews primarily cite this issue. WDD should not depend on this plugin — it's a reference for marketing automation integration possibilities.

---

### 1.7 Free Subscription Billing Alternatives

Beyond SUMO Subscriptions ($49) and WooCommerce Subscriptions (~$199/yr), these free alternatives are worth noting.

#### 1.7.1 Subscriptions for WooCommerce (WP Swings)
**URL:** https://wordpress.org/plugins/subscriptions-for-woocommerce/  
**Model:** Free + Pro | **Installs:** 10,000+ | **Rating:** 4.4/5 (175 reviews) | **Updated:** June 2026

Solid free tier: monthly/weekly/yearly billing, free trials, sign-up fees, admin subscription table, cancel by admin/customer, automated email notifications, HPOS/Multisite/WPML compatible, 21 language translations, REST API. Unique: Subscription Box feature (customer curates items for a box with flexible delivery schedule). Very recent addition: AI Subscription Health Dashboard Widget. Integrates with LearnPress, Points & Rewards, Wallet, Membership, AffiliateWP. Variable products and automatic failed-payment retry are Pro-only.

#### 1.7.2 Subscription & Recurring Payment for WooCommerce (Convers Lab)
**URL:** https://wordpress.org/plugins/subscription/  
**Model:** Free + Pro | **Installs:** 700+ | **Rating:** 4.9/5 (24 reviews) | **Updated:** April 2026

Highest-rated subscription plugin reviewed (4.9/5, zero negative reviews). Small but very recent and actively maintained. Free tier: daily/weekly/monthly/yearly billing, Stripe (with iDEAL and SEPA — unique among free plugins), PayPal, guest checkout, WPML/PolyLang, WooCommerce Blocks, user role changes on subscription status, REST API. Variable products, sign-up fees, installment plans, and grace period are Pro-only. Notable: fastest update cadence in this comparison (30+ releases since June 2025).

#### 1.7.3 Subscriptions for WooCommerce by Sublium (FunnelKit)
**URL:** https://wordpress.org/plugins/sublium-subscriptions-for-woocommerce/  
**Model:** Free (+ 2.9% transaction fee) + Pro | **Installs:** 300+ | **Rating:** 5.0/5 (4 reviews) | **Updated:** April 2026

Newest plugin but built by the FunnelKit team (40,000+ WooCommerce customers). Strongest feature set in the free tier: variable subscription products, installment plans, customer self-service pause/skip/reschedule, activity timeline, VIP subscriber tracking, cash flow forecasting, AffiliateWP integration for renewals, LearnDash, FunnelKit Funnel Builder and Automations integration. Critical limitation: free tier charges a **2.9% additional transaction fee** on all subscription revenue — this accumulates and must be weighed carefully. Pro removes the fee and adds MRR/ARR analytics, smart cancellation flows, drag-and-drop email builder, card updater.

**Summary for WDD:** WDD manages subscription records as metadata (linked to license expiry and SaaS account status). For actual gateway-level recurring billing, WDD integrates with Stripe/PayPal via WooCommerce hooks. WDD does not compete with these subscription engines — it layers on top of whichever one the store uses.

---

### 1.8 License Key / Serial Number Managers — Direct Competitors

#### 1.8.1 WC Serial Numbers (PluginEver)
**URL:** https://wordpress.org/plugins/wc-serial-numbers/  
**Model:** Free + Pro | **Installs:** 1,000+ | **Rating:** 4.7/5 (78 reviews) | **Updated:** May 2026

The most relevant direct competitor to WDD's Licensing module. Core concept: store or import pre-generated keys (serials, gift card codes, event tickets, usernames, eBook codes, software keys) and deliver them automatically on WooCommerce order completion. Free tier: manual key entry, CSV/TXT bulk import, auto-complete orders, key delivery on order page and in email, encryption in DB, built-in HTTP API (validate/activate/deactivate), REST API, customer self-service portal, HPOS/Blocks/WPML compatible, reuse keys on refund, revoke on cancellation, low-stock alerts. Pro adds: key generator (sequential/random), variable product support, Twilio SMS delivery, bulk export.

**Key Differences from WDD:**
| Capability | WC Serial Numbers | woo-digital-downloads |
|---|---|---|
| Key generation | Import or Pro generator | Dynamic `random_bytes()` — no import needed |
| Activation tracking | Basic activate/deactivate via API | Full domain+environment tracking with staging exemption |
| Activation limits | Not prominent | Core feature — enforced per domain |
| Update delivery | Not included | Built-in — `/wdd/v1/plugin/update-check` |
| ZIP version management | Not included | Full versioning, changelog, checksum, release channels |
| SaaS provisioning | Not included | Full webhook-based account creation |
| Download tokens | Not included | Signed expiring tokens per order item |
| Geo-blocking | Not included | Country-level block list |
| Subscription type | Not included | WooCommerce subscription integration planned |
| Product scope | Any WooCommerce product | `wdd_plugin`, `wdd_saas`, `wdd_bundle` types |

WC Serial Numbers is best for stores that sell generic digital keys (game codes, gift cards, software serials) where they already have the keys from a vendor. WDD is purpose-built for WordPress plugin developers and SaaS sellers who generate and control their own licenses.

---

### 1.9 Payment Gateways — Reference

WDD relies on WooCommerce's existing gateway ecosystem rather than building its own. Key gateways:

#### WooCommerce Stripe Payment Gateway
**URL:** https://wordpress.org/plugins/woocommerce-gateway-stripe/  
**Model:** Free | **Installs:** 700,000+ | **Rating:** 3.1/5 | **Updated:** June 2026

Official co-developed plugin (WooCommerce + Stripe). Supports 23 payment methods across 150+ countries: credit/debit cards, Apple Pay, Google Pay, Link, Klarna, Affirm, Afterpay/Clearpay, ACH, SEPA, Bancontact, iDEAL, BLIK, Boleto, Cash App, OXXO, WeChat Pay, Alipay, and more. Adaptive Pricing (automatic currency conversion). Stripe Radar ML fraud detection. Stripe Terminal + Tap to Pay for in-person. WooCommerce Subscriptions compatible via tokenized card-on-file. HPOS compatible. Rating is polarized (3.1) primarily due to v10.8.x regressions — underlying payment infrastructure is mature.

**WDD dependency:** Used for automatic subscription renewal and card-on-file storage. WDD hooks into `woocommerce_subscription_renewal_payment_complete` fired by this gateway.

#### WooCommerce PayPal Payments
**URL:** https://wordpress.org/plugins/woocommerce-paypal-payments/  
**Model:** Free | **Installs:** 800,000+ | **Rating:** 2.8/5 | **Updated:** May 2026

Official plugin (WooCommerce/PayPal/Automattic/Syde). Broadest PayPal feature set: PayPal, Venmo (US), Pay Later (BNPL), Apple Pay, Google Pay, Fastlane (accelerated 1-click checkout), ACDC/BCDC card processing, Crypto, APMs (iDEAL, SEPA, OXXO, Trustly, Ratepay, etc.). Level 2/3 card processing for B2B cost savings. Vaulting for saved cards (subscription card-on-file). Rating is low (2.8) largely due to button placement complaints and occasional breaking changes (v4.0.3 outage). The underlying PayPal integration is mission-critical for stores with PayPal buyers.

**WDD dependency:** WDD triggers subscription grace period / license suspension logic on `woocommerce_order_status_failed` from PayPal-initiated renewals.

---

### 1.10 JWT Authentication Solutions — Library Research

WDD's SaaS module needs JWT issuance for API key authentication. Two WordPress plugins were evaluated; decision is to use `firebase/php-jwt` directly rather than depend on either.

#### 1.10.1 JWT Auth (usefulteam/jwt-auth)
**URL:** https://wordpress.org/plugins/jwt-auth/  
**Model:** Free | **Installs:** 6,000+ | **Rating:** 5.0/5 (22 reviews, zero negative)

Technically the better implementation: refresh token support with rotation, per-device token tracking, short-lived access tokens (10-min default), long-lived refresh tokens (30-day default), PHPUnit tests, excellent filter hook API. **Critical issue:** not updated in 2+ years — WordPress.org shows a compatibility warning (only tested to WP 6.5.8; current is WP 7.x). Single-developer project with sustainability risk.

#### 1.10.2 JWT Authentication for WP REST API (tmeister)
**URL:** https://wordpress.org/plugins/jwt-authentication-for-wp-rest-api/  
**Model:** Freemium | **Installs:** 60,000+ | **Rating:** 4.4/5 (53 reviews) | **Updated:** 4 months ago

Most widely deployed JWT plugin for WordPress. React-based admin dashboard with Live API Explorer (test endpoints from admin panel). Actively maintained, tested to WP 6.9.4. Limitation: no refresh token or token revocation in free tier — both require the paid Pro plan at jwtauth.pro. Several reviews cite aggressive and misleading upsell notices.

**WDD Decision:** Neither plugin is suitable as a hard dependency. WDD will implement its own JWT issuance in `includes/SaaS/JwtIssuer.php` using `firebase/php-jwt` (already a declared Composer dependency, same library both plugins use internally). This avoids plugin conflicts, namespace collisions, and external plugin compatibility risks. WDD tokens will use HS256 signing with a store-specific secret stored in `wp_options`.

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

| Capability | EDD | SureCart | FluentCart | SUMO Subscriptions | woo-digital-downloads |
|---|---|---|---|---|---|
| WooCommerce native | No | No | No | **Yes** | **Yes** |
| Self-hosted | Yes | No (managed) | Yes | **Yes** | **Yes** |
| Licensing built-in | $199/yr add-on | Yes | Yes | No | **Yes** |
| WP Plugin auto-update API | Yes (extension) | No | Partial | No | **Yes** |
| GitHub → update sync | No | No | No | No | **Yes** |
| Subscription billing | $209/yr add-on | Yes | Yes | **Yes ($49 one-time)** | **Yes** |
| Subscription + non-subscription cart | No | Yes | Yes | **Yes** | **Yes** |
| SaaS provisioning via webhook | No | Partial | Partial | No | **Yes** |
| Geo-blocking on downloads | No | No | No | No | **Yes** |
| JWT for SaaS auth | No | No | No | No | **Yes** |
| Staging/localhost exemption | Basic | No | No | No | **Yes** |
| Remote kill-switch | Partial | No | No | No | **Yes** |
| Multi-site license option | Yes | Yes | Yes | No (single-site only) | **Yes** |
| Extends WooCommerce (not replaces) | No | No | No | **Yes** | **Yes** |

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
- SUMO Subscriptions for WooCommerce: https://codecanyon.net/item/sumo-subscriptions-woocommerce-subscription-system/16486054
- FantasticPlugins (SUMO author): https://fantasticplugins.com
- WC Recover Abandoned Cart (CodeCanyon): https://codecanyon.net/item/woocommerce-recover-abandoned-cart/7715167
- Cart Abandonment Recovery for WooCommerce: https://wordpress.org/plugins/woo-cart-abandonment-recovery/
- Abandoned Cart Lite for WooCommerce: https://wordpress.org/plugins/woocommerce-abandoned-cart/
- CartBounty – Save and Recover Abandoned Carts: https://wordpress.org/plugins/woo-save-abandoned-carts/
- SUMO Affiliates for WooCommerce: https://codecanyon.net/item/sumo-affiliates-woocommerce-affiliate-system/18273930
- SUMO Affiliates Pro: https://codecanyon.net/item/sumo-affiliates-pro-wordpress-affiliate-plugin/22795996
- MWB HubSpot for WooCommerce: https://wordpress.org/plugins/makewebbetter-hubspot-for-woocommerce/
- Subscriptions for WooCommerce (WP Swings): https://wordpress.org/plugins/subscriptions-for-woocommerce/
- Subscription & Recurring Payment (Convers Lab): https://wordpress.org/plugins/subscription/
- Subscriptions for WooCommerce by Sublium: https://wordpress.org/plugins/sublium-subscriptions-for-woocommerce/
- WC Serial Numbers: https://wordpress.org/plugins/wc-serial-numbers/
- WooCommerce PayPal Payments: https://wordpress.org/plugins/woocommerce-paypal-payments/
- WooCommerce Stripe Payment Gateway: https://wordpress.org/plugins/woocommerce-gateway-stripe/
- JWT Auth (usefulteam): https://wordpress.org/plugins/jwt-auth/
- JWT Authentication for WP REST API (tmeister): https://wordpress.org/plugins/jwt-authentication-for-wp-rest-api/
- firebase/php-jwt: https://github.com/firebase/php-jwt

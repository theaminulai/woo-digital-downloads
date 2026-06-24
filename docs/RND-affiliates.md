# RND — Affiliate Connector Module
**Plugin:** woo-digital-downloads
**Module:** Affiliate Connector
**Phase:** 5
**Standalone:** Yes — webhook bridge only; does not depend on any other WDD module

---

## Overview

WDD does not build a full affiliate management system. Instead, it fires WordPress action hooks and (optionally) outbound webhooks when key revenue events occur — license activated, order completed, subscription renewed — so that any affiliate plugin can record commissions without WDD needing to know the affiliate system's internals.

This keeps WDD focused on digital product delivery while giving affiliate platform full credit attribution flexibility.

---

## Why a Bridge, Not a Full Feature?

Affiliate management is complex: tracking cookies, referral links, payout methods, fraud detection, tax handling. Dedicated solutions already handle this well:

| Plugin | Price | Users | Notes |
|---|---|---|---|
| AffiliateWP | $149/yr | 200,000+ sites | Industry standard; REST API; Stripe/PayPal payouts |
| SUMO Affiliates Pro | $49 | 954 sales | Any WordPress site, not just WC |
| SUMO Affiliates for WooCommerce | $39 | 620 sales | WooCommerce-only |
| Solid Affiliate | $149/yr | — | Modern UI, WooCommerce-native |

WDD fires the events. The affiliate plugin does the tracking, referral attribution, and payout management.

---

## Standalone Usage

Enable the Affiliate Connector module in **Settings → Digital Downloads → Modules**.

This module:
- Fires `wdd_affiliate_event` on key revenue events
- Supports webhook delivery to external affiliate platforms (not just WordPress plugins)
- Provides a filter to pass custom commission data per product
- Does not manage affiliates, referral links, or payouts

**No other WDD module is required.**

---

## WDD Affiliate Hooks

### General Event Hook

```php
/**
 * Fired on every affiliate-relevant WDD event.
 *
 * @param string $event_type  One of: order_completed, license_activated, subscription_renewed,
 *                             subscription_cancelled, order_refunded
 * @param array  $data        Event payload — see per-event structure below
 */
do_action( 'wdd_affiliate_event', $event_type, $data );
```

### Per-Event Payloads

**order_completed:**
```php
$data = [
    'order_id'     => 1234,
    'user_id'      => 56,
    'product_id'   => 78,
    'product_type' => 'wdd_plugin',  // or wdd_saas, wdd_bundle
    'order_total'  => 49.00,
    'currency'     => 'USD',
    'license_id'   => 90,            // null if Licensing module disabled
    'timestamp'    => '2026-06-24T12:00:00Z',
];
```

**license_activated:**
```php
$data = [
    'license_id'  => 90,
    'domain'      => 'example.com',
    'environment' => 'production',   // or staging, local
    'product_id'  => 78,
    'user_id'     => 56,
    'timestamp'   => '2026-06-24T12:05:00Z',
];
```

**subscription_renewed:**
```php
$data = [
    'subscription_id' => 12,
    'order_id'        => 1235,
    'user_id'         => 56,
    'product_id'      => 78,
    'renewal_total'   => 49.00,
    'currency'        => 'USD',
    'new_expiry'      => '2027-06-24',
    'timestamp'       => '2026-06-24T12:00:00Z',
];
```

---

## Specific Hooks for Major Affiliate Plugins

### AffiliateWP Integration

AffiliateWP natively tracks WooCommerce orders via its WooCommerce add-on. For WDD-specific attribution (e.g., commission only on first license activation, not order):

```php
add_action( 'wdd_affiliate_event', function( $event_type, $data ) {
    if ( $event_type !== 'license_activated' ) return;
    if ( $data['environment'] !== 'production' ) return; // skip staging

    // Credit affiliate for production license activation
    if ( function_exists( 'affwp_add_referral' ) ) {
        $affiliate_id = affwp_get_affiliate_id( get_current_user_id() ); // or from cookie
        affwp_add_referral( [
            'affiliate_id' => $affiliate_id,
            'amount'       => $data['order_total'] * 0.30, // 30% commission
            'reference'    => $data['order_id'],
            'description'  => 'License activated: ' . $data['domain'],
            'status'       => 'unpaid',
        ] );
    }
}, 10, 2 );
```

### SUMO Affiliates Pro Integration

SUMO Affiliates Pro listens to WooCommerce's `woocommerce_order_status_completed` natively. For subscription renewals and SaaS events, bridge via WDD:

```php
add_action( 'wdd_affiliate_event', function( $event_type, $data ) {
    if ( $event_type !== 'subscription_renewed' ) return;

    // SUMO Affiliates Pro: trigger a commission on renewal
    do_action( 'sumo_affiliates_add_referral', [
        'order_id'     => $data['order_id'],
        'referral_amt' => $data['renewal_total'] * 0.20,
    ] );
}, 10, 2 );
```

---

## Outbound Webhook Delivery (External Platforms)

For affiliate platforms that are not WordPress plugins (e.g., Post Affiliate Pro, Tapfiliate, Impact), WDD can fire an outbound webhook on each event:

**Settings → Digital Downloads → Affiliates → Webhook URL**

Payload format (JSON, signed with HMAC-SHA256 via `X-WDD-Affiliate-Sig` header):

```json
{
  "event":      "order_completed",
  "timestamp":  "2026-06-24T12:00:00Z",
  "order_id":   1234,
  "user_id":    56,
  "product_id": 78,
  "total":      49.00,
  "currency":   "USD"
}
```

Signature verification on the receiving end:

```php
$expected = hash_hmac( 'sha256', $raw_body, $webhook_secret );
$received = $_SERVER['HTTP_X_WDD_AFFILIATE_SIG'];
if ( ! hash_equals( $expected, $received ) ) {
    // reject
}
```

---

## Commission Rate Filter

```php
/**
 * Customize commission rate per product.
 *
 * @param float $rate        Default commission rate (decimal, e.g. 0.30 = 30%)
 * @param int   $product_id  WooCommerce product ID
 * @param string $event_type  Which event triggered the commission
 * @return float
 */
$rate = apply_filters( 'wdd_affiliate_commission_rate', 0.30, $product_id, $event_type );
```

---

## Admin Settings

**Settings → Digital Downloads → Affiliates**

| Setting | Default | Description |
|---|---|---|
| Enable affiliate connector | Off | Master toggle |
| Fire on order completion | On | Trigger event when order completes |
| Fire on license activation | Off | Trigger event on first production activation |
| Fire on subscription renewal | Off | Trigger event on renewal payment |
| Webhook URL | — | External affiliate platform endpoint |
| Webhook secret | — | HMAC-SHA256 signing key |
| Default commission rate | 30% | Used in webhook payload and filters |

---

## Phase 5 Implementation Plan

1. `AffiliateConnector` class in `includes/Marketing/AffiliateConnector.php`
2. Hook into `wdd_license_activated`, `woocommerce_order_status_completed`, `wdd_license_renewed`
3. Fire `wdd_affiliate_event` with structured payload
4. Optional outbound webhook with HMAC-SHA256 signature
5. Admin settings page section
6. Documentation + integration guides for AffiliateWP and SUMO Affiliates Pro

---

## References

- AffiliateWP: https://affiliatewp.com
- SUMO Affiliates for WooCommerce: https://codecanyon.net/item/sumo-affiliates-woocommerce-affiliate-system/18273930
- SUMO Affiliates Pro: https://codecanyon.net/item/sumo-affiliates-pro-wordpress-affiliate-plugin/22795996
- Solid Affiliate: https://solidaffiliate.com
- Post Affiliate Pro: https://www.postaffiliatepro.com
- Tapfiliate: https://tapfiliate.com

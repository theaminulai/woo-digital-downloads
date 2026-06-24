# RND — Abandoned Cart Recovery Module
**Plugin:** woo-digital-downloads
**Module:** Abandoned Cart Recovery
**Phase:** 5
**Standalone:** Yes — webhook bridge only; does not depend on any other WDD module

---

## Overview

WDD does not build an abandoned cart email engine. Instead, it fires standardized WordPress action hooks so that any existing abandoned cart plugin can integrate without custom code. This keeps WDD lean and lets store owners use whatever cart recovery tool they already have (or the best-in-class free option).

---

## Why a Bridge, Not a Full Feature?

Cart abandonment email engines are a solved problem with excellent free options:

| Plugin | Installs | Rating | Cost |
|---|---|---|---|
| Cart Abandonment Recovery (Brainstorm Force) | 300,000+ | 4.8/5 | Free |
| Abandoned Cart Lite (Tychesoftwares) | 20,000+ | 4.1/5 | Free |
| CartBounty | 10,000+ | 4.8/5 | Free |
| WC Recover Abandoned Cart (FantasticPlugins) | 4,984 sales | 4.70/5 | $49 |

Building a competing email sequence engine would duplicate effort, require SMTP configuration, and add GDPR complexity. A hook bridge achieves better results with far less code.

---

## Standalone Usage

Enable the Abandoned Cart module in **Settings → Digital Downloads → Modules**.

This module:
- Monitors WooCommerce cart sessions for WDD product types (`wdd_plugin`, `wdd_saas`, `wdd_bundle`)
- Fires `wdd_cart_abandoned` when a cart with WDD products is detected as abandoned
- Fires `wdd_cart_recovered` when the customer returns and completes the order
- Passes cart content details (product IDs, values, customer identity if known) in the hook payload

**No other WDD module is required.**

---

## Integration: WDD Hooks → Third-Party AC Plugins

### Hook: `wdd_cart_abandoned`

```php
/**
 * Fired when a cart containing WDD products is detected as abandoned.
 *
 * @param int   $user_id        0 for guest, WordPress user ID for logged-in users
 * @param array $cart_contents  Array of WC cart items filtered to WDD products
 * @param float $cart_total     Total value of abandoned cart
 * @param string $customer_email Email if known (from checkout field or logged-in user)
 */
do_action( 'wdd_cart_abandoned', $user_id, $cart_contents, $cart_total, $customer_email );
```

### Hook: `wdd_cart_recovered`

```php
/**
 * Fired when an abandoned cart is recovered (order completed).
 *
 * @param int $user_id   WordPress user ID
 * @param int $order_id  WooCommerce order ID
 * @param float $order_total  Total value of recovered order
 */
do_action( 'wdd_cart_recovered', $user_id, $order_id, $order_total );
```

### Hook: `wdd_cart_reminder_sent`

```php
/**
 * Fired after WDD sends a cart reminder (if built-in email is enabled).
 *
 * @param string $customer_email
 * @param int    $attempt  Which reminder attempt (1, 2, 3)
 */
do_action( 'wdd_cart_reminder_sent', $customer_email, $attempt );
```

---

## Cart Abandonment Detection Strategy

Cart abandonment is detected via WooCommerce session data with a configurable timeout (default: 60 minutes of inactivity).

```php
// Scheduled via Action Scheduler, runs every 15 minutes
class AbandonedCart {

    public function check_abandoned_carts(): void {
        $threshold = time() - ( get_option( 'wdd_abandon_timeout', 60 ) * MINUTE_IN_SECONDS );

        // Query WC sessions updated before threshold that have WDD cart items
        // Fire wdd_cart_abandoned for each qualifying session
    }

    public function on_order_completed( int $order_id ): void {
        // Remove cart from abandoned tracking
        // Fire wdd_cart_recovered
    }
}
```

---

## Integration Example: Cart Abandonment Recovery (Brainstorm Force)

Brainstorm Force's "Cart Abandonment Recovery" plugin does not natively know about WDD cart events. To bridge:

```php
// In your theme's functions.php or a custom plugin
add_action( 'wdd_cart_abandoned', function( $user_id, $cart_contents, $cart_total, $email ) {
    // CAR plugin stores abandoned carts in its own table
    // You can insert directly or trigger its own capture mechanism
    if ( function_exists( 'wcf_ca_capture_cart' ) ) {
        wcf_ca_capture_cart( $email, $cart_contents, $cart_total );
    }
}, 10, 4 );
```

---

## Integration Example: CartBounty

CartBounty has native WooCommerce hooks but also exposes its own action: `cartbounty_save_cart`. WDD fires its hook first, then CartBounty takes over:

```php
add_action( 'wdd_cart_abandoned', function( $user_id, $cart_contents, $cart_total, $email ) {
    // CartBounty will pick this up automatically if WC session is active.
    // No extra code needed — WDD hook fires at the same point WC session is readable.
}, 10, 4 );
```

---

## Admin Settings

**Settings → Digital Downloads → Abandoned Cart**

| Setting | Default | Description |
|---|---|---|
| Enable abandoned cart tracking | Off | Master toggle for this module |
| Abandonment timeout (minutes) | 60 | How long before a cart is considered abandoned |
| Track guest carts | Off | Capture carts before email is entered |
| Built-in reminder email | Off | Enable WDD's own recovery email (basic) |
| Reminder 1 delay (minutes) | 60 | Send first reminder after N minutes |
| Reminder 2 delay (minutes) | 1440 | Send second reminder after N minutes (24h) |
| Reminder 3 delay (minutes) | 4320 | Send third reminder after N minutes (72h) |

---

## Recommended Third-Party Plugin

**Cart Abandonment Recovery by Brainstorm Force**
- Free, 300,000+ installs, 4.8/5 rating
- URL: https://wordpress.org/plugins/woo-cart-abandonment-recovery/
- Works alongside WDD via standard WooCommerce hooks
- Handles email templates, UTM tracking, coupon generation

---

## Phase 5 Implementation Plan

1. `AbandonedCart` class in `includes/Marketing/AbandonedCart.php`
2. Action Scheduler job: `wdd_check_abandoned_carts` every 15 minutes
3. DB table: `wp_wdd_abandoned_carts` (session hash, cart data, email, status, timestamps)
4. Admin settings page section
5. Optional built-in email template (HTML, respects WooCommerce email styles)
6. Recovery link generation tied to WDD cart restore endpoint

---

## DB Table: `wp_wdd_abandoned_carts`

```sql
CREATE TABLE {prefix}wdd_abandoned_carts (
    id              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    session_id      VARCHAR(255) NOT NULL,
    user_id         BIGINT UNSIGNED DEFAULT 0,
    customer_email  VARCHAR(255),
    cart_contents   LONGTEXT NOT NULL,
    cart_total      DECIMAL(10,2) DEFAULT 0.00,
    status          ENUM('abandoned','recovered','reminder_1_sent','reminder_2_sent','reminder_3_sent') DEFAULT 'abandoned',
    abandoned_at    DATETIME NOT NULL,
    recovered_at    DATETIME NULL,
    recovery_link   VARCHAR(500),
    PRIMARY KEY (id),
    KEY idx_email (customer_email),
    KEY idx_status (status),
    KEY idx_abandoned_at (abandoned_at)
);
```

---

## References

- Cart Abandonment Recovery (Brainstorm Force): https://wordpress.org/plugins/woo-cart-abandonment-recovery/
- CartBounty: https://wordpress.org/plugins/woo-save-abandoned-carts/
- Abandoned Cart Lite (Tychesoftwares): https://wordpress.org/plugins/woocommerce-abandoned-cart/
- WC Recover Abandoned Cart (FantasticPlugins): https://codecanyon.net/item/woocommerce-recover-abandoned-cart/7715167
- Action Scheduler: https://actionscheduler.org

# RND — Subscription Tracking Module
**Plugin:** woo-digital-downloads
**Module:** Subscription Tracking
**Phase:** 2
**Standalone:** Yes — tracks WooCommerce recurring order lifecycle independently

---

## Overview

The Subscriptions module does not replace your payment gateway's recurring billing. Instead, it sits alongside whatever subscription billing system you use (WooCommerce Subscriptions, SUMO Subscriptions, WP Swings, Stripe subscription schedules, etc.) and tracks subscription state as metadata. When a renewal succeeds, the linked license is extended. When a renewal fails, the dunning sequence begins and the license moves to `suspended`. When a subscription is cancelled, the license is revoked.

### Problem It Solves
- WooCommerce has no built-in concept of a subscription linked to a license
- After a renewal, nothing automatically extends a license expiry date
- Failed payment scenarios have no automated follow-up logic for digital products
- Upgrade/downgrade between subscription tiers requires proration logic

---

## Standalone Usage

Enable this module alone (without Licensing) to:
- Track subscription state in the WDD database
- Record renewal history
- Send dunning emails on failed payments
- Suspend SaaS accounts on cancellation (if SaaS module is enabled)

Without the Licensing module, subscription events do not affect any license records. With Licensing enabled, renewal events automatically extend the license expiry.

---

## Architecture

### Classes

| Class | File | Responsibility |
|---|---|---|
| `SubscriptionManager` | `includes/Subscriptions/SubscriptionManager.php` | Create, renew, cancel subscription records |
| `DunningManager` | `includes/Subscriptions/DunningManager.php` | Failed payment retry sequence + emails |
| `PlanUpgrade` | `includes/Subscriptions/PlanUpgrade.php` | Proration and upgrade/downgrade logic |

### Subscription Lifecycle

```
Order Completed (initial purchase)
    │
    └── SubscriptionManager::create()
            INSERT wp_wdd_subscriptions {status: active, renewal_at: +30days or +365days}
            [If Licensing active] → link license_id

Renewal Payment Completed
    │
    └── SubscriptionManager::renew()
            UPDATE wp_wdd_subscriptions {renewal_at: +period, status: active}
            [If Licensing active] → LicenseExpiry::extend(license_id, +period_days)
            [If SaaS active]     → AccountProvisioner::activate(account_id)

Renewal Payment Failed
    │
    └── DunningManager::on_payment_failed()
            UPDATE wp_wdd_subscriptions {status: past_due}
            Schedule dunning sequence via Action Scheduler:
                Day 1:  Send "Payment failed — please update payment method" email
                Day 3:  Send reminder email
                Day 7:  Set license status = 'suspended'
                        [If SaaS] AccountProvisioner::suspend(account_id)
                Day 14: Final notice, cancel subscription
                        Update status = 'cancelled'
                        [If Licensing] LicenseRevoke::revoke(license_id)

Subscription Cancelled (by customer or admin)
    │
    └── SubscriptionManager::cancel()
            UPDATE wp_wdd_subscriptions {status: cancelled, cancelled_at: NOW()}
            [If Licensing] → license stays active until expires_at (grace period)
            [If SaaS]     → AccountProvisioner::suspend(account_id)

Subscription Paused
    │
    └── SubscriptionManager::pause()
            UPDATE wp_wdd_subscriptions {status: paused}
            [If Licensing] → no change (license stays valid during pause period)
```

---

## Database Table

### wp_wdd_subscriptions
```sql
CREATE TABLE wp_wdd_subscriptions (
    id            BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id       BIGINT UNSIGNED NOT NULL,
    product_id    BIGINT UNSIGNED NOT NULL,
    order_id      BIGINT UNSIGNED NOT NULL,   -- initial order ID
    license_id    BIGINT UNSIGNED NULL,        -- FK wp_wdd_licenses (if Licensing enabled)
    saas_account_id BIGINT UNSIGNED NULL,      -- FK wp_wdd_saas_accounts (if SaaS enabled)
    status        ENUM('active','cancelled','paused','expired','past_due') DEFAULT 'active',
    billing_cycle ENUM('monthly','yearly') DEFAULT 'yearly',
    starts_at     DATETIME NOT NULL,
    expires_at    DATETIME NULL,
    renewal_at    DATETIME NULL,
    cancelled_at  DATETIME NULL,
    PRIMARY KEY (id),
    KEY idx_user_id (user_id),
    KEY idx_renewal (renewal_at),
    KEY idx_status (status)
);
```

---

## WooCommerce Integration Hooks

```php
// Initial subscription creation
add_action( 'woocommerce_order_status_completed', [ SubscriptionManager::class, 'on_order_completed' ] );

// Renewal payment success (fired by WooCommerce Subscriptions / SUMO Subscriptions / etc.)
add_action( 'woocommerce_subscription_renewal_payment_complete', [ SubscriptionManager::class, 'on_renewal_success' ], 10, 2 );

// Renewal payment failed
add_action( 'woocommerce_subscription_payment_failed', [ DunningManager::class, 'on_payment_failed' ], 10, 2 );

// Subscription cancelled (by customer or admin)
add_action( 'woocommerce_subscription_status_cancelled', [ SubscriptionManager::class, 'on_cancelled' ], 10, 1 );

// Subscription paused
add_action( 'woocommerce_subscription_status_on-hold', [ SubscriptionManager::class, 'on_paused' ], 10, 1 );
```

---

## Dunning Email Sequence

| Day | Email | Action |
|---|---|---|
| 0 | Payment failed — please update card | None (grace period starts) |
| 3 | Payment reminder | None |
| 7 | Final notice — access suspended | Suspend license / SaaS account |
| 14 | Subscription cancelled | Cancel subscription, revoke license |

All dunning emails are scheduled via Action Scheduler for reliable delivery even on shared hosting with unreliable WP-Cron.

Email templates stored in `templates/emails/` and filterable via standard WooCommerce email template override system.

---

## Configuration Options

| Option | Default | Description |
|---|---|---|
| `wdd_dunning_day_1` | `1` | Days after failure before first email |
| `wdd_dunning_day_2` | `3` | Days before second email |
| `wdd_dunning_suspend_day` | `7` | Days before suspension |
| `wdd_dunning_cancel_day` | `14` | Days before cancellation |
| `wdd_grace_period_days` | `0` | Days license stays active after cancellation |
| `wdd_billing_cycle_default` | `yearly` | Default billing cycle for WDD products |

---

## Plan Upgrade / Downgrade

`PlanUpgrade` handles mid-cycle tier changes:

```
Customer upgrades from Single Site ($49/yr) to Multi Site ($99/yr)
    │
    └── PlanUpgrade::process_upgrade()
            ├── Calculate days remaining in current cycle
            ├── Calculate prorated refund for unused days on old plan
            ├── Issue WooCommerce store credit or partial refund
            ├── Create new order for new plan (prorated amount)
            ├── Update wp_wdd_subscriptions with new product_id
            ├── Update wp_wdd_licenses plan_type and activation_limit
            └── Schedule renewal_at based on new billing cycle
```

---

## Competitive Context

| | SUMO Subscriptions ($49) | WP Swings (free) | Sublium (free + 2.9% fee) | woo-digital-downloads |
|---|---|---|---|---|
| Subscription billing | Yes (standalone) | Yes (standalone) | Yes (standalone) | **Tracks WC billing** |
| Linked to license expiry | No | No | No | **Yes** |
| Linked to SaaS account | No | No | No | **Yes** |
| Dunning sequence | Basic emails | No (Pro) | Yes (free) | **Yes** |
| Proration on upgrade | Yes | No | Yes (free) | **Yes** |
| Grace period | No | No | No | **Yes** |
| Action Scheduler | Yes | Yes | Not confirmed | **Yes** |
| Works with WooCommerce Subscriptions | Conflict possible | Yes | Yes | **Yes (hooks-based)** |
| WooCommerce native | Yes | Yes | Yes | **Yes** |
| Mixed with SaaS | No | No | No | **Yes** |

---

## Developer Hooks

```php
// After subscription record is created
do_action( 'wdd_subscription_created', $subscription_id, $order_id, $product_id );

// After successful renewal
do_action( 'wdd_subscription_renewed', $subscription_id, $new_renewal_at );

// When license is extended on renewal
do_action( 'wdd_license_renewed', $license_id, $new_expiry );

// After payment fails (before dunning starts)
do_action( 'wdd_subscription_payment_failed', $subscription_id );

// When subscription is suspended
do_action( 'wdd_subscription_suspended', $subscription_id );

// When subscription is cancelled
do_action( 'wdd_subscription_cancelled', $subscription_id );

// Filter dunning schedule (return array of days)
apply_filters( 'wdd_dunning_schedule', [ 1, 3, 7, 14 ], $subscription_id );
```

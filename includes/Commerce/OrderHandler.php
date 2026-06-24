<?php
/**
 * Hooks into WooCommerce order lifecycle to provision licenses and SaaS accounts.
 *
 * @package WooDigitalDownloads\Commerce
 */

namespace WooDigitalDownloads\Commerce;

defined( 'ABSPATH' ) || exit;

use WooDigitalDownloads\Licensing\LicenseGenerator;
use WooDigitalDownloads\Downloads\TokenManager;
use WooDigitalDownloads\SaaS\AccountProvisioner;

/**
 * Listens to order status changes and dispatches provisioning.
 */
class OrderHandler {

    public function __construct() {
        // Main provisioning trigger.
        add_action( 'woocommerce_order_status_completed', [ $this, 'on_order_completed' ], 10, 1 );

        // Revoke access on refund.
        add_action( 'woocommerce_order_status_refunded', [ $this, 'on_order_refunded' ], 10, 1 );

        // Send reminder emails before license expiry (hooked from cron).
        add_action( 'wdd_check_expired_licenses', [ $this, 'process_expired_licenses' ] );
    }

    /**
     * Runs when an order is marked Complete.
     * Generates a license and/or SaaS account for each line item.
     *
     * @param int $order_id
     */
    public function on_order_completed( int $order_id ): void {
        $order = wc_get_order( $order_id );

        if ( ! $order ) {
            return;
        }

        // Prevent double-provisioning.
        if ( $order->get_meta( '_wdd_provisioned' ) ) {
            return;
        }

        foreach ( $order->get_items() as $item ) {
            /** @var \WC_Order_Item_Product $item */
            $product = $item->get_product();

            if ( ! $product ) {
                continue;
            }

            $type = $product->get_type();

            if ( in_array( $type, [ ProductTypes::TYPE_PLUGIN, ProductTypes::TYPE_BUNDLE ], true ) ) {
                $this->provision_license( $order, $item, $product );
            }

            if ( in_array( $type, [ ProductTypes::TYPE_SAAS, ProductTypes::TYPE_BUNDLE ], true ) ) {
                $this->provision_saas( $order, $item, $product );
            }
        }

        $order->update_meta_data( '_wdd_provisioned', '1' );
        $order->save();

        do_action( 'wdd_order_provisioned', $order_id );
    }

    /**
     * Generate and store a license key for a plugin product.
     *
     * @param \WC_Order              $order
     * @param \WC_Order_Item_Product $item
     * @param \WC_Product            $product
     */
    private function provision_license(
        \WC_Order $order,
        \WC_Order_Item_Product $item,
        \WC_Product $product
    ): void {
        $generator = new LicenseGenerator();

        $license_id = $generator->create( [
            'order_id'        => $order->get_id(),
            'user_id'         => $order->get_customer_id(),
            'product_id'      => $product->get_id(),
            'plan_type'       => $product->get_meta( '_wdd_license_type' ) ?: 'single',
            'activation_limit' => (int) ( $product->get_meta( '_wdd_activation_limit' ) ?: 1 ),
            'expires_at'      => $this->calculate_expiry( $product ),
        ] );

        if ( $license_id ) {
            // Store reference on the order item for quick lookup.
            $item->update_meta_data( '_wdd_license_id', $license_id );
            $item->save();

            // Generate a secure download token as well.
            $token_manager = new TokenManager();
            $token_manager->create_for_order_item( $order, $item, $product );

            do_action( 'wdd_license_provisioned', $license_id, $order->get_id() );
        }
    }

    /**
     * Create a SaaS account for a SaaS-type product.
     *
     * @param \WC_Order              $order
     * @param \WC_Order_Item_Product $item
     * @param \WC_Product            $product
     */
    private function provision_saas(
        \WC_Order $order,
        \WC_Order_Item_Product $item,
        \WC_Product $product
    ): void {
        $provisioner = new AccountProvisioner();

        $account_id = $provisioner->provision( [
            'order_id'   => $order->get_id(),
            'user_id'    => $order->get_customer_id(),
            'product_id' => $product->get_id(),
            'plan'       => $product->get_meta( '_wdd_saas_plan' ) ?: 'starter',
        ] );

        if ( $account_id ) {
            $item->update_meta_data( '_wdd_saas_account_id', $account_id );
            $item->save();

            do_action( 'wdd_saas_provisioned', $account_id, $order->get_id() );
        }
    }

    /**
     * Revoke licenses and SaaS accounts when an order is refunded.
     *
     * @param int $order_id
     */
    public function on_order_refunded( int $order_id ): void {
        $order = wc_get_order( $order_id );

        if ( ! $order ) {
            return;
        }

        foreach ( $order->get_items() as $item ) {
            /** @var \WC_Order_Item_Product $item */

            $license_id = (int) $item->get_meta( '_wdd_license_id' );
            if ( $license_id ) {
                global $wpdb;
                $wpdb->update(
                    $wpdb->prefix . 'wdd_licenses',
                    [ 'status' => 'revoked', 'updated_at' => current_time( 'mysql' ) ],
                    [ 'id' => $license_id ],
                    [ '%s', '%s' ],
                    [ '%d' ]
                );

                do_action( 'wdd_license_revoked', $license_id, $order_id );
            }

            $account_id = (int) $item->get_meta( '_wdd_saas_account_id' );
            if ( $account_id ) {
                global $wpdb;
                $wpdb->update(
                    $wpdb->prefix . 'wdd_saas_accounts',
                    [ 'status' => 'cancelled' ],
                    [ 'id' => $account_id ],
                    [ '%s' ],
                    [ '%d' ]
                );

                do_action( 'wdd_saas_cancelled', $account_id, $order_id );
            }
        }
    }

    /**
     * Cron: mark expired licenses as 'expired' and send reminder emails.
     */
    public function process_expired_licenses(): void {
        global $wpdb;

        $expired = $wpdb->get_col(
            $wpdb->prepare(
                "SELECT id FROM {$wpdb->prefix}wdd_licenses
                 WHERE status = 'active'
                   AND expires_at IS NOT NULL
                   AND expires_at < %s",
                current_time( 'mysql' )
            )
        );

        foreach ( $expired as $license_id ) {
            $wpdb->update(
                $wpdb->prefix . 'wdd_licenses',
                [ 'status' => 'expired', 'updated_at' => current_time( 'mysql' ) ],
                [ 'id' => (int) $license_id ],
                [ '%s', '%s' ],
                [ '%d' ]
            );

            do_action( 'wdd_license_expired', (int) $license_id );
        }
    }

    /**
     * Calculate expiry date based on product meta (default: 1 year).
     *
     * @param \WC_Product $product
     * @return string|null MySQL datetime or null (lifetime).
     */
    private function calculate_expiry( \WC_Product $product ): ?string {
        $plan_type = $product->get_meta( '_wdd_license_type' );

        if ( 'lifetime' === $plan_type ) {
            return null;
        }

        $duration_days = (int) ( $product->get_meta( '_wdd_license_duration_days' ) ?: 365 );

        return gmdate( 'Y-m-d H:i:s', strtotime( "+{$duration_days} days" ) );
    }
}

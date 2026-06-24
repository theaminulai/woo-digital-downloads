<?php
/**
 * Provisions SaaS accounts when WooCommerce orders are completed.
 *
 * @package WooDigitalDownloads\SaaS
 */

namespace WooDigitalDownloads\SaaS;

defined( 'ABSPATH' ) || exit;

/**
 * Creates SaaS account records and dispatches provisioning webhooks.
 */
class AccountProvisioner {

    /**
     * Provision a new SaaS account.
     *
     * @param array{
     *   order_id: int,
     *   user_id: int,
     *   product_id: int,
     *   plan: string
     * } $args
     * @return int|false  New account ID or false on failure.
     */
    public function provision( array $args ): int|false {
        global $wpdb;

        $api_key = $this->generate_api_key();

        $result = $wpdb->insert(
            $wpdb->prefix . 'wdd_saas_accounts',
            [
                'user_id'        => (int) $args['user_id'],
                'order_id'       => (int) $args['order_id'],
                'product_id'     => (int) $args['product_id'],
                'plan'           => sanitize_text_field( $args['plan'] ),
                'api_key'        => $api_key,
                'status'         => 'active',
                'provisioned_at' => current_time( 'mysql' ),
            ],
            [ '%d', '%d', '%d', '%s', '%s', '%s', '%s' ]
        );

        if ( ! $result ) {
            return false;
        }

        $account_id = (int) $wpdb->insert_id;

        // Fire external provisioning webhook if configured.
        $webhook_url = get_option( 'wdd_saas_webhook_url' );
        if ( $webhook_url ) {
            $this->fire_webhook( $webhook_url, $account_id, $args, $api_key );
        }

        // Send login email to customer.
        $this->send_access_email( $args['user_id'], $args['product_id'], $api_key );

        return $account_id;
    }

    /**
     * Suspend a SaaS account (e.g. on failed payment).
     *
     * @param int $account_id
     * @return bool
     */
    public function suspend( int $account_id ): bool {
        global $wpdb;

        return (bool) $wpdb->update(
            $wpdb->prefix . 'wdd_saas_accounts',
            [ 'status' => 'suspended' ],
            [ 'id'     => $account_id ],
            [ '%s' ],
            [ '%d' ]
        );
    }

    /**
     * Re-activate a suspended account.
     *
     * @param int $account_id
     * @return bool
     */
    public function activate( int $account_id ): bool {
        global $wpdb;

        return (bool) $wpdb->update(
            $wpdb->prefix . 'wdd_saas_accounts',
            [ 'status' => 'active' ],
            [ 'id'     => $account_id ],
            [ '%s' ],
            [ '%d' ]
        );
    }

    /**
     * Fetch all SaaS accounts for a user.
     *
     * @param int $user_id
     * @return object[]
     */
    public function get_by_user( int $user_id ): array {
        global $wpdb;

        return $wpdb->get_results(
            $wpdb->prepare(
                "SELECT a.*, p.post_title AS product_name
                   FROM {$wpdb->prefix}wdd_saas_accounts a
                   LEFT JOIN {$wpdb->posts} p ON p.ID = a.product_id
                  WHERE a.user_id = %d
                  ORDER BY a.provisioned_at DESC",
                $user_id
            )
        ) ?: [];
    }

    // ─── Private helpers ─────────────────────────────────────────────────────

    /**
     * Generate a secure API key prefixed with 'wdd_'.
     *
     * @return string
     */
    private function generate_api_key(): string {
        return 'wdd_' . bin2hex( random_bytes( 24 ) );
    }

    /**
     * Fire a provisioning webhook to an external SaaS backend.
     *
     * @param string              $url
     * @param int                 $account_id
     * @param array<string,mixed> $args
     * @param string              $api_key
     */
    private function fire_webhook( string $url, int $account_id, array $args, string $api_key ): void {
        $user = get_userdata( $args['user_id'] );

        $body = wp_json_encode( [
            'event'      => 'account.provisioned',
            'account_id' => $account_id,
            'user_email' => $user ? $user->user_email : '',
            'plan'       => $args['plan'],
            'product_id' => $args['product_id'],
            'order_id'   => $args['order_id'],
            'api_key'    => $api_key,
            'timestamp'  => time(),
        ] );

        wp_remote_post( $url, [
            'body'    => $body,
            'headers' => [
                'Content-Type'  => 'application/json',
                'X-WDD-Webhook' => 'account.provisioned',
                'X-WDD-Sig'     => hash_hmac( 'sha256', $body, get_option( 'wdd_webhook_secret', '' ) ),
            ],
            'timeout'  => 10,
            'blocking' => false,
        ] );
    }

    /**
     * Email the customer their SaaS access details.
     *
     * @param int    $user_id
     * @param int    $product_id
     * @param string $api_key
     */
    private function send_access_email( int $user_id, int $product_id, string $api_key ): void {
        $user    = get_userdata( $user_id );
        $product = wc_get_product( $product_id );

        if ( ! $user || ! $product ) {
            return;
        }

        $subject = sprintf(
            /* translators: %s = product name */
            __( 'Your %s access is ready', 'woo-digital-downloads' ),
            $product->get_name()
        );

        $message = sprintf(
            /* translators: 1: first name, 2: product name, 3: api key */
            __(
                "Hi %1\$s,\n\nYour %2\$s account has been provisioned.\n\nAPI Key: %3\$s\n\nKeep this key safe — it grants access to your account.\n\nThank you!",
                'woo-digital-downloads'
            ),
            $user->first_name ?: $user->display_name,
            $product->get_name(),
            $api_key
        );

        wp_mail( $user->user_email, $subject, $message );
    }
}

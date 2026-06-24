<?php
/**
 * Creates and validates signed, expiring download tokens.
 *
 * @package WooDigitalDownloads\Downloads
 */

namespace WooDigitalDownloads\Downloads;

defined( 'ABSPATH' ) || exit;

/**
 * Secure download token system.
 * Each token is a random 64-char hex string stored in wp_wdd_downloads.
 */
class TokenManager {

    /** Default token TTL in seconds (24 hours). */
    private const DEFAULT_TTL = DAY_IN_SECONDS;

    /** Default maximum number of download attempts. */
    private const DEFAULT_MAX = 3;

    /**
     * Create a download token for an order item and store it in the DB.
     *
     * @param \WC_Order              $order
     * @param \WC_Order_Item_Product $item
     * @param \WC_Product            $product
     * @return string|false  The generated token, or false on DB failure.
     */
    public function create_for_order_item(
        \WC_Order $order,
        \WC_Order_Item_Product $item,
        \WC_Product $product
    ): string|false {
        $ttl         = (int) ( get_option( 'wdd_download_expiry_seconds', self::DEFAULT_TTL ) );
        $max         = (int) ( get_option( 'wdd_download_max_count', self::DEFAULT_MAX ) );
        $expires_at  = gmdate( 'Y-m-d H:i:s', time() + $ttl );

        return $this->insert_token( [
            'order_id'   => $order->get_id(),
            'user_id'    => $order->get_customer_id(),
            'product_id' => $product->get_id(),
            'file_id'    => (int) $product->get_meta( '_wdd_primary_file_id' ),
            'max_downloads' => $max,
            'expires_at' => $expires_at,
        ] );
    }

    /**
     * Create a token with explicit parameters.
     *
     * @param array{
     *   order_id: int,
     *   user_id: int,
     *   product_id: int,
     *   file_id: int,
     *   max_downloads?: int,
     *   expires_at?: string
     * } $args
     * @return string|false
     */
    public function create( array $args ): string|false {
        $args['max_downloads'] = $args['max_downloads'] ?? self::DEFAULT_MAX;
        $args['expires_at']    = $args['expires_at']    ?? gmdate( 'Y-m-d H:i:s', time() + self::DEFAULT_TTL );

        return $this->insert_token( $args );
    }

    /**
     * Validate a token and return its record, or null if invalid/expired/exhausted.
     *
     * @param string $token
     * @return object|null
     */
    public function validate( string $token ): ?object {
        global $wpdb;

        $record = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM {$wpdb->prefix}wdd_downloads WHERE token = %s LIMIT 1",
                sanitize_text_field( $token )
            )
        );

        if ( ! $record ) {
            return null;
        }

        if ( strtotime( $record->expires_at ) < time() ) {
            return null; // Token expired.
        }

        if ( (int) $record->download_count >= (int) $record->max_downloads ) {
            return null; // Download limit reached.
        }

        return $record;
    }

    /**
     * Increment the download counter for a token.
     *
     * @param int    $token_id  Primary key from wdd_downloads.
     * @param string $ip
     * @param string $user_agent
     */
    public function record_download( int $token_id, string $ip = '', string $user_agent = '' ): void {
        global $wpdb;

        $wpdb->query(
            $wpdb->prepare(
                "UPDATE {$wpdb->prefix}wdd_downloads
                    SET download_count = download_count + 1
                  WHERE id = %d",
                $token_id
            )
        );

        // Write a log entry.
        $wpdb->insert(
            $wpdb->prefix . 'wdd_download_logs',
            [
                'download_id'   => $token_id,
                'ip_address'    => sanitize_text_field( $ip ),
                'user_agent'    => sanitize_textarea_field( $user_agent ),
                'country_code'  => '',
                'downloaded_at' => current_time( 'mysql' ),
            ],
            [ '%d', '%s', '%s', '%s', '%s' ]
        );
    }

    /**
     * Fetch all download tokens for a user.
     *
     * @param int $user_id
     * @return object[]
     */
    public function get_by_user( int $user_id ): array {
        global $wpdb;

        return $wpdb->get_results(
            $wpdb->prepare(
                "SELECT d.*, p.post_title AS product_name
                   FROM {$wpdb->prefix}wdd_downloads d
                   LEFT JOIN {$wpdb->posts} p ON p.ID = d.product_id
                  WHERE d.user_id = %d
                  ORDER BY d.created_at DESC",
                $user_id
            )
        ) ?: [];
    }

    // ─── Private helpers ─────────────────────────────────────────────────────

    /**
     * Insert a token record and return the token string.
     *
     * @param array<string,mixed> $args
     * @return string|false
     */
    private function insert_token( array $args ): string|false {
        global $wpdb;

        $token = bin2hex( random_bytes( 32 ) ); // 64 hex chars.

        $inserted = $wpdb->insert(
            $wpdb->prefix . 'wdd_downloads',
            [
                'order_id'       => (int) $args['order_id'],
                'user_id'        => (int) $args['user_id'],
                'product_id'     => (int) $args['product_id'],
                'file_id'        => (int) $args['file_id'],
                'token'          => $token,
                'download_count' => 0,
                'max_downloads'  => (int) $args['max_downloads'],
                'expires_at'     => $args['expires_at'],
                'ip_address'     => '',
                'country_code'   => '',
                'created_at'     => current_time( 'mysql' ),
            ],
            [ '%d', '%d', '%d', '%d', '%s', '%d', '%d', '%s', '%s', '%s', '%s' ]
        );

        return $inserted ? $token : false;
    }
}

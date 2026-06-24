<?php
/**
 * Generates and stores license keys.
 *
 * @package WooDigitalDownloads\Licensing
 */

namespace WooDigitalDownloads\Licensing;

defined( 'ABSPATH' ) || exit;

/**
 * Creates license records in the database.
 */
class LicenseGenerator {

    /**
     * Create a new license and return its ID.
     *
     * @param array{
     *   order_id: int,
     *   user_id: int,
     *   product_id: int,
     *   plan_type: string,
     *   activation_limit: int,
     *   expires_at: string|null
     * } $args
     * @return int|false  New license ID on success, false on failure.
     */
    public function create( array $args ): int|false {
        global $wpdb;

        $key = $this->generate_key();

        $now = current_time( 'mysql' );

        $result = $wpdb->insert(
            $wpdb->prefix . 'wdd_licenses',
            [
                'order_id'        => (int) $args['order_id'],
                'user_id'         => (int) $args['user_id'],
                'product_id'      => (int) $args['product_id'],
                'license_key'     => $key,
                'plan_type'       => sanitize_text_field( $args['plan_type'] ),
                'status'          => 'active',
                'activation_limit' => (int) $args['activation_limit'],
                'activated_count'  => 0,
                'expires_at'      => $args['expires_at'] ?? null,
                'created_at'      => $now,
                'updated_at'      => $now,
            ],
            [ '%d', '%d', '%d', '%s', '%s', '%s', '%d', '%d', '%s', '%s', '%s' ]
        );

        return $result ? (int) $wpdb->insert_id : false;
    }

    /**
     * Generate a cryptographically secure license key.
     * Format: XXXX-XXXX-XXXX-XXXX-XXXX (uppercase hex groups).
     *
     * @return string
     */
    private function generate_key(): string {
        $bytes  = random_bytes( 20 );
        $hex    = strtoupper( bin2hex( $bytes ) );
        $groups = str_split( $hex, 8 );

        return implode( '-', $groups );
    }

    /**
     * Fetch a license record by key.
     *
     * @param string $key
     * @return object|null
     */
    public function get_by_key( string $key ): ?object {
        global $wpdb;

        return $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM {$wpdb->prefix}wdd_licenses WHERE license_key = %s LIMIT 1",
                $key
            )
        ) ?: null;
    }

    /**
     * Fetch all licenses for a user.
     *
     * @param int $user_id
     * @return object[]
     */
    public function get_by_user( int $user_id ): array {
        global $wpdb;

        return $wpdb->get_results(
            $wpdb->prepare(
                "SELECT l.*, p.post_title AS product_name
                   FROM {$wpdb->prefix}wdd_licenses l
                   LEFT JOIN {$wpdb->posts} p ON p.ID = l.product_id
                  WHERE l.user_id = %d
                  ORDER BY l.created_at DESC",
                $user_id
            )
        ) ?: [];
    }
}

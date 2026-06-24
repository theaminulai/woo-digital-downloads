<?php
/**
 * Manages product version records (plugin ZIPs).
 *
 * @package WooDigitalDownloads\Updates
 */

namespace WooDigitalDownloads\Updates;

defined( 'ABSPATH' ) || exit;

/**
 * CRUD for wp_wdd_product_versions.
 */
class VersionManager {

    /**
     * Add a new version for a product.
     *
     * @param array{
     *   product_id: int,
     *   version: string,
     *   file_path: string,
     *   requires_wp?: string,
     *   tested_wp?: string,
     *   requires_php?: string,
     *   channel?: string,
     *   changelog?: string
     * } $args
     * @return int|false  Inserted ID or false.
     */
    public function add( array $args ): int|false {
        global $wpdb;

        $file_path = sanitize_text_field( $args['file_path'] );
        $checksum  = file_exists( $file_path ) ? hash_file( 'sha256', $file_path ) : '';

        $result = $wpdb->insert(
            $wpdb->prefix . 'wdd_product_versions',
            [
                'product_id'      => (int) $args['product_id'],
                'version'         => sanitize_text_field( $args['version'] ),
                'file_path'       => $file_path,
                'checksum_sha256' => $checksum,
                'requires_wp'     => sanitize_text_field( $args['requires_wp']  ?? '' ),
                'tested_wp'       => sanitize_text_field( $args['tested_wp']    ?? '' ),
                'requires_php'    => sanitize_text_field( $args['requires_php'] ?? '' ),
                'channel'         => in_array( $args['channel'] ?? 'stable', [ 'stable', 'beta' ], true )
                                        ? $args['channel']
                                        : 'stable',
                'changelog'       => wp_kses_post( $args['changelog'] ?? '' ),
                'released_at'     => current_time( 'mysql' ),
            ],
            [ '%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s' ]
        );

        do_action( 'wdd_version_added', $wpdb->insert_id, $args['product_id'] );

        return $result ? (int) $wpdb->insert_id : false;
    }

    /**
     * Get the latest stable version for a product.
     *
     * @param int    $product_id
     * @param string $channel  'stable' | 'beta'
     * @return object|null
     */
    public function get_latest( int $product_id, string $channel = 'stable' ): ?object {
        global $wpdb;

        return $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM {$wpdb->prefix}wdd_product_versions
                  WHERE product_id = %d AND channel = %s
                  ORDER BY released_at DESC
                  LIMIT 1",
                $product_id,
                $channel
            )
        ) ?: null;
    }

    /**
     * Get all versions for a product, newest first.
     *
     * @param int $product_id
     * @return object[]
     */
    public function get_all( int $product_id ): array {
        global $wpdb;

        return $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM {$wpdb->prefix}wdd_product_versions
                  WHERE product_id = %d
                  ORDER BY released_at DESC",
                $product_id
            )
        ) ?: [];
    }

    /**
     * Get a specific version record by ID.
     *
     * @param int $version_id
     * @return object|null
     */
    public function get_by_id( int $version_id ): ?object {
        global $wpdb;

        return $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM {$wpdb->prefix}wdd_product_versions WHERE id = %d LIMIT 1",
                $version_id
            )
        ) ?: null;
    }

    /**
     * Delete a version record.
     *
     * @param int $version_id
     * @return bool
     */
    public function delete( int $version_id ): bool {
        global $wpdb;

        return (bool) $wpdb->delete(
            $wpdb->prefix . 'wdd_product_versions',
            [ 'id' => $version_id ],
            [ '%d' ]
        );
    }
}

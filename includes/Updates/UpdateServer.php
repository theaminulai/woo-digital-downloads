<?php
/**
 * REST endpoint that serves plugin update information to remote WordPress sites.
 *
 * Remote plugins call:
 *   GET /wp-json/wdd/v1/plugin/update-check?slug={slug}&version={ver}&license_key={key}&domain={domain}
 *
 * @package WooDigitalDownloads\Updates
 */

namespace WooDigitalDownloads\Updates;

defined( 'ABSPATH' ) || exit;

use WooDigitalDownloads\Licensing\LicenseActivator;

/**
 * Handles the plugin update REST API.
 */
class UpdateServer {

    public function __construct() {
        add_action( 'rest_api_init', [ $this, 'register_routes' ] );
    }

    public function register_routes(): void {
        register_rest_route( WDD_API_NAMESPACE, '/plugin/update-check', [
            'methods'             => \WP_REST_Server::READABLE,
            'callback'            => [ $this, 'update_check' ],
            'permission_callback' => '__return_true',
            'args'                => [
                'slug'        => [ 'required' => true,  'sanitize_callback' => 'sanitize_text_field' ],
                'version'     => [ 'required' => true,  'sanitize_callback' => 'sanitize_text_field' ],
                'license_key' => [ 'required' => true,  'sanitize_callback' => 'sanitize_text_field' ],
                'domain'      => [ 'required' => false, 'sanitize_callback' => 'sanitize_text_field' ],
            ],
        ] );

        register_rest_route( WDD_API_NAMESPACE, '/plugin/changelog/(?P<slug>[a-z0-9\-]+)', [
            'methods'             => \WP_REST_Server::READABLE,
            'callback'            => [ $this, 'get_changelog' ],
            'permission_callback' => '__return_true',
        ] );
    }

    /**
     * Returns update information for a plugin slug.
     *
     * @param \WP_REST_Request $request
     * @return \WP_REST_Response|\WP_Error
     */
    public function update_check( \WP_REST_Request $request ): \WP_REST_Response|\WP_Error {
        $slug        = $request->get_param( 'slug' );
        $version     = $request->get_param( 'version' );
        $license_key = $request->get_param( 'license_key' );
        $domain      = $request->get_param( 'domain' ) ?? '';

        // Validate license.
        $activator = new LicenseActivator();
        $result    = $activator->activate( $license_key, $domain ?: 'unknown', 'production' );

        if ( ! $result['success'] ) {
            return new \WP_Error(
                'wdd_invalid_license',
                $result['message'],
                [ 'status' => 403 ]
            );
        }

        // Find the product by slug (stored in product meta _wdd_plugin_slug).
        $product = $this->get_product_by_slug( $slug );

        if ( ! $product ) {
            return new \WP_Error(
                'wdd_plugin_not_found',
                __( 'Plugin not found.', 'woo-digital-downloads' ),
                [ 'status' => 404 ]
            );
        }

        $manager = new VersionManager();
        $latest  = $manager->get_latest( $product->get_id() );

        if ( ! $latest ) {
            return rest_ensure_response( [ 'no_update' => true ] );
        }

        // No update needed if already on latest.
        if ( version_compare( $version, $latest->version, '>=' ) ) {
            return rest_ensure_response( [ 'no_update' => true ] );
        }

        // Build the download URL for the update ZIP.
        $download_url = rest_url( WDD_API_NAMESPACE . '/plugin/download/' . $latest->id );

        return rest_ensure_response( [
            'no_update'    => false,
            'version'      => $latest->version,
            'download_url' => $download_url,
            'requires'     => $latest->requires_wp,
            'tested'       => $latest->tested_wp,
            'requires_php' => $latest->requires_php,
            'changelog'    => $latest->changelog,
            'checksum'     => $latest->checksum_sha256,
        ] );
    }

    /**
     * Returns the full changelog for a plugin slug.
     *
     * @param \WP_REST_Request $request
     * @return \WP_REST_Response|\WP_Error
     */
    public function get_changelog( \WP_REST_Request $request ): \WP_REST_Response|\WP_Error {
        $slug    = $request->get_param( 'slug' );
        $product = $this->get_product_by_slug( $slug );

        if ( ! $product ) {
            return new \WP_Error( 'wdd_not_found', __( 'Plugin not found.', 'woo-digital-downloads' ), [ 'status' => 404 ] );
        }

        $manager  = new VersionManager();
        $versions = $manager->get_all( $product->get_id() );

        return rest_ensure_response( array_map( static function ( object $v ): array {
            return [
                'version'   => $v->version,
                'changelog' => $v->changelog,
                'released'  => $v->released_at,
                'channel'   => $v->channel,
            ];
        }, $versions ) );
    }

    /**
     * Find a WooCommerce product by its plugin slug meta.
     *
     * @param string $slug
     * @return \WC_Product|null
     */
    private function get_product_by_slug( string $slug ): ?\WC_Product {
        $posts = get_posts( [
            'post_type'  => 'product',
            'meta_key'   => '_wdd_plugin_slug',   // phpcs:ignore WordPress.DB.SlowDBQuery
            'meta_value' => $slug,                 // phpcs:ignore WordPress.DB.SlowDBQuery
            'numberposts' => 1,
            'fields'     => 'ids',
        ] );

        if ( empty( $posts ) ) {
            return null;
        }

        return wc_get_product( $posts[0] );
    }
}

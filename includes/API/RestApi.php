<?php
/**
 * Bootstraps all REST API route groups.
 *
 * @package WooDigitalDownloads\API
 */

namespace WooDigitalDownloads\API;

defined( 'ABSPATH' ) || exit;

use WooDigitalDownloads\Licensing\LicenseActivator;
use WooDigitalDownloads\Updates\UpdateServer;
use WooDigitalDownloads\Downloads\TokenManager;
use WooDigitalDownloads\Downloads\DownloadDispatcher;
use WooDigitalDownloads\SaaS\AccountProvisioner;

/**
 * Central REST API registrar.
 *
 * Namespace: wdd/v1
 *
 * Routes registered here:
 *   POST  /license/activate
 *   POST  /license/deactivate
 *   GET   /license/check
 *   GET   /plugin/update-check       (delegated to UpdateServer)
 *   GET   /plugin/changelog/{slug}   (delegated to UpdateServer)
 *   GET   /saas/usage/{api_key}
 */
class RestApi {

    public function __construct() {
        // UpdateServer self-registers; instantiate it here so it's always loaded.
        new UpdateServer();
        // DownloadDispatcher uses rewrite rules, not REST routes.
        new DownloadDispatcher();

        add_action( 'rest_api_init', [ $this, 'register_routes' ] );
    }

    public function register_routes(): void {
        // ── License endpoints ──────────────────────────────────────────────

        register_rest_route( WDD_API_NAMESPACE, '/license/activate', [
            'methods'             => \WP_REST_Server::CREATABLE,
            'callback'            => [ $this, 'license_activate' ],
            'permission_callback' => '__return_true',
            'args'                => [
                'license_key' => [ 'required' => true,  'sanitize_callback' => 'sanitize_text_field' ],
                'domain'      => [ 'required' => true,  'sanitize_callback' => 'sanitize_text_field' ],
                'environment' => [ 'required' => false, 'sanitize_callback' => 'sanitize_text_field', 'default' => 'production' ],
            ],
        ] );

        register_rest_route( WDD_API_NAMESPACE, '/license/deactivate', [
            'methods'             => \WP_REST_Server::CREATABLE,
            'callback'            => [ $this, 'license_deactivate' ],
            'permission_callback' => '__return_true',
            'args'                => [
                'license_key' => [ 'required' => true, 'sanitize_callback' => 'sanitize_text_field' ],
                'domain'      => [ 'required' => true, 'sanitize_callback' => 'sanitize_text_field' ],
            ],
        ] );

        register_rest_route( WDD_API_NAMESPACE, '/license/check', [
            'methods'             => \WP_REST_Server::READABLE,
            'callback'            => [ $this, 'license_check' ],
            'permission_callback' => '__return_true',
            'args'                => [
                'license_key' => [ 'required' => true, 'sanitize_callback' => 'sanitize_text_field' ],
                'domain'      => [ 'required' => false, 'sanitize_callback' => 'sanitize_text_field', 'default' => '' ],
            ],
        ] );

        // ── License admin revoke (requires manage_woocommerce) ─────────────

        register_rest_route( WDD_API_NAMESPACE, '/license/revoke', [
            'methods'             => \WP_REST_Server::CREATABLE,
            'callback'            => [ $this, 'license_revoke' ],
            'permission_callback' => static fn() => current_user_can( 'manage_woocommerce' ),
            'args'                => [
                'license_key' => [ 'required' => true, 'sanitize_callback' => 'sanitize_text_field' ],
            ],
        ] );

        // ── SaaS usage ─────────────────────────────────────────────────────

        register_rest_route( WDD_API_NAMESPACE, '/saas/usage/(?P<api_key>[a-z0-9_]+)', [
            'methods'             => \WP_REST_Server::READABLE,
            'callback'            => [ $this, 'saas_usage' ],
            'permission_callback' => '__return_true',
        ] );
    }

    // ─── Callbacks ────────────────────────────────────────────────────────────

    public function license_activate( \WP_REST_Request $request ): \WP_REST_Response|\WP_Error {
        $activator = new LicenseActivator();
        $result    = $activator->activate(
            $request->get_param( 'license_key' ),
            $request->get_param( 'domain' ),
            $request->get_param( 'environment' )
        );

        if ( ! $result['success'] ) {
            return new \WP_Error( 'wdd_activation_failed', $result['message'], [ 'status' => 403 ] );
        }

        return rest_ensure_response( $result );
    }

    public function license_deactivate( \WP_REST_Request $request ): \WP_REST_Response|\WP_Error {
        $activator = new LicenseActivator();
        $result    = $activator->deactivate(
            $request->get_param( 'license_key' ),
            $request->get_param( 'domain' )
        );

        if ( ! $result['success'] ) {
            return new \WP_Error( 'wdd_deactivation_failed', $result['message'], [ 'status' => 400 ] );
        }

        return rest_ensure_response( $result );
    }

    public function license_check( \WP_REST_Request $request ): \WP_REST_Response|\WP_Error {
        global $wpdb;

        $key = $request->get_param( 'license_key' );

        $license = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM {$wpdb->prefix}wdd_licenses WHERE license_key = %s LIMIT 1",
                $key
            )
        );

        if ( ! $license ) {
            return new \WP_Error( 'wdd_not_found', __( 'Invalid license key.', 'woo-digital-downloads' ), [ 'status' => 404 ] );
        }

        return rest_ensure_response( [
            'status'           => $license->status,
            'plan_type'        => $license->plan_type,
            'activation_limit' => $license->activation_limit,
            'activated_count'  => $license->activated_count,
            'expires_at'       => $license->expires_at,
        ] );
    }

    public function license_revoke( \WP_REST_Request $request ): \WP_REST_Response|\WP_Error {
        global $wpdb;

        $key     = $request->get_param( 'license_key' );
        $updated = $wpdb->update(
            $wpdb->prefix . 'wdd_licenses',
            [ 'status' => 'revoked', 'updated_at' => current_time( 'mysql' ) ],
            [ 'license_key' => $key ],
            [ '%s', '%s' ],
            [ '%s' ]
        );

        if ( false === $updated ) {
            return new \WP_Error( 'wdd_revoke_failed', __( 'Could not revoke license.', 'woo-digital-downloads' ), [ 'status' => 500 ] );
        }

        do_action( 'wdd_license_revoked_via_api', $key );

        return rest_ensure_response( [ 'success' => true, 'message' => __( 'License revoked.', 'woo-digital-downloads' ) ] );
    }

    public function saas_usage( \WP_REST_Request $request ): \WP_REST_Response|\WP_Error {
        global $wpdb;

        $api_key = $request->get_param( 'api_key' );

        $account = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM {$wpdb->prefix}wdd_saas_accounts WHERE api_key = %s LIMIT 1",
                sanitize_text_field( $api_key )
            )
        );

        if ( ! $account ) {
            return new \WP_Error( 'wdd_not_found', __( 'Invalid API key.', 'woo-digital-downloads' ), [ 'status' => 401 ] );
        }

        if ( 'active' !== $account->status ) {
            return new \WP_Error( 'wdd_account_suspended', __( 'Account is not active.', 'woo-digital-downloads' ), [ 'status' => 403 ] );
        }

        return rest_ensure_response( [
            'plan'           => $account->plan,
            'status'         => $account->status,
            'provisioned_at' => $account->provisioned_at,
        ] );
    }
}

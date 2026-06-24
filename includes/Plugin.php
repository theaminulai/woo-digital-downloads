<?php
/**
 * Main plugin class — bootstraps all modules.
 *
 * @package WooDigitalDownloads
 */

namespace WooDigitalDownloads;

defined( 'ABSPATH' ) || exit;

use WooDigitalDownloads\Commerce\OrderHandler;
use WooDigitalDownloads\Commerce\ProductTypes;
use WooDigitalDownloads\API\RestApi;
use WooDigitalDownloads\CustomerDashboard\Dashboard;
use WooDigitalDownloads\Admin\Admin;

/**
 * Plugin singleton.
 */
final class Plugin {

    /** @var Plugin|null */
    private static ?Plugin $instance = null;

    /**
     * Returns the singleton instance.
     */
    public static function instance(): self {
        if ( null === self::$instance ) {
            self::$instance = new self();
            self::$instance->init();
        }

        return self::$instance;
    }

    /** Private constructor — use ::instance(). */
    private function __construct() {}

    /**
     * Boot all modules.
     */
    private function init(): void {
        // Commerce layer (WooCommerce hooks).
        new ProductTypes();
        new OrderHandler();

        // REST API.
        new RestApi();

        // Customer-facing My Account dashboard tabs.
        new Dashboard();

        // Admin screens (only in wp-admin).
        if ( is_admin() ) {
            new Admin();
        }

        do_action( 'wdd_loaded', $this );
    }

    /** Version helper. */
    public function version(): string {
        return WDD_VERSION;
    }
}

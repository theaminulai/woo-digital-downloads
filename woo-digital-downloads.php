
<?php
/**
 * Plugin Name:       WooDigital Downloads
 * Plugin URI:        https://yourwebsite.com/woo-digital-downloads
 * Description:       The ultimate digital downloads extension for WooCommerce — a powerful alternative to Easy Digital Downloads.
 * Version:           1.0.0
 * Author:            WooDigital Downloads
 * Author URI:        WooDigital Team
 * License:           GPL-3.0+
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain:       woo-digital-downloads
 * Domain Path:       /languages
 * Requires PHP:      7.4
 * Requires at least: 6.0
 * WC requires at least: 8.0
 * WC tested up to: 9.0
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

define( 'WDD_FILE',        __FILE__ );
// Retrieve the version dynamically from this file's header comments
$plugin_data = get_file_data( WDD_FILE, array( 'Version' => 'Version' ), 'plugin' );
$plugin_version = ! empty( $plugin_data['Version'] ) ? $plugin_data['Version'] : '0.0.1';
// Define plugin constants
define( 'WDD_VERSION', $plugin_version );
define( 'WDD_PLUGIN_DIR', plugin_dir_path( WDD_FILE ) );
define( 'WDD_PLUGIN_URL', plugin_dir_url( WDD_FILE ) );
define( 'WDD_PLUGIN_FILE', WDD_FILE );

// Check if WooCommerce is active
if ( ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
    add_action( 'admin_notices', function() {
        ?>
        <div class="error">
            <p><strong>WooDigital Downloads</strong> requires WooCommerce to be installed and activated.</p>
        </div>
        <?php
    });
    return;
}

/**
 * PSR-4 autoloader — WooDigitalDownloads\ maps to includes/.
 */
if ( file_exists( WDD_PLUGIN_DIR . 'vendor/autoload.php' ) ) {
	// Dev-only fallback (no Jetpack Autoloader installed yet).
	require_once WDD_PLUGIN_DIR . 'vendor/autoload.php';
} else {
	add_action( 'admin_notices', function (): void {
		echo '<div class="notice notice-error"><p>';
		echo esc_html__(
			'WooDigital Downloads: vendor/ directory not found. Run "composer install" in the plugin directory.',
			'WooDigital Downloads'
		);
		echo '</p></div>';
	} );
	return;
}

// Initialize the plugin
add_action( 'plugins_loaded', function() {
    \WooDigitalDownloads\Woo_Digital_Downloads::instance();
}, 11 );


register_activation_hook( WDD_FILE,   [ \WooDigitalDownloads\Plugin::class, 'activate' ] );
register_deactivation_hook( WDD_FILE, [ \WooDigitalDownloads\Plugin::class, 'deactivate' ] );

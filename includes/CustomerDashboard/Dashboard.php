<?php
/**
 * Adds custom tabs to WooCommerce My Account page.
 *
 * Tabs added:
 *   /my-account/wdd-licenses/
 *   /my-account/wdd-downloads/
 *   /my-account/wdd-api-keys/
 *
 * @package WooDigitalDownloads\CustomerDashboard
 */

namespace WooDigitalDownloads\CustomerDashboard;

defined( 'ABSPATH' ) || exit;

use WooDigitalDownloads\Licensing\LicenseGenerator;
use WooDigitalDownloads\Licensing\LicenseActivator;
use WooDigitalDownloads\Downloads\TokenManager;
use WooDigitalDownloads\SaaS\AccountProvisioner;

/**
 * Registers and renders My Account dashboard tabs.
 */
class Dashboard {

    /** @var array<string,string>  slug => label */
    private array $tabs = [];

    public function __construct() {
        $this->tabs = [
            'wdd-licenses'  => __( 'My Licenses',  'woo-digital-downloads' ),
            'wdd-downloads' => __( 'Downloads',     'woo-digital-downloads' ),
            'wdd-api-keys'  => __( 'API Keys',      'woo-digital-downloads' ),
        ];

        add_filter( 'woocommerce_account_menu_items',     [ $this, 'add_menu_items' ] );
        add_filter( 'woocommerce_get_query_vars',         [ $this, 'add_query_vars' ] );
        add_filter( 'woocommerce_account_menu_item_classes', [ $this, 'menu_item_classes' ], 10, 2 );

        foreach ( array_keys( $this->tabs ) as $slug ) {
            add_action( "woocommerce_account_{$slug}_endpoint", [ $this, 'render_tab' ] );
        }

        add_action( 'init', [ $this, 'add_endpoints' ] );
    }

    /** Register WooCommerce endpoints. */
    public function add_endpoints(): void {
        foreach ( array_keys( $this->tabs ) as $slug ) {
            add_rewrite_endpoint( $slug, EP_ROOT | EP_PAGES );
        }
    }

    /**
     * Inject tabs into the My Account menu.
     *
     * @param array<string,string> $items
     * @return array<string,string>
     */
    public function add_menu_items( array $items ): array {
        // Insert before "Logout".
        $logout = $items['customer-logout'] ?? null;
        unset( $items['customer-logout'] );

        foreach ( $this->tabs as $slug => $label ) {
            $items[ $slug ] = $label;
        }

        if ( $logout ) {
            $items['customer-logout'] = $logout;
        }

        return $items;
    }

    /**
     * Register query vars so WooCommerce can resolve our endpoints.
     *
     * @param array<string,string> $vars
     * @return array<string,string>
     */
    public function add_query_vars( array $vars ): array {
        foreach ( array_keys( $this->tabs ) as $slug ) {
            $vars[ $slug ] = $slug;
        }
        return $vars;
    }

    /**
     * Add active CSS class to menu items.
     *
     * @param string[] $classes
     * @param string   $endpoint
     * @return string[]
     */
    public function menu_item_classes( array $classes, string $endpoint ): array {
        global $wp;

        if ( isset( $wp->query_vars[ $endpoint ] ) ) {
            $classes[] = 'is-active';
        }

        return $classes;
    }

    /**
     * Dispatch rendering to the correct tab method.
     */
    public function render_tab(): void {
        global $wp;

        $active = '';
        foreach ( array_keys( $this->tabs ) as $slug ) {
            if ( isset( $wp->query_vars[ $slug ] ) ) {
                $active = $slug;
                break;
            }
        }

        switch ( $active ) {
            case 'wdd-licenses':
                $this->render_licenses_tab();
                break;
            case 'wdd-downloads':
                $this->render_downloads_tab();
                break;
            case 'wdd-api-keys':
                $this->render_api_keys_tab();
                break;
        }
    }

    // ─── Tab renderers ────────────────────────────────────────────────────────

    private function render_licenses_tab(): void {
        $user_id  = get_current_user_id();
        $licenses = ( new LicenseGenerator() )->get_by_user( $user_id );

        if ( empty( $licenses ) ) {
            echo '<p>' . esc_html__( 'You have no licenses yet.', 'woo-digital-downloads' ) . '</p>';
            return;
        }

        echo '<table class="woocommerce-table shop_table wdd-licenses-table">';
        echo '<thead><tr>'
            . '<th>' . esc_html__( 'Product',    'woo-digital-downloads' ) . '</th>'
            . '<th>' . esc_html__( 'License Key','woo-digital-downloads' ) . '</th>'
            . '<th>' . esc_html__( 'Status',     'woo-digital-downloads' ) . '</th>'
            . '<th>' . esc_html__( 'Sites Used', 'woo-digital-downloads' ) . '</th>'
            . '<th>' . esc_html__( 'Expires',    'woo-digital-downloads' ) . '</th>'
            . '</tr></thead><tbody>';

        foreach ( $licenses as $license ) {
            $expires = $license->expires_at
                ? esc_html( date_i18n( get_option( 'date_format' ), strtotime( $license->expires_at ) ) )
                : esc_html__( 'Lifetime', 'woo-digital-downloads' );

            printf(
                '<tr>
                    <td>%s</td>
                    <td><code>%s</code></td>
                    <td><span class="wdd-status wdd-status--%s">%s</span></td>
                    <td>%s / %s</td>
                    <td>%s</td>
                </tr>',
                esc_html( $license->product_name ?? '' ),
                esc_html( $license->license_key ),
                esc_attr( $license->status ),
                esc_html( ucfirst( $license->status ) ),
                esc_html( $license->activated_count ),
                'unlimited' === $license->plan_type ? esc_html__( '∞', 'woo-digital-downloads' ) : esc_html( $license->activation_limit ),
                $expires
            );
        }

        echo '</tbody></table>';
    }

    private function render_downloads_tab(): void {
        $user_id   = get_current_user_id();
        $downloads = ( new TokenManager() )->get_by_user( $user_id );

        if ( empty( $downloads ) ) {
            echo '<p>' . esc_html__( 'No downloads available.', 'woo-digital-downloads' ) . '</p>';
            return;
        }

        echo '<table class="woocommerce-table shop_table wdd-downloads-table">';
        echo '<thead><tr>'
            . '<th>' . esc_html__( 'Product',     'woo-digital-downloads' ) . '</th>'
            . '<th>' . esc_html__( 'Downloads',   'woo-digital-downloads' ) . '</th>'
            . '<th>' . esc_html__( 'Expires',     'woo-digital-downloads' ) . '</th>'
            . '<th>' . esc_html__( 'Action',      'woo-digital-downloads' ) . '</th>'
            . '</tr></thead><tbody>';

        foreach ( $downloads as $dl ) {
            $remaining  = max( 0, (int) $dl->max_downloads - (int) $dl->download_count );
            $expired    = strtotime( $dl->expires_at ) < time();
            $exhausted  = $remaining <= 0;
            $url        = home_url( 'wdd-download/' . $dl->token );

            printf(
                '<tr>
                    <td>%s</td>
                    <td>%d / %d</td>
                    <td>%s</td>
                    <td>%s</td>
                </tr>',
                esc_html( $dl->product_name ?? '' ),
                esc_html( $dl->download_count ),
                esc_html( $dl->max_downloads ),
                esc_html( date_i18n( get_option( 'date_format' ), strtotime( $dl->expires_at ) ) ),
                ( $expired || $exhausted )
                    ? '<span class="wdd-expired">' . esc_html__( 'Expired', 'woo-digital-downloads' ) . '</span>'
                    : '<a href="' . esc_url( $url ) . '" class="button">' . esc_html__( 'Download', 'woo-digital-downloads' ) . '</a>'
            );
        }

        echo '</tbody></table>';
    }

    private function render_api_keys_tab(): void {
        $user_id  = get_current_user_id();
        $accounts = ( new AccountProvisioner() )->get_by_user( $user_id );

        if ( empty( $accounts ) ) {
            echo '<p>' . esc_html__( 'No API keys found.', 'woo-digital-downloads' ) . '</p>';
            return;
        }

        echo '<table class="woocommerce-table shop_table wdd-api-keys-table">';
        echo '<thead><tr>'
            . '<th>' . esc_html__( 'Product', 'woo-digital-downloads' ) . '</th>'
            . '<th>' . esc_html__( 'Plan',    'woo-digital-downloads' ) . '</th>'
            . '<th>' . esc_html__( 'API Key', 'woo-digital-downloads' ) . '</th>'
            . '<th>' . esc_html__( 'Status',  'woo-digital-downloads' ) . '</th>'
            . '</tr></thead><tbody>';

        foreach ( $accounts as $account ) {
            printf(
                '<tr>
                    <td>%s</td>
                    <td>%s</td>
                    <td><code class="wdd-api-key">%s</code></td>
                    <td><span class="wdd-status wdd-status--%s">%s</span></td>
                </tr>',
                esc_html( $account->product_name ?? '' ),
                esc_html( ucfirst( $account->plan ) ),
                esc_html( $account->api_key ),
                esc_attr( $account->status ),
                esc_html( ucfirst( $account->status ) )
            );
        }

        echo '</tbody></table>';
    }
}

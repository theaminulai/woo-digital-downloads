<?php
/**
 * Registers custom WooCommerce product types for digital downloads.
 *
 * @package WooDigitalDownloads\Commerce
 */

namespace WooDigitalDownloads\Commerce;

defined( 'ABSPATH' ) || exit;

/**
 * Handles WooCommerce product type registration and meta boxes.
 */
class ProductTypes {

    /** Product type slug for WordPress plugins. */
    public const TYPE_PLUGIN = 'wdd_plugin';

    /** Product type slug for SaaS products. */
    public const TYPE_SAAS = 'wdd_saas';

    /** Product type slug for bundles (plugin + SaaS). */
    public const TYPE_BUNDLE = 'wdd_bundle';

    public function __construct() {
        // Register product type classes.
        add_filter( 'woocommerce_product_class', [ $this, 'product_class' ], 10, 2 );

        // Add our types to the product type dropdown.
        add_filter( 'product_type_selector', [ $this, 'add_product_types' ] );

        // Show/hide relevant WooCommerce panels for our types.
        add_filter( 'woocommerce_product_data_tabs', [ $this, 'product_data_tabs' ] );

        // Enqueue admin JS to toggle panels.
        add_action( 'admin_footer', [ $this, 'product_type_js' ] );

        // Make our product types always "virtual" (no shipping).
        add_filter( 'woocommerce_product_is_virtual', [ $this, 'force_virtual' ], 10, 2 );
    }

    /**
     * Map product type slug to PHP class.
     *
     * @param string $classname  Default WC class.
     * @param string $product_type  Type slug.
     * @return string
     */
    public function product_class( string $classname, string $product_type ): string {
        $map = [
            self::TYPE_PLUGIN => \WC_Product_Simple::class,
            self::TYPE_SAAS   => \WC_Product_Simple::class,
            self::TYPE_BUNDLE => \WC_Product_Simple::class,
        ];

        return $map[ $product_type ] ?? $classname;
    }

    /**
     * Append our product types to the type selector.
     *
     * @param array<string,string> $types
     * @return array<string,string>
     */
    public function add_product_types( array $types ): array {
        $types[ self::TYPE_PLUGIN ] = __( 'WordPress Plugin', 'woo-digital-downloads' );
        $types[ self::TYPE_SAAS ]   = __( 'SaaS Product',     'woo-digital-downloads' );
        $types[ self::TYPE_BUNDLE ] = __( 'Bundle (Plugin + SaaS)', 'woo-digital-downloads' );

        return $types;
    }

    /**
     * Add a custom "Digital Downloads" tab to the product data panel.
     *
     * @param array<string,array<string,mixed>> $tabs
     * @return array<string,array<string,mixed>>
     */
    public function product_data_tabs( array $tabs ): array {
        $tabs['wdd_settings'] = [
            'label'    => __( 'Digital Downloads', 'woo-digital-downloads' ),
            'target'   => 'wdd_product_data',
            'class'    => [ 'show_if_wdd_plugin', 'show_if_wdd_saas', 'show_if_wdd_bundle' ],
            'priority' => 60,
        ];

        return $tabs;
    }

    /**
     * Admin JS: show/hide standard WC panels based on our product types.
     */
    public function product_type_js(): void {
        $screen = get_current_screen();

        if ( ! $screen || 'product' !== $screen->id ) {
            return;
        }

        $types = [ self::TYPE_PLUGIN, self::TYPE_SAAS, self::TYPE_BUNDLE ];
        $types_js = implode( "','", array_map( 'esc_js', $types ) );
        ?>
        <script>
        ( function( $ ) {
            'use strict';

            var wddTypes = [ '<?php echo $types_js; // phpcs:ignore ?>' ];

            function wddToggle() {
                var type = $( 'select#product-type' ).val();

                if ( wddTypes.indexOf( type ) !== -1 ) {
                    // Hide panels that don't apply.
                    $( '#general_product_data .pricing' ).show();
                    $( 'li.linked_product_options, li.attribute_options, li.variation_options' ).hide();
                    $( '.shipping_tab' ).hide();
                } else {
                    $( 'li.linked_product_options, li.attribute_options' ).show();
                    $( '.shipping_tab' ).show();
                }
            }

            $( document ).ready( wddToggle );
            $( document ).on( 'change', 'select#product-type', wddToggle );
        } )( jQuery );
        </script>
        <?php
    }

    /**
     * Our product types are always virtual (never shipped).
     *
     * @param bool        $is_virtual
     * @param \WC_Product $product
     * @return bool
     */
    public function force_virtual( bool $is_virtual, \WC_Product $product ): bool {
        return in_array( $product->get_type(), [ self::TYPE_PLUGIN, self::TYPE_SAAS, self::TYPE_BUNDLE ], true )
            ? true
            : $is_virtual;
    }
}

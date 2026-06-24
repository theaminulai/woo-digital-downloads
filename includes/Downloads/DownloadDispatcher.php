<?php
/**
 * Serves protected files via PHP — no direct file URL exposed.
 *
 * @package WooDigitalDownloads\Downloads
 */

namespace WooDigitalDownloads\Downloads;

defined( 'ABSPATH' ) || exit;

/**
 * Handles the secure file-download endpoint.
 *
 * Usage: /wdd-download/{token}
 */
class DownloadDispatcher {

    /** Query var used in the rewrite rule. */
    private const QUERY_VAR = 'wdd_download_token';

    public function __construct() {
        add_action( 'init',              [ $this, 'add_rewrite_rule' ] );
        add_filter( 'query_vars',        [ $this, 'add_query_var' ] );
        add_action( 'template_redirect', [ $this, 'maybe_dispatch' ] );
    }

    /** Register the pretty URL: /wdd-download/{token} */
    public function add_rewrite_rule(): void {
        add_rewrite_rule(
            '^wdd-download/([a-f0-9]{64})/?$',
            'index.php?' . self::QUERY_VAR . '=$matches[1]',
            'top'
        );
    }

    /** @param string[] $vars */
    public function add_query_var( array $vars ): array {
        $vars[] = self::QUERY_VAR;
        return $vars;
    }

    /** Intercept the request and serve the file if the token is valid. */
    public function maybe_dispatch(): void {
        $token = get_query_var( self::QUERY_VAR );

        if ( ! $token ) {
            return;
        }

        $manager = new TokenManager();
        $record  = $manager->validate( $token );

        if ( ! $record ) {
            wp_die(
                esc_html__( 'This download link has expired or is no longer valid.', 'woo-digital-downloads' ),
                esc_html__( 'Download Error', 'woo-digital-downloads' ),
                [ 'response' => 403 ]
            );
        }

        $file_path = $this->resolve_file_path( (int) $record->file_id, (int) $record->product_id );

        if ( ! $file_path || ! file_exists( $file_path ) ) {
            wp_die(
                esc_html__( 'The requested file could not be found.', 'woo-digital-downloads' ),
                esc_html__( 'File Not Found', 'woo-digital-downloads' ),
                [ 'response' => 404 ]
            );
        }

        // Record the download event.
        $ip         = sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ?? '' ) );
        $user_agent = sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ?? '' ) );
        $manager->record_download( (int) $record->id, $ip, $user_agent );

        do_action( 'wdd_before_file_download', $record, $file_path );

        $this->stream_file( $file_path );
    }

    /**
     * Resolve a WooCommerce download file path.
     *
     * @param int $file_id    WDD internal file ID (stored in meta).
     * @param int $product_id WooCommerce product ID.
     * @return string|null    Absolute path on disk, or null if not found.
     */
    private function resolve_file_path( int $file_id, int $product_id ): ?string {
        // First try WDD-managed file path stored in product meta.
        $product = wc_get_product( $product_id );
        if ( $product ) {
            $file_path = $product->get_meta( '_wdd_file_path' );
            if ( $file_path && file_exists( $file_path ) ) {
                return $file_path;
            }

            // Fall back to WooCommerce downloadable files.
            foreach ( $product->get_downloads() as $dl ) {
                $path = $dl->get_file();
                // Convert URL to path if needed.
                if ( filter_var( $path, FILTER_VALIDATE_URL ) ) {
                    $path = $this->url_to_path( $path );
                }
                if ( $path && file_exists( $path ) ) {
                    return $path;
                }
            }
        }

        return null;
    }

    /**
     * Convert a local URL to an absolute server path.
     *
     * @param string $url
     * @return string|null
     */
    private function url_to_path( string $url ): ?string {
        $site_url  = site_url( '/' );
        $site_path = ABSPATH;

        if ( str_starts_with( $url, $site_url ) ) {
            return $site_path . substr( $url, strlen( $site_url ) );
        }

        return null;
    }

    /**
     * Stream a file to the browser with appropriate headers.
     *
     * @param string $file_path
     */
    private function stream_file( string $file_path ): void {
        $filename  = basename( $file_path );
        $file_size = filesize( $file_path );
        $mime      = mime_content_type( $file_path ) ?: 'application/octet-stream';

        // Clear any output buffering.
        if ( ob_get_level() ) {
            ob_end_clean();
        }

        nocache_headers();

        header( 'Content-Type: ' . $mime );
        header( 'Content-Disposition: attachment; filename="' . $filename . '"' );
        header( 'Content-Length: ' . $file_size );
        header( 'X-Content-Type-Options: nosniff' );

        readfile( $file_path ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_readfile
        exit;
    }
}

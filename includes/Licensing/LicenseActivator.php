<?php
/**
 * Handles domain activation and deactivation for licenses.
 *
 * @package WooDigitalDownloads\Licensing
 */

namespace WooDigitalDownloads\Licensing;

defined( 'ABSPATH' ) || exit;

/**
 * Activates and deactivates site licenses.
 */
class LicenseActivator {

    /** Domains that are always exempt from site-limit counting. */
    private const EXEMPT_PATTERNS = [
        'localhost',
        '127.0.0.1',
        '::1',
        '.local',
        '.test',
        '.staging.',
        'staging.',
    ];

    /**
     * Activate a license on a domain.
     *
     * @param string $license_key
     * @param string $domain        The site URL being activated.
     * @param string $environment   'production' | 'staging' | 'local'
     * @return array{success: bool, message: string, data?: array<string,mixed>}
     */
    public function activate( string $license_key, string $domain, string $environment = 'production' ): array {
        global $wpdb;

        $license = $this->get_valid_license( $license_key );

        if ( is_string( $license ) ) {
            return [ 'success' => false, 'message' => $license ];
        }

        $domain = $this->normalize_domain( $domain );

        // Check if this domain is already activated.
        $existing = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT id FROM {$wpdb->prefix}wdd_license_activations
                  WHERE license_id = %d AND domain = %s LIMIT 1",
                $license->id,
                $domain
            )
        );

        if ( $existing ) {
            // Update last_check timestamp.
            $wpdb->update(
                $wpdb->prefix . 'wdd_license_activations',
                [ 'last_check' => current_time( 'mysql' ) ],
                [ 'id' => $existing->id ],
                [ '%s' ],
                [ '%d' ]
            );

            return [
                'success' => true,
                'message' => __( 'License already active on this domain.', 'woo-digital-downloads' ),
                'data'    => $this->license_data( $license ),
            ];
        }

        // For production environments, check the activation limit.
        if ( 'production' === $environment && ! $this->is_exempt( $domain ) ) {
            if ( (int) $license->activated_count >= (int) $license->activation_limit
                && 'unlimited' !== $license->plan_type ) {
                return [
                    'success' => false,
                    'message' => sprintf(
                        /* translators: %1$d = current limit, %2$d = max allowed */
                        __( 'Activation limit reached (%1$d / %2$d sites).', 'woo-digital-downloads' ),
                        $license->activated_count,
                        $license->activation_limit
                    ),
                ];
            }
        }

        // Insert activation.
        $wpdb->insert(
            $wpdb->prefix . 'wdd_license_activations',
            [
                'license_id'   => (int) $license->id,
                'domain'       => $domain,
                'ip_address'   => $this->get_ip(),
                'environment'  => sanitize_text_field( $environment ),
                'activated_at' => current_time( 'mysql' ),
                'last_check'   => current_time( 'mysql' ),
            ],
            [ '%d', '%s', '%s', '%s', '%s', '%s' ]
        );

        // Increment counter only for production, non-exempt domains.
        if ( 'production' === $environment && ! $this->is_exempt( $domain ) ) {
            $wpdb->query(
                $wpdb->prepare(
                    "UPDATE {$wpdb->prefix}wdd_licenses
                        SET activated_count = activated_count + 1,
                            updated_at = %s
                      WHERE id = %d",
                    current_time( 'mysql' ),
                    $license->id
                )
            );
        }

        do_action( 'wdd_license_activated', $license->id, $domain );

        return [
            'success' => true,
            'message' => __( 'License activated successfully.', 'woo-digital-downloads' ),
            'data'    => $this->license_data( $license ),
        ];
    }

    /**
     * Deactivate a license on a specific domain.
     *
     * @param string $license_key
     * @param string $domain
     * @return array{success: bool, message: string}
     */
    public function deactivate( string $license_key, string $domain ): array {
        global $wpdb;

        $license = $this->get_valid_license( $license_key );

        if ( is_string( $license ) ) {
            return [ 'success' => false, 'message' => $license ];
        }

        $domain = $this->normalize_domain( $domain );

        $activation = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM {$wpdb->prefix}wdd_license_activations
                  WHERE license_id = %d AND domain = %s LIMIT 1",
                $license->id,
                $domain
            )
        );

        if ( ! $activation ) {
            return [
                'success' => false,
                'message' => __( 'Domain not found for this license.', 'woo-digital-downloads' ),
            ];
        }

        $wpdb->delete(
            $wpdb->prefix . 'wdd_license_activations',
            [ 'id' => $activation->id ],
            [ '%d' ]
        );

        // Decrement counter for production, non-exempt domains.
        if ( 'production' === $activation->environment && ! $this->is_exempt( $domain ) ) {
            $wpdb->query(
                $wpdb->prepare(
                    "UPDATE {$wpdb->prefix}wdd_licenses
                        SET activated_count = GREATEST(0, activated_count - 1),
                            updated_at = %s
                      WHERE id = %d",
                    current_time( 'mysql' ),
                    $license->id
                )
            );
        }

        do_action( 'wdd_license_deactivated', $license->id, $domain );

        return [
            'success' => true,
            'message' => __( 'License deactivated successfully.', 'woo-digital-downloads' ),
        ];
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    /**
     * Fetch and validate a license record.
     *
     * @param string $key
     * @return object|string  DB row on success, error message string on failure.
     */
    private function get_valid_license( string $key ): object|string {
        global $wpdb;

        $license = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM {$wpdb->prefix}wdd_licenses WHERE license_key = %s LIMIT 1",
                sanitize_text_field( $key )
            )
        );

        if ( ! $license ) {
            return __( 'Invalid license key.', 'woo-digital-downloads' );
        }

        if ( 'active' !== $license->status ) {
            return sprintf(
                /* translators: %s = status */
                __( 'License is %s.', 'woo-digital-downloads' ),
                $license->status
            );
        }

        if ( $license->expires_at && strtotime( $license->expires_at ) < time() ) {
            return __( 'License has expired.', 'woo-digital-downloads' );
        }

        return $license;
    }

    /** Strip protocol and trailing slashes from a domain. */
    private function normalize_domain( string $domain ): string {
        $domain = preg_replace( '#^https?://#i', '', $domain );
        return rtrim( $domain, '/' );
    }

    /** Check whether a domain matches an exempt pattern. */
    private function is_exempt( string $domain ): bool {
        foreach ( self::EXEMPT_PATTERNS as $pattern ) {
            if ( str_contains( $domain, $pattern ) ) {
                return true;
            }
        }
        return false;
    }

    /** Get the current visitor IP address. */
    private function get_ip(): string {
        foreach ( [ 'HTTP_CF_CONNECTING_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR' ] as $key ) {
            if ( ! empty( $_SERVER[ $key ] ) ) {
                return sanitize_text_field( wp_unslash( $_SERVER[ $key ] ) );
            }
        }
        return '';
    }

    /**
     * Build the public license data array returned to the client plugin.
     *
     * @param object $license
     * @return array<string,mixed>
     */
    private function license_data( object $license ): array {
        return [
            'license_key'     => $license->license_key,
            'status'          => $license->status,
            'plan_type'       => $license->plan_type,
            'activation_limit' => $license->activation_limit,
            'activated_count'  => $license->activated_count,
            'expires_at'      => $license->expires_at,
        ];
    }
}

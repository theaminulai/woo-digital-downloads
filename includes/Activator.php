<?php
/**
 * Plugin activation / deactivation — creates & removes custom DB tables.
 *
 * @package WooDigitalDownloads
 */

namespace WooDigitalDownloads;

defined( 'ABSPATH' ) || exit;

/**
 * Handles activation, deactivation, and DB schema.
 */
class Activator {

    /** DB version option key. */
    private const DB_VERSION_KEY = 'wdd_db_version';

    /** Current DB schema version. */
    private const DB_VERSION = '1.0.0';

    /**
     * Runs on plugin activation.
     */
    public static function activate(): void {
        self::create_tables();
        self::schedule_crons();

        update_option( self::DB_VERSION_KEY, self::DB_VERSION );

        // Flush rewrite rules so our custom endpoints work immediately.
        flush_rewrite_rules();
    }

    /**
     * Runs on plugin deactivation.
     */
    public static function deactivate(): void {
        wp_clear_scheduled_hook( 'wdd_check_expired_licenses' );
        wp_clear_scheduled_hook( 'wdd_process_dunning' );

        flush_rewrite_rules();
    }

    /**
     * Creates all custom database tables using dbDelta().
     */
    public static function create_tables(): void {
        global $wpdb;

        $charset = $wpdb->get_charset_collate();

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        // ── Licenses ──────────────────────────────────────────────────────────
        dbDelta( "CREATE TABLE {$wpdb->prefix}wdd_licenses (
            id              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            order_id        BIGINT UNSIGNED NOT NULL DEFAULT 0,
            user_id         BIGINT UNSIGNED NOT NULL DEFAULT 0,
            product_id      BIGINT UNSIGNED NOT NULL DEFAULT 0,
            license_key     VARCHAR(64)  NOT NULL DEFAULT '',
            plan_type       ENUM('single','multi','unlimited','lifetime') NOT NULL DEFAULT 'single',
            status          ENUM('active','expired','revoked','suspended') NOT NULL DEFAULT 'active',
            activation_limit INT UNSIGNED NOT NULL DEFAULT 1,
            activated_count  INT UNSIGNED NOT NULL DEFAULT 0,
            expires_at      DATETIME NULL DEFAULT NULL,
            created_at      DATETIME NOT NULL,
            updated_at      DATETIME NOT NULL,
            PRIMARY KEY  (id),
            UNIQUE KEY  license_key (license_key),
            KEY idx_user_id   (user_id),
            KEY idx_order_id  (order_id),
            KEY idx_product   (product_id),
            KEY idx_status    (status)
        ) $charset;" );

        // ── License activations (one row per activated domain) ────────────────
        dbDelta( "CREATE TABLE {$wpdb->prefix}wdd_license_activations (
            id              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            license_id      BIGINT UNSIGNED NOT NULL,
            domain          VARCHAR(255) NOT NULL DEFAULT '',
            ip_address      VARCHAR(45)  NOT NULL DEFAULT '',
            environment     ENUM('production','staging','local') NOT NULL DEFAULT 'production',
            activated_at    DATETIME NOT NULL,
            last_check      DATETIME NULL DEFAULT NULL,
            PRIMARY KEY  (id),
            KEY idx_license_id (license_id),
            KEY idx_domain     (domain)
        ) $charset;" );

        // ── Secure download tokens ────────────────────────────────────────────
        dbDelta( "CREATE TABLE {$wpdb->prefix}wdd_downloads (
            id              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            order_id        BIGINT UNSIGNED NOT NULL DEFAULT 0,
            user_id         BIGINT UNSIGNED NOT NULL DEFAULT 0,
            product_id      BIGINT UNSIGNED NOT NULL DEFAULT 0,
            file_id         BIGINT UNSIGNED NOT NULL DEFAULT 0,
            token           VARCHAR(128) NOT NULL DEFAULT '',
            download_count  INT UNSIGNED NOT NULL DEFAULT 0,
            max_downloads   INT UNSIGNED NOT NULL DEFAULT 3,
            expires_at      DATETIME NOT NULL,
            ip_address      VARCHAR(45) NOT NULL DEFAULT '',
            country_code    VARCHAR(2)  NOT NULL DEFAULT '',
            created_at      DATETIME NOT NULL,
            PRIMARY KEY  (id),
            UNIQUE KEY  token (token),
            KEY idx_order_user (order_id, user_id)
        ) $charset;" );

        // ── Download logs (one row per download attempt) ──────────────────────
        dbDelta( "CREATE TABLE {$wpdb->prefix}wdd_download_logs (
            id              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            download_id     BIGINT UNSIGNED NOT NULL,
            ip_address      VARCHAR(45) NOT NULL DEFAULT '',
            user_agent      TEXT,
            country_code    VARCHAR(2)  NOT NULL DEFAULT '',
            downloaded_at   DATETIME NOT NULL,
            PRIMARY KEY  (id),
            KEY idx_download_id (download_id),
            KEY idx_downloaded  (downloaded_at)
        ) $charset;" );

        // ── Product versions (plugin ZIP history) ─────────────────────────────
        dbDelta( "CREATE TABLE {$wpdb->prefix}wdd_product_versions (
            id              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            product_id      BIGINT UNSIGNED NOT NULL,
            version         VARCHAR(20) NOT NULL DEFAULT '',
            file_path       TEXT        NOT NULL,
            checksum_sha256 VARCHAR(64) NOT NULL DEFAULT '',
            requires_wp     VARCHAR(10) NOT NULL DEFAULT '',
            tested_wp       VARCHAR(10) NOT NULL DEFAULT '',
            requires_php    VARCHAR(10) NOT NULL DEFAULT '',
            channel         ENUM('stable','beta') NOT NULL DEFAULT 'stable',
            changelog       LONGTEXT,
            released_at     DATETIME NOT NULL,
            PRIMARY KEY  (id),
            KEY idx_product_version (product_id, version),
            KEY idx_channel         (channel)
        ) $charset;" );

        // ── Subscriptions ─────────────────────────────────────────────────────
        dbDelta( "CREATE TABLE {$wpdb->prefix}wdd_subscriptions (
            id              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            user_id         BIGINT UNSIGNED NOT NULL DEFAULT 0,
            product_id      BIGINT UNSIGNED NOT NULL DEFAULT 0,
            order_id        BIGINT UNSIGNED NOT NULL DEFAULT 0,
            license_id      BIGINT UNSIGNED NOT NULL DEFAULT 0,
            status          ENUM('active','cancelled','paused','expired','past_due') NOT NULL DEFAULT 'active',
            billing_cycle   ENUM('monthly','yearly') NOT NULL DEFAULT 'yearly',
            starts_at       DATETIME NOT NULL,
            expires_at      DATETIME NULL DEFAULT NULL,
            renewal_at      DATETIME NULL DEFAULT NULL,
            cancelled_at    DATETIME NULL DEFAULT NULL,
            PRIMARY KEY  (id),
            KEY idx_user_id   (user_id),
            KEY idx_status    (status),
            KEY idx_renewal   (renewal_at)
        ) $charset;" );

        // ── SaaS accounts ─────────────────────────────────────────────────────
        dbDelta( "CREATE TABLE {$wpdb->prefix}wdd_saas_accounts (
            id              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            user_id         BIGINT UNSIGNED NOT NULL DEFAULT 0,
            order_id        BIGINT UNSIGNED NOT NULL DEFAULT 0,
            product_id      BIGINT UNSIGNED NOT NULL DEFAULT 0,
            plan            VARCHAR(50)  NOT NULL DEFAULT '',
            api_key         VARCHAR(128) NOT NULL DEFAULT '',
            status          ENUM('active','suspended','cancelled') NOT NULL DEFAULT 'active',
            provisioned_at  DATETIME NOT NULL,
            PRIMARY KEY  (id),
            UNIQUE KEY  api_key           (api_key),
            KEY idx_user_product (user_id, product_id)
        ) $charset;" );
    }

    /**
     * Register WP-Cron recurring events.
     */
    private static function schedule_crons(): void {
        if ( ! wp_next_scheduled( 'wdd_check_expired_licenses' ) ) {
            wp_schedule_event( time(), 'daily', 'wdd_check_expired_licenses' );
        }

        if ( ! wp_next_scheduled( 'wdd_process_dunning' ) ) {
            wp_schedule_event( time(), 'twicedaily', 'wdd_process_dunning' );
        }
    }

    /**
     * Run a lightweight DB upgrade if the stored version is older.
     */
    public static function maybe_upgrade(): void {
        $stored = get_option( self::DB_VERSION_KEY, '0.0.0' );

        if ( version_compare( $stored, self::DB_VERSION, '<' ) ) {
            self::create_tables();
            update_option( self::DB_VERSION_KEY, self::DB_VERSION );
        }
    }
}

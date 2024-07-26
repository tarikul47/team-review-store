<?php
namespace Review_Store\Inc\Database;

class Database
{
    private static $instance = null;

    private function __construct()
    {
        // private constructor to prevent direct instantiation
    }

    public static function get_instance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Check if a table exists in the database.
     */
    public static function table_exists($table_name)
    {
        global $wpdb;
        $query = $wpdb->prepare("SHOW TABLES LIKE %s", $wpdb->esc_like($table_name));
        return $wpdb->get_var($query) == $table_name;
    }

    /**
     * Create required tables for the plugin.
     */
    public static function create_tables()
    {
        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();

        $tables = [
            "external_profile" => "CREATE TABLE {$wpdb->prefix}external_profile (
                external_profile_id BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(255) NOT NULL,
                email VARCHAR(255) NOT NULL,
                city VARCHAR(255),
                bio TEXT,
                photo_url VARCHAR(255),
                department VARCHAR(255),
                work_title VARCHAR(255),
                location VARCHAR(255),
                organization VARCHAR(255),
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                product_id BIGINT(20) UNSIGNED
            ) $charset_collate;",

            "external_profile_claims" => "CREATE TABLE {$wpdb->prefix}external_profile_claims (
                claim_id BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                external_profile_id BIGINT(20) UNSIGNED NOT NULL,
                claimer_user_id BIGINT(20) UNSIGNED NOT NULL,
                claim_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                claim_status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX (external_profile_id),
                INDEX (claimer_user_id),
                UNIQUE KEY unique_claim (external_profile_id, claimer_user_id)
            ) $charset_collate;",

            // "external_profile_memberships" => "CREATE TABLE {$wpdb->prefix}external_profile_memberships (
            //     membership_id BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            //     user_id BIGINT(20) UNSIGNED NOT NULL,
            //     membership_start_date DATE,
            //     membership_end_date DATE,
            //     membership_status ENUM('active', 'inactive', 'expired') DEFAULT 'active',
            //     INDEX (user_id)
            // ) $charset_collate;",

            "reviews" => "CREATE TABLE {$wpdb->prefix}reviews (
                review_id BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                external_profile_id BIGINT(20) UNSIGNED NOT NULL,
                reviewer_user_id BIGINT(20) UNSIGNED NOT NULL,
                rating INT,
                status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX (external_profile_id),
                INDEX (reviewer_user_id)
            ) $charset_collate;",

            "review_meta" => "CREATE TABLE {$wpdb->prefix}review_meta (
                meta_id BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                review_id BIGINT(20) UNSIGNED NOT NULL,
                meta_key VARCHAR(255),
                meta_value TEXT,
                INDEX (review_id),
                INDEX (meta_key)
            ) $charset_collate;",

            "email_queue" => "CREATE TABLE {$wpdb->prefix}email_queue (
                id BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                to_email VARCHAR(255) NOT NULL,
                subject VARCHAR(255) NOT NULL,
                message TEXT NOT NULL,
                status ENUM('pending', 'sent', 'failed') DEFAULT 'pending',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) $charset_collate;",

            "notifications" => "CREATE TABLE {$wpdb->prefix}notifications (
                id BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                user_id BIGINT(20) UNSIGNED NOT NULL,
                message TEXT NOT NULL,
                status ENUM('unread', 'read') DEFAULT 'unread',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX (user_id)
            ) $charset_collate;"
        ];

        require_once (ABSPATH . 'wp-admin/includes/upgrade.php');
        foreach ($tables as $name => $sql) {
            if (!self::table_exists($wpdb->prefix . $name)) {
                dbDelta($sql);
            }
        }
    }

    /**
     * Insert data into a table.
     */
    public static function insert($table, $data)
    {
        global $wpdb;
        $wpdb->insert("{$wpdb->prefix}$table", $data);
        return $wpdb->insert_id;
    }

    /**
     * Delete data from a table.
     */
    public static function delete($table, $where)
    {
        global $wpdb;
        return $wpdb->delete("{$wpdb->prefix}$table", $where);
    }

    /**
     * Update data in a table.
     */
    public static function update($table, $data, $where)
    {
        global $wpdb;
        return $wpdb->update("{$wpdb->prefix}$table", $data, $where);
    }

    /**
     * Get data from a table.
     */
    public function get($table, $where)
    {
        global $wpdb;
        $sql = "SELECT * FROM {$wpdb->prefix}{$table} WHERE " . self::build_where_clause($where);
        return $wpdb->get_row($sql);
    }

    private function build_where_clause($where)
    {
        $clauses = [];
        foreach ($where as $key => $value) {
            $clauses[] = "$key = '$value'";
        }
        return implode(' AND ', $clauses);
    }
}

<?php
namespace Review_Store\Inc\Database;

class Database
{
    private static $instance = null;
    private $wpdb;

    private function __construct($wpdb)
    {
        // private constructor to prevent direct instantiation
        $this->wpdb = $wpdb;
    }

    public static function getInstance($wpdb = null)
    {
        if (self::$instance === null) {
            if ($wpdb === null) {
                global $wpdb;
            }
            self::$instance = new self($wpdb);
        }
        return self::$instance;
    }

    /**
     * Check if a table exists in the database.
     *
     * @param string $table_name
     * @return bool
     */
    public static function table_exists(string $table_name): bool
    {
        global $wpdb;
        $query = $wpdb->prepare("SHOW TABLES LIKE %s", $wpdb->esc_like($wpdb->prefix . $table_name));
        return $wpdb->get_var($query) === $wpdb->prefix . $table_name;
    }

    /**
     * Create required tables for the plugin.
     */
    public static function create_tables(): void
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

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        foreach ($tables as $name => $sql) {
            if (!self::table_exists($name)) {
                dbDelta($sql);
            }
        }
    }

    /**
     * Insert data into a table.
     *
     * @param string $table
     * @param array $data
     * @return int|false
     */
    public function insert(string $table, array $data)
    {
        $table = $this->wpdb->prefix . $table;
        $this->wpdb->insert($table, $data);
        return $this->wpdb->insert_id;
    }


    /**
     * Insert a new user into the custom users table.
     *
     * @param string $name The name of the user.
     * @param string $email The email of the user.
     * @param int $product_id The ID of the product associated with the user.
     * @return int The ID of the newly inserted user.
     */
    public function insert_user($name, $email, $product_id)
    {
        $this->wpdb->insert(
            "{$this->wpdb->prefix}external_profile",
            array(
                'name' => $name,
                'email' => $email,
                'product_id' => $product_id,
            ),
            array(
                '%s',
                '%s',
                '%d',
            )
        );
        return $this->wpdb->insert_id;
    }

    /**
     * Insert a new review into the custom reviews table.
     *
     * @param int $user_id The ID of the user being reviewed.
     * @param string $reviewer_name The name of the reviewer.
     * @param string $review_content The content of the review.
     * @param int $rating The rating given by the reviewer.
     * @return int The ID of the newly inserted review.
     */
    public function insert_review($user_id, $reviewer_name, $review_content, $rating)
    {
        $this->wpdb->insert(
            "{$this->wpdb->prefix}urp_custom_reviews",
            array(
                'user_id' => $user_id,
                'reviewer_name' => $reviewer_name,
                'review_content' => $review_content,
                'rating' => $rating,
                'status' => 'approved',
            ),
            array(
                '%d',
                '%s',
                '%s',
                '%d',
                '%s'
            )
        );
        return $this->wpdb->insert_id;
    }

    /**
     * Delete data from a table.
     *
     * @param string $table
     * @param array $where
     * @return int|false
     */
    public function delete(string $table, array $where)
    {
        $table = $this->wpdb->prefix . $table;
        return $this->wpdb->delete($table, $where);
    }

    /**
     * Update data in a table.
     *
     * @param string $table
     * @param array $data
     * @param array $where
     * @return int|false
     */
    public function update(string $table, array $data, array $where)
    {
        $table = $this->wpdb->prefix . $table;
        return $this->wpdb->update($table, $data, $where);
    }

    /**
     * Get data from a table.
     *
     * @param string $table
     * @param array $where
     * @return object|null
     */
    public function get(string $table, array $where)
    {
        $sql = $this->wpdb->prepare(
            "SELECT * FROM {$this->wpdb->prefix}{$table} WHERE " . self::build_where_clause($where),
            ...array_values($where)
        );
        return $this->wpdb->get_row($sql);
    }

    /**
     * Build the WHERE clause for SQL queries.
     *
     * @param array $where
     * @return string
     */
    private function build_where_clause(array $where): string
    {
        $clauses = [];
        foreach ($where as $key => $value) {
            $clauses[] = $key . ' = %s';
        }
        return implode(' AND ', $clauses);
    }

    /**
     * Get users along with their total, approved, and pending review counts.
     *
     * @return array|object|null
     */
    public function get_users_with_review_data()
    {

        $query = "
            SELECT u.external_profile_id, u.name, u.email,
                   COUNT(r.review_id) as total_reviews,
                   SUM(CASE WHEN r.status = 'approved' THEN 1 ELSE 0 END) as approved_reviews,
                   SUM(CASE WHEN r.status = 'pending' THEN 1 ELSE 0 END) as pending_reviews
            FROM {$this->wpdb->prefix}external_profile u
            LEFT JOIN {$this->wpdb->prefix}reviews r ON u.external_profile_id = r.external_profile_id
            GROUP BY u.id, u.name, u.email
        ";

        return $this->wpdb->get_results($query);
    }
}

<?php

namespace Review_Store\Inc\Admin;

// use const Review_Store\NS;
use const Review_Store\PLUGIN_ADMIN_VIEWS_DIR;
use Review_Store\Inc\Database\Database;

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @link       http://onlytarikul.com
 * @since      1.0.0
 *
 * @author    Your Name or Your Company
 */
class Admin
{

	private $plugin_name;
	private $version;
	private $plugin_text_domain;
	private $db;

	public function __construct($plugin_name, $version, $plugin_text_domain)
	{
		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->plugin_text_domain = $plugin_text_domain;
		add_action('admin_menu', array($this, 'urp_admin_menu'));
		$this->db = Database::get_instance();
	}

	public function enqueue_styles()
	{
		wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/wp-plugin-name-admin.css', array(), $this->version, 'all');
	}

	public function enqueue_scripts()
	{
		wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/wp-plugin-name-admin.js', array('jquery'), $this->version, false);
	}

	public function urp_admin_menu()
	{
		add_menu_page(
			__('Review Store', $this->plugin_text_domain),
			__('Review Store', $this->plugin_text_domain),
			'manage_options',
			$this->plugin_name,
			array($this, 'urp_user_list_page'),
			'dashicons-admin-generic',
			6
		);

		add_submenu_page(
			$this->plugin_name,
			__('Edit User', $this->plugin_text_domain),
			__('Edit User', $this->plugin_text_domain),
			'manage_options',
			$this->plugin_name . '-edit-user',
			array($this, 'urp_edit_user_page')
		);

		add_submenu_page(
			$this->plugin_name,
			__('Add User', $this->plugin_text_domain),
			__('Add User', $this->plugin_text_domain),
			'manage_options',
			$this->plugin_name . '-add-user',
			array($this, 'urp_add_user_page')
		);

		add_submenu_page(
			$this->plugin_name,
			__('Review List', $this->plugin_text_domain),
			__('Review List', $this->plugin_text_domain),
			'manage_options',
			$this->plugin_name . '-review-list',
			array($this, 'urp_review_list_page')
		);

		add_submenu_page(
			$this->plugin_name,
			__('Approve Reviews', $this->plugin_text_domain),
			__('Approve Reviews', $this->plugin_text_domain),
			'manage_options',
			$this->plugin_name . '-approve-reviews',
			array($this, 'urp_approve_reviews_page')
		);
	}

	public function urp_user_list_page()
	{
		include_once PLUGIN_ADMIN_VIEWS_DIR . $this->plugin_name . '-admin-user-list-display.php';
	}

	public function urp_edit_user_page()
	{
		include_once PLUGIN_ADMIN_VIEWS_DIR . $this->plugin_name . '-admin-edit-user-display.php';
	}

	public function urp_add_user_page()
	{
		include_once PLUGIN_ADMIN_VIEWS_DIR . $this->plugin_name . '-admin-add-user-display.php';
	}

	public function urp_review_list_page()
	{
		include_once PLUGIN_ADMIN_VIEWS_DIR . $this->plugin_name . '-admin-review-list-display.php';
	}

	public function urp_approve_reviews_page()
	{
		include_once PLUGIN_ADMIN_VIEWS_DIR . $this->plugin_name . '-admin-approve-reviews-display.php';
	}

	public function urp_user_reviews_page()
	{
		include_once PLUGIN_ADMIN_VIEWS_DIR . $this->plugin_name . '-admin-user-reviews-display.php';
	}


	// public function insert_review($external_profile_id, $reviewer_user_id, $rating)
	// {
	// 	$data = [
	// 		'external_profile_id' => $external_profile_id,
	// 		'reviewer_user_id' => $reviewer_user_id,
	// 		'rating' => $rating,
	// 		'status' => 'pending',
	// 		'created_at' => current_time('mysql'),
	// 		'updated_at' => current_time('mysql')
	// 	];
	// 	$review_id = $this->db->insert('reviews', $data);
	// 	return $review_id;
	// }

	// public function delete_review($review_id)
	// {
	// 	$where = ['review_id' => $review_id];
	// 	return $this->db->delete('reviews', $where);
	// }

	// public function update_review($review_id, $data)
	// {
	// 	$where = ['review_id' => $review_id];
	// 	return $this->db->update('reviews', $data, $where);
	// }

	// public function get_review($review_id)
	// {
	// 	$where = ['review_id' => $review_id];
	// 	return $this->db->get('reviews', $where);
	// }

	// Example of using Database class for inserting a new review
	// public function insert_review($external_profile_id, $reviewer_user_id, $rating)
	// {
	// 	$data = [
	// 		'external_profile_id' => $external_profile_id,
	// 		'reviewer_user_id' => $reviewer_user_id,
	// 		'rating' => $rating,
	// 		'status' => 'pending',
	// 		'created_at' => current_time('mysql'),
	// 		'updated_at' => current_time('mysql')
	// 	];
	// 	$review_id = Database::insert('reviews', $data);
	// 	return $review_id;
	// }

	// // Example of using Database class for deleting a review
	// public function delete_review($review_id)
	// {
	// 	$where = ['review_id' => $review_id];
	// 	return Database::delete('reviews', $where);
	// }

	// // Example of using Database class for updating a review
	// public function update_review($review_id, $data)
	// {
	// 	$where = ['review_id' => $review_id];
	// 	return Database::update('reviews', $data, $where);
	// }

	// // Example of using Database class for getting a review
	// public function get_review($review_id)
	// {
	// 	$where = ['review_id' => $review_id];
	// 	return Database::get('reviews', $where);
	// }
}

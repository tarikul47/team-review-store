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
		global $wpdb;
		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->plugin_text_domain = $plugin_text_domain;
		add_action('admin_menu', array($this, 'urp_admin_menu'));
		$this->db = Database::getInstance($wpdb);

		// Register form submission handler
		add_action('admin_post_handle_add_user_form', array($this, 'handle_add_user_form_submission'));

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
		// if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['user_id'])) {
		//     $this->handle_delete_user(intval($_GET['user_id']));
		// }

		// if (isset($_POST['bulk_delete_users']) && !empty($_POST['user_ids'])) {
		//     $this->handle_bulk_delete_users(array_map('intval', $_POST['user_ids']));
		// }

		$users = $this->db->get_users_with_review_data();

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

	/**
	 * Handles the form submission for adding a new user and review.
	 * 
	 * This method processes the form data submitted by the user, verifies the nonce for security,
	 * and performs operations such as inserting the user into the database. It also handles
	 * additional tasks like generating PDFs and sending emails, although those parts are commented out
	 * for now. After processing, it redirects the user to a success page.
	 * 
	 * @return void
	 */
	public function handle_add_user_form_submission()
	{
		// Verify nonce
		if (!isset($_POST['add_user_and_review_nonce']) || !wp_verify_nonce($_POST['add_user_and_review_nonce'], 'add_user_and_review_action')) {
			wp_die('Nonce verification failed.');
		}

		// Process form data
		if (isset($_POST['submit_user'])) {
			$name = sanitize_text_field($_POST['name']);
			$email = sanitize_email($_POST['email']);
			$review_content = sanitize_textarea_field($_POST['review_content']);
			$rating = intval($_POST['rating']);

			// Additional processing (e.g., database operations, PDF generation)
			$content = '<h1>Reviews for ' . $name . '</h1>';
			$content .= '<h2>Review by ' . esc_html(wp_get_current_user()->display_name) . '</h2>';
			$content .= '<p>Review Content: ' . esc_html($review_content) . '</p>';
			$content .= '<p>Rating: ' . esc_html($rating) . '</p>';
			$content .= '<hr>';

			// Generate PDF and create/update product (currently commented out)
			//	$pdf_url = generate_product_pdf_from_person_review($name, $content);
			//	$product_id = $this->create_or_update_downloadable_product($name, $pdf_url = '');

			// Insert user into the database (product ID is 0 for now)
			$user_id = $this->db->insert_user($name, $email, 13);

			error_log(print_r($user_id, true));

			// Insert review into the database (currently commented out)
			//	$this->db->insert_review($user_id, wp_get_current_user()->display_name, $review_content, $rating);

			// Send email to the user (currently commented out)
			// $subject = 'Hurrah! A Review is live!';
			// $message = 'Hello ' . $name . ',<br>One of a review is now live. You can check it.';
			// urp_send_email($email, $subject, $message);

			// Redirect to avoid resubmission on refresh
			wp_redirect(admin_url('admin.php?page=' . $this->plugin_name . '-add-user&status=success'));
			exit;
		}
	}



	/**
	 * Generates a PDF file from the review content and saves it to the server.
	 * 
	 * This method uses the mPDF library to create a PDF from the provided review content,
	 * saves the PDF to the WordPress uploads directory, and returns the URL of the generated
	 * PDF file. It handles errors by logging them and returns `false` if PDF generation fails.
	 * 
	 * @param string $user_name The name of the user for whom the review is written.
	 * @param string $content The HTML content of the review to be included in the PDF.
	 * 
	 * @return string|false The URL of the generated PDF file, or `false` on failure.
	 */
	private function generate_product_pdf_from_person_review($user_name, $content)
	{
		try {
			$mpdf = new Mpdf();
			$mpdf->WriteHTML($content);

			$upload_dir = wp_upload_dir();
			$pdf_file = $upload_dir['path'] . '/user_' . sanitize_title($user_name) . '_review.pdf';
			$mpdf->Output($pdf_file, 'F');

			chmod($pdf_file, 0644);

			if (!file_exists($pdf_file) || filesize($pdf_file) == 0) {
				throw new \Exception('PDF file creation failed or the file is empty.');
			}

			return $upload_dir['url'] . '/user_' . sanitize_title($user_name) . '_review.pdf';
		} catch (\Exception $e) {
			error_log('Error generating PDF: ' . $e->getMessage());
			return false;
		}
	}



	/**
	 * Creates or updates a downloadable product in WooCommerce.
	 * 
	 * This method creates a new WooCommerce product or updates an existing one with the provided
	 * PDF URL. The product will be marked as downloadable and virtual, and the PDF will be attached
	 * as a downloadable file. It returns the product ID of the created or updated product.
	 * 
	 * @param string $user_name The name of the user for whom the review is written.
	 * @param string $pdf_url The URL of the PDF file to be attached to the product.
	 * @param int|null $product_id Optional. The ID of the product to update. If not provided, a new product will be created.
	 * 
	 * @return int|false The product ID of the created or updated product, or `false` on failure.
	 */
	private function create_or_update_downloadable_product($user_name, $pdf_url, $product_id = null)
	{
		if ($product_id) {
			$product = wc_get_product($product_id);
			if (!$product) {
				error_log('Product with ID ' . $product_id . ' not found.');
				return false;
			}
		} else {
			$product = new \WC_Product();
		}

		$product->set_name('Review for ' . $user_name);
		$product->set_status('publish');
		$product->set_catalog_visibility('visible');
		$product->set_description('Review PDF for ' . $user_name);
		$product->set_regular_price(10);
		$product->set_downloadable(true);
		$product->set_virtual(true);

		$download_id = wp_generate_uuid4();
		$downloads = [
			$download_id => [
				'name' => 'Review PDF',
				'file' => $pdf_url
			]
		];
		$product->set_downloads($downloads);
		$product->save();

		return $product->get_id();
	}



















	// // Method to handle single user deletion
	// private function handle_delete_user($user_id) {
	// 	Database::delete_user_and_related_data($user_id);
	// 	echo '<div class="updated"><p>User, related reviews, and associated product and PDF file(s) deleted successfully!</p></div>';
	// }

	// // Method to handle bulk user deletion
	// private function handle_bulk_delete_users($user_ids) {
	// 	foreach ($user_ids as $user_id) {
	// 		Database::delete_user_and_related_data($user_id);
	// 	}
	// 	echo '<div class="updated"><p>Selected users, related reviews, and associated products and PDF files deleted successfully!</p></div>';
	// }

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

<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       http://onlytarikul.com
 * @since      1.0.0
 *
 * @author    Your Name or Your Company
 */

?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<div class="wrap">
    <h2>Add New User and Review</h2>
    <form method="post" action="<?php echo admin_url('admin-post.php'); ?>">
        <?php wp_nonce_field('add_user_and_review_action', 'add_user_and_review_nonce'); ?>
        <input type="hidden" name="action" value="handle_add_user_form">
        <table class="form-table">
            <tbody>
                <tr>
                    <th scope="row"><label for="name">Name</label></th>
                    <td><input type="text" name="name" id="name" class="regular-text" required></td>
                </tr>
                <tr>
                    <th scope="row"><label for="email">Email</label></th>
                    <td><input type="email" name="email" id="email" class="regular-text" required></td>
                </tr>
                <tr>
                    <th scope="row"><label for="review_content">Review Content</label></th>
                    <td><textarea name="review_content" id="review_content" rows="5" class="regular-text"
                            required></textarea></td>
                </tr>
                <tr>
                    <th scope="row"><label for="rating">Rating</label></th>
                    <td>
                        <select name="rating" id="rating" required>
                            <option value="5">5 (Excellent)</option>
                            <option value="4">4 (Good)</option>
                            <option value="3">3 (Average)</option>
                            <option value="2">2 (Fair)</option>
                            <option value="1">1 (Poor)</option>
                        </select>
                    </td>
                </tr>
            </tbody>
        </table>
        <input type="submit" name="submit_user" id="submit_user" class="button button-primary"
            value="Add User and Review">
    </form>
</div>
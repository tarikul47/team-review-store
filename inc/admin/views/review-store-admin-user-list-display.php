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

<?php
echo '<div class="wrap">';
echo '<h2>User List - ' . count($users) . '</h2>';
echo '<form method="post" action="">';
echo '<table class="wp-list-table widefat fixed striped">';
echo '<thead><tr><th><input type="checkbox" id="select-all"></th><th>ID</th><th>Name</th><th>Email</th><th>Total Reviews</th><th>Approved Reviews</th><th>Pending Reviews</th><th>Actions</th><th>View Reviews</th></tr></thead><tbody>';

foreach ($users as $user) {
    echo '<tr>';
    echo '<td><input type="checkbox" name="user_ids[]" value="' . esc_attr($user->id) . '"></td>';
    echo '<td>' . esc_html($user->id) . '</td>';
    echo '<td>' . esc_html($user->name) . '</td>';
    echo '<td>' . esc_html($user->email) . '</td>';
    echo '<td>' . esc_html($user->total_reviews) . '</td>';
    echo '<td>' . esc_html($user->approved_reviews) . '</td>';
    echo '<td>' . esc_html($user->pending_reviews) . '</td>';
    echo '<td>
            <a href="' . esc_url(admin_url('admin.php?page=edit_user&user_id=' . esc_attr($user->id))) . '" class="button">Edit</a>
            <a href="' . esc_url(admin_url('admin.php?page=user-reviews-plugin&action=delete&user_id=' . esc_attr($user->id))) . '" class="button" onclick="return confirm(\'Are you sure you want to delete this user?\')">Delete</a>
          </td>';
    echo '<td>
            <a href="' . esc_url(admin_url('admin.php?page=user_reviews&user_id=' . esc_attr($user->id))) . '" class="button">View Reviews</a>
          </td>';
    echo '</tr>';
}

echo '</tbody></table>';
echo '<input type="submit" name="bulk_delete_users" class="button button-primary" value="Delete Selected Users and Related Data">';
echo '</form>';
echo '</div>';
?>

<script type="text/javascript">
    document.getElementById('select-all').addEventListener('click', function (event) {
        const checkboxes = document.querySelectorAll('input[name="user_ids[]"]');
        checkboxes.forEach(checkbox => {
            checkbox.checked = event.target.checked;
        });
    });
</script>
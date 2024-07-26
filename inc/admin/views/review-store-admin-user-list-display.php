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
    <h2>User List - <?php echo count($users); ?></h2>
    <form method="post" action="">
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th><input type="checkbox" id="select-all"></th>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Total Reviews</th>
                    <th>Approved Reviews</th>
                    <th>Pending Reviews</th>
                    <th>Actions</th>
                    <th>View Reviews</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><input type="checkbox" name="user_ids[]" value="<?php echo esc_attr($user->id); ?>"></td>
                        <td><?php echo esc_html($user->id); ?></td>
                        <td><?php echo esc_html($user->name); ?></td>
                        <td><?php echo esc_html($user->email); ?></td>
                        <td><?php echo esc_html($user->total_reviews); ?></td>
                        <td><?php echo esc_html($user->approved_reviews); ?></td>
                        <td><?php echo esc_html($user->pending_reviews); ?></td>
                        <td>
                            <a href="<?php echo esc_url(admin_url('admin.php?page=edit_user&user_id=' . esc_attr($user->id))); ?>"
                                class="button">Edit</a>
                            <a href="<?php echo esc_url(admin_url('admin.php?page=user-reviews-plugin&action=delete&user_id=' . esc_attr($user->id))); ?>"
                                class="button"
                                onclick="return confirm('Are you sure you want to delete this user?')">Delete</a>
                        </td>
                        <td>
                            <a href="<?php echo esc_url(admin_url('admin.php?page=user_reviews&user_id=' . esc_attr($user->id))); ?>"
                                class="button">View Reviews</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <input type="submit" name="bulk_delete_users" class="button button-primary"
            value="Delete Selected Users and Related Data">
    </form>
</div>

<script type="text/javascript">
    document.getElementById('select-all').addEventListener('click', function (event) {
        const checkboxes = document.querySelectorAll('input[name="user_ids[]"]');
        checkboxes.forEach(checkbox => {
            checkbox.checked = event.target.checked;
        });
    });
</script>
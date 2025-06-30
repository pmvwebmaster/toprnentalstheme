<?php
/**
 * Template Name: Reset Password
 */

defined('ABSPATH') || exit;

if (isset($_GET['key']) && isset($_GET['login'])) {
    $user = get_user_by('login', sanitize_text_field($_GET['login']));
    $key = sanitize_text_field($_GET['key']);
    $error = '';
    $success = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reset_password_submit']) && isset($_POST['reset_password_nonce']) && wp_verify_nonce($_POST['reset_password_nonce'], 'reset_password')) {
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];
        if ($new_password !== $confirm_password) {
            $error = 'Passwords do not match.';
        } elseif (empty($new_password)) {
            $error = 'Password cannot be empty.';
        } else {
            $check = check_password_reset_key($key, $user->user_login);
            if (is_wp_error($check)) {
                $error = 'Invalid or expired reset key.';
            } else {
                reset_password($user, $new_password);
                $success = 'Your password has been reset. You can now <a href="' . esc_url(wp_login_url()) . '">login</a>.';
            }
        }
    }
} else {
    wp_redirect(site_url());
    exit;
}

get_header();
?>
<div class="login-container">
    <h2 class="login-title">Reset Password</h2>
    <?php if (!empty($error)) echo '<p class="error-msg">' . esc_html($error) . '</p>'; ?>
    <?php if (!empty($success)) {
        echo '<p class="success-msg">' . $success . '</p>';
    } else { ?>
    <form method="post">
        <?php wp_nonce_field('reset_password', 'reset_password_nonce'); ?>
        <input type="password" name="new_password" placeholder="New password" required>
        <input type="password" name="confirm_password" placeholder="Confirm new password" required>
        <button type="submit" name="reset_password_submit">Reset Password</button>
    </form>
    <?php } ?>
</div>
<?php get_footer(); ?>

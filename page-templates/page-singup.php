<?php

/**
 * The template for displaying Sign Up Page
 *
 * Template Name:  Sign Up
 * @package meshije.al
 */
get_header();

function meshije_form_errors()
{
    static $wp_error; // global variable handle
    return isset($wp_error) ? $wp_error : ($wp_error = new WP_Error(null, null));
}
$registration_successful = false; // Flag to track successful registration

if (isset($_POST["meshije_username"]) && wp_verify_nonce($_POST["meshije_csrf"], 'meshije-csrf')) {
    $user_login = sanitize_user($_POST["meshije_username"]);
    $user_email = sanitize_email($_POST["meshije_email"]);
    $user_password = $_POST["meshije_password"];
    $user_password_confirm = $_POST["meshije_password_confirm"];

    if (empty($user_login)) {
        meshije_form_errors()->add('username_empty', __('Please enter a username'));
    }
    if (empty($user_email) || !is_email($user_email)) {
        meshije_form_errors()->add('email_invalid', __('Invalid email'));
    }
    if (email_exists($user_email) === 1) {
        meshije_form_errors()->add('email_exists', __('Email plase Log in'));
    }
    if (empty($user_password)) {
        meshije_form_errors()->add('password_empty', __('Please enter a password'));
    }
    if ($user_password !== $user_password_confirm) {
        meshije_form_errors()->add('password_mismatch', __('Passwords do not match'));
    }

    $errors = meshije_form_errors()->get_error_messages();

    if (empty($errors)) {
        $new_user_id = wp_insert_user(array(
            'user_login' => $user_login,
            'user_email' => $user_email,
            'user_pass' => wp_hash_password($user_password),
            'user_registered' => date('Y-m-d H:i:s'),
            'role' => 'meshije_front_end'
        ));

        if (!is_wp_error($new_user_id)) {
            // log the new user in
            $user = get_user_by('id', $new_user_id);
            wp_set_current_user($new_user_id, $user_login);
            wp_set_auth_cookie($new_user_id);
            do_action('wp_login', $user_login);
            $registration_successful = true; // Set flag to true
            wp_send_new_user_notifications($new_user_id);
            // send the newly created user to the home page after logging them in
            // wp_redirect(home_url());
            // exit;
        } else {
            meshije_form_errors()->add('registration_failed', __('User registration failed'));
        }
    }
}

function meshije_register_messages()
{
    if ($codes = meshije_form_errors()->get_error_codes()) {
        echo '<div class="meshije_errors">';
        // Loop error codes and display errors
        foreach ($codes as $code) {
            $message = meshije_form_errors()->get_error_message($code);
            echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 mb-4 rounded relative" role="alert">' . __('Error') . '</strong>: ' . $message . '</div>';
        }
        echo '</div>';
    }
    // Show success message if registration was successful
    if ($GLOBALS['registration_successful']) {
        echo '<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 mb-4 rounded relative" role="alert">' . __('Registration successful! You are now logged in.') . '</div>';
    }
}
?>
<div class="flex min-h-full flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <h2 class="text-2xl font-semibold mb-4"><?php _e('Mirë se erdhët!') ?></h2>
        <p class="text-base font-normal mb-4"><?php _e('Për të filluar procesin e regjistrimit ju duhet të plotësoni të dhënat në vazhdim') ?></p>

    </div> <?php meshije_register_messages(); ?>
    <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-[480px]">
        <div class="bg-white px-6 py-12 shadow sm:rounded-lg sm:px-12">
            <form method="post" class="space-y-4">
                <div>
                    <label for="username" class="block font-medium"><?php _e('Emri i përdoruesit') ?></label>
                    <input type="text" id="username" name="meshije_username" class="w-full px-3 py-2 border rounded-lg">
                </div>
                <div>
                    <label for="email" class="block font-medium"><?php _e('Email adresa') ?></label>
                    <input type="email" id="email" name="meshije_email" class="w-full px-3 py-2 border rounded-lg">
                </div>
                <div>
                    <label for="password" class="block font-medium"><?php _e('Vendosni fjalëkalimin') ?></label>
                    <input type="password" id="password" name="meshije_password" class="w-full px-3 py-2 border rounded-lg">
                </div>
                <div>
                    <label for="password_confirm" class="block font-medium"><?php _e('Konfirmojeni fjalëkalimin') ?></label>
                    <input type="password" id="password_confirm" name="meshije_password_confirm" class="w-full px-3 py-2 border rounded-lg">
                </div>
                <div>
                    <input type="hidden" name="meshije_csrf" value="<?= wp_create_nonce('meshije-csrf') ?>" class="w-full px-3 py-2 border rounded-lg">
                </div>
                <button type="submit" name="meshije_submit_signup" class="w-full py-2 px-4 bg-blue-500 text-white rounded-lg hover:bg-blue-600">Regjistrohu</button>
            </form>
            <div class="mt-4 text-center">
                <a href="<?php echo esc_url(home_url('/login/')); ?>" class="text-blue-500 hover:underline">Kyçu në llogarinë!</a>
                <br>
                <a href="<?php echo esc_url(wp_lostpassword_url()); ?>" class="text-blue-500 hover:underline">Keni harruar fjalëkalimin?</a>
            </div>
        </div>
    </div>
</div>
<?php
get_footer();

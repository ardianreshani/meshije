<?php
require_once get_template_directory() . '/vendor/autoload.php';

$gClient = new Google_Client();
$gClient->setClientId("490927217991-3hmg4c5cbpgfgs194pesgb6h1bmtl3gg.apps.googleusercontent.com");
$gClient->setClientSecret("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");
$gClient->setApplicationName("Meshije Login");
$gClient->setRedirectUri("http://localhost/meshijee/wp-admin/admin-ajax.php?action=meshije_login_google");
$gClient->addScope("https://www.googleapis.com/auth/plus.login https://www.googleapis.com/auth/userinfo.email");
$gClient->setPrompt('select_account');
// login URL
$login_url = $gClient->createAuthUrl();

add_shortcode('login_with_google', 'meshije_login_with_google');

function meshije_login_with_google()
{
    global $login_url;
    return $btnContent = '<a href="' . esc_url($login_url) . '" class="flex w-full items-center justify-center gap-3 border-2 rounded-md bg-white px-3 py-1.5 text-gray-900 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-lime-700 hover:bg-sky-700 hover:text-white">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12.0003 4.75C13.7703 4.75 15.3553 5.36002 16.6053 6.54998L20.0303 3.125C17.9502 1.19 15.2353 0 12.0003 0C7.31028 0 3.25527 2.69 1.28027 6.60998L5.27028 9.70498C6.21525 6.86002 8.87028 4.75 12.0003 4.75Z" fill="#EA4335"></path>
                            <path d="M23.49 12.275C23.49 11.49 23.415 10.73 23.3 10H12V14.51H18.47C18.18 15.99 17.34 17.25 16.08 18.1L19.945 21.1C22.2 19.01 23.49 15.92 23.49 12.275Z" fill="#4285F4"></path>
                            <path d="M5.26498 14.2949C5.02498 13.5699 4.88501 12.7999 4.88501 11.9999C4.88501 11.1999 5.01998 10.4299 5.26498 9.7049L1.275 6.60986C0.46 8.22986 0 10.0599 0 11.9999C0 13.9399 0.46 15.7699 1.28 17.3899L5.26498 14.2949Z" fill="#FBBC05"></path>
                            <path d="M12.0004 24.0001C15.2404 24.0001 17.9654 22.935 19.9454 21.095L16.0804 18.095C15.0054 18.82 13.6204 19.245 12.0004 19.245C8.8704 19.245 6.21537 17.135 5.2654 14.29L1.27539 17.385C3.25539 21.31 7.3104 24.0001 12.0004 24.0001Z" fill="#34A853"></path>
                        </svg>
                        <span class="text-sm font-semibold leading-6">Kyçu me Google</span>
                    </a>';
}

// add ajax action
add_action('wp_ajax_nopriv_meshije_login_google', 'meshije_login_google');
function meshije_login_google()
{
    global $gClient;
    // Rate limiting parameters
    $max_attempts = 5;
    $interval = 3600; // 1 hour in seconds
    $ip = $_SERVER['REMOTE_ADDR'];
    $key = 'login_attempt_' . $ip;

    // Get the current number of login attempts for the IP address
    $attempts = get_transient($key);

    // If attempts exceed the maximum allowed, return an error
    if ($attempts !== false && $attempts >= $max_attempts) {
        wp_send_json_error('Rate limit exceeded. Please try again later.');
    }

    // checking for google code
    if (isset($_GET['code'])) {
        $token = $gClient->fetchAccessTokenWithAuthCode($_GET['code']);
        if (!isset($token["error"])) {
            // get data from google
            $oAuth = new Google_Service_Oauth2($gClient);
            $userData = $oAuth->userinfo_v2_me->get();
            // sanitize and validate user data
            $user_email = sanitize_email($userData['email']);
            $user_given_name = sanitize_text_field($userData['givenName']);
            $user_family_name = sanitize_text_field($userData['familyName']);
            // check if user email already registered
            if (!email_exists($user_email)) {
                // generate password
                $bytes = openssl_random_pseudo_bytes(2);
                $password = wp_generate_password(12, false);
                $user_login = $userData['id'];

                $new_user_id = wp_insert_user(
                    array(
                        'user_login'        => $user_login,
                        'user_pass'         => $password,
                        'user_email'        => $user_email,
                        'user_nicename'     => $user_given_name . ' ' . $user_family_name,
                        'nickname'          => $user_given_name . ' ' . $user_family_name,
                        'first_name'        => $user_given_name,
                        'last_name'         => $user_family_name,
                        'user_registered'   => date('Y-m-d H:i:s'),
                        'role'              => 'meshije_front_end'
                    )
                );
                if (!is_wp_error($new_user_id)) {
                    // Add the user's avatar
                    $avatar_url = $userData['picture']; // Replace this with the URL of the image you want to set as the user's avatar
                    update_user_meta($new_user_id, 'wp_user_avatar', esc_url($avatar_url));
                }
                if ($new_user_id) {
                    // Increment the login attempt counter
                    $attempts = $attempts === false ? 1 : $attempts + 1;
                    set_transient($key, $attempts, $interval);

                    // send an email to the admin
                    wp_new_user_notification($new_user_id);

                    // log the new user in
                    do_action('wp_login', $user_login, $user_email);
                    wp_set_current_user($new_user_id);
                    wp_set_auth_cookie($new_user_id, true);

                    // send the newly created user to the home page after login
                    wp_redirect(home_url());
                    exit;
                }
            } else {
                //if user already registered than we are just logging in the user
                $user = get_user_by('email', $user_email);
                do_action('wp_login', $user->user_login, $user_email);
                wp_set_current_user($user->ID);
                wp_set_auth_cookie($user->ID, true);
                wp_redirect(home_url());
                exit;
            }
        } else {
            // Handle error fetching token from Google
            wp_send_json_error('Error fetching token from Google.');
        }
    } else {
        wp_redirect(home_url());
        exit();
    }
}
//ALLOW LOGGED OUT users to access admin-ajax.php action
function add_google_ajax_actions()
{
    add_action('wp_ajax_nopriv_meshije_login_google', 'meshije_login_google');
}
add_action('admin_init', 'add_google_ajax_actions');

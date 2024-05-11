<?php
require_once get_template_directory() . '/vendor/autoload.php';
// App ID : 688057316568761
// App secret: 45a26d0b2a236b2c883d2247c37026a8
session_start();
$fb = new \Facebook\Facebook([
    'app_id' => '688057316568761',
    'app_secret' => '45a26d0b2a236b2c883d2247c37026a8',
    'default_graph_version' => 'v2.10'
]);
$handler = $fb->getRedirectLoginHelper();

add_shortcode('facebook-login', 'login_with_facebook');
function login_with_facebook()
{

    if (!is_user_logged_in()) {
        if (!get_option('users_can_register')) {
            return ('The registrations are closed!');
        } else {
            global $handler;
            $nonce = wp_create_nonce("meshije_facebook_login_nonce");
            $link = admin_url('admin-ajax.php?action=meshije_facebook_login&nonce=' . $nonce);
            $redirect_to = $link;
            $data = ["email"];
            $fullURL = $handler->getLoginURL($redirect_to, $data);
            return '
				<a href="' . $fullURL . '">Login With Facebook</a>
			';
        }
    } else {
        $current_user = wp_get_current_user();
        return  'Hi ' . $current_user->first_name . '! - <a href="/wp-login.php?action=logout">Log Out</a>';
    }
}


add_action("wp_ajax_meshije_facebook_login", "meshije_facebook_login");
function meshije_facebook_login()
{

    global $handler, $fb;

    if (!wp_verify_nonce($_REQUEST['nonce'], "meshije_facebook_login_nonce")) {
        exit("No naughty business please");
    }

    try {
        $accessToken = $handler->getAccessToken();
    } catch (\Facebook\Exceptions\FacebookResponseException $e) {
        echo "Response Exception: " . $e->getMessage();
        exit();
    } catch (\Facebook\Exceptions\FacebookSDKException $e) {
        echo "SDK Exception: " . $e->getMessage();
        exit();
    }

    if (!$accessToken) {
        wp_redirect(home_url());
        exit;
    }

    $oAuth2Client = $fb->getOAuth2Client();
    if (!$accessToken->isLongLived())
        $accessToken = $oAuth2Client->getLongLivedAccesToken($accessToken);

    $response = $fb->get("/me?fields=id, first_name, last_name, email, picture.type(large)", $accessToken);
    $userData = $response->getGraphNode()->asArray();

    $user_email = $userData['email'];
    // check if user email already registered
    if (!email_exists($user_email)) {

        // generate password
        $bytes = openssl_random_pseudo_bytes(2);
        $password = md5(bin2hex($bytes));
        $user_login = strtolower($userData['first_name'] . $userData['last_name']);


        $new_user_id = wp_insert_user(
            array(
                'user_login'        => $user_login,
                'user_pass'             => $password,
                'user_email'        => $user_email,
                'first_name'        => $userData['first_name'],
                'last_name'            => $userData['last_name'],
                'user_registered'    => date('Y-m-d H:i:s'),
                'role'              => 'meshije_front_end'
            )
        );
        if ($new_user_id) {
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
        //if user already registered than we are just loggin in the user
        $user = get_user_by('email', $user_email);
        do_action('wp_login', $user->user_login, $user->user_email);
        wp_set_current_user($user->ID);
        wp_set_auth_cookie($user->ID, true);
        wp_redirect(home_url());
        exit;
    }
}

// allow logged out users to access admin-ajax.php action
function add_ajax_actions()
{
    add_action('wp_ajax_nopriv_meshije_facebook_login', 'meshije_facebook_login');
}

add_action('admin_init', 'add_ajax_actions');

// redirect users to home page after they will log out
add_action('wp_logout', 'meshije_redirect_after_logout');
function meshije_redirect_after_logout()
{
    wp_redirect(home_url());
    exit();
}

<?php

include 'inc/login-with-google.php';
include 'inc/login-with-facebook.php';
include 'inc/shortcode.php';
function boilerplate_load_assets()
{
  wp_enqueue_script('ourmainjs', get_theme_file_uri('/build/index.js'), array('wp-element'), '1.0', true);
  wp_enqueue_style('ourmaincss', get_theme_file_uri('/build/index.css'));
  wp_enqueue_script('user-recipes', get_theme_file_uri('/src/UserRecipes.js'), array(), '1.0', true);
  wp_localize_script('ourmainjs', 'meshijeData', array(
    'root_url' => get_site_url(),
    'nonce' => wp_create_nonce('wp_rest')
  ));
}

add_action('wp_enqueue_scripts', 'boilerplate_load_assets');

function boilerplate_add_support()
{
  add_theme_support('title-tag');
  add_theme_support('post-thumbnails');
}

add_action('after_setup_theme', 'boilerplate_add_support');


if (!function_exists('dd')) :
  /**
   * @return void
   */
  function dd(): void
  {
    if (func_num_args() === 1) {
      $a = func_get_args();
      echo '<pre>', var_dump($a[0]), '</pre><hr>';
    } else if (func_num_args() > 1)
      echo '<pre>', var_dump(func_get_args()), '</pre><hr>';
    else
      throw Exception('You must provide at least one argument to this function.');
  }
endif;

if (!function_exists('write_log')) {
  /**
   * @param $log
   * @return void
   */
  function write_log($log): void
  {
    if (is_array($log) || is_object($log)) {
      error_log(print_r($log, true));
    } else {
      error_log($log);
    }
  }
}

// Creating the Frontend User
// Creating the Frontend User Role
// Creating the Frontend User Role
function meshije_roles()
{
  add_role('meshije_front_end', 'Frontend User', array(
    'read' => true,
    'edit_user_recipes' => true,
    'delete_user_recipes' => true,
    'publish_user_recipes' => true,
    'upload_files' => true,
    // Add more capabilities as needed
  ));
}
add_action('init', 'meshije_roles');

// Add capabilities to 'user-recipes' custom post type
function add_user_recipes_capabilities()
{
  $role = get_role('meshije_front_end');
  $role->add_cap('publish_posts');
  $role->add_cap('edit_posts');
  $role->add_cap('edit_user_recipes');
  $role->add_cap('delete_user_recipes');
  $role->add_cap('publish_user_recipes');
  // Add more capabilities as needed
}
add_action('init', 'add_user_recipes_capabilities');
// hide the admin bar for frontend users
if (!current_user_can('manage_options')) {
  add_filter('show_admin_bar', '__return_false');
}


// get user meta image
// $meta = get_user_meta(5);
// echo '<img src="' . $meta['wp_user_avatar'][0] . '"/>';




// if (isset($_POST['submit_login'])) {
//   $email = sanitize_text_field($_POST['email']);
//   $password = $_POST['password'];
//   $remember = $_POST['remember-me'];
//   $user = wp_signon(array(
//     'user_login' => $username,
//     'user_password' => $password,
//     'remember' => true,
//   ));

//   if (!is_wp_error($user)) {
//     wp_redirect(home_url('/dashboard/')); // Redirect after successful login
//     exit;
//   } else {
//     echo 'Login failed. Please try again.';
//   }
// }

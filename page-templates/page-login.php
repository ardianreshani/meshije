<?php
if (is_user_logged_in()) {
    wp_redirect(esc_url(site_url('/dashboard')));
    exit;
}
/**

 * The template for displaying Login Page

 *

 * Template Name:  Login Page

 * @package meshije.al

 */

get_header();

?>

<!-- <div class="max-w-md mx-auto p-6 bg-white rounded-lg shadow-md">
    <h2 class="text-2xl font-semibold mb-4">Kyçu në llogarinë tuaj</h2>

    <form action="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>" method="post" class="space-y-4">
        <div>
            <label for="username" class="block font-medium">Email adresa</label>
            <input type="text" id="username" name="username" class="w-full px-3 py-2 border rounded-lg">
        </div>
        <div>
            <label for="password" class="block font-medium">Fjalëkalimi</label>
            <input type="password" id="password" name="password" class="w-full px-3 py-2 border rounded-lg">
        </div>
        <button type="submit" name="submit_login" class="w-full py-2 px-4 bg-blue-500 text-white rounded-lg hover:bg-blue-600">Hyr</button>
    </form>
    <div class="mt-4 text-center">
        <a href="<?php echo esc_url(home_url('/regjistrohu/')); ?>" class="text-blue-500 hover:underline">Nuk jeni të regjistruar? Regjistrohu!</a>
        <br>
        <a href="<?php echo esc_url(wp_lostpassword_url()); ?>" class="text-blue-500 hover:underline">Keni harruar fjalëkalimin?</a>
    </div>
    <div class="mt-6">
        <div class="relative">
            <div class="absolute inset-0 flex items-center">
                <div class="w-full border-t border-gray-300"></div>
            </div>
            <div class="relative flex justify-center text-sm">
                <span class="px-2 bg-white text-gray-500"> Or continue with </span>
            </div>
        </div>

        <div class="mt-6 ">
            <div>
                <?= do_shortcode('[login_with_google]'); ?>
            </div>
        </div>
    </div>
</div> -->


<div class="flex min-h-full flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <img class="mx-auto h-10 w-auto" src="https://tailwindui.com/img/logos/mark.svg?color=indigo&shade=600" alt="Your Company">
        <h2 class="mt-6 text-center text-2xl font-bold leading-9 tracking-tight text-gray-900">Kyçu në llogarinë tuaj</h2>
    </div>

    <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-[480px]">
        <div class="bg-white px-6 py-12 shadow sm:rounded-lg sm:px-12">
            <?php
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $email = sanitize_email($_POST['email']);
                $password = sanitize_text_field($_POST['password']);
                $remember_me = (isset($_POST['remember-me']) && $_POST['remember-me'] === 'on') ? true : false;

                // Attempt to log the user in
                $user = wp_authenticate($email, $password);

                if (is_wp_error($user)) {
                    $errors = $user->get_error_messages();
                    echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 mb-4 rounded relative" role="alert">';
                    foreach ($errors as $error) {
                        echo '<span class="block sm:inline">' . $error . '</span>';
                    }
                    echo '</div>';
                } else {
                    // Login successful
                    echo '<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 mb-4 rounded relative" role="alert">';
                    echo '<strong class="font-bold">Success!</strong>';
                    echo '<span class="block sm:inline">You have successfully logged in.</span>';
                    echo '</div>';
                    wp_set_auth_cookie($user->ID, $remember_me);
                    wp_redirect(home_url('/')); // Redirect to homepage after successful login
                    exit;
                }
            } ?>
            <form class="space-y-6" action="<?= esc_url($_SERVER['REQUEST_URI']); ?>" method="POST">
                <div>
                    <label for="email" class="block text-sm font-medium leading-6 text-gray-900">Email adresa</label>
                    <div class="mt-2">
                        <input id="email" name="email" type="email" autocomplete="email" required class="block w-full rounded-md border-0 p-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium leading-6 text-gray-900">Fjalëkalimi</label>
                    <div class="mt-2">
                        <input id="password" name="password" type="password" autocomplete="current-password" required class="block w-full rounded-md border-0 p-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input id="remember-me" name="remember-me" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600">
                        <label for="remember-me" class="ml-3 block text-sm leading-6 text-gray-900">Më mbaj në mend</label>
                    </div>

                    <div class="text-sm leading-6">
                        <a href="#" class="font-semibold text-indigo-600 hover:text-indigo-500">Keni harruar fjalëkalimin?</a>
                    </div>
                </div>

                <div>
                    <button type="submit" class="flex w-full justify-center rounded-md bg-indigo-600 px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Hyr</button>
                </div>
            </form>

            <div>
                <div class="relative mt-10">
                    <div class="absolute inset-0 flex items-center" aria-hidden="true">
                        <div class="w-full border-t border-gray-200"></div>
                    </div>
                    <div class="relative flex justify-center text-sm font-medium leading-6">
                        <span class="bg-white px-6 "></span>
                    </div>
                </div>

                <div class="mt-6">
                    <?= do_shortcode('[login_with_google]'); ?>
                </div>
                <div class="mt-6">
                    <?= do_shortcode('[facebook-login]'); ?>
                </div>

            </div>
        </div>

        <p class="mt-10 text-center text-sm text-gray-500">
            Nuk jeni të regjistruar?
            <a href="<?= esc_url(home_url('/regjistrohu/')); ?>" class="font-semibold leading-6 text-indigo-600 hover:text-indigo-500">Regjistrohu!</a>
        </p>
    </div>
</div>

<?php get_footer(); ?>
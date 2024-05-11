<?php
// if (is_user_logged_in()) {
//     wp_redirect(esc_url(site_url('/dashboard')));
//     exit;
// }
/**

 * The template for displaying Retrieve Password

 *

 * Template Name:  Retrieve Password

 * @package meshije.al

 */

get_header();

?>

<div class="flex min-h-full flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <img class="mx-auto h-10 w-auto" src="https://tailwindui.com/img/logos/mark.svg?color=indigo&shade=600" alt="Your Company">
        <h2 class="mt-6 text-center text-2xl font-bold leading-9 tracking-tight text-gray-900">Kyçu në llogarinë tuaj</h2>
    </div>

    <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-[480px]">
        <div class="bg-white px-6 py-12 shadow sm:rounded-lg sm:px-12">
            <?php
            if (isset($_POST['user_login'])) {
                $errors = retrieve_password();
                if (is_wp_error($errors)) {

                    echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 mb-4 rounded relative" role="alert">';
                    foreach ($errors->get_error_messages() as $error) {
                        echo '<span class="block sm:inline">' . $error . '</span>';
                    }
                    echo '</div>';
                } else {
                    // Login successful
                    echo '<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 mb-4 rounded relative" role="alert">';
                    echo '<strong class="font-bold">Success!</strong>';
                    echo '<span class="block sm:inline">You have successfully logged in.</span>';
                    echo '</div>';
                }
            } ?>
            <form class="space-y-6" action="<?= esc_url($_SERVER['REQUEST_URI']); ?>" method="POST">
                <div>
                    <label for="user_login" class="block text-sm font-medium leading-6 text-gray-900">Email adresa</label>
                    <div class="mt-2">
                        <input id="user_login" name="user_login" type="email" autocomplete="email" required class="block w-full rounded-md border-0 p-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                    </div>
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
        </div>
    </div>

    <p class="mt-10 text-center text-sm text-gray-500">
        Nuk jeni të regjistruar?
        <a href="<?= esc_url(home_url('/regjistrohu/')); ?>" class="font-semibold leading-6 text-indigo-600 hover:text-indigo-500">Regjistrohu!</a>
    </p>
</div>
</div>

<?php get_footer(); ?>
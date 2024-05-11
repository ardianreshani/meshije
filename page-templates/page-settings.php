<?php
/*
 * Template Name: Settings
 * Description: Allow users to update their profiles from Frontend.
 */
if (!is_user_logged_in()) {
    wp_redirect(esc_url(site_url('/kycu-ne-llogarine-tuaj/')));
    exit;
}
get_header();

/* Get user info. */
global $current_user, $wp_roles;

$error = array();
/* If profile was saved, update profile. */
if ('POST' == $_SERVER['REQUEST_METHOD'] && !empty($_POST['action']) && $_POST['action'] == 'update-user') {

    /* Update user password. */
    if (!empty($_POST['pass1']) && !empty($_POST['pass2'])) {
        if ($_POST['pass1'] == $_POST['pass2'])
            wp_update_user(array('ID' => $current_user->ID, 'user_pass' => esc_attr($_POST['pass1'])));
        else
            $error[] = __('The passwords you entered do not match.  Your password was not updated.', 'profile');
    }

    /* Update user information. */
    if (!empty($_POST['url']))
        wp_update_user(array('ID' => $current_user->ID, 'user_url' => esc_url($_POST['url'])));
    if (!empty($_POST['email'])) {
        if (!is_email(esc_attr($_POST['email'])))
            $error[] = __('The Email you entered is not valid.  please try again.', 'profile');
        elseif (email_exists(esc_attr($_POST['email'])) != $current_user->ID)
            $error[] = __('This email is already used by another user.  try a different one.', 'profile');
        else {
            wp_update_user(array('ID' => $current_user->ID, 'user_email' => esc_attr($_POST['email'])));
        }
    }

    if (!empty($_POST['first-name']))
        update_user_meta($current_user->ID, 'first_name', esc_attr($_POST['first-name']));
    if (!empty($_POST['last-name']))
        update_user_meta($current_user->ID, 'last_name', esc_attr($_POST['last-name']));
    if (!empty($_POST['description']))
        update_user_meta($current_user->ID, 'description', esc_attr($_POST['description']));

    /* Redirect so the page will show updated info.*/
    /*I am not Author of this Code- I don't know why but it worked for me after changing below line to if ( count($error) == 0 ){ */
    if (count($error) == 0) {
        //action hook for plugins and extra fields saving
        do_action('edit_user_profile_update', $current_user->ID);
        wp_redirect(get_permalink());
        exit;
    }
}
if (have_posts()) : while (have_posts()) : the_post(); ?>

        <div id="post-<?php the_ID(); ?>">
            <div class="entry-content entry">
                <?php if (!is_user_logged_in()) : ?>
                    <p class="warning">
                        <?php _e('You must be logged in to edit your profile.', 'profile'); ?>
                    </p><!-- .warning -->
                <?php else : ?>
                    <?php if (count($error) > 0) echo '<p class="error">' . implode("<br />", $error) . '</p>'; ?>
                    <form method="post" id="adduser" action="<?php the_permalink(); ?>">
                        <p class="form-username">
                            <label for="first-name"><?php _e('First Name', 'profile'); ?></label>
                            <input class="text-input" name="first-name" type="text" id="first-name" value="<?php the_author_meta('first_name', $current_user->ID); ?>" />
                        </p><!-- .form-username -->
                        <p class="form-username">
                            <label for="last-name"><?php _e('Last Name', 'profile'); ?></label>
                            <input class="text-input" name="last-name" type="text" id="last-name" value="<?php the_author_meta('last_name', $current_user->ID); ?>" />
                        </p><!-- .form-username -->
                        <p class="form-email">
                            <label for="email"><?php _e('E-mail *', 'profile'); ?></label>
                            <input class="text-input" name="email" type="text" id="email" value="<?php the_author_meta('user_email', $current_user->ID); ?>" />
                        </p><!-- .form-email -->
                        <p class="form-url">
                            <label for="url"><?php _e('Website', 'profile'); ?></label>
                            <input class="text-input" name="url" type="text" id="url" value="<?php the_author_meta('user_url', $current_user->ID); ?>" />
                        </p><!-- .form-url -->
                        <p class="form-password">
                            <label for="pass1"><?php _e('Password *', 'profile'); ?> </label>
                            <input class="text-input" name="pass1" type="password" id="pass1" />
                        </p><!-- .form-password -->
                        <p class="form-password">
                            <label for="pass2"><?php _e('Repeat Password *', 'profile'); ?></label>
                            <input class="text-input" name="pass2" type="password" id="pass2" />
                        </p><!-- .form-password -->
                        <p class="form-textarea">
                            <label for="description"><?php _e('Biographical Information', 'profile') ?></label>
                            <textarea name="description" id="description" rows="3" cols="50"><?php the_author_meta('description', $current_user->ID); ?></textarea>
                        </p><!-- .form-textarea -->

                        <?php
                        //action hook for plugin and extra fields
                        do_action('edit_user_profile', $current_user);
                        ?>
                        <p class="form-submit">
                            <?php //echo $referer; 
                            ?>
                            <input name="updateuser" type="submit" id="updateuser" class="submit button" value="<?php _e('Update', 'profile'); ?>" />
                            <?php wp_nonce_field('update-user') ?>
                            <input name="action" type="hidden" id="action" value="update-user" />
                        </p><!-- .form-submit -->
                    </form><!-- #adduser -->
                <?php endif; ?>
            </div><!-- .entry-content -->
        </div><!-- .hentry .post -->
    <?php endwhile; ?>
<?php else : ?>
    <p class="no-data">
        <?php _e('Sorry, no page matched your criteria.', 'profile'); ?>
    </p><!-- .no-data -->
<?php endif; ?>
<div class="xl:pl-72">
    <!-- Settings forms -->
    <div class="divide-y divide-white/5">
        <div class="grid max-w-7xl grid-cols-1 gap-x-8 gap-y-10 px-4 py-16 sm:px-6 md:grid-cols-3 lg:px-8">
            <div>
                <h2 class="text-base font-semibold leading-7 text-black">Personal Information</h2>
                <p class="mt-1 text-sm leading-6 text-gray-400">Use a permanent address where you can receive mail.</p>
            </div>

            <form class="md:col-span-2">
                <div class="grid grid-cols-1 gap-x-6 gap-y-8 sm:max-w-xl sm:grid-cols-6">
                    <div class="col-span-full flex items-center gap-x-8">
                        <img src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80" alt="" class="h-24 w-24 flex-none rounded-lg bg-gray-800 object-cover">
                        <div>
                            <button type="button" class="rounded-md bg-white/10 px-3 py-2 text-sm font-semibold text-black shadow-sm hover:bg-white/20">Change avatar</button>
                            <p class="mt-2 text-xs leading-5 text-gray-400">JPG, GIF or PNG. 1MB max.</p>
                        </div>
                    </div>

                    <div class="sm:col-span-3">
                        <label for="first-name" class="block text-sm font-medium leading-6 text-black">First name</label>
                        <div class="mt-2">
                            <input type="text" name="first-name" id="first-name" autocomplete="given-name" class="block w-full rounded-md border-0 bg-white/5 py-1.5 text-black shadow-sm ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-inset focus:ring-indigo-500 sm:text-sm sm:leading-6">
                        </div>
                    </div>

                    <div class="sm:col-span-3">
                        <label for="last-name" class="block text-sm font-medium leading-6 text-black">Last name</label>
                        <div class="mt-2">
                            <input type="text" name="last-name" id="last-name" autocomplete="family-name" class="block w-full rounded-md border-0 bg-white/5 py-1.5 text-black shadow-sm ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-inset focus:ring-indigo-500 sm:text-sm sm:leading-6">
                        </div>
                    </div>

                    <div class="col-span-full">
                        <label for="email" class="block text-sm font-medium leading-6 text-black">Email address</label>
                        <div class="mt-2">
                            <input id="email" name="email" type="email" autocomplete="email" class="block w-full rounded-md border-0 bg-white/5 py-1.5 text-black shadow-sm ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-inset focus:ring-indigo-500 sm:text-sm sm:leading-6">
                        </div>
                    </div>

                    <div class="col-span-full">
                        <label for="username" class="block text-sm font-medium leading-6 text-black">Username</label>
                        <div class="mt-2">
                            <div class="flex rounded-md bg-white/5 ring-1 ring-inset ring-white/10 focus-within:ring-2 focus-within:ring-inset focus-within:ring-indigo-500">
                                <span class="flex select-none items-center pl-3 text-gray-400 sm:text-sm">example.com/</span>
                                <input type="text" name="username" id="username" autocomplete="username" class="flex-1 border-0 bg-transparent py-1.5 pl-1 text-black focus:ring-0 sm:text-sm sm:leading-6" placeholder="janesmith">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-8 flex">
                    <button type="submit" class="rounded-md bg-indigo-500 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-400 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-500">Save</button>
                </div>
            </form>
        </div>

        <div class="grid max-w-7xl grid-cols-1 gap-x-8 gap-y-10 px-4 py-16 sm:px-6 md:grid-cols-3 lg:px-8">
            <div>
                <h2 class="text-base font-semibold leading-7 text-black">Change password</h2>
                <p class="mt-1 text-sm leading-6 text-gray-400">Update your password associated with your account.</p>
            </div>

            <form class="md:col-span-2">
                <div class="grid grid-cols-1 gap-x-6 gap-y-8 sm:max-w-xl sm:grid-cols-6">

                    <div class="col-span-full">
                        <label for="new-password" class="block text-sm font-medium leading-6 text-black">New password</label>
                        <div class="mt-2">
                            <input id="new-password" name="new_password" type="password" autocomplete="new-password" class="block w-full rounded-md border-0 bg-white/5 py-1.5 text-black shadow-sm ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-inset focus:ring-indigo-500 sm:text-sm sm:leading-6">
                        </div>
                    </div>

                    <div class="col-span-full">
                        <label for="confirm-password" class="block text-sm font-medium leading-6 text-black">Confirm password</label>
                        <div class="mt-2">
                            <input id="confirm-password" name="confirm_password" type="password" autocomplete="new-password" class="block w-full rounded-md border-0 bg-white/5 py-1.5 text-black shadow-sm ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-inset focus:ring-indigo-500 sm:text-sm sm:leading-6">
                        </div>
                    </div>
                </div>

                <div class="mt-8 flex">
                    <button type="submit" class="rounded-md bg-indigo-500 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-400 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-500">Save</button>
                </div>
            </form>
        </div>



        <div class="grid max-w-7xl grid-cols-1 gap-x-8 gap-y-10 px-4 py-16 sm:px-6 md:grid-cols-3 lg:px-8">
            <div>
                <h2 class="text-base font-semibold leading-7 text-black">Delete account</h2>
                <p class="mt-1 text-sm leading-6 text-gray-400">No longer want to use our service? You can delete your account here. This action is not reversible. All information related to this account will be deleted permanently.</p>
            </div>

            <form class="flex items-start md:col-span-2">
                <button type="submit" class="rounded-md bg-red-500 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-400">Yes, delete my account</button>
            </form>
        </div>
    </div>
</div>


<?php get_footer(); ?>
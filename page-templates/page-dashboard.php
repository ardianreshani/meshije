<?php
/*
 * Template Name: Dashboard
 * Description: Custom dashboard template for displaying user's recipes.
 */
if (!is_user_logged_in()) {
    wp_redirect(esc_url(site_url('/dashboard')));
    exit;
}
get_header(); ?>

<div class="max-w-7xl mx-auto  px-2 sm:px-4 lg:px-8">



    <div class="my-10 max-w-3xl">
        <label for="comment" class="block text-4xl mb-10 font-medium leading-6 text-gray-900">Posto një recetë</label>
        <div class="mt-2">
            <label for="title">Emri i recetës</label>
            <input type="text" name="title" id="title" class="mt-2 create-recipe-title block w-full border-1 rounded-md border-gray-300 p-2 text-lg font-medium placeholder:text-gray-400 focus:ring-0" placeholder="">
        </div>
        <div class="mt-6">
            <label for="title">Përshkrimi i recestës</label>
            <textarea rows="10" name="description" id="description" class="mt-2 create-recipe-content block w-full resize-none border-1 rounded-md border-gray-300 p-2 text-gray-900 placeholder:text-gray-400 focus:ring-0 sm:text-sm sm:leading-6" placeholder="Write a description..."></textarea>
        </div>
        <span for="cover-photo" class="block text-sm font-medium leading-6 text-gray-900">Ngargoni fotot gjatë përgatitjes së recetës</span>

        <div id="preview" class="mt-6  rounded-lg border-2 border-dashed border-gray-900/25 px-6 py-10">
            <div class="meshije-image-upload mt-2 flex justify-center rounded-lg px-6 py-10">
                <div class="text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-300" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M1.5 6a2.25 2.25 0 012.25-2.25h16.5A2.25 2.25 0 0122.5 6v12a2.25 2.25 0 01-2.25 2.25H3.75A2.25 2.25 0 011.5 18V6zM3 16.06V18c0 .414.336.75.75.75h16.5A.75.75 0 0021 18v-1.94l-2.69-2.689a1.5 1.5 0 00-2.12 0l-.88.879.97.97a.75.75 0 11-1.06 1.06l-5.16-5.159a1.5 1.5 0 00-2.12 0L3 16.061zm10.125-7.81a1.125 1.125 0 112.25 0 1.125 1.125 0 01-2.25 0z" clip-rule="evenodd"></path>
                    </svg>
                    <div class="mt-4 flex text-sm leading-6 text-gray-600">
                        <label for="file-upload" class="relative cursor-pointer rounded-md bg-white font-semibold text-indigo-600 focus-within:outline-none focus-within:ring-2 focus-within:ring-indigo-600 focus-within:ring-offset-2 hover:text-indigo-500">
                            <span class=" inline-block  text-center">Ngarko një Fotografi</span>
                            <input id="file-upload" name="file-upload" type="file" multiple class="sr-only" accept="image/png, image/jpg, image/jpeg">
                        </label>
                    </div>
                    <p class="text-xs leading-5 text-gray-600">Lejohen PNG, JPG, JEPG formate!</p>
                </div>
            </div>
        </div>
        <!-- <div class="flex flex-wrap mb-2 w-full overflow-x-hidden">
            <div id="preview" class="empty:hidden mt-1 w-full grid gap-4 grid-cols-1 md:grid-cols-2 lg:grid-cols-4 rounded-md border-2 border-dashed border-gray-900/25 p-4 cursor-pointer">
            </div>
        </div> -->
        <div class="mt-6 flex-shrink-0">
            <button type="submit" class="create-recipe inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Create</button>
        </div>
    </div>
    <div class="grid grid-cols-4 gap-6">
        <?php
        while (have_posts()) {
            the_post();

            $user_recipes = new WP_Query(array(
                'post_type'      => 'user-recipes',
                'posts_per_page' => -1,
                'author'         => get_current_user_id()
            ));

            while ($user_recipes->have_posts()) {
                $user_recipes->the_post();
        ?>

                <div class="bg-white shadow sm:rounded-lmd mb-10 ">
                    <div class="px-4 py-5 sm:p-6">
                        <div class="flex-col gap-6 sm:flex sm:items-start sm:justify-between">
                            <?php if (get_the_post_thumbnail_url(get_the_id())) : ?>
                                <div class="rounded-md overflow-hidden">
                                    <img src="<?= get_the_post_thumbnail_url(get_the_id()) ?>" alt="images" />
                                </div>
                            <?php endif ?>
                            <div>
                                <h3 class="text-base font-semibold leading-6 text-gray-900"> <input class="user-recipe-title" value="<?= esc_attr(get_the_title()) ?>"></h3>
                                <div class="mt-2 max-w-xl text-sm text-gray-500">
                                    <textarea class="user-recipe-content block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6  ">
                                 <?= esc_attr(wp_strip_all_tags(get_the_content())) ?>
                                </textarea>
                                </div>
                            </div>
                            <div class="flex gap-3 mt-5 sm:mt-0 sm:flex sm:flex-shrink-0 sm:items-center">
                                <button type="button" class="edit-recipe inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50" data-recipe-id="<?php the_ID() ?>">Edit</button>
                                <button type="button" class="delete-recipe inline-flex items-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-400  focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-black " data-recipe-id="<?php the_ID() ?>">Delete</button>
                            </div>
                        </div>
                    </div>
                </div>

        <?php

            }

            wp_reset_postdata(); // Reset the query
        }
        ?>
    </div>
</div>
<!-- Delete post alert -->
<div id="meshije-delet-post" class="relative z-10 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <!--
    Background backdrop, show/hide based on modal state.

    Entering: "ease-out duration-300"
      From: "opacity-0"
      To: "opacity-100"
    Leaving: "ease-in duration-200"
      From: "opacity-100"
      To: "opacity-0"
  -->
    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>

    <div class="width-20 fixed inset-0 z-10 overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <!--
        Modal panel, show/hide based on modal state.

        Entering: "ease-out duration-300"
          From: "opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
          To: "opacity-100 translate-y-0 sm:scale-100"
        Leaving: "ease-in duration-200"
          From: "opacity-100 translate-y-0 sm:scale-100"
          To: "opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
      -->
            <div class="relative transform overflow-hidden rounded-lg bg-white px-4 pb-4 pt-5 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:p-6">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                        <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                        </svg>
                    </div>
                    <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                        <h3 class="text-base font-semibold leading-6 text-gray-900" id="modal-title">Fshij postimin</h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500">Jeni të sigurt se dëshironi të vazhdoni? Ky veprim do të fshijë postimin tuaj nga blogu. Veprimi nuk mund të kthehet mbrapsht.</p>
                        </div>
                    </div>
                </div>
                <div class="mt-5 sm:ml-10 sm:mt-4 sm:flex sm:pl-4">
                    <button type="button" id="meshije-delet-btn" class="inline-flex w-full justify-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500 sm:w-auto">Fshij</button>
                    <button type="button" id="meshije-cancel-btn" class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:ml-3 sm:mt-0 sm:w-auto">Anulo</button>
                </div>
            </div>
        </div>
    </div>
</div>


<?php
get_footer();
?>
<?php

/**
 * Adds a box to the main column on the Post and Page edit screens.
 */
function clegend_add_meta_box_testimonial()
{

    $screens = array('testimonial');

    foreach ($screens as $screen) {

        add_meta_box(
            'testimonial_id',
            __('Testimonial Author', 'clegend_textdomain'),
            'clegend_meta_box_callback_testimonial',
            $screen
        );
    }
}

add_action('add_meta_boxes', 'clegend_add_meta_box_testimonial');

/**
 * Prints the box content.
 *
 * @param WP_Post $post The object for the current post/page.
 */
function clegend_meta_box_callback_testimonial($post)
{

    // Add a nonce field so we can check for it later.
    wp_nonce_field('clegend_save_meta_box_data_testimonial', 'clegend_meta_box_nonce_testimonial');

    wp_enqueue_style(
        'clegend-bootgrid',
        plugins_url('../css/bootgrid.css', __FILE__)
    );

    /*
    * Use get_post_meta() to retrieve an existing value
    * from the database and use the value for the form.
    */
    $testimonial_author = get_post_meta($post->ID, 'testimonial_author', true);

    ?>

    <div class="row">
        <div class="col-md-6 col-sm-6">
            <h2>Name and Title</h2>
            <textarea name="testimonial_author" id="testimonial_author" cols="30"
                      rows="10"><?php echo $testimonial_author; ?></textarea>
        </div>
    </div>

    <?php
}

/**
 * When the post is saved, saves our custom data.
 *
 * @param int $post_id The ID of the post being saved.
 */
function clegend_save_meta_box_data_testimonial($post_id)
{

    /*
     * We need to verify this came from our screen and with proper authorization,
     * because the save_post action can be triggered at other times.
     */

    // Check if our nonce is set.
    if (!isset($_POST['clegend_meta_box_nonce_testimonial'])) {
        return;
    }

    // Verify that the nonce is valid.
    if (!wp_verify_nonce($_POST['clegend_meta_box_nonce_testimonial'], 'clegend_save_meta_box_data_testimonial')) {
        return;
    }

    // If this is an autosave, our form has not been submitted, so we don't want to do anything.
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    // Check the user's permissions.
    if (isset($_POST['post_type']) && 'testimonial' == $_POST['post_type']) {

        if (!current_user_can('edit_page', $post_id)) {
            return;
        }

    } else {

        if (!current_user_can('edit_post', $post_id)) {
            return;
        }
    }

    /* OK, it's safe for us to save the data now. */

    // Make sure that it is set.
    if (!isset($_POST['testimonial_author'])) {
        return;
    }

    // Sanitize user input.
    $testimonial_author = $_POST['testimonial_author'];

    // Update the meta field in the database.
    update_post_meta($post_id, 'testimonial_author', $testimonial_author);
}

add_action('save_post', 'clegend_save_meta_box_data_testimonial');
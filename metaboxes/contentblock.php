<?php

/**
 * Adds a box to the main column on the Content Block edit screens.
 */
function clegend_add_meta_box()
{
    $screens = ['contentblock'];

    foreach ($screens as $screen) {

        add_meta_box(
            'contentblock_id',
            __('Content Editor Area', 'clegend_textdomain'),
            'clegend_meta_box_callback',
            $screen
        );
    }
}

add_action('add_meta_boxes', 'clegend_add_meta_box');

/**
 * Prints the box content.
 *
 * @param WP_Post $post The object for the current post/page.
 */
function clegend_meta_box_callback($post)
{

    // Add a nonce field so we can check for it later.
    wp_nonce_field('clegend_save_meta_box_data', 'clegend_meta_box_nonce');

    wp_enqueue_style(
        'codemirror',
        plugins_url('../css/codemirror.css', __FILE__)
    );

    wp_enqueue_style(
        'clegend-bootgrid',
        plugins_url('../css/bootgrid.css', __FILE__)
    );

    wp_enqueue_style(
        'clegend-custom',
        plugins_url('../css/custom.css', __FILE__)
    );

    wp_enqueue_script(
        'codemirror',
        plugins_url('../js/codemirror.js', __FILE__),
        ['jquery']
    );

    wp_enqueue_script(
        'codemirror-css',
        plugins_url('../js/codemirror-css.js', __FILE__),
        ['jquery']
    );

    wp_enqueue_script(
        'custom-script',
        plugins_url('../js/script.js', __FILE__),
        ['jquery']
    );

    /*
    * Use get_post_meta() to retrieve an existing value
    * from the database and use the value for the form.
    */
    $valueHTML   = get_post_meta($post->ID, 'valueHTML_key', true);
    $valueCSS    = get_post_meta($post->ID, 'valueCSS_key', true);
    $valueCSSRes = get_post_meta($post->ID, 'valueCSSRes_key', true);

    ?>

  <div class="row">
    <div class="col-md-12 col-sm-6">
      <h2>HTML Content</h2>
      <textarea name="contentblockHTML" id="contentblockHTML" cols="30"
                rows="10"><?php echo htmlspecialchars($valueHTML); ?></textarea>
    </div>
    <div class="col-md-12 col-sm-6">
      <h2>CSS Styles</h2>
      <textarea name="contentblockCSS" id="contentblockCSS" cols="30"
                rows="10"><?php echo $valueCSS; ?></textarea>
    </div>
    <div class="col-md-12 col-sm-6">
      <h2>Responsive CSS Styles</h2>
      <textarea name="contentblockCSSResponsive" id="contentblockCSSResponsive" cols="30"
                rows="10"><?php echo $valueCSSRes; ?></textarea>
    </div>
  </div>

  <div class="row">
    <div class="col-md-12 text-center">
      <button type="button" class="button button-primary button-large bluebutton">PREVIEW CSS/HTML</button>
    </div>
  </div>

  <div id="preview-html-css">

  </div>
  <style type="text/css" id="css-part">

  </style>

    <?php

//echo '<input type="text" id="clegend_new_field" name="clegend_new_field" value="' . esc_attr($value) . '" size="25" />';
}

/**
 * When the post is saved, saves our custom data.
 *
 * @param int $post_id The ID of the post being saved.
 */
function clegend_save_meta_box_data($post_id)
{

    /*
     * We need to verify this came from our screen and with proper authorization,
     * because the save_post action can be triggered at other times.
     */

    // Check if our nonce is set.
    if (!isset($_POST['clegend_meta_box_nonce'])) {
        return;
    }

    // Verify that the nonce is valid.
    if (!wp_verify_nonce($_POST['clegend_meta_box_nonce'], 'clegend_save_meta_box_data')) {
        return;
    }

    // If this is an autosave, our form has not been submitted, so we don't want to do anything.
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    // Check the user's permissions.
    if (isset($_POST['post_type']) && 'contentblock' == $_POST['post_type']) {

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
    if (!isset($_POST['contentblockHTML']) && !isset($_POST['contentblockCSS']) && !isset($_POST['contentblockCSSResponsive'])) {
        return;
    }

    // Sanitize user input.
    $contentblockHTML_new   = $_POST['contentblockHTML'];
    $contentblockCSS_new    = $_POST['contentblockCSS'];
    $contentblockCSSRes_new = $_POST['contentblockCSSResponsive'];

    // Update the meta field in the database.
    update_post_meta($post_id, 'valueHTML_key', $contentblockHTML_new);
    update_post_meta($post_id, 'valueCSS_key', $contentblockCSS_new);
    update_post_meta($post_id, 'valueCSSRes_key', $contentblockCSSRes_new);
}

add_action('save_post', 'clegend_save_meta_box_data');

<?php

/**
 * Adds a box to the main column on the Post and Page edit screens.
 */
function clegend_add_page_meta_box()
{

    $screens = array('page');

    foreach ($screens as $screen) {

        add_meta_box(
            'contentblock_id',
            __('Content Editor Area', 'clegend_textdomain'),
            'clegend_page_meta_box_callback',
            $screen
        );
    }
}

add_action('add_meta_boxes', 'clegend_add_page_meta_box');

/**
 * Prints the box content.
 *
 * @param WP_Post $post The object for the current post/page.
 */
function clegend_page_meta_box_callback($post)
{

    // Add a nonce field so we can check for it later.
    wp_nonce_field('clegend_page_save_meta_box_data', 'clegend_page_meta_box_nonce');

    wp_enqueue_style(
        'custompagecss',
        plugins_url('../css/custompagecss.css', __FILE__)
    );

    wp_enqueue_script(
        'custom-blocks',
        plugins_url('../js/blocks.js', __FILE__),
        array('jquery')
    );

    wp_enqueue_style(
        'clegend-bootgrid',
        plugins_url('../css/bootgrid.css', __FILE__)
    );

    add_action( 'admin_footer', 'my_action_javascript' ); // Write our JS below here

    function my_action_javascript() { ?>
        <script type="text/javascript" >
            var GLOBALROUTE = "<?php echo get_bloginfo("url"); ?>/wp-admin/admin-ajax.php";
        </script> <?php
    }

    /*
    * Use get_post_meta() to retrieve an existing value
    * from the database and use the value for the form.
    */
    $cb_ids = get_post_meta($post->ID, 'cb_ids', true);

    ?>

    <div class="container-fluid">

        <div class="row">

            <div class="col-md-6">

                <h3>Choose block</h3>

                <?php

                $contentBlocks = array(
                    "post_type" => "contentblock",
                    "showposts" => -1
                );

                $chosen = new WP_Query($contentBlocks);

                ?>

                <select name="contentblock" id="contentblock">

                    <?php

                    while ($chosen->have_posts()) : $chosen->the_post();

                        if (!in_array(get_the_ID(), $cb_ids)) {
                            ?>
                            <option value="<?php the_ID(); ?>"><?php the_title() ?></option>
                            <?php
                        }

                    endwhile;
                    wp_reset_query();
                    ?>
                </select>

                <br><br>

                <button type="button" class="button button-primary button-small" id="add-content-block">Add New Block
                </button>

            </div>

        </div>

        <div class="row">
            <div class="col-md-12 content-blocks">

                <?php
//                echo "<pre>";
//                print_r($cb_ids);
//                echo "</pre>";
                if (!empty($cb_ids)) :

                    $contentBlocks = array(
                        "post_type" => "contentblock",
                        "post__in" => $cb_ids,
                        'orderby' => 'post__in',
                        "showposts" => -1
                    );

                    $chosen = new WP_Query($contentBlocks);

                    while ($chosen->have_posts()) : $chosen->the_post();
                        ?>

                        <div class="block-item" data-postid="<?php the_ID(); ?>">
                            <span class="dragme">
                                <i class="fa fa-arrows"></i>
                            </span>
                            <strong>
                                <?php the_title(); ?>
                            </strong>
                            <div class="optionsme">
                                <a href="<?php echo get_edit_post_link(get_the_ID(), "") ?>"><i
                                        class="fa fa-external-link"></i></a>
                                <i class="fa fa-remove"></i>
                            </div>
                            <input type="hidden" class="contentblock-title" value="<?php the_title(); ?>">
                            <input type="hidden" name="contentblockgroup[]" class="contentblock-id"
                                   value="<?php the_ID(); ?>">
                        </div>

                        <?php

                    endwhile;
                    wp_reset_query();

                endif;
                ?>
            </div>
        </div>

    </div>

    <?php

}


function clegend_page_save_meta_box_data($post_id)
{

    /*
     * We need to verify this came from our screen and with proper authorization,
     * because the save_post action can be triggered at other times.
     */

    // Check if our nonce is set.
    if (!isset($_POST['clegend_page_meta_box_nonce'])) {
        return;
    }

    // Verify that the nonce is valid.
    if (!wp_verify_nonce($_POST['clegend_page_meta_box_nonce'], 'clegend_page_save_meta_box_data')) {
        return;
    }

    // If this is an autosave, our form has not been submitted, so we don't want to do anything.
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    // Check the user's permissions.
    if (isset($_POST['post_type']) && 'page' == $_POST['post_type']) {

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
    if (!isset($_POST['contentblockgroup'])) {
        update_post_meta($post_id, 'cb_ids', "");
        return;
    }
//    echo "<pre>";print_r($_POST['contentblockgroup']);exit;

    // Update the meta field in the database.
    update_post_meta($post_id, 'cb_ids', $_POST['contentblockgroup']);
}

add_action('save_post', 'clegend_page_save_meta_box_data');


add_action("wp_ajax_get_content_block_data", "get_result");
add_action("wp_ajax_nopriv_get_content_block_data", "get_result");

function get_result()
{

    $postReturn = get_post($_POST["contentblockid"]);

    $valueHTML = strip_tags(get_post_meta($postReturn->ID, 'valueHTML_key', true));

    //$postReturn[] = $valueHTML;

    $words = explode(" ", $valueHTML);
    $valueHTML = implode(" ", array_splice($words, 0, 10)) . "[...]";

    $bool = true;

    echo json_encode(
        array(
            "data" => $postReturn,
            "contentblockUID" => get_edit_post_link($postReturn->ID, ""),
            "contentblockText" => $valueHTML,
            "bool" => $bool
        )
    );
    die(0);


}
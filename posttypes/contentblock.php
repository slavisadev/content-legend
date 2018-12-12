<?php

add_action('init', 'clegend_post_types_init');

/**
 * register all post types
 */
function clegend_post_types_init()
{
    $labels = [
        'name'               => _x('Content blocks', 'post type general name', 'toposiguranje'),
        'singular_name'      => _x('Content block', 'post type singular name', 'toposiguranje'),
        'menu_name'          => _x('Content blocks', 'admin menu', 'toposiguranje'),
        'name_admin_bar'     => _x('Content block', 'add new on admin bar', 'toposiguranje'),
        'add_new'            => _x('Add New', 'content block', 'toposiguranje'),
        'add_new_item'       => __('Add New Content block', 'toposiguranje'),
        'new_item'           => __('New Content block', 'toposiguranje'),
        'edit_item'          => __('Edit Content block', 'toposiguranje'),
        'view_item'          => __('View Content block', 'toposiguranje'),
        'all_items'          => __('All Content blocks', 'toposiguranje'),
        'search_items'       => __('Search Content blocks', 'toposiguranje'),
        'parent_item_colon'  => __('Parent Content blocks:', 'toposiguranje'),
        'not_found'          => __('No content blocks found.', 'toposiguranje'),
        'not_found_in_trash' => __('No content blocks found in Trash.', 'toposiguranje'),
    ];

    $args = [
        'labels'             => $labels,
        'description'        => __('Description.', 'content-legend'),
        'public'             => false,
        'publicly_queryable' => false,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => ['slug' => 'contentblock'],
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => null,
        'supports'           => ['title', 'author'],
    ];

    register_post_type('contentblock', $args);
}

<?php

/////////////////////////////////////
// Custom Post Type setup - Technologies
/////////////////////////////////////
function technology_post_type() {
    $labels = array(
        'name' => 'Technologies',
        'singular_name' => 'Technology',
        'add_new' => 'Add New',
        'add_new_item' => 'Add New Technology',
        'edit_item' => 'Edit Technology',
        'new_item' => 'New Technology',
        'view_item' => 'View Technology',
        'search_items' => 'Search Technologies',
        'not_found' => 'No Technologies found',
        'not_fount_in_trash' => 'No Technologies found in Trash'
    );
    $args = array(
        'labels' => $labels,
        'public' => true,
        'menu_icon' => 'dashicons-admin-tools',
        'query_var' => true,
        'rewrite' => true,
        'capability_type' => 'page',
        'has_archive' => true,
        'hierarchical' => true,
        'menu_position' => 6,
        'supports' => array(
            'page-attributes',
            'title',
            'editor',
            'excerpt',
            'author',
            'revisions'
        ),
        'taxonomies' => [  'method_keywords', 'method_status' ],
        # Gutenberg editor config stuff from here on
        'show_in_rest' => true,
        // 'template' => array(),
    );
    register_post_type('technologies', $args);
}
add_action('init', 'technology_post_type');

// Don't paginate archives
function ngi_technologies_query( $query ){
    if(!is_admin() && $query->is_post_type_archive('technologies') && $query->is_main_query() ){
        $query->set( 'posts_per_page', -1 );
        // Sort by given order, then title
        $query->set( 'order', 'ASC' );
        $query->set( 'orderby', 'menu_order title' );
    }
}
add_action( 'pre_get_posts', 'ngi_technologies_query' );

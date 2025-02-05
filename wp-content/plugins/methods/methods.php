<?php

/////////////////////////////////////
// Custom Post Type setup - Methods
/////////////////////////////////////
function method_post_type() {
    $labels = array(
        'name' => 'Methods',
        'singular_name' => 'Method',
        'add_new' => 'Add New',
        'add_new_item' => 'Add New Method',
        'edit_item' => 'Edit Method',
        'new_item' => 'New Method',
        'view_item' => 'View Method',
        'search_items' => 'Search Methods',
        'not_found' => 'No Methods found',
        'not_fount_in_trash' => 'No Methods found in Trash'
    );
    $args = array(
        'labels' => $labels,
        'public' => true,
        'menu_icon' => 'dashicons-media-text',
        'query_var' => true,
        'rewrite' => true,
        'capability_type' => 'page',
        'has_archive' => true,
        'hierarchical' => false,
        'menu_position' => 6.1,
        'supports' => array(
            'title',
            'editor',
            'excerpt',
            'author',
            'revisions',
            'page-attributes'
        ),
        'taxonomies' => [ 'applications', 'method_keywords', 'method_status' ],
        # Gutenberg editor config stuff from here on
        'show_in_rest' => true,
        'template' => array(
            array('core/heading', array('content' => 'Sample Requirements')),
            array('core/list'),
            array('core/heading', array('content' => 'How to evaluate the sample quality')),
            array('core/paragraph', array('content' => 'We check your samples upon arrival, however we still require our users to do their own QC steps before sending samples. For this library prep method, we require:')),
            array('core/list'),
            array('core/paragraph', array('content' => 'If you are not able to carry out these steps, or your samples are below the required thresholds, please get in touch.')),
            array('core/heading', array('content' => 'What we do with your samples')),
            array('core/paragraph'),
            array('core/heading', array('content' => 'Library Preparation', 'level' => '3')),
            array('core/paragraph'),
            array('core/heading', array('content' => 'Library QC and Sequencing', 'level' => '3')),
            array('core/paragraph'),
            array('core/heading', array('content' => 'Expected Results')),
            array('core/paragraph'),
            array('core/heading', array('content' => 'Bioinformatics')),
            array('core/paragraph'),
        ),
    );
    register_post_type('methods', $args);
}
add_action('init', 'method_post_type');

// Don't paginate archives
function ngi_methods_query( $query ){
    if(!is_admin() && $query->is_post_type_archive('methods') && $query->is_main_query() ){
        $query->set( 'posts_per_page', -1 );
        // Sort by given order, then title
        $query->set( 'order', 'ASC' );
        $query->set( 'orderby', 'menu_order title' );
    }
}
add_action( 'pre_get_posts', 'ngi_methods_query' );

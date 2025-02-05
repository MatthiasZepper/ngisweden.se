<?php

/////////////////////////////////////
// Custom Post Type setup - Bioinformatics Analyses
/////////////////////////////////////
function bioinformatics_post_type() {
    $labels = array(
        'name' => 'Bioinformatics',
        'singular_name' => 'Bioinformatics',
        'add_new' => 'Add New',
        'add_new_item' => 'Add New Bioinformatics Analysis',
        'edit_item' => 'Edit Bioinformatics Analysis',
        'new_item' => 'New Bioinformatics Analysis',
        'view_item' => 'View Bioinformatics Analysis',
        'search_items' => 'Search Bioinformatics Analyses',
        'not_found' => 'No Bioinformatics Analyses found',
        'not_fount_in_trash' => 'No Bioinformatics Analyses found in Trash'
    );
    $args = array(
        'labels' => $labels,
        'public' => true,
        'menu_icon' => 'dashicons-desktop',
        'query_var' => true,
        'rewrite' => true,
        'capability_type' => 'page',
        'has_archive' => true,
        'hierarchical' => false,
        'menu_position' => 6.3,
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
            array('core/paragraph', array('placeholder' => 'Introduction paragraph describing the pipeline. Slightly longer version of the Excerpt, which will be shown above.')),
            array('core/shortcode', array('text' => '[github_badge repo=https://github.com/githubtraining/hellogitworld]')),
            array('core/heading', array('content' => 'When we run analysis')),
            array('core/paragraph', array('content' => 'We run this analysis routinely for all XXX projects where we have prepared the sequencing library in-house. If you have prepared a library yourself and we are just sequencing, please get in touch and mention that you would like us to run this analysis.')),
            array('core/paragraph', array('content' => 'The analysis works with any of the species that have a reference genome available in <a href="https://ewels.github.io/AWS-iGenomes/" target="_blank">AWS-iGenomes</a>. If in doubt, please ask whether we can run the pipeline for you.')),
            array('core/heading', array('content' => 'Input data')),
            array('core/paragraph', array('placeholder' => 'For example, FastQ files, a reference genome and some metadata.')),
            array('core/list'),
            array('core/heading', array('content' => 'Results')),
            array('core/paragraph', array('placeholder' => 'Short description of what results the pipeline generates. Link to repository docs for detail.')),
            array('core/list'),
            array('core/heading', array('content' => 'Example Report')),
            array('core/paragraph', array('placeholder' => 'Upload one or more example outputs. eg. a MultiQC report.')),
        ),
    );
    register_post_type('bioinformatics', $args);
}
add_action('init', 'bioinformatics_post_type');

// Don't paginate technology archives
function ngi_bioinformatics_query( $query ){
    if(!is_admin() && $query->is_post_type_archive('bioinformatics') && $query->is_main_query() ){
        $query->set( 'posts_per_page', -1 );
        // Sort by given order, then title
        $query->set( 'order', 'ASC' );
        $query->set( 'orderby', 'menu_order title' );
    }
}
add_action( 'pre_get_posts', 'ngi_bioinformatics_query' );

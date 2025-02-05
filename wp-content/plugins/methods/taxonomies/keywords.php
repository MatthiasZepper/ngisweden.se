<?php

//////////////////////////
// Methods Keywords
//////////////////////////

// Set up the taxonomy (categories)
function create_method_tax_keywords() {
    $keyword_labels = array(
        'name'                       => __( 'Associated Keywords' ),
        'singular_name'              => __( 'Associated Keyword' ),
        'search_items'               => __( 'Search Keywords' ),
        'popular_items'              => __( 'Popular Keywords' ),
        'all_items'                  => __( 'All Keywords' ),
        'parent_item'                => null,
        'parent_item_colon'          => null,
        'edit_item'                  => __( 'Edit Keyword' ),
        'update_item'                => __( 'Update Keyword' ),
        'add_new_item'               => __( 'Add New Keyword' ),
        'new_item_name'              => __( 'New Keyword Name' ),
        'separate_items_with_commas' => __( 'Separate keywords with commas' ),
        'add_or_remove_items'        => __( 'Add or remove keywords' ),
        'choose_from_most_used'      => __( 'Choose from the most used keywords' ),
        'not_found'                  => __( 'No keywords found.' ),
        'menu_name'                  => __( 'Keywords' ),
    );
    $keyword_args = array(
        'labels'                     => $keyword_labels,
        'hierarchical'               => false,
        'public'                     => true,
        'show_ui'                    => true,
        'show_admin_column'          => true,
        'show_in_nav_menus'          => true,
        'show_in_rest'               => true,
        'show_tagcloud'              => true,
        'update_count_callback'      => '_update_post_term_count',
        'query_var'                  => true,
    );
    register_taxonomy('method_keywords', 'methods', $keyword_args);
}
add_action( 'init', 'create_method_tax_keywords', 0 );

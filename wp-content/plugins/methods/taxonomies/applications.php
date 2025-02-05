<?php

//////////////////////////
// Applications Taxonomy
//////////////////////////

// Set up the taxonomy (categories)
function create_method_tax_applications() {
    $cat_labels = array(
        'name'                       => _x( 'Applications', 'Taxonomy General Name', 'text_domain' ),
        'singular_name'              => _x( 'Application', 'Taxonomy Singular Name', 'text_domain' ),
        'menu_name'                  => __( 'Applications', 'text_domain' ),
        'all_items'                  => __( 'All Applications', 'text_domain' ),
        'parent_item'                => __( 'Parent Application', 'text_domain' ),
        'parent_item_colon'          => __( 'Parent Application:', 'text_domain' ),
        'new_item_name'              => __( 'New Application', 'text_domain' ),
        'add_new_item'               => __( 'Add Application', 'text_domain' ),
        'edit_item'                  => __( 'Edit Application', 'text_domain' ),
        'update_item'                => __( 'Update Application', 'text_domain' ),
        'view_item'                  => __( 'View Application', 'text_domain' ),
        'separate_items_with_commas' => __( 'Separate applications with commas', 'text_domain' ),
        'add_or_remove_items'        => __( 'Add or remove applications', 'text_domain' ),
        'choose_from_most_used'      => __( 'Choose from the most used', 'text_domain' ),
        'popular_items'              => __( 'Popular Applications', 'text_domain' ),
        'search_items'               => __( 'Search Applications', 'text_domain' ),
        'not_found'                  => __( 'No applications found', 'text_domain' ),
        'no_terms'                   => __( 'No applications', 'text_domain' ),
        'items_list'                 => __( 'Applications list', 'text_domain' ),
        'items_list_navigation'      => __( 'Applications list navigation', 'text_domain' ),
    );
    $cat_args = array(
        'labels'                     => $cat_labels,
        'hierarchical'               => true,
        'public'                     => true,
        'show_ui'                    => true,
        'show_admin_column'          => true,
        'show_in_nav_menus'          => true,
        'show_in_rest'               => true,
        'show_tagcloud'              => true,
    );
    register_taxonomy( 'applications', 'methods', $cat_args );
}
add_action( 'init', 'create_method_tax_applications', 0 );


// Add custom fields to the Applications taxonomy
function applications_taxonomy_custom_fields($tag) {
    $page_link = '<a class="button button-primary" disabled>Edit Page</a>';
    $application_page_selected = false;
    $application_icon = '';
    if(is_object($tag)){
        $term_meta = get_option( "application_page_".$tag->term_id );
        if(isset($term_meta['application_page']) && $term_meta['application_page']){
            $page_link = '<a href="'.get_edit_post_link($term_meta['application_page']).'" class="button button-primary">Edit Page</a>';
            $application_page_selected = $term_meta['application_page'];
        }
        if(isset($term_meta['application_icon']) && $term_meta['application_icon']){
            $application_icon = $term_meta['application_icon'];
        }
    }
    ?>
    <tr class="form-field">
        <th scope="row" valign="top">
            <label for="application_page"><?php _e('Application page'); ?></label>
        </th>
        <td>
            <?php wp_dropdown_pages(array(
                'selected' => $application_page_selected,
                'id' => 'application_page',
                'name' => 'application_page',
                'show_option_none'  => '[ choose a page ]',
                'option_none_value' => 0,
                // Allow any page to be linked, even if a draft
                'post_status' => [ 'publish', 'draft', 'pending' ],
            ));
            echo $page_link;
            ?>
            <p><?php _e('Used so that we can write more than just a short description box.'); ?></p>
        </td>
    </tr>
    <tr class="form-field">
        <th scope="row" valign="top">
            <label for="application_icon"><?php _e('Application Icon'); ?></label>
        </th>
        <td>
            <input type="text" name="application_icon" id="application_icon" size="25" style="width:60%;" value="<?php echo $application_icon; ?>"><br />
            <p><?php _e('Find an icon and click + copy the text associated into the above box.'); ?>
            <a href="<?php echo get_template_directory_uri() ?>/includes/icons/index.php" target="_blank"><?php _e('Click here to find icon URLs'); ?></a></p>
        </td>
    </tr>
    <?php
}
// A callback function to save our extra taxonomy field(s)
function save_applications_tax_custom_fields( $term_id ) {
    $term_meta = get_option( "application_icon_".$term_id );
    if ( isset( $_POST['application_page'] ) ) {
        $term_meta['application_page'] = $_POST['application_page'];
    }
    if ( isset( $_POST['application_icon'] ) ) {
        $term_meta['application_icon'] = $_POST['application_icon'];
    }
    update_option( "application_page_".$term_id, $term_meta );
}
add_action( 'applications_add_form_fields', 'applications_taxonomy_custom_fields', 10, 2 );
add_action( 'applications_edit_form_fields', 'applications_taxonomy_custom_fields', 10, 2 );
add_action( 'edited_applications', 'save_applications_tax_custom_fields', 10, 2 );
add_action( 'create_applications', 'save_applications_tax_custom_fields', 10, 2 );

// Show this in the table of applications
function applications_pageurl_column_header( $columns ){
    $columns['header_name'] = 'Application Page';
    return $columns;
}
add_filter( "manage_edit-applications_columns", 'applications_pageurl_column_header', 10);

function applications_pageurl_column_content( $value, $column_name, $tax_id ){
    $term_meta = get_option( "application_page_".$tax_id );
    if(isset($term_meta['application_page'])){
        $ptitle = get_the_title($term_meta['application_page']);
        $purl = get_page_link($term_meta['application_page']);
        echo '<a href="'.$purl.'">'.$ptitle.'</a>';
    } else {
        echo '-';
    }
}
add_action( "manage_applications_custom_column", 'applications_pageurl_column_content', 10, 3);


// Sort archive taxonomy pages by the order flag, instead of date
add_action( 'pre_get_posts', 'ngi_archive_change_sort_order');
function ngi_archive_change_sort_order($query){
    if(is_tax('applications')){
        $query->set( 'order', 'ASC' );
        $query->set( 'orderby', 'menu_order title' );
    }
};

<?php

//////////////////////////
// Method Status Taxonomy
//////////////////////////

// Set up the taxonomy (categories)
function create_method_tax_status() {
    $cat_labels = array(
        'name'                       => _x( 'Status', 'Taxonomy General Name', 'text_domain' ),
        'singular_name'              => _x( 'Status', 'Taxonomy Singular Name', 'text_domain' ),
        'menu_name'                  => __( 'Statuses', 'text_domain' ),
        'all_items'                  => __( 'All Statuses', 'text_domain' ),
        'parent_item'                => __( 'Parent Status', 'text_domain' ),
        'parent_item_colon'          => __( 'Parent Status:', 'text_domain' ),
        'new_item_name'              => __( 'New Status', 'text_domain' ),
        'add_new_item'               => __( 'Add Status', 'text_domain' ),
        'edit_item'                  => __( 'Edit Status', 'text_domain' ),
        'update_item'                => __( 'Update Status', 'text_domain' ),
        'view_item'                  => __( 'View Status', 'text_domain' ),
        'separate_items_with_commas' => __( 'Separate statuses with commas', 'text_domain' ),
        'add_or_remove_items'        => __( 'Add or remove statuses', 'text_domain' ),
        'choose_from_most_used'      => __( 'Choose from the most used', 'text_domain' ),
        'popular_items'              => __( 'Popular Statuses', 'text_domain' ),
        'search_items'               => __( 'Search Statuses', 'text_domain' ),
        'not_found'                  => __( 'No statuses found', 'text_domain' ),
        'no_terms'                   => __( 'No statuses', 'text_domain' ),
        'items_list'                 => __( 'Statuses list', 'text_domain' ),
        'items_list_navigation'      => __( 'Statuses list navigation', 'text_domain' ),
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
    register_taxonomy( 'method_status', 'methods', $cat_args );
}
add_action( 'init', 'create_method_tax_status', 0 );


// Add custom fields to the Status taxonomy
function method_status_taxonomy_custom_fields($tag) {
    $status_colour = get_option( "method_status_colour_".$tag->term_id );
    ?>
    <tr class="form-field">
        <th scope="row" valign="top">
            <label for="method_status_colour"><?php _e('Status colour'); ?></label>
        </th>
        <td>
            <select name="method_status_colour" id="method_status_colour">
                <option value="red"<?php if($status_colour == 'red'){ echo ' selected="selected"'; } ?>>Red</option>
                <option value="green"<?php if($status_colour == 'green'){ echo ' selected="selected"'; } ?>>Green</option>
                <option value="blue"<?php if($status_colour == 'blue'){ echo ' selected="selected"'; } ?>>Blue</option>
                <option value="turquoise"<?php if($status_colour == 'turquoise'){ echo ' selected="selected"'; } ?>>Turquoise</option>
                <option value="orange"<?php if($status_colour == 'orange'){ echo ' selected="selected"'; } ?>>Orange</option>
            </select>
            <p><?php _e('Set the colour of the status label'); ?></p>
        </td>
    </tr>
    <?php
}
// A callback function to save our extra taxonomy field(s)
function save_method_status_tax_custom_fields( $term_id ) {
    $term_meta = get_option( "method_status_colour_".$term_id );
    if ( isset( $_POST['method_status_colour'] ) ) {
        update_option( "method_status_colour_".$term_id, $_POST['method_status_colour'] );
    }
}
add_action( 'method_status_add_form_fields', 'method_status_taxonomy_custom_fields', 10, 2 );
add_action( 'method_status_edit_form_fields', 'method_status_taxonomy_custom_fields', 10, 2 );
add_action( 'edited_method_status', 'save_method_status_tax_custom_fields', 10, 2 );
add_action( 'create_method_status', 'save_method_status_tax_custom_fields', 10, 2 );

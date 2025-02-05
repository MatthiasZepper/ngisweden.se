<?php

function add_meta_boxes_methods_link_tech() {
    add_meta_box(
        'methods_tech_link_metabox',
        'Relevant Technologies',
        'technologies_link_metabox_fields',
        'methods',
        'side',
        'low'
    );
}
add_action( 'add_meta_boxes_methods', 'add_meta_boxes_methods_link_tech' );

function technologies_link_metabox_fields() {

    echo '<input type="hidden" name="technologies_nonce" value="'.wp_create_nonce( basename( __FILE__ ) ).'" />';
    echo '<div style="padding: 10px 0 0;">';

    $all_technologies = wp_list_pages(array(
        'post_type' => 'technologies',
        'title_li' => null,
        'walker' => new NGIMethods_Hierarchical_Metabox_Checkboxes()
    ));

    echo '</div>';

}

add_action( 'save_post', 'save_technologies_link_metabox_fields' );
function save_technologies_link_metabox_fields( $post_id ) {
    // only run this for series
    if (get_post_type( $post_id ) != 'methods'){ return $post_id; }
    // verify nonce
    if ( empty( $_POST['technologies_nonce'] ) || !wp_verify_nonce( $_POST['technologies_nonce'], basename( __FILE__ ) ) ){
        return $post_id;
    }
    // check autosave
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ){ return $post_id; }
    // check permissions
    if ( !current_user_can( 'edit_post', $post_id ) ){ return $post_id; }

    // save
    update_post_meta( $post_id, '_technologies', array_map( 'intval', $_POST['technologies'] ) );
}



class NGIMethods_Hierarchical_Metabox_Checkboxes extends Walker_Page {

    public function start_lvl( &$output, $depth = 0, $args = array() ) {
        $output .= '<div class="editor-post-taxonomies__hierarchical-terms-subchoices">';
    }

    public function end_lvl( &$output, $depth = 0, $args = array() ) {
        $output .= "</div>";
    }

    public function start_el( &$output, $page, $depth = 0, $args = array(), $current_page = 0 ) {

        global $post;
        $selected_technologies = get_post_meta($post->ID, '_technologies', true);

        $selected = '';
        if(is_array($selected_technologies) && in_array( $page->ID, $selected_technologies )){
            $selected = ' checked="checked"';
        }

        $output .= '<div style="margin-bottom: 8px;">';
        $output .= '<input id="tech_page_link_'.$page->ID.'" name="technologies[]" type="checkbox" value="'.$page->ID.'"'.$selected.'>';
        $output .= '<label for="tech_page_link_'.$page->ID.'">'.$page->post_title.'</label>';
        $output .= '</div>';

    }

    public function end_el( &$output, $page, $depth = 0, $args = array() ) {

    }

}

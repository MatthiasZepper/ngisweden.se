<?php

function add_meta_boxes_methods_link_bioinfo() {
    add_meta_box(
        'methods_bioinfo_link_metabox',
        'Bioinformatics Methods',
        'bioinformatics_link_metabox_fields',
        'methods',
        'side',
        'low'
    );
}
add_action( 'add_meta_boxes_methods', 'add_meta_boxes_methods_link_bioinfo' );

function bioinformatics_link_metabox_fields() {
    global $post;
    $selected_bioinformatics = get_post_meta($post->ID, '_bioinformatics', true);
    $all_bioinformatics = get_posts( array(
        'post_type' => 'bioinformatics',
        'numberposts' => -1,
        'orderby' => 'post_title',
        'order' => 'ASC'
    ) );
    echo '<input type="hidden" name="bioinformatics_nonce" value="'.wp_create_nonce( basename( __FILE__ ) ).'" />';
    echo '<div style="padding: 10px 0 0;">';
    foreach ( $all_bioinformatics as $bioinfo ){
        $selected = '';
        if(is_array($selected_bioinformatics) && in_array( $bioinfo->ID, $selected_bioinformatics )){
            $selected = ' checked="checked"';
        }
        echo '<div style="margin-bottom: 8px;">';
        echo '<input id="bioinfo_page_link_'.$bioinfo->ID.'" name="bioinformatics[]" type="checkbox" value="'.$bioinfo->ID.'"'.$selected.'>';
        echo '<label for="bioinfo_page_link_'.$bioinfo->ID.'">'.$bioinfo->post_title.'</label>';
        echo '</div>';
    }
    echo '</div>';
}

add_action( 'save_post', 'save_bioinformatics_link_metabox_fields' );
function save_bioinformatics_link_metabox_fields( $post_id ) {
    // only run this for series
    if (get_post_type( $post_id ) != 'methods'){ return $post_id; }
    // verify nonce
    if ( empty( $_POST['bioinformatics_nonce'] ) || !wp_verify_nonce( $_POST['bioinformatics_nonce'], basename( __FILE__ ) ) ){
        return $post_id;
    }
    // check autosave
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ){ return $post_id; }
    // check permissions
    if ( !current_user_can( 'edit_post', $post_id ) ){ return $post_id; }

    // save
    update_post_meta( $post_id, '_bioinformatics', array_map( 'intval', $_POST['bioinformatics'] ) );
}

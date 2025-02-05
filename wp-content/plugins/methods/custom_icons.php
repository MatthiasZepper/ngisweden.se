<?php

function add_meta_boxes_methods_icon() {
    add_meta_box(
        'methods_icon_metabox',
        'Custom Icon',
        'methods_icon_metabox_fields',
        ['methods', 'technologies', 'bioinformatics'],
        'side',
        'low'
    );
}
add_action('add_meta_boxes', 'add_meta_boxes_methods_icon');

function methods_icon_metabox_fields() {

    global $post;
    $curr_icon = get_post_meta($post->ID, '_ngi_post_icon', true);
    wp_nonce_field(basename(__FILE__), 'methods_icon_metabox_nonce');

    echo '<label for="ngi_icon_image_source">Icon URL</label>';
    echo '<input type="text" id="ngi_icon_image_source" name="ngi_icon_image_source" style="width:100%;" value="'.$curr_icon.'">';
    echo '<p style="margin: 0.5rem 0 0;">Find an icon and click + copy the text associated into the above box.</p>';
    echo '<p><a href="'.get_template_directory_uri().'/includes/icons/index.php" target="_blank">Click here to find icon URLs</a></p>';
}

add_action('save_post', 'save_methods_icon_metabox_fields');
function save_methods_icon_metabox_fields($post_id) {
    // only run this for series
    if (get_post_type($post_id) != 'methods' && get_post_type($post_id) != 'technologies' && get_post_type($post_id) != 'bioinformatics'){
        return $post_id;
    }
    // verify nonce
    if (empty($_POST['methods_icon_metabox_nonce']) || !wp_verify_nonce($_POST['methods_icon_metabox_nonce'], basename(__FILE__))){
        return $post_id;
    }
    // check autosave
    if (defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE){ return $post_id; }
    // check permissions
    if (!current_user_can('edit_post', $post_id)){ return $post_id; }

    // save
    update_post_meta($post_id, '_ngi_post_icon', $_POST['ngi_icon_image_source']);
}

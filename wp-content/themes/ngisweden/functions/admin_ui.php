<?php

// Allow styling in the gutenberg editor to match front-end
function ngisweden_guten_block_editor_assets() {
    wp_enqueue_style(
        'ngisweden-editor-style',
        get_stylesheet_directory_uri() . "/editor.css",
        array(),
        '1.0'
    );
}
add_action('enqueue_block_editor_assets', 'ngisweden_guten_block_editor_assets');


// Clean up the admin interface

// We don't have comments on this site, so remove it
function ngisweden_admin_menu_cleanup() { remove_menu_page('edit-comments.php'); }
add_action( 'admin_menu', 'ngisweden_admin_menu_cleanup' );

function my_admin_bar_render() {
    global $wp_admin_bar;
    $wp_admin_bar->remove_menu('wp-logo');
    $wp_admin_bar->remove_menu('comments');
    $wp_admin_bar->remove_node('new-media');
    $wp_admin_bar->remove_node('new-cptbc');
    $wp_admin_bar->remove_node('new-user');
    $wp_admin_bar->remove_node('new-location');
    $wp_admin_bar->remove_node('new-event-recurring');
    $wp_admin_bar->remove_node('new-cookielawinfo');
    $wp_admin_bar->remove_node('monsterinsights_frontend_button');
    // Rename posts to news
    $new_post_node = $wp_admin_bar->get_node('new-post');
    $new_post_node->title = 'News';
    // Move News to the bottom
    $wp_admin_bar->remove_node('new-post');
    $wp_admin_bar->add_node($new_post_node);
    // Move New Event to bottom
    $new_event_node = $wp_admin_bar->get_node('new-event');
    $wp_admin_bar->remove_node('new-event');
    $wp_admin_bar->add_node($new_event_node);
}
add_action( 'wp_before_admin_bar_render', 'my_admin_bar_render' );

// Don't show the annoying Updraft admin bar thing.
define('UPDRAFTPLUS_ADMINBAR_DISABLE', true);

// Customise the order of pages in the admin list (move Media down)
function ngisweden_admin_menu_media_down() {
    return array(
        'index.php',                  // Dashboard
        'edit.php',                   // Posts
        'edit.php?post_type=methods', // Methods
        'edit.php?post_type=technologies', // Methods
        'edit.php?post_type=bioinformatics', // Methods
        'edit.php?post_type=page',    // Pages
        'edit.php?post_type=cptbc',   // Carousel
        'upload.php'                  // Media
    );
}
add_filter( 'custom_menu_order', '__return_true' );
add_filter( 'menu_order', 'ngisweden_admin_menu_media_down' );

// Remove certain pages from the menu
function my_remove_menus() {
    remove_submenu_page('index.php', 'index.php' ); // Default dashboard
    remove_submenu_page('themes.php', 'themes.php' ); // Theme chooser
    remove_submenu_page('themes.php', 'theme-editor.php' ); // Theme editor
    remove_submenu_page('plugins.php', 'plugin-editor.php' ); // Plugin editor
    remove_submenu_page('options-general.php', 'options-discussion.php' ); // Discussion
}
add_action( 'admin_menu', 'my_remove_menus', 999 );

// Remove the annoying boxes from the dashboard index page
function remove_dashboard_widgets() {
    // remove_meta_box( 'dashboard_right_now', 'dashboard', 'normal' );   // At a Glance
    remove_meta_box( 'dashboard_quick_press', 'dashboard', 'side' );  // Quick Draft
    remove_meta_box( 'dashboard_activity', 'dashboard', 'normal' );   // Activity
    remove_meta_box( 'dashboard_primary', 'dashboard', 'side' );      // WordPress Events and News
    // These don't seem to exist for me? But were on the WordPress example, so figure it doesn't hurt to keep
    remove_meta_box( 'dashboard_recent_comments', 'dashboard', 'normal' ); // Recent Comments
    remove_meta_box( 'dashboard_incoming_links', 'dashboard', 'normal' );  // Incoming Links
    remove_meta_box( 'dashboard_plugins', 'dashboard', 'normal' );         // Plugins
    remove_meta_box( 'dashboard_recent_drafts', 'dashboard', 'side' );     // Recent Drafts
    remove_meta_box( 'dashboard_secondary', 'dashboard', 'side' );         // Other WordPress News
}
add_action( 'wp_dashboard_setup', 'remove_dashboard_widgets' );

// Don't let people have any choice over the admin theme colour! muwahahahaha
remove_action( 'admin_color_scheme_picker', 'admin_color_scheme_picker' );

//
// Use a totally custom dashboard page
//

// Code stolen from https://wordpress.org/plugins/sweet-custom-dashboard/
add_action('admin_menu', 'ngi_admin_register_menu');
add_action('load-index.php', 'ngi_admin_redirect_dashboard');
function ngi_admin_redirect_dashboard() {
    if( is_admin() ) {
        $screen = get_current_screen();
        if( $screen->base == 'dashboard' ) {
            wp_redirect( admin_url( 'index.php?page=ngi-dashboard' ) );
        }
    }
}
function ngi_admin_register_menu() {
    add_dashboard_page(
        'Home',
        'Home',
        'read',
        'ngi-dashboard',
        'ngi_admin_create_dashboard',
        0
    );
}
function ngi_admin_create_dashboard() {
    include_once( 'admin_dashboard.php'  );
}

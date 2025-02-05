<?php
/* NGIsweden Theme Functions */

// Enqueue Bootstrap JS and CSS files
function ngis_wp_bootstrap_scripts_styles() {
    $ngisweden_theme = wp_get_theme();
    wp_enqueue_script('popperjs', get_stylesheet_directory_uri().'/includes/js/popper.min.js', array(), '1.14.7', true );
    wp_enqueue_script('bootstrapjs', get_stylesheet_directory_uri().'/includes/js/bootstrap.min.js', array('jquery'), '4.3.1', true );
    wp_enqueue_script('ngisweden', get_stylesheet_directory_uri().'/ngisweden.js', array('jquery'), $ngisweden_theme->version, true);
    wp_enqueue_style('bootstrapcss', get_stylesheet_directory_uri().'/includes/css/bootstrap.min.css', array(),'4.3.1');
    wp_enqueue_style('fontawesomecss', get_stylesheet_directory_uri().'/includes/css/fontawesome.all.min.css', array(),'5.8.1');
    wp_enqueue_style('ngisweden', get_stylesheet_directory_uri().'/style.css', array(), $ngisweden_theme->version);
}
add_action('wp_enqueue_scripts', 'ngis_wp_bootstrap_scripts_styles');

// Register navigation menus
function register_ngisweden_nav() {
    register_nav_menu('main-nav', __( 'Main Navigation' ));
    register_nav_menu('secondary-nav', __( 'Secondary Navigation' ));
}
add_action('init', 'register_ngisweden_nav');

// Nav menu custom walker
// https://github.com/wp-bootstrap/wp-bootstrap-navwalker
function ngi_register_navwalker(){
    require_once get_template_directory().'/includes/class-wp-bootstrap-navwalker.php';
}
add_action('after_setup_theme', 'ngi_register_navwalker');

// Nav breadcrumbs
require_once('functions/bootstrap-breadcrumb.php');

// Bootstrap pagination links
require_once('functions/bootstrap-pagination.php');

// Search - highlight terms
function ngi_highlight_search_terms($text){
    if(is_search() && !is_admin()){
        $sr = get_query_var('s');
        if(trim($sr) != ''){
            $keys = explode(" ",$sr);
            $keys = array_filter($keys);
            $text = preg_replace('/('.implode('|', $keys) .')/iu', '<mark>$1</mark>', $text);
        }
    }
    return $text;
}
add_filter('the_excerpt', 'ngi_highlight_search_terms');
add_filter('the_title', 'ngi_highlight_search_terms');

// Rename "Posts" to "News"
// https://gist.github.com/gyrus/3155982
add_action( 'admin_menu', 'ngisweden_change_post_menu_label' );
add_action( 'init', 'ngisweden_change_post_object_label' );
function ngisweden_change_post_menu_label() {
    global $menu;
    global $submenu;
    $menu[5][0] = 'News';
    $submenu['edit.php'][5][0] = 'News';
    $submenu['edit.php'][10][0] = 'Add News';
    $submenu['edit.php'][16][0] = 'News Tags';
    echo '';
}
function ngisweden_change_post_object_label() {
    global $wp_post_types;
    $labels = &$wp_post_types['post']->labels;
    $labels->name = 'News';
    $labels->singular_name = 'News';
    $labels->add_new = 'Add News';
    $labels->add_new_item = 'Add News';
    $labels->edit_item = 'Edit News';
    $labels->new_item = 'News';
    $labels->view_item = 'View News';
    $labels->search_items = 'Search News';
    $labels->not_found = 'No News found';
    $labels->not_found_in_trash = 'No News found in Trash';
}

// Exclude media library attachments from search results
add_action( 'init', 'ngisweden_exclude_attachments_from_search_results' );
function ngisweden_exclude_attachments_from_search_results() {
    global $wp_post_types;
    $wp_post_types['attachment']->exclude_from_search = true;
}

// Don't paginate archives for custom post types
function ngi_cpt_archive_posts_per_page( WP_Query $wp_query ) {
    if ($wp_query->is_main_query() && !is_admin()) {
        if(is_tax('applications') || is_post_type_archive('methods') || is_post_type_archive('technologies') || is_post_type_archive('bioinformatics')){
            $wp_query->set('posts_per_page', -1);
        }
    }
}
add_action('pre_get_posts', 'ngi_cpt_archive_posts_per_page');

// Use a custom colour palette for Gutenberg colour picker
function ngi_gutenberg_color_palette() {
    add_theme_support('editor-color-palette', array(
        array( 'color' => '#006fe6', 'name'  => 'NGI Blue', 'slug' => 'ngi-blue' ),
        array( 'color' => '#e7ecf7', 'name'  => 'Light Blue', 'slug' => 'light-blue' ),
        array( 'color' => '#0056b3', 'name'  => 'Dark Blue', 'slug' => 'dark-blue' ),
        array( 'color' => '#183c55', 'name'  => 'Darker Blue', 'slug' => 'darker-blue' ),
        array( 'color' => '#495057', 'name'  => 'Dark Grey Blue', 'slug' => 'dark-grey-blue' ),
        array( 'color' => '#4C4C4C', 'name'  => 'Grey', 'slug' => 'grey' ),

        array( 'color' => '#E9F2D1', 'name'  => 'SciLifeLab Lime 25%', 'slug' => 'scilifelab-lime-25' ),
        array( 'color' => '#D3E4A3', 'name'  => 'SciLifeLab Lime 50%', 'slug' => 'scilifelab-lime-50' ),
        array( 'color' => '#BDD775', 'name'  => 'SciLifeLab Lime 75%', 'slug' => 'scilifelab-lime-75' ),
        array( 'color' => '#a7c947', 'name'  => 'SciLifeLab Lime', 'slug' => 'scilifelab-lime' ),
        array( 'color' => '#004085', 'name'  => 'Dark Bootstrap Blue', 'slug' => 'dark-bootstrap-blue' ),
        array( 'color' => '#cce5ff', 'name'  => 'Light Bootstrap Blue', 'slug' => 'light-bootstrap-blue' ),

        array( 'color' => '#C0D6D8', 'name'  => 'SciLifeLab Teal 25%', 'slug' => 'scilifelab-teal-25' ),
        array( 'color' => '#82AEB2', 'name'  => 'SciLifeLab Teal 50%', 'slug' => 'scilifelab-teal-50' ),
        array( 'color' => '#43858B', 'name'  => 'SciLifeLab Teal 75%', 'slug' => 'scilifelab-teal-75' ),
        array( 'color' => '#045C64', 'name'  => 'SciLifeLab Teal', 'slug' => 'scilifelab-teal' ),
        array( 'color' => '#155724', 'name'  => 'Dark Bootstrap Green', 'slug' => 'dark-bootstrap-green' ),
        array( 'color' => '#d4edda', 'name'  => 'Light Bootstrap Green', 'slug' => 'light-bootstrap-green' ),

        array( 'color' => '#D2E5E7', 'name'  => 'SciLifeLab Aqua 25%', 'slug' => 'scilifelab-aqua-25' ),
        array( 'color' => '#A6CBCF', 'name'  => 'SciLifeLab Aqua 50%', 'slug' => 'scilifelab-aqua-50' ),
        array( 'color' => '#79B1B7', 'name'  => 'SciLifeLab Aqua 75%', 'slug' => 'scilifelab-aqua-75' ),
        array( 'color' => '#4c979f', 'name'  => 'SciLifeLab Aqua', 'slug' => 'scilifelab-aqua' ),
        array( 'color' => '#721c24', 'name'  => 'Dark Bootstrap Red', 'slug' => 'dark-bootstrap-red' ),
        array( 'color' => '#f8d7da', 'name'  => 'Light Bootstrap Red', 'slug' => 'light-bootstrap-red' ),

        array( 'color' => '#D2C7D4', 'name'  => 'SciLifeLab Grape 25%', 'slug' => 'scilifelab-grape-25' ),
        array( 'color' => '#A48FA9', 'name'  => 'SciLifeLab Grape 50%', 'slug' => 'scilifelab-grape-50' ),
        array( 'color' => '#77577E', 'name'  => 'SciLifeLab Grape 75%', 'slug' => 'scilifelab-grape-75' ),
        array( 'color' => '#491f53', 'name'  => 'SciLifeLab Grape', 'slug' => 'scilifelab-grape' ),
        array( 'color' => '#856404', 'name'  => 'Dark Bootstrap Yellow', 'slug' => 'dark-bootstrap-yellow' ),
        array( 'color' => '#fff3cd', 'name'  => 'Light Bootstrap Yellow', 'slug' => 'light-bootstrap-yellow' ),

        array( 'color' => '#E5E5E5', 'name'  => 'SciLifeLab Light Grey', 'slug' => 'scilifelab-light-grey' ),
        array( 'color' => '#A6A6A6', 'name'  => 'SciLifeLab Medium Grey', 'slug' => 'scilifelab-medium-grey' ),
        array( 'color' => '#3F3F3F', 'name'  => 'SciLifeLab Dark Grey', 'slug' => 'scilifelab-dark-grey' ),

    ));
}
add_action( 'after_setup_theme', 'ngi_gutenberg_color_palette' );

// Enqueue a custom javascript file for extending gutenberg
function ngi_guten_enqueue() {
    wp_enqueue_script('ngi-gutenberg-extend',
        get_stylesheet_directory_uri().'/ngi-gutenberg-extend.js',
        array( 'wp-blocks' )
    );
}
add_action('enqueue_block_editor_assets', 'ngi_guten_enqueue');

// Code to clean up and improve the WordPress admin interface
require_once('functions/admin_ui.php');

// Initialising widget areas, creating new widgets
require_once('functions/widgets.php');

// Theme shortcodes
require_once('functions/shortcodes/ngisweden_tabs.php');
require_once('functions/shortcodes/ngisweden_search.php');
require_once('functions/shortcodes/homepage_applications.php');
require_once('functions/shortcodes/ngisweden_publications.php');
require_once('functions/shortcodes/github_badge.php');
require_once('functions/shortcodes/mailchimp_subscribe.php');
require_once('functions/shortcodes/ngisweden_site_map.php');
require_once('functions/shortcodes/show_file.php');
require_once('functions/shortcodes/ngisweden_events_list.php');
require_once('functions/shortcodes/deployed_tools_versions.php');

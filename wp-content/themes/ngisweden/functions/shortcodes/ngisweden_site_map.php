<?php

// NGI Site Map Shortcode
// Shows a complete list of all website pages
function ngisweden_site_map_shortcode($atts_raw){
    $html = '
    <h3 id="toc" class="ngisweden_sitemap_posttype_header">Table of Contents</h3>
    <ul class="sitemap-ul">
        <li><a href="#pages">Pages</a></li>
        <li><a href="#applications">Applications</a></li>
        <li><a href="#technologies">Technologies</a></li>
    </ul>
    ';

    // Pages
    $html .= '<h3 id="pages" class="ngisweden_sitemap_posttype_header">Pages</h3>';
    // Exclude some specific pages
    $exclude_ids = array();
    foreach(['applications', 'bioinformatics', 'news'] as $e_slug){
        $e_page = get_page_by_path($e_slug);
        if($e_page){
            $exclude_ids[] = $e_page->ID;
        }
    }
    // Get list of page links
    $html .= '<ul class="sitemap-ul">';
    $html .= wp_list_pages(array(
        'post_type' => 'page',
        'sort_column' => 'menu_order',
        'title_li' => null,
        'exclude_tree' => $exclude_ids,
        'echo' => false
    ));
    $html .= '</ul>';

    // Applications
    $html .= '<h3 id="applications" class="ngisweden_sitemap_posttype_header">Applications</h3>';
    // Get all methods / bioinformatics posts
    $term_singular_names = array();
    $method_bioinfo_posts = get_posts(array(
        'post_type' => array('methods', 'bioinformatics'),
        'sort_column' => 'menu_order',
        'posts_per_page' => -1
    ));
    foreach($method_bioinfo_posts as $cpt_post){
        if(!array_key_exists($cpt_post->post_type, $term_singular_names)){
            $cpt_obj = get_post_type_object($cpt_post->post_type);
            $term_singular_names[$cpt_post->post_type] = $cpt_obj->labels->singular_name;
        }
        $cpt_post->post_type_singular_name = $term_singular_names[$cpt_post->post_type];
    }
    // Get applications and build output
    $applications_all = get_terms('applications', array('hide_empty' => false));
    $applications = array();
    sort_terms_hierarchically($applications_all, $applications);
    $html .= '<ul class="sitemap-ul">';
    $html .= print_applications($applications, $method_bioinfo_posts);
    $html .= '</ul>';

    // Technologies
    $html .= '<h3 id="technologies" class="ngisweden_sitemap_posttype_header">Technologies</h3>';
    $html .= '<ul class="sitemap-ul">';
    $html .= wp_list_pages(array(
        'post_type' => 'technologies',
        'sort_column' => 'menu_order',
        'title_li' => null,
        'exclude' => implode(',', $exclude_ids),
        'echo' => false
    ));
    $html .= '</ul>';

    return $html;
}
add_shortcode('ngisweden_site_map', 'ngisweden_site_map_shortcode');

// https://wordpress.stackexchange.com/a/99516/8616
function sort_terms_hierarchically(Array &$cats, Array &$into, $parentId = 0){
    foreach ($cats as $i => $cat) {
        if ($cat->parent == $parentId) {
            $into[$cat->term_id] = $cat;
            unset($cats[$i]);
        }
    }
    foreach ($into as $topCat) {
        $topCat->children = array();
        sort_terms_hierarchically($cats, $topCat->children, $topCat->term_id);
    }
}

function print_applications($applications, $method_bioinfo_posts){
    $html = '';
    foreach($applications as $term_id => $a){
        $html .= '<li><a href="'.get_term_link($a).'">'.$a->name.'</a>';
        $sub_li = '';
        if(count($a->children)){
            $sub_li .= print_applications($a->children, $method_bioinfo_posts);
        }
        foreach($method_bioinfo_posts as $cpt_post){
            if (has_term($a->term_id, 'applications', $cpt_post->ID)) {
                $sub_li .= '<li>';
                $sub_li .= '<a href="'.get_permalink($cpt_post->ID).'">'.$cpt_post->post_title.'</a>';
                $sub_li .= ' <span class="small text-muted">('.$cpt_post->post_type_singular_name.')</span>';
                $sub_li .= '</li>';
            }
        }
        if($sub_li){
            $html .= '<ul class="sitemap-ul">'.$sub_li.'</ul>';
        }
        $html .= '</li>';
    }
    return $html;
}

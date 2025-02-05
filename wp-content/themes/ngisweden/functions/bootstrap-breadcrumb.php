<?php

// Originally from https://github.com/ajulien-fr/bootstrap_breadcrumb

/**
 * Retrieve category parents.
 *
 * @param int $id Category ID.
 * @param array $visited Optional. Already linked to categories to prevent duplicates.
 * @return string|WP_Error A list of category parents on success, WP_Error on failure.
 */
function custom_get_tax_parents( $id, $visited = array(), $tax_type = 'category' ) {
  $chain = '';
  $parent = get_term( $id, $tax_type );

  if ( is_wp_error( $parent ) )
    return $parent;

  $name = $parent->name;

  if ( $parent->parent && ( $parent->parent != $parent->term_id ) && !in_array( $parent->parent, $visited ) ) {
    $visited[] = $parent->parent;
    $chain .= custom_get_tax_parents( $parent->parent, $visited, $tax_type );
  }

  $chain .= '<li class="breadcrumb-item"><a href="' . esc_url( get_category_link( $parent->term_id ) ) . '">' . $name. '</a>' . '</li>';

  return $chain;
}


function bootstrap_breadcrumb() {
  global $post;

  $html = '<div class="ngisweden-header-breadcrumbs"><div class="container"><ol class="breadcrumb">';

  if ( is_home() ) {
    $html .= '<li class="breadcrumb-item active bc-home">Home</li>';

    // Main News & Events page - get by looking for page slug 'applications'
    $root_page = get_page_by_path( 'news' );
    if($root_page){
      $html .= '<li class="breadcrumb-item bc-news"><a href="' . esc_url( get_permalink( $root_page ) ) . '">' . get_the_title( $root_page ) . '</a></li>';
    }
  }

  else {
    $html .= '<li class="breadcrumb-item"><a href="'.esc_url(home_url('/')).'">Home</a></li>';

    if ( is_attachment() ) {
      $parent = get_post($post->post_parent);
      $categories = get_the_category($parent->ID);

      if ( $categories[0] ) {
        $html .= custom_get_tax_parents($categories[0], array(), 'category');
      }

      $html .= '<li class="breadcrumb-item"><a href="' . esc_url( get_permalink( $parent ) ) . '">' . $parent->post_title . '</a></li>';
      $html .= '<li class="breadcrumb-item active bc-attachment">' . get_the_title() . '</li>';
    }

    elseif ( is_category() ) {
      $category = get_category( get_query_var( 'cat' ) );

      // Main News & Events page - get by looking for page slug 'applications'
      $root_page = get_page_by_path( 'news' );
      if($root_page){
        $html .= '<li class="breadcrumb-item"><a href="' . esc_url( get_permalink( $root_page ) ) . '">' . get_the_title( $root_page ) . '</a></li>';
      }

      if ( $category->parent != 0 ) {
        $html .= custom_get_tax_parents( $category->parent, array(), 'category' );
      }

      $html .= '<li class="breadcrumb-item active bc-cat">' . single_cat_title( '', false ) . '</li>';
    }

    elseif ( is_tax() ) {
      $term = get_queried_object();
      $taxonomy = get_taxonomy($term->taxonomy);
      $page_title = $taxonomy->label.': '.$term->name;

      // Main Applications page - get by looking for page with same slug as the tax slug
      $tax_page = get_page_by_path( $taxonomy->name );
      if($tax_page){
        $html .= '<li class="breadcrumb-item"><a href="' . esc_url( get_permalink( $tax_page ) ) . '">' . get_the_title( $tax_page ) . '</a></li>';
      }

      // Get upstream tax
      if ( $term->parent != 0 ) {
        $html .= @custom_get_tax_parents( $term->parent, array(), $taxonomy->slug );
      }

      $html .= '<li class="breadcrumb-item active bc-tax">' . single_cat_title( '', false ) . '</li>';
    }

    elseif ( is_page() && !is_front_page() ) {
      $parent_id = $post->post_parent;
      $parent_pages = array();

      while ( $parent_id ) {
        $page = get_page($parent_id);
        $parent_pages[] = $page;
        $parent_id = $page->post_parent;
      }

      $parent_pages = array_reverse( $parent_pages );

      if ( !empty( $parent_pages ) ) {
        foreach ( $parent_pages as $parent ) {
          $html .= '<li class="breadcrumb-item"><a href="' . esc_url( get_permalink( $parent->ID ) ) . '">' . get_the_title( $parent->ID ) . '</a></li>';
        }
      }

      $html .= '<li class="breadcrumb-item active bc-page">' . get_the_title() . '</li>';
    }

    elseif ( is_singular( 'post' ) ) {
      $categories = get_the_category();

      // Main News & Events page - get by looking for page slug 'applications'
      $root_page = get_page_by_path( 'news' );
      if($root_page){
        $html .= '<li class="breadcrumb-item"><a href="' . esc_url( get_permalink( $root_page ) ) . '">' . get_the_title( $root_page ) . '</a></li>';
      }

      if ( $categories[0] ) {
        $html .= custom_get_tax_parents($categories[0], array(), 'category' );
      }

      $html .= '<li class="breadcrumb-item active bc-post">' . get_the_title() . '</li>';
    }

    elseif ( is_singular( 'methods' ) || is_singular( 'technologies' ) || is_singular( 'bioinformatics' ) ) {
      $categories = get_the_terms(null, 'applications');

      // Main Applications page - get by looking for page slug 'applications'
      $page_slug = false;
      if(is_singular( 'methods' )){ $page_slug = 'applications'; }
      if(is_singular( 'technologies' )){ $page_slug = 'technologies'; }
      if(is_singular( 'bioinformatics' )){ $page_slug = 'bioinformatics'; }
      if($page_slug){
        $slug_page = get_page_by_path( $page_slug );
        if($slug_page){
          $html .= '<li class="breadcrumb-item"><a href="' . esc_url( get_permalink( $slug_page ) ) . '">' . get_the_title( $slug_page ) . '</a></li>';
        } else {
          // Just fake it - TODO: Must be a better way
          $html .= '<li class="breadcrumb-item"><a href="'.esc_url( get_bloginfo('url').'/'.$page_slug ).'">' . ucfirst($page_slug) . '</a></li>';
        }
      }

      // Parents for hierarchical post types (Technologies)
      $parent_id = $post->post_parent;
      $parent_pages = array();

      while ( $parent_id ) {
        $page = get_page($parent_id);
        $parent_pages[] = $page;
        $parent_id = $page->post_parent;
      }

      $parent_pages = array_reverse( $parent_pages );

      if ( !empty( $parent_pages ) ) {
        foreach ( $parent_pages as $parent ) {
          $html .= '<li class="breadcrumb-item"><a href="' . esc_url( get_permalink( $parent->ID ) ) . '">' . get_the_title( $parent->ID ) . '</a></li>';
        }
      }

      if ( $categories && $categories[0] ) {
        $html .= custom_get_tax_parents($categories[0], array(), 'applications');
      }

      $html .= '<li class="breadcrumb-item active bc-method-technology-bioinfo">' . get_the_title() . '</li>';
    }

    elseif ( is_singular( 'event' )) {
      // News parent
      $news_page = get_page_by_path( 'news' );
      if($news_page){
        $html .= '<li class="breadcrumb-item"><a href="' . esc_url( get_permalink( $news_page ) ) . '">' . get_the_title( $news_page ) . '</a></li>';
      }

      // Events parent
      $events_page = get_page_by_path( 'news/events' );
      if($events_page){
        $html .= '<li class="breadcrumb-item"><a href="' . esc_url( get_permalink( $events_page ) ) . '">' . get_the_title( $events_page ) . '</a></li>';
      }

      // Page
      $html .= '<li class="breadcrumb-item active bc-event">' . get_the_title() . '</li>';
    }

    elseif ( is_tag() ) {
      $html .= '<li class="breadcrumb-item active bc-tag">' . single_tag_title( '', false ) . '</li>';
    }

    elseif ( is_day() ) {
      // Main News & Events page - get by looking for page slug 'applications'
      $root_page = get_page_by_path( 'news' );
      if($root_page){
        $html .= '<li class="breadcrumb-item"><a href="' . esc_url( get_permalink( $root_page ) ) . '">' . get_the_title( $root_page ) . '</a></li>';
      }

      $html .= '<li class="breadcrumb-item"><a href="' . esc_url( get_year_link( get_the_time( 'Y' ) ) ) . '">' . get_the_time( 'Y' ) . '</a></li>';
      $html .= '<li class="breadcrumb-item"><a href="' . esc_url( get_month_link( get_the_time( 'Y' ), get_the_time( 'm' ) ) ) . '">' . get_the_time( 'm' ) . '</a></li>';
      $html .= '<li class="breadcrumb-item active bc-day">' . get_the_time('d') . '</li>';
    }

    elseif ( is_month() ) {
      // Main News & Events page - get by looking for page slug 'applications'
      $root_page = get_page_by_path( 'news' );
      if($root_page){
        $html .= '<li class="breadcrumb-item"><a href="' . esc_url( get_permalink( $root_page ) ) . '">' . get_the_title( $root_page ) . '</a></li>';
      }

      $html .= '<li class="breadcrumb-item"><a href="' . esc_url( get_year_link( get_the_time( 'Y' ) ) ) . '">' . get_the_time( 'Y' ) . '</a></li>';
      $html .= '<li class="breadcrumb-item active bc-month">' . get_the_time( 'F' ) . '</li>';
    }

    elseif ( is_year() ) {

      // Main News & Events page - get by looking for page slug 'applications'
      $root_page = get_page_by_path( 'news' );
      if($root_page){
        $html .= '<li class="breadcrumb-item"><a href="' . esc_url( get_permalink( $root_page ) ) . '">' . get_the_title( $root_page ) . '</a></li>';
      }

      // Append current year
      $html .= '<li class="breadcrumb-item active bc-year">' . get_the_time( 'Y' ) . '</li>';

    }

    elseif ( is_author() ) {
      $html .= '<li class="breadcrumb-item active bc-author">' . get_the_author() . '</li>';
    }

    elseif ( is_search() ) {
      $html .= '<li class="breadcrumb-item active bc-search">Search</li>';
    }

    elseif ( is_post_type_archive() ) {
      $html .= '<li class="breadcrumb-item active bc-archive">' . post_type_archive_title('', false) . '</li>';
    }

    elseif ( is_404() ) {
      $html .= '<li class="breadcrumb-item active bc-404">404</li>';
    }

  }

  $html .= '</ol></div></div>';

  echo $html;
}

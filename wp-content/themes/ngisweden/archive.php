<?php

// For regular news posts, use home.php instead
if(get_post_type() == 'post'){
  include('home.php');
  exit;
}

// Events post type, redirect to the Events page
if(get_post_type() == 'event'){
  $slug_page = get_page_by_path('news/events');
  wp_redirect( get_permalink( $slug_page ) );
  exit;
}

// If we have a page that exactly matches the URL, display that instead
$request_uri = trim($_SERVER['REQUEST_URI'], '/');
// Only top-level URLs (categories can associate their own page)
if(substr_count($request_uri, '/') == 0){
  $page = get_page_by_path($request_uri);
  if($page){
    get_header();
    echo '<div class="container main-page">';
    echo '<h1>'.$page->post_title.'</h1>';
    echo apply_filters('the_content', $page->post_content);
    echo '</div>';
    get_footer();
    exit;
  }
}


get_header(); ?>

<div class="container main-page">

  <?php

  //
  // PAGE CONTENTS
  // Get the WordPress page that is linked to this taxonomy
  //

  // Start by setting defaults using the values from the s type
  $term = get_queried_object();
  if(isset($term->term_id)){
    $term_meta = get_option( "application_page_".$term->term_id );
  }
  if($term->label){
    $page_title = $term->label;
  } else {
    $page_title = $term->name;
  }
  // If we're not looking at applications, prepend the taxonomy type
  if(isset($term->taxonomy) && $term->taxonomy != 'applications'){
    $taxonomy = get_taxonomy($term->taxonomy);
    if($taxonomy) {
      $page_title = $taxonomy->label.': '.$page_title;
    }
  }
  $page_intro = '';
  if(isset($term->term_id)){
    $app_description = trim(strip_tags(term_description($term->term_id, 'applications')));
  }
  if(isset($app_description) && strlen($app_description)){
    $page_intro = '<p class="methods-lead">'.$app_description.'</p>';
  }
  $page_contents = '';

  // Overwrite with the title and contents from the linked WP Page, if we have one
  if(isset($term_meta['application_page']) && $term_meta['application_page']){
    $app_page = get_post($term_meta['application_page']);
    $page_contents = $app_page->post_content;
  }

  // Start the structure to collect the cards
  $card_decks = array(
    'applications' => array(
      'title' => 'Applications',
      'cards' => array()
    ),
    'methods' => array(
      'title' => 'Methods',
      'cards' => array()
    ),
    'technologies' => array(
      'title' => 'Technologies',
      'cards' => array()
    ),
    'bioinformatics' => array(
      'title' => 'Bioinformatics',
      'cards' => array()
    )
  );

  //
  // CHILD APPLICATIONS
  // Applications are hierarchical. If this is a parent, get the children
  //

  $term_children_ids = @get_term_children($term->term_id, 'applications');
  $term_children = [];
  foreach ($term_children_ids as $child_id) {
    // Get the sub-term details
    $subterm = get_term_by('id', $child_id, 'applications' );
    // Ignore sub-children, only get direct children
    if($subterm->parent == $term->term_id){
      $term_children[$subterm->term_order] = $subterm;
    }
  }
  ksort($term_children);
  foreach($term_children as $subterm){
    // Get the description
    $subterm_app_description = trim(strip_tags(term_description($subterm->term_id, 'applications')));
    // Get the icon
    $term_meta = get_option('application_page_'.$subterm->term_id);
    $subterm_app_icon = '';
    if(isset($term_meta['application_icon'])){
      $a_icon_path = get_stylesheet_directory().'/'.$term_meta['application_icon'];
      if(file_exists($a_icon_path) && is_file($a_icon_path)){
        $subterm_app_icon = '<span class="application-icon">'.file_get_contents($a_icon_path).'</span>';
      }
    }


    // Build the card itself
    $card_output = '
    <div class="card">
      <div class="card-body">
        <h5 class="card-title">
          <a href="'.get_term_link($subterm->term_id, 'applications').'">'.$subterm->name.'</a>
          '.$subterm_app_icon.'
        </h5>
        '.$subterm_app_description.'
      </div>
    </div>';
    // Add to the array of card outputs
    array_push($card_decks['applications']['cards'], $card_output);
  }



  //
  // LAB METHODS, BIOINFORMATICS METHODS
  // Get the methods directly associated with this application
  //

  // Loop through the methods in this application and show snippets
  $methods_cards = array();
  $bioinformatics_cards = array();
  if (have_posts()) {
    while (have_posts()) {
      the_post();

      // Skip technologies with no children
      if(get_post_type() == 'technologies' && empty(get_children(get_the_ID()))){
        continue;
      }

      // Start building the method card
      $card_output = '
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">
            <a href="'.get_the_permalink().'">'.get_the_title().'</a>
          </h5>';
      // Excerpt intro text
      if(has_excerpt()) {
        $card_output .= '<p class="card-text">'.strip_tags(get_the_excerpt()).'</p>';
      }
      // General keywords
      $method_keywords = get_the_terms(null, 'method_keywords');
      if ($method_keywords && !is_wp_error($method_keywords)){
        foreach($method_keywords as $kw){
          $card_output .= '<a href="'.get_term_link($kw->slug, 'method_keywords').'" rel="tag" class="badge badge-secondary method-keyword '.$kw->slug.'">'.$kw->name.'</a> ';
        }
      }
      $card_output .= '</div></div>';

      // Add to the relevant array of cards
      array_push( $card_decks[ get_post_type() ]['cards'], $card_output );
    }
  }





  //
  // PRINT OUTPUT
  // Build the page contents now that we have everything ready
  //

  // Print the title and introduction
  echo '<h1>'.$page_title.'</h1>';

  // Blue box with one-line introduction
  echo $page_intro;

  // Echo the rest of the page contents
  echo $page_contents;

  // Print the tab headers
  echo '<div class="row mt-5 mb-3"><div class="col-sm-2 mb-3"><div class="nav flex-column nav-pills" role="tablist" aria-orientation="vertical">';
  $first = true;
  foreach($card_decks as $id => $deck){
    if(count($deck['cards']) > 0){
      // Card deck header
      echo '<a class="nav-link '.($first ? 'active' : '').'" id="'.$id.'-tab" data-toggle="pill" href="#'.$id.'" role="tab" aria-controls="'.$id.'" '.($first ? 'aria-selected="true"' : '').'>
        '.$deck['title'].' <span class="badge badge-light">'.count($deck['cards']).'
      </a>';
      $first = false;
    }
  }
  echo '</div></div>';

  // Print each set of card decks
  echo '<div class="col-sm-10"><div class="tab-content">';
  $cards_per_row = 2;
  $first = true;
  foreach($card_decks as $id => $deck){
    if(count($deck['cards']) > 0){
      // Start of tab content area
      echo '<div class="tab-pane fade '.($first ? 'show active' : '').'" id="'.$id.'" role="tabpanel" aria-labelledby="'.$id.'-tab">';
      $postcounter = -1;
      foreach($deck['cards'] as $card){
        $postcounter++;
        // Start a row of cards
        if($postcounter % $cards_per_row == 0) echo '<div class="ngisweden-application-methods card-deck">';
        // Print the card
        echo $card;
        // Finish a row of 3 cards
        if($postcounter % $cards_per_row == $cards_per_row-1) echo '</div>';
      }
      // Loop did not finish a row of 3 cards
      if($postcounter % $cards_per_row != $cards_per_row-1) echo '</div>';
      // End of tab content area
      echo '</div>';
      $first = false;
    }
  }
  echo '</div></div></div>';

  ?>

</div>

<?php get_footer();

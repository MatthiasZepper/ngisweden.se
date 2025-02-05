<?php // Custom Dashboard page

$tab = isset($_GET['tab']) ? $_GET['tab'] : null;

//
// DASHBOARD CHECKS
//
if($tab == 'warnings'):
$max_except_length = 300;

// METHODS WITH NO APPLICATION VS WITH
// NO KEYWORDS VS WITH
// Every method should belong to at least one application
// Check that we have some keywords
$all_method_posts = get_posts(array(
  'post_type' => array('methods', 'bioinformatics'),
  'posts_per_page' => -1
));
$methods_missing_application = [];
$methods_with_application = [];
$methods_missing_keywords = [];
$methods_with_keywords = [];
foreach($all_method_posts as $method_post){
  if(!get_the_terms($method_post->ID, 'applications')){
    $methods_missing_application[] = $method_post;
  }
  else{
    $methods_with_application[] = $method_post;
  }
  if(!get_the_terms($method_post->ID, 'method_keywords')){
    $methods_missing_keywords[] = $method_post;
  }
  else{
    $methods_with_keywords[] = $method_post;
  }
}

// MISSING FROM NAV OR NOT
// Check if we have any pages that should be in the main menu and are not vs that are in the menu
// Get menu page IDs
$menu_locations = get_nav_menu_locations();
$menu = wp_get_nav_menu_object($menu_locations['main-nav']);
$menu_items = wp_get_nav_menu_items($menu->term_id);
$menu_page_ids = [];
foreach($menu_items as $menu_item){
  if($menu_item->object == 'page'){
    $menu_page_ids[] = $menu_item->object_id;
  }
}
// Get all page IDs
$ignore_pages = [get_option('page_on_front')]; // Static homepage page ID
foreach(['applications', 'methods', 'bioinformatics', 'technologies'] as $slug){
  $ignore_page = get_page_by_path($slug);
  if($ignore_page){
    $ignore_pages[] = $ignore_page->ID;
  }
}
$ngi_pages = get_pages(array(
  'hierarchical'=> false,
  'exclude_tree' => [
    // Exclude pages that are children of 'Applications'
    get_page_by_path('applications')->ID,
    // Exclude children of 'News' (the Events pages)
    get_page_by_path('news')->ID,
  ],
  // Ignore pages with same basenames as CPTs
  'exclude' => $ignore_pages
));
$missing_pages = [];
$found_pages = [];
foreach($ngi_pages as $ngi_page){
  if(!in_array($ngi_page->ID, $menu_page_ids)){
    $missing_pages[] = $ngi_page;
  }
  else{
    $found_pages[] = $ngi_page;
  }
}

// BAD EXCERPTS VS GOOD
// Nearly everything should have an excerpt!
// Check that those set are not too long.

$all_cpt_posts = get_posts(array(
  'post_type' => array('methods', 'bioinformatics', 'technologies'),
  'posts_per_page' => -1
));
$cpts_missing_excerpt = [];
$cpts_with_excerpt = [];
$cpts_excerpt_too_long = [];
$cpts_excerpt_just_right = [];
foreach($all_cpt_posts as $cpt_post){
  if(!has_excerpt($cpt_post)){
    $cpts_missing_excerpt[] = $cpt_post;
  }
  else{
    $cpts_with_excerpt[] = $cpt_post;
    $excerpt_length = strlen(strip_tags(get_the_excerpt($cpt_post)));
    if($excerpt_length > $max_except_length){
      $cpts_excerpt_too_long[] = [$cpt_post, $excerpt_length];
    }
    else{
      $cpts_excerpt_just_right[] = [$cpt_post, $excerpt_length];
    }
  }
}
// APPLICATIONS WITH NO METHODS VS WITH
// MISSING APPLICATION DESCRIPTIONS VS NOT
$applications_missing_descriptions = [];
$applications_with_descriptions = [];
$applications_description_too_long = [];
$applications_description_just_right = [];
$applications_no_posts = [];
$applications_with_posts = [];
$applications = get_terms([
    'taxonomy' => 'applications',
    'hide_empty' => false,
]);
foreach($applications as $application){
  if($application->count == 0){
    $applications_no_posts[] = $application;
  }
  else{
    $applications_with_posts[] = $application;
  }
  if(trim($application->description) == ''){
    $applications_missing_descriptions[] = $application;
  } else {
    $applications_with_descriptions[] = $application;
    $description_length = strlen(trim($application->description));
    if($description_length > $max_except_length) {
      $applications_description_too_long[] = [$application, $description_length];
    }
    else{
      $applications_description_just_right[] = [$application, $description_length];
    }
  }
}

endif; // if($tab == 'warnings'):

?>

<div class="wrap about-wrap full-width-layout">

  <h1>Welcome to the <?php bloginfo('name'); ?> administration pages</h1>
  <div class="about-text">
    You can manage all of the content on the <?php bloginfo('name'); ?> website from these pages.
  </div>

  <nav class="nav-tab-wrapper">
    <a href="<?php echo admin_url('index.php?page=ngi-dashboard'); ?>" class="nav-tab <?php if($tab === null){ echo 'nav-tab-active'; } ?>">Your Content</a>
    <a href="<?php echo admin_url('index.php?page=ngi-dashboard'); ?>&tab=user_guide" class="nav-tab <?php if($tab == 'user_guide'){ echo 'nav-tab-active'; } ?>">NGI Sweden Website User Guide</a>
    <a href="<?php echo admin_url('index.php?page=ngi-dashboard'); ?>&tab=warnings" class="nav-tab <?php if($tab == 'warnings'){ echo 'nav-tab-active'; } ?>">Website Warnings</a>
  </nav>

  <?php if($tab === null): ?>
  <h3>Your Content</h3>
  <p class="description">We assign each web page to a person by setting them as an author.
  Follow these links to see only the content where you are set as author.</p>
  <?php
    echo '<ul style="list-style-type: inherit; margin-left: 2rem;">';
    $pcounts = array(
      'methods' => 0,
      'technologies' => 0,
      'bioinformatics' => 0,
      'page' => 0,
      'post' => 0,
    );
    $posts = get_posts(array(
      'author' => get_current_user_id(),
      'post_type' => array_keys($pcounts),
      'posts_per_page' => -1
    ));
    foreach($posts as $post) {
      $pcounts[$post->post_type] += 1;
    }
    foreach($pcounts as $pt => $pcount){
      echo '<li><a href="/wp-admin/edit.php?post_type='.$pt.'&author='.get_current_user_id().'">'.ucfirst($pt).' ('.$pcount.')</a></li>';
    }
    echo '</ul>';
  ?>
<?php endif;  // <?php if($tab === null):

if($tab == 'user_guide'):
?>

<h3>
  <a class="button button-primary" style="float:right; margin-bottom: 10px;" href="https://docs.google.com/document/d/1wXarUg1JlxSmDLZwmhZs5ChV1Kq28pV-dBniaASlPWA/edit?usp=sharing" target="_blank">Open docs in new tab</a>
  Website walkthrough for editors
</h3>
<div style="box-shadow: 0px 0px 6px 0px #0000002e;">
  <iframe style="width:100%; height: 600px;" src="https://docs.google.com/document/d/e/2PACX-1vTty9lSxiX0O9dgNO8v6jEftdxilRH-oVGuTpxLqCR5Ta1IULbIgcDKOvoWa-Hft4RADVxaSJhZYrFa/pub"></iframe>
</div>

<?php
endif; // if($tab == 'user_guide'):

if($tab == 'warnings'):
?>

  <?php if(count($methods_missing_application) > 0): ?>
    <h3 style="background-color: #f0dddd">Methods missing an application</h3>
    <p class="description">
      Every method must be assigned to at least one Application category, so that it can be discovered through the website.
    </p>
    <ul style="list-style-type: inherit; margin-left: 2rem;">
    <?php
    foreach($methods_missing_application as $method){
      echo '<li><a href="'.$method->guid.'">'.$method->post_title.'</a> (<a href="'.get_edit_post_link($method).'">edit</a>)</li>';
    }
    ?>
    </ul>
  <?php endif; ?>
  
  <?php if(count($methods_missing_application) == 0): ?>
    <h3 style="background-color: #dce6d3">No methods are missing applications</h3>
  <?php endif; ?>
    
  <?php if(count($methods_with_application) > 0): ?>
    <details>
    <summary>Method pages with applications (click arrow):</summary>
    <ul style="list-style-type: inherit; margin-left: 2rem;">
    <?php
    foreach($methods_with_application as $method){
      echo '<li><a href="'.$method->guid.'">'.$method->post_title.'</a> (<a href="'.get_edit_post_link($method).'">edit</a>)</li>';
    }
    ?>
    </ul>
    </details>
  <?php endif; ?>

  <?php if(count($applications_no_posts) > 0): ?>
    <h3 style="background-color: #f0dddd">Applications with no content</h3>
    <p class="description">
      Every application should have at least one method or bioinformatics page associated with it.
    </p>
    <ul style="list-style-type: inherit; margin-left: 2rem;">
    <?php
    foreach($applications_no_posts as $application){
      echo '<li><a href="'.get_term_link($application).'">'.$application->name.'</a> (<a href="'.get_edit_term_link($application).'">edit</a>)</li>';
    }
    ?>
    </ul>
  <?php endif; ?>
  
  <?php if(count($applications_no_posts) == 0): ?>
    <h3 style="background-color: #dce6d3">All applications have content</h3>
  <?php endif; ?>
  
  <?php if(count($applications_with_posts) > 0): ?>
    <details>
    <summary>Method pages with posts (click arrow):</summary>
    <ul style="list-style-type: inherit; margin-left: 2rem;">
    <?php
    foreach($applications_with_posts as $application){
      echo '<li><a href="'.get_term_link($application).'">'.$application->name.'</a> (<a href="'.get_edit_term_link($application).'">edit</a>)</li>';
    }
    ?>
    </ul>
    </details>
  <?php endif; ?>

  <?php if(count($missing_pages) > 0): ?>
    <h3 style="background-color: #f0dddd">Pages not in the main menu</h3>
    <p class="description">
      Website pages aren't automatically added to the main navigation, they have to be placed in the menu editor.
      The list below shows pages that are not in the top navigation.
      This excludes methods, applications, technologies, bioinformatics and news/events.
      <a href="<?php echo admin_url('nav-menus.php'); ?>">Click here to edit the main navigation</a>.
    </p>
    <ul style="list-style-type: inherit; margin-left: 2rem;">
    <?php
    foreach($missing_pages as $missing_page){
      echo '<li><a href="'.$missing_page->guid.'">'.$missing_page->post_title.'</a></li>';
    }
    ?>
    </ul>
  <?php endif; ?>
  
  <?php if(count($missing_pages) == 0): ?>
    <h3 style="background-color: #dce6d3">All pages are in the main menu</h3>
  <?php endif; ?>
  
  <?php if(count($found_pages) > 0): ?>
    <details>
    <summary>Pages that are properly included in the main menu (click arrow):</summary>
    <ul style="list-style-type: inherit; margin-left: 2rem;">
    <?php
    foreach($found_pages as $found_page){
      echo '<li><a href="'.$found_page->guid.'">'.$found_page->post_title.'</a></li>';
    }
    ?>
    </ul>
    </details>
  <?php endif; ?>

  <?php if(count($cpts_missing_excerpt) > 0): ?>
    <h3 style="background-color: #f0dddd">Content missing an excerpt</h3>
    <p class="description">
      Methods, technologies and bioinformatics posts should all have a <em>custom excerpt</em>.
      This is displayed on the cards on the listing pages and in a blue highlight at the top of the page.
      See the walkthrough below to find out how to write excerpts.
    </p>
    <ul style="list-style-type: inherit; margin-left: 2rem;">
    <?php
    foreach($cpts_missing_excerpt as $post){
      echo '<li><a href="'.$post->guid.'">'.$post->post_title.'</a> (<a href="'.get_edit_post_link($post).'">edit</a>)</li>';
    }
    ?>
    </ul>
  <?php endif; ?>
  
  <?php if(count($cpts_missing_excerpt) == 0): ?>
    <h3 style="background-color: #dce6d3">No content is missing an excerpt</h3>
  <?php endif; ?>
  
  <?php if(count($cpts_with_excerpt) > 0): ?>
    <details>
    <summary>cpts with excerpts (click arrow):</summary>
    <ul style="list-style-type: inherit; margin-left: 2rem;">
    <?php
    foreach($cpts_with_excerpt as $post){
      echo '<li><a href="'.$post->guid.'">'.$post->post_title.'</a> (<a href="'.get_edit_post_link($post).'">edit</a>)</li>';
    }
    ?>
    </ul>
    </details>
  <?php endif; ?>

  <?php if(count($cpts_excerpt_too_long) > 0): ?>
    <h3 style="background-color: #f0dddd">Excerpts too long</h3>
    <p class="description">
      Methods, technologies and bioinformatics posts should all have a short <em>custom excerpt</em>.
      For consistency with other pages, the excerpt should be kept short (less than <?php echo $max_except_length; ?> characters).
    </p>
    <ul style="list-style-type: inherit; margin-left: 2rem;">
    <?php
    foreach($cpts_excerpt_too_long as $except_too_long){
      $post = $except_too_long[0];
      $excerpt_length = $except_too_long[1];
      echo '<li><a href="'.$post->guid.'">'.$post->post_title.'</a> - '.$excerpt_length.' characters (<a href="'.get_edit_post_link($post).'">edit</a>)</li>';
    }
    ?>
    </ul>
  <?php endif; ?>
  
  <?php if(count($cpts_excerpt_too_long) == 0): ?>
    <h3 style="background-color: #dce6d3">No excerpts are too long</h3>
  <?php endif; ?>
  
  <?php if(count($cpts_excerpt_just_right) > 0): ?>
    <details>
    <summary>cpts with excerpts of proper length (click arrow):</summary>
    <ul style="list-style-type: inherit; margin-left: 2rem;">
    <?php
    foreach($cpts_excerpt_just_right as $except_just_right){
      $post = $except_just_right[0];
      $excerpt_length = $except_just_right[1];
      echo '<li><a href="'.$post->guid.'">'.$post->post_title.'</a> - '.$excerpt_length.' characters (<a href="'.get_edit_post_link($post).'">edit</a>)</li>';
    }
    ?>
    </ul>
    </details>
  <?php endif; ?>

  <?php if(count($applications_missing_descriptions) > 0): ?>
    <h3 style="background-color: #f0dddd">Applications missing a description</h3>
    <p class="description">
      Application categories should all have a <em>description</em>.
      This is displayed on the cards on the listing pages and in a blue highlight at the top of the page.
      See the walkthrough below to find out how to write application descriptions.
    </p>
    <ul style="list-style-type: inherit; margin-left: 2rem;">
    <?php
    foreach($applications_missing_descriptions as $application){
      echo '<li><a href="'.get_term_link($application).'">'.$application->name.'</a> (<a href="'.get_edit_term_link($application).'">edit</a>)</li>';
    }
    ?>
    </ul>
  <?php endif; ?>
  
  <?php if(count($applications_missing_descriptions) == 0): ?>
    <h3 style="background-color: #dce6d3">No applications are missing descriptions</h3>
  <?php endif; ?>
  
  <?php if(count($applications_with_descriptions) > 0): ?>
    <details>
    <summary>Application pages with descriptions (click arrow):</summary>
    <ul style="list-style-type: inherit; margin-left: 2rem;">
    <?php
    foreach($applications_with_descriptions as $application){
      echo '<li><a href="'.get_term_link($application).'">'.$application->name.'</a> (<a href="'.get_edit_term_link($application).'">edit</a>)</li>';
    }
    ?>
    </ul>
    </details>
  <?php endif; ?>

  <?php if(count($applications_description_too_long) > 0): ?>
    <h3 style="background-color: #f0dddd">Application descriptions too long</h3>
    <p class="description">
      Application categories should all have a <em>description</em>.
      For consistency with other content, the description should be kept short (less than <?php echo $max_except_length; ?> characters).
    </p>
    <ul style="list-style-type: inherit; margin-left: 2rem;">
    <?php
    foreach($applications_description_too_long as $description_too_long){
      $application = $description_too_long[0];
      $description_length = $description_too_long[1];
      echo '<li><a href="'.get_term_link($application).'">'.$application->name.'</a> - '.$description_length.' characters (<a href="'.get_edit_term_link($application).'">edit</a>)</li>';
    }
    ?>
    </ul>
  <?php endif; ?>
  
  <?php if(count($applications_description_too_long) == 0): ?>
    <h3 style="background-color: #dce6d3">No applications descriptions are too long</h3>
  <?php endif; ?>
  
  <?php if(count($applications_description_just_right) > 0): ?>
    <details>
    <summary>Application pages with descriptions of proper length (click arrow):</summary>
    <ul style="list-style-type: inherit; margin-left: 2rem;">
    <?php
    foreach($applications_description_just_right as $description_just_right){
      $application = $description_just_right[0];
      $description_length = $description_just_right[1];
      echo '<li><a href="'.get_term_link($application).'">'.$application->name.'</a> - '.$description_length.' characters (<a href="'.get_edit_term_link($application).'">edit</a>)</li>';
    }
    ?>
    </ul>
    </details>
  <?php endif; ?>

  <?php if(count($methods_missing_keywords) > 0): ?>
    <h3 style="background-color: #f0dddd">Methods with no keywords</h3>
    <p class="description">
      Keywords are used for easier searching and grouping of methods. Every method and bioinformatics should have at least one keyword.
    </p>
    <ul style="list-style-type: inherit; margin-left: 2rem;">
    <?php
    foreach($methods_missing_keywords as $method){
      echo '<li><a href="'.$method->guid.'">'.$method->post_title.'</a> (<a href="'.get_edit_post_link($method).'">edit</a>)</li>';
    }
    ?>
    </ul>
  <?php endif; ?>
  
  <?php if(count($methods_missing_keywords) == 0): ?>
    <h3 style="background-color: #dce6d3">All methods have keywords</h3>
  <?php endif; ?>
  
  <?php if(count($methods_with_keywords) > 0): ?>
    <details>
    <summary>Method pages with keywords (click arrow):</summary>
    <ul style="list-style-type: inherit; margin-left: 2rem;">
    <?php
    foreach($methods_with_keywords as $method){
      echo '<li><a href="'.$method->guid.'">'.$method->post_title.'</a> (<a href="'.get_edit_post_link($method).'">edit</a>)</li>';
    }
    ?>
    </ul>
    </details>
  <?php endif; ?>

  <hr>
  <h3>Administrators</h3>
  <p>The code for the website is available on GitHub:
    <a href="https://github.com/NationalGenomicsInfrastructure/ngisweden.se/" target="_blank">https://github.com/NationalGenomicsInfrastructure/ngisweden.se/</a></p>
  <p>Please contact <a href="mailto:it-support@scilifelab.se">SciLifeLab IT support</a> for help regarding the server.</p>

<?php endif; // if($tab == 'warnings'):
?>

</div>
<hr style="margin-top:3rem;">
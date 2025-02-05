<?php get_header();

$num_results_badge = '';
if (have_posts() && trim(get_query_var('s')) != '') {
  global $wp_query;
  $num_results_badge = ' <span class="badge badge-primary search-count-count-badge">'.$wp_query->found_posts.' results</span>';
}
?>

<div class="ngisweden-sidebar-page">
  <div class="container main-page">
    <h1>Search Results for <em>'<?php echo get_search_query(); ?>'</em><?php echo $num_results_badge; ?></h1>
    <div class="card-columns">
    <?php
    if (have_posts() && trim(get_query_var('s')) != '') {
      while (have_posts()) {
        the_post();
        if($post->post_parent){
          $parent = get_the_title($post->post_parent).' &raquo; ';
        } else {
          $parent = '';
        }
        echo '
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">'.$parent.'<a href="'.get_permalink().'">'.get_the_title().'</a></h5>
              <p class="card-text small text-muted">'.get_the_excerpt().'</p>
            </div>
          </div>';
      }
    } else {
      echo '<p class="text-muted lead">Sorry - nothing found.</p>';
      get_search_form();
    }

    ?>
    </div>
    <?php bootstrap_pagination(); ?>
  </div>
</div>

<?php get_footer(); ?>

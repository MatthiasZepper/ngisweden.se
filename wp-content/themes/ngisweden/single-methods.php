<?php get_header(); ?>

<div class="container main-page">

  <?php
  if (have_posts()) {
    while (have_posts()) {
      the_post();
      echo '<h1>'.get_the_title().'</h1>';
      echo '<p class="methods-lead">'.get_the_excerpt().'</p>';
      the_content();
    }
  }
  ?>

</div>

<?php get_footer();

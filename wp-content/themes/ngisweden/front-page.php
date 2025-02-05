<?php
get_header();
echo do_shortcode('[image-carousel]');
?>

<div class="container main-page" id="front-page-container">
  <?php

  if (have_posts()) {
    while (have_posts()) {
      the_post();
      the_content();
    }
  }
  ?>
</div>


<?php get_footer(); ?>

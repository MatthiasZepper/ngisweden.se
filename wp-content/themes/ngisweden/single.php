<?php get_header(); ?>

<div class="ngisweden-sidebar-page">
  <div class="container main-page">
    <div class="row">
      <div class="col-sm-9 content-main">
        <?php
        if (have_posts()) {
          while (have_posts()) {
            the_post();

            // Categories
            $method_application_badges = '';
            $method_applications = get_the_terms(null, 'applications');
            if ($method_applications && !is_wp_error($method_applications)){
              foreach($method_applications as $kw){
                $parents = '';
                if ( $kw->parent != 0 ) {
                  $parents = singlepage_get_application_parents( $kw->parent );
                }
                $method_application_badges .= '<a href="'.get_term_link($kw->slug, 'applications').'" rel="tag" class="badge badge-success method-keyword '.$kw->slug.'">'.$parents.$kw->name.'</a> ';
              }
            }

            echo '<h1 class="mt-2">'.get_the_title().'</h1>';
            if(has_excerpt() && get_the_excerpt() and strlen(trim(get_the_excerpt()))){
                echo '<p class="methods-lead">'.get_the_excerpt().'</p>';
            }
            the_content();
          }
        }
        ?>
      </div>
      <div class="col-sm-3 ngisweden-sidebar-page-sidebar">
        <div class="sticky-top">
        <?php
        // Post info
        echo '<h5 class="mt-3">Details</h5>';
        echo '<p class="mb-0">'.get_the_date().'</p>';
        echo '<p>By <a href="'.get_author_posts_url(get_the_author_meta('ID')).'">'.get_the_author_meta('display_name').'</a></p>';

        // Categories
        $cats = get_the_category();
        if ($cats && !is_wp_error($cats) && count($cats) > 0){
          echo '<h5 class="mt-3">Categories</h5>';
          foreach($cats as $cat){
            echo '<a href="'.get_category_link($cat).'" rel="tag" class="badge badge-info '.$cat->slug.'">'.$cat->name.'</a> ';
          }
        }

        // Keywords
        $keywords = get_the_tags($post->ID);
        if ($keywords && !is_wp_error($keywords) && count($keywords) > 0){
          echo '<h5 class="mt-3">Keywords</h5>';
          foreach($keywords as $kw){
            echo '<a href="'.get_tag_link($kw).'" rel="tag" class="badge badge-secondary '.$kw->slug.'">'.$kw->name.'</a> ';
          }
        }
        ?>
        </div>
      </div>
    </div>
  </div>
</div>

<?php get_footer();

<?php get_header(); ?>

<div class="ngisweden-sidebar-page">
  <div class="container main-page">
    <div class="row">
      <div class="col-sm-9 content-main">
        <?php
        if (have_posts()) {
          while (have_posts()) {
            the_post();

            echo '<h1>'.get_the_title().'</h1>';
            if(has_excerpt() && get_the_excerpt() and strlen(trim(get_the_excerpt()))){
                echo '<p class="methods-lead">'.get_the_excerpt().'</p>';
            }
            the_content();

            // Show nested technologies
            if(get_post_type() == 'technologies'){
              $tech_children = get_children(get_the_ID());
              if(!empty($tech_children)){
                echo '<h3 class="mt-5">Technologies within this category</h3>';
                $cards_per_row = 2;
                $postcounter = -1;
                foreach($tech_children as $child_id => $tech_child){
                  $postcounter++;

                  // Start a row of cards
                  if($postcounter % $cards_per_row == 0) echo '<div class="ngisweden-application-methods card-deck">';

                  // Print the card
                  echo '
                  <div class="card">
                    <div class="card-body">
                      <h5 class="card-title">
                        <a href="'.$tech_child->guid.'">'.$tech_child->post_title.'</a>
                      </h5>
                      '.$tech_child->post_excerpt.'
                    </div>
                  </div>';

                  // Finish a row of 3 cards
                  if($postcounter % $cards_per_row == $cards_per_row-1) echo '</div>';
                }
                // Loop did not finish a row of 3 cards
                if($postcounter % $cards_per_row != $cards_per_row-1) echo '</div>';

              }
            }
          }
        }
        ?>
      </div>
      <div class="col-sm-3 ngisweden-sidebar-page-sidebar">
        <div class="sticky-top">
        <?php
        //Sample Submission Guidelines button
        echo '<p class="small text-muted">Please read our sample submission instructions before sending samples:</p>';
        echo '<a href="/resources/sample-submission/" target="_blank" class="btn btn-warning mt-n2 mb-3" role="button">Sample Submission Guidelines</a>';
        // Application categories
        $method_applications = get_the_terms(null, 'applications');
        if ($method_applications && !is_wp_error($method_applications) && count($method_applications) > 0){
          echo '<h5>Applications</h5>';
          $app_ids = [];
          foreach($method_applications as $app){
            $app_ids[] = $app->term_id;
            // NOTE - probably doesn't work if we ever have
            // applications which are 3 levels deep
            if($app->parent){
              $app_ids[] = $app->parent;
            }
          }
          echo '<ul class="list-unstyled sidebar-links">';
          wp_list_categories(array(
            'taxonomy' => 'applications',
            'include' => $app_ids,
            'hide_empty' => false,
            'title_li' => '',
          ));
          echo '</ul>';
        }

        // Technologies
        if(get_post_type() == 'methods'){
          $linked_technologies_posts = get_post_meta( get_the_ID(), '_technologies', true );
          if($linked_technologies_posts && count($linked_technologies_posts) > 0){
            echo '<h5 class="mt-3">Relevant Technologies</h5>';
            echo '<ul class="list-unstyled sidebar-links">';
            wp_list_pages(array(
              'post_type' => 'technologies',
              'include' => $linked_technologies_posts,
              'title_li' => '',
              'sort_column' => 'menu_order'
            ));
            echo '</ul>';
          }
        }

        // Bioinformatics pipelines
        if(get_post_type() == 'methods'){
          $linked_bioinfo_posts = get_post_meta( get_the_ID(), '_bioinformatics', true );
          if($linked_bioinfo_posts && count($linked_bioinfo_posts) > 0){
            $series = new WP_Query( array(
              'post_type' => 'bioinformatics',
              'post__in' => $linked_bioinfo_posts,
              'nopaging' => true
            ) );
            if ( $series-> have_posts() ) {
              echo '<h5 class="mt-3">Bioinformatics Pipelines</h5>';
              echo '<div class="sidebar-links">';
              while ( $series->have_posts() ) {
                $series->the_post();
                echo '<p class="mb-0"><a href="'.get_the_permalink().'">'.get_the_title().'</a></p>';
                if(has_excerpt() && get_the_excerpt() and strlen(trim(get_the_excerpt()))){
                  echo '<p class="small text-muted">'.get_the_excerpt().'</p>';
                }
              }
              echo '</div>';
            }
            wp_reset_query();
          }
        }

        // Method Status
        $method_status_badges = '';
        $method_statuses = get_the_terms(null, 'method_status');
        if ($method_statuses && !is_wp_error($method_statuses) && count($method_statuses) > 0){
          echo '<h5 class="mt-3">Method Status</h5>';
          $color_classes = array(
            'red' => 'danger',
            'green' => 'success',
            'blue' => 'primary',
            'turquoise' => 'info',
            'orange' => 'warning'
          );
          foreach($method_statuses as $status){
            $colour = 'badge-secondary';
            $status_colour = get_option( "method_status_colour_".$status->term_id );
            if($status_colour){
              $colour = 'badge-'.$color_classes[$status_colour];
            }
            $url = get_term_link($status->slug, 'method_status');
            echo '<p class="mb-0"><a href="'.$url.'" class="method-status-icon badge '.$colour.'">'.$status->name.'</a></p>';
            echo '<p class="small text-muted">'.$status->description.'</p>';
          }
        }

        // Keywords / tags
        $method_keyword_badges = '';
        $method_keywords = get_the_terms(null, 'method_keywords');
        if ($method_keywords && !is_wp_error($method_keywords) && count($method_keywords) > 0){
          echo '<h5 class="mt-3">Keywords</h5>';
          foreach($method_keywords as $kw){
            echo '<a href="'.get_term_link($kw->slug, 'method_keywords').'" rel="tag" class="badge badge-secondary method-keyword '.$kw->slug.'">'.$kw->name.'</a> ';
          }
        }

        // Link to the software versions page
        if(get_post_type() == 'bioinformatics'){
          echo '<h5 class="mt-3">Software tool versions</h5>';
          echo '<p class="mb-0"><a href="'.get_permalink(get_page_by_path('software-tool-versions')).'">View all software versions</a></p>';
        }

        // Methods for this bioinformatics post
        if(get_post_type() == 'bioinformatics'){
          $linked_method_posts_query = new WP_Query(array(
            'posts_per_page'   => -1,
            'post_type'        => 'methods',
            'meta_key'         => '_bioinformatics',
            'meta_value'       => get_the_ID(),
            'meta_compare'     => 'LIKE'
          ));
          // echo '<pre>'.print_r($linked_method_posts_query, true).'</pre>';
          if($linked_method_posts_query->have_posts()){
            echo '<h5 class="mt-3">Compatible Methods</h5>';
            echo '<div class="sidebar-links">';
            while($linked_method_posts_query->have_posts()){
              $linked_method_posts_query->the_post();
              echo '<p class="mb-0"><a href="'.get_the_permalink().'">'.get_the_title().'</a></p>';
              if($linked_method_posts_query->post_count < 4 && has_excerpt() && get_the_excerpt() and strlen(trim(get_the_excerpt()))){
                echo '<p class="small text-muted">'.get_the_excerpt().'</p>';
              }
            }
            echo '</div>';
          }
          wp_reset_query();
        }
        ?>
        </div>
      </div>
    </div>
  </div>
</div>

<?php get_footer();

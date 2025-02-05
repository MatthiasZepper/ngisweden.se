<?php get_header();

global $post;
$EM_Event = em_get_event($post->ID, 'post_id');

?>

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
                echo '<div class="methods-lead">'.$EM_Event->output('#_EVENTEXCERPT').'</div>';
            }

            echo $EM_Event->output('#_EVENTNOTES');

            // echo '<pre>'.print_r($EM_Event, true).'</pre>';

            if($EM_Event->output('#_AVAILABLESPACES')){
              echo '<h3>Registration</h3>';
              echo $EM_Event->output('#_BOOKINGFORM');
            }

            if($EM_Event->output('#_EVENTIMAGE')){
              echo '<img class="mt-2 rounded shadow" src="'.$EM_Event->output('#_EVENTIMAGEURL').'" class="w-100">';
            }
          }
        }
        ?>
      </div>
      <div class="col-sm-3 ngisweden-sidebar-page-sidebar">
        <div class="sticky-top">

        <?php if(strlen($EM_Event->output('#_LOCATIONNAME')) || strlen($EM_Event->output('#_LOCATIONFULLBR'))): ?>
        <h5>Location</h5>
        <p class="mb-0">
          <?php echo $EM_Event->output('#_LOCATIONNAME'); ?><br>
          <?php echo $EM_Event->output('#_LOCATIONFULLBR'); ?>
        </p>
        <?php
        endif;

        echo $EM_Event->output('#_LOCATIONMAP');
        if($EM_Event->output('#_LOCATIONIMAGE')){
          // Do it like this so that CSS can control the max-width without forced heights
          echo '<img class="mt-1 rounded shadow-sm" src="'.get_the_post_thumbnail_url($EM_Event->output('#_LOCATIONPOSTID'), 'medium').'">';
        }
        ?>

        <h5 class="mt-4">Date / Time</h5>
        <p class="mb-0"><?php echo $EM_Event->output('#_EVENTDATES'); ?></p>
        <p class="mb-1"><?php echo $EM_Event->output('#_EVENTTIMES'); ?></p>
        <p class="mb-1">
          <a href="<?php echo $EM_Event->output('#_EVENTWEBCALURL'); ?>" class="btn btn-sm btn-outline-primary">
            <i class="fas fa-calendar-plus mr-1"></i> Add to calendar
          </a>
        </p>
        <p class="mb-0">
          <a href="<?php echo $EM_Event->output('#_EVENTICALURL'); ?>" class="btn btn-sm btn-outline-primary">
            <i class="fas fa-file-download mr-1"></i> Download calendar invite
          </a>
        </p>

        <?php

        // Categories
        if($EM_Event->output('#_EVENTCATEGORIES')){
          echo '<h5 class="mt-4">Categories</h5>';
          foreach($EM_Event->categories->terms as $cat){
            echo '<a href="'.get_category_link($cat).'" rel="tag" class="badge badge-info '.$cat->slug.'">'.$cat->name.'</a> ';
          }
        }

        // Keywords
        // TODO - doesn't work?
        if($EM_Event->output('#_EVENTTAGS') && $EM_Event->tags){
          echo '<h5 class="mt-4">Keywords</h5>';
          foreach($EM_Event->tags->terms as $kw){
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

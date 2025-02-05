<?php

// Shortcode to show a list of upcoming events on the homepage

function ngisweden_events_list($atts) {

  // Shortcode attributes
  extract(shortcode_atts(array(
    'limit' => 3,
    'min_events' => 2,
    'block_title' => 'Upcoming Events',
    'block_title_link' => false
  ), $atts));

  $html = '';

  if (class_exists('EM_Events')) {
    $em_settings = array(
      'limit' => $limit,
      'orderby' => 'event_start_date,event_start_time',
      'order' => 'ASC',
      'format' => '
        <div class="col-md mb-5">
          <p class="mb-1">#_EVENTLINK</p>
          <p class="small text-muted mb-1">#_EVENTDATES, #_EVENTTIMES</p>
          <p class="small">#_EVENTEXCERPT{10,...}</p>
        </div>
      '
    );

    // Check that we have enough events
    if(EM_Events::count($em_settings) < $min_events){
      return '';
    }

    // Get the events
    $events_output = EM_Events::output($em_settings);

    // Check if there were any upcoming events first
    if(strlen($events_output)){

      // Build the title if supplied
      $title = '';
      if(strlen($block_title)){
        $title = '<h5>';
        if($block_title_link) $title .= '<a href="'.$block_title_link.'" class="text-decoration-none text-body">';
        $title .= $block_title;
        if($block_title_link) $title .= '</a>';
        $title .= '</h5>';
      }

      // Final HTML
      $html = $title.'<div class="row">'.$events_output.'</div>';
    }
  }

  return $html;
}
add_shortcode('ngisweden_events_list', 'ngisweden_events_list');

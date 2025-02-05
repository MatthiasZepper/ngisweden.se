<?php

// Shortcodes for content to go on the homepage

function ngisweden_ajax_search($atts_raw){

  $output = '<div class="ngiswden-ajax-search ngi-tabs">';
  $output .= do_shortcode('[wpdreams_ajaxsearchlite]');
  $output .= '</div>';
  return $output;
}
add_shortcode('ngisweden_search', 'ngisweden_ajax_search');

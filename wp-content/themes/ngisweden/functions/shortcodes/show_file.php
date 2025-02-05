<?php

// Shortcode to include a HTML file in the page
// Snippet from https://www.isitwp.com/include-external-file-shortcode/

function ngi_show_file_shortcode_func($atts) {
  extract(shortcode_atts(array('file' => ''), $atts));
  if ($file!=''){
    return @file_get_contents($file);
  }
}
add_shortcode('show_file', 'ngi_show_file_shortcode_func');

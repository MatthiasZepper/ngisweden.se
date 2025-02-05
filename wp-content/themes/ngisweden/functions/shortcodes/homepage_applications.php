<?php

// Shortcodes for content to go on the homepage

function ngisweden_homepage_applications($atts_raw){

  $output = '<div class="homepage-applications">';

  // Tab buttons to switch between applications and technologies
  $output .= '
  <ul class="nav nav-pills nav-fill shadow-sm homepage-applications-tabbtns">
    <li class="nav-item">
      <a href="#homepage-applications-tabcontent" class="nav-link btn btn-light active" data-toggle="tab" role="tab">Applications</a>
    </li>
    <li class="nav-item">
      <a href="#homepage-technologies-tabcontent" class="nav-link btn btn-light" data-toggle="tab" role="tab">Technologies</a>
    </li>
    <li class="nav-item">
      <a href="#homepage-bioinformatics-tabcontent" class="nav-link btn btn-light" data-toggle="tab" role="tab">Bioinformatics</a>
    </li>
  </ul>
  ';

  $output .= '<div class="tab-content">';

  // APPLICATIONS
  $output .= '<div class="tab-pane fade show active" id="homepage-applications-tabcontent">';
  $output .= do_shortcode('[ngisweden_tabs]');
  $output .= '</div>';

  // TECHNOLOGIES
  $output .= '<div class="tab-pane fade" id="homepage-technologies-tabcontent">';
  $output .= do_shortcode('[ngisweden_tabs type=technologies]');
  $output .= '</div>';

  // BIOINFORMATICS
  $output .= '<div class="tab-pane fade" id="homepage-bioinformatics-tabcontent">';
  $output .= do_shortcode('[ngisweden_tabs type=bioinformatics]');
  $output .= '</div>';

  $output .= '</div>'; // .tab-content
  $output .= '</div>'; // #applications-shortcode-div
  return $output;
}
add_shortcode('homepage_applications', 'ngisweden_homepage_applications');

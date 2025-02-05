<?php

// NGI Deployed Tools Versions
// Renders the file generated on the cluster during deployment,
// showing which versions of which tools are currently in production.

function ngisweden_deployed_tools_versions_shortcode($atts_raw){

    // Fetch the deployment version file
    $locations = array('sthlm'=>'Stockholm node', 'upps'=>'Uppsala node');
    $output = '';
    foreach ($locations as $loc_code => $location){
      $deployed_file_contents = @file_get_contents(get_template_directory().'/cache/deployed_tools.'.$loc_code.'.version');

      $deployment_type = '';
      $deployed_at = '';
      $tools = [];

      foreach(preg_split("/\r\n|\n|\r/", $deployed_file_contents) as $line){
          if(substr($line, 0, 14) == '-- Deployed at'){
              $deployed_at = trim(str_replace('--', '', $line));
          }
          else if(substr($line, 0, 2) == '--'){
              $deployment_type = trim(str_replace('--', '', $line));
          }
          else {
              @list($tool, $version) = explode(':', $line, 2);
              if(strlen($tool) > 0){
                  $tools[] = [trim($tool), trim($version)];
              }
          }
      }
      $output .= '<h5>'.$location.'</h5>';
      if(count($tools) > 0){
          $output .= '<div class="deployed-tools-versions">';
          $output .= '<table class="table table-striped table-hover table-sm small">';
          $output .= '<thead><tr><th>Tool</th><th>Version</th></tr></thead>';
          $output .= '<tbody>';
          foreach($tools as $tool){
              $output .= '<tr><td>'.$tool[0].'</td><td><code>'.$tool[1].'</code></td></tr>';
          }
          $output .= '</tbody>';
          $output .= '</table>';
          $output .= '<p>Deployment type: '.$deployment_type.'</p>';
          $output .= '<p>'.$deployed_at.'</p>';
          $output .= '</div>';
      }
      else {
          $output = '<div class="deployed-tools-versions">';
          $output .= '<p class="text-muted font-italic">No version information found.</p>';
          $output .= '</div>';
      }
    }
    return $output;

}
add_shortcode('ngisweden_deployed_tools_versions', 'ngisweden_deployed_tools_versions_shortcode');

<?php

// NGI Publications Shortcode
// Fetches publications from publications.scilifelab.se and displays nicely
function ngisweden_pubs_shortcode($atts_raw){

    // Facility labels
    $fac_labels = array(
        'NGI Stockholm (Genomics Applications)',
        'NGI Stockholm (Genomics Production)',
        'NGI Uppsala (SNP&SEQ Technology Platform)',
        'NGI Uppsala (Uppsala Genome Center)'
    );
    $download_limit = 50;

    // Shortcode attribute defaults
    $atts = shortcode_atts( array(
        'title' => 1,
        'randomise' => 1,
        'num' => 5,
        'collabs' => 0,
        'max_collabs' => -1
    ), $atts_raw);

    // Fetch the cached publications data
    $pubs_json = @file_get_contents(get_template_directory().'/cache/publications_cache.json');
    $pubs_data = @json_decode($pubs_json, true);

    // Refresh cache if it doesn't exist or is more than a week old
    if(!$pubs_data or $pubs_data['downloaded'] < (time()-(60*60*24*7))){
        $pubs_data = array(
            'downloaded' => time(),
            'publications' => array()
        );
        foreach($fac_labels as $fac){
            $pubs_url = 'https://publications.scilifelab.se/label/'.rawurlencode($fac).'.json?limit='.$download_limit;
            $pubs_json = file_get_contents($pubs_url);
            $pubs_raw_data = json_decode($pubs_json, true);
            $pubs_data['publications'] = array_merge($pubs_data['publications'], $pubs_raw_data['publications']);
        }

        // Clean up
        $pub_ids = array();
        $dois = array();
        foreach($pubs_data['publications'] as $idx => $pub){
            // Remove duplicates - from parallel facilities and dup DOIs in publications.scilifelab.se
            if(in_array($pub['iuid'], $pub_ids) || in_array($pub['doi'], $dois)){
                unset($pubs_data['publications'][$idx]);
                continue;
            }
            array_push($pub_ids, $pub['iuid']);
            array_push($dois, $pub['doi']);

            // Check if this is a collaboration
            $pubs_data['publications'][$idx]['is_collab'] = false;
            foreach($fac_labels as $fac){
                if(isset($pub['labels'][$fac]) && $pub['labels'][$fac] == 'Collaborative'){
                    $pubs_data['publications'][$idx]['is_collab'] = true;
                }
            }
        }
        @file_put_contents(get_template_directory().'/cache/publications_cache.json', json_encode($pubs_data));
    }

    // Randomise the order
    if($atts['randomise']) {
        shuffle($pubs_data['publications']);
    }

    // Build output
    $modals = '';
    $pubs_items = array();
    $i = 0;
    $num_collabs = 0;
    foreach($pubs_data['publications'] as $pub){

        // Skip collaborative papers if we already have the maximum number
        if($atts['max_collabs'] >= 0 && $num_collabs >= $atts['max_collabs'] && $pub['is_collab']){
            continue;
        }

        // Skip non-collaborative papers if we need only collabs from here ony
        if($atts['collabs'] > 0){
            $remaining_non_collab = $atts['num'] - $atts['collabs'] - $i;
            if(!$pub['is_collab'] && $remaining_non_collab <= 0){
                continue;
            }
        }

        // Limit the number shown
        if($i >= $atts['num']){
            break;
        }

        // Bump the counters
        $i++;
        if($pub['is_collab']){
            $num_collabs += 1;
        }

        // Add to the visible list
        $pubs_items[] = '
        <a data-toggle="modal" data-target="#pub_'.$pub['iuid'].'" href="'.$pub['links']['display']['href'].'" target="_blank" class="list-group-item list-group-item-action'.($pub['is_collab'] ? ' list-pub-collab' : '').'">
            '.$pub['title'].'<br>
            <small class="text-muted"><em>'.$pub['journal']['title'].'</em> ('.explode('-', $pub['published'])[0].')</small>
        </a>';

        //
        // Make publication modal
        //

        // Make authors array
        $authors = array();
        foreach($pub['authors'] as $author){
            // If ALL CAPS then capitilise nicely
            if(strtoupper($author['given']) == $author['given']){
                $author['given'] = ucwords(strtolower($author['given']));
            }
            if(strtoupper($author['family']) == $author['family']){
                $author['family'] = ucwords(strtolower($author['family']));
            }
            $authors[] = '<span class="pub-author" title="'.$author['given'].' '.$author['family'].'">'.$author['initials'].' '.$author['family'].'</span>';
        }

        // Make publication ref string
        $pub_ref = '';
        if($pub['journal']['title']){
            $pub_ref .= '<em>'.$pub['journal']['title'].'</em>, ';
        }
        $pub_ref .= '<small>';
        if($pub['journal']['volume']){
            $pub_ref .= '<strong>'.$pub['journal']['volume'].'</strong> ';
        }
        if($pub['journal']['issue']){
            $pub_ref .= '('.$pub['journal']['issue'].') ';
        }
        if($pub['journal']['issn']){
            $pub_ref .= $pub['journal']['issn'].' ';
        }
        if($pub['published']){
            $pub_ref .= '('.explode('-', $pub['published'])[0].')';
        }
        $pub_ref .= '</small>';

        // NGI collaboration flag
        $collab_badge = '';
        if($pub['is_collab']){
            $collab_badge = '<span class="float-right badge badge-primary">NGI Collaboration</span>';
        }

        // Only show modal body if we have an abstract
        $footer_border = '';
        if($pub['abstract']){
            $abstract = '<div class="modal-body small">'.$pub['abstract'].'</div>';
        } else {
            // Due to a bootstrap bug, we need a modal-body element https://github.com/twbs/bootstrap/issues/28906
            // So just hide it if empty
            $abstract = '<div class="modal-body d-none"></div>';
            // If it's hidden we get a double border from the footer-header, so need to hide one
            $footer_border = 'border-0';
        }
        $modals .= '
        <div class="modal fade" id="pub_'.$pub['iuid'].'" tabindex="-1" role="dialog" aria-hidden="true">
          <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <div class="modal-title">
                    <h5>'.$pub['title'].'</h5>
                    <div class="font-weight-light pub-authors">'.implode(', ', $authors).'</div>
                    <p class="mb-0">'.$collab_badge.$pub_ref.'</p>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              </div>
              '.$abstract.'
              <div class="modal-footer '.$footer_border.'">
                <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Close</button>
                <a href="https://www.ncbi.nlm.nih.gov/pubmed/'.$pub['pmid'].'" target="_blank" class="btn btn-sm btn-info">Pubmed <i class="fas fa-external-link-alt fa-sm ml-2"></i></a>
                <a href="https://dx.doi.org/'.$pub['doi'].'" target="_blank" class="btn btn-sm btn-primary">DOI <i class="fas fa-external-link-alt fa-sm ml-2"></i></a>
                <a href="'.$pub['links']['display']['href'].'" target="_blank" class="btn btn-sm btn-success">SciLifeLab Pubs <i class="fas fa-external-link-alt fa-sm ml-2"></i></a>
              </div>
            </div>
          </div>
        </div>';
    }

    // Randomise the order again, so that collabs aren't always at the bottom
    if($atts['randomise']) {
        shuffle($pubs_items);
    }

    // Build the pubs list
    $pubs_div = '<div class="ngisweden-publications mb-5">';
    if($atts['title']) {
        $pubs_div .= '<h5>User Publications</h5>';
    }
    $pubs_div .= '<div class="list-group">';
    $pubs_div .= implode("\n", $pubs_items);
    $pubs_div .= '</div></div>';

    // Return the list and modals output
    return $pubs_div.$modals;
}
add_shortcode('ngisweden_publications', 'ngisweden_pubs_shortcode');

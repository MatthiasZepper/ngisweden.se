<?php

// GitHub Repo Badge Shortcode
// Uses the GitHub API to get details about a repo and show a nice badge
function github_badge_shortcode($atts_raw, $content = null){

    // Shortcode attribute defaults
    $atts = shortcode_atts( array(
        'repo' => '',
        'icon' => '',
        'centred' => 0
    ), $atts_raw);

    // Collect all repositories to plot
    $repos = [];

    // Shortcode repo attribute
    if(strlen(trim($atts['repo']))){
        $repos[] = trim($atts['repo']);
    }

    // Multiple repos from shortcode contents
    if($content){
        $repos = array_merge($repos, explode(PHP_EOL, $content));
    }

    // Fetch the cached publications data
    $repos_json = @file_get_contents(get_template_directory().'/cache/github_badge_cache.json');
    $repos_data = @json_decode($repos_json, true);
    // Save an empty array if we can't load the cached JSON
    if (json_last_error() !== JSON_ERROR_NONE) {
        $repos_data = array();
    }

    $printed_cards = 0;
    $html = '';
    foreach($repos as $repo_name){

        $icon = $atts['icon'];
        $centred = $atts['centred'];

        // Clean the repo name
        $repo_name = strip_tags($repo_name);
        $repo_name = trim($repo_name);

        // Pull out icon and centred if from a content block
        if(preg_match('/icon=(\S+)/', $repo_name, $icon_matches)){
            $icon = $icon_matches[1];
            $repo_name = preg_replace('/icon=\S+/', '', $repo_name);
        }
        if(preg_match('/centred=(\S+)/', $repo_name, $centred_matches)){
            $centred = $centred_matches[1];
            $repo_name = preg_replace('/centred=\S+/', '', $repo_name);
        }

        // Normalise repo name
        $repo_name = str_replace('https://github.com/', '', $repo_name);
        $repo_name = trim($repo_name, '/');
        $repo_parts = explode('/', $repo_name);
        // Skip if the repo looks wrong
        if(!strlen($repo_name) || count($repo_parts) != 2){
            continue;
        }

        // Get the data for this repo
        $repo = $repos_data[$repo_name];

        // Refresh cache if it doesn't exist or is more than a week old
        if(!$repo or $repo['api_data_downloaded'] < (time()-(60*60*24*7))){
            $gh_api_url = 'https://api.github.com/repos/'.$repo_name;
            // Fake the headers so that we don't get 403 forbidden
            $context = stream_context_create(array("http" => array(
                "header" => "User-Agent: Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/50.0.2661.102 Safari/537.36"
            )));
            $gh_repo_json = file_get_contents($gh_api_url, false, $context);
            $repo = json_decode($gh_repo_json, true);
            // Add our own timestamp so that we can refresh this after a week
            $repo['api_data_downloaded'] = time();
            // Save the cache
            $repos_data[$repo_name] = $repo;
            @file_put_contents(get_template_directory().'/cache/github_badge_cache.json', json_encode($repos_data));
        }

        // Build output
        $centre_margin = $centred ? 'ml-auto mr-auto' : '';
        $homepage = '';
        if($repo['homepage'] && strlen(trim($repo['homepage']))){
            $homepage = ' - <a href="'.$repo['homepage'].'" target="_blank">'.$repo['homepage'].'</a>';
        }
        $icon_url = $repo['owner']['avatar_url'];
        if(strlen(trim($icon))){
            $icon_url = $icon;
        }
        // Encourage line-breaks around the separator, if anywhere
        $full_name = str_replace('/', '<wbr>/<wbr>', $repo['full_name']);
        $print_url = str_replace('/', '<wbr>/<wbr>', $repo['html_url']);
        $html .= '
        <div class="card mt-2 mb-3 '.$centre_margin.'" style="max-width: 540px;">
            <div class="card-body px-0">
                <div class="row no-gutters">
                    <div class="col-md-4">
                        <a href="'.$repo['html_url'].'" target="_blank">
                            <img src="'.$icon_url.'" class="card-img p-4" alt="'.$repo['owner']['login'].'">
                        </a>
                    </div>
                    <div class="col-md-8">
                        <h5 class="mb-0"><a href="'.$repo['html_url'].'" target="_blank" class="text-body">'.$full_name.'</a></h5>
                        <p class="mb-1"><small><a href="'.$repo['html_url'].'" target="_blank" class="small text-muted">'.$print_url.'</a></small></p>
                        <p class="card-text small">'.$repo['description'].$homepage.'</p>
                    </div>
                </div>
            </div>
            <div class="card-footer small text-center px-0">
                <div class="row no-gutters">
                    <div class="col">
                        <a class="text-muted" href="'.$repo['html_url'].'/watchers" target="_blank"><i class="fas fa-eye"></i> '.$repo['watchers_count'].'</a>
                    </div>
                    <div class="col">
                        <a class="text-muted" href="'.$repo['html_url'].'/stargazers" target="_blank"><i class="fas fa-star"></i> '.$repo['stargazers_count'].'</a>
                    </div>
                    <div class="col">
                        <a class="text-muted" href="'.$repo['html_url'].'/network/members" target="_blank"><i class="fas fa-code-branch"></i> '.$repo['forks_count'].'</a>
                    </div>
                </div>
            </div>
        </div>';
        $printed_cards += 1;
    };

    // Put a warning in a HTML comment if we didn't get anything
    if($printed_cards == 0){
        return '<!-- [github_badge] shortcode called, but without repository. Use as follows: [github_badge repo=https://github.com/user/repo] or [github_badge]https://github.com/user/repo[/github_badge] -->';
    }

    // If we had more than one repo, put these into a bootstrap card deck
    if($printed_cards > 1){
        $html = '<div class="card-deck mx-0">'.$html.'</div>';
    }

    // Return the output
    return $html;
}
add_shortcode('github_badge', 'github_badge_shortcode');

<?php

// Shortcode to print the Mailchimp signup form

function ngisweden_mailchimp_subscribe_sc($atts_raw){

    // Shortcode attribute defaults
    $atts = shortcode_atts( array(
        'form' => 0,
        'icon' => 1,
        'btn_text' => 'Subscribe',
        'btn_colour' => 'primary',
        'btn_size' => ''
    ), $atts_raw);
    $btn_txt = $atts['btn_text'];
    $btn_colour = $atts['btn_colour'];
    if($atts['btn_size'] == 'large'){
        $btn_colour .= ' btn-lg';
    }
    if($atts['btn_size'] == 'small'){
        $btn_colour .= ' btn-sm';
    }

    // Just the form, will display in the page
    if($atts['form']){
        $html = <<<HTML
        <form action="https://scilifelab.us9.list-manage.com/subscribe/post?u=674e662b5645828c6431c0eea&amp;id=7239004f8a" method="post" name="mc-embedded-subscribe-form" class="mailchimp_inline_signup_form form-inline" target="_blank">
            <label for="mce-EMAIL" class="mb-2 mr-sm-2">Subscribe to our mailing list:</label>
            <input type="email" name="EMAIL" id="mce-EMAIL" class="form-control mb-2 mr-sm-2" placeholder="email address" required>
            <!-- real people should not fill this in and expect good things - do not remove this or risk form bot signups-->
            <div style="position: absolute; left: -5000px;" aria-hidden="true"><input type="text" name="b_674e662b5645828c6431c0eea_7239004f8a" tabindex="-1" value=""></div>
            <input type="submit" value="$btn_txt" name="subscribe" class="btn btn-$btn_colour mb-2">
        </form>
HTML;
        // Remember - 'HTML;' block end won't work if not indented
    }
    // Show a button that opens a modal with the form
    else {
        $privacy_policy_link = get_privacy_policy_url();
        $privacy_policy_btn = '';
        if(strlen(trim($privacy_policy_link))){
            $privacy_policy_btn = '<a href="'.$privacy_policy_link.'" class="btn btn-outline-secondary">Privacy policy</a>';
        }
        if($atts['icon']){
            $btn_txt = '<i class="fas fa-envelope mr-1"></i> '.$btn_txt;
        }
        $html = <<<HTML
        <button type="button" class="btn btn-$btn_colour mb-2" data-toggle="modal" data-target="#exampleModal">
            $btn_txt
        </button>

        <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <form action="https://scilifelab.us9.list-manage.com/subscribe/post?u=674e662b5645828c6431c0eea&amp;id=7239004f8a" method="post" name="mc-embedded-subscribe-form" class="mailchimp_modal_signup_form" target="_blank">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Subscribe to our newsletter</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="mce-EMAIL" class="mb-2 mr-sm-2">Your email address:</label>
                                <input type="email" name="EMAIL" id="mce-EMAIL" class="form-control mb-2 mr-sm-2" placeholder="Email address" required>
                            </div>
                            <p class="small">Sign up to our newsletter to get updates about the platform delivered directly to your inbox.
                            Newsletters are infrequent and we will not share your email address with any parties outside SciLifeLab.</p>
                        </div>
                        <div class="modal-footer">
                            $privacy_policy_btn
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <input type="submit" value="Subscribe" name="subscribe" class="btn btn-primary">
                        </div>
                    </div>
                </div>
                <!-- real people should not fill this in and expect good things - do not remove this or risk form bot signups-->
                <div style="position: absolute; left: -5000px;" aria-hidden="true"><input type="text" name="b_674e662b5645828c6431c0eea_7239004f8a" tabindex="-1" value=""></div>
            </form>
        </div>
HTML;
        // Remember - 'HTML;' block end won't work if not indented
    }

    return $html;
}
add_shortcode('mailchimp_subscribe', 'ngisweden_mailchimp_subscribe_sc');

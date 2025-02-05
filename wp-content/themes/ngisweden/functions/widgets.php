<?php

// Register widget areas
function ngisweden_widgets_init() {
    register_sidebar( array(
        'name' => 'Footer - Column 1',
        'id' => 'footer-widget-area-1',
        'before_widget' => '',
        'after_widget' => '',
        'before_title' => '<h4 class="widget-title">',
        'after_title' => '</h4>',
    ) );
    register_sidebar( array(
        'name' => 'Footer - Column 2',
        'id' => 'footer-widget-area-2',
        'before_widget' => '',
        'after_widget' => '',
        'before_title' => '<h4 class="widget-title">',
        'after_title' => '</h4>',
    ) );
    register_sidebar( array(
        'name' => 'Footer - Column 3',
        'id' => 'footer-widget-area-3',
        'before_widget' => '',
        'after_widget' => '',
        'before_title' => '<h4 class="widget-title">',
        'after_title' => '</h4>',
    ) );
    register_sidebar( array(
        'name' => 'Footer - Column 4',
        'id' => 'footer-widget-area-4',
        'before_widget' => '',
        'after_widget' => '',
        'before_title' => '<h4 class="widget-title">',
        'after_title' => '</h4>',
    ) );
    register_sidebar( array(
        'name' => 'Footer - Column 5',
        'id' => 'footer-widget-area-5',
        'before_widget' => '',
        'after_widget' => '',
        'before_title' => '<h4 class="widget-title">',
        'after_title' => '</h4>',
    ) );
}
add_action('widgets_init', 'ngisweden_widgets_init');



// Add theme widgets for footer
class FooterUniLogos extends WP_Widget {
    public function __construct() {
        $widget_opts = array(
            'classname' => 'ngisweden-footer-logos',
            'description' => 'SciLifeLab / university logos for the footer',
        );
        parent::__construct( 'ngisweden_footer_logos', 'NGI Footer Logos', $widget_opts);
    }
    public function widget( $args, $instance ) {
        echo $args['before_widget'];
        if ( ! empty( $instance['title'] ) ) {
            echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
        }
        echo '<div class="ngisweden-footer-logos text-md-right">
            <a href="https://www.scilifelab.se" target="_blank"><img src="'.get_stylesheet_directory_uri().'/img/SciLifeLab-logo.svg" class="footer-scilifelab-logo" alt="SciLifeLab logo"></a>
            <br>
            <a href="https://www.ki.se" target="_blank"><img src="'.get_stylesheet_directory_uri().'/img/KI-logo.svg" class="footer-uni-logo" alt="KI logo"></a>
            <a href="https://www.kth.se" target="_blank"><img src="'.get_stylesheet_directory_uri().'/img/KTH-logo.svg" class="footer-uni-logo" alt="KTH logo"></a>
            <a href="https://www.su.se" target="_blank"><img src="'.get_stylesheet_directory_uri().'/img/SU-logo.svg" class="footer-uni-logo" alt="SU logo"></a>
            <a href="https://www.uu.se" target="_blank"><img src="'.get_stylesheet_directory_uri().'/img/UU-logo.svg" class="footer-uni-logo" alt="UU logo"></a>
        </div>';
        echo $args['after_widget'];
    }
    // Admin interface
    public function form( $instance ) {
        $title = ! empty( $instance['title'] ) ? $instance['title'] : esc_html__( 'New title', 'text_domain' );
        ?>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_attr_e( 'Title:', 'text_domain' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
        </p>
        <?php
    }
    // Save sanitation
    public function update( $new_instance, $old_instance ) {
        $instance = array();
        $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? sanitize_text_field( $new_instance['title'] ) : '';
        return $instance;
    }
}
add_action( 'widgets_init', function(){ register_widget('FooterUniLogos'); });

class FooterSocialButtons extends WP_Widget {
    public function __construct() {
        $widget_opts = array(
            'classname' => 'ngisweden-footer-social',
            'description' => 'Twitter, YouTube and GitHub buttons',
        );
        parent::__construct( 'ngisweden_footer_social', 'NGI Footer Social Buttons', $widget_opts);
    }
    // Front end
    public function widget( $args, $instance ) {
        echo $args['before_widget'];
        if ( ! empty( $instance['title'] ) ) {
            echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
        }
        echo '<div class="ngisweden-footer-social">
            <a href="https://twitter.com/ngisweden?ref_src=twsrc%5Etfw" class="twitter-follow-button" data-show-count="false">Follow @ngisweden</a><script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>

            <br><script src="https://apis.google.com/js/platform.js"></script>
            <div class="g-ytsubscribe" data-channelid="UC7vNQ8xMt2zViIdtngTCTsw" data-layout="default" data-count="default"></div>

            <br><a class="github-button" href="https://github.com/NationalGenomicsInfrastructure" aria-label="Follow @NationalGenomicsInfrastructure on GitHub">Follow @NationalGenomicsInfrastructure</a>
            <br><a class="github-button" href="https://github.com/SciLifeLab" aria-label="Follow @SciLifeLab on GitHub">Follow @SciLifeLab</a>
            <script async defer src="https://buttons.github.io/buttons.js"></script>
        </div>';
        echo $args['after_widget'];
    }
    // Admin interface
    public function form( $instance ) {
        $title = ! empty( $instance['title'] ) ? $instance['title'] : esc_html__( 'New title', 'text_domain' );
        ?>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_attr_e( 'Title:', 'text_domain' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
        </p>
        <?php
    }
    // Save sanitation
    public function update( $new_instance, $old_instance ) {
        $instance = array();
        $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? sanitize_text_field( $new_instance['title'] ) : '';
        return $instance;
    }
}
add_action( 'widgets_init', function(){ register_widget('FooterSocialButtons'); });

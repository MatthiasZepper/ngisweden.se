<!DOCTYPE html>
<html lang="en">
<!--

         ,,,,,,,,,,,,,,((((((
         ,,,,,,,,,,,,,((((((
         ,,,,,,,,,,((((((((        ::::    :::   ::::::::   :::::::::::
          ,,,,,,((((((((((         :+:+:   :+:  :+:    :+:      :+:
           ,,((((((((((((          :+:+:+  +:+  +:+             +:+
                                   +#+ +:+ +#+  :#:             +#+
         //////////////,,          +#+  +#+#+#  +#+   +#+#      +#+
       ((((((((((((((((,,          #+#   #+#+#  #+#    #+#      #+#
     (((((((((((((((((,,,,         ###    ####   ########   ###########
   (((((((((((((((((((,,,,,
  (((((((((((((((((((,,,,,

Welcome to the NGI Sweden website code!
Check out the theme source on GitHub: https://github.com/nationalGenomicsInfrastructure/ngisweden.se/

-->
  <head>
    <!-- Google Tag Manager -->
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
    j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
    'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','GTM-PZDKQ493');</script>
    <!-- End Google Tag Manager -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />

    <title><?php bloginfo( 'name' ); wp_title(); ?></title>
    <meta name="description" content="<?php bloginfo('description'); ?>">
    <meta name="author" content="Phil Ewels">
    <meta name="copyright" content="All site content copyright <?php bloginfo( 'name' ); ?>, <?php echo date('Y'); ?>" />

    <?php wp_head(); ?>
  </head>
  <body <?php body_class('ngisweden'); ?>>
    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-PZDKQ493"
    height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->
    <nav class="navbar navbar-expand-lg fixed-top navbar-light shadow-sm main-nav-nav" id="main_navbar">
      <div class="container">
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-end ml-n8" id="navbarSupportedContent">
        <a class="navbar-brand" href="<?php echo home_url( '/' ); ?>">
          <img src="<?php echo get_stylesheet_directory_uri(); ?>/img/NGI-logo.svg" height="95" width="300" class="navbar-logo" alt="NGI logo" />
        </a>
          <?php
          // https://github.com/wp-bootstrap/wp-bootstrap-navwalker
          wp_nav_menu([
            'menu'            => 'main-nav',
            'theme_location'  => 'main-nav',
            'container'       => false,
            'menu_id'         => false,
            'menu_class'      => 'navbar-nav',
            'depth'           => 2,
            'fallback_cb'     => 'WP_Bootstrap_Navwalker::fallback',
            'walker'          => new WP_Bootstrap_Navwalker()
          ]);
          ?>
          <a class="btn btn-primary new-order-btn mr-2" id="menu-main-order-btn" href="https://ngisweden.scilifelab.se/orders">New Order</a>
          <?php echo do_shortcode('[wpdreams_ajaxsearchlite]'); ?>
        </div>
      </div>
    </nav>
    <?php
    // Navigation breadcrumbs
    if(!is_front_page()){
        bootstrap_breadcrumb();
    }

    // Customise > Bootstrap Banner
    if (function_exists('global_bootstrap_banner')){
        echo global_bootstrap_banner();
    }
    ?>

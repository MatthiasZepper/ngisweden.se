<!DOCTYPE html>
<html lang="en">
  <head>
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
    <nav class="navbar navbar-expand-lg fixed-top navbar-light bg-light shadow-sm main-nav-nav" id="main_navbar">
      <div class="container">
        <a class="navbar-brand" href="<?php echo home_url( '/' ); ?>">
          <img src="<?php echo get_stylesheet_directory_uri(); ?>/img/NGI-logo.svg" height="95" width="300" class="navbar-logo" alt="NGI logo" />
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse justify-content-end" id="navbarSupportedContent">
          <?php
          wp_nav_menu([
            'menu'            => 'main-nav',
            'theme_location'  => 'main-nav',
            'container'       => false,
            'menu_id'         => false,
            'menu_class'      => 'navbar-nav',
            'depth'           => 2,
            'fallback_cb'     => false,
            'walker'          => new bs4navwalker()
          ]);
          ?>
          <a class="btn btn-primary new-order-btn" id="menu-main-order-btn" href="https://ngisweden.scilifelab.se/">New Order</a>
          <?php echo do_shortcode('[wpdreams_ajaxsearchlite]'); ?>
        </div>
      </div>
    </nav>
    <?php if(!is_front_page()){ bootstrap_breadcrumb(); } ?>

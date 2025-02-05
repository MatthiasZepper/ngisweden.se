    <footer>
      <div class="container">
        <div class="row footer-widgets">
          <?php
          for ($i = 1; $i <= 5; $i++){
            if (is_active_sidebar('footer-widget-area-'.$i)){
              echo '<div class="col-md">';
              dynamic_sidebar( 'footer-widget-area-'.$i );
              echo '</div>';
            }
          }
          ?>
        </div>
        <?php
        // Print which template file is being used if we're developing the theme
        if(is_super_admin() && defined('WP_DEBUG') && WP_DEBUG === true){
          global $template;
          echo('<p class="small text-muted">Template file being used: <code>'.$template.'</code></p>');
        }
        ?>
      </div>
    </footer>
    <?php wp_footer(); ?>
  </body>
</html>

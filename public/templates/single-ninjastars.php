<?php
get_header(); ?>
    <div class="ng-thumb-wrapper">
      <?php if ( have_posts() ) : ?>
        <?php while ( have_posts() ) : the_post();
			
			include plugin_dir_path( dirname( __FILE__ ) ) .  'partials/ninja-gallery-content.php';
			
        endwhile; else: ?>
      <?php endif; ?>
    </div> <!-- end of thumb-wrapper -->
<?php    
get_footer();
?>
<?php
get_header(); ?>
    <div class="ns-reviews-single-wrapper">
      <?php if ( have_posts() ) : ?>
        <?php while ( have_posts() ) : the_post();
			
			include_once plugin_dir_path( dirname( __FILE__ ) ) .  'partials/ninjastars-content.php';
			
        endwhile; else: ?>
      <?php endif; 
      		include_once plugin_dir_path( dirname( __FILE__ ) ) .  'partials/ninjastars-average-ratings.php';
      ?>
    </div> <!-- end of thumb-wrapper -->
<?php    
get_footer();
?>
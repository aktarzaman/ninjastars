<?php
get_header(); ?>
    <div class="ns-reviews-archive-wrapper">
      <?php if ( have_posts() ) : ?>
        <?php while ( have_posts() ) : the_post();
			
			include plugin_dir_path( dirname( __FILE__ ) ) .  'partials/ninjastars-content.php';
			
        endwhile; else: ?>
      <?php endif; ?>
    </div> <!-- end of thumb-wrapper -->
    <?php include_once plugin_dir_path( dirname( __FILE__ ) ) .  'partials/ninjastars-average-ratings.php'; ?>
<?php    
get_footer();
?>
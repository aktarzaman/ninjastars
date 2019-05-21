<?php

/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       http://socialmedianinjas.com
 * @since      1.0.0
 *
 * @package    Ninjastars
 * @subpackage Ninjastars/public/partials
 */
?>

<?php

$review_count = wp_count_posts('ninjastars');
$review_count = $review_count->publish;

$args = array(
	'post_type' 		=> 'ninjastars',
	'post_status'		=> 'publish',
	'posts_per_page'	=> -1,
	'order'				=> 'DESC',
);

$reviews = get_posts( $args );
$total_rating = 0;
$best_rating = 0;
$worst_rating = 5;
foreach ( $reviews as $review ) : 
	setup_postdata( $review );
	$meta = get_post_custom( $review->ID );
	$rating = $meta['review_rating_val'][0];
	$total_rating += $rating;
	if ( $rating > $best_rating ) 
		$best_rating = $rating;
	if ( $rating < $worst_rating ) 
		$worst_rating = $rating;
endforeach;
$avg_rating = ( $total_rating / $review_count );
wp_reset_postdata();
?>

<div class="ns-single-schema">
	<span class="ns-biz-title" itemprop="name"><?php echo $biz_name ?></span> is rated 
	<span><?php echo round($avg_rating, 1); ?></span> stars over 
	<span><?php echo $review_count ?></span> reviews.
</div>
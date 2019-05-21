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
$round_rating= 1;
$review_count = wp_count_posts('ninjastars');
$review_count = $review_count->publish;
$total_rating = 0;
$best_rating = 0;
$worst_rating = 5;

$args = array(
	'post_type' 		=> 'ninjastars',
	'post_status'		=> 'publish',
	'posts_per_page'	=> -1,
	'order'				=> 'DESC',
);

$reviews = get_posts( $args );

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

# Calculate Round Rating
if ( $avg_rating < 1.4 ) $round_rating = 1;
if ( $avg_rating >= 1.4 && $avg_rating < 1.8 ) $round_rating = "1-5";
if ( $avg_rating >= 1.8 && $avg_rating < 2.3 ) $round_rating = "2";
if ( $avg_rating >= 2.3 && $avg_rating < 2.8 ) $round_rating = "2-5";
if ( $avg_rating >= 2.8 && $avg_rating < 3.3 ) $round_rating = "3";
if ( $avg_rating >= 3.3 && $avg_rating < 3.8 ) $round_rating = "3-5";
if ( $avg_rating >= 3.8 && $avg_rating < 4.3 ) $round_rating = "4";
if ( $avg_rating >= 4.3 && $avg_rating < 4.8 ) $round_rating = "4-5";
if ( $avg_rating >= 4.8) $round_rating = "5";
?>

<div class="ns-single-schema">
	<span class="ns-biz-title" itemprop="name"><?php echo $biz_name ?></span> is rated 
	<span><img src="<?php echo plugins_url( '../imgs/' . $round_rating . '-stars-md.png' , __FILE__ ) ?>" class="ns-avg-rating" /></span> stars over 
	<span><?php echo $review_count ?></span> reviews.
</div>
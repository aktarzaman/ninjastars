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
$meta = get_post_custom();
$biz_name = get_bloginfo( 'name' );
$ns_logo = get_option( 'ninjastars_logo', FALSE );
$ns_logo = ( $ns_logo != FALSE ? "<img src='$ns_logo' class=\"ns-logo\" />" : "" );
$ns_rating = $meta['review_rating_val'][0];
$ns_summary = $meta['review_summary_val'][0];
$ns_author = $meta['review_author_val'][0];
$ns_review = str_replace( '\\', '', $meta['review_content_val'][0] );
$ns_review = nl2br( $ns_review );
?>

<div class="ns-single hreview">
	<div class="ns-left">
		<div class="item">
			<div class="fn">
				<div class="ns-logo value-title" title="<?php echo $biz_name ?>">
					<?php echo $ns_logo ?>
				</div>
			</div>
		</div>
		<div class="ns-rating rating">
			<img src="<?php echo plugins_url( "../imgs/$ns_rating-stars-xs.png", __FILE__ ) ?>" class="value-title" title="<?php echo $ns_rating ?>" alt="Review Stars" />
		</div>
	</div>
	<div class="ns-right">
		<?php echo ( $ns_summary != FALSE ? "<h4><span class=\"summary\">$ns_summary</span></h4>" : '' ) ?>
		<span class="ns-authorname"><span class="reviewer"><?php echo $ns_author ?></span> says:</span>
		<p class="ns-content description"><?php echo $ns_review ?></p>
	</div>
</div>

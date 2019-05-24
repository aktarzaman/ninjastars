<?php
class Ninjastars_Shortcodes {
	private $plugin_name;
	private $version;

	public function __construct($plugin_name, $version) {
		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	public function ninjastars_shortcode_list() {
		$shortcodes = array(
			'ns_reviews',
			'ns_review',
			'ns_widget'
		);

		foreach ( $shortcodes as $shortcode ) :
			add_shortcode( $shortcode, array($this, $shortcode . '_shortcode' ));
		endforeach;
	}


	public function ns_reviews_shortcode ( $atts, $content = null ) {
		extract(shortcode_atts(array(
			'category' => '',
		), $atts));

		global $post;
		# declaring variables
		$biz_name = get_bloginfo('name');
		$ns_color = get_option( 'ninjastars_color', FALSE );
		$ns_logo = get_option( 'ninjastars_logo', FALSE );
		$ns_logo = ( $ns_logo != FALSE ? "<img src='$ns_logo' class=\"ns-logo\" />" : "" );
		$review_count = wp_count_posts('ninjastars');
		$review_count = $review_count->publish;
		$total_rating = 0;
		$best_rating = 0;
		$worst_rating = 5;
		$avg_rating = 0;
		$round_rating = 1;
		$output= '';
		

		# All Reviews array
		$all_args = array(
			'post_type' 		=> 'ninjastars',
			'post_status'		=> 'publish',
			'posts_per_page'	=> -1,
		);
		
		# show all reviews
		if ( empty($category) ) {
			$args = array(
				'post_type' 		=> 'ninjastars',
				'post_status'		=> 'publish',
				'posts_per_page'	=> -1,
				'order'				=> 'DESC',
			);
		# showing only reviews with a specific category slug (comma separated)
		} else {
			$args = array(
				'post_type' 		=> 'ninjastars',
				'post_status'		=> 'publish',
				'posts_per_page'	=> -1,
				'order'				=> 'DESC',
				'tax_query' => array(
					array(
						'taxonomy' => 'ninjastars-category',
						'field' => 'slug',
						'terms' => explode(', ', $category)
					)
				)
			);
		}

		$all_reviews = get_posts( $all_args );

		foreach ( $all_reviews as $single_review ) : 
			setup_postdata( $single_review );
			$meta = get_post_custom( $single_review->ID );
			$rating = $meta['review_rating_val'][0];
			$total_rating += $rating;
			if ( $rating > $best_rating ) $best_rating = $rating;
			if ( $rating < $worst_rating ) $worst_rating = $rating;
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
		
		ob_start();
		?>
		<div id="ns_reviews_page">
			<div id="ns_head">
				<h2 id="ns_biz_title"><?php echo $biz_name ?></h2>
				<h3 id="ns_biz_ratings">
					Rated 
					<img src="<?php echo plugins_url( '/imgs/' . $round_rating . '-stars-md.png' , __FILE__ ) ?>" class="ns-avg-rating" />				
					out of <span><?php echo $review_count ?></span> 
					reviews.
				</h3>
			</div><?php // #ns_head ?>
			<div id="ns_body">
		<?php
		$output .=ob_get_clean();
		$reviews = get_posts( $args );
		foreach ( $reviews as $review ) :
			setup_postdata( $review );
			$meta = get_post_custom( $review->ID );
			$review_summary = ( $meta['review_summary_val'][0] != '' ? $meta['review_summary_val'][0] : FALSE );
			$review_author = str_replace( '\\', '', $meta['review_author_val'][0] );
			$reviewer_title = ( $meta['review_author_title_val'][0] !== '' ? $meta['review_author_title_val'][0] : FALSE );
			$review_rating = $meta['review_rating_val'][0];
			$review_content = str_replace( '\\', '', $meta['review_content_val'][0] );
			$review_content = nl2br( $review_content );
			$terms = get_the_terms($review->ID, 'ninjastars-category');
			$term_class = '';
			if(!empty($terms)) {
				foreach ($terms as $term) {
					$term_class .= ' cat-'.$term->slug;
				}
			}

			ob_start();
			?>
			<a name="r<?php echo $review->ID ?>"></a>
			<div class="ns-review hreview<?php echo !empty($term_class) ? $term_class : ''; ?>">
				<div class="ns-left">
					<div class="item">
						<div class="fn">
							<div class="ns-logo value-title" title="<?php echo $biz_name ?>">
								<?php echo $ns_logo ?>
							</div>
						</div>
					</div>
					<div class="ns-rating rating">
						<img src="<?php echo plugins_url( "/imgs/$review_rating-stars-xs.png", __FILE__ ) ?>" class="value-title" title="<?php echo $review_rating ?>" />
					</div>
				</div>
				<div class="ns-right">
					<?php echo ( $review_summary != FALSE ? "<h4><span class=\"summary\">$review_summary</span></h4>" : '' ) ?>
					<p class="ns-content description"><?php echo $review_content ?></p>
					<span class="ns-authorname"><span class="reviewer"><?php echo $review_author ?></span>
					<?php echo ( $reviewer_title !== FALSE ? "<span class=\"ns-title\">$reviewer_title</span> " : '' ) ?>
					</span>
				</div>
			</div>
			<?php
			$output .= ob_get_clean();
			wp_reset_postdata();
		endforeach;
		
		$output .= "</div></div>";
		return $output;
	} # sc_ns_reviews()


	public function ns_review_shortcode ( $atts, $content = null ) {
		extract(shortcode_atts(array(
			'category' => '',
		), $atts));

		global $post;
		# declaring variables
		$biz_name = get_bloginfo('name');
		$ns_color = get_option( 'ninjastars_color', FALSE );
		$ns_logo = get_option( 'ninjastars_logo', FALSE );
		$ns_logo = ( $ns_logo != FALSE ? "<img src='$ns_logo' class=\"ns-logo\" />" : "" );
		$review_count = wp_count_posts('ninjastars');
		$review_count = $review_count->publish;
		$total_rating = 0;
		$best_rating = 0;
		$worst_rating = 5;
		$avg_rating = 0;
		$round_rating = 1;
		$output= '';
		$ns_readmore = get_option( 'ninjastars_readmore', FALSE );

		# All Reviews array
		$all_args = array(
			'post_type' 		=> 'ninjastars',
			'post_status'		=> 'publish',
			'posts_per_page'	=> -1,
		);
		
		# show all reviews
		if ( empty($category) ) {
			$args = array(
				'post_type' 		=> 'ninjastars',
				'post_status'		=> 'publish',
				'posts_per_page'	=> 1,
				'orderby' 			=> 'rand',
				'order'				=> 'DESC',
			);
		# showing only reviews with a specific category slug (comma separated)
		} else {
			$args = array(
				'post_type' 		=> 'ninjastars',
				'post_status'		=> 'publish',
				'posts_per_page'	=> 1,
				'orderby' 			=> 'rand',
				'order'				=> 'DESC',
				'tax_query' => array(
					array(
						'taxonomy' => 'ninjastars-category',
						'field' => 'slug',
						'terms' => explode(', ', $category)
					)
				)
			);
		}

		$all_reviews = get_posts( $all_args );

		foreach ( $all_reviews as $single_review ) : 
			setup_postdata( $single_review );
			$meta = get_post_custom( $single_review->ID );
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

		$reviews = get_posts( $args );
		$i = 0;
		foreach ( $reviews as $review ) : 
			$i++;
			if($i == 1){
			setup_postdata( $review );
			$meta = get_post_custom( $review->ID );
			$review_summary = ( $meta['review_summary_val'][0] != '' ? $meta['review_summary_val'][0] : FALSE );
			$review_author = str_replace( '\\', '', $meta['review_author_val'][0] );
			$reviewer_title = ( $meta['review_author_title_val'][0] !== '' ? $meta['review_author_title_val'][0] : FALSE );
			$review_rating = $meta['review_rating_val'][0];
			$review_content = str_replace( '\\', '', $meta['review_content_val'][0] );
			$review_content = nl2br( $review_content );
			$terms = get_the_terms($review->ID, 'ninjastars-category');
			$term_class = '';
			if(!empty($terms)) {
				foreach ($terms as $term) {
					$term_class .= ' cat-'.$term->slug;
				}
			}

		ob_start();
		?>
			<div class="ns-single hreview<?php echo !empty($term_class) ? $term_class : ''; ?>">
				<div class="ns-left">
					<div class="item">
						<div class="fn">
							<div class="ns-logo value-title" title="<?php echo $biz_name ?>">
								<?php echo $ns_logo ?>
							</div>
						</div>
					</div>
					<div class="ns-rating rating">
						<img src="<?php echo plugins_url( "/imgs/$review_rating-stars-xs.png", __FILE__ ) ?>" class="value-title" title="<?php echo $review_rating ?>" />
					</div>
				</div>
				<div class="ns-right">
					<?php echo ( $review_summary != FALSE ? "<h4><span class=\"summary\">$review_summary</span></h4>" : '' ) ?>
					<span class="ns-authorname"><span class="reviewer"><?php echo $review_author ?></span> says:</span>
					<p class="ns-content description"><?php echo $review_content ?></p>
				</div>
			</div>
			<div class="ns-single-schema">
				<span class="ns-biz-title" itemprop="name"><?php echo $biz_name ?></span> is rated 
				<span><img src="<?php echo plugins_url( '/imgs/' . $round_rating . '-stars-md.png' , __FILE__ ) ?>" class="ns-avg-rating" /></span> stars over 
				<span><?php echo $review_count ?></span> reviews. 
				<?php echo ( $ns_readmore != FALSE ? "<a href='" . $ns_readmore . "'>Read more reviews</a>" : "" ) ?>
			</div>
		<?php
		$output .= ob_get_clean();
		wp_reset_postdata();
		}
		endforeach;
		
		return $output;

	} // sc_ns_review ()


	public function ns_widget_shortcode ( $atts, $content = null ) {
		extract(shortcode_atts(array(
			'category' => '',
		), $atts));

		global $post;
		# declaring variables
		$biz_name = get_bloginfo('name');
		$ns_color = get_option( 'ninjastars_color', FALSE );
		$ns_logo = get_option( 'ninjastars_logo', FALSE );
		$ns_logo = ( $ns_logo != FALSE ? "<img src='$ns_logo' class=\"ns-logo\" />" : "" );
		$review_count = wp_count_posts('ninjastars');
		$review_count = $review_count->publish;
		$total_rating = 0;
		$best_rating = 0;
		$worst_rating = 5;
		$avg_rating = 0;
		$round_rating = 1;
		$output= '';
		$ns_readmore = get_option( 'ninjastars_readmore', FALSE );

		# All Reviews array
		$all_args = array(
			'post_type' 		=> 'ninjastars',
			'post_status'		=> 'publish',
			'posts_per_page'	=> -1,
		);
		
		# show all reviews
		if ( empty($category) ) {
			$args = array(
				'post_type' 		=> 'ninjastars',
				'post_status'		=> 'publish',
				'posts_per_page'	=> 1,
				'orderby' 			=> 'rand',
				'order'				=> 'DESC',
			);
		# showing only reviews with a specific category slug (comma separated)
		} else {
			$args = array(
				'post_type' 		=> 'ninjastars',
				'post_status'		=> 'publish',
				'posts_per_page'	=> 1,
				'orderby' 			=> 'rand',
				'order'				=> 'DESC',
				'tax_query' => array(
					array(
						'taxonomy' => 'ninjastars-category',
						'field' => 'slug',
						'terms' => explode(', ', $category)
					)
				)
			);
		}

		$all_reviews = get_posts( $all_args );

		foreach ( $all_reviews as $single_review ) : 
			setup_postdata( $single_review );
			$meta = get_post_custom( $single_review->ID );
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

		$reviews = get_posts( $args );
		$i = 0;
		foreach ( $reviews as $review ) : 
			$i++;
			if($i ==1 ) {
			setup_postdata( $review );
			$meta = get_post_custom( $review->ID );
			$review_summary = ( $meta['review_summary_val'][0] != '' ? $meta['review_summary_val'][0] : FALSE );
			$review_author = str_replace( '\\', '', $meta['review_author_val'][0] );
			$reviewer_title = ( $meta['review_author_title_val'][0] !== '' ? $meta['review_author_title_val'][0] : FALSE );
			$review_rating = $meta['review_rating_val'][0];
			$review_content = str_replace( '\\', '', $meta['review_content_val'][0] );
			$review_content = nl2br( $review_content );
			$terms = get_the_terms($review->ID, 'ninjastars-category');
			$term_class = '';
			if(!empty($terms)) {
				foreach ($terms as $term) {
					$term_class .= ' cat-'.$term->slug;
				}
			}
		ob_start();
		?>
		<div class="ns-widget hreview<?php echo !empty($term_class) ? $term_class : ''; ?>">
				<div class="ns-widget-review">					
					<span class="ns-widget-authorname"><span class="reviewer"><?php echo $review_author ?></span> says:</span>
					<p class="ns-widget-content description">
					<?php echo ( $review_summary != FALSE ? "<b><span class=\"summary\">$review_summary</span></b>" : '' ) ?>	
					<?php if ( strlen( $review_content ) > 200 ) : ?>
						"<?php echo substr( $review_content, 0, 200 ) ?>..." <a href="<?php echo $ns_readmore?>#r<?php echo $review->ID ?>">read more</a>.
					<?php else : ?>
						"<?php echo $review_content ?>"
					<?php endif ?>
					</p>
				</div>
			</div>
			<div class="ns-widget-info">
				<div class="item">
					<div class="fn">
						<div class="ns-logo value-title" title="<?php echo $biz_name ?>">
							<?php echo $ns_logo ?>
						</div>
					</div>
				</div>
				<div class="ns-rating rating">
					<img src="<?php echo plugins_url( "/imgs/$review_rating-stars-xs.png", __FILE__ ) ?>" class="value-title" title="<?php echo $review_rating ?>" />
				</div>
			</div>
			<div class="ns-single-schema">
				<span class="ns-biz-title" itemprop="name"><?php echo $biz_name ?></span> is rated 
				<span><img src="<?php echo plugins_url( '/imgs/' . $round_rating . '-stars-md.png' , __FILE__ ) ?>" class="ns-avg-rating" /></span> stars over 
				<span><?php echo $review_count ?></span> reviews. 
				<?php echo ( $ns_readmore != FALSE ? "<a href='" . $ns_readmore . "'>Read more reviews</a>" : "" ) ?>
			</div>
		<?php
		$output .= ob_get_clean();
		wp_reset_postdata();
		}
		endforeach;
		
		return $output;

	} // sc_ns_widget ()

}
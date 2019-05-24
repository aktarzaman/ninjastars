<?php
class NinjaStars_Widget extends WP_Widget {
	public function __construct() {

		// Initializing the basic parameters
		$widget_ops = array(
			'classname' 	=> esc_attr( 'ninjastars-widget' ),
			'description' 	=> esc_html__( 'Displays a random review', 'ninjastars' ),
		);

		parent::__construct( 'ninjastars_widget', esc_html__( 'NinjaStars Review', 'ninjastars' ), $widget_ops );
	}

	/**
	 * 
	 * 
	 * Creating the inputs and variable at the backend
	 * @param $instance Widget options
	 */

	public function form( $instance ) {

		// Assigning or updating the values
		$title = ( ! empty( $instance[ 'title' ] ) ? $instance[ 'title' ] : '' ); ?>

		<p>
		<label for="<?php echo esc_attr($this->get_field_id( 'title' )); ?>"><?php echo esc_html( 'Title:'); ?></label>
		<input class="widefat" id="<?php echo esc_attr($this->get_field_id( 'title' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'title' )); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<?php
	}


	// Updating the widget value
	public function update( $new_instance, $old_instance ) {
		
		$instance = $old_instance;
		// Updating to the latest values
		$instance[ 'title' ] 				= ( ! empty( $new_instance[ 'title' ] ) ? strip_tags( $new_instance[ 'title' ] ) : '' );
		return $instance;
	}

	/**
	 * 
	 * Displays the form on a widgetized location, such as a footer or sidebar.
	 *	Front end
	 * @param array $args Theme's interaction with the widget
	 * @param array $instance Widget options
	 */

	public function widget( $args, $instance ) {
		$title = apply_filters( 'widget_title', $instance['title'] );
		
		printf( '%s', $args[ 'before_widget' ] ); 
		if( ! empty( $instance[ 'title' ] ) ) {			
			printf( '%s' . $title. '%s', $args[ 'before_title' ], $args[ 'after_title' ]);			
		}

		$biz_name = get_bloginfo('name');
		$review_count = wp_count_posts('ninjastars');
		$review_count = $review_count->publish;
		$total_rating = 0;
		$best_rating = 0;
		$worst_rating = 5;
		$ns_readmore = ( 
			get_option('ninjastars_readmore', FALSE ) != FALSE 
			? '<a href=\'' . get_option('ninjastars_readmore') . '\'>(Read More)</a>'
			: '' 
		);

		$all_args = array(
			'post_type' 		=> 'ninjastars',
			'post_status'		=> 'publish',
			'posts_per_page'	=> -1,
			'order'				=> 'DESC'
		);

		$all_reviews = get_posts( $all_args );

		foreach ( $all_reviews as $review ) : 
			setup_postdata( $review );
			$meta = get_post_custom( $review->ID );
			$rating = $meta['review_rating_val'][0];
			$total_rating += $rating;
			if ( $rating > $best_rating ) $best_rating = $rating;
			if ( $rating < $worst_rating ) $worst_rating = $rating;
		endforeach;
		
		$avg_rating = ( $total_rating / $review_count );
		wp_reset_postdata();
		
		$rand_args = array(
			'post_type' => 'ninjastars', 
			'orderby' => 'rand',
			'order' => 'DESC', 
			'posts_per_page' => 1
		);

		$reviews = get_posts( $rand_args );


		$i = 0;

		foreach ( $reviews as $review ) :
			$i++;
			if( $i ==1 ) {
			setup_postdata( $review );
			$meta = get_post_custom( $review->ID );
			$review_summary = ( $meta['review_summary_val'][0] != '' ? $meta['review_summary_val'][0] : FALSE );
			$review_author = str_replace( '\\', '', $meta['review_author_val'][0] );
			$reviewer_title = ( $meta['review_author_title_val'][0] !== '' ? $meta['review_author_title_val'][0] : FALSE );
			$review_rating = $meta['review_rating_val'][0];
			$review_content = str_replace( '\\', '', $meta['review_content_val'][0] );
			$review_content = nl2br( $review_content );

			$ns_review_sub = ( 
				strlen( $review_content ) > 250 
				? substr( $review_content, 0, 250 ) . '... ' . $ns_readmore 
				: $review_content 
			); ?>

			<div class='ns-widget-review hreview'>
				<div class='ns-widget-content'>
					<p class='description'><?php echo $ns_review_sub; ?></p>
				</div>
				<div class='ns-widget-footer'>
					<div class='ns-widget-rating'>
						<img src="<?php echo plugins_url( "/imgs/$review_rating-stars-xs.png", __FILE__ ) ?>" class='value-title' title="<?php echo $review_rating; ?>" alt="Star ratings" />
					</div>
					<span class='ns-widget-author'>Written by <span class='reviewer'><?php echo $review_author; ?></span></span>
				</div>
			</div> <!-- end of ns-widget-review -->

			<div class='ns-widget-schema'>
				<span class='ns-biz-title' itemprop='name'><?php echo $biz_name; ?></span> is rated 
				<span itemprop='ratingValue'><?php echo round($avg_rating, 2); ?></span> stars over 
				<span itemprop='reviewCount'><?php echo $review_count; ?></span> reviews.
			</div> <!-- end of ns-widget-schema -->

			<span itemprop='aggregateRating' itemscope='' itemtype='http://schema.org/AggregateRating'>
					<meta itemprop='ratingValue' content='<?php echo round($avg_rating, 2); ?>'>
					<meta itemprop='bestRating' content='<?php echo $best_rating; ?>'>
					<meta itemprop='worstRating' content='<?php echo $worst_rating; ?>'>
			</span> <!-- end of aggregateRating -->

			<?php 
			wp_reset_postdata();
		}
		endforeach;
		
		printf( '%s', $args[ 'after_widget' ] );
	}
	

	public function ninjastars_load_widget() {
	    register_widget( 'ninjastars_widget' );
	}

}
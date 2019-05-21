<?php 
/*
* Plugin Name: NinjaStars
* Description: hReview 0.4 microformat plugin jam-packed with Star Power.  
* Author: The 108 Group, LLC
* Author URI: http://socialmedianinjas.com
* Version: 1.6
*/


/* 
	UPDATE NOTES

-	v 1.6 (16th Nov, 2018)
	Fixed the layout issue when placing contents above or bottom [ns_revies] shortcode

-	v 1.5 (24th Sep, 2018)
	If site URL has the string 'ninjastars', then it will be redirected to Home Page

-   v 1.4
	added "Show Review Publish date" on settings page which will show the review publish date at the right side of the review box (20th Mar 2018)

	v1.3 
-	added review categories	

	v1.1 
- Removed Schema markup for "rated X out of Y reviews"
- Removed Ninja Stars widget (was causing template errors)

TO DO LIST 
-----------
• Default order options (asc vs desc)
• "Enable Random Review Order?" option
• REVIEWER LOCATION
• REVIEWER WEBSITE
• REVIEWER BUSINESS/COMPANY
• WIDGET CONTENT BG STYLE
• WIDGET FOOTER BG STYLE
	
*/


class NinjaStars {


	function __construct () {
		# Initializes the custom post type 'ninjastars'
		add_action( 'init', array( $this, 'add_post_type' ) );
		# Disables the editor inside review edit page
		add_action( 'admin_head', array( $this, 'disable_editor' ) );
		# Initializes and displays "Settings" underneath "NS Reviews"
		add_action( 'admin_menu', array( $this, 'ns_init_options_page' ) );
		add_action( 'admin_init', array( $this, 'ns_init_settings_fields' ) );
		# Adds custom meta boxes for Review Summary, Review Rating, and Review Content
		add_action( 'add_meta_boxes', array( $this, 'add_review_meta' ) );
		# Initializes the custom post type post list display
		add_filter( 'manage_edit-ninjastars_columns' , array( $this, 'postlist_init_custom_cols' ) );
		# Filters through columns in the custom post type post list and inputs meta data
		add_action( 'manage_ninjastars_posts_custom_column' , array( $this, 'postlist_add_custom_cols' ), 10, 2 );
		# Saves user-defined review meta
		add_action( 'save_post', array( $this, 'save_review' ) );
		# [ns_reviews] - Prints all reviews in schema format
		add_shortcode( 'ns_reviews', array( $this, 'sc_ns_reviews' ) );
		# [ns_review] -- Prints random review
		add_shortcode( 'ns_review', array( $this, 'sc_ns_review' ) );
		# [ns_widget] -- for Sidebar & Footer
		add_shortcode( 'ns_widget', array( $this, 'sc_ns_widget' ) );
		# Sets custom color theme for plugin
		add_action( 'wp_head', array( $this, 'ns_insert_styles') );
		# Enables/disables certain styles on admin-only NinjaStars pages
		add_action( 'admin_head', array( $this, 'ns_admin_styles' ) );
		# Redirect to home page if URL contains string 'ninjastars'
		add_action( 'template_redirect', array( $this, 'ninjastars_in_permalink' ) );
	} # __construct ()


 
 	function add_post_type () {
		register_post_type( 'ninjastars',
			array(
				'labels' 			=> array(
					'name' 					=> 'NS Reviews',
					'singular_name' 		=> 'Review',
					'add_new'				=> 'Add New Review',
					'add_new_item'			=> 'Add New Review',
					'edit_item'				=> 'Edit Review',
					'view_item'				=> 'View Review',
					'search_item'			=> 'Search Reviews',
					'not_found'				=> 'No Reviews Found',
					'not_found_in_trash'	=> 'No Reviews Found in Trash'
				),
				'menu_icon'				=> 'dashicons-star-filled',
				'public' 				=> true,
				'has_archive' 			=> true,
				'supports'				=> array('title','thumbnail', 'page-attributes'),
				'description'			=> 'These projects appear as an organized list in whatever page you designate.',
				'show_in_nav_menus'		=> false,
				'show_in_menu'			=> true,
				'menu_position'			=> 40,
				'exclude_from_search'	=> true,
				'show_ui'				=> true,
				'taxonomies'          	=> array( 'category' ),
			)
		);
 	} // add_post_type ()



 	function ns_init_options_page () {
 		add_submenu_page( 
			# Parent Slug
 			'edit.php?post_type=ninjastars',
 			# Page Title
 			'NinjaStars Options',
 			# Menu Title
 			'Settings',
 			# Capability
 			'manage_options',
 			# Settings Menu Slug	
 			'ninjastars-options',
 			# Function Callback
 			array( $this, 'ns_settings_page_cb' )
 		);
 	} # ns_init_options_page ()



 	# outputs plugin options page
 	function ns_settings_page_cb () {
		echo '<div id="wrap">';
		$current_screen = get_current_screen();
		# Verify that data is being saved ONLY from the NinjaStars options page.
		if ( $current_screen->id == "ninjastars_page_ninjastars-options" && isset( $_POST['ns_submit_opts'] ) ) :
			if ( isset( $_POST['ninjastars_color'] ) ) : 
				update_option( 'ninjastars_color', sanitize_text_field( $_POST['ninjastars_color'] ) );
			endif;
			if ( isset( $_POST['ninjastars_logo'] ) ) :
				update_option( 'ninjastars_logo', sanitize_text_field( $_POST['ninjastars_logo'] ) );
			endif;
			if ( isset( $_POST['ninjastars_rcolor'] ) ) :
				update_option( 'ninjastars_rcolor', sanitize_text_field( $_POST['ninjastars_rcolor'] ) );
			endif;
			if ( isset( $_POST['ninjastars_readmore'] ) ) :
				update_option( 'ninjastars_readmore', sanitize_text_field( $_POST['ninjastars_readmore'] ) );
			endif;
			if ( isset( $_POST['ninjastars_fcolor'] ) ) :
				update_option( 'ninjastars_fcolor', sanitize_text_field( $_POST['ninjastars_fcolor'] ) );
			endif;
		endif;
		echo '<form method="POST" action="">';
		settings_fields( 'ninjastars_settings_group' );   
        do_settings_sections( 'ninjastars' );
        submit_button( 'Save NinjaStars Options', 'primary', 'ns_submit_opts' );
        echo '</form></div>'; 	
 	} # ns_settings_page_cb ()



 	function ns_init_settings_fields () {
 		register_setting(
 			'ninjastars_settings_group',
 			'ninjastars_settings'
 		);
 		add_settings_section(
 			'ninjastars_settings_group',
 			'<b>NinjaStars Settings</b>',
 			array( $this, 'ns_settings_section_cb' ),
 			'ninjastars'
 		);
 		add_settings_field(
 			'ninjastars_color',
 			'<b>Primary Theme Color</b>',
 			array( $this, 'ns_settings_color_cb' ),
 			'ninjastars',
 			'ninjastars_settings_group'
 		);
  		add_settings_field(
 			'ninjastars_rcolor',
 			'<b>Review Content BG Color</b>',
 			array( $this, 'ns_settings_rcolor_cb' ),
 			'ninjastars',
 			'ninjastars_settings_group'
 		);
  		add_settings_field(
 			'ninjastars_fcolor',
 			'<b>Widget Author Name Text Color</b>',
 			array( $this, 'ns_settings_fcolor_cb' ),
 			'ninjastars',
 			'ninjastars_settings_group'
 		);
 		add_settings_field(
 			'ninjastars_logo',
 			'<b>Logo Upload URL</b>',
 			array( $this, 'ns_settings_logo_cb' ),
 			'ninjastars',
 			'ninjastars_settings_group'
 		);
 		add_settings_field(
 			'ninjastars_readmore',
 			'<b>Reviews Link</b>',
 			array( $this, 'ns_settings_readmore_cb'),
 			'ninjastars',
 			'ninjastars_settings_group'
 		);
 	} # ns_init_settings_fields ()

 

 	function ns_settings_section_cb () {
 		?>
 			<p>Insert your reviews into your desired page by using the shortcode <code>[ns_reviews]</code></p>
 			<p>Insert a random single review into a page or post by using the shortcode <code>[ns_review]</code>. You can also use the NinjaStar Review widget for this as well.</p>
 		<?
 	} # ns_settings_section_cb ()



 	function ns_settings_color_cb () {
 		$opt = get_option( 'ninjastars_color', '' );
 		ob_start();
 		?>
 		<input type="text" name="ninjastars_color" placeholder="Example: #010800" value="<?= $opt ?>" />
 		<p class="description">
 			What is the primary color of your theme?<br/>
 			Default: '#cccccc' (light-grey)
 		</p>
 		<?
 		echo ob_get_clean();
 	} // ns_settings_color_cb ()



  	function ns_settings_rcolor_cb () {

 		$opt = get_option( 'ninjastars_rcolor', '' );
 		ob_start();
 		?>
		<input type="text" name="ninjastars_rcolor" placeholder="Example: #eeeeee" value="<?= $opt ?>"  />
		<p class="description">
			What is the background color of the review content?<br/>
			Leave blank for transparent. Default: transparent.
		</p>
		<?
 		echo ob_get_clean();

 	} // ns_settings_rcolor_cb ()



   	function ns_settings_fcolor_cb () {

 		$opt = get_option( 'ninjastars_fcolor', '' );
 		ob_start();
 		?>
 		<input type="text" name="ninjastars_fcolor" placeholder="Example: #eeeeee" value="<?= $opt ?>" />
 		<p class="description">
 			What color is the author name on the widget?<br/>
 			This color must contrast well against the Primary Color as a background.<br/>
 			Default: '#ffffff' (off-white)
 		</p>
 		<?
 		echo ob_get_clean();

 	} // ns_settings_fcolor_cb ()



 	function ns_settings_logo_cb () {

 		$opt = get_option( 'ninjastars_logo', '' );
 		ob_start();
 		?>
 		<input type="text" name="ninjastars_logo" placeholder="Insert logo URL here" value="<?= $opt ?>" />
 		<p class="description">
 			Copy/paste image you'd like to appear next to each review. Typically brand logo. <br/>
 			Image resolution should not exceed 75x85 px.
 		</p>

 		<?
 		echo ob_get_clean();

 	} // ns_settings_logo_cb ()



 	function ns_settings_readmore_cb () {

 		$opt = get_option( 'ninjastars_readmore', '' );
 		ob_start();
 		?>
 		<input type="text" name="ninjastars_readmore" placeholder="Link to Reviews Page" value="<?= $opt ?>" />
 		<p class="description">
 			What page is your reviews shortcode going to be on? <br/>
			Example: http://yourdomain.com/read-reviews/
		</p>
 		<?
 		echo ob_get_clean();

 	} // ns_settings_readmore_cb ()



 	// Disables the Visual/Text editor within the NinjaStars post type
	function disable_editor () {
		if ( get_post_type() == 'ninjastars' ) :
			remove_post_type_support( 'ninjastars', 'editor' );
			remove_post_type_support( 'ninjastars', 'title' );
		endif;
	} // disable_editor ()



	function add_review_meta () {
		add_meta_box(
			'ninjastars_author',
			'Reviewer / Author',
			array( $this, 'review_author_meta' ),
			'ninjastars',
			'normal',
			'high'			
		);
		add_meta_box(
			'ninjastars_author_title',
			'Reviewer Title',
			array( $this, 'review_author_title_meta' ),
			'ninjastars',
			'normal',
			'high'			
		);
		add_meta_box( 
			'ninjastars_summary',
			'Review Summary',
			array( $this, 'review_summary_meta' ),
			'ninjastars',
			'normal',
			'high'
		);		
		add_meta_box(
			'ninjastars_rating',
			'Rating Value',
			array( $this, 'review_rating_meta' ),
			'ninjastars',
			'normal',
			'high'
			// no callback
		);
		add_meta_box(
			'ninjastars_review',
			'Review Content',
			array( $this, 'review_content_meta' ),
			'ninjastars',
			'normal',
			'high'
			// callback
		);
	} // add_review_meta ()



 	function review_author_meta ( $post ) {
		$values = get_post_custom( $post->ID );
		$val = isset( $values['review_author_val'] ) ? esc_attr( $values['review_author_val'][0] ) : '';
		wp_nonce_field( 'my_meta_box_nonce', 'meta_box_nonce' );
		?>
			<style> #post-body-content { display: none; } </style>
			<p class="description">What is the name of the review author?</p>
			<input type="text" name="review_author_val" class="widefat" style="width:100%;" value="<?= $val ?>" placeholder="Enter Author Name" />
		<?
		echo ob_get_clean();
	} // review_author_meta ( $post )



 	function review_author_title_meta ( $post ) {
		$values = get_post_custom( $post->ID );
		$val = isset( $values['review_author_title_val'] ) ? esc_attr( $values['review_author_title_val'][0] ) : '';
		wp_nonce_field( 'my_meta_box_nonce', 'meta_box_nonce' );
		?>
			<p class="description">What is the title of the review author? (Optional.)</p>
			<input type="text" name="review_author_title_val" class="widefat" style="width:100%;" value="<?= $val ?>" placeholder="Enter Author Title" />
		<?
		echo ob_get_clean();
	} // review_author_title_meta ( $post )



	function review_summary_meta ( $post ) {
		$values = get_post_custom( $post->ID );
		$val = isset( $values['review_summary_val'] ) ? esc_attr( $values['review_summary_val'][0] ) : '';
		wp_nonce_field( 'my_meta_box_nonce', 'meta_box_nonce' );
		?>
			<p class="description">Enter a short sentence describing the reviewer's experience.</p>
			<input type="text" name="review_summary_val" class="widefat" style="width:100%;" value="<?= $val ?>" placeholder="Enter Review Summary" />
		<?		
		echo ob_get_clean();
	} // review_summary_meta ( $post )



	function review_rating_meta ( $post ) {
		$values = get_post_custom( $post->ID );
		$val = isset( $values['review_rating_val'] ) ? esc_attr( $values['review_rating_val'][0] ) : '';
		wp_nonce_field( 'my_meta_box_nonce', 'meta_box_nonce' );
		ob_start();
		?>
			<p class="description">Select the appropriate star-rating to reflect on the review.</p>
			<label for="stars5"><img src="<?= plugins_url( '/imgs/5-stars-xs.png', __FILE__ ) ?>" /></label>
			<input type="radio" name="review_rating_val" id="stars5" value="5" <?= ( $val == '' || $val == '5' ? 'checked' : '' ) ?> />
			<label for="stars4"><img src="<?= plugins_url( '/imgs/4-stars-xs.png', __FILE__ ) ?>" /></label>
			<input type="radio" name="review_rating_val" id="stars4" value="4" <?= ( $val == '4' ? 'checked' : '' ) ?> />
			<label for="stars3"><img src="<?= plugins_url( '/imgs/3-stars-xs.png', __FILE__ ) ?>" /></label>
			<input type="radio" name="review_rating_val" id="stars3" value="3" <?= ( $val == '3' ? 'checked' : '' ) ?> />
			<label for="stars2"><img src="<?= plugins_url( '/imgs/2-stars-xs.png', __FILE__ ) ?>" /></label>
			<input type="radio" name="review_rating_val" id="stars2" value="2" <?= ( $val == '2' ? 'checked' : '' ) ?> />
			<label for="stars1"><img src="<?= plugins_url( '/imgs/1-stars-xs.png', __FILE__ ) ?>" /></label>
			<input type="radio" name="review_rating_val" id="stars1" value="1" <?= ( $val == '1' ? 'checked' : '' ) ?> />
			<label for="stars0"><img src="<?= plugins_url( '/imgs/0-stars-xs.png', __FILE__ ) ?>" /></label>
			<input type="radio" name="review_rating_val" id="stars0" value="0" <?= ( $val == '0' ? 'checked' : '' ) ?> />
		<?
		$output = ob_get_clean();
		echo $output;

	} // review_rating_meta ( $post )



	function review_content_meta( $post ) {
		$values = get_post_custom( $post->ID );
		wp_nonce_field( 'my_meta_box_nonce', 'meta_box_nonce' );
		$val = isset( $values['review_content_val'] ) ? $values['review_content_val'][0] : '';
		$val = str_replace( '\\', '', $val );
		ob_start();
		?>
		 	<p class="description">What does the reviewer have to say about their experience?</p>
			<textarea name="review_content_val" class="widefat" style="width:100%;min-height:200px;"><?= $val ?></textarea>
		<?
		$output = ob_get_clean();
		echo $output;
	}

	

	function save_review ( $post_id ) {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
		if ( !isset( $_POST['meta_box_nonce'] ) || !wp_verify_nonce( $_POST['meta_box_nonce'], 'my_meta_box_nonce' ) ) return;
		if ( !current_user_can( 'edit_post' ) ) return;
		if ( isset( $_POST['review_author_val'] ) )
			update_post_meta( $post_id, 'review_author_val',  $_POST['review_author_val'] );
		if ( isset( $_POST['review_author_title_val'] ) )
			update_post_meta( $post_id, 'review_author_title_val',  $_POST['review_author_title_val'] );
		if ( isset( $_POST['review_summary_val'] ) )
			update_post_meta( $post_id, 'review_summary_val',  $_POST['review_summary_val'] );
		if ( isset( $_POST['review_rating_val'] ) )
			update_post_meta( $post_id, 'review_rating_val', $_POST['review_rating_val'] );
		if ( isset( $_POST['review_content_val'] ) )
			update_post_meta( $post_id, 'review_content_val',  $_POST['review_content_val'] );
	} // save_review ( $post_id )



	function postlist_init_custom_cols ( $columns ) {
    	unset( $columns['title'] );
    	unset( $columns['author'] );
    	unset( $columns['date'] );
		return array_merge(
			$columns,
			array(
				'review_author' => 'Author',
				'review_rating' => 'Rating',
				'review_summary' => 'Summary',
				'date' => 'Review Posted'
			)
			//$columns
		);

	} // postlist_init_custom_cols ( $columns )



	function postlist_add_custom_cols ( $cols, $post_id ) {
		$meta = get_post_custom( $post_id );
		switch ( $cols ) :
			case 'review_author' :
				$author = "<a href=\"post.php?post=" . $post_id . "&action=edit\">" . $meta['review_author_val'][0] . "</a>";
				echo $author;
				break;
			case 'review_rating' :
				$rating = $meta['review_rating_val'][0];
				echo "<img src=\"" . plugins_url( "/imgs/$rating-stars-xs.png", __FILE__ ) . "\" />";
				break;
			case 'review_summary' :
				$summary = $meta['review_summary_val'][0];
				echo $summary;
				break;
		endswitch;
	} # postlist_add_custom_cols ( $cols, $post_id )



	function sc_ns_reviews ( $atts ) {
		$atts = shortcode_atts( array(
			'category' => FALSE,
		), $atts );
		global $post; 
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
			//setup_postdata( $review );
			$meta = get_post_custom( $review->ID );
			$rating = $meta['review_rating_val'][0];
			$total_rating += $rating;
			if ( $rating > $best_rating ) $best_rating = $rating;
			if ( $rating < $worst_rating ) $worst_rating = $rating;
		endforeach;
		$avg_rating = ( $total_rating / $review_count );
		#echo 'AVG rating ='. $avg_rating;
		$biz_name = get_bloginfo('name');
		if ( $avg_rating < 1.4 ) $round_rating = 1;
		if ( $avg_rating >= 1.4 && $avg_rating < 1.8 ) $round_rating = "1-5";
		if ( $avg_rating >= 1.8 && $avg_rating < 2.3 ) $round_rating = "2";
		if ( $avg_rating >= 2.3 && $avg_rating < 2.8 ) $round_rating = "2-5";
		if ( $avg_rating >= 2.8 && $avg_rating < 3.3 ) $round_rating = "3";
		if ( $avg_rating >= 3.3 && $avg_rating < 3.8 ) $round_rating = "3-5";
		if ( $avg_rating >= 3.8 && $avg_rating < 4.3 ) $round_rating = "4";
		if ( $avg_rating >= 4.3 && $avg_rating < 4.8 ) $round_rating = "4-5";
		if ( $avg_rating >= 4.8 && $avg_rating <= 5 ) $round_rating = "5";
		$output= '';
		ob_start();
		?>
		<div id="ns_reviews_page">
			<div id="ns_head">
				<h2 id="ns_biz_title"><?= $biz_name ?></h2>
				<h3 id="ns_biz_ratings">
					Rated 
					<img src="<?= plugins_url( '/imgs/' . $round_rating . '-stars-md.png' , __FILE__ ) ?>" class="ns-avg-rating" />					
					out of <span><?= $review_count ?></span> 
					reviews.
				</h3>
			</div><? // #ns_head ?>
			<div id="ns_body">
		<?
		$output .=ob_get_clean();
		foreach ( $reviews as $review ) :
			$post = $review;
			setup_postdata( $post );
			$meta = get_post_custom( $review->ID );
			$review_summary = ( $meta['review_summary_val'][0] != '' ? $meta['review_summary_val'][0] : FALSE );
			$review_author = str_replace( '\\', '', $meta['review_author_val'][0] );
			$reviewer_title = ( $meta['review_author_title_val'][0] !== '' ? $meta['review_author_title_val'][0] : FALSE );
			$review_rating = $meta['review_rating_val'][0];
			$review_content = str_replace( '\\', '', $meta['review_content_val'][0] );
			$review_content = nl2br( $review_content );
			$review_id = get_the_ID();
			$ns_color = get_option( 'ninjastars_color', FALSE );
			$ns_logo = get_option( 'ninjastars_logo', FALSE );
			$ns_logo = ( $ns_logo != FALSE ? "<img src='$ns_logo' class=\"ns-logo\" />" : "" );
			$print_review = FALSE;
			if ( !empty( $atts['category'] ) ) :
				$cats = get_the_category();
				foreach ( $cats as $cat => $value ) :
					if ( $value->slug == $atts['category'] ) :
						$print_review = TRUE;
					endif;
				endforeach;
			else : 
				$print_review = TRUE;
			endif;
			if ( $print_review == TRUE ) :
			ob_start();
			?>
			<a name="r<?= $review_id ?>"></a>
			<div class="ns-review hreview<?= @$atts['category'] ? ' cat-' . $atts['category'] : '' ?>">
				<div class="ns-left">
					<div class="item">
						<div class="fn">
							<div class="ns-logo value-title" title="<?= $biz_name ?>">
								<?= $ns_logo ?>
							</div>
						</div>
					</div>
					<div class="ns-rating rating">
						<img src="<?= plugins_url( "/imgs/$review_rating-stars-xs.png", __FILE__ ) ?>" class="value-title" title="<?= $review_rating ?>" />
					</div>
				</div>
				<div class="ns-right">
					<?= ( $review_summary != FALSE ? "<h4><span class=\"summary\">$review_summary</span></h4>" : '' ) ?>
					<p class="ns-content description"><?= $review_content ?></p>
					<span class="ns-authorname"><span class="reviewer"><?= $review_author ?></span>
					<?= ( $reviewer_title !== FALSE ? "<span class=\"ns-title\">$reviewer_title</span> " : '' ) ?>
					</span>
				</div>
			</div>
			<?
			$output .= ob_get_clean();
			endif;
		endforeach;
		wp_reset_postdata();
		$output .= "</div></div>";
		return $output;
	} # sc_ns_reviews()



	function ns_insert_styles () {
		global $post;
		$theme_color = get_option( 'ninjastars_color', '#CCCCCC' );
		$bg_color = get_option( 'ninjastars_rcolor', 'transparent' );
		$footer_color = get_option( 'ninjastars_fcolor', '#FFFFFF' );
		ob_start();
		?>
		<link href="<?= plugins_url( '/ninjastars.css', __FILE__ ) ?>" type="text/css" rel="stylesheet">
		<!-- NinjaStars Custom Styles -->
		<style type="text/css"> 
			.ns-left, 
			.ns-widget-footer,
			.ns-widget-info { 
				background-color: <?= $theme_color ?>; 
			} 
			.ns-right, 
			.ns-widget-content { 
				background-color: <?= $bg_color ?>; 
			}
			.ns-widget-author { 
				color: <?= $footer_color ?>; 
			}
		</style>
		<?
		$output .= ob_get_clean();
		echo $output;

	} # ns_insert_styles()



	function sc_ns_review ( $atts ) {
		$atts = shortcode_atts( array(
			'category' => FALSE,
		), $atts );

		$review_count = wp_count_posts('ninjastars');
		$review_count = $review_count->publish;
		# showing ALL reviews
		if ( $atts['category'] !== FALSE ) {
			$args = array(
				'post_type' 		=> 'ninjastars',
				'post_status'		=> 'publish',
				'posts_per_page'	=> -1,
				'order'				=> 'DESC',
			);
		# showing only reviews with a specific category
		} else {
			$args = array(
				'post_type' 		=> 'ninjastars',
				'post_status'		=> 'publish',
				'posts_per_page'	=> -1,
				'order'				=> 'DESC',
				'category_name'		=> $atts['category'],
			);
		}
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
		$biz_name = get_bloginfo( 'name' );
		$ns_readmore = get_option( 'ninjastars_readmore', FALSE );
		$args = array(
			'post_type' => 'ninjastars', 
			'orderby' => 'rand',
			'order' => 'DESC', 
			'posts_per_page' => 1
		);
		$review = new WP_Query( $args );
		while ( $review->have_posts() ) : 
			$review->the_post();
			$meta = get_post_custom( $review->ID );
			$ns_summary = $meta['review_summary_val'][0];
			$ns_rating = $meta['review_rating_val'][0];
			$ns_author = $meta['review_author_val'][0];
			$ns_review = str_replace( '\\', '', $meta['review_content_val'][0] );
			$ns_review = nl2br( $ns_review );
			$ns_logo = get_option( 'ninjastars_logo', FALSE );
			$ns_logo = ( $ns_logo != FALSE ? "<img src='$ns_logo' class=\"ns-logo\" />" : "" );
		endwhile;
		wp_reset_postdata();
		ob_start();
		?>
		<div class="ns-single hreview">
				<div class="ns-left">
					<div class="item">
						<div class="fn">
							<div class="ns-logo value-title" title="<?= $biz_name ?>">
								<?= $ns_logo ?>
							</div>
						</div>
					</div>
					<div class="ns-rating rating">
						<img src="<?= plugins_url( "/imgs/$ns_rating-stars-xs.png", __FILE__ ) ?>" class="value-title" title="<?= $ns_rating ?>" />
					</div>
				</div>
				<div class="ns-right">
					<?= ( $ns_summary != FALSE ? "<h4><span class=\"summary\">$ns_summary</span></h4>" : '' ) ?>
					<span class="ns-authorname"><span class="reviewer"><?= $ns_author ?></span> says:</span>
					<p class="ns-content description"><?= $ns_review ?></p>
				</div>
			</div>
			<div class="ns-single-schema">
				<span class="ns-biz-title" itemprop="name"><?= $biz_name ?></span> is rated 
				<span><?= $avg_rating ?></span> stars over 
				<span><?= $review_count ?></span> reviews. 
				<?= ( $ns_readmore != FALSE ? "<a href='" . $ns_readmore . "'>Read more reviews</a>" : "" ) ?>
			</div>
		<?
		$output = ob_get_clean();
		return $output;

	} // sc_ns_review ()


	function sc_ns_widget () {

		$review_count = wp_count_posts('ninjastars');
		$review_count = $review_count->publish;
		$args = array(
			'post_type' 		=> 'ninjastars',
			'post_status'		=> 'publish',
			'posts_per_page'	=> -1,
			'order'				=> 'DESC'
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
		$biz_name = get_bloginfo( 'name' );
		$ns_readmore = get_option( 'ninjastars_readmore', FALSE );
		$args = array(
			'post_type' => 'ninjastars', 
			'orderby' => 'rand',
			'order' => 'DESC', 
			'posts_per_page' => 1
		);
		$review = new WP_Query( $args );
		while ( $review->have_posts() ) : 
			$review->the_post();
			$meta = get_post_custom( $review->ID );
			$ns_summary = $meta['review_summary_val'][0];
			$ns_rating = $meta['review_rating_val'][0];
			$ns_author = $meta['review_author_val'][0];
			$review_id = $review->ID;
			$ns_review = str_replace( '\\', '', $meta['review_content_val'][0] );
			$ns_review = nl2br( $ns_review );
			$ns_logo = get_option( 'ninjastars_logo', FALSE );
			$ns_logo = ( $ns_logo != FALSE ? "<img src='$ns_logo' class=\"ns-logo\" />" : "" );
		endwhile;
		wp_reset_postdata();
		ob_start();
		?>
		<div class="ns-widget hreview">
				<div class="ns-widget-review">					
					<span class="ns-widget-authorname"><span class="reviewer"><?= $ns_author ?></span> says:</span>
					<p class="ns-widget-content description">
					<?= ( $ns_summary != FALSE ? "<b><span class=\"summary\">$ns_summary</span></b>" : '' ) ?>	
					<? if ( strlen( $ns_review ) > 200 ) : ?>
						"<?= substr( $ns_review, 0, 200 ) ?>..." <a href="r<?= $review_id ?>">read more</a>.
					<? else : ?>
						"<?= $ns_review ?>"
					<? endif ?>
					</p>
				</div>
			</div>
			<div class="ns-widget-info">
				<div class="item">
					<div class="fn">
						<div class="ns-logo value-title" title="<?= $biz_name ?>">
							<?= $ns_logo ?>
						</div>
					</div>
				</div>
				<div class="ns-rating rating">
					<img src="<?= plugins_url( "/imgs/$ns_rating-stars-xs.png", __FILE__ ) ?>" class="value-title" title="<?= $ns_rating ?>" />
				</div>
			</div>
			<div class="ns-single-schema">
				<span class="ns-biz-title" itemprop="name"><?= $biz_name ?></span> is rated 
				<span><?= $avg_rating ?></span> stars over 
				<span><?= $review_count ?></span> reviews. 
				<?= ( $ns_readmore != FALSE ? "<a href='" . $ns_readmore . "'>Read more reviews</a>" : "" ) ?>
			</div>
		<?
		$output = ob_get_clean();
		return $output;

	} // sc_ns_widget ()


	function ns_admin_styles () {
		?>
		<style>
			.post-type-ninjastars th[id*="wpseo"],
			.post-type-ninjastars th[class*="wpseo"],
			.post-type-ninjastars td[class*="wpseo"] {
				display: none !important; }
		</style>
		<?
	} // ns_admin_styles ()


	#redirect user to home page if URL has 'ninjastars'
	function ninjastars_in_permalink() {
		$ninja_site_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
		
		if (strpos($ninja_site_url, 'ninjastars') !== false) {
			wp_redirect( home_url(), 301 );
			exit;
		}
	}
	

}

$ninjastars = new NinjaStars();

?>
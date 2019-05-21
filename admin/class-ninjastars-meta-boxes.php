<?php
class Ninjastars_Meta_Boxes {
	private $plugin_name;
	private $version;

	public function __construct($plugin_name, $version) {
		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}


	public function ninjastars_add_meta_boxes() {
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
	}


 	public function review_author_meta ( $post ) {
		$values = get_post_custom( $post->ID );
		$val = isset( $values['review_author_val'] ) ? esc_attr( $values['review_author_val'][0] ) : '';
		wp_nonce_field( 'my_meta_box_nonce', 'meta_box_nonce' );
		?>
			<style> #post-body-content { display: none; } </style>
			<p class="description">What is the name of the review author?</p>
			<input type="text" name="review_author_val" class="widefat" style="width:100%;" value="<?php echo $val ?>" placeholder="Enter Author Name" />
		<?php
		echo ob_get_clean();
	} // review_author_meta ( $post )



 	public function review_author_title_meta ( $post ) {
		$values = get_post_custom( $post->ID );
		$val = isset( $values['review_author_title_val'] ) ? esc_attr( $values['review_author_title_val'][0] ) : '';
		wp_nonce_field( 'my_meta_box_nonce', 'meta_box_nonce' );
		?>
			<p class="description">What is the title of the review author? (Optional.)</p>
			<input type="text" name="review_author_title_val" class="widefat" style="width:100%;" value="<?php echo $val ?>" placeholder="Enter Author Title" />
		<?php
		echo ob_get_clean();
	} // review_author_title_meta ( $post )



	public function review_summary_meta ( $post ) {
		$values = get_post_custom( $post->ID );
		$val = isset( $values['review_summary_val'] ) ? esc_attr( $values['review_summary_val'][0] ) : '';
		wp_nonce_field( 'my_meta_box_nonce', 'meta_box_nonce' );
		?>
			<p class="description">Enter a short sentence describing the reviewer's experience.</p>
			<input type="text" name="review_summary_val" class="widefat" style="width:100%;" value="<?php echo $val ?>" placeholder="Enter Review Summary" />
		<?php
		echo ob_get_clean();
	} // review_summary_meta ( $post )



	public function review_rating_meta ( $post ) {
		$values = get_post_custom( $post->ID );
		$val = isset( $values['review_rating_val'] ) ? esc_attr( $values['review_rating_val'][0] ) : '';
		wp_nonce_field( 'my_meta_box_nonce', 'meta_box_nonce' );
		ob_start();
		?>
			<p class="description">Select the appropriate star-rating to reflect on the review.</p>
			<label for="stars5"><img src="<?php echo plugins_url( '/imgs/5-stars-xs.png', __FILE__ ) ?>" /></label>
			<input type="radio" name="review_rating_val" id="stars5" value="5" <?php echo ( $val == '' || $val == '5' ? 'checked' : '' ) ?> />
			<label for="stars4"><img src="<?php echo plugins_url( '/imgs/4-stars-xs.png', __FILE__ ) ?>" /></label>
			<input type="radio" name="review_rating_val" id="stars4" value="4" <?php echo ( $val == '4' ? 'checked' : '' ) ?> />
			<label for="stars3"><img src="<?php echo plugins_url( '/imgs/3-stars-xs.png', __FILE__ ) ?>" /></label>
			<input type="radio" name="review_rating_val" id="stars3" value="3" <?php echo ( $val == '3' ? 'checked' : '' ) ?> />
			<label for="stars2"><img src="<?php echo plugins_url( '/imgs/2-stars-xs.png', __FILE__ ) ?>" /></label>
			<input type="radio" name="review_rating_val" id="stars2" value="2" <?php echo ( $val == '2' ? 'checked' : '' ) ?> />
			<label for="stars1"><img src="<?php echo plugins_url( '/imgs/1-stars-xs.png', __FILE__ ) ?>" /></label>
			<input type="radio" name="review_rating_val" id="stars1" value="1" <?php echo ( $val == '1' ? 'checked' : '' ) ?> />
			<label for="stars0"><img src="<?php echo plugins_url( '/imgs/0-stars-xs.png', __FILE__ ) ?>" /></label>
			<input type="radio" name="review_rating_val" id="stars0" value="0" <?php echo ( $val == '0' ? 'checked' : '' ) ?> />
		<?php
		$output = ob_get_clean();
		echo $output;

	} // review_rating_meta ( $post )



	public function review_content_meta( $post ) {
		$values = get_post_custom( $post->ID );
		wp_nonce_field( 'my_meta_box_nonce', 'meta_box_nonce' );
		$val = isset( $values['review_content_val'] ) ? $values['review_content_val'][0] : '';
		$val = str_replace( '\\', '', $val );
		ob_start();
		?>
		 	<p class="description">What does the reviewer have to say about their experience?</p>
			<textarea name="review_content_val" class="widefat" style="width:100%;min-height:200px;"><?php echo $val ?></textarea>
		<?php
		$output = ob_get_clean();
		echo $output;
	}

	public function ninjastars_save_post_data ( $post_id ) {
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


}
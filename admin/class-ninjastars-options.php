<?php
class Ninjastars_Options {
	private $plugin_name;
	private $version;

	public function __construct($plugin_name, $version) {
		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}


 	public function ninjastars_init_options_page () {
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

 	function ninjastars_init_settings_fields () {
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


 	public function ns_settings_section_cb () {
 		?>
 			<p>Insert your reviews into your desired page by using the shortcode <code>[ns_reviews]</code></p>
 			<p>Insert a random single review into a page or post by using the shortcode <code>[ns_review]</code>. You can also use the NinjaStar Review widget for this as well.</p>
 		<?php
 	} # ns_settings_section_cb ()



 	public function ns_settings_color_cb () {
 		$opt = get_option( 'ninjastars_color', '' );
 		ob_start();
 		?>
 		<input type="text" name="ninjastars_color" placeholder="Example: #010800" value="<?php echo $opt ?>" />
 		<p class="description">
 			What is the primary color of your theme?<br/>
 			Default: '#cccccc' (light-grey)
 		</p>
 		<?php
 		echo ob_get_clean();
 	} // ns_settings_color_cb ()



  	public function ns_settings_rcolor_cb () {

 		$opt = get_option( 'ninjastars_rcolor', '' );
 		ob_start();
 		?>
		<input type="text" name="ninjastars_rcolor" placeholder="Example: #eeeeee" value="<?php echo $opt ?>"  />
		<p class="description">
			What is the background color of the review content?<br/>
			Leave blank for transparent. Default: transparent.
		</p>
		<?php
 		echo ob_get_clean();

 	} // ns_settings_rcolor_cb ()



   	public function ns_settings_fcolor_cb () {

 		$opt = get_option( 'ninjastars_fcolor', '' );
 		ob_start();
 		?>
 		<input type="text" name="ninjastars_fcolor" placeholder="Example: #eeeeee" value="<?php echo $opt ?>" />
 		<p class="description">
 			What color is the author name on the widget?<br/>
 			This color must contrast well against the Primary Color as a background.<br/>
 			Default: '#ffffff' (off-white)
 		</p>
 		<?php
 		echo ob_get_clean();

 	} // ns_settings_fcolor_cb ()



 	public function ns_settings_logo_cb () {

 		$opt = get_option( 'ninjastars_logo', '' );
 		ob_start();
 		?>
 		<input type="text" name="ninjastars_logo" placeholder="Insert logo URL here" value="<?php echo $opt ?>" />
 		<p class="description">
 			Copy/paste image you'd like to appear next to each review. Typically brand logo. <br/>
 			Image resolution should not exceed 75x85 px.
 		</p>

 		<?php
 		echo ob_get_clean();

 	} // ns_settings_logo_cb ()



 	public function ns_settings_readmore_cb () {

 		$opt = get_option( 'ninjastars_readmore', '' );
 		ob_start();
 		?>
 		<input type="text" name="ninjastars_readmore" placeholder="Link to Reviews Page" value="<?php echo $opt ?>" />
 		<p class="description">
 			What page is your reviews shortcode going to be on? <br/>
			Example: http://yourdomain.com/read-reviews/
		</p>
 		<?php
 		echo ob_get_clean();

 	} // ns_settings_readmore_cb ()
}
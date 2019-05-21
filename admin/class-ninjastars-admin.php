<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://socialmedianinjas.com
 * @since      1.0.0
 *
 * @package    Ninjastars
 * @subpackage Ninjastars/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Ninjastars
 * @subpackage Ninjastars/admin
 * @author     The 108 Group, LLC <ninjas@socialmedianinjas.com>
 */
class Ninjastars_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->load_dependencies();
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Ninjastars_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Ninjastars_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/ninjastars-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Ninjastars_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Ninjastars_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/ninjastars-admin.js', array( 'jquery' ), $this->version, false );

	}


	public function load_dependencies() {
		require_once plugin_dir_path( dirname( __FILE__ ) ) .  'admin/class-ninjastars-custom-post-type.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) .  'admin/class-ninjastars-meta-boxes.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) .  'admin/class-ninjastars-options.php';
	}

	// Show all Website Gallery Posts on archive page
	public function ninjastars_archive( $query ) {
	  if ( is_post_type_archive( 'ninjastars' ) ) {
	    $query->set( 'posts_per_page', -1 );
	    return;
	  }
	}


	public function ninjastars_disable_editor() {
		if ( get_post_type() == 'ninjastars' ) :
			remove_post_type_support( 'ninjastars', 'editor' );
		endif;
	}


	public function ninjastars_postlist_init_custom_cols ( $columns ) {
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



	public function ninjastars_postlist_add_custom_cols ( $cols, $post_id ) {
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


	public function ninjastars_admin_styles () {
		?>
		<style>
			.post-type-ninjastars th[id*="wpseo"],
			.post-type-ninjastars th[class*="wpseo"],
			.post-type-ninjastars td[class*="wpseo"] {
				display: none !important; }
		</style>
		<?php
	} // ns_admin_styles ()

}

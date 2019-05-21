<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://socialmedianinjas.com
 * @since      1.0.0
 *
 * @package    Ninjastars
 * @subpackage Ninjastars/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Ninjastars
 * @subpackage Ninjastars/public
 * @author     The 108 Group, LLC <ninjas@socialmedianinjas.com>
 */
class Ninjastars_Public {

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
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->load_dependencies();

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
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

		wp_enqueue_style( 'ninjastars-styles', plugin_dir_url( __FILE__ ) . 'css/ninjastars.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
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

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/ninjastars-public.js', array( 'jquery' ), $this->version, false );

	}

	public function load_dependencies() {
		require_once plugin_dir_path( dirname( __FILE__ ) ) .  'public/class-ninjastars-shortcodes.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) .  'public/class-ninjastars-template-loader.php';
	}


	public function ninjastars_insert_styles () {
		global $post;
		$theme_color = get_option( 'ninjastars_color', '#CCCCCC' );
		$bg_color = get_option( 'ninjastars_rcolor', 'transparent' );
		$footer_color = get_option( 'ninjastars_fcolor', '#FFFFFF' );
		ob_start();
		?>
		<!-- NinjaStars Custom Styles -->
		<style type="text/css"> 
			.ns-left, 
			.ns-widget-footer,
			.ns-widget-info { 
				background-color: <?php echo $theme_color ?>; 
			} 
			.ns-right, 
			.ns-widget-content { 
				background-color: <?php echo $bg_color ?>; 
			}
			.ns-widget-author { 
				color: <?php echo $footer_color ?>; 
			}
		</style>
		<?php
		$output .= ob_get_clean();
		echo $output;

	} # ns_insert_styles()
}

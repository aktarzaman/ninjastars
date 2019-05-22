<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://socialmedianinjas.com
 * @since      1.0.0
 *
 * @package    Ninjastars
 * @subpackage Ninjastars/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Ninjastars
 * @subpackage Ninjastars/includes
 * @author     The 108 Group, LLC <ninjas@socialmedianinjas.com>
 */
class Ninjastars {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Ninjastars_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'NINJASTARS_VERSION' ) ) {
			$this->version = NINJASTARS_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'ninjastars';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Ninjastars_Loader. Orchestrates the hooks of the plugin.
	 * - Ninjastars_i18n. Defines internationalization functionality.
	 * - Ninjastars_Admin. Defines all hooks for the admin area.
	 * - Ninjastars_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-ninjastars-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-ninjastars-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-ninjastars-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-ninjastars-public.php';

		$this->loader = new Ninjastars_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Ninjastars_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Ninjastars_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Ninjastars_Admin( $this->get_plugin_name(), $this->get_version() );
		$plugin_options = new Ninjastars_Options( $this->get_plugin_name(), $this->get_version() );
		$plugin_cpt = new Ninjastars_Custom_Post_Type( $this->get_plugin_name(), $this->get_version() );
		$plugin_metabox = new Ninjastars_Meta_Boxes( $this->get_plugin_name(), $this->get_version() );
		$plugin_widget = new Ninjastars_Widget();

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action( 'admin_head', $plugin_admin, 'ninjastars_admin_styles' );
		$this->loader->add_action( 'pre_get_posts', $plugin_admin, 'ninjastars_archive', 1);
		$this->loader->add_action( 'admin_head', $plugin_admin, 'ninjastars_disable_editor', 10, 1);
		$this->loader->add_filter( 'manage_edit-ninjastars_columns', $plugin_admin, 'ninjastars_postlist_init_custom_cols',10, 1);
		$this->loader->add_action( 'manage_ninjastars_posts_custom_column', $plugin_admin, 'ninjastars_postlist_add_custom_cols', 10, 2);
		
		$this->loader->add_action( 'init', $plugin_cpt, 'ninjastars_post_type' );
		$this->loader->add_action( 'init', $plugin_cpt, 'ninjastars_taxonomy' );
		
		$this->loader->add_action( 'add_meta_boxes', $plugin_metabox, 'ninjastars_add_meta_boxes' );
		$this->loader->add_action( 'save_post', $plugin_metabox, 'ninjastars_save_post_data', 10, 1 );
		
		$this->loader->add_action( 'admin_menu', $plugin_options, 'ninjastars_init_options_page' );
		$this->loader->add_action( 'admin_init', $plugin_options, 'ninjastars_init_settings_fields' );
		$this->loader->add_action( 'widgets_init', $plugin_widget, 'ninjastars_load_widget' );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Ninjastars_Public( $this->get_plugin_name(), $this->get_version() );
		$plugin_shortcode = new Ninjastars_Shortcodes( $this->get_plugin_name(), $this->get_version() );
		$plugin_template = new Ninjastars_Template_Loader( $this->get_plugin_name(), $this->get_version(), array( 
				'ninjastars',
			) );


		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		$this->loader->add_action( 'wp_head', $plugin_public, 'ninjastars_insert_styles' );
		
		
		$this->loader->add_action( 'init', $plugin_shortcode, 'ninjastars_shortcode_list' );
		$this->loader->add_filter( 'template_include', $plugin_template, 'ninjastars_include_templates' );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Ninjastars_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}

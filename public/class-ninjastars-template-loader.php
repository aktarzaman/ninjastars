<?php
/**
 * Custom Template Loader for plugin
 *
 * @package     Ninja Gallery
 * @category    Core
 * @since       1.0
 */
// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'This script cannot be accessed directly.' );
}
/**
 * Class to locate template.
 *
 * @since 1.0
 */
class Ninjastars_Template_Loader {

	private $plugin_name;
	private $version;
	/**
	 * Custom Post Type Slug.
	 *
	 * @access private
	 * @since  1.0
	 * @var    array
	 */
	private $cpt_types = array();
	/**
	 * Class constructor.
	 *
	 * @param  array $args CPT slug name.
	 * @access public
	 * @since  1.0
	 */
	function __construct( $plugin_name, $version, array $args ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->cpt_types = $args;
	}
	/**
	 * Method to return template.
	 *
	 * @param  string $template The template.
	 * @access public
	 * @since  1.0
	 */
	public function ninjastars_include_templates( $template ) {
		foreach ( $this->cpt_types as $cpt ) {
			if ( get_post_type() === $cpt ) {
				return $this->get_custom_template( $cpt );
			}
		}
		return $template;
	}
	/**
	 * Method to get the templates.
	 *
	 * @param  string $cpt CPT slug name.
	 * @access private
	 * @since  1.0
	 */
	private function get_custom_template( $cpt ) {
		// Archive view template.
		if ( is_post_type_archive( $cpt ) ) {
			return $this->locate_template( $cpt, 'archive' );
		}
		// Single view template.
		if ( is_singular( $cpt ) ) {
			return $this->locate_template( $cpt, 'single' );
		}
	}
	/**
	 * Method to locate templates.
	 *
	 * @param  string $cpt CPT slug name.
	 * @param  string $type Type of the template.
	 * @access private
	 * @since  1.0
	 */
	private function locate_template( $cpt, $type ) {
		$theme_files = array( $type . '-' . $cpt . '.php', 'ninjastars/' . $type . '-' . $cpt . '.php' );
		$exists_in_theme = locate_template( $theme_files, false );
		if ( '' !== $exists_in_theme ) {
			// Checking the template in theme first. If located, return the template.
			return $exists_in_theme;
		} else {
			// If template is not located in theme, return the default template from plugin.
			return plugin_dir_path( __FILE__ ) . 'templates/' . $type . '-' . $cpt . '.php';
		}
	}
}
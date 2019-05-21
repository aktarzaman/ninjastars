<?php

/**
 * Fired during plugin deactivation
 *
 * @link       http://socialmedianinjas.com
 * @since      1.0.0
 *
 * @package    Ninjastars
 * @subpackage Ninjastars/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Ninjastars
 * @subpackage Ninjastars/includes
 * @author     The 108 Group, LLC <ninjas@socialmedianinjas.com>
 */
class Ninjastars_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {
		flush_rewrite_rules();
	}

}

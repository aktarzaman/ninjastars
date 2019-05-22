<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://socialmedianinjas.com
 * @since             1.0.0
 * @package           Ninjastars
 *
 * @wordpress-plugin
 * Plugin Name:       NinjaStars
 * Plugin URI:        http://socialmedianinjas.com
 * Description:       hReview 0.4 microformat plugin jam-packed with Star Power. 
 * Version:           1.8.0
 * Author:            The 108 Group, LLC
 * Author URI:        http://socialmedianinjas.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       ninjastars
 * Domain Path:       /languages
 */


/* 
	UPDATE NOTES
	v 1.8 (23rd May, 2019 by Zaman)
		1. Added 'NinjaStars Widget' on appearance->widgets dashboard

-	v 1.7 (22nd May, 2019 by Zaman)
		1. Converted to OOP framework
		2. Increased Security 
		3. Introduced custom taxonomies
		4. Removed old redirection for single and archive view
		5. Introduced template support from plugin itself for single and archive views
		6. Yoast SEO sitemap Fix for review title
		7. Improved shortcodes internal layout & bug fixing
		8. Reduced databse query load
		9. Some other minor fixes

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

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'NINJASTARS_VERSION', '1.7.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-ninjastars-activator.php
 */
function activate_ninjastars() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-ninjastars-activator.php';
	Ninjastars_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-ninjastars-deactivator.php
 */
function deactivate_ninjastars() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-ninjastars-deactivator.php';
	Ninjastars_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_ninjastars' );
register_deactivation_hook( __FILE__, 'deactivate_ninjastars' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-ninjastars.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_ninjastars() {

	$plugin = new Ninjastars();
	$plugin->run();

}
run_ninjastars();

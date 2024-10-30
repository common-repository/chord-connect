<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @since             1.0.0
 * @package           ChordConnect
 *
 * @wordpress-plugin
 * Plugin Name:       Chord Connect
 * Plugin URI:        https://chordconnect.com/wp-plugin
 * Description:       <strong>Experience the highest quality video chat, no downloads required.</strong> Easily integrate Chordconnect into your Wordpress site.
 * Version:           1.0.4
 * Author:            CloudEngage
 * Author URI:        https://cloudengage.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       chordconnect
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

/**
 * Current plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'CHORDCONNECT_VERSION', '{{ 1.0.4 }}' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-chordconnect-activator.php
 */
function activate_chordconnect() {
    require_once plugin_dir_path( __FILE__ ) . 'includes/class-chordconnect-activator.php';
    Chordconnect_Activator::activate();
    Chordconnect_Activator::register_successful_install();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-chordconnect-deactivator.php
 */
function deactivate_chordconnect() {
    require_once plugin_dir_path( __FILE__ ) . 'includes/class-chordconnect-deactivator.php';
    Chordconnect_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_chordconnect' );
register_deactivation_hook( __FILE__, 'deactivate_chordconnect' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-chordconnect.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_chordconnect() {

    $plugin = new ChordConnect();
    $plugin->run();

}
run_chordconnect();

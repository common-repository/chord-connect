<?php

/**
 * Fired during plugin deactivation
 *
 * @link       https://cloudengage.com/
 * @since      1.0.0
 *
 * @package    Cloudengage
 * @subpackage Cloudengage/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    ChordConnect
 * @subpackage ChordConnect/includes
 * @author     ChordConnect <rory@cloudengage.com>
 */
class Chordconnect_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {

        // write our current settings out to the presets file
        $path = plugin_dir_path( dirname( __FILE__ ) ) . '/includes/presets.json';
        $data = array(
            "script_url" => get_option( 'chordconnect_script_url', '' ),
        );

        file_put_contents( $path, json_encode($data) );

        // unregister our settings/options
        delete_option( 'chordconnect_check_timestamp' );
        delete_option( 'chordconnect_check_status' );
        delete_option( 'chordconnect_script_url' );

        // remove our scheduled event
        wp_clear_scheduled_hook( 'chordconnect_script_version_check' );
	}

}

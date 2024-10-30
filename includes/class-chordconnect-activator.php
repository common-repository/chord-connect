<?php

/**
 * Fired during plugin activation
 *
 * @link       https://cloudengage.com/
 * @since      1.0.0
 *
 * @package    Chordconnect
 * @subpackage Chordconnect/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    ChordConnect
 * @subpackage ChordConnect/includes
 * @author     ChordConnect <rory@cloudengage.com>
 */
class Chordconnect_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {

        add_option( 'chordconnect_script_url' );

        // update our status immediately
        update_option( 'chordconnect_check_status', ChordConnect::STATUS_API_CHECK_NEVER );

        $path = plugin_dir_path( dirname( __FILE__ ) ) . '/includes/presets.json';

        $file = realpath( $path );

        if ( is_file( $file ) ) {
            $presets = json_decode( file_get_contents( $file ), true );

            if ( isset( $presets['script_url'] ) ) {
                update_option( 'chordconnect_script_url', $presets['script_url'] );
            }
        }
        
        // on activation, we want to set up our wp_cron sechedule too
        if (! wp_next_scheduled ( 'chordconnect_script_version_check' )) {
            wp_schedule_event(time(), 'twicedaily', 'chordconnect_script_version_check');
        }
	}

    public static function register_successful_install() {
        $current_user = wp_get_current_user();
        if ( $current_user->exists() ) {
            $data = array(
                'email' => $current_user->user_email,
                'first_name' => $current_user->user_firstname,
                'last_name' => $current_user->user_lastname,
                'company' => get_bloginfo('name'),
                'url' => get_bloginfo('url')
            );
    
            $curl = curl_init('https://go.cloudengage.com/signup/wordpress');
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            curl_setopt($curl, CURLOPT_HTTPHEADER, ['Accept: application/json']);
    
            $response = curl_exec($curl);
            $response = json_decode($response);
    
            curl_close($curl);
        }
    }
}

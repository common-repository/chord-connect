<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://cloudengage.com/
 * @since      1.0.0
 *
 * @package    Chordconnect
 * @subpackage Chordconnect/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    ChordConnect
 * @subpackage ChordConnect/includes
 * @author     ChordConnect <rory@cloudengage.com>
 */
abstract class Chordconnect_Base {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	protected $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	protected $version;

    protected $instance_name;

    protected $instance_path;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $instance_name, $version ) {

		$this->plugin_name = $plugin_name;
        $this->instance_name = $instance_name;
        $this->version = $version;
	}

    public function get_template( $filename, $template_data = array() ) {

        $path = plugin_dir_path( dirname( __FILE__ ) ) . $this->instance_name . '/templates/';

        $file = realpath( $path . $filename );

        if ( is_file( $file ) ) {

            ob_start();

            if ( $template_data ) {
                // Load variables
                foreach ( $template_data as $key => $value ) {
                    $$key = $value;
                }
            }

            include( $file );

            $out = ob_get_contents();
            ob_end_clean();

            return $out;

        } else {

            return '';

        }
    }

	public function get_notice( $type, $class = array() ) {

        return $this->get_template( 'chordconnect-notice.php', array(
            'type' => $type,
            'class' => $class
        ) );

    }

}

<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @since      1.0.0
 *
 * @package    Chordconnect
 * @subpackage Chordconnect/includes
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
 * @package    ChordConnect
 * @subpackage ChordConnect/includes
 * @author     ChordConnect <rory@cloudengage.com>
 */
class ChordConnect {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      ChordConnect_Loader    $loader    Maintains and registers all hooks for the plugin.
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

    const NOTICE_NONE = 0;

    const NOTICE_WIDGET_ACTIVATED = 1;

    const NOTICE_WIDGET_UPDATE_SUCCESS = 2;

    const NOTICE_APIKEY_CLEARED = 3;

    const NOTICE_WIDGET_DEACTIVATED = 4;

    const NOTICE_SETUP_NEXT_STEPS = 5;

    const NOTICE_WIDGET_UPDATE_AVAILABLE = 6;

    const NOTICE_NONCE_ERROR = 7;

    const NOTICE_API_COMM_ERROR = 8; // could not communicate with the service

    const NOTICE_API_AUTH_ERROR = 9; // API could not validate token

    const NOTICE_API_KEY_ERROR = 10; // API Key is blank

    const NOTICE_APIKEY_INVALIDATED = 11;

    const STATUS_API_CHECK_OK = 101; // API validated the API Key without issue

    const STATUS_API_CHECK_FAIL = 102; // API could not validate token

    const STATUS_API_CHECK_NO_SERVICE = 103; // API could not be reached

    const STATUS_API_CHECK_NO_CALL = 104; // No call was made to the API

    const STATUS_API_CHECK_NEVER = 105; // No check has ever been made

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
		if ( defined( 'CHORDCONNECT_VERSION' ) ) {
			$this->version = CHORDCONNECT_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'chordConnect';

		$this->load_dependencies();
		$this->define_public_hooks();
		$this->define_admin_hooks();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Chordconnect_Loader. Orchestrates the hooks of the plugin.
	 * - Chordconnect_i18n. Defines internationalization functionality.
	 * - Chordconnect_Admin. Defines all hooks for the admin area.
	 * - Chordconnect_Public. Defines all hooks for the public side of the site.
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
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-chordconnect-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-chordconnect-i18n.php';

        /**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-chordconnect-base.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-chordconnect-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-chordconnect-public.php';

		$this->loader = new ChordConnect_Loader();
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Chordconnect_Admin( $this->get_plugin_name(), 'admin', $this->get_version() );

        $this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );

        // add our settings page to the General Settings menu
        $this->loader->add_action( 'admin_menu', $plugin_admin, 'initialize_menu' );

        $this->loader->add_action( 'admin_post_chordconnect_handle_form_post', $plugin_admin, 'handle_form_post' );

        $this->loader->add_action( 'admin_post_chordconnect_handle_get_update', $plugin_admin, 'handle_get_update' );

        $this->loader->add_action( 'load-settings_page_chordconnect', $plugin_admin, 'settings_page_on_load' );

        $this->loader->add_action( 'admin_notices', $plugin_admin, 'admin_notices' );

        // adds a settings link to the plugin admin page
        $this->loader->add_filter( 'plugin_action_links_chordconnect/chordconnect.php', $plugin_admin, 'plugin_add_settings_link' );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new ChordConnect_Public( $this->get_plugin_name(), 'public', $this->get_version() );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_head_scripts' );
		$this->loader->register_activation_hook('chordconnect.php', 'register_successful_install' );
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
	 * @return    Chordconnect_Loader    Orchestrates the hooks of the plugin.
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

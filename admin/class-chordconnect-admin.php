<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://chordconnect.com/
 * @since      1.0.0
 *
 * @package    ChordConnect
 * @subpackage ChordConnect/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    ChordConnect
 * @subpackage ChordConnect/admin
 * @author     Cloudengage <rory@cloudengage.com>
 */
class ChordConnect_Admin extends Chordconnect_Base {

    private $issues = array();

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/chordconnect-admin.css', array(), $this->version, 'all' );

	}

    public function initialize_menu() {

        $this->check_for_issues();

        $menu_text = 'Chord Connect';
        // add a notification bubble if there is a widget update
        if ( count ( $this->issues ) ) {
            $menu_text .= ' <span class="chordconnect__badge">' . count ( $this->issues ). '</span>';
        }

        add_submenu_page(
            'options-general.php',
            'ChordConnect',
            $menu_text,
            'manage_options',
            'chordconnect',
            array( $this, 'settings_page' )
        );
    }

    public function check_for_issues() {

        if (! get_option( 'chordconnect_organization_subdomain', false ) && !in_array( Chordconnect::NOTICE_SETUP_NEXT_STEPS, $this->issues ) ) {
            $this->issues[] = Chordconnect::NOTICE_SETUP_NEXT_STEPS;
        }

        if ( $this->is_local_script_outdated() && ! in_array( Chordconnect::NOTICE_WIDGET_UPDATE_AVAILABLE, $this->issues ) ) {
            $this->issues[] = Chordconnect::NOTICE_WIDGET_UPDATE_AVAILABLE;
        }

        $check_status = get_option( 'chordconnect_check_status', Chordconnect::STATUS_API_CHECK_NEVER );

        if ( $check_status == Chordconnect::STATUS_API_CHECK_FAIL ) {
            $this->issues[] = Chordconnect::NOTICE_WIDGET_DEACTIVATED;
        }

    }

    public function admin_notices() {

        if ( $this->issues ) {
            foreach ( $this->issues as $notice ) {
                echo $this->get_notice( $notice );
            }
        }

    }

    /**
     * Removes CloudEngage admin notices on the settings page
     */
    public function settings_page_on_load() {
        remove_action( 'admin_notices', array( $this, 'admin_notices' ) );
    }

    public function settings_page() {

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( __( 'You do not have permission to access this page.' ) );
        }

        $data = array();
        $data['chordconnect_organization_name'] = get_option( 'chordconnect_organization_name', '' );
        $data['chordconnect_organization_subdomain'] = get_option( 'chordconnect_organization_subdomain', '' );
        $data['chordconnect_environment'] = get_option( 'chordconnect_environment', '' );
        $data['chordconnect_info_url'] = get_option( 'chordconnect_info_url', '' );
        $data['chordconnect_download_url'] = get_option( 'chordconnect_download_url', '' ) . get_option( 'chordconnect_organization_hash', '');
        $data['chordconnect_local_plugin_version'] = CHORDCONNECT_VERSION;
        $data['chordconnect_remote_plugin_version'] = get_option( 'chordconnect_remote_plugin_version', CHORDCONNECT_VERSION );
        $data['chordconnect_remote_widget_version'] = get_option( 'chordconnect_remote_widget_version', '' );
        $data['chordconnect_check_status'] = get_option( 'chordconnect_check_status', Chordconnect::STATUS_API_CHECK_NEVER );
        $data['chordconnect_update_available'] = false;

        if ( $data['chordconnect_check_timestamp'] ) {
            $data['chordconnect_last_checked'] = date( 'F j, Y @ H:i', $data['chordconnect_check_timestamp'] );
        }

        if ( $this->is_local_script_outdated() ) {
            $data['notices'][] = $this->get_notice( Chordconnect::NOTICE_WIDGET_UPDATE_AVAILABLE, array( 'chordconnect__notice' ) );
            $data['chordconnect_update_available'] = true;
        }

        if ( ! empty( $_GET['notice'] ) ) {
             $data['notices'][] = $this->get_notice( $_GET['notice'], array( 'chordconnect__notice' ) );

             if ( $_GET['notice'] == Chordconnect::NOTICE_APIKEY_CLEARED && ! in_array( Chordconnect::NOTICE_WIDGET_DEACTIVATED, $data['notices'] ) ) {
                 $data['notices'][] = $this->get_notice( Chordconnect::NOTICE_WIDGET_DEACTIVATED, array( 'chordconnect__notice' ) );
             }
        }

        echo $this->get_template( 'chordconnect-admin-settings.php', $data );

    }

    public function handle_get_update() {

        $notice = Chordconnect::NOTICE_NONE;

        if ( isset( $_GET['_cenonce'] ) && wp_verify_nonce( $_GET['_cenonce'], 'chordconnect_update_widget' ) ) {

            $chordconnect_organization_subdomain = get_option( 'chordconnect_organization_subdomain', '' );

            if ( $chordconnect_organization_subdomain ) {
                $notice = Chordconnect::get_remote_organization( Chordconnect::NOTICE_WIDGET_UPDATE_SUCCESS );
            }
        } else {
            $notice = Chordconnect::NOTICE_NONCE_ERROR;
        }

        wp_redirect( admin_url( 'options-general.php?page=chordconnect&notice=' . $notice ) );
        exit;
    }

    public function handle_form_post() {

        $notice = Chordconnect::NOTICE_NONE;

        /**
         * Handle form POST
         */
        if ( isset( $_POST['chordconnect_organization_subdomain'] ) ) {

            if ( isset( $_POST['chordconnect_settings_form'] ) && wp_verify_nonce( $_POST['chordconnect_settings_form'], 'chordconnect_settings' ) ) {

                $chordconnect_organization_subdomain = get_option( 'chordconnect_organization_subdomain', '' );
                // pull the submitted subdomain key and sanitize it
                $chordconnect_submitted_subdomain = sanitize_text_field( $_POST['chordconnect_organization_subdomain'] );

                // if the API Key is blank, we can clear the various fields
                if ( ! $chordconnect_submitted_subdomain ) {
                    update_option( 'chordconnect_organization_subdomain', '' );
                    update_option( 'chordconnect_organization_name', '' );
                    update_option( 'chordconnect_organization_hash', '' );
                    update_option( 'chordconnect_remote_plugin_version', CHORDCONNECT_VERSION );
                    update_option( 'chordconnect_check_timestamp', current_time( 'timestamp' ) );
                    update_option( 'chordconnect_check_status', Chordconnect::STATUS_API_CHECK_NO_CALL );

                    wp_redirect( admin_url( 'options-general.php?page=chordconnect&notice=' . Chordconnect::NOTICE_APIKEY_CLEARED ) );
                    exit;
                } else {

                    if ( $chordconnect_submitted_subdomain && $chordconnect_organization_subdomain != $chordconnect_submitted_subdomain ) {
                        // update the key regardless
                        update_option( 'chordconnect_organization_subdomain', $chordconnect_submitted_subdomain );
                        // recheck for the hash
                        update_option( 'chordconnect_organization_hash', '' );
                        update_option( 'chordconnect_organization_name', '' );
                        update_option( 'chordconnect_remote_plugin_version', CHORDCONNECT_VERSION );
                        $notice = Chordconnect::get_remote_organization( Chordconnect::NOTICE_WIDGET_ACTIVATED );
                    } else {
                        wp_redirect( admin_url( 'options-general.php?page=chordconnect&notice=' . Chordconnect::NOTICE_NONE ) );
                        exit;
                    }
                }

            } else {
                $notice = Chordconnect::NOTICE_NONCE_ERROR;
            }

        }

        wp_redirect( admin_url( 'options-general.php?page=chordconnect&notice=' . $notice ) );
        exit;
    }

    public function plugin_add_settings_link( $links ) {
        $link = '<a href="' . admin_url( 'options-general.php?page=chord-connect' ) . '">' . __( 'Settings' ) . '</a>';
        array_unshift( $links, $link );
        return $links;
    }

    /** 
     * Add link to the plugin card that gets displayed in the installed plugins panel.
     * 
    */
    public function wk_plugin_row_meta( $links, $file ) {    
        if ( $file === 'chordConnect/chordconnect.php' ) {
            $links[] = '<a href="' . admin_url( 'options-general.php?page=chord-connect' ) . '" style="color:#239B56;">' . __( 'Next Steps' ) . '</a>';
        }
        return $links;
    }

    protected function is_local_script_outdated() {

        $chordconnect_local_script_version = CHORDCONNECT_VERSION;
        $chordconnect_remote_script_version = get_option( 'chordconnect_remote_plugin_version', 0 );

        return $chordconnect_remote_script_version > $chordconnect_local_script_version;

    }

}

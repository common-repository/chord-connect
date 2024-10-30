<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

switch ( $type ) {
               
    case Chordconnect::NOTICE_WIDGET_ACTIVATED:
        $icon = null;
        $bold_text = 'Your Key has been successfully updated.';
        $message = 'Version ' . CHORDCONNECT_VERSION . ' of the CloudEngage Plugin is now active.';
        $class[] = 'notice-success';
        $class[] = 'is-dismissible';
        break;
        
    case Chordconnect::NOTICE_WIDGET_UPDATE_SUCCESS:
        $icon = null;
        $bold_text = 'CloudEngage Plugin updated successfully.';
        $message = 'The Plugin has been updated to version ' . CHORDCONNECT_VERSION;
        $class[] = 'notice-success';
        $class[] = 'is-dismissible';
        break;
    
    case Chordconnect::NOTICE_APIKEY_CLEARED:
        $icon = null;
        $bold_text = 'Your Key has been removed.';
        $message = '';
        $class[] = 'notice-success';
        $class[] = 'is-dismissible';
        break;
    
    case Chordconnect::NOTICE_WIDGET_DEACTIVATED:
        $icon = null;
        $bold_text = 'The CloudEngage Plugin is not currently active.';
        $link = admin_url( 'options-general.php?page=chordconnect' );  
        $message = '<a href="' . $link . '">Please check your Key.</a>';
        $class[] = 'notice-warning';
        $class[] = 'is-dismissible';
        break;
    
    case Chordconnect::NOTICE_SETUP_NEXT_STEPS:
        $icon = '<img src="' . esc_url( plugins_url( '../img/chordconnect-logo.png', __FILE__ ) ) . '" alt="Chord Connect" />';
        $bold_text = 'Chord Connect has been successfully installed!';
        
        $link = admin_url( 'options-general.php?page=chordconnect' );  
        $message = '<a href="' . $link . '" style="margin-left: 10px;">Next Steps</a>.';

        $class[] = 'notice-info';
        $class[] = 'chordconnect__branded_notice';
        $class[] = 'is-dismissible';
        break;
    
    case Chordconnect::NOTICE_WIDGET_UPDATE_AVAILABLE:
        $icon = '<img src="' . esc_url( plugins_url( '../img/chordconnect-logo.png', __FILE__ ) ) . '" alt="Chord Connect" />';
        $bold_text = 'There is a new version of the CloudEngage Plugin available.';
        
        //$update_link = wp_nonce_url( admin_url( 'admin-post.php?action=chordconnect_handle_get_update' ), 'chordconnect_update_widget', '_cenonce' );
        $link = admin_url( 'options-general.php?page=chordconnect' );
        $message = '<a href="' . $link . '">Update to version ' . get_option( 'chordconnect_remote_plugin_version' ) . '</a>.';

        $class[] = 'notice-info';
        $class[] = 'chordconnect__branded_notice';
        break;
        
    case Chordconnect::NOTICE_NONCE_ERROR:
        $icon = null;
        $bold_text = 'Hold up!';
        $message = 'The authenticity of this request could not be verified...';
        $class[] = 'notice-error';
        $class[] = 'is-dismissible';
        break;
    
    case Chordconnect::NOTICE_API_COMM_ERROR:
        $icon = null;
        $bold_text = 'Communication Error -';
        $message = 'The CloudEngage Service may be temporarily unavailable. Please try again later.';
        $class[] = 'notice-error';
        $class[] = 'is-dismissible';
        break;
    
    case Chordconnect::NOTICE_NONE:
    default:
        return '';
        
}

?>
<div class="<?php echo implode( ' ', $class );?> notice">
    <p><?php echo $icon;?><b><?php _e( $bold_text, "chordconnect" );?></b> <?php _e( $message, "chordconnect" );?></p>
</div>

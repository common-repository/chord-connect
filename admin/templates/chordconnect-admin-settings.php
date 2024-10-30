<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://chordconnect.com/
 * @since      1.0.0
 *
 * @package    Chordconnect
 * @subpackage Chordconnect/admin/templates
 */
$hero_text_tag = __( "A Video Community for your entire team, on your own website.", "chordconnect" );
$hero_text = __( "Experience the highest quality video chat, no downloads required. Just send your personal video link to friends and family, and you’re off!", "chordconnect" );
$chordconnect_info_url = "https://go.cloudengage.com";
$masthead_text = __( "Chord Connect", "chordconnect" );

if ( $chordconnect_organization_subdomain && $chordconnect_check_status == Chordconnect::STATUS_API_CHECK_OK ) {
    $button_text = __( "Change", "chordconnect" );
    $intro_text = array(
        __( "Plugin Version", "chordconnect") . " <b>" . $chordconnect_local_plugin_version . "</b>",
        __( "Widget Version", "chordconnect") . " <b>" . $chordconnect_remote_widget_version . "</b>",
        "Organization <b>" . $chordconnect_organization_name . "</b>"
    );

    if ( $chordconnect_environment ) {
        $intro_text[] = "Environment <b>" . strtoupper( $chordconnect_environment ) . "</b>";
    }

    $form_header = __( "Current Settings", "chordconnect" );
    $descriptive_text = __( "To change the Organization Key, enter the new Key into the field above", "chordconnect" );
} 

switch ( $chordconnect_check_status ) {

    default:
        $status_text = '';

}

?>
<div class="chordconnect">

    <div class="chordconnect__masthead">

        <img src="<?php echo esc_url( plugins_url( '../img/chordconnect-logo.png', __FILE__ ) ); ?>" alt="Chordconnect" />
        <h3 style="display:inline-block;margin:0 0 0 10px;line-height:34px; vertical-align:top;"><?php echo $masthead_text; ?></h3>
    </div>

    <div class="chordconnect__content">

        <form method="post" action="<?php echo admin_url( 'admin-post.php' ); ?>">
            <input type="hidden" name="action" value="chordconnect_handle_form_post" />
            <?php wp_nonce_field( 'chordconnect_settings', 'chordconnect_settings_form' ); ?>

            <?php
            if ( $notices ) {
                foreach ( $notices as $notice ) {
                    echo $notice;
                }
            };
            ?>
    
            <div class="chordconnect__section chordconnect__section--white">
            <h3><?php echo $form_header; ?></h3>
                <p class="chordconnect__text--large"><strong><?php echo $hero_text_tag; ?></strong> <?php echo $hero_text; ?> </p>
                <p><?php echo implode( "<br>", $intro_text ); ?></p>
            </div>

            <?php if (! $chordconnect_organization_subdomain ): ?>
                <div class="chordconnect__section chordconnect__section--white chordconnect__section--adjoined">

                    <h3><?php _e( "You're now ready to start using Chord Connect", "chordconnect" );?></h3>
                    <ol>
                        <li><?php _e( "Log in to your CloudEngage account.", "chordconnect" );?>.</li>
                        <li><?php _e( "Visit the Chord Connect admin page.", "chordconnect" );?>.</li>
                        <li><?php _e( "Configure settings and start sending invites!", "chordconnect" );?>.</li>
                    </ol>

                    <p class="submit">
                        <a href="<?php echo $chordconnect_info_url; ?>" target="_blank" class="button button-seconday chordconnect__button"><?php _e( "Visit CloudEngage Now", "chordconnect" ); ?></a>
                    </p>

                </div>
            <?php elseif ( $chordconnect_organization_subdomain && $chordconnect_update_available ): ?>
                
            <?php endif; ?>

        </form>
    </div>
</div>

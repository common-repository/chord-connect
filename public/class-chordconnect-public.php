<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @since      1.0.0
 *
 * @package    ChordConnect
 * @subpackage ChordConnect/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    ChordConnect
 * @subpackage ChordConnect/includes
 * @author     ChordConnect <rory@cloudengage.com>
 */
class Chordconnect_Public extends Chordconnect_Base {

    public function enqueue_head_scripts() {
        $scriptURL = get_option( 'chordconnect_script_url', false );
        wp_enqueue_script('CHORDCONNECT_SCRIPT', $scriptURL, [], null);
    }
}

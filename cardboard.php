<?php
/**
 * Plugin Name: Cardboard
 * Version: 0.1-alpha
 * Description: PLUGIN DESCRIPTION HERE
 * Author: YOUR NAME HERE
 * Author URI: YOUR SITE HERE
 * Plugin URI: PLUGIN SITE HERE
 * Text Domain: cardboard
 * Domain Path: /languages
 * @package Cardboard
 */

require_once dirname( __FILE__ ) . '/vendor/autoload.php';

add_action( "add_attachment", function( $post_id ){
	$src = get_attached_file( $post_id );
	if ( Cardboard::is_panorama_photo( $src ) ) {
		update_post_meta( $post_id, 'is_panorama_photo', true );
	}
} );

add_filter( 'image_send_to_editor', function( $html, $post_id ) {
	if ( get_post_meta( $post_id, 'is_panorama_photo' ) ) {
		return '[cardboard id="' . esc_attr( $post_id ) . '"]';
	} else {
		return $html;
	}
}, 10, 2 );

add_shortcode( 'cardboard', function( $p, $content ) {
	if ( intval( $p['id'] ) ) {
		$src = wp_get_attachment_image_src( $p['id'], 'full' );
		if ( $src ) {
			return sprintf(
				'<div class="cardboard" data-image="%s"></div>',
				esc_url( $src[0] )
			);
		}
	}
} );

add_action( "wp_enqueue_scripts", function() {
	wp_enqueue_script(
		"three-js",
		plugins_url( 'js/three.min.js', __FILE__ ),
		array(),
		time(),
		true
	);
	wp_enqueue_script(
		"three-plugins-js",
		plugins_url( 'js/three-plugins.min.js', __FILE__ ),
		array( 'three-js' ),
		time(),
		true
	);
	wp_enqueue_script(
		"cardboard-js",
		plugins_url( 'js/cardboard.js', __FILE__ ),
		array( 'jquery','three-plugins-js' ),
		time(),
		true
	);
} );
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

add_filter( 'get_image_tag_class', function( $class, $post_id, $align, $size ) {
	if ( get_post_meta( $post_id, 'is_panorama_photo' ) ) {
		return $class . ' panorama_photo';
	} else {
		return $class;
	}
}, 10, 4 );

add_action( "wp_enqueue_scripts", function() {

} )

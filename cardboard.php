<?php
/**
 * Plugin Name: Cardboard
 * Version: 0.1.7
 * Description: This plugin enables you to enjoy 360 photo with Google Cardboard.
 * Author: Takayuki Miyauchi
 * Author URI: http://firegoby.jp/
 * Plugin URI: https://github.com/miya0001/cardboard
 * Text Domain: cardboard
 * Domain Path: /languages
 * @package cardboard
 */

register_activation_hook( __FILE__, 'cardboard_init' );

function cardboard_init() {
	add_rewrite_endpoint( 'cardboard', EP_ROOT );
	flush_rewrite_rules();
}

$cardboard = new CardBoard();

class Cardboard
{
	const NS = 'http://ns.google.com/photos/1.0/panorama/';

	public function __construct()
	{
		add_action( "plugins_loaded", array( $this, "plugins_loaded" ) );
	}

	public function plugins_loaded()
	{
		if ( is_admin() ) {
			add_action( "add_attachment", array( $this, "add_attachment" ) );
			add_filter( "image_send_to_editor", array( $this, "image_send_to_editor" ), 10, 8 );
		} else {
			add_action( "wp_head", array( $this, "wp_head" ) );
			add_action( "wp_enqueue_scripts", array( $this, "wp_enqueue_scripts" ) );
			add_action( "init", array( $this, "init" ) );
			add_filter( "query_vars", array( $this, "query_vars" ) );
			add_action( "template_redirect", array( $this, "template_redirect" ) );

			add_shortcode( 'cardboard', function( $p, $content ) {
				if ( intval( $p['id'] ) ) {
					$src = wp_get_attachment_image_src( $p['id'], 'full' );
					if ( $src ) {
						return sprintf(
							'<div class="cardboard" data-image="%s"><a class="full-screen" href="%s"><span class="dashicons dashicons-editor-expand"></span></a></div>',
							esc_url( $src[0] ),
							home_url( 'cardboard/' . intval( $p['id'] ) )
						);
					}
				}
			} );
		}
	}

	public function query_vars( $query )
	{
		$query[] = 'cardboard';
		return $query;
	}

	public function template_redirect()
	{
		if ( isset( $GLOBALS['wp_query']->query['cardboard'] ) ) {
			if ( intval( get_query_var( 'cardboard' ) ) ) {
				$src = wp_get_attachment_image_src( get_query_var( 'cardboard' ), 'full' );
				$post = get_post( get_query_var( 'cardboard' ) );
				if ( $src && $post ) {
					?>
<!DOCTYPE html>

<html lang="en">
<head>
<title><?php echo esc_html( $post->post_title ); ?></title>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes" />
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent" />
<style>
body {
width: 100%;
height: 100%;
background-color: #000;
color: #fff;
margin: 0px;
padding: 0;
overflow: hidden;
}
</style>
</head>

<body></body>

<script>
WebVRConfig = {};
</script>
<script type="text/javascript" src="<?php echo plugins_url( 'three/three.min.js', __FILE__ ); ?>"></script>
<script type="text/javascript" src="<?php echo plugins_url( 'three/three-webvr.min.js', __FILE__ ); ?>"></script>
<script>
var renderer = new THREE.WebGLRenderer( { antialias: true } );
renderer.setPixelRatio( window.devicePixelRatio );

document.body.appendChild( renderer.domElement );

var scene, camera, controls, effect, manager;
scene = new THREE.Scene();
camera = new THREE.PerspectiveCamera( 75, window.innerWidth / window.innerHeight, 1, 100 );
controls = new THREE.VRControls( camera );
effect = new THREE.VREffect( renderer );
effect.setSize( window.innerWidth, window.innerHeight );
manager = new WebVRManager( renderer, effect, { hideButton: false } );

init();

function init() {
    var texloader = new THREE.TextureLoader();
    var sphere = new THREE.Mesh(
        new THREE.SphereGeometry( 20, 32, 24, 0 ), // Note: Math.PI * 2 = 360
        new THREE.MeshBasicMaterial( {
            map: texloader.load( '<?php echo esc_js( $src[0] ); ?>' )
        } )
    );
    sphere.scale.x = -1;

    scene.add( sphere );

    animate();
}

function animate( timestamp ) {
    controls.update();
    manager.render( scene, camera, timestamp );
    requestAnimationFrame( animate );
}
</script>
</html>
					<?php
					exit;
				}
			}
			$GLOBALS['wp_query']->set_404();
			status_header( 404 );
			return;
		}
	}

	public function init()
	{
		add_rewrite_endpoint( 'cardboard', EP_ROOT );
	}

	public function add_attachment( $post_id )
	{
		$src = get_attached_file( $post_id );
		if ( self::is_panorama_photo( $src ) ) {
			update_post_meta( $post_id, 'is_panorama_photo', true );
		}
	}

	public function image_send_to_editor( $html, $post_id, $caption, $title, $align, $url, $size, $alt )
	{
		if ( get_post_meta( $post_id, 'is_panorama_photo' ) && ( ! is_array( $size ) && 'full' === $size ) ) {
			return '[cardboard id="' . esc_attr( $post_id ) . '"]';
		} else {
			return $html;
		}
	}

	public function wp_head()
	{
		?>
		<style>
		.cardboard
		{
			position: relative;
		}
		.cardboard .full-screen
		{
			display: block;
			position: absolute;
			bottom: 8px;
			right: 8px;
			z-index: 999;
			color: #ffffff;
			text-decoration: none;
			border: none;
		}
		</style>
		<?php
	}

	public function wp_enqueue_scripts()
	{
		wp_enqueue_style( 'dashicons' );

		wp_enqueue_script(
			"three-js",
			plugins_url( 'three/three.min.js', __FILE__ ),
			array(),
			time(),
			true
		);
		wp_enqueue_script(
			"three-orbit-controls-js",
			plugins_url( 'three/three-orbit-controls.min.js', __FILE__ ),
			array( 'three-js' ),
			time(),
			true
		);
		wp_enqueue_script(
			"cardboard-js",
			plugins_url( 'js/cardboard.js', __FILE__ ),
			array( 'jquery','three-orbit-controls-js' ),
			time(),
			true
		);
	}

	/**
	 * Check exif and xmp meta data for detecting is it a paorama or not.
	 * @param  string  $image A path to image.
	 * @return boolean        Is image panorama photo or not.
	 */
	public static function is_panorama_photo( $image )
	{
		$content = file_get_contents( $image );
		$xmp_data_start = strpos( $content, '<x:xmpmeta' );
		$xmp_data_end   = strpos( $content, '</x:xmpmeta>' );
		$xmp_length     = $xmp_data_end - $xmp_data_start;
		if ( $xmp_length ) {
			$xmp_data = substr( $content, $xmp_data_start, $xmp_length + 12 );
			$xmp = simplexml_load_string( $xmp_data );
			$xmp = $xmp->children( "http://www.w3.org/1999/02/22-rdf-syntax-ns#" );
			$xmp = $xmp->RDF->Description;
			if ( "TRUE" === strtoupper( (string) $xmp->attributes( self::NS )->UsePanoramaViewer ) ) {
				return true;
			} elseif ( "TRUE" === strtoupper( (string) $xmp->children( self::NS )->UsePanoramaViewer ) ) {
				return true;
			}
		}

		$models = array(
			'RICOH THETA',
		);
		$models = apply_filters( 'cardboard_exif_models', $models );

		$exif = exif_read_data( $image );
		if ( $exif && ! empty( $exif['Model'] ) ) {
			foreach ( $models as $model ) {
				if ( false !== strpos( strtoupper( $exif['Model'] ), strtoupper( $model ) ) ) {
					return true;
				}
			}
		}

		return false;
	}
}

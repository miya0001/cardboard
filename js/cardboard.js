( function( $ ) {
	$( '.cardboard' ).each( function() {
		var width = $( this ).width(),
				height = $( this ).width() / 16 * 9;

		// scene
		var scene = new THREE.Scene();

		// mesh
		var geometry = new THREE.SphereGeometry( 5, 16, 12 );
		geometry.scale( -1, 1, 1 );
		var material = new THREE.MeshBasicMaterial( {
			 map: THREE.ImageUtils.loadTexture( $( this ).data( 'image' ) )
		} );
		var sphere = new THREE.Mesh( geometry, material );
		scene.add( sphere );

		// camera
		var camera = new THREE.PerspectiveCamera( 75, width / height, 1, 100 );
		camera.position.set( 0, 0, 0.1 );
		camera.lookAt( sphere.position );

		// render
		var renderer = new THREE.WebGLRenderer();
		renderer.setSize( width, height );
		renderer.setClearColor( { color: 0x000000 } );
		$( this ).append( renderer.domElement );
		renderer.render( scene, camera );

		// control
		var controls = new THREE.OrbitControls( camera, renderer.domElement );
		controls.minDistance = 0;
		controls.maxDistance = 5;

		var render = function() {
			requestAnimationFrame( render );
			sphere.rotation.y -= 0.05 * Math.PI / 180;
			renderer.render( scene, camera );
			controls.update();
		}
		render();
	} );
} )( jQuery );

( function( $ ) {
	$( '.cardboard' ).each( function() {
		var _self = this;
		var width = $( _self ).width(),
				height = $( _self ).width() / 16 * 9;

		// scene
		var scene = new THREE.Scene();

		// mesh
		var geometry = new THREE.SphereGeometry( 5, 32, 24 );
		geometry.scale( -1, 1, 1 );

		var texture;
		var video;
		var videoImageContext;
		var image = $( _self ).data( 'image' );
		if( image.indexOf('mp4') > -1 ) {
			video = document.createElement('video');
			video.src = image;
			video.load();
			video.loop = true;
			video.volume = 0;
			video.play();

			var videoImage = document.createElement('canvas');
			videoImage.width = 1600;
			videoImage.height = 900;

			videoImageContext = videoImage.getContext('2d');
			videoImageContext.fillStyle = '#000000';
			videoImageContext.fillRect(0, 0, videoImage.width, videoImage.height);

			//生成したcanvasをtextureとしてTHREE.Textureオブジェクトを生成
			texture = new THREE.Texture(videoImage);
			texture.minFilter = THREE.LinearFilter;
			texture.magFilter = THREE.LinearFilter;

		}
		else {
			var texloader = new THREE.TextureLoader();
			texture = texloader.load( image )
		}

		var material = new THREE.MeshBasicMaterial( {
			 map: texture,
			overdraw: true, side:THREE.DoubleSide
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
		$( _self ).append( renderer.domElement );
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
			//for video
			if (video && video.readyState === video.HAVE_ENOUGH_DATA) {
				videoImageContext.drawImage(video, 0, 0);
				if (texture) {
					texture.needsUpdate = true;
				}
			}
		}
		render();

		window.addEventListener( 'resize', function() {
			var width = $( _self ).width(),
					height = $( _self ).width() / 16 * 9;
			camera.aspect = width / height;
			camera.updateProjectionMatrix();

			renderer.setSize( width, height );
		}, false );
	} );
} )( jQuery );

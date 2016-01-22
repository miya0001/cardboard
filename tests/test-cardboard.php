<?php

class Cardboard_Test extends WP_UnitTestCase
{
	/**
	 * @test
	 */
	function is_panorama_photo()
	{
		$result = Cardboard::is_panorama_photo( dirname( __FILE__ ) . '/img/ricoh-theta-s.jpg' );
		$this->assertTrue( $result );

		$result = Cardboard::is_panorama_photo( dirname( __FILE__ ) . '/img/no-exif.jpg' );
		$this->assertFalse( $result );
	}
}
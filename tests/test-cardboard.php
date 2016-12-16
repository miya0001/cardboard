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

		$result = Cardboard::is_panorama_photo( dirname( __FILE__ ) . '/img/streetview.jpg' );
		$this->assertTrue( $result );

		$result = Cardboard::is_panorama_photo( dirname( __FILE__ ) . '/img/not-jpeg.png' );
		$this->assertFalse( $result );
	}

	/**
	 * @test
	 */
	function is_able_to_access_cardbord() {
		$attachment = $this->factory->attachment->create();
		$this->go_to( home_url( 'cardboard/'. $attachment ) );
		$this->assertFalse( is_404() );
	}

	/**
	 * @test
	 */
	function test_rewrite_endpoint() {
		global $wp_rewrite;
		$this->assertContains( array( EP_ROOT, Cardboard::QUERY_VAR, Cardboard::QUERY_VAR ) , $wp_rewrite->endpoints );
		Cardboard::remove_rewrite_endpoint();
		$this->assertNotContains( array( EP_ROOT, Cardboard::QUERY_VAR, Cardboard::QUERY_VAR ) , $wp_rewrite->endpoints );
	}
}

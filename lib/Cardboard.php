<?php

class Cardboard
{
	public static function is_panorama_photo( $image )
	{
		$models = array(
			'RICOH THETA',
			'Street View',
		);
		$models = apply_filters( 'cardboard_exif_models', $models );

		$exif = exif_read_data( $image );
		if ( $exif ) {
			foreach ( $models as $model ) {
				if ( false !== strpos( strtoupper( $exif['Model'] ), strtoupper( $model ) ) ) {
					return true;
				}
			}
		}

		$content = file_get_contents( $image );
		$xmp_data_start = strpos( $content, '<x:xmpmeta' );
		$xmp_data_end   = strpos( $content, '</x:xmpmeta>' );
		$xmp_length     = $xmp_data_end - $xmp_data_start;
		if ( $xmp_length ) {
			$xmp_data = substr( $content, $xmp_data_start, $xmp_length + 12 );
			foreach ( $models as $model ) {
				if ( false !== strpos( strtoupper( $xmp_data ), strtoupper( $model ) ) ) {
					return true;
				}
			}
		}

		return false;
	}
}

<?php

class Cardboard
{
	public static function is_panorama_photo( $image )
	{
		$exif = exif_read_data( $image );
		if ( $exif ) {
			$models = array(
				'RICOH THETA',
			);
			$models = apply_filters( 'cardboard_exif_models', $models );
			foreach ( $models as $model ) {
				if ( false !== strpos( strtoupper( $exif['Model'] ), strtoupper( $model ) ) ) {
					return true;
				}
			}
		}
		return false;
	}
}

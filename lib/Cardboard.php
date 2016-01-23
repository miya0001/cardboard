<?php

class Cardboard
{
	const NS = 'http://ns.google.com/photos/1.0/panorama/';
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
		if ( $exif ) {
			foreach ( $models as $model ) {
				if ( false !== strpos( strtoupper( $exif['Model'] ), strtoupper( $model ) ) ) {
					return true;
				}
			}
		}

		return false;
	}
}

<?php
// ******************************************************
//
// Remover des contenus
//
// ******************************************************

class WPComponent__utility
{

    // ============================================================
    // Load Templates
    // ============================================================

	public function get_file_data( $file ) {
		// We don't need to write to the file, so just open for reading.
		$fp = fopen( $file, 'r' );

		// Pull only the first 8kiB of the file in.
		$file_data = fread( $fp, 8192 );

		// PHP will close file handle, but we are good citizens.
		fclose( $fp );

		// Make sure we catch CR-only line endings.
		$file_data = str_replace( "\r", "\n", $file_data );

		$string = trim(preg_replace('/\s+/', ' ', $file_data));

		$jsons = [];

		$res = preg_match_all('~\{(?:[^{}]|(?R))*\}~', $string, $matches);

		if( !empty($matches[0]) ){

			foreach ($matches[0] as $key => $res){
				if( $this->isJSON($res) ):
					$jsons[] = $res;
				endif;
			}

			return $jsons;
		}

	}

	public function isJSON($string){
		return is_string($string) && is_object(json_decode($string)) ? true : false;
	}	

}
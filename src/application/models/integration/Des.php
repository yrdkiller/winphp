<?php
/************************************************************
 *  @Author: yrd 
 *  @Create Time: 2013-07-08
 *************************************************************/
class Des {
	public static function encrypt($str, $key = null) {
		global $CONFIG;
		if($key === null) {
			$key = $CONFIG["DES_DEFAULt_KEY"];
		}

		$size = mcrypt_get_block_size( MCRYPT_DES, MCRYPT_MODE_CBC );
		$str = self::pkcs5Pad( $str, $size );
		return mcrypt_cbc(MCRYPT_DES, $key, $str, MCRYPT_ENCRYPT, $key);
	}

	public static function decrypt($str, $key = null) {
		global $CONFIG;
		if($key === null) {
			$key = $CONFIG["DES_DEFAULt_KEY"];
		}

		$str = mcrypt_cbc(MCRYPT_DES, $key, $str, MCRYPT_DECRYPT, $key);
		$str = self::pkcs5Unpad($str);
		return $str;
	}

	private static function pkcs5Pad($text, $blocksize) {
		$pad = $blocksize - (strlen( $text ) % $blocksize);
		return $text . str_repeat( chr( $pad ), $pad );
	}

	private static function pkcs5Unpad($text) {
		$pad = ord( $text {strlen( $text ) - 1} );
		if($pad > strlen( $text ))
			return false;
		if(strspn( $text, chr( $pad ), strlen( $text ) - $pad ) != $pad)
			return false;
		return substr( $text, 0, - 1 * $pad );
	}

}


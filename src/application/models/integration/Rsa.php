<?php
/************************************************************
 *  @Author: yrd 
 *  @Create Time: 2013-07-08
 *************************************************************/
class Rsa {
	public static function encrypt($content, $key=null) {
		if($key === null) {
			$key = self::getKey('PUBLIC');
		}
		openssl_public_encrypt($content, $encrypted, $key);
		return base64_encode($encrypted);
	}

	public static function decrypt($content, $key=null) {
		if($key === null) {
			$key = self::getKey('PRIVATE');
		}
		$encrypted = base64_decode($content);
		openssl_private_decrypt($encrypted, $decrypted, $key);
		return $decrypted;
	}

	private static function getKey($key) {
		global $CONFIG;
		return file_get_contents($CONFIG["RSA_{$key}_KEY"]);
	}
}


<?php
class Multi {
	const TIMEOUT = 5;
	public static function getInfo($urls) {
		if(empty($urls)) {
			return ;
		}
		$conn = array();
		$mh = curl_multi_init();
		foreach($urls as $i=>$url) {
			$conn[$i] = curl_init();
			curl_setopt($conn[$i], CURLOPT_URL, $url);
			curl_setopt($conn[$i], CURLOPT_MAXREDIRS, 10);
			curl_setopt($conn[$i], CURLOPT_TIMEOUT, self::TIMEOUT);
			curl_setopt($conn[$i], CURLOPT_RETURNTRANSFER, true);
			curl_multi_add_handle($mh, $conn[$i]);
		}

		$active = null;
		do {
			$status = curl_multi_exec($mh, $active);
		} while($status == CURLM_CALL_MULTI_PERFORM || $active);

		$ret = array();
		foreach($urls as $i => $url) {
			$ret[$i] = curl_multi_getcontent($conn[$i]);
			curl_close($conn[$i]);
		}
		curl_multi_close($mh);
		return $ret;
	}

	public static function getOne($url) {
		$info = "";
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_TIMEOUT, self::TIMEOUT);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_exec($ch);
		if(!curl_errno($ch)) {
			$info = curl_multi_getcontent($ch);
		}
		curl_close($ch);
		return $info;
	}
}
?>

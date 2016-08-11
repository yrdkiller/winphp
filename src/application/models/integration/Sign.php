<?php
class Sign {
	public static function checkSign($signParam, $sec_key) {
		global $CONFIG;
		if(empty($signParam) || !isset($signParam['sign'])) {
			return false;
		}
		$sign = trim($signParam['sign']);
		unset($signParam['sign']);
		ksort($signParam);
		$signStr = http_build_query($signParam);
		if(!empty($signParam["sk"]) && $CONFIG["IS_TEST"]) {
			echo "<!--";
			echo md5($signStr.$sec_key);
			echo "-->";
			echo "\n";
		}
		if(md5($signStr.$sec_key) == $sign) {
			return true;
		}
		return false;
	}

	public static function getSign($signParam, $sec_key) {
		if(empty($signParam)) {
			return ;
		}
		ksort($signParam);
		$signStr = http_build_query($signParam);
		return md5($signStr.$sec_key);
	}

}
?>

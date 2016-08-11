<?php
/**
$path = "/data/ota_files/build";
$info = Mydir::getFiles($path);
print_r($info);
/***/

class Mydir {
	public static function getFiles($path) {
		if(!is_dir($path)) {
			return false;
		}
		$ret = array();
		$info = `tree -if {$path}`;
		$tmp = explode("\n", $info);
		foreach($tmp as $val) {
			if(!is_file($val)) {
				continue;
			}
			$ret[] = trim($val, $path);
		}
		return $ret;
	}
}
?>

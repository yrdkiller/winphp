<?php
/**
 * 自动添加 ota 信息脚本。
 * by 好运来 2014-10-19
 * */
include("/home/q/system/demo/src/include/config.inc.php");
include("/home/q/system/demo/src/include/auto_load.php");

$files = Mydir::getFiles($CONFIG['BUILD_DIR']);
if(empty($files)) {
	return;
}

//echo "\n==============START=============\n";
$month = date("Ym");
chdir($CONFIG['STORE_DIR']);
$param = array();
foreach($files as $val) {
	$val = trim($val);
	$info = getInfo($val);
	if(!$info) {
		continue;
	}
	$ver = $info["version_code"];
	$param[$ver]["p"] = $info["channel"];
	$param[$ver]["ver"] = $ver;
	if(!empty($info["user"])) {
		$param[$ver]["user"] = $CONFIG["BUILD_DIR"].$info["user"];
	} else {
		$param[$ver]["debug"] = $CONFIG["BUILD_DIR"].$info["debug"];
	}
}

// print_r($param);
// echo count($param);

if(empty($param) || count($param) < 1) {
	return;
}

foreach($param as $item) {
	$item["make_ota"] = 1;
	$sign = Sign::getSign($item, "ota-new");
	$url = http_build_query($item);
	$url .= "&sign={$sign}";
	//echo "php /home/q/system/demo/src/tools/files_one.php \"{$url}\"\n";
	`php /home/q/system/demo/src/tools/files_one.php "{$url}"`;
}

//echo "\n===============END==============\n";

function getInfo($file) {
	$path = trim($file, '/ ');
	$info = explode('/', $path);
	if(empty($info) || count($info) != 4) {
		return false;
	}
	if($info[3] == "info.log") {
		return false;
	}
	$ret = array();
	$ret["product"] = $info[0];
	$ret["channel"] = $info[1];
	$ret["version_code"] = $info[2];
	if(stripos($info[3], "userdebug") !== false) {
		$ret["debug"] = $file;
	} else {
		$ret["user"] = $file;
	}
	return $ret;
}
?>

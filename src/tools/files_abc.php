<?php
/************************************************************
 * 添加OTA信息
 *  @copyright(c): 2014
 *  @Author: yrd
 *  @Create Time: 2014-10-29
 *  @FileName: ota_one.php
 *************************************************************/

include("/home/q/system/demo/src/include/auto_load.php");
$query = trim($argv[1]);
if(empty($query)) {
	return;
}

$_SERVER["REQUEST_URI"] = "/files/abc?{$query}";
include("/home/q/system/demo/src/www/index.php");
?>

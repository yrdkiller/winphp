<?php
date_default_timezone_set('Asia/Chongqing');
set_include_path("/home/q/php/:/home/q/system/demo/config/:/home/q/system/demo/src/www/:.");
chdir("/home/q/system/demo/src/www/");

//include_once('/home/q/system/xhprof/prepend.php');

$ROOT = dirname(__FILE__);
list($nameUri,$queryStr) = @explode("?" , $_SERVER['REQUEST_URI']);
$_SERVER['SCRIPT_NAME'] = $nameUri;
$_SERVER['PHP_SELF'] = $nameUri;
if(!empty($queryStr)) {
	parse_str($queryStr, $params);
	$_GET = array_merge($params, $_GET);
}

require_once "/home/q/system/demo/src/include/config.inc.php";
require_once "/home/q/system/demo/src/include/functions.inc.php";
require_once "/home/q/system/demo/src/include/auto_load.php";

$str = trim($_SERVER["SCRIPT_NAME"], "/ ");
$tmp = explode("/", $str);
$num = count($tmp);
if($num == 2) {
	$controller = ucfirst($tmp[0])."Controller";
	if(class_exists($controller)) {
		$action = $tmp[1]."Action";
		$oo = new $controller();
		if(method_exists($oo, $action)) {
			$oo->$action();
		}
	}
}

?>

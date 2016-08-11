<?php
$dir = dirname(__FILE__);

if($a = is_test()) {
	include("{$dir}/server/server_conf.{$a}.php");
} else {
	include("{$dir}/server/server_conf.release.php");
}

$arr_master_config = array(
	"driver"=>"mysql",
	"port"=> $CONFIG["WIN_MASTER_PORT"],
	"host"=> $CONFIG["WIN_MASTER_HOST"], //主机IP
	"username"=> $CONFIG["WIN_MASTER_USERNAME"],
	"password"=> $CONFIG["WIN_MASTER_PASSWORD"],
	"database"=> $CONFIG["WIN_MASTER_DATABASE"],
	"charset"=>"utf8",
	"unix_socket"=>"",
	"options"=>array()
);

$arr_slave_config = array(
	"driver"=>"mysql",
	"port"=> $CONFIG["WIN_PORT"],
	"host"=> $CONFIG["WIN_HOST"], //主机IP
	"username"=> $CONFIG["WIN_USERNAME"],
	"password"=> $CONFIG["WIN_PASSWORD"],
	"database"=> $CONFIG["WIN_DATABASE"],
	"charset"=>"utf8",
	"unix_socket"=>"",
	"options"=>array()
);

function is_test() {
	$uname = php_uname("n");
	if(stripos($uname, "test") !== false) {
		return "test";
	}
	return false;
}

?>

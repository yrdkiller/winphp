<?php
/************************************************************
 * 服务状态监控程序
 *  @copyright(c): yrdwin.com 
 *  @Author: yrd
 *  @Create Time: 2012-9-2
 *  @FileName: check.php
 *************************************************************/
ini_set('display_errors', 0);

$ROOT = realpath(dirname(__FILE__)."/..");

set_include_path("/home/q/php/:{$ROOT}:.");  //intf
require_once("{$ROOT}/include/auto_load.php");
require_once("{$ROOT}/include/config.inc.php");

$CHECK= array(
	"mysql"	=> array($arr_master_config,$arr_slave_config),
	"tt"	=> array(array("ttserver_intf.yrdwin.com", "1980")),
	"memcache"	=> array(array("localhost", "11213"),array("localhost", "11214")),
	"search"	=> array(array("search_intf.yrdwin.com", "8360")),
	"suggest"	=> array(array("search_intf.yrdwin.com", "8989")),
);

$status = 'OK';
$res = array();

foreach($CHECK as $k=>$item) {
	$a = "check{$k}";
	foreach($item as $kk) {
		if($k == "mysql") {
			$kk["host"] = gethostbyname($kk["host"]);
			$ret = $a($kk);
		} else {
			$host = gethostbyname($kk[0]);
			$ret = $a($host, $kk[1]);
		}
		$res["DATA"][] = $ret;
		if($ret["REV"] == "FAILED") {
			$status = 'FAILED';
		}
	}
}

$res['REV'] = $status;

if(1 == $_REQUEST['debug']) {
	print_r($res);
} else {
	echo serialize($res);
}

function checkSearch($host, $port) {
	$name = '小鸟';
	$name = mb_convert_encoding($name, 'gbk','utf-8,auto');
	$os = 2;
	$start = 0;
	$count = 10;
	$market = "mobile_safe";
	$kw = "";
	$kw .= " (name|".$name.") or (tag|".$name.") or (brief|".$name.")";
	$kw .= " (os|".$os.")";
	$kw .= " (market|".$market.")";
	$kw = urlencode(trim($kw));
	$url = "http://{$host}:{$port}/search.htm?kw={$kw}&filter=datatype:=1;osver:<16;&start={$start}&count={$count}&maxview=2000";
	$dat = getUrl($url);
	$dat = unserialize( $dat);

	if($dat['total']>0) {
		$ret['REV'] = 'OK';
	} else {
		$ret['REV'] = 'FAILED';
	}

	$ret['search'] = "{$host}:{$port}";

	return $ret;
}

function checkSuggest($host, $port) {
	$name = "360";
	$os = 2;
	$start = 0;
	$count = 5;
	$market = "modile_safe";

	$args = "word=".$name;
	$args .= "&category=".$os;
	$args .= "&count=".$count;
	$args .= "&redmark=0";	

	$url = "http://{$host}:{$port}/suggest/openbox?".$args;

	$dat = getUrl($url);

	if(!empty($dat)) {
		$ret['REV'] = 'OK';
	} else {
		$ret['REV'] = 'FAILED';
	}

	$ret['suggest'] = "{$host}:{$port}";

	return $ret;
}

function checkMemcache($host,$port) {
	$memHandle = new Memcached();
	$memHandle->addServer($host, $port);
	$memHandle->set("monitor", "monitor", 10);

	if("monitor" == $memHandle->get("monitor")) {
		$ret['REV'] = 'OK';
	} else {
		$ret['REV'] = 'FAILED';
	}

	$ret['memcache'] = "{$host}:{$port}";

	return $ret;
}

function checkTT($host,$port) {
	$memHandle = new TokyoTyrant($host, $port);
	$memHandle->put("monitor", "monitor");

	if("monitor" == $memHandle->get("monitor")) {
		$ret['REV'] = 'OK';
	} else {
		$ret['REV'] = 'FAILED';
	}

	$ret['ttserver'] = "{$host}:{$port}";

	return $ret;
}

function checkMysql($arr_master_config) {
	$db = QFrameDB::getInstance($arr_master_config);
	$sql = "select 1";
	$aa = $db->getRow($sql,null,false);
	if($aa["1"] == "1") {
		$ret['REV'] = 'OK';
	} else {
		$ret['REV'] = 'FAILED';
	}
	$ret['mysql'] = "{$arr_master_config['host']}:{$arr_master_config['port']}";
	return $ret;
}

function getUrl($url) {
	$info = "";
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_exec($ch);
	if(!curl_errno($ch)) {
		$info = curl_multi_getcontent($ch);
	}
	curl_close($ch);
	return $info;
}



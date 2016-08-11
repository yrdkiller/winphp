<?php
function getParam($key,$def="") {
	global $_GET, $_POST;
	if(isset($_GET[$key])) {
		$val = $_GET[$key];
	} else if(isset($_POST[$key])) {
		$val = $_POST[$key];
	} else {
		$val = $def;
	}
	return $val;
}
function getDB($w=false) {
	global $arr_master_config, $arr_slave_config;
	if($w) {
		$db = QFrameDB::getInstance($arr_master_config);
	} else {
		$db = QFrameDB::getInstance($arr_slave_config);
	}
	return $db;
}

function notEmpty($a) {
	if(empty($a) && $a !== 0 && $a !== "0") {
		return false;
	}
	return true;
}
/**
 * $ret["soft"] = array(array("abc"=>123,"xx"=>"ss"), array("aa"=>"mm","mm"=>"ww"));
 * $arr["softs"] = $ret;
 * $xml = toXML($arr);
 * echo $xml;
 * */
function toXML($arr) {
	$str = '<?xml version="1.0" encoding="UTF-8"?>';
	if(!empty($arr)) {
		$str .= _toXML($arr);
	}
	return $str;
}
function _toXML($arr,$p="") {
	$str = "";
	if(!empty($arr)) {
		$i = 0;
		$j = count($arr);
		foreach($arr as $k=>$v) {
			$i++;
			if(is_array($v)) {
				if(is_int($k)) {
					$s = _toXML($v);
					$str .= "<{$p}>{$s}</{$p}>";
				} else {
					$s = _toXML($v,$k);
					if(!empty($p) && $i==$j ) {
						$str = "<{$p}>{$str}{$s}</{$p}>";
					} else {
						$str .= $s;
					}
				}
			} else {
				$s = "<![CDATA[{$v}]]>";
				$str .= "<{$k}>{$s}</{$k}>";
			}
		}
	}
	return $str;
}


/** 
* 压缩html : 清除换行符,清除制表符,去掉注释标记 
* @param $string 
* @return 压缩后的$string 
* */ 
function compress_html($string) {
	$string = str_replace("\r\n", '', $string); //清除换行符 
	$string = str_replace("\n", '', $string); //清除换行符 
	$string = str_replace("\t", '', $string); //清除制表符 
	$pattern = array ( 
	"/> *([^ ]*) *</", //去掉注释标记 
	"/[\s]+/", 
	"/<!--[^!]*-->/", 
	//"/\" /", 
	//"/ \"/", 
	"'/\*[^*]*\*/'" 
	); 
	$replace = array ( 
	">\\1<", 
	" ", 
	"", 
	//"\"", 
	//"\"", 
	"" 
	); 
	return preg_replace($pattern, $replace, $string); 
}
  // 存储单位转换,输入为字节数
function tranlateSize( $size ,$count=2){
	if($size <1024 && $size >0){
		$appSize = $size."B";
	}elseif($size <1024*1024){
		$appSize = round($size/1024 , $count)."KB";
	}elseif($size <1024*1024*1024){
		$appSize = round($size/(1024*1024) , $count)."MB";
	}else{
		$appSize = "未知";
	}
	return $appSize;
}

/**
 * 请求url并返回其内容
 * */
function fetch_url($url, $timeout=1) {
	$info = "";
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_exec($ch);
	if(!curl_errno($ch)) {
		$info = curl_multi_getcontent($ch);
	}
	curl_close($ch);
	return $info;
}

?>

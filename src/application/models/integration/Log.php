<?php
/************************************************************
 *  @Author: yrd 
 *  @Create Time: 2013-07-08
 *************************************************************/
class Log {
	public static function append($msg, $type) {
		global $CONFIG;
		$day = date("Y-m-d");
		$path = $CONFIG['LOGSPATH'];
		$file = "{$path}{$type}.log.{$day}";
		if(is_array($msg)) {
			$msg = print_r($msg, true);
		}
		$dt = date("Y-m-d H:i:s");
		error_log("Time:[{$dt}]\t{$msg}\n", 3, $file);
	}
}


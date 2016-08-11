<?php
/************************************************************
 *  @Author: yrd 
 *  @Create Time: 2013-07-08
 *************************************************************/
class BaseAction {

	//@todo 权限验证写这里
	public function init() {/*{{{*/
	}/*}}}*/

	public function getParams($t,$v="") {
		$var = getParam($t, $v);
		$var = filter_var($var, FILTER_SANITIZE_STRING);
		return $var;
	}

	public function FromFiled($obj) {/*{{{*/
		$vals = $obj->getVals();
		foreach($vals as $key=>$val) {
			$obj->$key = trim($this->getParams($key, $val));
		}
		return $obj;
	}/*}}}*/

	public function trace($type=-1, $data="", $msg) {/*{{{*/
		$bc = trim(getParam("bc", ""));
		$arr = array(
			"errno"=>$type,
			"msg"=>$msg,
			"data"=>$data,
		);
		if(!empty($bc)) {
			echo "{$bc}(".json_encode($arr).");";
		} else {
			echo json_encode($arr);
		}
		exit();
	}/*}}}*/

	public function microTimeFloat() {/*{{{*/
		list($usec, $sec) = explode(" ", microtime());
		return ((float)$sec + (float)$usec);
	}/*}}}*/

}

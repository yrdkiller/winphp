<?php
/************************************************************
 *  @Author: yrd 
 *  @Create Time: 2013-07-08
 *************************************************************/
class CheckFrom {

	public static function checkIdNumber($idnumber) {/*{{{*/
		if(empty($idnumber)) {
			return false;
		}
		$vCity = array(
			'11','12','13','14','15','21','22',
			'23','31','32','33','34','35','36',
			'37','41','42','43','44','45','46',
			'50','51','52','53','54','61','62',
			'63','64','65','71','81','82','91'
		);
		if(!preg_match('/^([\d]{17}[xX\d]|[\d]{15})$/i', $idnumber)) {
			return false;
		}
		if(!in_array(substr($idnumber, 0, 2), $vCity)) {
			return false;
		}
		$idnumber = preg_replace('/[xX]$/i', 'a', $idnumber);
		$vLength = strlen($idnumber);
		if($vLength == 18) {
			$vBirthday = substr($idnumber, 6, 4) . '-' . substr($idnumber, 10, 2) . '-' . substr($idnumber, 12, 2);
		} else {
			$vBirthday = '19' . substr($idnumber, 6, 2) . '-' . substr($idnumber, 8, 2) . '-' . substr($idnumber, 10, 2);
		}
		if(date('Y-m-d', strtotime($vBirthday)) != $vBirthday) {
			return false;
		}
		if($vLength == 18) {
			$vSum = 0;
			for($i = 17 ; $i >= 0 ; $i--) {
				$vSubStr = substr($idnumber, 17 - $i, 1);
				$vSum += (pow(2, $i) % 11) * (($vSubStr == 'a') ? 10 : intval($vSubStr , 11));
			}
			if($vSum % 11 != 1) {
				return false;
			}
		}
		return true;
	}/*}}}*/
	public static function checkMobile($mobile) {/*{{{*/
		if(empty($mobile)) {
			return false;
		}
		if(preg_match("/^1[3458]{1}[0-9]{9}$/", $mobile)) {
			return true;
		}
		return false;
	}/*}}}*/
	public static function checkName($fullname) {/*{{{*/
		if(empty($fullname)) {
			return false;
		}
		$len = mb_strlen($fullname);
		if($len < 2 || $len > 20) {
			return false;
		}
		return true;
	}/*}}}*/
	public static function checkEmail($email) {/*{{{*/
		if(empty($email)) {
			return false;
		}
		if(filter_var($email, FILTER_VALIDATE_EMAIL)) {
			return true;
		}
		return false;
	}/*}}}*/
	public static function checkUserType($usertype, $qid) {/*{{{*/
		if($usertype == 0) {
			return 1;
		}
		if(empty($qid)) {
			return 0;
		}
		$stat = User::getPrivilege($qid);
		return $stat;
	}/*}}}*/
	public static function checkCaptcha($captcha) {/*{{{*/
		if(__ENV__=="test" && $captcha=="88xx") {
			return true;
		}
		if(!empty($captcha)) {
			include_once('qcaptchaclient/qcaptcha.php');
			$q = new QCaptcha('baoxian');
			$r = $q->verifycode($captcha);
			if ($r['errno'] == '0') {
				return true;
			}
		}
		return false;
	}/*}}}*/
	public static function checkCredit($credit) {/*{{{*/
		if($credit == 1) {
			return true;
		}
		return false;
	}/*}}}*/
	public static function checkImei($imei) {/*{{{*/
		if(empty($imei) || $imei == "null") {
			return false;
		}
		if(preg_match("/^[0-9a-z]+$/i", $imei)) {
			return true;
		}
		return false;
	}/*}}}*/
	public static function checkCpu($cpu) {/*{{{*/
		if(empty($cpu) || $cpu == "null") {
			return false;
		}
		if(preg_match("/^[0-9a-z]+$/i", $cpu)) {
			return true;
		}
		return false;
	}/*}}}*/
	public static function checkProductCode($product) {/*{{{*/
		if(!empty($product)) {
			return true;
		}
		return false;
	}/*}}}*/

}

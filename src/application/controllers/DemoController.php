<?php
class DemoController {
	public function indexAction() {
		echo "welcome";
	}

	public function testAction() {
		$testInfo = "hello! Install test";
		$renew = $this->getParams("renew", 0);
		$param = $this->getParams("param", "");
		$sign  = $this->getParams("sign", "");
		$sec_key = SecretModel::getSecret($param);
		if(empty($param) || !Sign::checkSign($_GET, $sec_key)) {
			echo "-1";
			return;
		}
		$mm = getMM();
		$key = "demo_test_{$param}";
		if(!$renew) {
			// 主动缓存模式，只从缓存中获取数据，不被动创建缓存，效率更高，性能有保障.
			// 缓存更新方法：demo/src/tools/crontab_demo.php
			$dat = $mm->get($key);
			echo $dat;
			return;
		}
		$data = DemoModel::test($param);
		$arr = array(
			"aa" => $testInfo,
			"bb" => time(),
			"data" => $data,
		);
		$dat = json_encode($arr);
		echo $dat;
		$mm->set($key, $dat);
	}

	public function getMM($type=4) {
		return Cache::getInstance($type);
	}

}

?>

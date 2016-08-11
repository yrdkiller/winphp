<?php
class SecretModel extends BaseModel {
	private $data = null;
	const EXP_TIME = 86400;
	const ALL_DATA_KEY = "secret_all";

	public function __construct() {
		parent::__construct("demo_product", "id");
	}

	public static function getInstance() {
		static $nm = null;
		if($nm == null) {
			$nm = new SecretModel();
		}
		return $nm;
	}

	public function initData() {
		$ret = $this->getAll();
		if(empty($ret)) {
			return;
		}
		$arr = array();
		foreach($ret as $v) {
			$arr[$v["channel_id"]] = $v;
		}
		$this->data = $arr;
	}

	public function getData() {
		if($this->data != null) {
			return $this->data;
		}
		$mm = $this->getMM();
		$dat = $mm->get(self::ALL_DATA_KEY);
		if(!empty($dat)) {
			$this->data = json_decode($dat, true);
			return $this->data;
		}
		$this->initData();
		$dat = json_encode($this->data);
		$mm->set(self::ALL_DATA_KEY, $dat, self::EXP_TIME);
		return $this->data;
	}

	public function getMM($type=4) {
		return Cache::getInstance($type);
	}

	public static function getSecret($product) {
		$xx = self::getInstance();
		$data = $xx->getData();
		if(!isset($data[$product])) {
			return;
		}
		$dat = $data[$product];
		return $dat["secret_key"];
	}


}
?>

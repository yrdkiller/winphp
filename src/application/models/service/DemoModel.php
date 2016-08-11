<?php
class DemoModel extends BaseModel {
	private $data = null;
	function __construct() {
		parent::__construct("demo", "id");
	}

	public static function getInstance() {
		static $nm = null;
		if($nm == null) {
			$nm = new DemoModel();
		}
		return $nm;
	}

	private function test($sk) {
		$store  = new DemoDao();
		$store->sk = $sk;
		$this->loadByKey($store);
		return $store;
	}

	public function store($sk, $val, $tok, $product) {
		$now = date("Y-m-d H:i:s");
		$dao = $this->getData($sk);
		$dao->tok = $tok;
		$dao->val = $val;
		$dao->product = $product;
		$dao->update_time = $now;
		$this->data[$sk] = $dao;
		if(empty($dao->id)) {
			$dao->create_time = $now;
			return $this->add($dao);
		}
		return $this->update($dao);
	}


}
?>

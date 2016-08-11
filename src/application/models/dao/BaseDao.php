<?php
class BaseDao {
	public function getVals() {
		return get_object_vars($this);
	}

	public function autoRepair() {
	}

	public function getEliteVals() {
		$this->autoRepair();
		$vals = $this->getVals();
		$arr = array();
		if(empty($vals)) {
			return $arr;
		}
		foreach($vals as $k=>$v) {
			if(!empty($v)) {
				$arr[$k] = $v;
			}
		}
		return $arr;
	}

	public function paseVal($data) {
		if(empty($data) || !is_array($data)) {
			return;
		}
		$vals = $this->getVals();
		foreach($vals as $k=>$v) {
			if(!empty($data[$k])) {
				$this->$k = $data[$k];
			}
		}
	}

	public function paseJson($json) {
		if(empty($json)) {
			return;
		}
		$data = json_decode($json, true);
		$this->paseVal($data);
	}

}


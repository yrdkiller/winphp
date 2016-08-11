<?php
/************************************************************
 *  @Author: yrd 
 *  @Create Time: 2013-07-08
 *************************************************************/
class BaseModel {
	protected $db;
	protected $table;
	protected $p_key;
	public function __construct($table, $p_key) {
		// 数据库初始化
		global $arr_master_config;
		$this->table = $table;
		$this->p_key = $p_key;
		$this->db = QFrameDB::getInstance($arr_master_config);
	}

	public function load($obj) {
		$p_key = $this->p_key;
		$sql = "SELECT * FROM {$this->table} WHERE {$p_key}=?";
		$ret = $this->db->getRow($sql, $obj->$p_key);
		if(empty($ret)) {
			return false;
		}
		$vals = $obj->getVals();
		foreach($vals as $key=>$val) {
			if(isset($ret[$key])) {
				$obj->$key = $ret[$key];
			}
		}
		return true;
	}

	public function loadByKey($obj, $ext_where="", $orderby="") {
		$arr = $obj->getVals();
		$keys = $vals = array();
		foreach($arr as $key=>$val) {
			if(!empty($val)) {
				$keys[] = $key;
				$vals[] = $val;
			}
		}
		$where = join("=? AND ", $keys);
		$where .= empty($where) ? "" : "=?";
		$where = ( (empty($where)&&empty( $ext_where)) ? "" : "WHERE ({$where} {$ext_where})" );
		if(empty($orderby)) {
			$orderby = "{$this->p_key} DESC";
		}
		$sql = "SELECT * FROM {$this->table} {$where} ORDER BY {$orderby} LIMIT 1";
		$ret = $this->db->getRow($sql, $vals);
		if(empty($ret)) {
			return false;
		}
		reset($arr);
		foreach($arr as $key=>$val) {
			if(isset($ret[$key])) {
				$obj->$key = $ret[$key];
			}
		}
		return true;

	}

	public function getList($arr, $start=0, $num=20) {
		if(empty($arr["column"])) {
			$arr["column"] = "*";
		}
		$where = ( empty($arr["where"]) ? "" : "{$arr['where']}" );
		$sql = "SELECT {$arr['column']} FROM {$this->table}"
			. "\n {$where} {$arr['order']}"
			. "\n LIMIT {$start}, {$num}";
		$ret = $this->db->getAll($sql, $arr["value"]);
		return $ret;
	}

	public function getNum($status="") {
		$sql = "SELECT COUNT(1) num FROM {$this->table}";
		if(!empty($status)) {
			$sql .= " WHERE status=?";
		}
		$ret = $this->db->getRow($sql, $status);
		return $ret["num"];
	}

	public function check($arr) {
		$sql = "SELECT status FROM {$this->table} WHERE ";
		$where = array();
		foreach($arr as $key=>$val) {
			$where[] = "{$key} = ?";
		}
		$sql .= join(" AND ", $where);
		$ret = $this->db->getRow($sql, array_values($arr));
		return $ret["status"];
	}

	public function update($obj) {
		try{
			$p_key = $this->p_key;
			$arr = $obj->getVals();
			$ret = $this->db->update($this->table, $arr, "{$p_key} = ?", $obj->$p_key);
			return $ret;
		} catch(Exception $e) {
			Log::append($e, "update_{$this->table}");
		}
		return false;
	}

	public function add($obj) {
		try{
			$arr = $obj->getVals();
			$ret = $this->db->insert($this->table, $arr);
			return $ret;
		} catch(Exception $e) {
			Log::append($e, "insert_{$this->table}");
		}
		return false;
	}

	public function getAll($arr=array()) {
		if(empty($arr["column"])) {
			$arr["column"] = "*";
		}
		$where = ( empty($arr["where"]) ? "" : "{$arr['where']}" );
		$order = ( empty($arr["order"]) ? "" : "{$arr['order']}" );
		$value = ( empty($arr["value"]) ? "" : "{$arr['value']}" );
		$sql = "SELECT {$arr['column']} FROM {$this->table}"
			. "\n {$where} {$order}";
		$ret = $this->db->getAll($sql, $value);
		return $ret;
	}

}



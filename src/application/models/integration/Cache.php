<?php
class Cache {
	const TMP_CACHE = 600;
	private $tt;
	private $mm;
	private $fc;
	private $rd;
	private $tp; // 0: 默认  1: memcache    2: file    3:  tt+mem    4:  redis

	private function __construct($tp) {
		$this->tp = $tp;
		switch($tp) {
		case 4:
			$this->rd = $this->getRD();
		case 1:
			$this->mm = $this->getMM();
			break;
		case 3:
			$this->tt = $this->getTT();
			break;
		case 2:
			$this->fc = FileCache::getInstance();
			break;
		default:
			$this->mm = $this->getMM();
			$this->rd = $this->getRD();
		}
	}

	private function _close() {
		try {
			$this->mm->close();
			$this->rd->close();
		} catch(Exception $e) {
		}
	}

	public static function close() {
		$c =  self::getInstance(1);
		$c->_close();
	}

	public static function getInstance($type) {
		$rc = null;
		try {
			switch($type) {
			case 1:
				static $mc = null;
				if($mc == null) {
					$mc = new Cache($type);
				}
				$rc = $mc;
				break;
			case 4:
				static $rd = null;
				if($rd == null) {
					$rd = new Cache($type);
				}
				$rc = $rd;
				break;
			case 3:
				static $tc = null;
				if($tc == null) {
					$tc = new Cache($type);
				}
				$rc = $tc;
				break;
			case 2:
				static $fc = null;
				if($fc == null) {
					$fc = new Cache($type);
				}
				$rc = $fc;
				break;
			default:
				static $cc = null;
				if($cc == null) {
					$cc = new Cache(0);
				}
				$rc = $cc;
			}
		} catch(Exception $e) {
			error_log("connect:".$e);
		}
		return $rc;
	}

    public static function getKey($key, $pix="k") {
		if(is_array($key)) {
            $key = http_build_query($key);
        }
        $key = trim(strip_tags($key));
        if(empty($key)) {
            return false;
        }
        if(strlen($key) > 32) {
            $key = md5($key);
        }
        $key = "{$pix}_{$key}";
        return $key;
    }

	public function set($key, $val, $time=0) {
		$key = str_replace(" ", "", $key);
		switch($this->tp) {
		case 3:
			$this->tt->put($key, $val);
		case 1:
			$this->mm->set($key, $val, $time);
			break;
		case 4:
			$this->rd->set($key, $val);
			if($time!=0) {
				$this->rd->expireAt($key, time()+$time);
			}
			break;
		case 2:
			$this->fc->set($key, $val);
			break;
		default:
			$this->mm->set($key, $val, $time);
			$this->rd->set($key, $val);
			if($time!=0) {
				$this->rd->expireAt($key, time()+$time);
			}
		}
	}

	public function get($key) {
		$key = str_replace(" ", "", $key);
		switch($this->tp) {
		case 1:
			$dat = $this->mm->get($key);
			break;
		case 4:
			$dat = $this->rd->get($key);
			break;
		case 2:
			$dat = $this->fc->get($key);
			break;
		case 3:
			$dat = $this->tt->get($key);
			break;
		default:
			$dat = $this->mm->get($key);
			if(empty($dat)) {
				$dat = $this->rd->get($key);
			}
		}
		return $dat;
	}

	public function getFlag($key,$cachetime){
		$key = str_replace(" ", "", $key);
		if($this->tp == 2) {
			$this->fc->isexpired($key,$cachetime);
		} else {
			return false;
		}
	}

	public function setMulti($data, $time=0) {
		switch($this->tp) {
		case 4: //避免多 key 问题
		case 2:
			foreach($data as $k=>$v) {
				$this->set($k, $v, $time);
			}
			break;
		case 3:
			foreach($data as $k=>$v) {
				$k = str_replace(" ", "", $k);
				$this->tt->put($k, $v);
			}
		case 1:
		default:
			$this->mm->setMulti($data, $time);
			break;
		}
	}

	public function getMulti($keys, &$finds) {
		$ret = array();
		foreach($keys as $k) {
			$dat = $this->get($k);
			if(!empty($dat)) {
				$ret[$k] = $dat;
				$finds[$k] = 0;
			}
		}
		return $ret;
	}

	public function delete($key) {
		$key = str_replace(" ", "", $key);
		switch($this->tp) {
		case 3:
			$this->tt->out($key);
		case 1:
			$this->mm->delete($key);
			break;
		case 4:
			$this->rd->delete($key);
			break;
		case 2:
			$this->fc->delete($key);
			break;
		default:
			$this->mm->delete($key);
			$this->rc->delete($key);
		}
	}

	private function getRD() {
		global $CONFIG;
		$obj_rd = new Redis();
		$obj_rd->connect($CONFIG["REDIS_HOST"], $CONFIG["REDIS_PORT"], 1);
		return $obj_rd;
	}

	private function getMM() {
		global $CONFIG;
		$obj_mem = new Memcached();
		//$obj_mem = new MemCachecc();
		$obj_mem->addServer($CONFIG["MEMCACH_HOST"], $CONFIG["MEMCACH_PORT"]);
		return $obj_mem;
	}

	private function getTT() {
		global $CONFIG;
		$obj_tt = new TokyoTyrant($CONFIG["TTSERVER_HOST"], $CONFIG["TTSERVER_PORT"]);
		return $obj_tt;
	}
}

class FileCache {
	//文件缓存，早已废弃，不推荐
	private $dir;

	private function __construct() {
		global $CONFIG;
		$dir_name = "{$CONFIG['CACHEPATH']}/fc/";
		if(!is_dir($dir_name)) {
			`mkdir -p $dir_name`;
		}
		$this->dir = $dir_name;
	}

	public static function getInstance() {
		static $fc = null;
		if($fc == null) {
			$fc = new FileCache();
		}
		return $fc;
	}

	public function set( $key, $val ) {
		$filename = $this->dir.$key;
		file_put_contents($filename, $val);
	}

	public function get( $key ) {
		$filename = $this->dir.$key;
		$dat = null;
		if(file_exists($filename)) {
			$dat = file_get_contents($filename);
		}
		return $dat;
	}

	public function setCacheStatus($key, $stat=false) {
		$filename = $this->dir.$key."a";
		if($stat==false) {
			unlink($filename);
		} else {
			file_put_contents($filename, "ok");
		}
	}

	public function isexpired($key,$cache_time=1800) {
		$filename = $this->dir.$key;
		if( (!file_exists($filename) || filemtime($filename)<(time()-$cache_time)) && !file_exists($filename."a")) {
			return true;
		} else {
			return false;
		}
	}

	public function delete( $key ) {
		$filename = $this->dir.$key;
		if(file_exists($filename)) {
			unlink($filename);
		}
	}

}

class MemCachecc {
	private $mm;

	public function __construct() {
		$this->mm = new Memcache();
	}

	public function addServer($host, $port) {
		$this->mm->addServer($host, $port);
	}

	public function set( $key, $val, $exp=0 ) {
		$this->mm->set($key, $val, MEMCACHE_COMPRESSED, $exp);
	}

	public function get( $key ) {
		if(!empty($key)) {
			return $this->mm->get($key);
		}
		return null;
	}

	public function setMulti($data, $time=0) {
		foreach($data as $k=>$v) {
			$k = str_replace(" ", "", $k);
			$this->set($k, $v, $time);
		}
	}

	public function getMulti($keys, &$finds) {
		$ret = array();
		foreach($keys as $k) {
			$k = str_replace(" ", "", $k);
			$dat = $this->get($k);
			if($dat !== null) {
				$ret[$k] = $dat;
				$finds[$k] = 0;
			}
		}
		return $ret;
	}

	public function delete( $key ) {
		$this->mm->delete($key);
	}

	public function close() {
		$this->mm->close();
	}

}
?>

<?php
/**
 *
 */

class CRedis extends Redis {
    public $servers = array('127.0.0.1', 6379);
    public $connectionPersistent = false;
    public $keyPrefix = '';
    
    public function __construct() {
        parent::__construct();
    }
    
    public function init() {
    	try {
	        if($this->connectionPersistent) {
	            $this->pconnect($this->servers['host'], $this->servers['port']);
	        } else {
	            $this->connect($this->servers['host'], $this->servers['port']);
	        }
    	} catch (Exception $e) {
    		throw new Yaf_Exception('连接Redis失败: ' . $e->getMessage());
    	}
        //print_r($this->INFO());
        //echo $this->servers['host'];exit;
    }
    
    public function keys($prefix) {
        return parent::keys($this->generateUniqueKey($prefix));
    }
    
    public function get($key) {
        return parent::get($this->generateUniqueKey($key));
    }
    
    public function set($key, $value = null, $timeout = 0) {
        if($timeout > 0) {
            return parent::setex($this->generateUniqueKey($key), $timeout, $value);
        } else {
            return parent::set($this->generateUniqueKey($key), $value);
        }
    }
    
    public function setex($key, $timeout = 0, $value = null) {
        return parent::setex($this->generateUniqueKey($key), $timeout, $value);
    }
    
    public function expire($key, $timeout) {
        return parent::expire($this->generateUniqueKey($key), $timeout);
    }
    
    public function persist($key) {
        return parent::persist($this->generateUniqueKey($key));
    }
    
    public function hkeys($prefix, $key) {
        return parent::hkeys($this->generateUniqueKey($prefix), $key);
    }
    
    public function hlen($prefix) {
        return parent::hlen($this->generateUniqueKey($prefix));
    }
    
    public function hget($prefix, $key) {
        $ret = unserialize(parent::hget($this->generateUniqueKey($prefix), $key));
        return $ret;
    }
    
    public function hset($prefix, $key, $value) {
        return parent::hset($this->generateUniqueKey($prefix), $key, serialize($value));
    }
    
    public function close() {
        if(!$this->connectionPersistent) {
            parent::close();
        }
    }
    
    public function __destruct() {
        $this->close();
        
        parent::__destruct();
    }
    
	protected function generateUniqueKey($key) {
		return "{$this->keyPrefix}{$key}";
	}
}

?>

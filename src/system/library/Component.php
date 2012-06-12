<?php

class Component {
	private $_m;

	public static $__app = null;

	public static $__request = null;

	public static $__config = null;
	
	public static $__db = null;
	
	public static $__mongo = null;
	
	public static $__user = null;
	
	public static $__session = null;
    
	public static $__cache = null;
    
	public static $__queue = null;
    
    public function init() {
    }
    
    public function getUniqueId() {
        return substr(md5(serialize($this->config->application->toArray())), 8, 16);
    }
    
    public function getApp() {
        if(self::$__app === null) {
            self::$__app = Yaf_Application::app();
            
            $this->app = self::$__app;
        }
        
        return self::$__app;
    }
	
    public function getConfig() {
        if(self::$__config === null) {
            self::$__config = Yaf_Application::app()->getConfig();
            
            $this->config = self::$__config;
        }
        
        return self::$__config;
    }
    
	public function getSession() {
		if(self::$__session === null) {
            self::$__session = Yaf_Registry::get('session');
            
            //$this->session = self::$__session;
            //$this->session->open();
            self::$__session->open();
		}
		
		return self::$__session;
	}
    
	public function getUser() {
		if(self::$__user === null) {
            self::$__user = Yaf_Registry::get('user');
            
            //$this->user = self::$__user;
		}
		
		return self::$__user;
	}
    
	public function getDb() {
		if(self::$__db === null) {
            self::$__db = Yaf_Registry::get('db');
            
            //$this->db = self::$__db;
            //$this->db ->open();
            self::$__db->open();
		}
		
		return self::$__db;
	}
    
	public function getMongo() {
		if(self::$__mongo === null) {
            self::$__mongo = Yaf_Registry::get('mongo');
            
            //$this->mongo = self::$__mongo;
            //$this->mongo ->open();
            self::$__mongo->open();
		}
		
		return self::$__mongo;
	}
	
	public function getCache() {
		if(self::$__cache === null) {
            self::$__cache = Yaf_Registry::get('cache');
            
            //$this->cache = self::$__cache;
		}
		
		return self::$__cache;
	}
	
	public function getQueue() {
		if(self::$__queue === null) {
            self::$__queue = Yaf_Registry::get('queue');
            
            //$this->queue = self::$__queue;
		}
		
		return self::$__queue;
	}
	
	public function __get($name) {
		if (isset($this->$name)) {
			return $this->$name;
		} else {
			$getter='get'.$name;
			if (method_exists($this,$getter)) {
				return $this->$getter();
			} else if(isset($this->$name)) {
			    return $this->$name;
			}
		}
		
		return null;
	}
	
	public function __set($name, $value) {
	    if (isset($this->$name)) {
			$this->$name = $value;
		} else {
			$setter='set'.$name;
			if (method_exists($this, $setter)) {
				$this->$setter($value);
			} else {
			    $this->$name = $value;
			}
		}
	}
	
	public function __isset($name) {
	    if (isset($this->$name)) {
			return true;
		} else {
			return false;
		}
	}
}
?>
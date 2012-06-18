<?php
/**
 *
 */

class Controller extends Yaf_Controller_Abstract {
    
    public $yafAutoRender = true;

	public static $__app = null;

	public static $__request = null;

	public static $__config = null;
	
	public static $__db = null;
	
	public static $__user = null;
	
	public static $__session = null;
    
	public static $__cache = null;
    
	public static $__queue = null;
    
    public function init() {
        
        $this->user = $this->user;
        $this->view = $this->getView();
        
        $this->view->assign('sitename', '微电影在线');
    }
    
    public function getId() {
        return isset($this->id) ? $this->id : substr(md5($this->getRequest()->getRequestUri()), 8, 16);
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
            
            $this->session = self::$__session;
            
            $this->session->open();
		}
		
		return self::$__session;
	}
    
	public function getUser() {
		if(self::$__user === null) {
            self::$__user = Yaf_Registry::get('user');
            
            $this->user = self::$__user;
		}
		
		return self::$__user;
	}
    
	public function getDb() {
		if(self::$__db === null) {
            self::$__db = Yaf_Registry::get('db');
            
            $this->db = self::$__db;
            $this->db ->open();
		}
		
		return self::$__db;
	}
	
	public function getCache() {
		if(self::$__cache === null) {
            self::$__cache = Yaf_Registry::get('cache');
            
            $this->cache = self::$__cache;
		}
		
		return self::$__cache;
	}
	
	public function getQueue() {
		if(self::$__queue === null) {
            self::$__queue = Yaf_Registry::get('queue');
            
            $this->queue = self::$__queue;
		}
		
		return self::$__queue;
	}
	
	public function __get($name) {
		if(isset($this->$name)) {
			return $this->$name;
		} else {
			$getter='get'.$name;
			if(method_exists($this,$getter)) {
				return $this->$getter();
			}
		}
		
		return null;
	}
}

?>

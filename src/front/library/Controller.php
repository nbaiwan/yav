<?php
/**
 *
 */

class Controller extends Yaf_Controller_Abstract {

	public static $__app = null;

	public static $__request = null;

	public static $__config = null;
	
	public static $__db = null;
    
	public static $__cache = null;
    
	public static $__queue = null;
	
    public function getApp() {
        if(self::$__app === null) {
            self::$__app = Yaf_Application::app();
        }
        
        return self::$__app;
    }
	
    public function getConfig() {
        if(self::$__config === null) {
            self::$__config = Yaf_Application::app()->getConfig();
        }
        
        return self::$__config;
    }
    
	public function getDb() {
		if(self::$__db === null) {
            self::$__db = Yaf_Registry::get('db');
		}
		
		return self::$__db;
	}
	
	public function getCache() {
		if(self::$__cache === null) {
            self::$__cache = Yaf_Registry::get('cache');
		}
		
		return self::$__cache;
	}
	
	public function getQueue() {
		if(self::$__queue === null) {
            self::$__queue = Yaf_Registry::get('queue');
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
			} else if(is_array($this->_m)) {
				foreach($this->_m as $object) {
					if($object->getEnabled() && (property_exists($object,$name) || $object->canGetProperty($name))) {
						return $object->$name;
					}
				}
			}
		}
		
		return null;
	}
}

?>

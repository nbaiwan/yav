<?php
/**
 *
 */

class CSession implements Iterator, ArrayAccess, Countable {
    public static $__opened = false;
    public $expire = 1200;
    public $domain = '';
    public $path = '/';
    public $secure = null;
    public $httpOnly = null;
    public $autoStart = false;
    public $prefix = 'vsenho.session.';
    
    public $driver = 'File';
    
    private $_key;
	private $_keys;
	
	private $_session = null;
    
    public function __construct() {
        //parent::getInstance();
    }
    
    public function init() {
        session_set_cookie_params($this->expire, $this->path, $this->domain, $this->secure, $this->httpOnly);
        
        if($this->autoStart) {
            $this->open();
        }
    }
    
    public function open() {
        $session_class = "C{$this->driver}Session";
        if(!self::$__opened && class_exists($session_class)) {
            $handler = new $session_class($this);
            session_set_save_handler(
                array($handler, 'open'),
                array($handler, 'close'),
                array($handler, 'read'),
                array($handler, 'write'),
                array($handler, 'destroy'),
                array($handler, 'gc')
            );
            
            session_start();
            self::$__opened = true;
        }
    }
    
    public function close() {
        session_write_close();
    }
    
    public function __destruct() {
        $this->close();
    }
	
	public function __isset($key) {
	    return isset($this->_m[$key]);
	}
	
	public function __unset($key) {
	    unset($this->_m[$key]);
	}
	
	public function current() {
	    return isset($_SESSION[$this->_key]) ? $_SESSION[$this->_key] : null;
	}
	
	public function next() {
	    $this->_key = next($this->_keys);
	    return $_SESSION[$this->_key];
	}
	
	public function key() {
	    return $this->_key;
	}
	
	public function valid() {
		return $this->_key !== false;
	}
	
	public function rewind() {
	    $this->_keys = array_keys($_SESSION);
	    return ($this->_key = reset($this->_keys));
	}
	
	public function offsetExists($key) {
	    return isset($_SESSION[$key]);
	}
	
	public function offsetGet($key) {
	    if(isset($_SESSION[$key])) {
	        return $_SESSION[$key];
	    }
	    
	    return null;
	}
	
	public function offsetSet($key, $value) {
	    $_SESSION[$key] = $value;
	}
	
	public function offsetUnset($key) {
	    unset($_SESSION[$key]);
	    
	}
	
	public function count() {
	    return count($_SESSION);
	}
}

class CFileSession {
    
    private $_s = null;
    
    public function __construct($session) {
        $this->_s = $session;
    }
    
    public function open($savePath, $sessionName) {
        
        return true;
    }
    
    public function close() {
        
        return true;
    }
    
    public function read($id) {
        
        return '';
    }
    
    public function write($id, $value) {
        
        return true;
    }
    
    public function destroy($id) {
        
        return true;
    }
    
    public function gc($maxLifetime) {
        
        return true;
    }
}

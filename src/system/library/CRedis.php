<?php
/**
 * Redis缓存类，需安装Redis扩展
 * @version $Id: library/CRedis.php Apr 17, 2012 11:37:02 AM
 * @author Jacky Zhang <myself.fervor@gmail.com>
 * @copyright 启航网络科技
 */

class CRedis extends Redis implements ICache {
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
        $data = unserialize(parent::get($this->generateUniqueKey($key)));
        
		if(!is_array($data)) {
			return false;
		}
		if(!($data[1] instanceof ICacheDependency) || !$data[1]->getHasChanged()) {
			return $data[0];
		}
		
        return false;
    }
    
    public function set($key, $value = null, $timeout = 0, $dependency = null) {
		if($dependency !== null) {
			$dependency->evaluateDependency();
		}
		$data = array($value, $dependency);
		
        if($timeout > 0) {
            return parent::setex($this->generateUniqueKey($key), $timeout, serialize($data));
        } else {
            return parent::set($this->generateUniqueKey($key), serialize($data));
        }
    }
    
    public function del($key) {
        return parent::del($this->generateUniqueKey($key));
    }
    
    public function setex($key, $timeout = 0, $value = null, $dependency = null) {
		if($dependency !== null) {
			$dependency->evaluateDependency();
		}
		
		$data = array($value, $dependency);
		
        return parent::setex($this->generateUniqueKey($key), $timeout, serialize($data));
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
        $data = unserialize(parent::hget($this->generateUniqueKey($prefix), $key));
        
		if(!is_array($data)) {
			return false;
		}
		if(isset($data[1]) && (!($data[1] instanceof ICacheDependency) || !$data[1]->getHasChanged())) {
			return $data[0];
		}
		
        return false;
    }
    
    public function hset($prefix, $key, $value, $dependency = null) {
		if($dependency !== null) {
			$dependency->evaluateDependency();
		}
		
		$data = array($value, $dependency);
		
        return parent::hset($this->generateUniqueKey($prefix), $key, serialize($data));
    }
    
    public function hdel($prefix, $key) {
        return parent::hdel($this->generateUniqueKey($prefix), $key);
    }
    
    public function lpush($key, $value) {
        return parent::lpush($this->generateUniqueKey($key), $value);
    }
    
    public function rpush($key, $value) {
        return parent::rpush($this->generateUniqueKey($key), $value);
    }
    
    public function lpop($key, $value) {
        return parent::lpop($this->generateUniqueKey($key), $value);
    }
    
    public function rpop($key, $value) {
        return parent::rpop($this->generateUniqueKey($key), $value);
    }
    
    public function llen($key) {
        return parent::llen($this->generateUniqueKey($key));
    }
    
    public function close() {
        if(!$this->connectionPersistent) {
            parent::close();
        }
    }
    
    public function __destruct() {
        $this->close();
    }
    
	protected function generateUniqueKey($key) {
		return "{$this->keyPrefix}{$key}";
	}
}

interface ICache {
	
}

/**
 * 缓存依赖接口
 */
interface ICacheDependency {
    public function evaluateDependency();
    public function getHasChanged();
}

/**
 * 缓存依赖
 */
class CCacheDependency implements ICacheDependency {
    private $_data;
    
    public function evaluateDependency() {
        $this->_data = $this->generateDependentData();
    }
    
    public function getHasChanged() {
        return $this->generateDependentData() != $this->_data;
    }
    
    public function getDependentData() {
        return $this->_data;
    }
    
    protected function generateDependentData() {
        return null;
    }
    
	public function evaluateExpression($_expression_, $_data_=array()) {
		if(is_string($_expression_)) {
			extract($_data_);
			return eval('return '.$_expression_.';');
		} else {
			$_data_[] = $this;
			return call_user_func_array($_expression_, $_data_);
		}
	}
}

/**
 * 缓存表达式依赖
 */
class CExpressionDependency extends CCacheDependency {
    
    public $expression;
    
    public function __construct($expression = 'true') {
        $this->expression = $expression;
    }
    
    protected function generateDependentData() {
        return $this->evaluateExpression($this->expression);
    }
}


?>

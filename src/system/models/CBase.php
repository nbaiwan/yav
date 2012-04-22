<?php
/**
 *
 */

class CBaseModel {

	public static $__app = null;
	
	public static $__db = null;
	
	public static $__user = null;
    
	public static $__cache = null;
    
    public static $__instance = null;
    
    public static $__model = null;
    
    public function __construct() {
        $this->init();
    }
    
    public function init() {
    }
    
    public static function inst() {
        if(static::$__instance === null) {
            static::$__instance = new static::$__model();
        }
        
        return static::$__instance;
    }
    
    public function getApp() {
        if(self::$__app === null) {
            self::$__app = Yaf_Application::app();
            
            $this->app = self::$__app;
        }
        
        return self::$__app;
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
            $this->db->open();
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
    
    public function buildQuery($params) {
        if(!is_array($params)) {
            return '';
        }
        
        //print_r($params);exit;
        
        $sql = "";
        
        if(isset($params['select'])) {
            $sql .= "SELECT {$params['select']}\r\n";
        }
        if(isset($params['from'])) {
            $from = '';
            if(is_array($params['from'][0])) {
                for($i=0, $j=count($params['from']); $i<$j; $i++) {
                    if(is_array($params['from'][$i])) {
                        $from .= ", `{$params['from'][$i][0]}` `{$params['from'][$i][1]}`";
                    } else {
                        $from .= ", `{$params['from'][$i]}`";
                    }
                }
                $from = trim($from, ', ');
                $sql .= "FROM {$from}\r\n";
            } else if(is_array($params['from'])) {
                $sql .= "FROM `{$params['from'][0]}` `{$params['from'][1]}`\r\n";
            } else {
                $sql .= "FROM `{$params['from']}`\r\n";
            }
        }
        
        if(isset($params['join'])) {
            if(is_array($params['join'][0])) {
                for($i=0, $j=count($params['join']); $i<$j; $i++) {
                    $sql .= "INNER JOIN `{$params['join'][$i][0]}` {$params['join'][$i][1]} ON {$params['join'][$i][2]}\r\n";
                }
            } else if(is_array($params['join'])) {
                $sql .= "INNER JOIN `{$params['join'][0]}` `{$params['join'][1]}` ON {$params['join'][2]}\r\n";
            } else {
                $sql .= "INNER JOIN `{$params['join']}`\r\n";
            }
        }
        
        if(isset($params['leftJoin'])) {
            if(is_array($params['leftJoin'][0])) {
                for($i=0, $j=count($params['leftJoin']); $i<$j; $i++) {
                    $sql .= "LEFT JOIN `{$params['leftJoin'][$i][0]}` {$params['leftJoin'][$i][1]} ON {$params['leftJoin'][$i][2]}\r\n";
                }
            } else if(is_array($params['leftJoin'])) {
                $sql .= "LEFT JOIN `{$params['leftJoin'][0]}` `{$params['leftJoin'][1]}` ON {$params['leftJoin'][2]}\r\n";
            } else {
                $sql .= "LEFT JOIN `{$params['leftJoin']}`\r\n";
            }
        }
        
        if(isset($params['where'])) {
            $where = $this->buildWhere($params['where']);
            $where = trim($where, ' OR ');
            $where = trim($where, ' AND ');
            $sql .= "WHERE {$where}";
        }
        
        return $sql;
    }
    
    public function buildWhere($params) {
        $where = '';
        for($i=0, $j = count($params); $i<$j; ) {
            if(is_array($params[$i])) {
                $where .= $this->buildWhere($params[$i]);
                $i++;
            } else if(is_array($params[$i+1])) {
                $ret = $this->buildWhere($params[$i+1]);
                $ret = trim($ret, ' OR ');
                $ret = trim($ret, ' AND ');
                $where .= " {$params[$i]} ($ret)";
                $i += 2;
            } else {
                $op = $params[$i];
                switch($op) {
                    case 'OR LIKE':
                        $where .= " OR {$params[$i+1]} LIKE {$params[$i+2]}";
                        $i += 3;
                        break;
                    case 'LIKE':
                    case 'AND LIKE':
                        $where .= " AND {$params[$i+1]} LIKE {$params[$i+2]}";
                        $i += 3;
                        break;
                    case 'AND':
                        $where .= " AND {$params[$i+1]}";
                        $i += 2;
                        break;
                    case 'OR':
                        $where .= " OR {$params[$i+1]}";
                        $i += 2;
                        break;
                    default:
                        throw new Yaf_Exception('buildQuery Exception');
                        break;
                }
            }
        }
        
        return $where;
    }
}

?>

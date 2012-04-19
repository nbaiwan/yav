<?php
/**
 *
 */

class CDatabase extends PDO {
	public $dsn = null;
	public $username = null;
	public $password = null;
	public $options = array();
	
    public $connectionPersistent = false;
    public $autoConnect = false;
    public $tablePrefix = '';
    public $debugMode = false;
    
    private $_statement = null;
    
    private $_connected = false;
    
    public function __construct() {
    }
    
    public function init() {
    	if($this->autoConnect) {
    		$this->open();
    	}
    }
    
    public function open() {
        if(!$this->_connected) {
            try {
                $password = Common::mysql_db_encrypt($this->password);
                parent::__construct($this->dsn, $this->username, $password, $this->options);
                
                $this->_connected = true;
            } catch (Exception $e) {
                throw new Yaf_Exception('连接数据库失败: ' . $e->getMessage());
            }
        }
    }
    
    public function prepare($sql, $options = array()) {
        $sql = preg_replace("/\{\{([^\}]+)\}\}/s", "{$this->tablePrefix}\\1", $sql);
        $this->_statement = parent::prepare($sql);
        
        return $this->_statement;
    }
    
    public function bindValue($name, $value, $data_type = PDO::PARAM_STR) {
        $this->_statement->bindValue($name, $value, $data_type);
    }
    
    public function bindParam($name, $value, $data_type = PDO::PARAM_STR) {
        $this->_statement->bindParam($name, $value, $data_type);
    }
    
    public function execute($params = array()) {
        if($params && is_array($params)) {
            foreach($params as $_k=>$_v) {
                $this->bindValue($_k, $_v);
            }
        }
        
        if($ret = $this->_statement->execute()) {
        } else {
            if($this->debugMode) {
                $error_info = $this->_statement->errorInfo();
                throw new Yaf_Exception($error_info[2], $error_info[1]);
            }
        }
        
        return $ret;
    }
    
    public function query($sql = '', $params = array()) {
    }
    
    public function queryAll($sql = '', $params = array()) {
        if($sql != '') {
            $this->prepare($sql);
        }
        
        if($this->execute($params)) {
            $ret = $this->_statement->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $ret = array();
        }
        
        return $ret;
    }
    
    public function queryRow($sql = '', $params = array()) {
        if($sql != '') {
            $this->prepare($sql);
        }
        
        if($this->execute($params)) {
            $ret = $this->_statement->fetch(PDO::FETCH_ASSOC);
        } else {
            $ret = array();
        }
        
        return $ret;
    }
    
    public function queryScalar($sql = '', $params = array()) {
        if($sql != '') {
            $this->prepare($sql);
        }
        
        if($this->execute($params)) {
            $ret = $this->_statement->fetchColumn();
        } else {
            $ret = array();
        }
        
        return $ret;
    }
    
    public function queryColumn($sql = '', $params = array()) {
        if($sql != '') {
            $this->prepare($sql);
        }
        
        $this->execute($params);
        $ret = array();
        while($row = $this->_statement->fetchColumn()) {
            $ret[] = $row;
        }
        
        return $ret;
    }
	public function insert($table, $columns) {
		$params = array();
		$names = array();
		$placeholders = array();
		foreach($columns as $_k=>$_v)
		{
			$names[]= "`{$_k}`";
            $placeholders[] = ":{$_k}";
            $params[":{$_k}"] = $_v;
		}
		$sql = "INSERT INTO `{$table}`(" . implode(', ',$names) . ")
                VALUES (" . implode(', ', $placeholders) . ")";
        $this->prepare($sql)->execute($params);
        
		return $this->_statement->rowCount();
	}
    
    public function getLastInsertID() {
        return parent::lastInsertId();
    }
    
	public function update($table, $columns, $conditions='', $params=array()) {
		$lines=array();
		foreach($columns as $_k=>$_v) {
            $lines[] = "`{$_k}` = :{$_k}";
            $params[":{$_k}"] = $_v;
		}
		$sql = "UPDATE `{$table}` SET ". implode(', ', $lines);
        
        if($conditions) {
            if(is_string($conditions)) {
                $sql .= " WHERE {$conditions}";
            }
        }
        $this->prepare($sql)->execute($params);
        
		return $this->_statement->rowCount();
	}
}

?>

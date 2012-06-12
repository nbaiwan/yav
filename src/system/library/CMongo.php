<?php
/**
 *
 */

class CMongo extends Mongo {
    public $server = null;
    public $dbname = null;
	public $options = array();
	
    public $connectionPersistent = false;
    public $autoConnect = false;
    public $tablePrefix = '';
    public $debugMode = false;
    
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
                //$password = Common::mysql_db_encrypt($this->password);
                //parent::__construct($this->dsn, $this->username, $password, $this->options);
                
                parent::__construct($this->server);
                
                $this->_connected = true;
            } catch (Exception $e) {
                throw new Yaf_Exception('连接MongoDB失败: ' . $e->getMessage());
            }
        }
    }
    
    public function getLastInsertID() {
        return parent::lastInsertId();
    }
    
	public function insert($table, $data) {
	    $dbname = $this->dbname;
	    return $this->$dbname->$table->insert($data);
	}
    
	public function update($table, $columns, $conditions=array(), $params=array()) {
	    $dbname = $this->dbname;
	    
		return $this->$dbname->$table->update(
	        $conditions,
	        array(
                '$set' => $columns,
            )
        );
	}
	
	public function findOne($table, $params) {
	    $dbname = $this->dbname;
	    return $this->$dbname->$table->findOne($params);
	}
    
	public function find($table, $params) {
	    $dbname = $this->dbname;
	    return $this->$dbname->$table->find($params);
	}
	
	public function queryRow($table, $params) {
	    $dbname = $this->dbname;
	    
	}
	
	public function queryAll($table, $params = array()) {
	    
	    $ret = iterator_to_array($ret);
	    
	    return $ret;
	}
}

?>

<?php
/**
 * CDbConnection class file
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @link http://www.yiiframework.com/
 * @copyright Copyright &copy; 2008-2011 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

/**
 * CDbConnection represents a connection to a database.
 *
 * CDbConnection works together with {@link CDbCommand}, {@link CDbDataReader}
 * and {@link CDbTransaction} to provide data access to various DBMS
 * in a common set of APIs. They are a thin wrapper of the {@link http://www.php.net/manual/en/ref.pdo.php PDO}
 * PHP extension.
 *
 * To establish a connection, set {@link setActive active} to true after
 * specifying {@link dsn}, {@link username} and {@link password}.
 *
 * The following example shows how to create a CDbConnection instance and establish
 * the actual connection:
 * <pre>
 * $connection=new CDbConnection($dsn,$username,$password);
 * $connection->active=true;
 * </pre>
 *
 * After the DB connection is established, one can execute an SQL statement like the following:
 * <pre>
 * $command=$connection->createCommand($sqlStatement);
 * $command->execute();   // a non-query SQL statement execution
 * // or execute an SQL query and fetch the result set
 * $reader=$command->query();
 *
 * // each $row is an array representing a row of data
 * foreach($reader as $row) ...
 * </pre>
 *
 * One can do prepared SQL execution and bind parameters to the prepared SQL:
 * <pre>
 * $command=$connection->createCommand($sqlStatement);
 * $command->bindParam($name1,$value1);
 * $command->bindParam($name2,$value2);
 * $command->execute();
 * </pre>
 *
 * To use transaction, do like the following:
 * <pre>
 * $transaction=$connection->beginTransaction();
 * try
 * {
 *    $connection->createCommand($sql1)->execute();
 *    $connection->createCommand($sql2)->execute();
 *    //.... other SQL executions
 *    $transaction->commit();
 * }
 * catch(Exception $e)
 * {
 *    $transaction->rollBack();
 * }
 * </pre>
 *
 * CDbConnection also provides a set of methods to support setting and querying
 * of certain DBMS attributes, such as {@link getNullConversion nullConversion}.
 *
 * Since CDbConnection implements the interface IApplicationComponent, it can
 * be used as an application component and be configured in application configuration,
 * like the following,
 * <pre>
 * array(
 *     'components'=>array(
 *         'db'=>array(
 *             'class'=>'CDbConnection',
 *             'dsn'=>'sqlite:path/to/dbfile',
 *         ),
 *     ),
 * )
 * </pre>
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @version $Id: CDbConnection.php 3123 2011-03-25 12:20:47Z qiang.xue $
 * @package system.db
 * @since 1.0
 */
class Db_CDbConnection extends CComponent {
	
	public $dsn;
	
	public $username = '';
	
	public $password = '';
	
	public $schemaCachingDuration = 0;
	
	public $schemaCachingExclude=array();
	
	public $schemaCacheId = 'cache';
	
	public $queryCachingDuration = 0;
	
	public $queryCachingDependency;
	
	public $queryCachingCount = 0;
	
	public $queryCacheId = 'cache';
	
	public $autoConnect=true;
	
	public $charset;
	
	public $emulatePrepare=false;
	
	public $enableParamLogging=false;
	
	public $enableProfiling=false;
	
	public $tablePrefix;
	
	public $initSqls;
	
	public $driverMap=array(
		'pgsql'=>'CPgsqlSchema',    // PostgreSQL
		'mysqli'=>'CMysqlSchema',   // MySQL
		'mysql'=>'Db_Schema_Mysql_CMysqlSchema',    // MySQL
		'sqlite'=>'CSqliteSchema',  // sqlite 3
		'sqlite2'=>'CSqliteSchema', // sqlite 2
		'mssql'=>'CMssqlSchema',    // Mssql driver on windows hosts
		'dblib'=>'CMssqlSchema',    // dblib drivers on linux (and maybe others os) hosts
		'sqlsrv'=>'CMssqlSchema',   // Mssql
		'oci'=>'COciSchema',        // Oracle driver
	);

	private $_attributes=array();
	private $_active=false;
	private $_pdo;
	private $_transaction;
	private $_schema;
	
	public function __construct($dsn='', $username='', $password='') {
		$this->dsn = $dsn;
		$this->username = $username;
		$this->password = $password;
	}
	
	public function __sleep() {
		$this->close();
		return array_keys(get_object_vars($this));
	}
	
	public static function getAvailableDrivers() {
		return PDO::getAvailableDrivers();
	}
	
	public function init() {
		parent::init();
		if($this->autoConnect) {
			$this->setActive(true);
		}
	}
	
	public function getActive() {
		return $this->_active;
	}
	
	public function setActive($value) {
		if($value!=$this->_active) {
			if($value) {
				$this->open();
			} else {
				$this->close();
			}
		}
	}
	
	public function cache($duration, $dependency=null, $query_count=1) {
		$this->query_caching_duration = $duration;
		$this->query_caching_dependency = $dependency;
		$this->query_caching_count = $query_count;
		return $this;
	}
	
	protected function open() {
		if($this->_pdo===null) {
			if(empty($this->dsn)) {
				throw new Yaf_Exception('CDbConnection.dsn cannot be empty.');
			}
			try {
				$this->_pdo=$this->createPdoInstance();
				$this->initConnection($this->_pdo);
				$this->_active=true;
			} catch(PDOException $e) {
				if(YAF_DEBUG) {
					throw new Yaf_Exception('CDbConnection failed to open the DB connection: ' . $e->getMessage(),(int)$e->getCode(),$e->errorInfo);
				} else {
					throw new Yaf_Exception('CDbConnection failed to open the DB connection.', (int)$e->getCode(), $e->errorInfo);
				}
			}
		}
	}
	
	protected function close() {
		$this->_pdo=null;
		$this->_active=false;
		$this->_schema=null;
	}
	
	protected function createPdoInstance() {
		//$password = $this->password;
        $password = Common::mysql_db_encrypt($this->password);
		$pdoClass = 'PDO';
		if(($pos =  strpos($this->dsn, ':')) !==false) {
			$driver = strtolower(substr($this->dsn, 0,  $pos));
			if($driver === 'mssql' || $driver === 'dblib') {
				$pdoClass = 'CMssqlPdoAdapter';
			}
		}
		return new $pdoClass($this->dsn, $this->username,
									$password, $this->_attributes);
	}
	
	protected function initConnection($pdo) {
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		if($this->emulatePrepare && constant('PDO::ATTR_EMULATE_PREPARES')) {
			$pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES,true);
		}
		if($this->charset!==null) {
			$driver=strtolower($pdo->getAttribute(PDO::ATTR_DRIVER_NAME));
			if(in_array($driver,array('pgsql','mysql','mysqli'))) {
				$pdo->exec('SET NAMES '.$pdo->quote($this->charset));
			}
		}
		if($this->initSQLs!==null) {
			foreach($this->initSQLs as $sql) {
				$pdo->exec($sql);
			}
		}
	}
	
	public function getPdoInstance() {
		return $this->_pdo;
	}
	
	public function createCommand($query=null)
	{
		$this->setActive(true);
		return new Db_CDbCommand($this, $query);
	}
	
	public function getCurrentTransaction()
	{
		if($this->_transaction!==null) {
			if($this->_transaction->getActive()) {
				return $this->_transaction;
			}
		}
		return null;
	}
	
	public function beginTransaction()
	{
		$this->setActive(true);
		$this->_pdo->beginTransaction();
		return $this->_transaction=new CDbTransaction($this);
	}
	
	public function getSchema()
	{
		if($this->_schema!==null) {
			return $this->_schema;
		} else {
			$driver=$this->getDriverName();
			if(isset($this->driverMap[$driver])) {
                return $this->_schema=new $this->driverMap[$driver]($this);
			} else {
				throw new Yaf_Exception("CDbConnection does not support reading schema for {$driver} database.");
			}
		}
	}
	
	public function getCommandBuilder()
	{
		return $this->getSchema()->getCommandBuilder();
	}
	
	public function getLastInsertID($sequence_name='')
	{
		$this->setActive(true);
		return $this->_pdo->lastInsertId($sequence_name);
	}
	
	public function quoteValue($str)
	{
		if(is_int($str) || is_float($str)) {
			return $str;
		}

		$this->setActive(true);
		if(($value=$this->_pdo->quote($str))!==false) {
			return $value;
		} else {  // the driver doesn't support quote (e.g. oci)
			return "'" . addcslashes(str_replace("'", "''", $str), "\000\n\r\\\032") . "'";
		}
	}
	
	public function quoteTableName($name)
	{
		return $this->getSchema()->quoteTableName($name);
	}
	
	public function quoteColumnName($name)
	{
		return $this->getSchema()->quoteColumnName($name);
	}
	
	public function getPdoType($type)
	{
		static $map=array (
			'boolean'=>PDO::PARAM_BOOL,
			'integer'=>PDO::PARAM_INT,
			'string'=>PDO::PARAM_STR,
			'NULL'=>PDO::PARAM_NULL,
		);
		return isset($map[$type]) ? $map[$type] : PDO::PARAM_STR;
	}
	
	public function getColumnCase()
	{
		return $this->getAttribute(PDO::ATTR_CASE);
	}
	
	public function setColumnCase($value)
	{
		$this->setAttribute(PDO::ATTR_CASE,$value);
	}
	
	public function getNullConversion()
	{
		return $this->getAttribute(PDO::ATTR_ORACLE_NULLS);
	}
	
	public function setNullConversion($value)
	{
		$this->setAttribute(PDO::ATTR_ORACLE_NULLS,$value);
	}
	
	public function getAutoCommit()
	{
		return $this->getAttribute(PDO::ATTR_AUTOCOMMIT);
	}
	
	public function setAutoCommit($value)
	{
		$this->setAttribute(PDO::ATTR_AUTOCOMMIT,$value);
	}
	
	public function getPersistent()
	{
		return $this->getAttribute(PDO::ATTR_PERSISTENT);
	}
	
	public function setPersistent($value)
	{
		return $this->setAttribute(PDO::ATTR_PERSISTENT,$value);
	}
	
	public function getDriverName()
	{
		if(($pos=strpos($this->dsn, ':'))!==false) {
			return strtolower(substr($this->dsn, 0, $pos));
		}
		// return $this->getAttribute(PDO::ATTR_DRIVER_NAME);
	}
	
	public function getClientVersion()
	{
		return $this->getAttribute(PDO::ATTR_CLIENT_VERSION);
	}
	
	public function getConnectionStatus()
	{
		return $this->getAttribute(PDO::ATTR_CONNECTION_STATUS);
	}
	
	public function getPrefetch()
	{
		return $this->getAttribute(PDO::ATTR_PREFETCH);
	}
	
	public function getServerInfo()
	{
		return $this->getAttribute(PDO::ATTR_SERVER_INFO);
	}
	
	public function getServerVersion()
	{
		return $this->getAttribute(PDO::ATTR_SERVER_VERSION);
	}
	
	public function getTimeout()
	{
		return $this->getAttribute(PDO::ATTR_TIMEOUT);
	}
	
	public function getAttribute($name)
	{
		$this->setActive(true);
		return $this->_pdo->getAttribute($name);
	}
	
	public function setAttribute($name,$value)
	{
		if($this->_pdo instanceof PDO) {
			$this->_pdo->setAttribute($name,$value);
		} else {
			$this->_attributes[$name]=$value;
		}
	}
	
	public function getAttributes()
	{
		return $this->_attributes;
	}
	
	public function setAttributes($values)
	{
		foreach($values as $name=>$value) {
			$this->_attributes[$name]=$value;
		}
	}
	
	public function getStats()
	{
		/*
		$logger=Yii::getLogger();
		$timings=$logger->getProfilingResults(null,'system.db.CDbCommand.query');
		$count=count($timings);
		$time=array_sum($timings);
		$timings=$logger->getProfilingResults(null,'system.db.CDbCommand.execute');
		$count+=count($timings);
		$time+=array_sum($timings);
		return array($count, $time);
		*/
	}
}

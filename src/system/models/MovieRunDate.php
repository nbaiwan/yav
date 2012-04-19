<?php

/**
 * This is the model class for table "{{movie_classes}}".
 *
 * The followings are the available columns in table '{{movie_classes}}':
 * @property string $PID
 * @property string $PName
 * @property string $PIdentify
 * @property integer $PState
 */
class MovieRunDateModel extends CBaseModel {
	const STAT_STATUS_DELETED = 0;
	
	const STAT_STATUS_LOCKED = 1;
	
	const STAT_STATUS_NORMAL = 4;
	
	public static $__rundates = null;
    
    public static $__instance = null;
    
    public static $__model = 'MovieRunDateModel';
	
	public function getRundateById($rundate_id)
	{
		$_r = array();
		$rundates = is_array(self::$__rundates) ? self::$__rundates : $this->readRundatesArray();
		foreach ($rundates as $_k=>$_v) {
			if($_v['rundate_id'] == $rundate_id) {
				$_r = $_v;
				break;
			}
		}
		unset($rundates, $_k, $_v);
		
		return $_r;
	}
	
	/**
	 * 读取权限数据结构
	 */
	public function getRundatesByCache()
	{
		if(self::$__rundates!==null) {
			return self::$__rundates;
		}
		
		if(isset($this->cache) && (self::$__rundates = $this->cache->get('movie.rundates'))) {
			return self::$__rundates;
		}
		
		//无缓存数据， 读取数据库中的数据
		self::$__rundates = $this->readRundatesArray();
		//写入缓存
		if(isset($this->cache)) {
			$this->cache->set('movie.rundates', self::$__rundates);
		}
		
		return self::$__rundates;
	}
	
	/**
	 * 从数据库中取权限数据
	 */
	protected function readRundatesArray()
	{
		$sql = "SELECT rundate_id, rundate_date, rundate_rank, rundate_status, rundate_lasttime, rundate_dateline
				FROM {{movie_rundates}}
				WHERE rundate_status>:rundate_status
				ORDER BY rundate_rank ASC";
		$this->db->prepare($sql);
		$this->db->bindValue(':rundate_status', self::STAT_STATUS_DELETED);
		$ret = $this->db->queryAll();
		$_r = array();
		foreach($ret as $_k=>$_v) {
			$row = array(
				'rundate_id' => $_v['rundate_id'],
				'rundate_date' => $_v['rundate_date'],
				'rundate_status' => $_v['rundate_status'],
				'rundate_rank' => $_v['rundate_rank'],
			);
			$_r[] = $row;
		}
		
		self::$__rundates = $_r;
		
		return self::$__rundates;
	}
	
	/**
	 * 强制更新缓存
	 */
	public function updateCache() {
		//
		self::$__rundates = null;
		
		//
		if(isset($this->cache)) {
			self::$__rundates = $this->read_rundates_array();
			$this->cache->set('movie.rundates', self::$__rundates);
		}
		
		return true;
	}
}
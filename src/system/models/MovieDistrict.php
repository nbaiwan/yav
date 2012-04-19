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
class MovieDistrictModel extends CBaseModel {
	const STAT_STATUS_DELETED = 0;
	
	const STAT_STATUS_LOCKED = 1;
	
	const STAT_STATUS_NORMAL = 4;
	
	public static $__districts = null;
    
    public static $__instance = null;
    
    public static $__model = 'MovieDistrictModel';
	
	public function getDistrictById($district_id)
	{
		$_r = array();
		$districts = is_array(self::$__districts) ? self::$__districts : $this->readDistrictsArray();
		foreach ($districts as $_k=>$_v) {
			if($_v['district_id'] == $district_id) {
				$_r = $_v;
				break;
			}
		}
		unset($districts, $_k, $_v);
		
		return $_r;
	}
	
	/**
	 * 读取权限数据结构
	 */
	public function getDistrictsByCache()
	{
		if(self::$__districts!==null) {
			return self::$__districts;
		}
		
		if(isset($this->cache) && (self::$__districts = $this->cache->get('movie.districts'))) {
			return self::$__districts;
		}
		
		//无缓存数据， 读取数据库中的数据
		self::$__districts = $this->readDistrictsArray();
		//写入缓存
		if(isset($this->cache)) {
			$this->cache->set('movie.districts', self::$__districts);
		}
		
		return self::$__districts;
	}
	
	/**
	 * 从数据库中取权限数据
	 */
	protected function readDistrictsArray()
	{
		$sql = "SELECT c.district_id, c.district_name, c.district_identify, c.district_rank, c.district_status, c.district_lasttime, c.district_dateline
				FROM {{movie_districts}} c
				WHERE c.district_status>:district_status
				ORDER BY c.district_rank ASC";
		$this->db->prepare($sql);
		$this->db->bindValue(':district_status', self::STAT_STATUS_DELETED);
		$result = $this->db->queryAll();
		$_r = array();
		foreach($result as $k=>$v) {
			$row = array(
				'district_id' => $v['district_id'],
				'district_name' => $v['district_name'],
				'district_identify' => $v['district_identify'],
				'district_status' => $v['district_status'],
				'district_rank' => $v['district_rank'],
			);
			$_r[] = $row;
		}
		
		self::$__districts = $_r;
		
		return self::$__districts;
	}
	
	/**
	 * 强制更新缓存
	 */
	public function updateCache()
	{
		//
		self::$__districts = null;
		
		//
		if(isset($this->cache)) {
			self::$__districts = $this->readDistrictsArray();
			$this->cache->set('movie.districts', self::$__districts);
		}
		
		return true;
	}
}
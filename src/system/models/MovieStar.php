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
class MovieStarModel extends CBaseModel {
	const STAT_STATUS_DELETED = 0;
	
	const STAT_STATUS_LOCKED = 1;
	
	const STAT_STATUS_NORMAL = 4;
	
	public static $__stars = null;
    
    public static $__instance = null;
    
    public static $__model = 'MovieStarModel';
	
	public function getStarById($star_id) {
		$_r = array();
		$stars = is_array(self::$__stars) ? self::$__stars : $this->readStarsArray();
		foreach ($stars as $_k=>$_v) {
			if($_v['star_id'] == $star_id) {
				$_r = $_v;
				break;
			}
		}
		unset($stars, $_k, $_v);
		
		return $_r;
	}
	
	/**
	 * 读取权限数据结构
	 */
	public function getStarsByCache()
	{
		if(self::$__stars!==null) {
			return self::$__stars;
		}
		
		if(isset($this->cache) && (self::$__stars = $this->cache->get('movie.stars'))) {
			return self::$__stars;
		}
		
		//无缓存数据， 读取数据库中的数据
		self::$__stars = $this->readStarsArray();
		//写入缓存
		if(isset($this->cache)) {
			$this->cache->set('movie.stars', self::$__stars);
		}
		
		return self::$__stars;
	}
	
	/**
	 * 从数据库中取权限数据
	 */
	protected function readStarsArray()
	{
		$sql = "SELECT c.star_id, c.star_name, c.star_english_name, c.star_rank, c.star_status, c.star_lasttime, c.star_dateline
				FROM {{movie_stars}} c
				WHERE c.star_status>:star_status
				ORDER BY c.star_rank ASC";
		$this->db->prepare($sql);
		$this->db->bindValue(':star_status', self::STAT_STATUS_DELETED);
		$ret = $this->db->queryAll();
		$_r = array();
		foreach($ret as $_k=>$_v) {
			$row = array(
				'star_id' => $_v['star_id'],
				'star_name' => $_v['star_name'],
				'star_english_name' => $_v['star_english_name'],
				'star_status' => $_v['star_status'],
				'star_rank' => $_v['star_rank'],
			);
			$_r[] = $row;
		}
		
		self::$__stars = $_r;
		
		return self::$__stars;
	}
	
	/**
	 * 强制更新缓存
	 */
	public function updateCache()
	{
		//
		self::$__stars = null;
		
		//
		if(isset($this->cache)) {
			self::$__stars = self::readStarsArray();
			$this->cache->set('movie.stars', self::$__stars);
		}
		
		return true;
	}
}
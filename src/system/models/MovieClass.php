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
class MovieClassModel extends CBaseModel {
	const STAT_STATUS_DELETED = 0;
	
	const STAT_STATUS_LOCKED = 1;
	
	const STAT_STATUS_NORMAL = 4;
	
	public static $__classes = null;
    
    public static $__instance = null;
    
    public static $__model = 'MovieClassModel';
	
	public function getClassById($class_id)
	{
		$_r = array();
		$classes = is_array(self::$__classes) ? self::$__classes : $this->readClassesArray();
		foreach ($classes as $_k=>$_v) {
			if($_v['class_id'] == $class_id) {
				$_r = $_v;
				break;
			}
		}
		unset($classes, $_k, $_v);
		
		return $_r;
	}
	
	/**
	 * 读取权限数据结构
	 */
	public function getClassesByCache()
	{
		if(self::$__classes!==null) {
			return self::$__classes;
		}
		
		if(isset($this->cache) && (self::$__classes = $this->cache->get('movie.classes'))) {
			return self::$__classes;
		}
		
		//无缓存数据， 读取数据库中的数据
		self::$__classes = $this->readClassesArray();
		//重新整理数据
		self::$__classes = $this->buildClassesList();
		//写入缓存
		if(isset($this->cache)) {
			$this->cache->set('movie.classes', self::$__classes);
		}
		
		return self::$__classes;
	}
	
	/**
	 * 将权限数组数据，按顺序排序好，
	 */
	protected function buildClassesList($parent_id = 0, $deepth = 1)
	{
		$classes = is_array(self::$__classes) ? self::$__classes : $this->readClassesArray();
		$_r = array();
		foreach($classes as $_k=>$_v) {
			if($_v['parent_id'] == $parent_id) {
				$_v['identify_tree'] = $this->getIdentifyPath($_v['class_id']);
				$_v['deepth'] = $deepth;
				$_r[] = $_v;
				$children = $this->buildClassesList($_v['class_id'], $deepth+1);
				$_r = array_merge($_r, $children);
			}
		}
		
		return $_r;
	}
	
	/**
	 * 从数据库中取权限数据
	 */
	protected function readClassesArray()
	{
		$sql = "SELECT c.class_id, c.class_name, c.parent_id, c.class_identify, c.class_rank, c.class_status, c.class_lasttime, c.class_dateline
				FROM {{movie_classes}} c
				WHERE c.class_status>:class_status
				ORDER BY c.class_rank ASC";
		$this->db->prepare($sql);
		$this->db->bindValue(':class_status', self::STAT_STATUS_DELETED);
		$result = $this->db->queryAll();
		$_r = array();
		foreach($result as $k=>$v) {
			$row = array(
				'class_id' => $v['class_id'],
				'parent_id' => $v['parent_id'],
				'class_name' => $v['class_name'],
				'class_identify' => $v['class_identify'],
				'class_status' => $v['class_status'],
				'class_rank' => $v['class_rank'],
			);
			$_r[] = $row;
		}
		
		self::$__classes = $_r;
		
		return self::$__classes;
	}
	
	/**
	 * 根据PID获取权限名称
	 * @param mixed $pid			权限ID
	 * @return string $className	权限名称
	 */
	public function getClassNameById($parent_id)
	{
		$classes = is_array(self::$__classes) ? self::$__classes : $this->getClassesByCache();
		$class_name = '';
		foreach($classes as $_k=>$_v) {
			if($_v['parent_id'] == $parent_id) {
				$class_name = $_v['class_name'];
				break;
			}
		}
		
		return $class_name;
	}
	
	
	/**
	 * 获取权限节点Identify路径
	 */
	public function getIdentifyPath($parent_id)
	{
		$classes = is_array(self::$__classes) ? self::$__classes : $this->getClassesByCache();
		$_r = '/';
		foreach($classes as $_k=>$_v) {
			if($_v['class_id'] == $parent_id) {
				$_r .= $_v['class_identify'];
				if($_v['parent_id']>0) {
					$_r = $this->getIdentifyPath($_v['parent_id']) . $_r;
				}
				break;
			}
		}
		$_r = trim($_r, '/');
		
		return $_r;
	}
	
	/**
	 * 获取权限节点Purview路径
	 */
	public function getClassPath($pid)
	{
		$classes = is_array(self::$__classes) ? self::$__classes : $this->getClassesByCache();
		$_r = '/';
		foreach($classes as $_k=>$_v) {
			if($_v['class_id'] == $pid) {
				$_r .= $_v['class_name'];
				if($_v['parent_id']>0) {
					$_r = $this->getClassPath($_v['parent_id']) . $_r;
				}
				break;
			}
		}
		$_r = trim($_r, '/');
		
		return $_r;
	}
	
	/**
	 * 强制更新缓存
	 */
	public function updateCache()
	{
		//
		self::$__classes = null;
		
		//
		if(isset($this->cache)) {
			self::$__classes = $this->buildClassesList();
			Yii::app()->cache->set('movie.classes', self::$__classes);
		}
		
		return true;
	}
}
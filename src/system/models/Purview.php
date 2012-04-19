<?php

/**
 * This is the model class for table "{{purview}}".
 *
 * The followings are the available columns in table '{{purview}}':
 * @property string $purview_id
 * @property string $PName
 * @property string $PIdentify
 * @property integer $PState
 */
class PurviewModel extends CBaseModel {
	const STAT_STATUS_DELETED = 0;
	
	const STAT_STATUS_LOCKED = 1;
	
	const STAT_STATUS_NORMAL = 4;
    
    public static $__instance = null;
    
    public static $__model = 'PurviewModel';
	
	public static $__puviews = null;
	
	/**
	 * 取权限列表
	 */
	public function getPurviewList($parent_id = 0) {
		$purviews = $this->getPurviewsByCache();
		$parents = array($parent_id);
		$result = array();
		foreach($purviews as $key=>$purview) {
			if($purview['purview_id'] == $parent_id || in_array($purview['parent_id'], $parents)) {
				$parents[] = $purview['purview_id'];
				$result[] = $purview;
			}
		}
		
		return $result;
	}
	
	public function getPurviewById($purview_id) {
		$_r = array();
		$purviews = is_array(self::$__puviews) ? self::$__puviews : $this->readPurviewsArray();
		foreach ($purviews as $_k=>$_v) {
			if($_v['purview_id'] == $purview_id) {
				$_r = $_v;
				break;
			}
		}
		unset($purviews, $_k, $_v);
		
		return $_r;
	}
	
	public function getPurviewsByOwner($group_id, $user_id) {
		$_r = array();
		
		//
		$role = GroupModel::inst()->getGroupsByCache($group_id);
		$group_purviews = json_decode($role['purviews'], true);
		//
		$user = UserModel::inst()->getUserById($user_id);
		$user_purviews = json_decode($user['purviews'], true);
		
		//合并权限
		$purviews = is_array($group_purviews) ? array_merge($group_purviews, $user_purviews) : $user_purviews;
		
		//
		$_r = array();
		foreach($this->getPurviewsByCache() as $_k=>$_v) {
			if($role['purviews'] == 'all' || in_array($_v['purview_id'], $purviews)) {
				$_r[] = $_v;
			}
		}
		
		return $_r;
	}
	
	/**
	 * 读取权限数据结构
	 */
	public function getPurviewsByCache() {
		if(self::$__puviews!==null) {
			return self::$__puviews;
		}
		
		if(isset($this->cache) && (self::$__puviews = $this->cache->get('admin.purviews'))) {
			return self::$__puviews;
		}
		
		//无缓存数据， 读取数据库中的数据
		self::$__puviews = $this->readPurviewsArray();
		//重新整理数据
		self::$__puviews = $this->buildPurviewsList();
		//写入缓存
		if(isset($this->cache)) {
			$this->cache->set('admin.purviews', self::$__puviews);
		}
		
		return self::$__puviews;
	}
	
	/**
	 * 将权限数组数据，按顺序排序好，
	 */
	protected function buildPurviewsList($parent_id = 0, $deepth = 1) {
		$purviewArray = is_array(self::$__puviews) ? self::$__puviews : $this->readPurviewsArray();
		$result = array();
		foreach($purviewArray as $key=>$value) {
			if($value['parent_id'] == $parent_id) {
				$value['identify_tree'] = $this->getIdentifyPath($value['purview_id']);
				$value['deepth'] = $deepth;
				$result[] = $value;
				$child = $this->buildPurviewsList($value['purview_id'], $deepth+1);
				$result = array_merge($result, $child);
			}
		}
		
		return $result;
	}
	
	/**
	 * 从数据库中取权限数据
	 */
	protected function readPurviewsArray() {
		$sql = "SELECT p.purview_id, p.purview_name, p.parent_id, p.identify, p.purview_rank, p.status, p.lasttime, p.dateline
				FROM {{purview}} p
				WHERE p.status>:status
				ORDER BY p.purview_rank ASC";
		$params = array(
            ':status' => self::STAT_STATUS_DELETED,
        );
		$result = $this->db->queryAll($sql, $params);
		self::$__puviews = array();
		foreach($result as $k=>$v) {
			$row = array(
				'purview_id' => $v['purview_id'],
				'parent_id' => $v['parent_id'],
				'purview_name' => $v['purview_name'],
				'identify' => $v['identify'],
				'status' => $v['status'],
				'purview_rank' => $v['purview_rank'],
			);
			self::$__puviews[] = $row;
		}
		
		return self::$__puviews;
	}
	
	/**
	 * 根据purview_id获取权限名称
	 * @param mixed $pid			权限ID
	 * @return string $purviewName	权限名称
	 */
	public function getPurviewNameById($pid) {
		$purviewArray = is_array(self::$__puviews) ? self::$__puviews : $this->getPurviewCache();
		$purviewName = '';
		foreach($purviewArray as $key=>$value) {
			if($value['purview_id'] == $pid) {
				$purviewName = $value['purview_name'];
				break;
			}
		}
		
		return $purviewName;
	}
	
	
	/**
	 * 获取权限节点Identify路径
	 */
	public function getIdentifyPath($pid) {
		$purviewArray = is_array(self::$__puviews) ? self::$__puviews : $this->getPurviewsByCache();
		$result = '/';
		foreach($purviewArray as $key=>$value) {
			if($value['purview_id'] == $pid) {
				$result .= $value['identify'];
				if($value['parent_id']>0) {
					$result = $this->getIdentifyPath($value['parent_id']) . $result;
				}
				break;
			}
		}
		$result = trim($result, '/');
		
		return $result;
	}
	
	/**
	 * 获取权限节点Purview路径
	 */
	public function getPurviewPath($pid) {
		$purviewArray = is_array(self::$__puviews) ? self::$__puviews : $this->getPurviewsByCache();
		$result = '/';
		foreach($purviewArray as $key=>$value) {
			if($value['purview_id'] == $pid) {
				$result .= $value['purview_name'];
				if($value['parent_id']>0) {
					$result = $this->getPurviewPath($value['parent_id']) . $result;
				}
				break;
			}
		}
		$result = trim($result, '/');
		
		return $result;
	}
	
	/**
	 * 强制更新缓存
	 */
	public function updateCache() {
		//
		self::$__puviews = null;
		
		//
		if(isset($this->cache)) {
			self::$__puviews = $this->buildPurviewsList();
			$this->cache->set('admin.purviews', self::$__puviews);
		}
		
		return true;
	}
}
<?php

/**
 * This is the model class for table "{{role}}".
 *
 * The followings are the available columns in table '{{role}}':
 * @property string $RID
 * @property string $RName
 * @property string $RPIDs
 * @property integer $RShow
 * @property integer $RState
 */
class GroupModel extends CBaseModel {
	const STAT_STATUS_DELETED = 0;
	
	const STAT_STATUS_LOCKED = 1;
	
	const STAT_STATUS_NORMAL = 4;
	
	public static $__groups = null;
    
    public static $__instance = null;
    
    public static $__model = 'GroupModel';
	
	public $SRPID;
	
	public function getGroupNameById($group_id)
	{
		$_r = '';
		foreach (self::get_groups_by_cache() as $_k=>$_v) {
			if($_v['group_id'] == $group_id) {
				$_r = $_v['group_name'];
				break;
			}
		}
		
		return $_r;
	}
	
	public function getGroupById($group_id)
	{
		$_r = array();
		foreach ($this->getGroupsByCache() as $_k=>$_v) {
			if($_v['group_id'] == $group_id) {
				$_v['purviews'] = json_decode($_v['purviews'], true);
				$_v['purviews'] = is_array($_v['purviews']) ? $_v['purviews'] : array();
				$_r = $_v;
				break;
			}
		}
		
		return $_r;
	}
	
	/**
	 * 取出指定角色及下属角色数据
	 * @param unknown_type $group_id
	 */
	public function getGroupsByOwner($group_id)
	{
		$_r = array();
		$groups = $this->getGroupsByCache();
		$_a_r = array(
			$group_id => $group_id,
		);
		foreach($groups as $_k=>$_v) {
			if($groups[$group_id]['purviews'] == 'all' || in_array($_v['group_id'], $_a_r) || in_array($_v['parent_id'], $_a_r)) {
				$_a_r[$_v['group_id']] = $_v['group_id'];
				$_r[$_k] = $_v;
			}
		}
		unset($groups, $_a_r, $_k, $_v);
		
		return $_r;
	}
	 
	/**
	 * 从缓存读取角色数据
	 */
	public function getGroupsByCache($group_id = null)
	{
		if(self::$__groups !== null) {
			//return self::$__groups;
		} else if(isset($this->cache) && (self::$__groups = $this->cache->get('admin.groups'))) {
			//
		} else {
			//无缓存数据， 读取数据库中的数据
			self::$__groups = self::buildGroupsList();
			
			//写入缓存
			if(isset($this->cache)) {
				$this->cache->set('admin.groups', self::$__groups);
			}
		}
		
		if($group_id != null) {
			$_r = isset(self::$__groups[$group_id]) ? self::$__groups[$group_id] : array();
		} else {
			$_r = self::$__groups;
		}
		
		return $_r;
	}
	
	/**
	 * 将角色数组数据，按顺序排序好，
	 */
	protected function buildGroupsList($parent_id = 0, $deepth = 1)
	{
		$purviews = is_array(self::$__groups) ? self::$__groups : $this->readGroupsArray();
		$result = array();
		foreach($purviews as $_k=>$_v) {
			if($_v['parent_id'] == $parent_id) {
				$_v['nodes'] = $this->getGroupNodes($_v['parent_id']);
				$_v['deepth'] = $deepth;
				$result[] = $_v;
				$child = $this->buildGroupsList($_v['group_id'], $deepth+1);
				$result = array_merge($result, $child);
			}
		}
		
		$new_result = array();
		if($deepth == 1) {
			foreach($result as $_k=>$_v) {
				$new_result[$_v['group_id']] = $_v;
			}
			unset($result);
		} else {
			$new_result = $result;
			unset($result);
		}
		
		return $new_result;
	}
	
	/**
	 * 从数据库中读取角色数据
	 */
	protected function readGroupsArray() {
		$sql = "SELECT g.group_id, g.group_name, g.parent_id, g.purviews, g.is_system, g.group_rank, g.lasttime, g.dateline, g.status
				FROM {{group}} g
				WHERE g.status>:status
				ORDER BY g.group_rank ASC";
		$this->db->prepare($sql);
		$this->db->bindValue(':status', self::STAT_STATUS_DELETED);
		$result = $this->db->queryAll();
		$_r = array();
		foreach($result as $_k=>$_v) {
			$row = array(
				'group_id' => $_v['group_id'],
				'group_name' => $_v['group_name'],
				'parent_id' => $_v['parent_id'],
				'purviews' => $_v['purviews'],
				'is_system' => $_v['is_system'],
				'group_rank' => $_v['group_rank'],
				'lasttime' => $_v['lasttime'],
				'dateline' => $_v['dateline'],
				'status' => $_v['status'],
			);
			$_r[$_v['group_id']] = $row;
		}
		
		self::$__groups = $_r;
		
		return self::$__groups;
	}
	
	/**
	 * 获取角色节点Purview路径
	 */
	public function getGroupNodes($parent_id) {
		$groups = is_array(self::$__groups) ? self::$__groups : $this->getGroupsByCache();
		$result = '/';
		foreach($groups as $_k=>$_v) {
			if($_v['group_id'] == $parent_id) {
				$result .= $_v['group_name'];
				if($_v['parent_id']>0) {
					$result = $this->getGroupNodes($_v['parent_id']) . $result;
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
	public function updateCache()
	{
		//
		self::$__groups = null;
		
		if(isset($this->cache)) {
			self::$__models = $this->buildGroupsList();
			$this->cache->set('admin.groups', self::$__models);
		}
		
		return true;
	}
}
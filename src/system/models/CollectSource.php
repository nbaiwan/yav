<?php

class CollectSourceModel extends CBaseModel {
    
    public static $__instance = null;
    
    public static $__model = 'CollectSourceModel';
    
	//已删除
	const STAT_STATUS_DELETED = 0;
	//正常
	const STAT_STATUS_NORMAL = 4;
	//
	private static $__sources = null;
	
	/**
	 * 
	 * @param unknown_type $params
	 */
	public function getSourceById($collect_source_id, $allow_cache = true) {
		if(isset($this->cache) && $allow_cache) {
			//
			$cache_key =  "collect.source.row.{$collect_source_id}";
			$ret = json_decode($this->cache->get($cache_key), true);
			
			if(!empty($ret) && is_array($ret)) {
				return $ret;
			}
		}
		
		$sql = "SELECT collect_source_id, collect_source_name, collect_source_website, collect_source_remark, collect_source_rank, collect_source_lasttime, collect_source_dateline FROM {{collect_source}} WHERE collect_source_id=:collect_source_id";
		$this->db->prepare($sql);
		$this->db->bindValue(':collect_source_id', $collect_source_id);
		$this->db->execute();
		$ret = $this->db->queryRow();
		
		if(!empty($ret) && isset($this->cache) && $allow_cache) {
			//
			$this->cache->set($cache_key, json_encode($ret));
		}
		
		return $ret;
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $params
	 */
	public function Pages($params = array()) {
		//设置默认参数
		$_defaults_params = array(
			'allow_cache' => true,
			'page' => isset($_GET['page']) ? intval($_GET['page']) : 1,
			'pagesize' => 10,
		);
		$params = array_merge($_defaults_params, $params);
		
		//有开启缓存功能，则从缓存中取数据， 如果有数据，则直接返回结果
		if($params['allow_cache'] && isset($this->cache)) {
			$cache_key =  'collect.source.pages.' . serialize($params);
			$_r = $this->cache->get($cache_key);
			
			if($_r && is_array($_r)) {
				return $_r;
			}
		}
		
		//添加条件
		//$__addons = array('AND', 'u.game_union_status=:GState');
		//$__params = array('GState'=>1);

		//
		$builds = array(
            'select' => 'COUNT(`u`.`collect_source_id`) AS `COUNT`',
            'from' => array('{{collect_source}}', 'u')
        );
		if(isset($params['collect_source_status']) && !empty($params['collect_source_status'])) {
			$builds['where'] = array('AND', '`collect_source_status`=:collect_source_status');
			$sql_params[':collect_source_status'] = $params['collect_source_status'];
		} else {
			$builds['where'] = array('AND', '`collect_source_status`>:collect_source_status');
			$sql_params[':collect_source_status'] = 0;
		}
		//
		if(isset($params['collect_source_id']) && !empty($params['collect_source_id'])) {
			$builds['where'][] = array('AND', 'u.`collect_source_id`=:collect_source_id');
			$sql_params[':collect_source_id'] = $params['collect_source_id'];
		}
		//
		if(isset($params['collect_source_name']) && !empty($params['collect_source_name'])) {
			$builds['where'][] = array('LIKE', '`u`.`collect_source_name`', ':collect_source_name');
			$sql_params[':collect_source_name'] = "%{$params['collect_source_name']}%";
		}
		//
		//
		if(isset($params['searchKey']) && $params['searchKey']) {
			$builds['where'][] = array(
				'AND',
				array(
					'OR LIKE',
					'`g`.`collect_source_name`',
					':searchKey',
				),
			);
			$sql_params[':searchKey'] = "%{$params['searchKey']}%";
		}
		
        //$command = $this->db->createCommand();
        $sql = $this->buildQuery($builds);
		
		//统计数量
        $count = $this->db->queryScalar($sql, $sql_params);
		
		//分页处理
		$pages = new CPagination($count);
		
		//设置分页大小
		$pages->pageSize = $params['pagesize'];
		
		if(isset($params['orderby']) && $params['orderby']) {
			$builds['order'] = $params['orderby'];
		} else {
			$builds['order'] = array(
					'u.collect_source_rank ASC',
					'u.collect_source_id DESC',
				);
		}
		$builds['select'] = '`collect_source_id`, `collect_source_name`, `collect_source_website`, `collect_source_remark`, `collect_source_rank`, `collect_source_status`, `collect_source_lasttime`, `collect_source_dateline`';
        $pages->applyLimit($builds);
        $sql = $this->buildQuery($builds);
		$_r['pages'] = $pages;
		$_r['rows'] = $this->db->queryAll($sql, $sql_params);
		
		//有开启缓存，则把结果添加到缓存中
		if($params['allow_cache'] && isset($this->cache)) {
			$cache_cache_time = Setting::get_setting_value('COLLECT_SOURCE_PAGES_CACHE_TIME');
			$this->cache->set($_cache_key, json_encode($_r), $_cache_cache_time);
			unset($cache_cache_time, $cache_key);
		}

		return $_r;
	}
	
	/**
	 * 从缓存读取游戏工会数据
	 */
	public function getSourcesByCache() {
		if(self::$__sources !== null) {
			return self::$__sources;
		}
		
		if(isset($this->cache) && (self::$__sources = $this->cache->get('collect.sources'))) {
			return self::$__sources;
		}
		
		//无缓存数据， 读取数据库中的数据
		self::$__sources = $this->readSourceArray();
		//写入缓存
		if(isset($this->cache)) {
			$this->cache->set('collect.sources', self::$__sources);
		}
		
		return self::$__sources;
	}
	
	/**
	 * 从数据库中读取游戏工会数据
	 */
	protected function readSourceArray() {
		$sql = "SELECT * FROM {{collect_source}} ORDER BY collect_source_rank ASC";
		$this->db->prepare($sql);
		$sources = $this->db->queryAll();
		self::$__sources = array();
		foreach($sources as $_k=>$_v) {
			$row = array(
				'collect_source_id' => $_v['collect_source_id'],
				'collect_source_name' => $_v['collect_source_name'],
				'collect_source_website' => $_v['collect_source_website'],
				'collect_source_rank' => $_v['collect_source_rank'],
				'collect_source_remark' => $_v['collect_source_remark'],
				'collect_source_status' => $_v['collect_source_status'],
				'collect_source_lasttime' => $_v['collect_source_lasttime'],
				'collect_source_dateline' => $_v['collect_source_dateline'],
			);
			self::$__sources[] = $row;
		}
		
		return self::$__sources;
	}
	
	/**
	 * 根据game_union_id获取游戏工会名称
	 * @param mixed $game_union_id    游戏工会编号
	 * @return string $game_union_name    游戏工会名称
	 */
	public function getSourceNameById($collect_source_id) {
		$sources_array = is_array(self::$__sources) ? self::$__sources : $this->getSourcesByCache();
		$collect_source_name = '';
		foreach($sources_array as $key=>$value) {
			if($value['collect_source_id'] == $collect_source_id) {
				$collect_source_name = $value['collect_source_name'];
				break;
			}
		}
		
		return $collect_source_name;
	}
	
	/**
	 * 强制更新缓存
	 */
	public function updateCache()
	{
		//
		self::$__sources = null;
		
		//
		if(isset($this->cache)) {
            $__sources = $this->readSourceArray();
            $this->cache->set('collect.sources', $__sources);
        }
		
		return true;
	}
}
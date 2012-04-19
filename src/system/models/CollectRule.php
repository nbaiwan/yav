<?php

class CollectRuleModel extends CBaseModel {
	//已删除
	const STAT_DELETED = 0;
	//正常
	const STAT_NORMAL = 4;
	//
	private static $__rules = null;
    
    public static $__instance = null;
    
    public static $__model = 'CollectRuleModel';
	
	/**
	 * 
	 * @param unknown_type $params
	 */
	public function getRuleById($collect_rule_id, $allow_cache = true)
	{
		if(isset($this->cache) && $allow_cache) {
			//
			$cacheKey =  "collect.rule.row.{$collect_rule_id}";
			$ret = json_decode($this->cache->get($cacheKey), true);
			
			if(!empty($ret) && is_array($ret)) {
				return $ret;
			}
		}
		
		$sql = "SELECT collect_rule_id, collect_rule_name, collect_source_id, collect_model_id, collect_rule_remark, collect_rule_rank, collect_rule_lasttime, collect_rule_dateline FROM {{collect_rule}} WHERE collect_rule_id=:collect_rule_id";
		$params = array(
            ':collect_rule_id' => $collect_rule_id,
        );
		$ret = $this->db->queryRow($sql, $params);
		
		if(!empty($ret) && isset($this->cache) && $allow_cache) {
			//
			$this->cache->set($cacheKey, json_encode($ret));
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
			$cacheKey =  'collect.rule.pages.' . serialize($params);
			$ret = $this->cache->get($cacheKey);
			
			if($ret && is_array($ret)) {
				return $ret;
			}
		}
		
		//添加条件
        $builds = array(
            'select' => 'COUNT(ct.collect_rule_id) AS COUNT',
            'from' => array('{{collect_rule}}', 'ct'),
        );
        
		if(isset($params['collect_rule_status']) && !empty($params['collect_rule_status'])) {
			$builds['where'][] = array('AND', 'ct.collect_rule_status=:collect_rule_status');
			$sql_params[':collect_rule_status'] = $params['collect_rule_status'];
		} else {
			$builds['where'][] = array('AND', 'ct.collect_rule_status>:collect_rule_status');
			$sql_params[':collect_rule_status'] = 0;
		}
        
		if(isset($params['collect_rule_id']) && !empty($params['collect_rule_id'])) {
			$builds['where'][] = array('AND', 'ct.collect_rule_id=:collect_rule_id');
			$sql_params[':collect_rule_id'] = $params['collect_rule_id'];
		}
		//
		if(isset($params['collect_rule_name']) && !empty($params['collect_rule_name'])) {
			$builds['where'][] = array('LIKE', 'ct.collect_rule_name', '%:collect_rule_name%');
			$sql_params[':collect_rule_name'] = $params['collect_rule_name'];
		}
		//
		//
		if(isset($params['searchKey']) && $params['searchKey']) {
			$builds['where'][] = array(
				'OR',
				array(
					'OR LIKE',
					'ct.collect_rule_name',
					":searchKey",
				),
			);
			$sql_params[':searchKey'] = "%{$params['searchKey']}%";
		}
        $sql = $this->buildQuery($builds);
		
		//统计数量
		$count =  $this->db->queryScalar($sql, $sql_params);
		
		//分页处理
		$pages = new CPagination($count);
		
		//设置分页大小
		$pages->pageSize = $params['pagesize'];
		
		if(isset($params['orderby']) && $params['orderby']) {
			$builds['order'] = $params['orderby'];
		} else {
			$builds['order'] = array(
					'ct.collect_rule_dateline DESC',
				);
		}
        
        $builds['select'] = 'ct.collect_rule_id, ct.collect_rule_name';
        $pages->applyLimit($builds);
        $sql = $this->buildQuery($builds);
		$ret = array(
            'pages' => $pages,
            'rows' => $this->db->queryAll($sql, $sql_params),
        );
		
		//有开启缓存，则把结果添加到缓存中
		if($params['allow_cache'] && isset($this->cache)) {
			$cacheTimeout = SettingModel::getSettingValue('COLLECT_TEMPLATE_PAGES_CACHE_TIME');
			$this->cache->set($cacheKey, json_encode($ret), $cacheTimeout);
			unset($cacheTimeout, $cacheKey);
		}
		return $ret;
	}
	
	/**
	 * 从缓存读取游戏工会数据
	 */
	public function getRulesByCache() {
		if(self::$__rules !== null) {
			return self::$__rules;
		}
		
		if(isset($this->cache) && (self::$__rules = $this->cache->get('collect.rule'))) {
			return self::$__rules;
		}
		
		//无缓存数据， 读取数据库中的数据
		self::$__rules = $this->readRuleArray();
		//写入缓存
        if(isset($this->cache)) {
            $this->cache->set('collect.rule', self::$__rules);
        }
		
		return self::$__rules;
	}
	
	/**
	 * 从数据库中读取游戏工会数据
	 */
	protected function readRuleArray()
	{
		$sql = "SELECT * FROM {{collect_rule}} ORDER BY collect_rule_rank ASC";
		$_r = $this->db->queryAll($sql);
		self::$__rules = array();
		foreach($_r as $_k=>$_v) {
			$row = array(
				'collect_rule_id' => $_v['collect_rule_id'],
				'collect_rule_name' => $_v['collect_rule_name'],
				'collect_source_id' => $_v['collect_source_id'],
				'collect_model_id' => $_v['collect_model_id'],
				'collect_rule_rank' => $_v['collect_rule_rank'],
				'collect_rule_remark' => $_v['collect_rule_remark'],
				'collect_rule_lasttime' => $_v['collect_rule_lasttime'],
				'collect_rule_dateline' => $_v['collect_rule_dateline'],
			);
			self::$__rules[] = $row;
		}
		
		return self::$__rules;
	}
	
	/**
	 * 根据game_union_id获取游戏工会名称
	 * @param mixed $game_union_id    游戏工会编号
	 * @return string $game_union_name    游戏工会名称
	 */
	public function getRuleNameById($collect_rule_id) {
		$_rules_array = is_array(self::$__rules) ? self::$__rules : $this->getRulesByCache();
		$collect_rule_name = '';
		foreach($_rules_array as $key=>$value) {
			if($value['collect_rule_id'] == $collect_rule_id) {
				$collect_rule_name = $value['collect_rule_name'];
				break;
			}
		}
		
		return $collect_rule_name;
	}
	
	public function getModelIdById($collect_rule_id) {
		$_rules_array = is_array(self::$__rules) ? self::$__rules : $this->getRulesByCache();
		$collect_model_id = '';
		foreach($_rules_array as $key=>$value) {
			if($value['collect_rule_id'] == $collect_rule_id) {
				$collect_model_id = $value['collect_model_id'];
				break;
			}
		}
		
		return $collect_model_id;
	}
	
	/**
	 * 强制更新缓存
	 */
	public function updateCache()
	{
		//
		self::$__rules = null;
		
		//
        if(isset($this->cache)) {
            $__rules = $this->readRuleArray();
            $this->cache->set('collect.rule', $__rules);
        }
		
		return true;
	}
}
<?php

class CollectTemplateModel extends CBaseModel {
	//已删除
	const STAT_STATUS_DELETED = 0;
	//正常
	const STAT_STATUS_NORMAL = 4;
	//
	private static $__templates = null;
    
    public static $__instance = null;
    
    public static $__model = 'CollectTemplateModel';
	
	/**
	 * 
	 * @param unknown_type $params
	 */
	public function getTemplateById($collect_template_id, $allow_cache = false)
	{
		if(isset($this->cache) && $allow_cache) {
			//
			$cacheKey =  "collect.template.row.{$collect_template_id}";
			$ret = json_decode($this->cache->get($cacheKey), true);
			
			if(!empty($ret) && is_array($ret)) {
				return $ret;
			}
		}
		
		$sql = "SELECT * FROM {{collect_template}} WHERE collect_template_id=:collect_template_id";
        $params = array(
            ':collect_template_id' => $collect_template_id,
        );
		$ret = $this->db->queryRow($sql, $params);
		
		$ret['collect_template_list_rules'] && $ret['collect_template_list_rules'] = json_decode($ret['collect_template_list_rules'], true);
		if(!is_array($ret['collect_template_list_rules'])) {
			$ret['collect_template_list_rules'] = array();
		}
		$ret['collect_template_addons_rules'] && $ret['collect_template_addons_rules'] = json_decode($ret['collect_template_addons_rules'], true);
		if(!is_array($ret['collect_template_addons_rules'])) {
			$ret['collect_template_addons_rules'] = array();
		}
		
		$fields = CollectModelFieldModel::inst()->getFieldsByModelId($ret['collect_model_id']);
		foreach($fields as $_k=>$_v) {
			if(isset($ret['collect_template_addons_rules'][$_v['collect_fields_identify']])) {
				$ret['collect_template_addons_rules'][$_v['collect_fields_identify']] += $_v;
			} else {
				$_v['begin'] = '';
				$_v['end'] = '';
				$ret['collect_template_addons_rules'][$_v['collect_fields_identify']] = $_v;
			}
		}
		unset($fields, $_k, $_v);
		
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
			$cacheKey =  'collect.template.pages.' . serialize($params);
			$ret = $this->cache->get($cacheKey);
			
			if($_r && is_array($ret)) {
				return $ret;
			}
		}
		
		//添加条件
		$builds = array(
            'select' => 'COUNT(ct.collect_template_id) AS COUNT',
            'from' => array('{{collect_template}}', 'ct'),
            'join' => array(
                array('{{collect_source}}', 'cs', '`cs`.`collect_source_id`=`ct`.`collect_source_id`'),
                array('{{collect_model}}', 'cm', '`cm`.`collect_model_id`=`ct`.`collect_model_id`'),
            )
        );
        $sql_params = array();
		if(isset($params['collect_template_status']) && !empty($params['collect_template_status'])) {
            $builds['where'][] = array(
                'AND', 'ct.collect_template_status=:collect_template_status',
            );
            $sql_params[':collect_template_status'] = $params['collect_template_status'];
		} else {
            $builds['where'][] = array(
                'AND', 'ct.collect_template_status>:collect_template_status',
            );
            $sql_params[':collect_template_status'] = 0;
		}
		//
		if(isset($params['collect_template_id']) && !empty($params['collect_template_id'])) {
            $builds['where'][] = array(
                'AND', 'ct.collect_template_id=:collect_template_id',
            );
            $sql_params[':collect_template_id'] = $params['collect_template_id'];
		}
		if(isset($params['collect_model_id']) && !empty($params['collect_model_id'])) {
            $builds['where'][] = array(
                'AND', 'ct.collect_model_id=:collect_model_id',
            );
            $sql_params[':collect_model_id'] = $params['collect_model_id'];
		}
		if(isset($params['collect_source_id']) && !empty($params['collect_source_id'])) {
            $builds['where'][] = array(
                'AND', 'ct.collect_source_id=:collect_source_id',
            );
            $sql_params[':collect_source_id'] = $params['collect_source_id'];
		}
		if(isset($params['collect_template_id']) && !empty($params['collect_template_id'])) {
            $builds['where'][] = array(
                'AND', 'ct.collect_template_id=:collect_template_id',
            );
            $sql_params[':collect_template_id'] = $params['collect_template_id'];
		}
		//
		if(isset($params['collect_template_name']) && !empty($params['collect_template_name'])) {
            $builds['where'][] = array(
                'LIKE',
                'ct.collect_template_name',
                ':collect_template_name',
            );
            $sql_params[':collect_template_name'] = "%{$params['collect_template_name']}%";
		}
		//
		//
		if(isset($params['searchKey']) && $params['searchKey']) {
            $builds['where'][] = array(
                'LIKE',
                'ct.collect_template_name',
                ':collect_template_name',
            );
            $sql_params[':collect_template_name'] = "%{$params['searchKey']}%";
		}
        
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
                    'ct.collect_template_rank ASC',
                    'ct.collect_template_id DESC',
                );
		}
        $builds['select'] = 'ct.collect_template_id,ct.collect_template_rank, ct.collect_template_name, ct.collect_template_charset, cs.collect_source_name, cm.collect_model_name, ct.collect_template_lasttime, ct.collect_template_dateline';
        $pages->applyLimit($builds);
        $sql = $this->buildQuery($builds);
		$ret['pages'] = $pages;
		$ret['rows'] = $this->db->queryAll($sql, $sql_params);
		$charset = CollectTaskModel::getCharsets();
        foreach($ret['rows'] as $_k=>$_v){
			$ret['rows'][$_k]['collect_template_charset'] = $charset[$_v['collect_template_charset']];
		}
		//有开启缓存，则把结果添加到缓存中
		/*if($params['allow_cache'] && isset($this->cache)) {
			$_cache_cache_time = Setting::get_setting_value('COLLECT_TEMPLATE_PAGES_CACHE_TIME');
			$this->cache->set($cacheKey, json_encode($ret), $_cache_cache_time);
			unset($_cache_cache_time, $cacheKey);
		}*/
		return $ret;
	}
	
	/**
	 * 从缓存读取游戏工会数据
	 */
	public function getTemplatesByCache() {
		if(self::$__templates !== null) {
			return self::$__templates;
		}
		
		if(isset($this->cache) && (self::$__templates = $this->cache->get('collect.template'))) {
			return self::$__templates;
		}
		
		//无缓存数据， 读取数据库中的数据
		self::$__templates = $this->readTemplateArray();
		//写入缓存
		if(isset($this->cache)) {
			$this->cache->set('collect.template', self::$__templates);
		}
		
		return self::$__templates;
	}
	
	/**
	 * 从数据库中读取游戏工会数据
	 */
	protected function readTemplateArray() {
		$sql = "SELECT * FROM {{collect_template}} ORDER BY collect_template_rank ASC";
		$ret = $this->db->queryAll($sql);
		self::$__templates = array();
		foreach($ret as $_k=>$_v) {
			$row = array(
				'collect_template_id' => $_v['collect_template_id'],
				'collect_template_name' => $_v['collect_template_name'],
				'collect_source_id' => $_v['collect_source_id'],
				'collect_model_id' => $_v['collect_model_id'],
				'collect_template_remark' => $_v['collect_template_remark'],
				'collect_template_list_rules' => $_v['collect_template_list_rules'],
				'collect_template_addons_rules' => $_v['collect_template_addons_rules'],
				'collect_template_rank' => $_v['collect_template_rank'],
				'collect_template_lasttime' => $_v['collect_template_lasttime'],
				'collect_template_dateline' => $_v['collect_template_dateline'],
			);
			self::$__templates[] = $row;
		}
		
		return self::$__templates;
	}
	
	/**
	 * 根据game_union_id获取游戏工会名称
	 * @param mixed $game_union_id    游戏工会编号
	 * @return string $game_union_name    游戏工会名称
	 */
	public function getTemplateNameById($collect_template_id) {
		$_templates_array = is_array(self::$__templates) ? self::$__templates : $this->getTemplatesByCache();
		$collect_template_name = '';
		foreach($_templates_array as $key=>$value) {
			if($value['collect_template_id'] == $collect_template_id) {
				$collect_template_name = $value['collect_template_name'];
				break;
			}
		}
		
		return $collect_template_name;
	}
	
	public function getModelIdById($collect_template_id) {
		$_templates_array = is_array(self::$__templates) ? self::$__templates : $this->getTemplatesByCache();
		$collect_model_id = '';
		foreach($_templates_array as $key=>$value) {
			if($value['collect_template_id'] == $collect_template_id) {
				$collect_model_id = $value['collect_model_id'];
				break;
			}
		}
		
		return $collect_model_id;
	}
	
	/**
	 * 强制更新缓存
	 */
	public function updateCache() {
		//
		self::$__templates = null;
		
		self::$__templates = $this->readTemplateArray();
		if(isset($this->cache)) {
			$this->cache->set('collect.template', self::$__templates);
		}
		
		return true;
	}
}
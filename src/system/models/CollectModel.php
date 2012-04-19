<?php

class CollectModelModel extends CBaseModel {
	//已删除
	const STAT_STATUS_DELETED = 0;
	//正常
	const STAT_STATUS_NORMAL = 4;
    
    public static $__instance = null;
    
    public static $__model = "CollectModelModel";
	//
	private static $__models = null;
	
	/**
	 * 
	 * @param unknown_type $params
	 */
	public function getModelById($collect_model_id, $allow_cache = false)
	{
		if(isset($this->cache) && $allow_cache) {
			//
			$cache_key =  "collect.model.row.{$collect_model_id}";
			$ret = json_decode($this->cache->get($cache_key), true);
			
			if(!empty($ret) && is_array($ret)) {
				return $ret;
			}
		}
		
		$sql = "SELECT c.collect_model_id,c.content_model_id, c.collect_model_name,cf.content_model_id, c.collect_model_identify,c.collect_model_rank, c.collect_model_lasttime, c.collect_model_dateline FROM {{collect_model}} c LEFT JOIN {{content_model}} cf ON cf.content_model_id=c.content_model_id WHERE collect_model_id=:collect_model_id";
		$this->db->prepare($sql);
		$this->db->bindValue(':collect_model_id', $collect_model_id);
		$ret = $this->db->queryRow();
		
		if(!empty($ret) && $allow_cache && isset($this->cache)) {
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
	public function Pages($params = array())
	{
		//设置默认参数
		$_defaults_params = array(
			'allow_cache' => true,
			'page' => isset($_GET['page']) ? intval($_GET['page']) : 1,
			'pagesize' => 10,
		);
		$params = array_merge($_defaults_params, $params);
		
		//有开启缓存功能，则从缓存中取数据， 如果有数据，则直接返回结果
		if($params['allow_cache'] && isset($this->cache)) {
			$cache_key =  'collect.model.pages.' . serialize($params);
			$ret = $this->cache->get($cache_key);
			
			if($ret && is_array($ret)) {
				return $ret;
			}
		}
        
		//添加条件
		$builds = array(
            'select' => 'COUNT(`u`.`collect_model_id`) AS `COUNT`',
            'from' => array('{{collect_model}}', 'u')
        );
		if(isset($params['collect_model_status']) && !empty($params['collect_model_status'])) {
			$builds['where'] = array('AND', '`collect_model_status`=:collect_model_status');
			$sql_params[':collect_model_status'] = $params['collect_model_status'];
		} else {
			$builds['where'] = array('AND', '`collect_model_status`>:collect_model_status');
			$sql_params[':collect_model_status'] = 0;
		}
		//
		if(isset($params['collect_model_id']) && !empty($params['collect_model_id'])) {
			$builds['where'][] = array('AND', '`u`.`collect_model_id`=:collect_model_id');
			$sql_params[':collect_model_id'] = $params['collect_model_id'];
		}
		//
		if(isset($params['collect_model_name']) && !empty($params['collect_model_name'])) {
			$builds['where'][] = array(
                'LIKE',
                '`u`.`collect_model_name`',
                ':collect_model_name'
            );
			$sql_params[':collect_model_name'] = $params['collect_model_name'];
		}
		//
		//
		if(isset($params['searchKey']) && $params['searchKey']) {
			$builds['where'][] = array(
                'LIKE',
                '`u`.`collect_model_name`',
                ':searchKey'
            );
			$sql_params[':searchKey'] = $params['searchKey'];
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
					'`u`.`collect_model_rank` ASC',
					'`u`.`collect_model_id` DESC',
				);
		}
        
        $builds['select'] = '`u`.`collect_model_id`, `u`.`collect_model_name`, `u`.`collect_model_identify`, `u`.`collect_model_rank`, `u`.`collect_model_lasttime`, `u`.`collect_model_dateline`, `u`.`content_model_id`, `c`.`content_model_name`';
        $builds['leftJoin'] = array(
                '{{content_model}}', 'c', '`c`.`content_model_id`=`u`.`content_model_id`'
            );
        $pages->applyLimit($builds);
        $sql = $this->buildQuery($builds);
		$ret['pages'] = $pages;
		$ret['rows'] = $this->db->queryAll($sql, $sql_params);
		//有开启缓存，则把结果添加到缓存中
		if($params['allow_cache'] && isset($this->cache)) {
			$cache_cache_time = Setting::inst()->getSettingValue('COLLECT_MODEL_PAGES_CACHE_TIME');
			$this->cache->set($_cache_key, json_encode($ret), $cache_cache_time);
			unset($cache_cache_time, $cache_key);
		}
		return $ret;
	}
	
	public function getModelsByCache() {
		if(is_array(self::$__models) && count(self::$__models)) {
			return self::$__models;
		}
		
		if(isset($this->cache)) {
			if($this->cache && (self::$__models = $this->cache->get('collect.model'))) {
				return self::$__models;
			}
		}
		
		//无缓存数据， 读取数据库中的数据
		self::$__models = $this->readModelArray();
		//写入缓存
		if(isset($this->cache)) {
			$this->cache->set('collect.model', self::$__models);
		}
		
		return self::$__models;
	}
	

	protected function readModelArray() {
		$sql = "SELECT * FROM {{collect_model}} WHERE collect_model_status>:collect_model_status ORDER BY collect_model_rank ASC";
        $params = array(
            ':collect_model_status'=>self::STAT_STATUS_DELETED,
        );
		$ret = $this->db->queryAll($sql, $params);
		self::$__models = array();
		foreach($ret as $_k=>$_v) {
			$row = array(
				'collect_model_id' => $_v['collect_model_id'],
				'collect_model_name' => $_v['collect_model_name'],
				'collect_model_identify' => $_v['collect_model_identify'],
				'collect_model_rank' => $_v['collect_model_rank'],
				'collect_model_lasttime' => $_v['collect_model_lasttime'],
				'collect_model_dateline' => $_v['collect_model_dateline'],
			);
			self::$__models[] = $row;
		}
		
		return self::$__models;
	}
	public function getModelIdByIdentify($collect_model_identify) {
		$_models_array = is_array(self::$__models) ? self::$__models : $this->getModelsByCache();
		$collect_model_id = '';
		foreach($_models_array as $key=>$value) {
			if($value['collect_model_identify'] == $collect_model_identify) {
				$collect_model_id = $value['collect_model_id'];
				break;
			}
		}
		
		return $collect_model_id;
	}
	public function getModelNameById($collect_model_id) {
		$_models_array = is_array(self::$__models) ? self::$__models : $this->getModelsByCache();
		$collect_model_name = '';
		foreach($_models_array as $key=>$value) {
			if($value['collect_model_id'] == $collect_model_id) {
				$collect_model_name = $value['collect_model_name'];
				break;
			}
		}
		
		return $collect_model_name;
	}
	public function getModelIdentifyById($collect_model_id) {
		$_models_array = (is_array(self::$__models) && count(self::$__models)) ? self::$__models : $this->getModelsByCache();
		$collect_model_identify = '';
		foreach($_models_array as $key=>$value) {
			if($value['collect_model_id'] == $collect_model_id) {
				$collect_model_identify = $value['collect_model_identify'];
				break;
			}
		}
		
		return $collect_model_identify;
	}
	/**
	 * 强制更新缓存
	 */
	public function updateCache() {
		//
		self::$__models = null;
		
		//
		if(isset($this->cache)) {
			self::$__models = $this->readModelArray();
			$this->cache->set('collect.model', self::$__models);
		}
		
		return true;
	}
}
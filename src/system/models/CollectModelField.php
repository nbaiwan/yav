<?php

class CollectModelFieldModel extends CBaseModel {
	//已删除
	const STAT_STATUS_DELETED = 0;
	//正常
	const STAT_STATUS_NORMAL = 4;
    
    public static $__instance = null;
    
    public static $__model = 'CollectModelFieldModel';
	//字段类型
	private static $__types = array(
		1 => "单行文本",
		2 => "多行文本"
	);
	//
	private static $__fields = null;
	
	/**
	 * 
	 * @param unknown_type $params
	 */
	public function getFieldsIdByIdentify($game_model_id, $collect_fields_identify) {
		$_sql = "SELECT collect_fields_id FROM {{collect_model_fields}} WHERE collect_fields_identify=:collect_fields_identify AND collect_model_id=:collect_model_id AND collect_fields_status=:collect_fields_status";
		$this->db->prepare($sql);
		$this->db->bindValue(':collect_fields_identify', $collect_fields_identify);
		$this->db->bindValue(':collect_model_id', $game_model_id);
		$this->db->bindValue(':collect_fields_status', self::STAT_STATUS_NORMAL);
		$ret = $this->db->queryScalar();
        
		return $ret;
	}
	
	/**
	 * 通过模型ID，取得该模型所对应的所有字段
	 * @param int $collect_model_id   content_model_field_id
	 */
	public function getFieldsByModelId($collect_model_id){
		$sql = "SELECT c1.collect_fields_id, c1.collect_fields_name, c1.collect_fields_identify, c1.collect_fields_rank FROM {{collect_model_fields}} c1
				WHERE c1.collect_model_id=:collect_model_id AND c1.collect_fields_status=:collect_fields_status";
		$this->db->prepare($sql);
        $this->db->bindValue(':collect_model_id', $collect_model_id);
		$this->db->bindValue(':collect_fields_status', self::STAT_STATUS_NORMAL);
		$ret = $this->db->queryAll();
        
		return $ret;
	}
	
	
	public function getFieldsIdentifyById($game_model_id, $collect_fields_id) {
		$_sql = "SELECT collect_fields_identify FROM {{collect_model_fields}} WHERE collect_fields_id=:collect_fields_id AND collect_model_id=:collect_model_id AND collect_fields_status=:collect_fields_status";
		$this->db->prepare($sql);
		$this->db->bindValue(':collect_fields_id', $collect_fields_id);
		$this->db->bindValue(':collect_model_id', $game_model_id);
		$this->db->bindValue(':collect_fields_status', self::STAT_STATUS_NORMAL);
		$ret = $this->db->queryRow();
        
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
			$cacheKey =  md5('collect.fields.pages.' . serialize($params));
			$ret = $this->cache->get($cacheKey);
			
			if($ret && is_array($ret)) {
				return $ret;
			}
		}
        
		//添加条件
		$builds = array(
            'select' => 'COUNT(`mf`.`collect_fields_id`) AS `COUNT`',
            'from' => array('{{collect_model_fields}}', 'mf')
        );
		if(isset($params['collect_fields_status']) && !empty($params['collect_fields_status'])) {
			$builds['where'] = array('AND', '`mf`.`collect_fields_status`=:collect_fields_status');
			$sql_params[':collect_fields_status'] = $params['collect_fields_status'];
		} else {
			$builds['where'] = array('AND', '`mf`.`collect_fields_status`>:collect_fields_status');
			$sql_params[':collect_fields_status'] = 0;
		}
		
		//
		if(isset($params['collect_model_id']) && !empty($params['collect_model_id'])) {
			$builds['where'][] = array('AND', '`mf`.`collect_model_id`=:collect_model_id');
			$sql_params[':collect_model_id'] = $params['collect_model_id'];
		}
		
		if(isset($params['collect_fields_id']) && !empty($params['collect_fields_id'])) {
			$builds['where'][] = array('AND', '`mf`.`collect_fields_id`=:collect_fields_id');
			$sql_params[':collect_fields_id'] = $params['collect_fields_id'];
		}
		
		if(isset($params['collect_fields_name']) && !empty($params['collect_fields_name'])) {
			$builds['where'][] = array(
                'LIKE',
                '`mf`.`collect_fields_name`',
                ':collect_fields_name'
            );
			$sql_params[':collect_fields_name'] = "{$params['collect_fields_name']}";
		}
		
		if(isset($params['searchKey']) && !empty($params['searchKey'])) {
			$builds['where'][] = array(
                'LIKE',
                '`mf`.`collect_fields_name`',
                ':searchKey'
            );
			$sql_params[':searchKey'] = "%{$params['searchKey']}%";
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
					'`mf`.`collect_fields_rank` ASC',
					'`mf`.`collect_fields_id` DESC',
				);
		}
		
        $builds['select'] = '`mf`.`collect_fields_id`, `mf`.`collect_fields_name`,`mf`.`collect_fields_system`, `mf`.`collect_fields_belong`, `mf`.`collect_fields_identify`, `mf`.`collect_fields_rank`, `mf`.`collect_fields_lasttime`, `mf`.`collect_fields_type`, `cf`.`content_model_field_name`';
        $builds['leftJoin'] = array(
                '{{content_model_fields}}', 'cf', '`cf`.`content_model_field_id`=`mf`.`content_model_field_id`',
            );
        $pages->applyLimit($builds);
        $sql = $this->buildQuery($builds);
		$ret['pages'] = $pages;
		$ret['rows'] = $this->db->queryAll($sql, $sql_params);
		
		foreach($ret["rows"] as $_k=>$_v){
			$ret["rows"][$_k]['collect_fields_type'] = self::getFieldTypes($_v['collect_fields_type']);
		}
		
		//有开启缓存，则把结果添加到缓存中
		if($params['allow_cache'] && isset($this->cache)) {
			$cacheTime = Setting::inst()->getSettingValue('COLLECT_MODEL_PAGES_CACHE_TIME');
			$this->cache->set($cacheKey, json_encode($ret), $cacheTime);
			unset($cacheTime, $cacheKey);
		}
		return $ret;
	}
	
	/**
	 * 从缓存读取游戏工会数据
	 */
	public function getFieldsByCache() {
		if(self::$__fields !== null) {
			return self::$__fields;
		}
		
		$cache = $this->cache;
		if(isset($this->cache) && (self::$__fields = $this->cache->get('collect.model.fields'))) {
			return self::$__fields;
		}
		
		//无缓存数据， 读取数据库中的数据
		self::$__fields = self::read_fields_array();
		//写入缓存
		if(isset($this->cache)) {
			$this->cache->set('collect.fields', self::$__fields);
		}
		
		return self::$__fields;
	}
	
	/**
	 * 从数据库中读取游戏工会数据
	 */
	protected function readFieldsArray() {
		$sql = "SELECT * FROM {{collect_model_fields}} ORDER BY collect_fields_rank ASC";
		$cmd = $this->db->createCommand($sql);
		$cmd->execute();
		$_r = $cmd->queryAll();
		$result = array();
		foreach($_r as $_k=>$_v) {
			$row = array(
				'collect_fields_id' => $_v['collect_fields_id'],
				'collect_fields_name' => $_v['collect_fields_name'],
				'collect_fields_website' => $_v['collect_fields_website'],
				'collect_fields_charset' => $_v['collect_fields_charset'],
				'collect_fields_rank' => $_v['collect_fields_rank'],
				'collect_fields_remark' => $_v['collect_fields_remark'],
				'collect_fields_lasttime' => $_v['collect_fields_lasttime'],
				'collect_fields_dateline' => $_v['collect_fields_dateline'],
			);
			$result[] = $row;
		}
		
		self::$__fields = $result;
		
		return self::$__fields;
	}
	
	/**
	 * 根据game_union_id获取游戏工会名称
	 * @param mixed $game_union_id    游戏工会编号
	 * @return string $game_union_name    游戏工会名称
	 */
	public function getFieldsNameById($collect_fields_id) {
		$_fields_array = is_array(self::$__fields) ? self::$__fields : self::get_fields_by_cache();
		$collect_fields_name = '';
		foreach($_fields_array as $key=>$value) {
			if($value['collect_fields_id'] == $collect_fields_id) {
				$collect_fields_name = $value['collect_fields_name'];
				break;
			}
		}
		
		return $collect_fields_name;
	}
    
	public function getFieldById($collect_fields_id, $allow_cache = true) {
		if(isset($this->cache) && $allow_cache) {
			//
			$cacheKey =  "collect.source.row.{$collect_fields_id}";
			$ret = json_decode($this->cache->get($cacheKey), true);
			
			if(!empty($ret) && is_array($ret)) {
				return $ret;
			}
		}
		
		$sql = "SELECT * FROM {{collect_model_fields}} WHERE collect_fields_id=:collect_fields_id";
		$params = array(
            ':collect_fields_id' => $collect_fields_id,
        );
		$ret = $this->db->queryRow($sql, $params);
		
		if(!empty($ret) && isset($this->cache) && $allow_cache) {
			//
			$this->cache->set($cacheKey, json_encode($ret));
		}
		
		return $ret;
	}
	/**
	 * 强制更新缓存
	 */
	public function updateCache() {
		//
		self::$__fields = null;
		
		//
		if(isset($this->cache)) {
			self::$__fields = $this->readFieldsArray();
			$this->cache->set('collect.model.fields', self::$__fields);
		}
		
		return true;
	}
	
	public static function getFieldTypes($collect_fields_type = null) {
	
		if($collect_fields_type !== null) {
			$ret = isset(self::$__types[$collect_fields_type]) ? self::$__types[$collect_fields_type] : '';
		} else {
			$ret = array();
			foreach(self::$__types as $_k=>$_v) {
				$ret[$_k] = $_v;
			}
		}
		
		return $ret;
	}
}
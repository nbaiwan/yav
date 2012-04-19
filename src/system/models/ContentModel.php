<?php

class ContentModelModel extends CBaseModel {
	//启用
	const STAT_ALLOW_YES = 4;
	
	//禁用
	const STAT_ALLOW_NO = 1;
	
	//删除
	const STAT_DELETED = 0;
    
    public static $__instance = null;
    
    public static $__model = "ContentModelModel";
	
	private static $__models = null;
	
	private static $__status = array(
		self::STAT_ALLOW_YES => '启用',
		self::STAT_ALLOW_NO => '禁用',
	);
	 
	/**
	 * 从缓存读取内容模型数据
	 */
	public function getModelsByCache()
	{
		if(self::$__models !== null) {
			return self::$__models;
		}
		
		if(isset($this->cache) && (self::$__models = $this->cache->get('content.models'))) {
			return self::$__models;
		}
		
		//无缓存数据， 读取数据库中的数据
		self::$__models = $this->readModelsByCache();
		
		//写入缓存
		if(isset($this->cache)) {
			$this->cache->set('content.models', self::$__models);
		}
		
		return self::$__models;
	}
	 
	/**
	 * 从缓存读取内容模型数据，
	 */
	public function getModelById($content_model_id = null) {
		self::$__models = (self::$__models == null) ? $this->getModelsByCache() : self::$__models;
		
		$_r = array();
		if(!empty($content_model_id)) {
			foreach (self::$__models as $_k=>$_v) {
				if($_v['content_model_id'] == $content_model_id) {
					$_r = $_v;
					break;
				}
			}
		} else {
			foreach (self::$__models as $_k=>$_v) {
				if($_v['content_model_is_default']) {
					$_r = $_v;
					break;
				}
			}
		}
		
		return $_r;
	}
	
	/**
	 * 从数据库中读取内容模型数据
	 */
	protected function readModelsByCache() {
		$sql = "SELECT content_model_id, content_model_name, content_model_identify, content_model_edit_template, content_model_list_template, content_model_is_default, content_model_is_system, content_model_status, content_model_rank, content_model_lasttime, content_model_dateline
				FROM {{content_model}}
				WHERE content_model_status>:content_model_status
				ORDER BY content_model_rank ASC";
		$params = array(
            ':content_model_status' => 0
        );
		$ret = $this->db->queryAll($sql, $params);
		self::$__models = array();
		foreach($ret as $_k=>$_v) {
			$row = array(
				'content_model_id' => $_v['content_model_id'],
				'content_model_name' => $_v['content_model_name'],
				'content_model_identify' => $_v['content_model_identify'],
				'content_model_edit_template' => $_v['content_model_edit_template'],
				'content_model_list_template' => $_v['content_model_list_template'],
				'content_model_is_default' => $_v['content_model_is_default'],
				'content_model_is_system' => $_v['content_model_is_system'],
				'content_model_status' => $_v['content_model_status'],
				'content_model_rank' => $_v['content_model_rank'],
				'content_model_lasttime' => $_v['content_model_lasttime'],
				'content_model_dateline' => $_v['content_model_dateline'],
			);
			self::$__models[] = $row;
		}
		
		return self::$__models;
	}
	
	/**
	 * 根据content_model_id获取分类名称
	 * @param mixed $content_model_id    分类编号
	 * @return string $content_model_name    分类名称
	 */
	public function getModelNameById($content_model_id) {
		$_models_array = is_array(self::$__models) ? self::$__models : $this->getModelsByCache();
		$content_model_name = '';
		foreach($_models_array as $key=>$value) {
			if($value['content_model_id'] == $content_model_id) {
				$content_model_name = $value['content_model_name'];
				break;
			}
		}
		
		return $content_model_name;
	}
	
	/**
	 * 根据content_model_id获取分类名称
	 * @param mixed $content_model_id    分类编号
	 * @return string $content_model_name    分类名称
	 */
	public function getModelIdentifyById($content_model_id) {
		$_models_array = is_array(self::$__models) ? self::$__models : $this->getModelsByCache();
		$content_model_identify = '';
		foreach($_models_array as $key=>$value) {
			if($value['content_model_id'] == $content_model_id) {
				$content_model_identify = $value['content_model_identify'];
				break;
			}
		}
		
		return $content_model_identify;
	}
	
	/**
	 * 强制更新缓存
	 */
	public function updateCache()
	{
		//
		self::$__models = null;
		
		//
		if(isset($this->cache)) {
			self::$__models = $this->readModelsArray();
			$this->cache->set('content.models', self::$__models);
		}
		
		return true;
	}
	
	/**
	 * 获取模型状态
	 * @param mixed $content_model_status
	 */
	public function getModelStatus($content_model_status = null)
	{
		if($content_model_status) {
			$_r = '';
			if(isset(self::$__status[$content_model_status])) {
				$_r = self::$__status[$content_model_status];
			}
		} else {
			$_r = array();
			foreach(self::$__status as $_k=>$_v) {
				$_r[$_k] = self::$__status[$_v];
			}
		}
		
		return $_r;
	}
	
	/**
	 * 
	 * @param unknown_type $content_model_id
	 */
	public function getModelTableById($content_model_id) {
		/*
		 * 取出原有表结构
		 */
		$sql = "SELECT * FROM {{content_model_fields}}
				WHERE content_model_id=:content_model_id AND content_model_field_status>:content_model_field_status
				ORDER BY content_model_field_rank ASC";
		$params = array(
            ':content_model_id' => $content_model_id,
            ':content_model_field_status' => 0, 
        );
		$query = $this->db->queryAll($sql);
		$colums = array();
		foreach($query as $row) {
			$colums[] = $row;
		}
		
		return $colums;
	}
	
	/**
	 * 
	 * @param unknown_type $content_model_id
	 */
	public function getModelTableByIdentify($content_model_identify) {
		//$content_model_identify = self::getModel_id_by_identify($content_model_identify);
		//return self::getModel_table_by_id($content_model_identify);
	}
	
	/*
	 * 创建附加表 
	 */
	public function createAddonsTable($suffix) {
		//检查表是否存在， 如果存在， 直接返回
		$_table_name = $this->db->tablePrefix . 'content_addons' . $suffix;
		$sql = "SHOW TABLES;";
		$query = $cmd->queryAll($sql);
		foreach($query as $row) {
			if(in_array($_table_name, $row)) {
				return false;
			}
		}
		
		//表不存在， 创建新表
		unset($cmd, $query);
		$sql = "CREATE TABLE `{{content_addons{$suffix}}}` (
					`content_archives_id` INT UNSIGNED NOT NULL COMMENT '档案编号',
					`content_channel_id` INT UNSIGNED NOT NULL COMMENT '频道编号',
					`class_id` INT UNSIGNED NOT NULL COMMENT '栏目编号',
					PRIMARY KEY (`content_archives_id`)
				) ENGINE=MyISAM DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci;";
		if(!$cmd->execute($sql)) {
			return false;
		}
		
		return true;
	}
	
	/*
	 * 更新附加表 
	 */
	public function alterAddonsTable($suffix, $params = array()) {
		/*
		 * 检查表是否存在，不存在则先创建
		 */
		$this->createAddonsTable($suffix);
		
		$_table_name = $this->db->tablePrefix . 'content_addons' . $suffix;
		/*
		 * 取出原有表结构，并整理
		 */
		$sql = "SHOW COLUMNS FROM `{$_table_name}`;";
		$query = $this->db->queryAll($sql);
		$colums = array();
		foreach($query as $row) {
			$colums[$row['Field']] = $row;
		}
		unset($query, $row);
		
		/*
		 * 修改字段信息
		 */
		foreach($params as $colum) {
			$field = $colum['field_identify'];
			if(isset($colums[$field])) {
				$addons = "MODIFY COLUMN `{$field}`";
			} else {
				$addons = "ADD COLUMN `{$colum['field_identify']}`";
			}
			
			/*
			 * 字段类型或长度变更
			 */
			$_field_length = intval($colum['field_length']) > 0 ? intval($colum['field_length']) : 1;
			switch ($colum['field_type']) {
				case ContentModelFieldModel::DATA_TYPE_INTEGER:
					$type = "INT({$_field_length})";
				case ContentModelFieldModel::DATA_TYPE_INTEGER_UNSIGNED:
					$type = "INT({$_field_length}) UNSIGNED";
					break;
				case ContentModelFieldModel::DATA_TYPE_FLOAT:
					$type = "FLOAT(10, 2)";
					break;
				case ContentModelFieldModel::DATA_TYPE_DATE_TIME:
					$type = "INT(10) UNSIGNED";
					break;
				case ContentModelFieldModel::DATA_TYPE_IMAGE:
				case ContentModelFieldModel::DATA_TYPE_MEDIA:
				case ContentModelFieldModel::DATA_TYPE_ATTACH_OTHER:
					$type = "VARCHAR({$_field_length}) CHARACTER SET utf8 COLLATE utf8_general_ci";
					break;
				case ContentModelFieldModel::DATA_TYPE_SELECT:
				case ContentModelFieldModel::DATA_TYPE_RADIO:
				case ContentModelFieldModel::DATA_TYPE_CHECKBOX:
				case ContentModelFieldModel::DATA_TYPE_SINGLE_TEXT_VARCHAR:
					$type = "VARCHAR({$_field_length}) CHARACTER SET utf8 COLLATE utf8_general_ci";
					break;
				case ContentModelFieldModel::DATA_TYPE_SINGLE_TEXT_CHAR:
					if($_field_length>255) {
						$type = "TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL";
					} else {
						$type = "CHAR({$_field_length}) CHARACTER SET utf8 COLLATE utf8_general_ci";
					}
					break;
				case ContentModelFieldModel::DATA_TYPE_HTML_TEXT:
				case ContentModelFieldModel::DATA_TYPE_MULTI_TEXT:
					$type = "TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL";
					break;
			}
			/*
			 * 保存更新
			 */
			$sql = "ALTER TABLE `{$_table_name}` {$addons} {$type} NOT NULL COMMENT '{$colum['field_name']}';";
			//print_r($sql);
			//exit;
			$this->db->execute($sql);
		}
		unset($colums);
		
		/*
		 * 
		 */
		/*$sql = "SHOW COLUMNS FROM `{$_table_name}`;";
		$cmd = $this->db->createCommand($sql);
		$query = $cmd->queryAll();
		$colums = array();
		foreach($query as $row) {
			$colums[$row['Field']] = $row;
		}
		unset($query, $row);*/
		
		/*
		 * 修改字段顺序 
		 */
		/*$prefield = 'OSNumber';
		foreach($params as $colum) {
			$field = $colum['FieldIdentify'];
			$sql = "ALTER TABLE `{$_table_name}` MODIFY COLUMN `{$colum['FieldIdentify']}` {$colums[$field]['Type']} CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '{$colum['FieldName']}' AFTER `{$prefield}`";
			$cmd = $this->db->createCommand($sql);
			$cmd->execute();
			$prefield = $colum['FieldIdentify'];
		}*/
		
		return true;
	}
}
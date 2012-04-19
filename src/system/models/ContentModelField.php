<?php

class ContentModelField
{
	//启用
	const STAT_ALLOW_YES = 4;
	
	//禁用
	const STAT_ALLOW_NO = 1;
	
	//删除
	const STAT_DELETED = 0;
	
	// 数据类型
	// 单行文本(varchar)
	const DATA_TYPE_SINGLE_TEXT_VARCHAR = 10;
	// 单行文本(char)
	const DATA_TYPE_SINGLE_TEXT_CHAR = 20;
	// 多行文本
	const DATA_TYPE_MULTI_TEXT = 30;
	// HTML文本
	const DATA_TYPE_HTML_TEXT = 40;
	// 整数
	const DATA_TYPE_INTEGER = 50;
	// 整数(无符号)
	const DATA_TYPE_INTEGER_UNSIGNED = 60;
	// 小数
	const DATA_TYPE_FLOAT = 70;
	// 日期时间
	const DATA_TYPE_DATE_TIME = 80;
	// 图片
	const DATA_TYPE_IMAGE = 90;
	// 媒体文件
	const DATA_TYPE_MEDIA = 100;
	// 其他附件
	const DATA_TYPE_ATTACH_OTHER = 110;
	// OPTION下拉框
	const DATA_TYPE_SELECT = 120;
	// RADIO选项卡
	const DATA_TYPE_RADIO = 130;
	// CHECKBOX多选框
	const DATA_TYPE_CHECKBOX = 140;
	
	private static $__fields = null;
	
	private static $__status = array(
		self::STAT_ALLOW_YES => '启用',
		self::STAT_ALLOW_NO => '禁用',
	);
	
	private static $__types = array(
		self::DATA_TYPE_SINGLE_TEXT_VARCHAR => '单行文本(varchar)',
		self::DATA_TYPE_SINGLE_TEXT_CHAR => '单行文本(char)',
		self::DATA_TYPE_MULTI_TEXT => '多行文本',
		self::DATA_TYPE_HTML_TEXT => 'HTML文本',
		self::DATA_TYPE_INTEGER => '整数类型',
		self::DATA_TYPE_INTEGER_UNSIGNED => '无符号整数',
		self::DATA_TYPE_FLOAT => '小数类型',
		self::DATA_TYPE_DATE_TIME => '日期类型',
		self::DATA_TYPE_IMAGE => '图片',
		self::DATA_TYPE_MEDIA => '媒体文件',
		self::DATA_TYPE_ATTACH_OTHER => '其他附件',
		self::DATA_TYPE_SELECT => 'select下拉框',
		self::DATA_TYPE_RADIO => 'radio单选',
		self::DATA_TYPE_CHECKBOX => 'checkbox多选',
	);
	 
	/**
	 * 从缓存读取内容模型数据
	 */
	public static function get_fields_by_cache($content_model_id = null)
	{
		if(empty(self::$__fields)) {
			if(isset(Yii::app()->cache)) {
				$_cache_key = "content.model.fields";
				self::$__fields = Yii::app()->cache->get($_cache_key);
				//return self::$__fields;
			}
			
			if(empty(self::$__fields)) {
				//无缓存数据， 读取数据库中的数据
				self::$__fields = self::read_fields_array();
				
				//写入缓存
				if(isset(Yii::app()->cache)) {
					Yii::app()->cache->set($_cache_key, self::$__fields);
				}
			}
		}
		
		if($content_model_id > 0) {
			$_r = array();
			foreach(self::$__fields as $_k=>$_v) {
				if($_v['content_model_id'] == $content_model_id) {
					$_r[] = $_v;
				}
			}
		} else {
			$_r = self::$__fields;
		}
		
		return $_r;
	}
	 
	/**
	 * 从缓存读取内容模型数据
	 */
	public static function get_field_by_id($content_model_field_id)
	{
		self::$__fields = (self::$__fields == null) ? self::get_fields_by_cache() : self::$__fields;
		
		$_r = array();
		if(!empty($content_model_field_id)) {
			foreach (self::$__fields as $_k=>$_v) {
				if($_v['content_model_field_id'] == $content_model_field_id) {
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
	protected static function read_fields_array()
	{
		$sql = "SELECT content_model_field_id, content_model_id, content_model_field_name, content_model_field_identify, content_model_field_type, content_model_field_default, content_model_field_tips, content_model_field_max_length, content_model_field_rank, content_model_field_is_show, content_model_field_is_system, content_model_field_status, content_model_field_lasttime, content_model_field_dateline
				FROM {{content_model_fields}}
				WHERE content_model_field_status>:content_model_field_status
				ORDER BY content_model_field_rank ASC";
		$cmd = Yii::app()->db->createCommand($sql);
		$cmd->bindValue(':content_model_field_status', 0);
		$cmd->execute();
		$_r = $cmd->queryAll();
		$result = array();
		foreach($_r as $_k=>$_v) {
			$row = array(
				'content_model_field_id' => $_v['content_model_field_id'],
				'content_model_id' => $_v['content_model_id'],
				'content_model_field_name' => $_v['content_model_field_name'],
				'content_model_field_identify' => $_v['content_model_field_identify'],
				'content_model_field_type' => $_v['content_model_field_type'],
				'content_model_field_default' => $_v['content_model_field_default'],
				'content_model_field_tips' => $_v['content_model_field_tips'],
				'content_model_field_max_length' => $_v['content_model_field_max_length'],
				'content_model_field_rank' => $_v['content_model_field_rank'],
				'content_model_field_is_show' => $_v['content_model_field_is_show'],
				'content_model_field_is_system' => $_v['content_model_field_is_system'],
				'content_model_field_status' => $_v['content_model_field_status'],
				'content_model_field_lasttime' => $_v['content_model_field_lasttime'],
				'content_model_field_dateline' => $_v['content_model_field_dateline'],
			);
			$result[] = $row;
		}
		
		self::$__fields = $result;
		
		return self::$__fields;
	}
	
	/**
	 * 根据content_model_field_id获取分类名称
	 * @param mixed $content_model_field_id    分类编号
	 * @return string $content_model_field_name    分类名称
	 */
	public static function get_field_name_by_id($content_model_field_id)
	{
		$_fields_array = is_array(self::$__fields) ? self::$__fields : self::get_fields_by_cache();
		$content_model_field_name = '';
		foreach($_fields_array as $key=>$value) {
			if($value['content_model_field_id'] == $content_model_field_id) {
				$content_model_field_name = $value['content_model_field_name'];
				break;
			}
		}
		
		return $content_model_field_name;
	}
	
	/**
	 * 强制更新缓存
	 */
	public static function update_cache()
	{
		//
		self::$__fields = null;
		
		//
		if(isset(Yii::app()->cache)) {
			$_fields = self::read_fields_array();
			Yii::app()->cache->set('content.model.fields', $_fields);
		}
		
		return true;
	}
	
	/**
	 * 获取模型状态
	 * @param mixed $content_model_field_status
	 */
	public static function get_field_status($content_model_field_status = null)
	{
		if($content_model_field_status) {
			$_r = '';
			if(isset(self::$__status[$content_model_field_status])) {
				$_r = Yii::t('admincp', self::$__status[$content_model_field_status]);
			}
		} else {
			$_r = array();
			foreach(self::$__status as $_k=>$_v) {
				$_r[$_k] = Yii::t('admincp', $_v);
			}
		}
		
		return $_r;
	}
	
	/**
	 * 获取字段数据类型
	 * @param mixed $content_model_field_status
	 */
	public static function get_field_data_types($content_model_field_type = null)
	{
		if($content_model_field_type) {
			$_r = '';
			if(isset(self::$__types[$content_model_field_type])) {
				$_r = Yii::t('admincp', self::$__types[$content_model_field_type]);
			}
		} else {
			$_r = array();
			foreach(self::$__types as $_k=>$_v) {
				$_r[$_k] = Yii::t('admincp', $_v);
			}
		}
		
		return $_r;
	}
	
	public static function get_html_by_id($content_model_field_id, $value)
	{
		$_r = '';
		$_field = self::get_field_by_id($content_model_field_id);
		switch ($_field['content_model_field_id']) {
			case self::DATA_TYPE_SINGLE_TEXT_VARCHAR:
			case self::DATA_TYPE_SINGLE_TEXT_CHAR:
				$_r = "<input type=\"text\" name=\"Archive[{$_field['content_model_field_identify']}]\" id=\"Archive_{$_field['content_model_field_identify']}\" value=\"{$value}\" />";
				break;
			case self::DATA_TYPE_MULTI_TEXT:
				$_r = "<textarea name=\"Archive[{$_field['content_model_field_identify']}]\" id=\"Archive_{$_field['content_model_field_identify']}\">{$value}</textarea>";
				break;
			case self::DATA_TYPE_HTML_TEXT:
				$_r = "<textarea name=\"Archive[{$_field['content_model_field_identify']}]\" id=\"Archive_{$_field['content_model_field_identify']}\">{$value}</textarea>";
				break;
			case self::DATA_TYPE_INTEGER:
				$_r = "<input type=\"text\" name=\"Archive[{$_field['content_model_field_identify']}]\" id=\"Archive_{$_field['content_model_field_identify']}\" value=\"{$value}\" />";
				break;
			case self::DATA_TYPE_INTEGER_UNSIGNED:
				$_r = "<input type=\"text\" name=\"Archive[{$_field['content_model_field_identify']}]\" id=\"Archive_{$_field['content_model_field_identify']}\" value=\"{$value}\" />";
				break;
			case self::DATA_TYPE_FLOAT:
				$_r = "<input type=\"text\" name=\"Archive[{$_field['content_model_field_identify']}]\" id=\"Archive_{$_field['content_model_field_identify']}\" value=\"{$value}\" />";
				break;
			case self::DATA_TYPE_DATE_TIME:
				$_r = "<input type=\"text\" name=\"Archive[{$_field['content_model_field_identify']}]\" id=\"Archive_{$_field['content_model_field_identify']}\" value=\"{$value}\" />";
				break;
			case self::DATA_TYPE_IMAGE:
				$_r = "<input type=\"text\" name=\"Archive[{$_field['content_model_field_identify']}]\" id=\"Archive_{$_field['content_model_field_identify']}\" value=\"{$value}\" />";
				break;
			case self::DATA_TYPE_MEDIA:
				$_r = "<input type=\"text\" name=\"Archive[{$_field['content_model_field_identify']}]\" id=\"Archive_{$_field['content_model_field_identify']}\" value=\"{$value}\" />";
				break;
			case self::DATA_TYPE_ATTACH_OTHER:
				$_r = "<input type=\"text\" name=\"Archive[{$_field['content_model_field_identify']}]\" id=\"Archive_{$_field['content_model_field_identify']}\" value=\"{$value}\" />";
				break;
			case self::DATA_TYPE_SELECT:
				break;
			case self::DATA_TYPE_RADIO:
				break;
			case self::DATA_TYPE_CHECKBOX:
				break;
		}
		
		return $_r;
	}
}
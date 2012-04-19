<?php

/**
 * This is the model class for table "{{setting}}".
 *
 * The followings are the available columns in table '{{setting}}':
 * @property string $setting_id
 * @property string $setting_name
 * @property string $setting_identify
 * @property string $setting_value
 * @property integer $is_system
 * @property integer $rank
 */
class Setting
{
	public static $__settings = null;
	
	/*
	 * 从缓存中获取设置数据
	 */
	public static function get_settings_by_cache()
	{
		if(self::$__settings!==null) {
			return self::$__settings;
		} else {
			if(isset(Yii::app()->cache) && (self::$__settings = Yii::app()->cache->get('settings'))) {
				return self::$__settings;
			} else {
				//无缓存数据， 读取数据库中的数据
				self::$__settings = self::read_settings_array();
			}
			
			if(isset(Yii::app()->cache)) {
				//写入缓存
				Yii::app()->cache->set('settings', self::$__settings);
			}
		}
		
		/*
		if(!empty($key)) {
			return isset(self::$__settings[$key]) ? self::$__settings[$key]['value'] : '';
		}*/
		
		return self::$__settings;
	}
	
	public static function get_settings_by_group($setting_group)
	{
		self::$__settings || self::$__settings = self::get_settings_by_cache();
		
		$_r = array();
		foreach(self::$__settings as $_k=>$_v) {
			if($_v['setting_group'] == $setting_group) {
				$_r[] = $_v;
			}
		}
		
		return $_r;
	}
	
	/*
	 * 从缓存中获取设置数据
	 */
	public static function get_setting_value($key)
	{
		if(self::$__settings !== null) {
			//return self::$__settings;
		} else { 
		
			$cache = Yii::app()->cache;
			if(isset(Yii::app()->cache) && (self::$__settings = Yii::app()->cache->get('settings'))) {
				//return self::$__settings;
			} else {
				//无缓存数据， 读取数据库中的数据
				self::$__settings = self::read_settings_array();
				//写入缓存
				
			}
			
			if(isset(Yii::app()->cache)) {
				Yii::app()->cache->set('settings', self::$__settings);
			}
		}
		
		if(!empty($key)) {
			return isset(self::$__settings[$key]) ? self::$__settings[$key]['value'] : '';
		}
		
		return self::$__settings;
	}
	
	/*
	 * 从数据库中取设置数据
	 */
	protected static function read_settings_array()
	{
		/*$criteria = new CDbCriteria;
		$criteria->select = 'SettingName, SettingIdentify, SettingValue';
		$criteria->order = 'SettingGroup, Rank, SID ASC';
		$data = Setting::model()->findAll($criteria);*/
		
		$data = Yii::app()->db->createCommand()->select()
			->from("{{setting}}")
			->order("setting_group, rank, setting_id ASC")
			->queryAll();
		$result = array();
		foreach($data as $_k=>$_v) {
			$result[$_v['setting_identify']] = array(
				'setting_name' => $_v['setting_name'],
				'setting_group' => $_v['setting_group'],
				'setting_identify' => $_v['setting_identify'],
				'setting_type' => $_v['setting_type'],
				'setting_message' => $_v['setting_message'],
				'setting_options' => $_v['setting_options'],
				'setting_value' => $_v['setting_value'],
				'is_system' => $_v['is_system'],
				'is_show' => $_v['is_show'],
				'rank' => $_v['rank'],
			);
		}
		
		self::$__settings = $result;
		
		return self::$__settings;
	}
	
	/*
	 * 强制更新缓存
	 */
	public static function update_cache()
	{
		//$cache->delete('settings');
		if(isset(Yii::app()->cache)) {
			$settings = self::read_settings_array();
			Yii::app()->cache->set('settings', $settings);
		}
		
		return true;
	}
}
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
class SettingModel extends CBaseModel {
	public static $__settings = null;
    
    public static $__instance = null;
    
    public static $__model = 'SettingModel';
	
	/*
	 * 从缓存中获取设置数据
	 */
	public function getSettingsByCache()
	{
		if(self::$__settings!==null) {
			return self::$__settings;
		} else {
			if(isset($this->cache) && (self::$__settings = $this->cache->get('settings'))) {
				return self::$__settings;
			} else {
				//无缓存数据， 读取数据库中的数据
				self::$__settings = $this->readSettingsArray();
			}
			
			if(isset($this->cache)) {
				//写入缓存
				$this->cache->set('settings', self::$__settings);
			}
		}
		
		/*
		if(!empty($key)) {
			return isset(self::$__settings[$key]) ? self::$__settings[$key]['value'] : '';
		}*/
		
		return self::$__settings;
	}
	
	public function getSettingsByGroup($setting_group) {
		self::$__settings || self::$__settings = $this->getSettingsByCache();
		
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
	public function getSettingValue($key)
	{
		if(self::$__settings !== null) {
			//return self::$__settings;
		} else { 
		
			$cache = $this->cache;
			if(isset($this->cache) && (self::$__settings = $this->cache->get('settings'))) {
				//return self::$__settings;
			} else {
				//无缓存数据， 读取数据库中的数据
				self::$__settings = $this->readSettingsArray();
				//写入缓存
				
			}
			
			if(isset($this->cache)) {
				$this->cache->set('settings', self::$__settings);
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
	protected function readSettingsArray() {
		/*$criteria = new CDbCriteria;
		$criteria->select = 'SettingName, SettingIdentify, SettingValue';
		$criteria->order = 'SettingGroup, Rank, SID ASC';
		$data = Setting::model()->findAll($criteria);*/
		
        $sql = "SELECT * FROM {{setting}} ORDER BY setting_group, rank, setting_id ASC";
        $data = $this->db->queryAll($sql);
		self::$__settings  = array();
		foreach($data as $_k=>$_v) {
			self::$__settings[$_v['setting_identify']] = array(
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
		
		return self::$__settings;
	}
	
	/*
	 * 强制更新缓存
	 */
	public function updateCache() {
		if(isset($this->cache)) {
			$settings = $this->readSettingsArray();
			$this->cache->set('settings', $settings);
		}
		
		return true;
	}
}
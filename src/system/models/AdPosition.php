<?php

class AdPosition
{
	//已删除
	const STAT_DELETED = 0;
	//正常
	const STAT_NORMAL = 4;
	//
	private static $__positions = null;
	
	public static function get_position_by_identify()
	{
		self::$__positions = self::get_positions_by_cache();
		
		foreach(self::$__positions as $_k=>$_v) {
			if($_v['ad_position_identify'] == $identify) {
				
				break;
			}
		}
	}
	
	public static function get_position_name_by_id($ad_position_id)
	{
		$_sql = "SELECT ad_position_name FROM {{ad_position}} WHERE ad_position_id=:ad_position_id";
		$_cmd = Yii::app()->db->createCommand($_sql);
		$_cmd->bindValue(':ad_position_id', $ad_position_id);
		$_r = $_cmd->queryScalar();
		
		return $_r;
	}
	
	/**
	 * 
	 * @param unknown_type $params
	 */
	public static function get_one_by_id($id, $allow_cache = true)
	{
		if(isset(Yii::app()->cache) && $allow_cache) {
			//
			$_cache_key =  md5("ad.position.row.{$id}");
			$_r = json_decode(Yii::app()->cache->get($_cache_key), true);
			
			if(!empty($_r) && is_array($_r)) {
				return $_r;
			}
		}
		
		$_sql = "SELECT * FROM {{ad_position}} WHERE ad_position_id=:ad_position_id";
		$_cmd = Yii::app()->db->createCommand($_sql);
		$_cmd->bindValue(':ad_position_id', $id);
		$_r = $_cmd->queryRow();
		
		if(!empty($_r) && isset(Yii::app()->cache) && $allow_cache) {
			//
			Yii::app()->cache->set($_cache_key, json_encode($_r));
		}
		
		return $_r;
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $params
	 */
	public static function Pages($params = array())
	{
		//设置默认参数
		$_defaults_params = array(
			'allow_cache' => true,
			'page' => isset($_GET['page']) ? intval($_GET['page']) : 1,
			'pagesize' => 10,
		);
		$params = array_merge($_defaults_params, $params);
		
		//有开启缓存功能，则从缓存中取数据， 如果有数据，则直接返回结果
		if($params['allow_cache'] && isset(Yii::app()->cache)) {
			$_cache_key =  md5('ad.position.pages.' . serialize($params));
			$_r = Yii::app()->cache->get($_cache_key);
			
			if($_r && is_array($_r)) {
				return $_r;
			}
		}
		
		$cmd = Yii::app()->db->createCommand();
		$cmd->select('COUNT(ap.ad_position_id) AS COUNT')
			->from('{{ad_position}} ap')
			->join('{{ad_categories}} ac', 'ac.ad_categories_id=ap.ad_categories_id');
		
		//添加条件
		//$__addons = array('AND', 'u.union_status=:GState');
		//$__params = array('GState'=>1);

		//
		if(isset($params['ad_position_status']) && !empty($params['ad_position_status'])) {
			$__addons = array('AND', 'ap.ad_position_status=:ad_position_status');
			$__params = array(':ad_position_status'=>$params['ad_position_status']);
		} else {
			$__addons = array('AND', 'ap.ad_position_status>:ad_position_status');
			$__params = array(':ad_position_status'=>0);
		}
		if(isset($params['ad_categories_id']) && !empty($params['ad_categories_id'])) {
			$__addons[] = array('AND', 'ap.ad_categories_id=:ad_categories_id');
			$__params = array_merge($__params, array(':ad_categories_id'=>$params['ad_categories_id']));
		}
		if(is_array($__addons) && is_array($__params)){
			$cmd->where($__addons, $__params);
		}
		
		//统计数量
		$count =  $cmd->queryScalar();
		
		//分页处理
		$pages = new CPagination($count);
		
		//设置分页大小
		$pages->pageSize = $params['pagesize'];
		
		//清空前面执行过的SQL
		$cmd->setText('');
		if(isset($params['orderby']) && $params['orderby']) {
			$cmd->order($params['orderby']);
		} else {
			$cmd->order(array(
					
					'ap.ad_position_rank ASC',
					'ap.ad_position_id DESC',
				)
			);
		}
		$cmd->select('ap.ad_position_id, ap.ad_position_name, ap.ad_position_identify, ap.ad_position_identify, ap.ad_position_type, ap.ad_position_system, ap.ad_position_rank, ac.ad_categories_id, ac.ad_categories_name')
			->limit($pages->getLimit())
			->offset($pages->getOffset());
		$_r['pages'] = $pages;
		$_r['rows'] = $cmd->queryAll();
		
		//有开启缓存，则把结果添加到缓存中
		if($params['allow_cache'] && isset(Yii::app()->cache)) {
			$_cache_cache_time = Setting::get_setting_value('AD_POSITION_PAGES_CACHE_TIME');
			Yii::app()->cache->add($_cache_key, json_encode($_r), $_cache_cache_time);
			unset($_cache_cache_time, $_cache_key);
		}
		return $_r;
	}
	
	public static function get_js_path(){
		return str_replace('/',DIRECTORY_SEPARATOR,dirname(Yii::getPathOfAlias('webroot')).'/system/static/ad/js');
	}
	
	public static function get_positions_by_cache()
	{
		
	}
	
	protected static function read_model_array()
	{
		
	}
	
	/**
	 * 强制更新缓存
	 */
	public static function update_cache()
	{
		
	}
}
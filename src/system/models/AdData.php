<?php

class AdData
{
	//已删除
	const STAT_DELETED = 0;
	//正常
	const STAT_NORMAL = 4;
	
	static $PAGE = array(1=>"首页", 2=>"新闻页", 3=>"游戏页", 4=>"指数页", 5=>"搜索页", 6=>"更多页");
	//
	private static $__models = null;
	
	public static function get_relative_info($ad_position_id, $ad_data_relative_id){
		$re = array();
		$pos = AdPosition::get_one_by_id($ad_position_id, false);
		if($pos['ad_position_relative_type'] == 'archives'){
			$archives_info = ContentArchives::get_archive_by_id($ad_data_relative_id, false);
			$re['ad_data_subject'] = $archives_info['content_archives_subject'];
			$re['ad_data_link'] = Common::sign_archives_url($ad_data_relative_id);
		}
		return $re;
	}
	public static function get_ads_by_posidiont_identify($identify, $allow_cache = true)
	{
		if(isset(Yii::app()->cache) && $allow_cache) {
			//
			$_cache_key =  md5("game.ad.data.identify.{$identify}");
			$_r = json_decode(Yii::app()->cache->get($_cache_key), true);
			
			if(!empty($_r) && is_array($_r)) {
				return $_r;
			}
		}
		
		$_sql = "SELECT ad.*, ap.ad_position_relative_type FROM {{ad_position}} ap
				INNER JOIN {{ad_data}} ad ON ad.ad_position_id=ap.ad_position_id
				WHERE ad.ad_data_status=:ad_data_status AND ap.ad_position_identify = :ad_position_identify AND ad.ad_data_is_show=:ad_data_is_show
				ORDER BY ap.ad_position_rank, ad.ad_data_rank ASC";
		$_cmd = Yii::app()->db->createCommand($_sql);
		$_cmd->bindValue(':ad_data_status', self::STAT_NORMAL, PDO::PARAM_INT);
		$_cmd->bindValue(':ad_position_identify', $identify, PDO::PARAM_STR);
		$_cmd->bindValue(':ad_data_is_show', 1, PDO::PARAM_INT);
		$_r = $_cmd->queryAll();
		foreach($_r as $_k=>$_v) {
			if($_v['ad_position_relative_type'] == 'archives') {
				$_r[$_k] += ContentArchives::get_archive_by_id($_v['ad_data_relative_id']);
				if($_v['ad_data_subject'] == '') {
					$_r[$_k]['ad_data_subject'] = $_r[$_k]['content_archives_subject'];
				}
				if($_v['ad_data_link'] == '') {
					$_r[$_k]['ad_data_link'] = Common::sign_archives_url($_v['ad_data_relative_id']);
				}
			}
			if($_v['ad_data_image_md5'] != '') {
				$_r[$_k]['image'] = UploadFile::get_file_path($_v['ad_data_image_md5'], 'ad/images');
			}
			if($_v['ad_data_flash_md5'] != '') {
				$_r[$_k]['flash'] = UploadFile::get_file_path($_v['ad_data_flash_md5'], 'ad/flash');
			}
		}
		
		if(!empty($_r) && isset(Yii::app()->cache) && $allow_cache) {
			//
			Yii::app()->cache->set($_cache_key, json_encode($_r));
		}
		
		return $_r;
	}
	
	public static function get_subject_by_id($ad_data_id)
	{
		$_sql = "SELECT ad_data_subject FROM {{ad_data}} WHERE ad_data_id=:ad_data_id";
		$_cmd = Yii::app()->db->createCommand($_sql);
		$_cmd->bindValue(':ad_data_id', $ad_data_id, PDO::PARAM_INT);
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
			$_cache_key =  md5("ad.data.row.{$id}");
			$_r = json_decode(Yii::app()->cache->get($_cache_key), true);
			
			if(!empty($_r) && is_array($_r)) {
				return $_r;
			}
		}
		
		$_sql = "SELECT * FROM {{ad_data}} WHERE ad_data_id=:ad_data_id";
		$_cmd = Yii::app()->db->createCommand($_sql);
		$_cmd->bindValue(':ad_data_id', $id, PDO::PARAM_INT);
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
		$cmd->select('COUNT(gad.ad_data_id) AS COUNT')
			->from('{{ad_data}} gad');
		
		//添加条件
		//$__addons = array('AND', 'u.union_status=:GState');
		//$__params = array('GState'=>1);

		//
		if(isset($params['ad_data_status']) && !empty($params['ad_data_status'])) {
			$__addons = array('AND', 'gad.ad_data_status=:ad_data_status');
			$__params = array(':ad_data_status'=>$params['ad_data_status']);
		} else {
			$__addons = array('AND', 'gad.ad_data_status>:ad_data_status');
			$__params = array(':ad_data_status'=>0);
		}
		if(isset($params['ad_position_id']) && !empty($params['ad_position_id'])) {
			$__addons[] = array('AND', 'gad.ad_position_id=:ad_position_id');
			$__params = array_merge($__params, array(':ad_position_id'=>$params['ad_position_id']));
		}
		if(isset($params['search_key']) && !empty($params['search_key'])) {
			$__addons[] = array(
				'AND',
				'gad.ad_data_subject LIKE :search_key',
			);
			$__params = array_merge($__params, array(':search_key'=>"%{$params['search_key']}%"));
			
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
					'gad.ad_data_rank ASC',
					'gad.ad_data_id DESC',
				)
			);
		}
		$cmd->select('gad.ad_data_id, gad.ad_data_subject, gad.ad_data_type, gad.ad_data_link, gap.ad_position_name, gad.ad_data_rank, gad.ad_data_is_show,gad.ad_data_relative_id,gad.ad_position_id')
			->join("{{ad_position}} gap","gad.ad_position_id=gap.ad_position_id")
			->limit($pages->getLimit())
			->offset($pages->getOffset());
		$_r['pages'] = $pages;
		$_r['rows'] = $cmd->queryAll();
		
		//有开启缓存，则把结果添加到缓存中
		if($params['allow_cache'] && isset(Yii::app()->cache)) {
			$_cache_cache_time = Setting::get_setting_value('AD_DATA_PAGES_CACHE_TIME');
			Yii::app()->cache->add($_cache_key, json_encode($_r), $_cache_cache_time);
			unset($_cache_cache_time, $_cache_key);
		}
		return $_r;
	}
	
	public static function get_models_by_cache()
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
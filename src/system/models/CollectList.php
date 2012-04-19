<?php

class CollectList
{
	//已删除
	const STAT_DELETED = 0;
	//正常
	const STAT_NORMAL = 4;
	//
	private static $__lists = null;
	
	/**
	 * 
	 * @param unknown_type $params
	 */
	public static function get_list_by_id($collect_list_id, $allow_cache = true)
	{
		
		$_sql = "SELECT * FROM {{collect_list}} WHERE collect_list_id=:collect_list_id";
		$_cmd = Yii::app()->db->createCommand($_sql);
		$_cmd->bindValue(':collect_list_id', $collect_list_id);
		$_cmd->execute();
		$_r = $_cmd->queryRow();
		return $_r;
	}
	/**
	 * 通过采集列表的ID，一次将关系列表的相关数据取出来；
	 * @param int $collect_list_id  列表ID
	 */
	public static function get_list_model_by_id($collect_list_id){
		$_sql = "SELECT cl.`collect_task_id`,  ct.`collect_template_id`,cte.`collect_model_id`,cte.`collect_template_id`,cm.`collect_model_id`,cm.`content_model_id`,cl.`collect_list_url`,cl.`collect_list_title`, 
				 ct.`collect_task_name`, ct.`collect_task_rulearr`, ct.`collect_task_totalpagereg`,ct.`collect_task_filter`, ct.`collect_task_listreg`,  ct.`collect_task_pagestart` ,  ct.`collect_task_pageend`,  
				 ct.`collect_task_liststart`, ct.`collect_task_listend`,ct.`collect_task_pagerule` ,  ct.`collect_task_listurls` ,  ct.`collect_task_rank` , ct.`collect_task_saveimg`,  ct.`collect_task_charset`,  
				 ct.`game_id`,  ct.`content_class_id`,ct.`collect_task_status` , ct.`collect_task_lasttime` ,  ct.`collect_task_dateline` ,  ct.`collect_task_lastcollecttime` ,  cte.`collect_template_name`,
				 cte.`collect_source_id`, cte.`collect_template_rank`,cte.`collect_template_remark`,cte.`collect_template_lasttime`, cte.`collect_template_dateline` FROM collect_list cl  
				 LEFT JOIN collect_task ct ON ct.collect_task_id=cl.collect_task_id 
				 LEFT JOIN collect_template cte ON cte.collect_template_id=ct.collect_template_id 
				 LEFT JOIN collect_model cm ON cte.collect_model_id=cm.collect_model_id 
				 LEFT JOIN content_model cml ON cml.content_model_id=cm.content_model_id
  				 where cl.collect_list_id=:collect_list_id";
		
		$_cmd = Yii::app()->db->createCommand($_sql);
		$_cmd->bindValue(':collect_list_id', $collect_list_id);
		$_cmd->execute();
		$_r = $_cmd->queryRow();
		$collect_task = array(
			'collect_task_id'=>$_r['collect_task_id'], 
			'collect_template_id'=>$_r['collect_template_id'], 
			'collect_task_name'=>$_r['collect_task_name'], 
			'collect_task_rulearr'=>$_r['collect_task_rulearr'], 
			'collect_task_totalpagereg'=>$_r['collect_task_totalpagereg'],
			'collect_task_filter'=>$_r['collect_task_filter'],
			'collect_task_listreg'=>$_r['collect_task_listreg'],
			'collect_task_pagestart'=>$_r['collect_task_pagestart'] ,
			'collect_task_pageend'=>$_r['collect_task_pageend'],
			'collect_task_liststart'=>$_r['collect_task_liststart'],
			'collect_task_listend'=>$_r['collect_task_listend'],
			'collect_task_pagerule'=>$_r['collect_task_pagerule'] ,
			'collect_task_listurls'=>$_r['collect_task_listurls'] ,
			'collect_task_rank'=>$_r['collect_task_rank'] ,
			'collect_task_saveimg'=>$_r['collect_task_saveimg'],
			'collect_task_charset'=>$_r['collect_task_charset'],
			'game_id'=>$_r['game_id'],
			'content_class_id'=>$_r['content_class_id'],
			'collect_task_status'=>$_r['collect_task_status'] ,
			'collect_task_lasttime'=>$_r['collect_task_lasttime'] ,
			'collect_task_dateline'=>$_r['collect_task_dateline'] ,
			'collect_task_lastcollecttime '=>$_r['collect_task_lastcollecttime'],
		);
		$collect_list = array(
			'collect_task_id'=>$_r['collect_task_id'],
			'collect_list_url'=>$_r['collect_list_url'],
			'collect_list_title'=>$_r['collect_list_title'],
		);
		$collect_model = array(
			'collect_model_id'=>$_r['collect_model_id'],
			
		);
		$content_model = array(
			'content_model_id'=>$_r['content_model_id'],
			
		);
		return array('collect_task'=>$collect_task,'collect_list'=>$collect_list,'collect_model'=>$collect_model,'content_model'=>$content_model);
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
		
		$cmd = Yii::app()->db->createCommand();
		$cmd->select('COUNT(u.collect_list_id) AS COUNT')
			->from('{{collect_list}} u')
			->join('{{collect_task}} ct', 'u.collect_task_id=ct.collect_task_id');
		if(intval($params['collect_task_id'])) {
			$__addons = array('AND', 'u.collect_task_id=:collect_task_id');
			$__params = array(':collect_task_id'=>$params['collect_task_id']);
		}else{
			$__addons = array('AND', 'u.collect_task_id!=:collect_task_id');
			$__params = array(':collect_task_id'=>0);
		}
		if($params['collect_list_check']!=="") {
			$__addons[] = array('AND', 'u.collect_list_check=:collect_list_check');
			$__params = array_merge($__params, array(':collect_list_check'=>$params['collect_list_check']));
		}
		if($params['collect_list_day'] && is_array($params['collect_list_day'])) {
			$__addons[] = array('AND', 'u.collect_list_day BETWEEN "'.$params['collect_list_day'][0].'" AND "'.$params['collect_list_day'][1].'"');
		}elseif($params['collect_list_day']){
			$__addons[] = array('AND', 'u.collect_list_day=:collect_list_day');
			$__params = array_merge($__params, array(':collect_list_day'=>$params['collect_list_day']));
		}
		if($params['collect_list_title']) {
			$__addons[] = array(
				'AND',
				array(
					'OR LIKE',
					'u.collect_list_title',
					"%{$params[collect_list_title]}%",
				),
			);
			
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
					'u.collect_list_day DESC',
				)
			);
		}
		$cmd->select('u.*,ct.collect_task_name')
			->limit($pages->getLimit())
			->offset($pages->getOffset());
		$_r['pages'] = $pages;
		$_r['rows'] = $cmd->queryAll();
		
		//有开启缓存，则把结果添加到缓存中
		if($params['allow_cache'] && isset(Yii::app()->cache)) {
			$_cache_cache_time = Setting::get_setting_value('COLLECT_MODEL_PAGES_CACHE_TIME');
			Yii::app()->cache->add($_cache_key, json_encode($_r), $_cache_cache_time);
			unset($_cache_cache_time, $_cache_key);
		}
		return $_r;
	}
	
}
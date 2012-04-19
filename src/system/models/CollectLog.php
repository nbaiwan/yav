<?php

class CollectLog
{
	
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
			'pagesize' => 30,
		);
		$params = array_merge($_defaults_params, $params);
		
		$cmd = Yii::app()->db->createCommand();
		$cmd->select('COUNT(u.collect_log_id) AS COUNT')
			->from('{{collect_log}} u');
		if(intval($params['collect_log_insert_time'])) {
			$__addons = array('AND', 'u.collect_log_insert_time=:collect_log_insert_time');
			$__params = array(':collect_log_insert_time'=>$params['collect_log_insert_time']);
		}
		if($params['collect_log_msg']) {
			$__addons[] = array(
				'AND',
				array(
					'OR LIKE',
					'u.collect_log_msg',
					"%{$params[collect_log_msg]}%",
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
					'u.collect_log_id DESC',
				)
			);
		}
		$cmd->select('u.*')
			->limit($pages->getLimit())
			->offset($pages->getOffset());
		$_r['pages'] = $pages;
		$_r['rows'] = $cmd->queryAll();
		return $_r;
	}
	
}
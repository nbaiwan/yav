<?php

class ContentArchives
{
	//已删除
	const STAT_DELETED = 0;
	//待审核
	const STAT_WAIT_AUDIT = 1;
	//已审核
	const STAT_NORMAL = 4;
	private static $__status = array(
		self::STAT_WAIT_AUDIT => '待审核',
		self::STAT_NORMAL => '正常',
	);
	
	//头条
	const STAT_ARCHIVES_FLAG_H = 'h';
	//推荐
	const STAT_ARCHIVES_FLAG_C = 'c';
	//加粗
	const STAT_ARCHIVES_FLAG_B = 'b';
	//图片
	const STAT_ARCHIVES_FLAG_P = 'p';
	//跳转
	const STAT_ARCHIVES_FLAG_J = 'j';
	
	private static $__flags = array(
		self::STAT_ARCHIVES_FLAG_H => '头条',
		self::STAT_ARCHIVES_FLAG_C => '推荐',
		self::STAT_ARCHIVES_FLAG_B => '加粗',
		self::STAT_ARCHIVES_FLAG_P => '图片',
		self::STAT_ARCHIVES_FLAG_J => '跳转',
	);
	
	/**
	 * 根据content_archives_id获取文档
	 * @param mixed $content_archives_id    文档编号
	 * @return string $content_archives     文档
	 */
	public static function get_archive_by_id($content_archives_id, $allow_cache = false)
	{
		$content_archives_name = '';
		if(!$content_archives_id) {
			return '';
		}
		
		//有开启缓存功能，则从缓存中取数据， 如果有数据，则直接返回结果
		if($allow_cache && Yii::app()->cache) {
			$_cache_key =  'content.archive.row.'.$content_archives_id;
			$_r = Yii::app()->cache->get($_cache_key);
			
			if($_r && is_array($_r)) {
				return $_r;
			}
		}
		
		$cmd = Yii::app()->db->createCommand();
		$cmd->select('a.content_archives_id, ac.content_model_id, m.content_model_name, ac.class_id, ac.class_name, a.content_archives_subject, a.content_archives_color, a.content_archives_short_subject, a.content_archives_flag, a.content_archives_jump_url, a.content_archives_source, a.content_archives_author, a.content_archives_thumb, a.content_archives_keywords, a.content_archives_summary, a.content_archives_status, a.content_archives_rank, a.content_archives_pubtime, a.content_archives_lasttime, a.content_archives_dateline')
			->from('{{content_archives}} a')
			->join('{{content_archives_classes_relating}} acr', 'acr.content_archives_id=a.content_archives_id')
			->join('{{content_archives_classes}} ac', 'ac.class_id=acr.class_id')
			->join('{{content_model}} m', 'm.content_model_id=ac.content_model_id')
			->where('a.content_archives_id=:content_archives_id', array('content_archives_id'=>$content_archives_id));
		$_r = $cmd->queryRow();
		
		if($_r) {
			//附加表结构
			$_r['content_model_colums'] = ContentModel::get_model_table_by_id($_r['content_model_id']);
			
			//附加表数据
			$content_model_identify = ContentModel::get_model_identify_by_id($_r['content_model_id']);
			$_sql = "SELECT * FROM {{content_addons{$content_model_identify}}} WHERE content_archives_id=:content_archives_id LIMIT 0, 1";
			$cmd = Yii::app()->db->createCommand($_sql);
			$cmd->bindValue(':content_archives_id', $content_archives_id);
			$_a_r = $cmd->queryRow();
			
			if(is_array($_a_r)) {
				//
				foreach($_r['content_model_colums'] as $_k=>$_v) {
					if($_v['content_model_field_type'] == ContentModelField::DATA_TYPE_CHECKBOX) {
						$_a_r[$_v['content_model_field_identify']] = unserialize($_a_r[$_v['content_model_field_identify']]);
					}
				}
				$_r = array_merge($_r, $_a_r);
			}
			unset($_a_r);
			
			//自定义属性
			$_r['content_archives_flag'] = explode(',', $_r['content_archives_flag']);
			
			//文档栏目
			$_sql = "SELECT c.*
					FROM {{content_archives_classes_relating}} cr
					INNER JOIN {{content_archives_classes}} c ON c.class_id = cr.class_id 
					WHERE cr.content_archives_id=:content_archives_id";
			$_r['content_archives_classes'] = Yii::app()->db->createCommand($_sql)->queryAll(true, array(':content_archives_id'=>$content_archives_id));
			
			//文档标签
			$_sql = "SELECT tags_id, tags_name
					FROM {{content_archives_tags}}
					WHERE content_archives_id=:content_archives_id
					ORDER BY tags_id ASC";
			$_r['content_archives_tags'] = Yii::app()->db->createCommand($_sql)->queryAll(true, array(':content_archives_id'=>$content_archives_id));
		}
		
		//有开启缓存，则把结果添加到缓存中
		if($allow_cache && Yii::app()->cache) {
			$_cache_time_out = Setting::getSettingCache('GOODS_ROW_TIME_OUT');
			Yii::app()->cache->set($_cache_key, $_r, $_cache_time_out);
			unset($_cache_time_out, $_cache_key);
		}
		
		return $_r ? $_r : array();
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
			'pagesize' => 15,
		);
		$params = array_merge($_defaults_params, $params);
		
		//有开启缓存功能，则从缓存中取数据， 如果有数据，则直接返回结果
		if($params['allow_cache'] && isset(Yii::app()->cache)) {
			$_cache_key =  md5('content.archive.pages.' . serialize($params));
			$_r = json_decode(Yii::app()->cache->get($_cache_key), true);
			
			if($_r && is_array($_r)) {
				return $_r;
			}
		}
		
		$cmd = Yii::app()->db->createCommand();
		$cmd->select('COUNT(DISTINCT a.content_archives_id) AS COUNT')
			->from('{{content_archives}} a')
			->join('{{content_archives_classes_relating}} acr', 'acr.content_archives_id=a.content_archives_id')
			->join('{{content_archives_classes}} ac', 'ac.class_id=acr.class_id')
			->join('{{content_model}} m', 'm.content_model_id=ac.content_model_id');
		
		//添加条件
		if(isset($params['content_archives_status']) && !empty($params['content_archives_status'])) {
			$__addons = array('AND', 'a.content_archives_status=:content_archives_status');
			$__params = array(':content_archives_status'=>$params['content_archives_status']);
		} else {
			$__addons = array('AND', 'a.content_archives_status>:content_archives_status');
			$__params = array(':content_archives_status'=>0);
		}
		//
		if(isset($params['content_archives_id']) && !empty($params['content_archives_id'])) {
			$__addons[] = array('AND', 'a.content_archives_id=:content_archives_id');
			$__params[':content_archives_id'] = $params['content_archives_id'];
		}
		//
		if(isset($params['content_model_id']) && !empty($params['content_model_id'])) {
			$__addons[] = array('AND', 'ac.content_model_id=:content_model_id');
			$__params[':content_model_id'] = $params['content_model_id'];
		}
		//
		if(isset($params['class_id']) && !empty($params['class_id'])) {
			$_classes = ContentArchivesClass::get_classes_children($params['class_id']);
			$_s = '';
			foreach($_classes as $_k=>$_v) {
				$_s .= ",:class_id{$_k}";
				$__params[":class_id{$_k}"] = $_v;
			}
			$_s = trim($_s, ',');
			$__addons[] = array('AND', "acr.class_id IN({$_s})");
			unset($_classes, $_s);
		}
		//
		if(isset($params['content_archives_subject']) && !empty($params['content_archives_subject'])) {
			$__addons[] = array(
				'OR',
				array(
					'OR LIKE',
					'a.content_archives_subject',
					"%{$params['content_archives_subject']}%",
				),
			);
			//$__addons[] = array('LIKE', 'a.content_archives_name', '%:content_archives_name%');
			//$__params = array_merge($__params, array(':content_archives_name'=>$params['content_archives_name']));
		}
		//
		if(isset($params['search_key']) && $params['search_key']) {
			if(is_numeric($params['search_key'])){
				$__addons[] = array('AND', 'a.content_archives_id=:content_archives_id');
				$__params[':content_archives_id'] = $params['search_key'];
			}else{
				$__addons[] = array(
					'OR',
					array(
						'OR LIKE',
						'a.content_archives_subject',
						"%{$params['search_key']}%",
					),
				);
			}
			//$__params = array_merge($__params, array(':search_key'=>$params['search_key']));
		}
		$cmd->where($__addons, $__params);
		
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
					'a.content_archives_rank ASC',
					'a.content_archives_dateline DESC',
				)
			);
		}
		$cmd->select('a.content_archives_id, ac.content_model_id, m.content_model_name, ac.class_id, ac.class_name, a.content_archives_subject, a.content_archives_color, a.content_archives_short_subject, a.content_archives_flag, a.content_archives_jump_url, a.content_archives_source, a.content_archives_author, a.content_archives_thumb, a.content_archives_keywords, a.content_archives_summary, a.content_archives_status, a.content_archives_rank, a.content_archives_pubtime, a.content_archives_is_build, a.content_archives_lasttime, a.content_archives_dateline')
			->group('a.content_archives_id')
			->limit($pages->getLimit())
			->offset($pages->getOffset());
		
		$_r['pages'] = $pages;
		
		$_r['rows'] = $cmd->queryAll();
		
		//有开启缓存，则把结果添加到缓存中
		if($params['allow_cache'] && isset(Yii::app()->cache)) {
			$_cache_cache_time = Setting::getSettingCache('CONTENT_ARCHIVES_PAGES_CACHE_TIME');
			Yii::app()->cache->set($_cache_key, json_encode($_r), $_cache_cache_time);
			unset($_cache_cache_time, $_cache_key);
		}
		
		return $_r;
	}
	
	/**
	 * 根据content_archives_id获取文档标题
	 * @param mixed $content_archives_id    文档编号
	 * @return string $content_archives_name    文档标题
	 */
	public static function get_archive_subject_by_id($content_archives_id)
	{
		$_r = self::get_archive_by_id($content_archives_id);
		
		return $_r['content_archives_subject'];
	}
	
	/**
	 * 审核文档
	 * @param mixed $content_archives_id 游戏公会编号
	 * @param boolean $is_audit 是/否审核
	 */
	public static function audit($content_archives_id, $is_audit = true)
	{
		$_flag = Yii::app()->db->createCommand()->update('{{content_archives}}',
			array(
				'content_archives_status' => $is_audit ? self::STAT_NORMAL : self::STAT_WAIT_AUDIT,
			),
			'content_archives_id=:content_archives_id',
			array(':content_archives_id'=>$content_archives_id)
		);
		
		return $_flag;
	}
	
	/**
	 * 获取文档状态
	 * @param mixed $content_archives_status 游戏状态值
	 */
	public static function get_archives_status($content_archives_status = null)
	{
		if($content_archives_status) {
			if(isset(self::$__status[$content_archives_status])) {
				$_r = self::$__status[$content_archives_status];
			} else {
				$_r = '';
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
	 * 获取文档属性
	 * @param mixed $content_archives_flag 文档属性
	 */
	public static function get_archives_flag($content_archives_flag = null)
	{
		if($content_archives_flag) {
			if(!is_array($content_archives_flag)) {
				$content_archives_flag = explode(',', $content_archives_flag);
			}
			$_r = array();
			foreach ($content_archives_flag as $_k=>$_v) {
				if(isset(self::$__flags[$_v])) {
					$_r[] = self::$__flags[$_v];
				}
			}
			if(!empty($_r)) {
				$_r = "[<span style=\"color:#f00;\">" . implode(' ', $_r) . "</span>]";
			} else {
				$_r = "";
			}
		} else {
			$_r = array();
			foreach(self::$__flags as $_k=>$_v) {
				$_r[$_k] = Yii::t('admincp', $_v) . "[{$_k}]";
			}
		}
		return $_r;
	}
	
	public static function create_static_html($id){
		$c = file_get_contents("http://www.wan123.com".Common::sign_archives_url($id, false));
		if($c == ""){
			return false;
		}
		$f = "static/html/news/".$id.".html";
		file_put_contents($f,$c);
		if(file_exists($f)){
			return true;
		}else{
			return false;
		}
	}
}
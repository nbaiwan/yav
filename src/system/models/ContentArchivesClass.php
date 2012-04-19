<?php

class ContentArchivesClass
{
	const STAT_STATUS_DELETE = 0;
	const STAT_STATUS_NORMAL = 4;
	
	//
	const STAT_PART_FINAL_CLASS = 1;
	const STAT_PART_COVER_CLASS = 2;
	const STAT_PART_EXTERNAL_LINKS = 4;
	
	private static $__parts = array(
		self::STAT_PART_FINAL_CLASS => '最终栏目',
		self::STAT_PART_COVER_CLASS => '频道封面',
		self::STAT_PART_EXTERNAL_LINKS => '外部连接',
	);
	
	private static $__classes = null;
	/**
	 * 从缓存读取游戏分类数据
	 */
	public static function get_classes_by_cache()
	{
		if(self::$__classes !== null) {
			return self::$__classes;
		}
		
		if(isset(Yii::app()->cache) && (self::$__classes = Yii::app()->cache->get('content.archives.classes'))) {
			return self::$__classes;
		}
		
		//无缓存数据， 读取数据库中的数据
		//self::$__classes = self::read_classes_array();
		//重新整理数据
		self::$__classes = self::build_classes_list();
		//写入缓存
		if(isset(Yii::app()->cache)) {
			Yii::app()->cache->set('content.archives.classes', self::$__classes);
		}
		
		return self::$__classes;
	}
	 
	/**
	 * 从缓存读取内容模型数据
	 */
	public static function get_class_by_id($class_id)
	{
		self::$__classes = (self::$__classes == null) ? self::get_classes_by_cache() : self::$__classes;
		
		$_r = array();
		if(!empty($class_id)) {
			foreach (self::$__classes as $_k=>$_v) {
				if($_v['class_id'] == $class_id) {
					$_r = $_v;
					break;
				}
			}
		}
		
		return $_r;
	}
	
	/**
	 * 将游戏分类数组数据，按顺序排序好，
	 */
	protected static function build_classes_list($parent_id = 0, $deepth = 1)
	{
		$_classes_array = is_array(self::$__classes) ? self::$__classes : self::read_classes_array();
		$_r = array();
		foreach($_classes_array as $_k=>$_v) {
			if($_v['class_parent_id'] == $parent_id) {
				$_v['identify_path'] = self::get_identify_path($_v['class_id']);
				$_v['deepth'] = $deepth;
				$_r[] = $_v;
				$_child = self::build_classes_list($_v['class_id'], $deepth+1);
				$_r = array_merge($_r, $_child);
			}
		}
		
		if($deepth == 1) {
			$parents = array();
			foreach($_r as $_k=>$_v) {
				if(empty($parents)) {
					$_r[$_k]['parent_key'] = $_k;
				} else {
					$_r[$_k]['parent_key'] = $parents[max(array_keys($parents))];
				}
				
				if(isset($_r[$_k+1])) {
					if($_r[$_k]['deepth'] < $_r[$_k+1]['deepth']) {
						$parents[] = $_k;
					} else if($_r[$_k]['deepth'] > $_r[$_k+1]['deepth']) {
						unset($parents[max(array_keys($parents))]);
					}
				}
			}
		}
		
		return $_r;
	}
	
	/**
	 * 从数据库中读取游戏分类数据
	 */
	protected static function read_classes_array()
	{
		$sql = "SELECT * FROM {{content_archives_classes}} WHERE class_status>:class_status ORDER BY class_rank ASC";
		$cmd = Yii::app()->db->createCommand($sql);
		$cmd->bindValue(':class_status', 0);
		$cmd->execute();
		$_r = $cmd->queryAll();
		$result = array();
		foreach($_r as $_k=>$_v) {
			$row = array(
				'class_id' => $_v['class_id'],
				'class_name' => $_v['class_name'],
				'content_model_id' => $_v['content_model_id'],
				'class_parent_id' => $_v['class_parent_id'],
				'class_identify' => $_v['class_identify'],
				'class_is_default' => $_v['class_is_default'],
				'class_default' => $_v['class_default'],
				'class_is_part' => $_v['class_is_part'],
				'class_tempindex' => $_v['class_tempindex'],
				'class_templist' => $_v['class_templist'],
				'class_temparticle' => $_v['class_temparticle'],
				'class_seo_keywords' => $_v['class_seo_keywords'],
				'class_seo_description' => $_v['class_seo_description'],
				'class_rank' => $_v['class_rank'],
				'class_is_show' => $_v['class_is_show'],
				'class_is_system' => $_v['class_is_system'],
				'class_status' => $_v['class_status'],
				'class_lasttime' => $_v['class_lasttime'],
				'class_dateline' => $_v['class_dateline'],
			);
			$result[] = $row;
		}
		
		self::$__classes = $result;
		
		return self::$__classes;
	}
	
	/**
	 * 根据class_id获取分类名称
	 * @param mixed $class_id    分类编号
	 * @return string $class_name    分类名称
	 */
	public static function get_class_name_by_id($class_id)
	{
		$_classes_array = is_array(self::$__classes) ? self::$__classes : self::get_classes_by_cache();
		$class_name = '';
		foreach($_classes_array as $key=>$value) {
			if($value['class_id'] == $class_id) {
				$class_name = $value['class_name'];
				break;
			}
		}
		
		return $class_name;
	}
	/**
	 * 获取权限节点Identify路径
	 */
	public static function get_identify_path($class_id)
	{
		$_classes_array = is_array(self::$__classes) ? self::$__classes : self::get_classes_by_cache();
		$_r = '/';
		foreach($_classes_array as $_k=>$_v) {
			if($_v['class_id'] == $class_id) {
				$_r .= $_v['class_identify'];
				if($_v['class_parent_id']>0) {
					$_r = self::get_identify_path($_v['class_parent_id']) . $_r;
				}
				break;
			}
		}
		$_r = trim($_r, '/');
		
		return $_r;
	}
	
	/**
	 * 获取游戏分类节点路径
	 */
	public static function get_classes_path($class_id)
	{
		$_classes_array = is_array(self::$__classes) ? self::$__classes : self::get_classes_by_cache();
		$_r = '/';
		foreach($_classes_array as $key=>$value) {
			if($value['class_id'] == $class_id) {
				$_r .= $value['class_name'];
				if($value['class_parent_id']>0) {
					$_r = self::get_classes_path($value['class_parent_id']) . $_r;
				}
				break;
			}
		}
		$_r = trim($_r, '/');
		
		return $_r;
	}
	
	public static function get_classes_children($class_id)
	{
		$_classes_array = is_array(self::$__classes) ? self::$__classes : self::get_classes_by_cache();
		$_r = array($class_id);
		foreach($_classes_array as $key=>$value) {
			if($value['class_parent_id'] == $class_id) {
				$_r[] = $value['class_id'];
				$_r += self::get_classes_children($value['class_id']);
			}
		}
		
		return $_r;
	}
	
	/**
	 * 强制更新缓存
	 */
	public static function update_cache()
	{
		//
		self::$__classes = null;
		
		//
		if(isset(Yii::app()->cache)) {
			//$_cache->delete('content.archives.classes');
			$_classes = self::build_classes_list();
			Yii::app()->cache->set('content.archives.classes', $_classes);
		}
		
		return true;
	}
	
	/**
	 * 获取栏目属性
	 * @param mixed $class_is_part 文档属性
	 */
	public static function get_classes_parts($class_is_part = null)
	{
		if($class_is_part) {
			if(isset(self::$__parts[$class_is_part])) {
				$_r = self::$__parts[$class_is_part];
			} else {
				$_r = '';
			}
		} else {
			$_r = array();
			foreach(self::$__parts as $_k=>$_v) {
				$_r[$_k] = Yii::t('admincp', $_v);
			}
		}
		return $_r;
	}
}
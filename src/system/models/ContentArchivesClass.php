<?php

class ContentArchivesClassModel extends CBaseModel {
	const STAT_STATUS_DELETE = 0;
	const STAT_STATUS_NORMAL = 4;
	
	//
	const STAT_PART_FINAL_CLASS = 1;
	const STAT_PART_COVER_CLASS = 2;
	const STAT_PART_EXTERNAL_LINKS = 4;
    
    public static $__instance = null;
    
    public static $__model = 'ContentArchivesClassModel';
	
	private static $__parts = array(
		self::STAT_PART_FINAL_CLASS => '最终栏目',
		self::STAT_PART_COVER_CLASS => '频道封面',
		self::STAT_PART_EXTERNAL_LINKS => '外部连接',
	);
	
	private static $__classes = null;
	/**
	 * 从缓存读取游戏分类数据
	 */
	public function getClassesByCache() {
		if (self::$__classes !== null) {
			return self::$__classes;
		}
		
		if (($this->cache instanceof ICache) && (self::$__classes = $this->cache->get('content.archives.classes'))) {
			return self::$__classes;
		}
		
		//无缓存数据， 读取数据库中的数据
		//self::$__classes = self::read_classes_array();
		//重新整理数据
		self::$__classes = $this->buildClassesList();
		//写入缓存
		if ($this->cache instanceof ICache) {
			$this->cache->set('content.archives.classes', self::$__classes);
		}
		
		return self::$__classes;
	}
	 
	/**
	 * 从缓存读取内容模型数据
	 */
	public function getClassById($classId) {
		self::$__classes = (self::$__classes == null) ? $this->getClassesByCache() : self::$__classes;
		
		$ret = array();
		if (!empty($classId)) {
			foreach (self::$__classes as $_k=>$_v) {
				if($_v['class_id'] == $classId) {
					$ret = $_v;
					break;
				}
			}
		}
		
		return $ret;
	}
	
	/**
	 * 将游戏分类数组数据，按顺序排序好，
	 */
	protected function buildClassesList($parentId = 0, $deepth = 1) {
		$classesArray = is_array(self::$__classes) ? self::$__classes : $this->readClassesArray();
		$ret = array();
		foreach ($classesArray as $_k=>$_v) {
			if ($_v['class_parent_id'] == $parentId) {
				$_v['identify_path'] = self::getIdentifyNode($_v['class_id']);
				$_v['deepth'] = $deepth;
				$ret[] = $_v;
				$child = $this->buildClassesList($_v['class_id'], $deepth+1);
				$ret += $child;
			}
		}
		
		if ($deepth == 1) {
			$parents = array();
			foreach ($ret as $_k=>$_v) {
				if (empty($parents)) {
					$ret[$_k]['parent_key'] = $_k;
				} else {
					$ret[$_k]['parent_key'] = $parents[max(array_keys($parents))];
				}
				
				if(isset($ret[$_k+1])) {
					if($ret[$_k]['deepth'] < $ret[$_k+1]['deepth']) {
						$parents[] = $_k;
					} else if($ret[$_k]['deepth'] > $ret[$_k+1]['deepth']) {
						unset($parents[max(array_keys($parents))]);
					}
				}
			}
		}
		
		return $ret;
	}
	
	/**
	 * 从数据库中读取游戏分类数据
	 */
	protected function readClassesArray() {
		$sql = "SELECT * FROM {{content_archives_classes}} WHERE class_status>:class_status ORDER BY class_rank ASC";
		$this->db->prepare($sql);
		$this->db->bindValue(':class_status', 0);
		$ret = $this->db->queryAll();
		self::$__classes = array();
		foreach ($ret as $_k=>$_v) {
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
			self::$__classes[] = $row;
		}
		
		return self::$__classes;
	}
	
	/**
	 * 根据class_id获取分类名称
	 * @param mixed $classId    分类编号
	 * @return string $className    分类名称
	 */
	public function getClassNameById($classId) {
		$classesArray = is_array(self::$__classes) ? self::$__classes : $this->getClassesByCache();
		$className = '';
		foreach ($classesArray as $key=>$value) {
			if ($value['class_id'] == $classId) {
				$className = $value['class_name'];
				break;
			}
		}
		
		return $className;
	}
	/**
	 * 获取权限节点Identify路径
	 */
	public function getIdentifyNode($classId) {
		$classesArray = is_array(self::$__classes) ? self::$__classes : $this->getClassesByCache();
		$identifyNode = '/';
		foreach ($classesArray as $_k=>$_v) {
			if ($_v['class_id'] == $classId) {
				$identifyNode .= $_v['class_identify'];
				if ($_v['class_parent_id']>0) {
					$identifyNode = $this->getIdentifyNode($_v['class_parent_id']) . $identifyNode;
				}
				break;
			}
		}
		$identifyNode = trim($identifyNode, '/');
		
		return $identifyNode;
	}
	
	/**
	 * 获取游戏分类节点路径
	 */
	public function getClassNode($classId) {
		$classesArray = is_array(self::$__classes) ? self::$__classes : $this->getClassesByCache();
		$classNode = '/';
		foreach ($classesArray as $_k=>$_v) {
			if ($_v['class_id'] == $classId) {
				$classNode .= $_v['class_name'];
				if ($_v['class_parent_id']>0) {
					$classNode = $this->getClassesNode($_v['class_parent_id']) . $classNode;
				}
				break;
			}
		}
		$classNode = trim($classNode, '/');
		
		return $classNode;
	}
	
	public function getClassChildren($classId) {
		$classesArray = is_array(self::$__classes) ? self::$__classes : $this->getClassesByCache();
		$classChildren = array($classId);
		foreach ($classesArray as $_k=>$_v) {
			if ($_v['class_parent_id'] == $classId) {
				$classChildren[] = $_v['class_id'];
				$classChildren += $this->getClassChildren($_v['class_id']);
			}
		}
		
		return $classChildren;
	}
	
	/**
	 * 强制更新缓存
	 */
	public function updateCache() {
		//
		self::$__classes = null;
		
		//
		if ($this->cache instanceof ICache) {
			//$_cache->delete('content.archives.classes');
			$classes = $this->buildClassesList();
			$this->cache->set('content.archives.classes', $classes);
		}
		
		return true;
	}
	
	/**
	 * 获取栏目属性
	 * @param mixed $classIsPart 文档属性
	 */
	public function getClassesParts($classIsPart = null) {
		if ($classIsPart) {
			if (isset(self::$__parts[$classIsPart])) {
				$ret = self::$__parts[$classIsPart];
			} else {
				$ret = array();
			}
		} else {
			$ret = array();
			foreach (self::$__parts as $_k=>$_v) {
				$ret[$_k] = $_v;
			}
		}
		return $ret;
	}
}
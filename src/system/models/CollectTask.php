<?php

class CollectTaskModel extends CBaseModel {
	//已删除
	const STAT_STATUS_DELETED = 0;
	//正常
	const STAT_STATUS_NORMAL = 4;
	//
	private static $__tasks = null;
	public static $_image_type = array('jpg', 'jpeg', 'gif', 'png', 'bmp');
	public static $_type_list = null;
    
    public static $__instance = null;
    
    public static $__model = 'CollectTaskModel';
	
	/**
	 * 
	 * @param unknown_type $params
	 */
	public function getTaskById($collect_task_id, $allow_cache = false) {
		if(isset($this->cache) && $allow_cache) {
			//
			$_cache_key =  md5("collect.task.row.{$collect_task_id}");
			$ret = json_decode($this->cache->get($_cache_key), true);
			
			if(!empty($ret) && is_array($ret)) {
				return $ret;
			}
		}
		
		$sql = "SELECT t.*, tp.collect_model_id, tp.collect_template_list_rules, tp.collect_template_addons_rules, m.collect_model_id, m.collect_model_name, m.collect_model_identify, s.collect_source_id FROM {{collect_task}} t
				INNER JOIN {{collect_template}} tp ON tp.collect_template_id=t.collect_template_id
				INNER JOIN {{collect_model}} m ON m.collect_model_id=tp.collect_model_id
				INNER JOIN {{collect_source}} s ON s.collect_source_id=tp.collect_source_id
				WHERE t.collect_task_id=:collect_task_id";
        $params = array(
            ':collect_task_id' => $collect_task_id,
        );
		$ret = $this->db->queryRow($sql, $params);
		
		// 列表规则
		$ret['collect_task_list_rules'] && $ret['collect_task_list_rules'] = json_decode($ret['collect_task_list_rules'], true);
		$ret['collect_template_list_rules'] && $ret['collect_template_list_rules'] = json_decode($ret['collect_template_list_rules'], true);
		if(!is_array($ret['collect_task_list_rules'])) {
			$ret['collect_task_list_rules'] = array();
		}
        
		// 列表开始
		if(!empty($ret['collect_task_list_rules']['begin'])) {
			$ret['collect_list_rules']['begin'] = $ret['collect_task_list_rules']['begin'];
		} else {
			$ret['collect_list_rules']['begin'] = $ret['collect_template_list_rules']['begin'];
		}
		$ret['collect_list_rules']['begin'] = self::rule2reg($ret['collect_list_rules']['begin']);
		
		// 列表结束
		if(!empty($ret['collect_task_list_rules']['end'])) {
			$ret['collect_list_rules']['end'] = $ret['collect_task_list_rules']['end'];
		} else {
			$ret['collect_list_rules']['end'] = $ret['collect_template_list_rules']['end'];
		}
		$ret['collect_list_rules']['end'] = self::rule2reg($ret['collect_list_rules']['end']);
		
		// 附加规则
		$ret['collect_task_addons_rules'] && $ret['collect_task_addons_rules'] = json_decode($ret['collect_task_addons_rules'], true);
		$ret['collect_template_addons_rules'] && $ret['collect_template_addons_rules'] = json_decode($ret['collect_template_addons_rules'], true);
		if(!is_array($ret['collect_task_addons_rules'])) {
			$ret['collect_content_rules'] = array();
		} else {
			$ret['collect_content_rules'] = $ret['collect_task_addons_rules'];
		}
		unset($ret['collect_task_list_rules'], $ret['collect_task_addons_rules']);
		
		$collect_model_fields = CollectModelFieldModel::inst()->getFieldsByModelId($ret['collect_model_id']);
		foreach($collect_model_fields as $_k=>$_v) {
			if(empty($ret['collect_content_rules'][$_v['collect_fields_identify']]['begin'])) {
				$ret['collect_content_rules'][$_v['collect_fields_identify']]['begin'] = $ret['collect_template_addons_rules'][$_v['collect_fields_identify']]['begin'];
			}
			if(empty($ret['collect_content_rules'][$_v['collect_fields_identify']]['end'])) {
				$ret['collect_content_rules'][$_v['collect_fields_identify']]['end'] = $ret['collect_template_addons_rules'][$_v['collect_fields_identify']]['end'];
			}
			$ret['collect_content_rules'][$_v['collect_fields_identify']]['begin'] = self::rule2reg($ret['collect_content_rules'][$_v['collect_fields_identify']]['begin']);
			$ret['collect_content_rules'][$_v['collect_fields_identify']]['end'] = self::rule2reg($ret['collect_content_rules'][$_v['collect_fields_identify']]['end']);
			$ret['collect_content_rules'][$_v['collect_fields_identify']] += $_v;
		}
		unset($collect_model_fields, $_k, $_v, $ret['collect_template_list_rules'], $ret['collect_template_addons_rules']);
		
		// 采集任务附加规则
		foreach($ret['collect_content_rules'] as $_k=>$_v) {
			$rules['addons'][$_k] = $_v;
			if(empty($_v['begin'])) {
				$rules['addons'][$_k]['begin'] = $ret['collect_template_addons_rules'][$_k]['begin'];
			}
			if(empty($_v['end'])) {
				$rules['addons'][$_k]['end'] = $ret['collect_template_addons_rules'][$_k]['end'];
			}
			$rules['addons'][$_k]['begin'] = self::rule2reg($rules['addons'][$_k]['begin']);
			$rules['addons'][$_k]['end'] = self::rule2reg($rules['addons'][$_k]['end']);
		}
		
		if(!empty($ret) && isset($this->cache) && $allow_cache) {
			//
			$this->cache->set($_cache_key, json_encode($ret));
		}
		
		return $ret;
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $params
	 */
	public function Pages($params = array()) {
		//设置默认参数
		$_defaults_params = array(
			'allow_cache' => true,
			'page' => isset($_GET['page']) ? intval($_GET['page']) : 1,
			'pagesize' => 10,
		);
		$params = array_merge($_defaults_params, $params);
		
		//有开启缓存功能，则从缓存中取数据， 如果有数据，则直接返回结果
		if($params['allow_cache'] && isset($this->cache)) {
			$cacheKey =  md5('collect.task.pages.' . serialize($params));
			$ret = $this->cache->get($cacheKey);
			
			if($ret && is_array($ret)) {
				return $ret;
			}
		}
		
		//添加条件
		$builds = array(
            'select' => 'COUNT(DISTINCT t.collect_task_id) AS COUNT',
            'from' => array('{{collect_task}}', 't'),
            'join' => array(
                array('{{collect_template}}', 'tp', '`tp`.`collect_template_id`=`t`.`collect_template_id`'),
                array('{{collect_model}}', 'm', '`m`.`collect_model_id`=`tp`.`collect_model_id`'),
                array('{{collect_source}}', 's', '`s`.`collect_source_id`=`tp`.`collect_source_id`'),
            )
        );
        
		if(isset($params['collect_task_status']) && !empty($params['collect_task_status'])) {
			$builds['where'][] = array('AND', '`t`.`collect_task_status`=:collect_task_status');
			$sql_params[':collect_task_status'] = $params['collect_task_status'];
		} else {
			$builds['where'][] = array('AND', '`t`.`collect_task_status`>:collect_task_status');
			$sql_params[':collect_task_status'] = 0;
		}
		
		//
		if(isset($params['collect_task_id']) && !empty($params['collect_task_id'])) {
			$builds['where'][] = array('AND', '`t`.`collect_task_id`=:collect_task_id');
			$sql_params[':collect_task_id'] = $params['collect_task_id'];
		}
		
		if(isset($params['collect_template_id']) && !empty($params['collect_template_id'])) {
			$builds['where'][] = array('AND', 't.collect_template_id IN(:collect_template_id)');
			$sql_params[':collect_template_id'] = $params['collect_template_id'];
		}
		if($params['collect_task_name']) {
			$builds['where'][] = array(
				'AND',
				array(
					'OR LIKE',
					'`t`.`collect_task_name`',
					':collect_task_name',
				),
			);
            $sql_params[':collect_task_name'] = "%{$params['collect_task_name']}%";
			
		}
		
		//
		if(isset($params['searchKey']) && $params['searchKey']) {
			$builds['where'][] = array(
				'AND',
				array(
					'OR LIKE',
					't.collect_task_name',
					":searchKey",
				),
			);
            $sql_params[':searchKey'] = "%{$params['searchKey']}%";
		}
        
        $sql = $this->buildQuery($builds);
		
		//统计数量
        $count = $this->db->queryScalar($sql, $sql_params);
		
		//分页处理
		$pages = new CPagination($count);
		
		//设置分页大小
		$pages->pageSize = $params['pagesize'];
        
		if(isset($params['orderby']) && $params['orderby']) {
			$builds['order'] = $params['orderby'];
		} else {
			$builds['order'] = array(
					'`t`.`collect_task_rank` ASC',
					'`t`.`collect_task_id` DESC',
                );
		}
        
        $builds['select'] = 't.collect_task_id, t.collect_task_name, tp.collect_template_name, tp.collect_model_id, s.collect_source_name, tp.collect_source_id, m.collect_model_name, t.collect_template_id, t.collect_task_rank, t.collect_task_lastcollecttime, t.collect_task_lasttime, t.collect_task_dateline';
        $pages->applyLimit($builds);
        $sql = $this->buildQuery($builds);
		$ret['pages'] = $pages;
		$ret['rows'] = $this->db->queryAll($sql, $sql_params);
		
		//有开启缓存，则把结果添加到缓存中
		if($params['allow_cache'] && isset($this->cache)) {
			$cacheTimeout = Setting::get_setting_value('COLLECT_TEMPLATE_PAGES_CACHE_TIME');
			$this->cache->set($cacheKey, json_encode($ret), $cacheTimeout);
			unset($cacheTimeout, $cacheKey);
		}
		
		return $ret;
	}
	
	/**
	 * 从缓存读取游戏工会数据
	 */
	public function getTasksByCache()
	{
		if(self::$__tasks !== null) {
			return self::$__tasks;
		}
		
		if(isset($this->cache) && (self::$__tasks = $this->cache->get('collect.task'))) {
			return self::$__tasks;
		}
		
		//无缓存数据， 读取数据库中的数据
		self::$__tasks = self::read_task_array();
		//写入缓存
		if(isset($this->cache)) {
			$this->cache->set('collect.task', self::$__tasks);
		}
		
		return self::$__tasks;
	}
	
	/**
	 * 从数据库中读取游戏工会数据
	 */
	protected function readTaskArray() {
		$sql = "SELECT * FROM {{collect_task}} ORDER BY collect_task_id ASC";
		$cmd = $this->db->createCommand($sql);
		$cmd->execute();
		$_r = $cmd->queryAll();
		$result = array();
		foreach($_r as $_k=>$_v) {
			$row = array(
				'collect_task_id' => $_v['collect_task_id'],
				'collect_task_name' => $_v['collect_task_name'],
				'collect_template_id' => $_v['collect_template_id'],
				'collect_task_remark' => $_v['collect_task_remark'],
				'collect_task_rank' => $_v['collect_task_rank'],
				'collect_task_lasttime' => $_v['collect_task_lasttime'],
				'collect_task_dateline' => $_v['collect_task_dateline'],
			);
			$result[] = $row;
		}
		
		self::$__tasks = $result;
		
		return self::$__tasks;
	}
	
	/**
	 * 根据game_union_id获取游戏工会名称
	 * @param mixed $game_union_id    游戏工会编号
	 * @return string $game_union_name    游戏工会名称
	 */
	public function getTaskNameById($collect_task_id) {
		$_tasks_array = is_array(self::$__tasks) ? self::$__tasks : $this->getTasksByCache();
		$collect_task_name = '';
		foreach($_tasks_array as $key=>$value) {
			if($value['collect_task_id'] == $collect_task_id) {
				$collect_task_name = $value['collect_task_name'];
				break;
			}
		}
		
		return $collect_task_name;
	}
	
	/**
	 * 强制更新缓存
	 */
	public function updateCache() {
		//
		self::$__tasks = null;
		
		//
		self::$__tasks = $this->readTaskArray();
		if(isset($this->cache)) {
			$this->cache->set('collect.task', $__tasks);
		}
		
		return true;
	}
	
	public static function re2ab($data, $url) { //相对地址换成绝对地址
		$url_arr = parse_url($url);
		if($url_arr['path']) {
			$a = explode("/", $url_arr['path']);
			array_pop($a);
			$pa = implode("/", $a)."/";
		} else {
			$pa = "";
		}
		//转换非/开头的相对地址
		$data = preg_replace("<(.*?)(src|href)=([\"\'])(?![http|\s])([^\/]*?)[\"\'](.*?)>", "$1$2=$3".$url_arr['scheme']."://".$url_arr['host']."/".$pa."$4$3$5$6", $data);
		//转换/开头的相对地址
		$data = preg_replace("<(.*?)(src|href)=([\"\'])(?![http|\s])(\/.*?)[\"\'](.*?)>", "$1$2=$3".$url_arr['scheme']."://".$url_arr['host']."/"."$4$3$5$6", $data);
		return preg_replace("/([a-z]+)\/\//", "$1/", $data);
	}
	
	public static function listCollect($param) {
		$re['success'] = false;
		$ereg = $param["ereg"];
		$ereg = preg_replace("/\[[^\[]*\]/U", "([\s\S]*)", $ereg);
		$ereg = str_replace("/", "\/", $ereg);
		$ereg = str_replace("[(", "\[(", $ereg); //转义原有的方括号
		$ereg = str_replace(")]", ")\]", $ereg);
		//echo $ereg;exit;
		$ereg = "/".$ereg."/Ui";
		$read = self::readHtml($param["url"]);
		if($read['success']) {
			$content = $read['content'];
		} else {
			$re['error'] = $read['error'];
			return $re;
		}
		
		if($content){
			$content = mb_convert_encoding($content, "UTF-8", "gb2312,gbk,utf-8");
			$content = self::re2ab($content, $param["url"]);
			if($param['listarea']){
				$param['listarea'] = str_replace("/", "\/", $param['listarea']);
				preg_match("/{$param[listarea]}/Ui", $content, $listarea);
				
				$content = $listarea[1];
			}
			preg_match_all($ereg, $content, $m, PREG_SET_ORDER);
			if($m){
				$re['success'] = true;
				$re['content'] = $m;
			}else{
				$re['error'] = "规则错误，没有匹配到内容！";
			}
		}else{
			$re['error'] = "内容为空！";
		}
		
		return $re;
	}
	
	public static function isUrl($url) {
		return preg_match("/^http:\/\/[A-Za-z0-9]+\.[A-Za-z0-9]+[\/=\?%\-&_~`@[\]\':+!]*([^<>\"])*$/", $url);
	}
	
	public static function isImage($url) {
		return preg_match("/^http:\/\/[A-Za-z0-9]+\.[A-Za-z0-9]+[\/=\?%\-&_~`@[\]\':+!]*([^<>\"])*(jpg|gif|png|jpeg)$/i", $url);
	}
	
	public static function readHtml($url) {
		$re = array();
		$re['success'] = false;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 5.1; rv:6.0.2) Gecko/20100101 Firefox/6.0.2');
		curl_setopt($ch, CURLOPT_ENCODING, 'gzip,deflate'); 
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		//curl_setopt($ch, CURLOPT_AUTOREFERER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		//curl_setopt($ch, CURLOPT_MAXREDIRS, 10); 
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		//$response = curl_exec($ch);
		$response = self::curlExecFollow($ch);
		if($response === FALSE) {
			$re['error'] = "没有采集到内容！";
			return $re;
		} else {
			$status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			if($status && $status != 200 && $status != 301) {
				$re['error'] = "采集地址错误！";
				return $re;
			}
		}
		curl_close($ch);
		list($header, $content) = explode("\r\n\r\n", $response, 2);
		$re['success'] = true;
		$re['content'] = $content;
		return $re;
	}
	
	public static function curlExecFollow($ch, &$maxredirect = null) {
		$mr = $maxredirect === null ? 5 : intval($maxredirect);
		if (ini_get('open_basedir') == '' && ini_get('safe_mode' == 'Off')) {
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, $mr > 0);
			curl_setopt($ch, CURLOPT_MAXREDIRS, $mr);
		} else {
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
		if ($mr > 0) {
			$newurl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
			
			$rch = curl_copy_handle($ch);
			curl_setopt($rch, CURLOPT_HEADER, true);
			curl_setopt($rch, CURLOPT_NOBODY, true);
			curl_setopt($rch, CURLOPT_FORBID_REUSE, false);
			curl_setopt($rch, CURLOPT_RETURNTRANSFER, true);
			do {
				curl_setopt($rch, CURLOPT_URL, $newurl);
				$header = curl_exec($rch);
				if (curl_errno($rch)) {
					$code = 0;
				} else {
					$code = curl_getinfo($rch, CURLINFO_HTTP_CODE);
					if ($code == 301 || $code == 302) {
						preg_match('/Location:(.*?)\n/', $header, $matches);
						$newurl = trim(array_pop($matches));
					} else {
						$code = 0;
					}
				}
			} while ($code && --$mr);
				curl_close($rch);
				if (!$mr) {
					if ($maxredirect === null) {
						trigger_error('Too many redirects. When following redirects, libcurl hit the maximum amount.', E_USER_WARNING);
					} else {
						$maxredirect = 0;
					}
					return false;
				}
				curl_setopt($ch, CURLOPT_URL, $newurl);
			}
		}
		return curl_exec($ch);
	}
	
	public static function getUrlcontents($url, &$charset = 'utf-8') {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; rv:8.0.1) Gecko/20100101 Firefox/8.0.1');
		curl_setopt($ch, CURLOPT_ENCODING, 'gzip,deflate'); 
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); 
		curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
		curl_setopt($ch, CURLOPT_AUTOREFERER, true);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, 60);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$_r = curl_exec($ch);
		
		if($_r = curl_exec($ch)) {
			
			if(preg_match("/charset=([^\r\n\"\'\s]*\b)/m", $_r, $charset)) {
				$charset = $charset[1];
			} else {
				$charset = 'utf-8';
			}
			
			$_r = preg_split("/\r\n\r\n|\r\r|\n\n/m", $_r, 2);
			
			return $_r[1];
		} else {
			return '';
		}
	}
	
	public static function getAbsoluteUrl($url, $compare_url) {
		if(!($_r = parse_url($url)) || !isset($_r['scheme']) || !isset($_r['host'])) {
			//
			$compare_r = parse_url($compare_url);
			//
			if(substr($url, 0, 1) == '/') {
				$compare_r['path'] = trim($_r['path'], '/');
			} else {
				// 
				$paths = preg_split("/\//", $_r['path'], 0, PREG_SPLIT_NO_EMPTY);
				//
				$compare_paths = preg_split("/\//", $compare_r['path'], 0, PREG_SPLIT_NO_EMPTY);
				if(substr($url, 0, 2) == '..') {
					unset($compare_paths[count($compare_paths) - 1]);
					for ($i = 0, $j = count($paths); $i < $j ; $i++) {
						if($paths[$i] != '..') {
							break;
						}
						unset($paths[$i], $compare_paths[count($compare_paths) - 1]);
					}
					$compare_r['path'] = implode('/', $compare_paths) . '/' . implode('/', $paths);
				} else {
					if($compare_paths) {
						$compare_r['path'] = implode('/', $compare_paths) . '/' . implode('/', $paths);
					} else {
						$compare_r['path'] = implode('/', $paths);
					}
				}
			}
			
			$url = $compare_r['scheme'] . '://';
			if(isset($compare_r['user']) && isset($compare_r['pass'])) {
				$url .= "{$compare_r['user']}:{$compare_r['pass']}@";
			}
			$url .= $compare_r['host'];
			if(isset($compare_r['port'])) {
				$url .= ":{$compare_r['port']}";
			}
			$url .= "/{$compare_r['path']}";
			if(isset($_r['query'])) {
				$url .= "?{$_r['query']}";
			}
			if(isset($_r['fragment'])) {
				$url .= "#{$_r['fragment']}";
			}
		}
		
		return $url;
	}
	
	public static function getListUrls($urls) {		
		$collect_task_urls = preg_split("/\r\n|\r|\n/m", $urls, 0, PREG_SPLIT_NO_EMPTY);
		
		$collect_list_urls = array();
		if($collect_task_urls) {
			foreach($collect_task_urls as $_k=>$_v) {
				if(preg_match("/<(.+?),(.+?),(.+?),(.+?)>/", $_v, $_r)) {
					for($i = $_r[1]; $i <= $_r[2]; $i++) {
						$collect_list_urls[] = str_replace($_r[0], $i * $_r[3], $_v);
					}
				} else {
					$collect_list_urls[] = $_v;
				}
			}
		}
		
		return $collect_list_urls;
	}
	
	public static function getContentUrls($url, $begin, $end) {
		//
		$list_charset = 'utf-8';
		$list_body =  self::getUrlContents($url, $list_charset);
		
		if(strtolower($list_charset) != 'utf-8') {
			$list_body = mb_convert_encoding($list_body, 'UTF-8', $list_charset);
		}
		
		$content_urls = array();
		if(preg_match("/{$begin}(.+?){$end}/s", $list_body, $list_content)) {
			$list_content = $list_content[0];
			
			//
			preg_match_all("/<a(.+?)href=([\"\']?)([^\"\' >]*)([\"\']?)([^>]+?)>(.+?)<\/a>/s", $list_content, $_r, PREG_SET_ORDER);
			
			foreach($_r as $_k=>$_v) {
				$content_urls[] = self::getAbsoluteUrl($_v[3], $url);
			}
		}
		
		return $content_urls;
	}
	
	public static function rule2reg($rule, $params = array()) {
		$rule = str_replace('(*)', '(.+?)', $rule);
		$rule = str_replace('!', '\!', $rule);
		$rule = str_replace('/', '\/', $rule);
		$rule = str_replace('-', '\-', $rule);
		$rule = str_replace('[', '\[', $rule);
		$rule = str_replace(']', '\]', $rule);
		$rule = str_replace('.', '\.', $rule);
		$rule = str_replace('+', '\+', $rule);
		$rule = str_replace('*', '\*', $rule);
		
		return $rule;
	}
	
	public static function getCharsets(){
		return array(
			1 => "UTF-8",
			2 => "GBK",
			3 => "GB2312"
		);
	}
}
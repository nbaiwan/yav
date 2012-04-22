<?php

class UserLogsModel extends CBaseModel {
    
    public static $__instance = null;
    
    public static $__model = 'UserLogsModel';
	
	public function add($type = 'orders', $item_id = '', $action = 'exchange', $result = 'failure', $message = '', $data = array()) {
        $data += array(
            'user_id' => $this->user->id ? $this->user->id : 0,
            'user_name' => $this->user->name ? $this->user->name : '',
            'user_ip' => Common::getIp(),
        );
		$sql = "INSERT INTO {{user_logs}}
				VALUES(:log_id, :user_id, :log_type, :log_item_id, :log_action, :log_result, :log_message, :log_data, :user_ip, :lasttime)";
		$this->db->prepare($sql);
		$this->db->bindValue(':log_id', 0, PDO::PARAM_INT);
		$this->db->bindValue(':user_id', $this->user->isLogin() ? $this->user->id : 0, PDO::PARAM_INT);
		$this->db->bindValue(':log_type', $type, PDO::PARAM_STR);
		$this->db->bindValue(':log_item_id', $item_id, PDO::PARAM_INT);
		$this->db->bindValue(':log_action', $action, PDO::PARAM_STR);
		$this->db->bindValue(':log_result', $result, PDO::PARAM_STR);
		$this->db->bindValue(':log_message', $message, PDO::PARAM_STR);
		$this->db->bindValue(':log_data', json_encode($data), PDO::PARAM_STR);
		$this->db->bindValue(':user_ip', Common::getIp(true), PDO::PARAM_INT);
		$this->db->bindValue(':lasttime', $_SERVER['REQUEST_TIME'], PDO::PARAM_INT);
		if(!$this->db->execute()) {
            //
		}
	}
	
	/**
	 * @param integer pagesize
	 * @param integer CID
	 * @param integer CState
	 * @param integer GState
	 * 
	 * @param string  order
	 * 
	 * @return array $rows
	 */
	public function Pages($params = array()) {
		//设置默认参数
		$_defaults_params = array(
			'allow_cache' => false,
			'page' => isset($_GET['page']) ? intval($_GET['page']) : 1,
			'pagesize' => 10,
		);
		$params = array_merge($_defaults_params, $params);
		
		//有开启缓存功能，则从缓存中取数据， 如果有数据，则直接返回结果
		if($params['allow_cache'] && isset($this->cache)) {
			$cacheKey =  md5('admin.logs.pages' . serialize($params));
			$ret = $this->cache->get($cacheKey);
			
			if($ret && is_array($ret)) {
				return $ret;
			}
		}
		
		//添加条件
		$builds = array(
            'select' => 'COUNT(ul.log_id) AS COUNT',
            'from' => array('{{user_logs}}', 'ul'),
        );
        $sql_params = array();
        
		if(isset($params['searchKey']) && $params['searchKey']) {
            $builds[] = array(
                'AND',
                array(
                    array(
                        'OR LIKE',
                        'ul.log_type',
                        ':searchKey_lType',
                    ),
                    array(
                        'OR LIKE',
                        'ul.log_action',
                        ':searchKey_lAction',
                    ),
                    array(
                        'OR LIKE',
                        'ul.log_result',
                        ':searchKey_lResult',
                    ),
                    array(
                        'OR LIKE',
                        'ul.log_message',
                        ':searchKey_lMessage',
                    ),
                    array(
                        'OR LIKE',
                        'ul.log_data',
                        ':searchKey_lData',
                    ),
                )
            );
            $sql_params[':searchKey_lType'] = "%{$params['searchKey']}%";
            $sql_params[':searchKey_lAction'] = "%{$params['searchKey']}%";
            $sql_params[':searchKey_lResult'] = "%{$params['searchKey']}%";
            $sql_params[':searchKey_lMessage'] = "%{$params['searchKey']}%";
            $sql_params[':searchKey_lData'] = "%{$params['searchKey']}%";
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
			$cmd->order();
		} else {
            $builds['order'] = array(
					'ul.lasttime DESC',
				);
		}
        $builds['select'] = 'ul.log_id, ul.user_id, ul.log_type, ul.log_item_id, ul.log_action, ul.log_result, ul.log_message, ul.log_data, ul.user_ip, ul.lasttime';
        $pages->applyLimit($builds);
        $sql = $this->buildQuery($builds);
		$result['pages'] = $pages;
		$result['rows'] = $this->db->queryAll($sql);
		
		foreach($result['rows'] as $key=>$row) {
			$data = json_decode($row['log_data'], true);
			foreach($data as $__key=>$__value) {
				$row['log_message'] = str_replace("{{$__key}}", "\"{$__value}\"", $row['log_message']);
			}
			$result['rows'][$key] = $row;
		}
		
		//有开启缓存，则把结果添加到缓存中
		if($params['allow_cache'] && isset($this->cache)) {
			$cacheTimeOut = SettingModel::inst()->getSettingValue('ADMIN_LOGS_TIME_OUT');
			$this->cache->set($cacheKey, $result, $cacheTimeOut);
			unset($cacheTimeOut, $cacheKey);
		}
		
		return $result;
	}
}
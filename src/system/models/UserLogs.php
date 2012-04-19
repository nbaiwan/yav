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
				VALUES(:LID, :USER_ID, :TYPE, :ITEM_ID, :ACTION, :RESULT, :MESSAGE, :DATA, :USER_IP, :ADD_TIME)";
		$this->db->prepare($sql);
		$this->db->bindValue(':LID', 0, PDO::PARAM_INT);
		$this->db->bindValue(':USER_ID', $this->user->id, PDO::PARAM_INT);
		$this->db->bindValue(':TYPE', $type, PDO::PARAM_STR);
		$this->db->bindValue(':ITEM_ID', $item_id, PDO::PARAM_INT);
		$this->db->bindValue(':ACTION', $action, PDO::PARAM_STR);
		$this->db->bindValue(':RESULT', $result, PDO::PARAM_STR);
		$this->db->bindValue(':MESSAGE', $message, PDO::PARAM_STR);
		$this->db->bindValue(':DATA', json_encode($data), PDO::PARAM_STR);
		$this->db->bindValue(':USER_IP', Common::getIp(true), PDO::PARAM_INT);
		$this->db->bindValue(':ADD_TIME', $_SERVER['REQUEST_TIME'], PDO::PARAM_INT);
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
            'select' => 'COUNT(ul.LID) AS COUNT',
            'from' => array('{{user_logs}}', 'ul'),
        );
        $sql_params = array();
        
		if(isset($params['searchKey']) && $params['searchKey']) {
            $builds[] = array(
                'AND',
                array(
                    array(
                        'OR LIKE',
                        'ul.LType',
                        ':searchKey_LType',
                    ),
                    array(
                        'OR LIKE',
                        'ul.LAction',
                        ':searchKey_LAction',
                    ),
                    array(
                        'OR LIKE',
                        'ul.LResult',
                        ':searchKey_LResult',
                    ),
                    array(
                        'OR LIKE',
                        'ul.LMessage',
                        ':searchKey_LMessage',
                    ),
                    array(
                        'OR LIKE',
                        'ul.LData',
                        ':searchKey_LData',
                    ),
                )
            );
            $sql_params[':searchKey_LType'] = "%{$params['searchKey']}%";
            $sql_params[':searchKey_LAction'] = "%{$params['searchKey']}%";
            $sql_params[':searchKey_LResult'] = "%{$params['searchKey']}%";
            $sql_params[':searchKey_LMessage'] = "%{$params['searchKey']}%";
            $sql_params[':searchKey_LData'] = "%{$params['searchKey']}%";
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
					'ul.LAddTime DESC',
				);
		}
        $builds['select'] = 'ul.LID, ul.LAID, ul.LType, ul.LItemID, ul.LAction, ul.LResult, ul.LMessage, ul.LData, ul.LUserIP, ul.LAddTime';
        $pages->applyLimit($builds);
        $sql = $this->buildQuery($builds);
		$result['pages'] = $pages;
		$result['rows'] = $this->db->queryAll($sql);
		
		foreach($result['rows'] as $key=>$row) {
			$data = json_decode($row['LData'], true);
			foreach($data as $__key=>$__value) {
				$row['LMessage'] = str_replace("{{$__key}}", "\"{$__value}\"", $row['LMessage']);
			}
			$result['rows'][$key] = $row;
		}
		
		//有开启缓存，则把结果添加到缓存中
		if($params['allow_cache'] && isset($this->cache)) {
			$cacheTimeOut = SettingModel::inst()->getSettingCache('ADMIN_LOGS_TIME_OUT');
			$this->cache->set($cacheKey, $result, $cacheTimeOut);
			unset($cacheTimeOut, $cacheKey);
		}
		
		return $result;
	}
}
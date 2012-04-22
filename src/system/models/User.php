<?php

class UserModel extends CBaseModel {
	const STAT_STATUS_DELETED = 0;
	
	const STAT_STATUS_LOCKED = 1;
	
	const STAT_STATUS_NORMAL = 4;
    
    const MSG_SUCCESS = 0;
    const MSG_ERROR_USERNAME_NOT_EXISTS = 1;
    const MSG_ERROR_PASSWORD_INCORRECT = 2;
    
    public static $__instance = null;
    
    public static $__model = 'UserModel';
    
    public function login($user_name, $user_pwd) {
        $user = $this->getUserByName($user_name);
        
        if($user) {
            if($user['password'] == md5(md5($user_pwd) . $user['salt'])) {
                //
                $this->user->login(
                    array(
                        'user_id' => $user['user_id'],
                        'user_name' => $user['user_name'],
                        'realname' => $user['realname'],
                        'email' => $user['email'],
                        'group_id' => $user['group_id'],
                        'purviews' => $user['purviews'],
                    )
                );
                
                return self::MSG_SUCCESS;
            } else {
                // 密码不正确
                
                return self::MSG_ERROR_PASSWORD_INCORRECT;
            }
        } else {
            // 用户不存在
            return self::MSG_ERROR_USERNAME_NOT_EXISTS;
        }
    }
	
	/*
	 * 验证用户密码
	 */
	public function validatePassword($user_pwd) {
		if(md5(md5($user_pwd).$this->UserSalt) == $this->UserPwd) {
			return true;
		} else {
			return false;
		}
	}
	
	/*
	 * 生成用户随机码
	 */
	public function signSalt() {
		$salt = '';
		$str[0] = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$str[1] = '0123456789';
		for($i=0; $i<6; $i++) {
			$r1 = mt_rand(0,999);
			if(($r1 = $r1%2) == 1) {
				$r2 = mt_rand(0, 9);
			} else {
				$r2 = mt_rand(0, 51);
			}
			$salt .= $str[$r1][$r2];
		}
		
		return $salt;
	}
	
	/**
	 * @param integer $user_id
	 * @return array $rows
	 */
	public function getUserById($user_id) {
        $sql = "SELECT u.user_id, u.user_name, u.realname, u.password, u.email, u.user_id, u.group_id, g.group_name, u.purviews, g.purviews as group_purviews, u.salt, u.logintimes, u.lastvisit, u.lastip, u.user_rank, u.lasttime, u.dateline, u.is_system, u.status
                FROM {{user}} u
                LEFT JOIN {{group}} g ON g.group_id=u.group_id
                WHERE u.status>:status AND u.user_id=:user_id";
		$this->db->prepare($sql);
		$this->db->bindValue(':user_id', $user_id);
		$this->db->bindValue(':status', self::STAT_STATUS_DELETED);
		$ret = $this->db->queryRow();
		
		return $ret;
	}
	
	/**
	 * @param integer $user_id
	 * @return array $rows
	 */
	public function getUserByName($user_name) {
        $sql = "SELECT u.user_id, u.user_name, u.realname, u.password, u.email, u.user_id, u.group_id, g.group_name, u.purviews, g.purviews as role_purviews, u.salt, u.logintimes, u.lastvisit, u.lastip, u.user_rank, u.lasttime, u.dateline, u.is_system, u.status
                FROM {{user}} u
                LEFT JOIN {{group}} g ON g.group_id=u.group_id
                WHERE u.status>:status AND u.user_name=:user_name";
		$this->db->prepare($sql);
		$this->db->bindValue(':user_name', $user_name);
		$this->db->bindValue(':status', self::STAT_STATUS_DELETED);
		$ret = $this->db->queryRow();
		
		return $ret;
	}
	
	/**
	 * @param integer $user_id
	 * @return array $rows
	 */
	public function getUserId($user_name) {
        $sql = "SELECT u.user_id
                FROM {{user}} u
                WHERE u.user_name=:user_name";
		$this->db->prepare($sql);
		$this->db->bindValue(':user_name', $user_name);
		$user_id = $this->db->queryScalar();
		
		return $user_id;
	}
	
	/*
	 * 获取用户访问列表
	 */
	public static function getAccessList($group_id, $user_pids = '') {
		if($user_pids != '') {
			$user_pids = json_decode($user_pids, true);
		} else {
			$user_pids = array();
		}
		
		if(isset($this->cache)) {
			$groups = $this->cache->get('user.roles');
			$purviews = $this->cache->get('user.purviews');
		}
		if(empty($groups)) {
			$groups = GroupModel::getGroupsByCache();//Role::getRoles();
		}
		if(empty($purviews)) {
			$purviews = PurviewModel::getPurviewsByCache();
		}
		
		if(empty($groups)) {
			return array();
		}
		$group_pids = $groups[$group_id]['purviews'];
		
		if($group_pids != 'all') {
			$group_pids = json_decode($group_pids, true);
			$pids = is_array($group_pids) && is_array($user_pids) ? array_merge($group_pids, $user_pids) : array();
		}
		
		$ret = array();
		foreach($purviews as $_k=>$_v) {
			if($group_pids=='all' || in_array($_v['purview_id'], $pids)) {
				$ret[] = $_v['identify_tree'];
			}
		}
		
		return $ret;
	}
	
	/**
	 * 取出指定管理员及下属管理员的数据
	 * @param unknown_type $user_id
	 */
	public function getUsersByOwner($user_id, $is_same_group = false) {
		//取用户数据
		$user = self::get_user_by_id($user_id);
		if(empty($user)) {
			return array();
		}
		
		$params = array(
			'pagesize' => 999,
			'group_id' => $user['group_id'],
		);
		$ret = $this->Pages($params);
		$users = array();
		foreach($ret['rows'] as $_k=>$_v) {
			if($user['group_purviews'] == 'all' || $_v['user_id'] == $user['user_id']
					|| ($is_same_group && $_v['group_id'] == $user['group_id'])) {
				$users[] = $_v;
			}
		}
		unset($ret, $_k, $_v);
		
		return $users;
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
			'pagesize' => 15,
		);
		$params = array_merge($_defaults_params, $params);
		
		//有开启缓存功能，则从缓存中取数据， 如果有数据，则直接返回结果
		if($params['allow_cache'] && isset($this->cache)) {
			$cache_key =  'user.pages.' . serialize($params);
			$ret = $this->cache->get($_cache_key);
			
			if($ret && is_array($ret)) {
				return $ret;
			}
		}
		
		//添加条件
        $builds = array(
            'select' => 'COUNT(u.user_id) AS COUNT',
            'from' => array('{{user}}', 'u'),
            'leftJoin' => array('{{group}}', 'g', '`g`.`group_id`=`u`.`group_id`'),
        );
        
		if(isset($params['status']) && !empty($params['status'])) {
			$builds['where'][] = array('AND', 'u.status=:status');
			$sql_params = array(':status'=>$params['status']);
		} else {
			$builds['where'][] = array('AND', 'u.status>:status');
			$sql_params = array(':status'=>self::STAT_STATUS_DELETED);
		}
		
		if(isset($params['group_id']) && !empty($params['group_id'])) {
			$groups = GroupModel::inst()->getGroupsByOwner($params['group_id']);
			$group_ids = array();
			
			$_addons_groups = array();
            
			foreach($groups as $_k=>$_v) {
                $_addons_groups[] = array(
                    "OR",
                    "`u`.`group_id`=:group_id_{$_k}",
                );
				$sql_params[":group_id_{$_k}"] = $_v['group_id'];
			}
            
			$builds['where'][] = array(
				'AND',
				$_addons_groups,
			);
		}
		
		if(isset($params['search_key']) && $params['search_key']) {
			$builds['where'][] = array(
				'AND',
				array(
					'OR LIKE',
					'u.user_name',
					':search_key_1',
				),
				array(
					'OR LIKE', 
					'u.realname',
					':search_key_2',
				),
				array(
					'OR LIKE', 
					'u.email',
					':search_key_3',
				),
			);
            $sql_params[':search_key_1'] = "%{$params['search_key']}%";
            $sql_params[':search_key_2'] = "%{$params['search_key']}%";
            $sql_params[':search_key_3'] = "%{$params['search_key']}%";
		}
        $sql = $this->buildQuery($builds);
		
		//统计数量
		$count =  $this->db->queryScalar($sql, $sql_params);
		
		//分页处理
		$pages = new CPagination($count);
		
		//设置分页大小
		$pages->pageSize = $params['pagesize'];
		
		//清空前面执行过的SQL
		if(isset($params['orderby']) && $params['orderby']) {
			$builds['order'] = $params['orderby'];
		} else {
			$builds['order'] = array(
                '`r`.`role_rank` ASC',
                '`u`.`user_rank` ASC',
                '`u`.`user_id` ASC',
			);
		}
        $builds['select'] = 'u.user_id, u.user_name, u.realname, u.email, u.user_id, u.group_id, g.group_name, u.logintimes, u.lastvisit, u.lastip, u.user_rank, u.lasttime, u.dateline, u.is_system, u.status';
        $pages->applyLimit($builds);
        $sql = $this->buildQuery($builds);
        
		$result['pages'] = $pages;
		$result['rows'] = $this->db->queryAll($sql, $sql_params);
		
		//有开启缓存，则把结果添加到缓存中
		if($params['allow_cache'] && isset($this->cache)) {
			$cache_time_out = SettingModel::inst()->getSettingCache('ADMIN_LOGS_TIME_OUT');
			$this->cache->add($cache_key, $result, $cache_time_out);
			unset($cache_time_out, $cache_key);
		}
		
		return $result;
	}
}
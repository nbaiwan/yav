<?php

class Admin_UserController extends SysController {

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function createAction()
	{
		if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['Admin']))
		{
			$_POST['Admin']['purviews'] = isset($_POST['Admin']['purviews']) ? json_encode($_POST['Admin']['purviews']) : json_encode(array());
			
			$salt = UserModel::inst()->signSalt();
			$password = md5(md5($_POST['Admin']['password']) . $salt);
		
			$allow_groups = GroupModel::inst()->getGroupsByOwner($this->user->group_id);
			
			if($allow_groups[$this->user->group_id]['purviews'] != 'all' && (!isset($allow_groups[$_POST['Admin']['group_id']]) || $_POST['Admin']['group_id'] == $this->user->group_id)) {
				$this->redirect[] = array(
					'text' => '',
					'href' => $this->forward,
				);
				$this->message('您无权限在该角色组下添加新用户', self::MSG_ERROR, true);
			}
				
			$update_data = array(
				'user_id' => 0,
				'user_name' => $_POST['Admin']['user_name'],
				'realname' => $_POST['Admin']['realname'],
				'email' => $_POST['Admin']['email'],
				'password' => $password,
				'salt' => $salt,
				'group_id' => $_POST['Admin']['group_id'],
				'purviews' => $_POST['Admin']['purviews'],
				'logintimes' => 0,
				'lastvisit' => 0,
				'lastip' => 0,
				'user_rank' => isset($_POST['Admin']['user_rank']) && $_POST['Admin']['user_rank'] ? intval($_POST['Admin']['user_rank']) : 255,
				'status' => $_POST['Admin']['status'],
				'lasttime' => $_SERVER['REQUEST_TIME'],
				'dateline' => $_SERVER['REQUEST_TIME'],
			);
			$flag = $this->db->insert(
				'{{admin}}',
				$update_data
			);
			
			if($flag) {
				//记录操作日志
				$message = '{user_name}添加了管理员{administrator}';
				$data = array(
					'administrator' => $_POST['Admin']['user_name'],
					'data' => $_POST['Admin'],
				);
				UserLogsModel::inst()->add('Admin/User', $this->db->getLastInsertID(), 'Modify', 'success', $message, $data);
				
				$this->redirect[] = array(
					'text' => '',
					'href' => $this->forward,
				);
				$this->message('添加管理员完成', self::MSG_SUCCESS, true);
			}
		}
		
		$user = array(
			'user_id' => 0,
			'user_name' => '',
			'realname' => '',
			'email' => '',
			'group_id' => 0,
			'user_rank' => 0,
			'status' => UserModel::STAT_STATUS_NORMAL,
		);
		//$roles = GroupModel::inst()->get_roles_by_cache();
		//$purviews = PurviewModel::getPurviewList();
		$roles = GroupModel::inst()->getGroupsByOwner($this->user->group_id);
		$purviews = PurviewModel::inst()->getPurviewsByOwner($this->user->group_id, $this->user->id);

		$this->render('create',array(
			'user'=>$user,
			'roles'=>$roles,
			'purviews'=>$purviews,
		));
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function updateAction($id)
	{
		$user = UserModel::inst()->getUserById($id);
		if($user) {
			$user['purviews'] = $user['purviews'] ? json_decode($user['purviews'], true) : array();
			$user['purviews'] = is_array($user['purviews']) ? array($user['purviews']) : array();
		}
		
		if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['Admin']))
		{
			$administrator = $user['user_name'];
			$_POST['Admin']['purviews'] = isset($_POST['Admin']['purviews']) ? json_encode($_POST['Admin']['purviews']) : json_encode(array());
			
			$password = empty($_POST['Admin']['password']) ? $user['password'] : md5(md5($_POST['Admin']['password']) . $user['salt']);
		
			$allow_groups = GroupModel::inst()->getGroupsByOwner($this->user->group_id);
			
			if($user['user_id'] == $this->user->id) {
				$update_data = array(
					'user_name' => $_POST['Admin']['user_name'],
					'realname' => $_POST['Admin']['realname'],
					'email' => $_POST['Admin']['email'],
					'password' => $password,
					'lasttime' => $_SERVER['REQUEST_TIME'],
				);
			} else {
				$allow_groups = GroupModel::inst()->getGroupsByOwner($this->user->group_id);
				if($allow_groups[$this->user->group_id]['purviews'] != 'all' && (!isset($allow_groups[$_POST['Admin']['group_id']]) || $_POST['Admin']['group_id'] == $this->user->group_id)) {
					$this->redirect[] = array(
						'text' => '',
						'href' => $this->forward,
					);
					$this->message('您无权限在该角色组下添加新用户', self::MSG_ERROR, true);
				}
				
				$update_data = array(
					'user_name' => $_POST['Admin']['user_name'],
					'realname' => $_POST['Admin']['realname'],
					'email' => $_POST['Admin']['email'],
					'password' => $password,
					'group_id' => isset($_POST['Admin']['group_id']) ? $_POST['Admin']['group_id'] : $user['group_id'],
					'purviews' => isset($_POST['Admin']['purviews']) ? $_POST['Admin']['purviews'] : $user['purviews'],
					'user_rank' => isset($_POST['Admin']['user_rank']) ? $_POST['Admin']['user_rank'] : $user['user_rank'],
					'status' => isset($_POST['Admin']['status']) ? $_POST['Admin']['status'] : $user['status'],
					'lasttime' => $_SERVER['REQUEST_TIME'],
				);
			}
			$flag = $this->db->update(
				'{{admin}}',
				$update_data,
				'user_id=:user_id',
				array(
					':user_id'=>$id
				)
			);
			
			if($flag) {
				//记录操作日志
				$message = '{user_name}修改了管理员{administrator}信息';
				if($administrator != $_POST['Admin']['user_name']) {
					$message .= '，改名为：{new_administrator}';
				}
				$data = array(
					'administrator' => $administrator,
					'new_administrator' => $_POST['Admin']['user_name'],
					'data' => $_POST['Admin'],
				);
				UserLogsModel::inst()->add('Admin/User', $id, 'Insert', 'success', $message, $data);
				
				$this->redirect[] = array(
					'text' => '',
					'href' => $this->forward,
				);
				$this->message('修改管理员资料完成', self::MSG_SUCCESS, true);
			}
		}
		//$user['APIDs'] = array();
		//$roles = GroupModel::inst()->get_roles_by_cache();
		//$purviews = PurviewModel::getPurviewList();
		$roles = GroupModel::inst()->getGroupsByOwner($this->user->group_id);
		$purviews = PurviewModel::get_purviews_by_owner($this->user->group_id, $this->user->id);

		$this->render('update',array(
			'user'=>$user,
			'roles' => $roles,
			'purviews'=>$purviews,
		));
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function deleteAction($id)
	{
		$user = UserModel::inst()->getUserById($id);
		
		if($id == '1') {
			$this->redirect[] = array(
				'text' => Yii::t('admincp', 'Admin Index'),
				'href' => $this->forward,
			);
			$this->message(Yii::t('admincp', 'System Admin Can Not Be Delete'), self::MSG_ERROR, true);
		}
		$administrator = $user['user_name'];
		$flag = $this->db->update(
			'{{admin}}',
			array(
				'status' => UserModel::STAT_STATUS_DELETED,
			),
			'user_id=:user_id',
			array(
				':user_id' => $id,
			)
		);
		
		if($flag) {
		
			//记录操作日志
			$message = '{user_name}删除了管理员{administrator}';
			$data = array(
				'administrator' => $user['username'],
				'data' => $user,
			);
			UserLogsModel::inst()->add('Admin/User', $user['user_id'], 'Delete', 'success', $message, $data);
		}

		// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
		if(!isset($_GET['ajax'])) {
			$this->redirect[] = array(
				'text' => '',
				'href' => $this->forward,
			);
			$this->message('删除管理员信息完成', self::MSG_SUCCESS, true);
		}
	}

	/**
	 * Manages all models.
	 */
	public function indexAction()
	{
		$params = array(
			'group_id' => $this->user->group_id,
		);
		
		$this->getView()->assign(
            array(
				'data' => UserModel::inst()->Pages($params),
			)
		);
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function modifyMyInfoAction() {
		$user = UserModel::inst()->getUserById($this->user->id);
		if($user) {
			$user['purviews'] = $user['purviews'] ? json_decode($user['purviews'], true) : array();
			$user['purviews'] = is_array($user['purviews']) ? array($user['purviews']) : array();
		}
		
		if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['Admin'])) {
			$_POST['Admin']['purviews'] = isset($_POST['Admin']['purviews']) ? json_encode($_POST['Admin']['purviews']) : json_encode(array());
			
			$update_data = array(
				//'user_name' => $_POST['Admin']['user_name'],
				'realname' => $_POST['Admin']['realname'],
				'email' => $_POST['Admin']['email'],
				'lasttime' => $_SERVER['REQUEST_TIME'],
			);
			$flag = $this->db->update(
				'{{user}}',
				$update_data,
				'user_id=:user_id',
				array(
					':user_id' => $this->user->id,
				)
			);
			
			if($flag) {
				//记录操作日志
				$message = '管理员{user_name}修改了自己的用户资料';
				$data = array(
					'data' => $_POST['Admin'],
				);
				UserLogsModel::inst()->add('Admin/User/ModifyMyInfo', $this->user->id, 'Insert', 'success', $message, $data);
				
				$this->redirect[] = array(
					'text' => '',
					'href' => $this->forward,
				);
				$this->message('修改用户资料完成', self::MSG_SUCCESS, true);
			}
		}

		$this->getView()->assign(
            array(
                'user'=>$user,
            )
        );
	}

	/**
	 * Manages all models.
	 */
	public function modifyMyPwdAction() {
		$user = UserModel::inst()->getUserById($this->user->id);
		
		if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['Admin']))
		{
			// Check Old Password
			$password = md5(md5($_POST['Admin']['password']) . $user['salt']);
			if($password != $user['password']) {
				$this->redirect[] = array(
					'text' => '',
					'href' => $this->forward,
				);
				$this->message('您填写的旧密码错误！', self::MSG_ERROR, true);
			}
			
			// Check New Password
			if(empty($_POST['Admin']['new_password']) || strlen($_POST['Admin']['new_password']) < 6 || strlen($_POST['Admin']['new_password']) > 20) {
				$this->redirect[] = array(
					'text' => '',
					'href' => $this->forward,
				);
				$this->message('新密码必须是6-20个字符！', self::MSG_ERROR, true);
			}
			
			// Sure New Password
			if($_POST['Admin']['new_password'] != $_POST['Admin']['new_password2']) {
				$this->redirect[] = array(
					'text' => '',
					'href' => $this->forward,
				);
				$this->message('您输入的两次密码不一致！', self::MSG_ERROR, true);
			}
			$new_password = md5(md5($_POST['Admin']['new_password']) . $user['salt']);
			
			//
			$flag = $this->db->update(
				'{{admin}}',
				array(
					'password' => $new_password,
				),
				'user_id=:user_id',
				array(
					':user_id'=>$this->user->id,
				)
			);
			
			if($flag) {
				//记录操作日志
				$message = '管理员{user_name}修改了自己的密码';
				$data = array(
					'data' => $_POST['Admin'],
				);
				UserLogsModel::inst()->add('Admin/User/ModifyPwd', $this->id, 'Insert', 'success', $message, $data);
				
				$this->redirect[] = array(
					'text' => '',
					'href' => $this->forward,
				);
				$this->message('修改密码成功！', self::MSG_SUCCESS, true);
			} else {
			}
		}
        
        $this->getView()->assign(
            array(
                'user_name' => $this->user->name,
            )
        );
	}
}

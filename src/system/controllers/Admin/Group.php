<?php

class Admin_GroupController extends SystemController
{
	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function createAction() {
		if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['Group'])) {
			$allow_groups = GroupModel::inst()->getGroupsByOwner($this->user->group_id);
			
			if($allow_groups[$this->user->group_id]['purviews'] != 'all' && !isset($allow_groups[$_POST['Group']['parent_id']])) {
				$this->redirect[] = array(
					'text' => '',
					'href' => $this->forward,
				);
				$this->message('您无权限在该用户组下添加子用户组', self::MSG_ERROR, true);
			}
			
			$_POST['Group']['purviews'] = isset($_POST['Group']['purviews']) ? json_encode($_POST['Group']['purviews']) : json_encode(array());
			
			$update_data = array(
				'group_id' => 0,
				'group_name' => $_POST['Group']['group_name'],
				'parent_id' => $_POST['Group']['parent_id'],
				'purviews' => $_POST['Group']['purviews'],
				'group_rank' => $_POST['Group']['group_rank'],
				'status' => GroupModel::inst()->STAT_STATUS_NORMAL,
				'lasttime' => $_SERVER['REQUEST_TIME'],
				'dateline' => $_SERVER['REQUEST_TIME'],
			);
			$flag = $this->db->insert(
				'{{role}}',
				$update_data
			);
			
			if($flag) {
				//更新缓存
				GroupModel::inst()->updateCache();
				//记录操作日志
				$message = '{user_name}添加了用户组{group_name}';
				$data = array(
					'group_name' => $_POST['Group']['group_name'],
					'data' => $_POST['Group'],
				);
				UserLogsModel::inst()->add('Admin/Admin', $this->db->getLastInsertID(), 'Modify', 'success', $message, $data);
				
				$this->redirect[] = array(
					'text' => '',
					'href' => $this->forward,
				);
				$this->message('添加用户组完成', self::MSG_SUCCESS, true);
			}
		}
		
		$role = array(
			'group_id' => 0,
			'group_name' => '',
			'parent_id' => 0,
			'purviews' => array(),
			'group_rank' => 255,
			'status' => GroupModel::inst()->STAT_STATUS_NORMAL,
		);
		//$groups = GroupModel::inst()->get_groups_by_cache();
		//$purviews = Purview::getPurviewList();
		$groups = GroupModel::inst()->getGroupsByOwner($this->user->group_id);
		$purviews = PurviewModel::inst()->getPurviewsByOwner($this->user->group_id, $this->user->id);
		
		$this->getView()->assign(
            array(
                'role'=>$role,
                'groups'=>$groups,
                'purviews'=>$purviews,
            )
        );
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function updateAction($id) {
		$group = GroupModel::inst()->getGroupById($id);
		if($group['is_system'] == '1') {
			$this->redirect[] = array(
				'text' => '用户组列表',
				'href' => $this->forward,
			);
			$this->message('系统组不能被修改', self::MSG_ERROR, true);
		}

		if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['Group'])) {
			$allow_groups = GroupModel::inst()->getGroupsByOwner($this->user->group_id);
			if($allow_groups[$this->user->group_id]['purviews'] != 'all' && !isset($allow_groups[$_POST['Group']['parent_id']])) {
				$this->redirect[] = array(
					'text' => '',
					'href' => $this->forward,
				);
				$this->message('您无权限在该用户组下添加子用户组', self::MSG_ERROR, true);
			}
			
			$_POST['Group']['purviews'] = isset($_POST['Group']['purviews']) ? json_encode($_POST['Group']['purviews']) : json_encode(array());
			
			$update_data = array(
				'group_name' => $_POST['Group']['group_name'],
				'parent_id' => $_POST['Group']['parent_id'],
				'purviews' => $_POST['Group']['purviews'],
				'group_rank' => $_POST['Group']['group_rank'],
				'lasttime' => $_SERVER['REQUEST_TIME'],
			);
			$flag = $this->db->update(
				'{{group}}',
				$update_data,
				'group_id=:group_id',
				array(
					':group_id' => $id,
				)
			);
			
			if($flag) {
				//更新缓存
				GroupModel::inst()->updateCache();
				//记录操作日志
				$message = '{user_name}修改了用户组{group_name}';
				if($group['group_name'] != $_POST['Group']['group_name']) {
					$message .= '，改名为：{new_group_name}';
				}
				$data = array(
					'group_name' => $group['group_name'],
					'new_group_name' => $_POST['Group']['group_name'],
					'data' => array(
						'old' => $group,
						'new' => $_POST['Group'],
					),
				);
				UserLogsModel::inst()->add('Admin/Group', $id, 'Modify', 'success', $message, $data);
				
				$this->redirect[] = array(
					'text' => '',
					'href' => $this->forward,
				);
				$this->message('修改用户组完成', self::MSG_SUCCESS, true);
			} else {
				$this->message('修改用户组信息失败', self::MSG_ERROR, false);
			}
		}
		
		$groups = GroupModel::inst()->getGroupsByOwner($this->user->group_id);
		$purviews = PurviewModel::inst()->getPurviewsByOwner($this->user->group_id, $this->user->id);
		
		$this->getView()->assign(
            array(
                'my_group_id' => $this->user->group_id,
                'group' => $group,
                'groups' => $groups,
                'purviews' => $purviews,
            )
        );
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function deleteAction($id) {
        $group = GroupModel::inst()->getGroupById($id);
        if(!$group) {
            $this->redirect[] = array(
                'text' => '用户组列表',
                'href' => '/admin/group/index',
            );
            $this->message('用户组不存在', self::MSG_ERROR, true);
        }
        
        if($group['is_system'] == '1') {
            $this->redirect[] = array(
                'text' => '用户组列表',
                'href' => '/admin/group/index',
            );
            $this->message('系统组不能被删除', self::MSG_ERROR, true);
        }
        $sql = "UPDATE {{group}} SET `status`=:status WHERE `group_id`=:group_id AND is_system<>:is_system";
        $params = array(
            ':group_id' => $id,
            ':status' => 0,
            ':is_system' => 1,
        );
        if($flag = $this->db->execute($sql, $params)) {
            //记录操作日志
            $message = '{user_name}删除了用户组{group_name}';
            $data = array(
                'group_name' => $group['group_name'],
                'data' => $group,
            );
            UserLogsModel::inst()->add('Admin/Group', $group['group_id'], 'Delete', 'success', $message, $data);
            
            if(!isset($_GET['ajax'])) {
                $this->redirect[] = array(
                    'text' => '用户组列表',
                    'href' => '/admin/group/index',
                );
                $this->message('删除用户组成功', self::MSG_ERROR, true);
            } else {
                echo json_encode(
                    array(
                        'ok'=>true,
                    )
                );
                exit;
            }
        } else {
            if(!isset($_GET['ajax'])) {
                $this->redirect[] = array(
                    'text' => '用户组列表',
                    'href' => '/admin/group/index',
                );
                $this->message('删除用户组成功', self::MSG_ERROR, true);
            } else {
                echo json_encode(
                    array(
                        'ok'=>false,
                    )
                );
                exit;
            }
        }
	}
    
	public function indexAction() {
		$this->getView()->assign(
			array(
				'groups' => GroupModel::inst()->getGroupsByOwner($this->user->group_id),
			)
		);
	}
}

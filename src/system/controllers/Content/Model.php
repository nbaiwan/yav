<?php

class Content_ModelController extends SysController {
	
	/**
	 * 添加内容模型
	 */
	public function actionCreate() {
		if($_SERVER['REQUEST_METHOD'] == 'POST') {
			if(!isset($_POST['Model']) || !is_array($_POST['Model'])) {
				$this->redirect[] = array(
					'text' => '',
					'href' => 'javascript:history.go(-1);',
				);
				$this->message('保存内容模型信息错误', self::MSG_ERROR, true);
			}
			
			if($_POST['Model']['content_model_name'] =='') {
				$this->redirect[] = array(
					'text' => '',
					'href' => 'javascript:history.go(-1);',
				);
				$this->message('内容模型名称不能为空', self::MSG_ERROR, true);
			}
			
			if(empty($_POST['Model']['content_model_identify'])) {
				$this->redirect[] = array(
					'text' => '',
					'href' => 'javascript:history.go(-1);',
				);
				$this->message('内容模型标识不能为空', self::MSG_ERROR, true);
			}
			
			$flag = Yii::app()->db->createCommand()->insert('{{content_model}}',
				array(
					'content_model_id' => 0,
					'content_model_name' => $_POST['Model']['content_model_name'],
					'content_model_identify' => $_POST['Model']['content_model_identify'],
					'content_model_edit_template' => $_POST['Model']['content_model_edit_template'],
					'content_model_list_template' => $_POST['Model']['content_model_list_template'],
					'content_model_is_default' => ($_POST['Model']['content_model_is_default']) ? intval($_POST['Model']['content_model_is_default']) : 0,
					'content_model_rank' => ($_POST['Model']['content_model_rank']) ? intval($_POST['Model']['content_model_rank']) : 255,
					'content_model_status' => ContentModel::STAT_ALLOW_YES,
					'content_model_lasttime' => $_SERVER['REQUEST_TIME'],
					'content_model_dateline' => $_SERVER['REQUEST_TIME'],
				)
			);
			
			$content_model_id = 0;
			$content_model_name = $_POST['Model']['content_model_name'];
			if($flag) {
				$content_model_id = Yii::app()->db->getLastInsertID();
				if(intval($_POST['Model']['content_model_is_default'])) {
					Yii::app()->db->createCommand()->update(
						'{{content_model}}',
						array(
							'content_model_is_default' => 0,
						),
						'content_model_id<>:content_model_id',
						array(':content_model_id'=>$content_model_id)
					);
				}
				//更新缓存
				ContentModel::update_cache();
				
				//记录操作日志
				$user = Yii::app()->user;
				$message = '{user_name}添加了内容模型({model_name})';
				$data = array(
					'user_id' => $user->id,
					'user_name' => $user->name,
					'model_name' => $content_model_name,
					'addons_data' => array('content_model_id'=>$content_model_id),
				);
				AdminLogs::add($user->id, 'Content/Model', $content_model_id, 'Insert', 'success', $message, $data);
				
				if(!isset($_GET['ajax'])) {
					$this->redirect[] = array(
						'text' => '',
						'href' => url($this->module->id. '/Content/Model/Index'),
					);
					$this->message('添加内容模型完成', self::MSG_SUCCESS, true);
				}
			} else {
				//记录操作日志
				$user = Yii::app()->user;
				$message = '{user_name}添加内容模型{model_name}失败';
				$data = array(
					'user_id' => $user->id,
					'user_name' => $user->name,
					'model_name' => $content_model_name,
					'addons_data' => array('server'=>$_POST['Model']),
				);
				AdminLogs::add($user->id, 'Content/Model', $content_model_id, 'Insert', 'failure', $message, $data);
				
				$this->redirect[] = array(
					'text' => '',
					'href' => 'javascript:history.go(-1);',
				);
				$this->message('添加内容模型失败', self::MSG_ERROR, true);
			}
		}
		
		$model = array(
			'content_model_id' => 0,
			'content_model_name' => '',
			'content_model_identify' => '',
			'content_model_is_default' => 0,
			'content_model_rank' => 255,
		);
		
		$this->render('create',
			array(
				'model' => $model,
			)
		);
	}
	
	public function actionUpdate($id, $page = null)
	{
		if($_SERVER['REQUEST_METHOD'] == 'POST') {
			if(!isset($_POST['Model']) || !is_array($_POST['Model'])) {
				$this->redirect[] = array(
					'text' => '',
					'href' => 'javascript:history.go(-1);',
				);
				$this->message('保存内容模型信息错误', self::MSG_ERROR, true);
			}
			
			if($_POST['Model']['content_model_name'] =='') {
				$this->redirect[] = array(
					'text' => '',
					'href' => 'javascript:history.go(-1);',
				);
				$this->message('内容模型名称不能为空', self::MSG_ERROR, true);
			}
			
			/*if(empty($_POST['Model']['content_model_identify'])) {
				$this->redirect[] = array(
					'text' => '',
					'href' => 'javascript:history.go(-1);',
				);
				$this->message('内容模型标识不能为空', self::MSG_ERROR, true);
			}*/
			
			$flag = Yii::app()->db->createCommand()->update('{{content_model}}',
				array(
					'content_model_name' => $_POST['Model']['content_model_name'],
					//'content_model_identify' => $_POST['Model']['content_model_identify'],
					'content_model_edit_template' => $_POST['Model']['content_model_edit_template'],
					'content_model_list_template' => $_POST['Model']['content_model_list_template'],
					'content_model_is_default' => ($_POST['Model']['content_model_is_default']) ? intval($_POST['Model']['content_model_is_default']) : 0,
					'content_model_rank' => ($_POST['Model']['content_model_rank']) ? intval($_POST['Model']['content_model_rank']) : 255,
					'content_model_lasttime' => $_SERVER['REQUEST_TIME'],
				),
				'content_model_id=:content_model_id',
				array(':content_model_id'=>$id)
			);
			
			if($flag) {
				$content_model_name = $_POST['Model']['content_model_name'];
				if(intval($_POST['Model']['content_model_is_default'])) {
					Yii::app()->db->createCommand()->update(
						'{{content_model}}',
						array(
							'content_model_is_default' => 0,
						),
						'content_model_id<>:content_model_id',
						array(':content_model_id'=>$id)
					);
				}
				//更新缓存
				ContentModel::update_cache();
				
				//记录操作日志
				$user = Yii::app()->user;
				$message = '{user_name}修改了内容模型({model_name})信息';
				$data = array(
					'user_id' => $user->id,
					'user_name' => $user->name,
					'model_name' => $content_model_name,
					'addons_data' => array('content_model_id'=>$id),
				);
				AdminLogs::add($user->id, 'Content/Model', $id, 'Modify', 'success', $message, $data);
				
				if(!isset($_GET['ajax'])) {
					$this->redirect[] = array(
						'text' => '',
						'href' => url($this->module->id. '/Content/Model/Index'.($page ? '/'.$page : '')),
					);
					$this->message('保存内容模型成功', self::MSG_SUCCESS, true);
				}
			} else {
				//记录操作日志
				$user = Yii::app()->user;
				$message = '{user_name}修改内容模型({model_name})信息失败';
				$data = array(
					'user_id' => $user->id,
					'user_name' => $user->name,
					'model_name' => $content_model_name,
					'addons_data' => array('server'=>$_POST['Model']),
				);
				AdminLogs::add($user->id, 'Content/Model', $id, 'Modify', 'failure', $message, $data);
				
				$this->redirect[] = array(
					'text' => '',
					'href' => 'javascript:history.go(-1);',
				);
				$this->message('保存内容模型信息失败', self::MSG_ERROR, true);
			}
		}
		
		$model = ContentModel::get_model_by_id($id);
		if(empty($model)) {
			$this->redirect[] = array(
				'text' => '',
				'href' => url($this->module->id . '/Content/Model/Index'),
			);
			$this->message('内容模型不存在或已被删除', self::MSG_ERROR, true);
		}
		
		$this->render('update',
			array(
				'model' => $model,
			)
		);
	}
	
	/**
	 * 设置默认模型
	 * @param unknown_type $id
	 */
	public function actionDefault($id)
	{
		$content_model = ContentModel::get_model_by_id($id);
		if($content_model['content_model_status'] == ContentModel::STAT_ALLOW_YES) {
			$flag = Yii::app()->db->createCommand()->update(
				'{{content_model}}',
				array(
					'content_model_is_default' => 1,
				),
				'content_model_id=:content_model_id',
				array(':content_model_id'=>$id)
			);
			
			$flag = Yii::app()->db->createCommand()->update(
				'{{content_model}}',
				array(
					'content_model_is_default' => 0,
				),
				'content_model_id<>:content_model_id',
				array(':content_model_id'=>$id)
			);
			//记录操作日志
			$user = Yii::app()->user;
			$message = '{user_name}设置内容模型({model_name})为默认模型';
			$data = array(
				'user_id' => $user->id,
				'user_name' => $user->name,
				'model_name' => $content_model['content_model_name'],
				'addons_data' => array('server'=>$_POST['Model']),
			);
			AdminLogs::add($user->id, 'Content/Model', $id, 'Modify', 'success', $message, $data);
			
			//更新缓存
			ContentModel::update_cache();
			
			$this->redirect[] = array(
				'text' => '',
				'href' => url($this->module->id . '/Content/Model/Index'),
			);
			$this->message('设置默认模型成功', self::MSG_SUCCESS, true);
		} else {
			$this->redirect[] = array(
				'text' => '',
				'href' => url($this->module->id . '/Content/Model/Index'),
			);
			$this->message('该内容模型不允许设置为默认模型', self::MSG_ERROR, true);
		}
	}
	
	/**
	 * 删除内容模型
	 * @param mixed $id 内容模型编号
	 */
	public function actionDelete($id, $page = null)
	{
		$content_model_name = ContentModel::get_model_name_by_id($id);
		Yii::app()->db->createCommand()->update('{{content_model}}',
			array(
				'content_model_status' => ContentModel::STAT_DELETED,
			),
			'content_model_id=:content_model_id',
			array(':content_model_id'=>$id)
		);
		ContentModel::update_cache();
			
		//记录操作日志
		$user = Yii::app()->user;
		$message = '{user_name}删除了内容模型({model_name})';
		$data = array(
			'user_id' => $user->id,
			'user_name' => $user->name,
			'model_name' => $content_model_name,
			'addons_data' => array('content_model_id'=>$id),
		);
		AdminLogs::add($user->id, 'Content/Model', $id, 'Delete', 'success', $message, $data);
		
		if(!isset($_GET['ajax'])) {
			$this->redirect[] = array(
				'text' => '',
				'href' => url($this->module->id. '/Content/Model/Index'.($page ? '/'.$page : '')),
			);
			$this->message('删除内容模型完成', self::MSG_SUCCESS, true);
		}
	}
	
	public function actionEnable($id)
	{
		$content_model = ContentModel::get_model_by_id($id);
		Yii::app()->db->createCommand()->update(
			'{{content_model}}',
			array(
				'content_model_status' => ContentModel::STAT_ALLOW_YES,
			),
			'content_model_id=:content_model_id',
			array(':content_model_id'=>$id)
		);
		//更新缓存
		ContentModel::update_cache();
		
		//记录操作日志
		$user = Yii::app()->user;
		$message = '{user_name}修改内容模型({model_name})状态为启用';
		$data = array(
			'user_id' => $user->id,
			'user_name' => $user->name,
			'model_name' => $content_model['content_model_name'],
			'addons_data' => array('server'=>$_POST['Model']),
		);
		AdminLogs::add($user->id, 'Content/Model', $id, 'Modify', 'success', $message, $data);
		
		if(!isset($_GET['ajax'])) {
			$this->redirect[] = array(
				'text' => '',
				'href' => url($this->module->id. '/Content/Model/Index'),
			);
			$this->message('启用内容模型完成', self::MSG_SUCCESS, true);
		} else {
			$_r = array(
				'ok' => true,
			);
			echo json_encode($_r);
			exit;
		}
		
	}
	
	public function actionDisable($id)
	{
		$content_model = ContentModel::get_model_by_id($id);
		Yii::app()->db->createCommand()->update(
			'{{content_model}}',
			array(
				'content_model_status' => ContentModel::STAT_ALLOW_NO,
			),
			'content_model_id=:content_model_id',
			array(':content_model_id'=>$id)
		);
		//更新缓存
		ContentModel::update_cache();
		
		//记录操作日志
		$user = Yii::app()->user;
		$message = '{user_name}修改内容模型({model_name})状态为禁用';
		$data = array(
			'user_id' => $user->id,
			'user_name' => $user->name,
			'model_name' => $content_model['content_model_name'],
			'addons_data' => array('server'=>$_POST['Model']),
		);
		AdminLogs::add($user->id, 'Content/Model', $id, 'Modify', 'success', $message, $data);
		
		if(!isset($_GET['ajax'])) {
			$this->redirect[] = array(
				'text' => '',
				'href' => url($this->module->id. '/Content/Model/Index'),
			);
			$this->message('禁用内容模型完成', self::MSG_SUCCESS, true);
		} else {
			$_r = array(
				'ok' => true,
			);
			echo json_encode($_r);
			exit;
		}
	}
	
	/**
	 * 内容模型管理
	 */
	public function indexAction($page = null) {
		if($_SERVER['REQUEST_METHOD']=='POST') {
			//保存修改
			if(!is_array($_POST['Model']['content_model_rank'])) $_POST['Model']['content_model_rank'] = array();
			foreach($_POST['Model']['content_model_rank'] as $_k=>$_v) {
				$flag = Yii::app()->db->createCommand()->update('{{content_model}}',
					array(
						'content_model_rank' => ($_POST['Model']['content_model_rank'][$_k]) ? intval($_POST['Model']['content_model_rank'][$_k]) : 255,
						'content_model_lasttime' => $_SERVER['REQUEST_TIME'],
					),
					'content_model_id=:content_model_id',
					array(':content_model_id'=>$_k)
				);
				if($flag) {
					//记录操作日志
					$message = '{user_name}修改了内容模型({model_name})排序';
					$data = array(
						'user_id' => Yii::app()->user->id,
						'user_name' => Yii::app()->user->name,
						'model_name' => ContentModel::get_model_name_by_id($_k),
						'addons_data' => $_POST,
					);
					AdminLogs::add(Yii::app()->user->id, 'Content/Model', $_k, 'Modify', 'success', $message, $data);
				}
			}
			ContentModel::update_cache();
			
			//$this->refresh();
			
			$this->redirect[] = array(
				'text' => '',
				'href' => '/content/model/index',
			);
			$this->message('修改内容模型完成', self::MSG_SUCCESS, true);
		}
		
		$this->getView()->assign(
            array(
                'models' => ContentModelModel::inst()->getModelsByCache(),
            )
        );
	}
}

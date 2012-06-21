<?php

class FieldController extends SystemController
{
	/**
	 * 
	 * @param unknown_type $content_model_id
	 */
	public function actionCreate($content_model_id)
	{
		if($_SERVER['REQUEST_METHOD'] == 'POST') {
			if(!isset($_POST['Field']) || !is_array($_POST['Field'])) {
				$this->redirect[] = array(
					'text' => '',
					'href' => 'javascript:history.go(-1);',
				);
				$this->message('保存模型字段信息错误', self::MSG_ERROR, true);
			}
			
			if($_POST['Field']['content_model_field_name'] =='') {
				$this->redirect[] = array(
					'text' => '',
					'href' => 'javascript:history.go(-1);',
				);
				$this->message('字段名称不能为空', self::MSG_ERROR, true);
			}
			
			if($_POST['Field']['content_model_field_identify'] =='') {
				$this->redirect[] = array(
					'text' => '',
					'href' => 'javascript:history.go(-1);',
				);
				$this->message('字段标识不能为空', self::MSG_ERROR, true);
			}
			
			if(!preg_match("/^[\w]+$/",$_POST['Field']['content_model_field_identify'])) {
				$this->redirect[] = array(
					'text' => '',
					'href' => 'javascript:history.go(-1);',
				);
				$this->message('字段标识只能为英文字母', self::MSG_ERROR, true);
			}
			
			if(!isset($_POST['Field']['content_model_field_type']) || $_POST['Field']['content_model_field_type'] == '') {
				$this->redirect[] = array(
					'text' => '',
					'href' => 'javascript:history.go(-1);',
				);
				$this->message('请选择字段数据类型', self::MSG_ERROR, true);
			}
			
			if(intval($_POST['Field']['content_model_field_max_length'])<1) {
				$this->redirect[] = array(
					'text' => '',
					'href' => 'javascript:history.go(-1);',
				);
				$this->message('字段最大长度格式错误，请填写大于0的数字', self::MSG_ERROR, true);
			}
			
			//检查标识是否重复
			$sql = "SELECT COUNT(content_model_field_id) from {{content_model_fields}}
					WHERE content_model_id=:content_model_id AND content_model_field_identify=:content_model_field_identify AND content_model_field_status<>:content_model_field_status";
			$cmd = Yii::app()->db->createCommand($sql);
			$cmd->bindValue(':content_model_field_identify', $_POST['Field']['content_model_field_identify']);
			$cmd->bindValue(':content_model_id', $content_model_id);
			$cmd->bindValue(':content_model_field_status', ContentModelField::STAT_DELETED);
			if($cmd->queryScalar()>0) {
				$this->redirect[] = array(
					'text' => '',
					'href' => 'javascript:history.go(-1);',
				);
				$this->message('字段标识已经存在，请返回修改', self::MSG_ERROR, true);
			}
			
			$flag = Yii::app()->db->createCommand()->insert('{{content_model_fields}}',
				array(
					'content_model_field_id' => 0,
					'content_model_id' => $content_model_id,
					'content_model_field_name' => $_POST['Field']['content_model_field_name'],
					'content_model_field_identify' => $_POST['Field']['content_model_field_identify'],
					'content_model_field_type' => isset($_POST['Field']['content_model_field_type']) ? intval($_POST['Field']['content_model_field_type']) : 0,
					'content_model_field_default' => $_POST['Field']['content_model_field_default'],
					'content_model_field_tips' => $_POST['Field']['content_model_field_tips'],
					'content_model_field_max_length' => isset($_POST['Field']['content_model_field_max_length']) ? intval($_POST['Field']['content_model_field_max_length']) : 1,
					'content_model_field_rank' => !empty($_POST['Field']['content_model_field_rank']) ? intval($_POST['Field']['content_model_field_rank']) : 255,
					'content_model_field_is_system' => 0,
					'content_model_field_status' => ContentModelField::STAT_ALLOW_YES,
					'content_model_field_lasttime' => $_SERVER['REQUEST_TIME'],
					'content_model_field_dateline' => $_SERVER['REQUEST_TIME'],
				)
			);
			
			//
			if($flag) {
				$content_model_field_id = Yii::app()->db->getLastInsertID();
				$content_model_field_name = $_POST['Field']['content_model_field_name'];
				
				$content_model_identify = ContentModel::get_model_identify_by_id($content_model_id);
				$newfields = array(
					array(
						'field_name' => $_POST['Field']['content_model_field_name'],
						'field_identify' => $_POST['Field']['content_model_field_identify'],
						'field_type' => $_POST['Field']['content_model_field_type'],
						'field_length' => $_POST['Field']['content_model_field_max_length'],
					),
				);
				if(ContentModel::alter_addons_table($content_model_identify, $newfields)) {
					//更新附加表成功
				} else {
					//更新附加表失败
				}
				
				//更新缓存
				ContentModelField::update_cache();
				
				//记录操作日志
				$user = Yii::app()->user;
				$message = '{user_name}添加模型字段{field_name}';
				$data = array(
					'user_id' => $user->id,
					'user_name' => $user->name,
					'field_name' => $content_model_field_name,
					'data' => array('content_model_field_id'=>$content_model_field_id),
				);
				AdminLogs::add($user->id, 'Content/Model/Field', $content_model_field_id, 'Insert', 'success', $message, $data);
			
				if(!isset($_GET['ajax'])) {
					$this->redirect[] = array(
						'text' => '',
						'href' => url($this->module->id. "/Content/Model/{$content_model_id}/Field/Index"),
					);
					$this->message('添加模型字段完成', self::MSG_SUCCESS, true);
				}
			} else {
				//记录操作日志
				$user = Yii::app()->user;
				$message = '{user_name}添加模型字段{field_name}失败';
				$data = array(
					'user_id' => $user->id,
					'user_name' => $user->name,
					'field_name' => $_POST['Field']['content_model_field_name'],
					'data' => array('fields'=>$_POST['Field']),
				);
				AdminLogs::add($user->id, 'Content/Model/Field', $content_model_field_id, 'Insert', 'failure', $message, $data);
				
				$this->redirect[] = array(
					'text' => '',
					'href' => 'javascript:history.go(-1);',
				);
				$this->message('添加模型字段失败', self::MSG_ERROR, true);
			}
		}
		
		$field = array(
			'content_model_field_id' => 0,
			'content_model_field_name' => '',
			'content_model_field_identify' => '',
			'content_model_field_type' => ContentModelField::DATA_TYPE_SINGLE_TEXT_VARCHAR,
			'content_model_field_default' => '',
			'content_model_field_tips' => '',
			'content_model_field_max_length' => '80',
			'content_model_field_rank' => 255,
			//'field_type_arr' => ContentModelField::$_TYPE
		);
		$this->render('create',
			array(
				'field' => $field,
				"content_model_id" => $content_model_id,
			)
		);
	}
	
	public function actionUpdate($content_model_id, $id, $page = null)
	{
		$field = ContentModelField::get_field_by_id($id,false);
		if($_SERVER['REQUEST_METHOD'] == 'POST') {
			if(!isset($_POST['Field']) || !is_array($_POST['Field'])) {
				$this->redirect[] = array(
					'text' => '',
					'href' => 'javascript:history.go(-1);',
				);
				$this->message('保存模型字段信息错误', self::MSG_ERROR, true);
			}
			
			if($_POST['Field']['content_model_field_name'] =='') {
				$this->redirect[] = array(
					'text' => '',
					'href' => 'javascript:history.go(-1);',
				);
				$this->message('字段名称不能为空', self::MSG_ERROR, true);
			}
			
			/*if($_POST['Field']['content_model_field_identify'] =='') {
				$this->redirect[] = array(
					'text' => '',
					'href' => 'javascript:history.go(-1);',
				);
				$this->message('字段标识不能为空', self::MSG_ERROR, true);
			}
			
			if(!preg_match("/^[\w]+$/",$_POST['Field']['content_model_field_identify'])) {
				$this->redirect[] = array(
					'text' => '',
					'href' => 'javascript:history.go(-1);',
				);
				$this->message('字段标识只能为英文字母', self::MSG_ERROR, true);
			}*/
			
			if(!isset($_POST['Field']['content_model_field_type']) || $_POST['Field']['content_model_field_type'] == '') {
				$this->redirect[] = array(
					'text' => '',
					'href' => 'javascript:history.go(-1);',
				);
				$this->message('请选择字段数据类型', self::MSG_ERROR, true);
			}
			
			if(intval($_POST['Field']['content_model_field_max_length'])<1) {
				$this->redirect[] = array(
					'text' => '',
					'href' => 'javascript:history.go(-1);',
				);
				$this->message('字段最大长度格式错误，请填写大于0的数字', self::MSG_ERROR, true);
			}
			
			/*$content_model_field = ContentModelField::get_field_by_id($id);
			$sql = "SELECT `content_model_field_identify` FROM {{content_model_fields}}
					WHERE content_model_field_id<>:content_model_field_id AND `content_model_field_status`<>:content_model_field_status AND content_model_field_identify=:content_model_field_identify  AND `content_model_id`=:content_model_id";
			$cmd = Yii::app()->db->createCommand($sql);
			$cmd->bindValue(':content_model_field_id', $id);
			$cmd->bindValue(':content_model_field_status', ContentModelField::STAT_DELETED);
			$cmd->bindValue(':content_model_field_identify', $_POST['Field']['content_model_field_identify']);
			$cmd->bindValue(':content_model_id', $content_model_id);
			$cmd->execute();
			
			if($cmd->queryScalar()) {
				$this->redirect[] = array(
					'text' => '',
					'href' => 'javascript:history.go(-1);',
				);
				$this->message('字段标识重复', self::MSG_ERROR, true);
			}*/
			
			$flag = Yii::app()->db->createCommand()->update('{{content_model_fields}}',
				array(
					'content_model_field_name' => $_POST['Field']['content_model_field_name'],
					//'content_model_field_identify' => $_POST['Field']['content_model_field_identify'],
					'content_model_field_type' => isset($_POST['Field']['content_model_field_type']) ? intval($_POST['Field']['content_model_field_type']) : 0,
					'content_model_field_default' => $_POST['Field']['content_model_field_default'],
					'content_model_field_tips' => $_POST['Field']['content_model_field_tips'],
					'content_model_field_max_length' => isset($_POST['Field']['content_model_field_max_length']) ? intval($_POST['Field']['content_model_field_max_length']) : 1,
					'content_model_field_rank' => !empty($_POST['Field']['content_model_field_rank']) ? intval($_POST['Field']['content_model_field_rank']) : 255,
					'content_model_field_lasttime' => $_SERVER['REQUEST_TIME'],
				),
				'content_model_field_id=:content_model_field_id',
				array(':content_model_field_id'=>$id)
			);
			
			//
			if($flag) {
				//更新缓存
				ContentModelField::update_cache();
				
				$content_model_identify = ContentModel::get_model_identify_by_id($content_model_id);
				$newfields = array(
					array(
						'field_name' => $_POST['Field']['content_model_field_name'],
						'field_identify' => $field['content_model_field_identify'],
						'field_type' => $_POST['Field']['content_model_field_type'],
						'field_length' => $_POST['Field']['content_model_field_max_length'],
					),
				);
				if(ContentModel::alter_addons_table($content_model_identify, $newfields)) {
					//更新附加表成功
				} else {
					//更新附加表失败
				}
				
				//记录操作日志
				$user = Yii::app()->user;
				$message = '{user_name}修改内容模型字段({field_name})信息成功';
				$data = array(
					'user_id' => $user->id,
					'user_name' => $user->name,
					'field_name' => $field['content_model_field_name'],
					'data' => array('content_model_field_id'=>$id),
				);
				AdminLogs::add($user->id, 'Content/Model/Field', $id, 'Modify', 'success', $message, $data);
			
			}
			if(!isset($_GET['ajax'])) {
				$this->redirect[] = array(
					'text' => '',
					'href' => url($this->module->id. "/Content/Model/{$content_model_id}/Field/Index"),
				);
				$this->message('修改模型字段完成', self::MSG_SUCCESS, true);
			}
		}
		
		if(empty($field)) {
			$this->redirect[] = array(
				'text' => '',
				'href' => url($this->module->id . "/Collect/Model/{$content_model_id}/Field/Index"),
			);
			$this->message('模型字段不存在或已被删除', self::MSG_ERROR, true);
		}
		
		$this->render('update',
			array(
				'field' => $field,
				"content_model_id" => $content_model_id,
			)
		);
	}
	
	public function actionDelete($content_model_id, $id)
	{
		$model_field = ContentModelField::get_field_by_id($id);
		if(empty($model_field)) {
			$this->redirect[] = array(
				'text' => '',
				'href' => $this->forward,
			);
			$this->message('档案栏目不存在或已被删除', self::MSG_ERROR, true);
		}
		
		if($model_field['content_model_field_is_system'] == 1) {
			$this->redirect[] = array(
				'text' => '',
				'href' => $this->forward,
			);
			$this->message('系统模型字段不允许删除', self::MSG_ERROR, true);
		}
		
		Yii::app()->db->createCommand()->update('{{content_model_fields}}',
			array(
				'content_model_field_status' => ContentModelField::STAT_DELETED,
			),
			'content_model_field_id=:content_model_field_id',
			array(':content_model_field_id'=>$id)
		);
		//更新缓存
		ContentModelField::update_cache();
			
		//记录操作日志
		$user = Yii::app()->user;
		$message = '{user_name}删除了内容模型字段{field_name}信息';
		$data = array(
			'user_id' => $user->id,
			'user_name' => $user->name,
			'field_name' => $model_field['content_model_field_name'],
			'data' => array('content_model_field_id'=>$id),
		);
		AdminLogs::add($user->id, 'Content/Model/Field', $id, 'Delete', 'success', $message, $data);
		
		if(!isset($_GET['ajax'])) {
			$this->redirect[] = array(
				'text' => '',
				'href' => $this->forward,
			);
			$this->message('模型字段删除成功', self::MSG_SUCCESS, true);
		} else {
			
			exit;
		}
	}
	
	/**
	 * 
	 * @param unknown_type $content_model_id
	 */
	public function actionCheckIdentify($content_model_id, $identify, $id = null)
	{
		$sql = "SELECT * FROM {{content_model_fields}}
			WHERE content_model_id=:content_model_id AND content_model_field_identify=:content_model_field_identify AND content_model_field_status=:content_model_field_status";
		$__addons = array(
			'AND',
			'content_model_id=:content_model_id',
			array('AND', 'content_model_field_identify=:content_model_field_identify'),
			array('AND', 'content_model_field_status>:content_model_field_status'),
		);
		$__params = array(
			':content_model_id' => $content_model_id,
			':content_model_field_identify' => $identify,
			':content_model_field_status' => ContentModelField::STAT_DELETED,
		);
		if($id !== NULL) {
			$__addons[] = array('AND', 'content_model_field_id<>:content_model_field_id');
			$__params[':content_model_field_id'] = $id;
		}

		$count = Yii::app()->db->createCommand()->select('COUNT(content_model_field_id)')
			->from('{{content_model_fields}}')
			->where($__addons, $__params)
			->queryScalar();
		if($count > 0) {
			$_r = array(
				'ok' => false,
				'reason' => '字段标识已存在！',
			);
		} else {
			$_r = array(
				'ok' => true,
				'reason' => '',
			);
		}
		
		echo json_encode($_r);
		exit;
	}
	
	/**
	 * 
	 * @param unknown_type $content_model_id
	 */
	public function actionIndex($content_model_id)
	{
		if($_SERVER['REQUEST_METHOD']=='POST') {
			//保存修改
			if(!is_array($_POST['Field']['content_model_field_rank'])) $_POST['Field']['content_model_field_rank'] = array();
			foreach($_POST['Field']['content_model_field_rank'] as $_k=>$_v) {
				$flag = Yii::app()->db->createCommand()->update('{{content_model_fields}}',
					array(
						'content_model_field_rank' => ($_POST['Field']['content_model_field_rank'][$_k]) ? intval($_POST['Field']['content_model_field_rank'][$_k]) : 255,
						//'content_model_field_lasttime' => $_SERVER['REQUEST_TIME'],
					),
					'content_model_field_id=:content_model_field_id',
					array(':content_model_field_id'=>$_k)
				);
				if($flag) {
					//记录操作日志
					$message = '{user_name}修改了内容模型({model_name})字段({field_name})排序';
					$data = array(
						'user_id' => Yii::app()->user->id,
						'user_name' => Yii::app()->user->name,
						'model_name' => ContentModel::get_model_name_by_id($content_model_id),
						'field_name' => ContentModelField::get_field_name_by_id($_v),
						'addons_data' => $_POST,
					);
					AdminLogs::add(Yii::app()->user->id, 'Content/Model/Field', $_k, 'Modify', 'success', $message, $data);
				}
			}
			ContentModelField::update_cache();
			
			//$this->refresh();
			
			$this->redirect[] = array(
				'text' => '',
				'href' => url($this->module->id. "/Content/Model/{$content_model_id}/Field/Index"),
			);
			$this->message('修改字段排序完成', self::MSG_SUCCESS, true);
		}
		
		$fields = ContentModelField::get_fields_by_cache($content_model_id);
		
		$this->render('index',array(
			'fields' => $fields,
			"content_model_id" => $content_model_id,
		));
	}
}

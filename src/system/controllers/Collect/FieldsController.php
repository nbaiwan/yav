<?php

class FieldsController extends SysController
{
	public function actionIndex($collect_model_id)
	{
		$this->render('index',array(
			'fields' => CollectModelField::Pages(
				array(
					'allow_cache' => false,
					"collect_model_id" => $collect_model_id,
					//'pagesize' => 1,
				)
			),
			"collect_model_id" => $collect_model_id
		));
	}
	
	public function actionRank($id=null){
		$id = $_POST['fields_id'];
		$rank = $_POST['rank'];
		$ids = is_array($id) ? $id : array($id);
		$ids = array_filter($ids);
		$_sql_in = '';
		$_sql_param = array();
		if(!count($ids)){
			$this->redirect[] = array(
				'text' => '',
				//'href' => url($this->module->id . '/Collect/List/Index'),
				'href' => $_SERVER['HTTP_REFERER'],
			);
			$this->message('请选择至少一项进行操作！', self::MSG_SUCCESS, true);
		}
		foreach($ids as $id) {
			$r = intval($rank[$id]);
			$_sql = "UPDATE {{collect_model_fields}} SET `collect_fields_rank`={$r} WHERE collect_fields_id = ({$id})";
			$_cmd = Yii::app()->db->createCommand($_sql);
			$_cmd->execute($_sql_param);
		}
		
		
		if(!isset($_GET['ajax'])) {
			$this->redirect[] = array(
				'text' => '',
				//'href' => url($this->module->id . '/Collect/List/Index'),
				'href' => $_SERVER['HTTP_REFERER'],
			);
			$this->message('排序成功', self::MSG_SUCCESS, true);
		}
	}
	
	public function actionUpdate($collect_model_id, $id)
	{
		$current_collect = CollectModel::get_model_by_id($collect_model_id);//当前采集模型
		if($_SERVER['REQUEST_METHOD'] == 'POST') {
			if(!isset($_POST['Fields']) || !is_array($_POST['Fields'])) {
				$this->redirect[] = array(
					'text' => '',
					'href' => 'javascript:history.go(-1);',
				);
				$this->message('保存采集字段信息错误', self::MSG_ERROR, true);
			}
			
			if($_POST['Fields']['collect_fields_name'] =='') {
				$this->redirect[] = array(
					'text' => '',
					'href' => 'javascript:history.go(-1);',
				);
				$this->message('采集字段名称不能为空', self::MSG_ERROR, true);
			}
			
			if($_POST['Fields']['collect_fields_identify'] =='') {
				$this->redirect[] = array(
					'text' => '',
					'href' => 'javascript:history.go(-1);',
				);
				$this->message('采集字段标识不能为空', self::MSG_ERROR, true);
			}
			
			/*
			if($_POST['Fields']['collect_fields_belong'] =='') {
				$this->redirect[] = array(
					'text' => '',
					'href' => 'javascript:history.go(-1);',
				);
				$this->message('字段归属不能为空', self::MSG_ERROR, true);
			}*/
			
			if(!preg_match("/^[a-z]([a-z\d\_]+?)$/", $_POST['Fields']['collect_fields_identify'])) {
				$this->redirect[] = array(
					'text' => '',
					'href' => 'javascript:history.go(-1);',
				);
				$this->message('采集字段标识必须是以字母开头的字母、数字、下划线', self::MSG_ERROR, true);
			}
			
			$old_collect_model_field = CollectModelField::get_field_by_id($id);
			$sql = "SELECT `collect_fields_identify` FROM {{collect_model_fields}} WHERE `collect_fields_id`!=:collect_fields_id AND collect_fields_identify=:collect_fields_identify  AND `collect_model_id`=:collect_model_id AND `collect_fields_status`!=:collect_fields_status";
			$cmd = Yii::app()->db->createCommand($sql);
			$cmd->execute(array(':collect_fields_id'=>$id, ':collect_model_id'=>$collect_model_id, ':collect_fields_status'=>CollectModelField::STAT_STATUS_DELETED, ':collect_fields_identify'=>$_POST['Fields']['collect_fields_identify']));
			
			if($cmd->queryScalar()) {
				$this->redirect[] = array(
					'text' => '',
					'href' => 'javascript:history.go(-1);',
				);
				$this->message('采集字段标识不能重复', self::MSG_ERROR, true);
			}
			
			$flag = Yii::app()->db->createCommand()->update('{{collect_model_fields}}',
				array(
					'collect_fields_name' => $_POST['Fields']['collect_fields_name'],
					'collect_fields_identify' => $_POST['Fields']['collect_fields_identify'],
					'collect_fields_type' => $_POST['Fields']['collect_fields_type'],
					'collect_fields_belong' => $_POST['Fields']['collect_fields_belong'],
					'collect_fields_rank' => $_POST['Fields']['collect_fields_rank'] ? intval($_POST['Fields']['collect_fields_rank']) : 255,
					'collect_fields_lasttime' => $_SERVER['REQUEST_TIME'],
					'content_model_field_id' => $_POST['Fields']['content_model_field_id'],
				),
				'collect_fields_id=:collect_fields_id',
				array(':collect_fields_id'=>$id)
			);
			
			$collect_model_identify = CollectModel::get_model_identify_by_id($collect_model_id);
			if($_POST['Fields']['collect_fields_type'] == 1){//单行文本
				$f = " VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ;";
			}
			if($_POST['Fields']['collect_fields_type'] == 2){//多行文本
				$f = " TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ;";
			}
			if($_POST['Fields']['collect_fields_type'] == 3){//日期时间
				$f = " DATETIME NOT NULL ;";
			}
			$sql = "ALTER TABLE `collect_model_addons{$collect_model_identify}` CHANGE  `".$old_collect_model_field['collect_fields_identify']."` `".$_POST['Fields']['collect_fields_identify']."`".$f;
			Yii::app()->db->createCommand($sql)->execute();
			if($flag) {
				
				$collect_fields_id = Yii::app()->db->getLastInsertID();
				$collect_fields_name = $_POST['Fields']['collect_fields_name'];
				//更新缓存
				CollectModelField::update_cache();
				
				//记录操作日志
				$user = Yii::app()->user;
				$message = '{user_name}修改了采集字段{collect_fields_name}';
				$data = array(
					'user_id' => $user->id,
					'user_name' => $user->name,
					'collect_fields_name' => $collect_fields_name,
					'addons_data' => array('collect_fields_id'=>$collect_fields_id),
				);
				AdminLogs::add($user->id, 'Collect/Model/Fields', $collect_fields_id, 'Modify', 'success', $message, $data);
				
				if(!isset($_GET['ajax'])) {
					$this->redirect[] = array(
						'text' => '',
						'href' => url($this->module->id. "/Collect/Model/{$collect_model_id}/Fields/Index"),
					);
					$this->message('保存采集字段成功', self::MSG_SUCCESS, true);
				}
			} else {
				//记录操作日志
				$user = Yii::app()->user;
				$message = '{user_name}修改采集字段({collect_fields_name})信息失败';
				$data = array(
					'user_id' => $user->id,
					'user_name' => $user->name,
					'collect_fields_name' => $collect_fields_name,
					'addons_data' => array('fields'=>$_POST['Fields']),
				);
				AdminLogs::add($user->id, 'Collect/Model/Fields', $collect_fields_id, 'Modify', 'failure', $message, $data);
				
				$this->redirect[] = array(
					'text' => '',
					'href' => 'javascript:history.go(-1);',
				);
				$this->message('保存采集字段信息失败', self::MSG_ERROR, true);
			}
		}
		
		$field = CollectModelField::get_field_by_id($id, false);
		if(empty($field)) {
			$this->redirect[] = array(
				'text' => '',
				'href' => url($this->module->id . "/Collect/Model/{$collect_model_id}/Fields/Index"),
			);
			$this->message('采集字段不存在或已被删除', self::MSG_ERROR, true);
		}
		
		$this->render('update',
			array(
				'field' => $field,
				'field_types' => CollectModelField::get_field_types(),
				'content_model_fields' => ContentModelField::get_fields_by_cache($current_collect['content_model_id']),
				"collect_model_id" => $collect_model_id,
			)
		);
	}
	public function actionCreate($collect_model_id)
	{
		$model = CollectModel::get_model_by_id($collect_model_id);//当前采集模型
		if($_SERVER['REQUEST_METHOD'] == 'POST') {
			if(!isset($_POST['Fields']) || !is_array($_POST['Fields'])) {
				$this->redirect[] = array(
					'text' => '',
					'href' => 'javascript:history.go(-1);',
				);
				$this->message('保存采集字段信息错误', self::MSG_ERROR, true);
			}
			
			if($_POST['Fields']['collect_fields_name'] =='') {
				$this->redirect[] = array(
					'text' => '',
					'href' => 'javascript:history.go(-1);',
				);
				$this->message('采集字段名称不能为空', self::MSG_ERROR, true);
			}
			
			if($_POST['Fields']['collect_fields_type'] =='') {
				$this->redirect[] = array(
					'text' => '',
					'href' => 'javascript:history.go(-1);',
				);
				$this->message('采集字段类型不能为空', self::MSG_ERROR, true);
			}
			
			/*
			if($_POST['Fields']['collect_fields_belong'] =='') {
				$this->redirect[] = array(
					'text' => '',
					'href' => 'javascript:history.go(-1);',
				);
				$this->message('字段归属不能为空', self::MSG_ERROR, true);
			}*/
			
			if($_POST['Fields']['collect_fields_identify'] =='') {
				$this->redirect[] = array(
					'text' => '',
					'href' => 'javascript:history.go(-1);',
				);
				$this->message('采集字段标识不能为空', self::MSG_ERROR, true);
			}
			
			/*
			if($_POST['Fields']['collect_fields_identify'] =='collect_content_id') {
				$this->redirect[] = array(
					'text' => '',
					'href' => 'javascript:history.go(-1);',
				);
				$this->message('采集字段标识已存在', self::MSG_ERROR, true);
			}*/
			
			if(!preg_match("/^[a-z]([a-z\d\_]+?)$/", $_POST['Fields']['collect_fields_identify'])) {
				$this->redirect[] = array(
					'text' => '',
					'href' => 'javascript:history.go(-1);',
				);
				$this->message('采集字段标识必须是以字母开头的字母、数字、下划线', self::MSG_ERROR, true);
			}
			
			if(CollectModelField::get_fields_id_by_identify($collect_model_id,$_POST['Fields']['collect_fields_identify'])) {
				$this->redirect[] = array(
					'text' => '',
					'href' => 'javascript:history.go(-1);',
				);
				$this->message('采集字段标识重复', self::MSG_ERROR, true);
			}
			
			$flag = Yii::app()->db->createCommand()->insert('{{collect_model_fields}}',
				array(
					'collect_fields_id' => 0,
					'collect_model_id' => $collect_model_id,
					'collect_fields_name' => $_POST['Fields']['collect_fields_name'],
					'collect_fields_identify' => $_POST['Fields']['collect_fields_identify'],
					'collect_fields_status' => CollectModelField::STAT_STATUS_NORMAL,
					'collect_fields_type' => $_POST['Fields']['collect_fields_type'],
					'content_model_field_id' => $_POST['Fields']['content_model_field_id'],
					//'collect_fields_belong' => $_POST['Fields']['collect_fields_belong'],
					'collect_fields_rank' => $_POST['Fields']['collect_fields_rank'],
					'collect_fields_lasttime' => $_SERVER['REQUEST_TIME'],
					'collect_fields_dateline' => $_SERVER['REQUEST_TIME'],
				)
			);
			
			$collect_model_identify = CollectModel::get_model_identify_by_id($collect_model_id);
			
			if($_POST['Fields']['collect_fields_type'] == 1){//单行文本
				$f = " VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ;";
			}
			
			if($_POST['Fields']['collect_fields_type'] == 2){//多行文本
				$f = " TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ;";
			}
			
			if($_POST['Fields']['collect_fields_type'] == 3){//日期时间
				$f = " DATETIME NOT NULL ;";
			}
			
			$sql = "ALTER TABLE `collect_model_addons{$collect_model_identify}` ADD `".$_POST['Fields']['collect_fields_identify']."` ".$f;
			Yii::app()->db->createCommand($sql)->execute();
			if($flag) {
				$collect_fields_id = Yii::app()->db->getLastInsertID();
				$collect_fields_name = $_POST['Fields']['collect_fields_name'];
	
				
				//记录操作日志
				$user = Yii::app()->user;
				$message = '{user_name}添加采集字段{collect_fields_name}';
				$data = array(
					'user_id' => $user->id,
					'user_name' => $user->name,
					'collect_fields_name' => $collect_fields_name,
					'addons_data' => array('collect_fields_id'=>$collect_fields_id),
				);
				AdminLogs::add($user->id, 'Collect/Model/Fields', $collect_fields_id, 'Insert', 'success', $message, $data);
				
				if(!isset($_GET['ajax'])) {
					$this->redirect[] = array(
						'text' => '',
						'href' => url($this->module->id. "/Collect/Model/{$collect_model_id}/Fields/Index"),
					);
					$this->message('添加采集字段完成', self::MSG_SUCCESS, true);
				}
			} else {
				//记录操作日志
				$user = Yii::app()->user;
				$message = '{user_name}添加采集字段{collect_fields_name}失败';
				$data = array(
					'user_id' => $user->id,
					'user_name' => $user->name,
					'collect_fields_name' => $_POST['Fields']['collect_fields_name'],
					'addons_data' => array('fields'=>$_POST['Fields']),
				);
				AdminLogs::add($user->id, 'Collect/Model/Fields', 0, 'Insert', 'failure', $message, $data);
				
				$this->redirect[] = array(
					'text' => '',
					'href' => 'javascript:history.go(-1);',
				);
				$this->message('添加采集字段失败', self::MSG_ERROR, true);
			}
		}
		
		$field = array(
			'collect_fields_id' => 0,
			'collect_fields_name' => '',
			'collect_fields_identify' => '',
			'collect_fields_rank' => 255,
		);
		
		$this->render('create',
			array(
				'field' => $field,
				'field_types' => CollectModelField::get_field_types(),
				'content_model_fields' => ContentModelField::get_fields_by_cache($model['content_model_id']),
				"collect_model_id" => $collect_model_id,
			)
		);
	}
	
	public function actionDelete($collect_model_id, $id)
	{
		$collect_fields_name = CollectModelField::get_fields_name_by_id($id);
		$collect_model_identify = CollectModel::get_model_identify_by_id($collect_model_id);
		$collect_fields = CollectModelField::get_fields_identify_by_id($collect_model_id, $id);
		$sql = "ALTER TABLE `collect_model_addons{$collect_model_identify}` DROP  `".$collect_fields['collect_fields_identify']."`";
		Yii::app()->db->createCommand($sql)->execute();
		Yii::app()->db->createCommand()->update('{{collect_model_fields}}',
			array(
				'collect_fields_status' => CollectModelField::STAT_DELETED,
			),
			'collect_fields_id=:collect_fields_id',
			array(':collect_fields_id'=>$id)
		);
		
		CollectModelField::update_cache();
			
		//记录操作日志
		$user = Yii::app()->user;
		$message = '{user_name}删除了采集字段{collect_fields_name}';
		$data = array(
			'user_id' => $user->id,
			'user_name' => $user->name,
			'collect_fields_name' => $collect_fields_name,
			'data' => array('collect_fields_id'=>$id),
		);
		AdminLogs::add($user->id, 'Collect/Fields', $id, 'Delete', 'success', $message, $data);
		
		if(!isset($_GET['ajax'])) {
			$this->redirect(array("Collect/Model/{$collect_model_id}/Fields/Index"));
		}
	}
}

<?php

class Collect_TemplateController extends SysController {
	public function indexAction() {
		$model_arr = CollectModelModel::inst()->getModelsByCache();
		$source_arr = CollectSourceModel::inst()->getSourcesByCache();
		$collect_source_id = isset($_GET['collect_source_id']) ? intval($_GET['collect_source_id']) : null;
		$collect_model_id = isset($_GET['collect_model_id']) ? intval($_GET['collect_model_id']) : null;
		$searchKey = isset($_GET['searchKey']) ? trim($_GET['searchKey']) : null;
		$this->getView()->assign(
            array(
                'templates' => CollectTemplateModel::inst()->Pages(
                    array(
                        'allow_cache' => false,
                        'collect_model_id' => $collect_model_id,
                        'collect_source_id' => $collect_source_id,
                        'searchKey' => $searchKey,
                    )
                ),
                'model_arr' => $model_arr,
                'source_arr' => $source_arr,
            )
        );
	}
	
	public function updateAction($id) {
		if($_SERVER['REQUEST_METHOD'] == 'POST') {
			if(!isset($_POST['Template']) || !is_array($_POST['Template'])) {
				$this->redirect[] = array(
					'text' => '',
					'href' => 'javascript:history.go(-1);',
				);
				$this->message('保存采集模板信息错误', self::MSG_ERROR, true);
			}
			
			if($_POST['Template']['collect_template_name'] =='') {
				$this->redirect[] = array(
					'text' => '',
					'href' => 'javascript:history.go(-1);',
				);
				$this->message('采集模板名称不能为空', self::MSG_ERROR, true);
			}
			
			if($_POST['Template']['collect_source_id'] =='') {
				$this->redirect[] = array(
					'text' => '',
					'href' => 'javascript:history.go(-1);',
				);
				$this->message('请选择一个采集来源', self::MSG_ERROR, true);
			}
			/*if($_POST['Template']['collect_template_charset'] =='') {
				$this->redirect[] = array(
					'text' => '',
					'href' => 'javascript:history.go(-1);',
				);
				$this->message('请选择一个编码', self::MSG_ERROR, true);
			}*/
			$old_collect_model_id = CollectTemplateModel::inst()->getModelIdById($id);
			if($_POST['Template']['collect_model_id'] =='') {
				$this->redirect[] = array(
					'text' => '',
					'href' => 'javascript:history.go(-1);',
				);
				$this->message('请选择一个采集模型', self::MSG_ERROR, true);
			}
			$flag = $this->db->update('{{collect_template}}',
				array(
					'collect_template_name' => $_POST['Template']['collect_template_name'],
					'collect_source_id' => $_POST['Template']['collect_source_id'],
					'collect_model_id' => $_POST['Template']['collect_model_id'],
					//'collect_template_charset' => $_POST['Template']['collect_template_charset'],
					'collect_template_remark' => $_POST['Template']['collect_template_remark'],
					
					'collect_template_status' => CollectTemplateModel::STAT_STATUS_NORMAL,
					'collect_template_rank' => $_POST['Template']['collect_template_rank'] ? intval($_POST['Template']['collect_template_rank']) : 255,
					'collect_template_lasttime' => $_SERVER['REQUEST_TIME'],
					'update_user_id' => $this->user->id
				),
				'collect_template_id=:collect_template_id',
				array(':collect_template_id'=>$id)
			);
			
			if($flag) {
				$collect_template_id = $id;
				$collect_template_name = $_POST['Template']['collect_template_name'];
				
				//更新缓存
				CollectTemplateModel::inst()->updateCache();
				
				//记录操作日志
				
				$message = '{user_name}修改了采集模板{collect_template_name}';
				$data = array(
					'collect_template_name' => $collect_template_name,
					'addons_data' => array('collect_template_id'=>$collect_template_id),
				);
				UserLogsModel::inst()->add('Collect/Template', $collect_template_id, 'Insert', 'success', $message, $data);
				
				if(!isset($_GET['ajax'])) {
					$this->redirect[] = array(
						'text' => '',
						'href' => '/collect_template/index',
					);
					$this->message('保存采集模板成功', self::MSG_SUCCESS, true);
				}
			} else {
				//记录操作日志
				$message = '{user_name}修改采集模板({collect_template_name})信息失败';
				$data = array(
					'collect_template_name' => $collect_template_name,
					'addons_data' => array('template'=>$_POST['Template']),
				);
				UserLogsModel::inst()->add('Collect/Template', $collect_template_id, 'Modify', 'failure', $message, $data);
				
				$this->redirect[] = array(
					'text' => '',
					'href' => 'javascript:history.go(-1);',
				);
				$this->message('保存采集模板信息失败', self::MSG_ERROR, true);
			}
		}
		
		$template = CollectTemplateModel::inst()->getTemplateById($id,false);
		if(empty($template)) {
			$this->redirect[] = array(
				'text' => '',
				'href' => '/collect_template/index',
			);
			$this->message('采集模板不存在或已被删除', self::MSG_ERROR, true);
		}
		//print_r($template);
		$sql = "SELECT `collect_model_id`,`collect_model_name` FROM `{{collect_model}}` WHERE `collect_model_status`!=:collect_model_status";
        $params = array(
            ':collect_model_status' => CollectModelModel::STAT_STATUS_DELETED,
        );
		$model_arr = $this->db->queryAll($sql, $params);
		
		$sql = "SELECT `collect_source_id`,`collect_source_name` FROM `{{collect_source}}` WHERE `collect_source_status`!=:collect_source_status";
		$params = array(
            ':collect_source_status' => CollectSourceModel::STAT_STATUS_DELETED,
        );
		$source_arr = $this->db->queryAll($sql, $params);
		$_charset = CollectTaskModel::getCharsets();
		$template['charset'] = $_charset;
		$template['model_arr'] = $model_arr;
		$template['source_arr'] = $source_arr;
		
		$this->getView()->assign(
			array(
				'template' => $template,
			)
		);
	}
	
	public function createAction() {
		if($_SERVER['REQUEST_METHOD'] == 'POST') {
			if(!isset($_POST['Template']) || !is_array($_POST['Template'])) {
				$this->redirect[] = array(
					'text' => '',
					'href' => 'javascript:history.go(-1);',
				);
				$this->message('保存采集模板信息错误', self::MSG_ERROR, true);
			}
			
			if($_POST['Template']['collect_template_name'] =='') {
				$this->redirect[] = array(
					'text' => '',
					'href' => 'javascript:history.go(-1);',
				);
				$this->message('采集模板名称不能为空', self::MSG_ERROR, true);
			}
			
			if($_POST['Template']['collect_source_id'] =='') {
				$this->redirect[] = array(
					'text' => '',
					'href' => 'javascript:history.go(-1);',
				);
				$this->message('请选择一个采集来源', self::MSG_ERROR, true);
			}
			/*if($_POST['Template']['collect_template_charset'] =='') {
				$this->redirect[] = array(
					'text' => '',
					'href' => 'javascript:history.go(-1);',
				);
				$this->message('请选择一个编码', self::MSG_ERROR, true);
			}*/
			if($_POST['Template']['collect_model_id'] =='') {
				$this->redirect[] = array(
					'text' => '',
					'href' => 'javascript:history.go(-1);',
				);
				$this->message('请选择一个采集模型', self::MSG_ERROR, true);
			}
			$flag = $this->db->insert('{{collect_template}}',
				array(
					'collect_template_id' => 0,
					'collect_template_name' => $_POST['Template']['collect_template_name'],
					'collect_source_id' => $_POST['Template']['collect_source_id'],
					'collect_model_id' => $_POST['Template']['collect_model_id'],
					'collect_template_remark' => $_POST['Template']['collect_template_remark'],
					'collect_template_list_rules' => json_encode(array()),
					'collect_template_addons_rules' => json_encode(array()),
					'collect_template_rank' => $_POST['Template']['collect_template_rank'],
					'collect_template_status' => CollectTemplateModel::STAT_STATUS_NORMAL,
					'collect_template_lasttime' => $_SERVER['REQUEST_TIME'],
					'collect_template_dateline' => $_SERVER['REQUEST_TIME'],
					'update_user_id' => $this->user->id,
					'insert_user_id' => $this->user->id
				)
			);
			
			if($flag) {
				$collect_template_id = $this->db->getLastInsertID();
				$collect_template_name = $_POST['Template']['collect_template_name'];
	
				/*$sql = "SELECT `collect_fields_id` FROM {{collect_model_fields}} WHERE `collect_model_id`=:collect_model_id AND `collect_fields_status`!=:collect_fields_status";
				$cmd = $this->db->createCommand($sql);
				$cmd->execute(array(':collect_fields_status'=>CollectModelField::STAT_STATUS_DELETED,":collect_model_id"=>$_POST['Template']['collect_model_id']));
				$fields_arr = $cmd->queryAll();
				if(is_array($fields_arr) && count($fields_arr)){
					foreach($fields_arr as $k=>$v){
						$this->db->createCommand()->insert('{{collect_template2fields}}',
							array(
								'collect_fields_id' => $v['collect_fields_id'],
								'collect_template_id' => $collect_template_id,
								'collect_template_rule_status' => CollectTemplateModel::STAT_STATUS_NORMAL,
							)
						);
					}
				}*/
				//记录操作日志
				
				$message = '{user_name}添加采集模板{collect_template_name}';
				$data = array(
					'collect_template_name' => $collect_template_name,
					'data' => array('collect_template_id'=>$collect_template_id),
				);
				UserLogsModel::inst()->add('Collect/Template', $collect_template_id, 'Insert', 'success', $message, $data);
				
				if(!isset($_GET['ajax'])) {
					$this->redirect[] = array(
						'text' => '',
						'href' => '/collect_template/'.$collect_template_id.'/rule',
					);
					$this->message('添加采集模板完成，请添加规则', self::MSG_SUCCESS, true);
				}
			} else {
				//记录操作日志
				$message = '{user_name}添加采集模板{collect_template_name}失败';
				$data = array(
					'collect_template_name' => $collect_template_name,
					'data' => array('template'=>$_POST['Template']),
				);
				UserLogsModel::inst()->add('Collect/Template', $collect_template_id, 'Insert', 'failure', $message, $data);
				
				$this->redirect[] = array(
					'text' => '',
					'href' => 'javascript:history.go(-1);',
				);
				$this->message('添加采集模板失败', self::MSG_ERROR, true);
			}
		}
		$sql = "SELECT `collect_model_id`,`collect_model_name` FROM `{{collect_model}}` WHERE `collect_model_status`!=:collect_model_status";
		$params = array(
            ':collect_model_status' => CollectModelModel::STAT_STATUS_DELETED,
        );
		$model_arr = $this->db->queryAll($sql, $params);
		
		$sql = "SELECT `collect_source_id`,`collect_source_name` FROM `{{collect_source}}` WHERE `collect_source_status`!=:collect_source_status";
		$params = array(
            ':collect_source_status' => CollectSourceModel::STAT_STATUS_DELETED
        );
		$source_arr = $this->db->queryAll($sql, $params);
		$charset = CollectTaskModel::getCharsets();
        
		$template = array(
            'collect_source_id' => 0,
            'collect_model_id' => 0,
            'collect_template_name' => '',
            'collect_template_remark' => '',
			'model_arr' => $model_arr,
			'source_arr' => $source_arr,
			'charset' => $charset,
            'collect_template_rank' => 255,
		);
		
		$this->getView()->assign(
			array(
				'template' => $template,
			)
		);
	}
	
	public function deleteAction($id) {
		$collect_template_name = CollectTemplateModel::inst()->getTemplateNameById($id);
		$this->db->update('{{collect_template}}',
			array(
				'collect_template_status' => CollectTemplateModel::STAT_STATUS_DELETED,
			),
			'collect_template_id=:collect_template_id',
			array(':collect_template_id'=>$id)
		);
		
		CollectTemplateModel::inst()->updateCache();
			
		//记录操作日志
		$message = '{user_name}删除了采集模板{collect_template_name}';
		$data = array(
			'collect_template_name' => $collect_template_name,
			'data' => array('collect_template_id'=>$id),
		);
		UserLogsModel::inst()->add('Collect/Template', $id, 'Delete', 'success', $message, $data);
		
		if(!isset($_GET['ajax'])) {
			$this->redirect('/collect_template/index');
		}
	}
	
	public function ruleAction($id) {
		if($_SERVER['REQUEST_METHOD'] == 'POST') {
			$template_fields = array();
			if($_POST['Rule']){
				$this->db->update('{{collect_template}}',
					array(
						'collect_template_list_rules' => json_encode($_POST['Rule']['List']),
						'collect_template_addons_rules' => json_encode($_POST['Rule']['Addons']),
					),
					'collect_template_id=:collect_template_id',
					array(':collect_template_id'=>intval($id))
				);
			}
			
			if(!isset($_GET['ajax'])) {
				$this->redirect[] = array(
					'text' => '',
					'href' => '/collect_template/index',
				);
				$this->message('修改模板规则完成', self::MSG_SUCCESS, true);
			}
		}
		
		$template = CollectTemplateModel::inst()->getTemplateById($id);
		
		$fields = CollectModelFieldModel::inst()->getFieldsByModelId($template['collect_model_id']);
		
		foreach($fields as $_k=>$_v) {
			if(isset($template['collect_template_addons_rules'][$_v['collect_fields_identify']])) {
				$template['collect_template_addons_rules'][$_v['collect_fields_identify']] += $_v;
			} else {
				$rules['collect_template_addons_rules'][$_v['collect_fields_identify']] = $_v;
			}
		}
		unset($fields);
		
		$this->getView()->assign(
			array(
				'template' => $template,
			)
		);
	}
	
	public function loadFieldsAction($id) {
		$sql = "SELECT cf.`collect_fields_id`,cf.`collect_fields_name` FROM {{collect_template}} ct
			INNER JOIN {{collect_model_fields}} cf ON cf.collect_model_id=ct.collect_model_id
			WHERE cf.`collect_fields_status`!=:collect_fields_status AND ct.`collect_template_id`=:collect_template_id";
		$params = array(
            ':collect_fields_status' => CollectModelFieldModel::STAT_STATUS_DELETED,
            ":collect_template_id" => $id
        );
		$fields_arr = $this->db->queryAll($sql, $params);
		$re = "";
		foreach($fields_arr as $v){
			$re .= "[".$v['collect_fields_name']."] ";
		}
		echo $re;
	}
	
	public function loadCharsetAction($id) {
		$template = CollectTemplateModel::getTemplateById($id,false);
		$_charset = CollectTaskModel::getCharset();
		echo $_charset[$template['collect_template_charset']];
	}
	
	public function loadBoxAction() {
	}
}

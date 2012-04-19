<?php

class Collect_RuleController extends SysController {
	public function indexAction() {
		$this->getView()->assign(
            array(
                'rule' => CollectRuleModel::inst()->Pages(
                    array(
                        'allow_cache' => false,
                        //'pagesize' => 1,
                    )
                ),
            )
        );
	}
	
	public function updateAction($id, $page = null) {
		if($_SERVER['REQUEST_METHOD'] == 'POST') {
			if(!isset($_POST['Rule']) || !is_array($_POST['Rule'])) {
				$this->redirect[] = array(
					'text' => '',
					'href' => 'javascript:history.go(-1);',
				);
				$this->message('保存采集模板信息错误', self::MSG_ERROR, true);
			}
			
			if($_POST['Rule']['collect_rule_name'] =='') {
				$this->redirect[] = array(
					'text' => '',
					'href' => 'javascript:history.go(-1);',
				);
				$this->message('采集模板名称不能为空', self::MSG_ERROR, true);
			}
			
			if($_POST['Rule']['collect_source_id'] =='') {
				$this->redirect[] = array(
					'text' => '',
					'href' => 'javascript:history.go(-1);',
				);
				$this->message('请选择一个采集来源', self::MSG_ERROR, true);
			}
			$old_collect_model_id = CollectRule::get_model_id_by_id($id);
			if($_POST['Rule']['collect_model_id'] =='') {
				$this->redirect[] = array(
					'text' => '',
					'href' => 'javascript:history.go(-1);',
				);
				$this->message('请选择一个采集模型', self::MSG_ERROR, true);
			}
			
			$flag = $this->db->update('{{collect_rule}}',
				array(
					'collect_rule_name' => $_POST['Rule']['collect_rule_name'],
					'collect_source_id' => $_POST['Rule']['collect_source_id'],
					'collect_model_id' => $_POST['Rule']['collect_model_id'],
					'collect_rule_remark' => $_POST['Rule']['collect_rule_remark'],
					'collect_rule_status' => CollectRule::STAT_NORMAL,
					'collect_rule_rank' => $_POST['Rule']['collect_rule_rank'] ? intval($_POST['Rule']['collect_rule_rank']) : 255,
					'collect_rule_lasttime' => $_SERVER['REQUEST_TIME'],
				),
				'collect_rule_id=:collect_rule_id',
				array(':collect_rule_id'=>$id)
			);
			
			if($flag) {
				$collect_rule_id = $id;
				$collect_rule_name = $_POST['Rule']['collect_rule_name'];
				if($old_collect_model_id != $_POST['Rule']['collect_model_id']){
					$this->db->update('{{collect_rule2fields}}',
						array(
							'collect_rule_rule_status' => CollectRuleModel::STAT_DELETED,
						),
						'collect_rule_id=:collect_rule_id',
						array(':collect_rule_id'=>$id)
					);
					
					CollectRuleModel::inst()->updateCache();
					$sql = "SELECT `collect_fields_id` FROM `collect_fields` WHERE `collect_model_id`=:collect_model_id AND `collect_fields_status`!=:collect_fields_status";
					$params = array(
                        ':collect_fields_status' => CollectFields::STAT_DELETED,
                        ":collect_model_id" => $_POST['Rule']['collect_model_id']
                    );
					$fields_arr = $this->db->queryAll($sql, $params);
					if(is_array($fields_arr) && count($fields_arr)){
						foreach($fields_arr as $k=>$v){
							$this->db->insert('{{collect_rule2fields}}',
								array(
									'collect_fields_id' => $v['collect_fields_id'],
									'collect_rule_id' => $collect_rule_id,
									'collect_rule_rule_status' => CollectRuleModel::STAT_NORMAL,
								)
							);
						}
					}
				}
				//更新缓存
				CollectRuleModel::inst()->updateCache();
				
				//记录操作日志
				$message = '{user_name}修改了采集模板{collect_rule_name}';
				$data = array(
					'collect_rule_name' => $collect_rule_name,
					'addons_data' => array('collect_rule_id'=>$collect_rule_id),
				);
				AdminLogs::add('Collect/Rule', $collect_rule_id, 'Insert', 'success', $message, $data);
				
				if(!isset($_GET['ajax'])) {
					$this->redirect[] = array(
						'text' => '',
						'href' => '/collect_rule/Index',
					);
					$this->message('保存采集模板成功', self::MSG_SUCCESS, true);
				}
			} else {
				//记录操作日志
				$message = '{user_name}修改采集模板({collect_rule_name})信息失败';
				$data = array(
					'collect_rule_name' => $collect_rule_name,
					'addons_data' => array('rule'=>$_POST['Rule']),
				);
				AdminLogs::add('Collect/Rule', $collect_rule_id, 'Modify', 'failure', $message, $data);
				
				$this->redirect[] = array(
					'text' => '',
					'href' => 'javascript:history.go(-1);',
				);
				$this->message('保存采集模板信息失败', self::MSG_ERROR, true);
			}
		}
		
		$rule = CollectRuleModel::inst()->getRuleById($id, false);
		if(empty($rule)) {
			$this->redirect[] = array(
				'text' => '',
				'href' => '/collect_rule/index',
			);
			$this->message('采集模板不存在或已被删除', self::MSG_ERROR, true);
		}
		//print_r($rule);
		$sql = "SELECT `collect_model_id`,`collect_model_name` FROM `collect_model` WHERE `collect_model_status`!=:collect_model_status";
		$params = array(
            ':collect_model_status' => CollectModelModel::STAT_DELETED,
        );
		$model_arr = $this->db->queryAll($sql, $params);
		
		$sql = "SELECT `collect_source_id`,`collect_source_name` FROM `collect_source` WHERE `collect_source_status`!=:collect_source_status";
		$params = array(
            ':collect_source_status' => CollectSourceModel::STAT_DELETED,
        );
		$source_arr = $this->db->queryAll($sql, $params);
		$rule['model_arr'] = $model_arr;
		$rule['source_arr'] = $source_arr;
		$this->getView()->assign(
			array(
				'rule' => $rule,
				'page' => $page,
			)
		);
	}
	public function createAction() {
		if($_SERVER['REQUEST_METHOD'] == 'POST') {
			if(!isset($_POST['Rule']) || !is_array($_POST['Rule'])) {
				$this->redirect[] = array(
					'text' => '',
					'href' => 'javascript:history.go(-1);',
				);
				$this->message('保存采集模板信息错误', self::MSG_ERROR, true);
			}
			
			if($_POST['Rule']['collect_rule_name'] =='') {
				$this->redirect[] = array(
					'text' => '',
					'href' => 'javascript:history.go(-1);',
				);
				$this->message('采集模板名称不能为空', self::MSG_ERROR, true);
			}
			
			if($_POST['Rule']['collect_source_id'] =='') {
				$this->redirect[] = array(
					'text' => '',
					'href' => 'javascript:history.go(-1);',
				);
				$this->message('请选择一个采集来源', self::MSG_ERROR, true);
			}
			
			if($_POST['Rule']['collect_model_id'] =='') {
				$this->redirect[] = array(
					'text' => '',
					'href' => 'javascript:history.go(-1);',
				);
				$this->message('请选择一个采集模型', self::MSG_ERROR, true);
			}
			
			$flag = $this->db->insert('{{collect_rule}}',
				array(
					'collect_rule_id' => 0,
					'collect_rule_name' => $_POST['Rule']['collect_rule_name'],
					'collect_source_id' => $_POST['Rule']['collect_source_id'],
					'collect_model_id' => $_POST['Rule']['collect_model_id'],
					'collect_rule_remark' => $_POST['Rule']['collect_rule_remark'],
					'collect_rule_status' => CollectRuleModel::STAT_NORMAL,
					'collect_rule_rank' => $_POST['Rule']['collect_rule_rank'],
					'collect_rule_lasttime' => $_SERVER['REQUEST_TIME'],
					'collect_rule_dateline' => $_SERVER['REQUEST_TIME'],
				)
			);
			
			if($flag) {
				$collect_rule_id = $this->db->getLastInsertID();
				$collect_rule_name = $_POST['Rule']['collect_rule_name'];
	
				$sql = "SELECT `collect_fields_id` FROM `collect_fields` WHERE `collect_model_id`=:collect_model_id AND `collect_fields_status`!=:collect_fields_status";
				$params = array(
                    ':collect_fields_status' => CollectFieldsModel::STAT_DELETED,
                    ":collect_model_id" => $_POST['Rule']['collect_model_id']
                );
				$fields_arr = $this->db->queryAll($sql, $params);
				if(is_array($fields_arr) && count($fields_arr)){
					foreach($fields_arr as $k=>$v){
						$this->db->insert('{{collect_rule2fields}}',
							array(
								'collect_fields_id' => $v['collect_fields_id'],
								'collect_rule_id' => $collect_rule_id,
								'collect_rule_rule_status' => CollectRuleModel::STAT_NORMAL,
							)
						);
					}
				}
				//记录操作日志
				$message = '{user_name}添加采集模板{collect_rule_name}';
				$data = array(
					'collect_rule_name' => $collect_rule_name,
					'addons_data' => array('collect_rule_id'=>$collect_rule_id),
				);
				AdminLogs::add('Collect/Rule', $collect_rule_id, 'Insert', 'success', $message, $data);
				
				if(!isset($_GET['ajax'])) {
					$this->redirect[] = array(
						'text' => '',
						'href' => url($this->module->id. '/Collect/Rule/Index'),
					);
					$this->message('添加采集模板完成', self::MSG_SUCCESS, true);
				}
			} else {
				//记录操作日志
				$message = '{user_name}添加采集模板{collect_rule_name}失败';
				$data = array(
					'collect_rule_name' => $collect_rule_name,
					'addons_data' => array('rule'=>$_POST['Rule']),
				);
				AdminLogs::add('Collect/Rule', $collect_rule_id, 'Insert', 'failure', $message, $data);
				
				$this->redirect[] = array(
					'text' => '',
					'href' => 'javascript:history.go(-1);',
				);
				$this->message('添加采集模板失败', self::MSG_ERROR, true);
			}
		}
		$sql = "SELECT `collect_template_id`,`collect_template_name` FROM `collect_template` WHERE `collect_template_status`!=:collect_template_status";
		$params = array(
            ':collect_template_status' => CollectTemplate::STAT_DELETED,
        );
		$template_arr = $this->db->queryAll($sql, $params);
		$_charset = array(
            1 => "utf-8",
            2 => "GBK",
            3 => "gb2312"
        );
		$rule = array(
			'collect_rule_id' => 0,
			'collect_rule_name' => '',
			'collect_rule_remark' => '',
			'collect_rule_rank' => 255,
			'template_arr' => $template_arr,
			'charset' => $_charset,
		);
		
		$this->getView()->assign(
			array(
				'rule' => $rule,
			)
		);
	}
	public function deleteAction($id, $page = null) {
		$collect_rule_name = CollectRuleModel::inst()->getRuleNameById($id);
		$this->db->update('{{collect_rule}}',
			array(
				'collect_rule_status' => CollectRuleModel::STAT_DELETED,
			),
			'collect_rule_id=:collect_rule_id',
			array(':collect_rule_id'=>$id)
		);
		
		CollectRuleModel::inst()->updateCache();
			
		//记录操作日志
		$message = '{user_name}删除了采集模板{collect_rule_name}';
		$data = array(
			'collect_rule_name' => $collect_rule_name,
			'addons_data' => array('collect_rule_id'=>$id),
		);
		AdminLogs::add('Collect/Rule', $id, 'Delete', 'success', $message, $data);
		
		if(!isset($_GET['ajax'])) {
			$this->redirect('/collect_rule/index');
		}
	}
	public function ruleAction($id, $page = null) {
		if($_SERVER['REQUEST_METHOD'] == 'POST') {
			if(!isset($_POST['Rule']) || !is_array($_POST['Rule'])) {
				$this->redirect[] = array(
					'text' => '',
					'href' => 'javascript:history.go(-1);',
				);
				$this->message('保存采集模板信息错误', self::MSG_ERROR, true);
			}
			
			foreach($_POST['Rule'] as $k=>$v){
				$this->db->update('{{collect_rule2fields}}',
					array(
						'collect_rule_rule' => $v,
					),
					'collect_fields_id=:collect_fields_id',
					array(':collect_fields_id'=>intval($k))
				);
			}
			if(!isset($_GET['ajax'])) {
				$this->redirect[] = array(
					'text' => '',
					'href' => url($this->module->id. '/Collect/Rule/Index'),
				);
				$this->message('修改模板规则完成', self::MSG_SUCCESS, true);
			}
		}

		$sql = "SELECT `ct`.`collect_fields_id`,`ct`.`collect_rule_rule`,`cf`.`collect_fields_name` FROM `collect_rule2fields` `ct`
			INNER JOIN {{collect_fields}} `cf` ON  `ct`.`collect_fields_id`= `cf`.`collect_fields_id`
			WHERE `collect_rule_id`=:collect_rule_id AND `collect_rule_rule_status`!=:collect_rule_rule_status";
		$params = array(
            ':collect_rule_rule_status' => CollectFieldsModel::STAT_DELETED,
            ":collect_rule_id" => $id
        );
		$rule = $this->db->queryAll($sql, $params);
		$this->getView()->assign(
			array(
				'rule' => $rule,
			)
		);
	}
	public function loadFieldsAction($id) {
		$sql = "SELECT `collect_fields_id`,`collect_fields_name` FROM `collect_fields` WHERE `collect_fields_status`!=:collect_fields_status AND `collect_model_id`=:collect_model_id";
		$params = array(
            ':collect_fields_status' => CollectFieldsModel::STAT_DELETED,
            ":collect_model_id" => $id,
        );
		$fields_arr = $this->db->queryAll($sql, $params);
		echo json_encode($fields_arr);
        exit;
	}
}

<?php

class Collect_ModelController extends SysController {
	public function indexAction() {
		$this->getView()->assign(
            array(
                'models' => CollectModelModel::inst()->Pages(
                    array(
                        'allow_cache' => false,
                        //'pagesize' => 1,
                    )
                )
            )
        );
	}
	
	public function updateAction($id)
	{
		if($_SERVER['REQUEST_METHOD'] == 'POST') {
			if(!isset($_POST['Model']) || !is_array($_POST['Model'])) {
				$this->redirect[] = array(
					'text' => '',
					'href' => 'javascript:history.go(-1);',
				);
				$this->message('保存采集模型信息错误', self::MSG_ERROR, true);
			}
			
			if($_POST['Model']['collect_model_name'] =='') {
				$this->redirect[] = array(
					'text' => '',
					'href' => 'javascript:history.go(-1);',
				);
				$this->message('采集模型名称不能为空', self::MSG_ERROR, true);
			}
			if($_POST['Model']['collect_model_identify'] =='') {
				$this->redirect[] = array(
					'text' => '',
					'href' => 'javascript:history.go(-1);',
				);
				$this->message('采集模型标识不能为空', self::MSG_ERROR, true);
			}
			
			if(preg_match("/^[0-9]+.*/",$_POST['Model']['collect_model_identify'])) {
				$this->redirect[] = array(
					'text' => '',
					'href' => 'javascript:history.go(-1);',
				);
				$this->message('采集模型标识不能以数字开头', self::MSG_ERROR, true);
			}
			$old_collect_model_identify = CollectModelModel::inst()->getModelIdentifyById($id);
			$sql = "SELECT `collect_model_identify` FROM `{{collect_model}}` WHERE `collect_model_id`!=:collect_model_id AND collect_model_identify=:collect_model_identify";
			$params = array(
                ':collect_model_id' => $id,
                ':collect_model_identify' => $_POST['Model']['collect_model_identify']
            );
			if($this->db->queryScalar($sql, $params)) {
				$this->redirect[] = array(
					'text' => '',
					'href' => 'javascript:history.go(-1);',
				);
				$this->message('采集模型标识不能重复', self::MSG_ERROR, true);
			}
			
			$flag = $this->db->update('{{collect_model}}',
				array(
					'collect_model_name' => $_POST['Model']['collect_model_name'],
					'collect_model_identify' => $_POST['Model']['collect_model_identify'],
					'collect_model_rank' => $_POST['Model']['collect_model_rank'] ? intval($_POST['Model']['collect_model_rank']) : 255,
					'collect_model_lasttime' => $_SERVER['REQUEST_TIME'],
					'content_model_id' => $_POST['Model']['content_model_id'],
				),
				'collect_model_id=:collect_model_id',
				array(':collect_model_id'=>$id)
			);
			if($old_collect_model_identify != $_POST['Model']['collect_model_identify']){
				$sql = "RENAME TABLE `collect_model_addons{$old_collect_model_identify}` TO `collect_model_addons".$_POST['Model']['collect_model_identify']."` ;";
				$this->db->execute($sql);
			}
			if($flag) {
				$collect_model_id = $this->db->getLastInsertID();
				$collect_model_name = $_POST['Model']['collect_model_name'];
				//更新缓存
				CollectModelModel::inst()->updateCache();
				
				//记录操作日志
				$message = '{user_name}修改了采集模型{collect_model_name}';
				$data = array(
					'collect_model_name' => $collect_model_name,
					'addons_data' => array('collect_model_id'=>$collect_model_id),
				);
				UserLogsModel::inst()->add('Collect/Model', $collect_model_id, 'Insert', 'success', $message, $data);
				
				if(!isset($_GET['ajax'])) {
					$this->redirect[] = array(
						'text' => '',
						'href' => '/collect_model/index',
					);
					$this->message('保存采集模型成功', self::MSG_SUCCESS, true);
				}
			} else {
				//记录操作日志
				$message = '{user_name}修改采集模型({collect_model_name})信息失败';
				$data = array(
					'collect_model_name' => $collect_model_name,
					'addons_data' => array('model'=>$_POST['Model']),
				);
				UserLogsModel::inst()->add('Collect/Model', $collect_model_id, 'Modify', 'failure', $message, $data);
				
				$this->redirect[] = array(
					'text' => '',
					'href' => 'javascript:history.go(-1);',
				);
				$this->message('保存采集模型信息失败', self::MSG_ERROR, true);
			}
		}
		
		$model = CollectModelModel::inst()->getModelById($id,false);
		if(empty($model)) {
			$this->redirect[] = array(
				'text' => '',
				'href' => '/collect_model/index',
			);
			$this->message('采集模型不存在或已被删除', self::MSG_ERROR, true);
		}
		//print_r($model);
		$this->getView()->assign(
			array(
				'model' => $model,
				'content_model' => ContentModelModel::inst()->getModelsByCache(),
			)
		);
	}
	public function actionCreate()
	{
		if($_SERVER['REQUEST_METHOD'] == 'POST') {
			if(!isset($_POST['Model']) || !is_array($_POST['Model'])) {
				$this->redirect[] = array(
					'text' => '',
					'href' => 'javascript:history.go(-1);',
				);
				$this->message('保存采集模型信息错误', self::MSG_ERROR, true);
			}
			
			if($_POST['Model']['collect_model_name'] =='') {
				$this->redirect[] = array(
					'text' => '',
					'href' => 'javascript:history.go(-1);',
				);
				$this->message('采集模型名称不能为空', self::MSG_ERROR, true);
			}
			if($_POST['Model']['collect_model_identify'] =='') {
				$this->redirect[] = array(
					'text' => '',
					'href' => 'javascript:history.go(-1);',
				);
				$this->message('采集模型标识不能为空', self::MSG_ERROR, true);
			}
			
			if(preg_match("/^[0-9]+.*/",$_POST['Fields']['collect_model_identify'])) {
				$this->redirect[] = array(
					'text' => '',
					'href' => 'javascript:history.go(-1);',
				);
				$this->message('采集模型标识不能以数字开头', self::MSG_ERROR, true);
			}
			$old_collect_model_identify = CollectModel::get_model_identify_by_id($id);
			$sql = "SELECT `collect_model_identify` FROM `collect_model` WHERE collect_model_identify=:collect_model_identify AND `collect_model_status`!=:collect_model_status";
			$cmd = $this->db->createCommand($sql);
			
			$cmd->execute(array(':collect_model_identify'=>$_POST['Model']['collect_model_identify'],":collect_model_status"=>CollectModel::STAT_STATUS_DELETED));
			if($cmd->queryScalar()) {
				$this->redirect[] = array(
					'text' => '',
					'href' => 'javascript:history.go(-1);',
				);
				$this->message('采集模型标识不能重复', self::MSG_ERROR, true);
			}
			$flag = $this->db->createCommand()->insert('{{collect_model}}',
				array(
					'collect_model_id' => 0,
					'collect_model_name' => $_POST['Model']['collect_model_name'],
					'collect_model_identify' => $_POST['Model']['collect_model_identify'],
					'collect_model_status' => CollectModel::STAT_STATUS_NORMAL,
					'collect_model_rank' => $_POST['Model']['collect_model_rank'],
					'collect_model_lasttime' => $_SERVER['REQUEST_TIME'],
					'collect_model_dateline' => $_SERVER['REQUEST_TIME'],
					'content_model_id' => $_POST['Model']['content_model_id'],//增加内容模型
				)
			);
			
			if($flag) {
				$collect_model_id = $this->db->getLastInsertID();
				$collect_model_name = $_POST['Model']['collect_model_name'];
				
				$sql = "create table `collect_model_addons{$_POST['Model']['collect_model_identify']}` (
						`collect_content_id` int unsigned not null auto_increment,
						`collect_list_id` int unsigned not null comment '采集列表id',
						`collect_content_body` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL comment '正文内容',
						`collect_content_day` date not null comment '采集时间',
						PRIMARY KEY  (`collect_content_id`),
						UNIQUE KEY `collect_list_id` (`collect_list_id`)
					)ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='{$_POST['Model']['collect_model_name']}附加表';";
				$this->db->createCommand($sql)->execute();
				$this->db->createCommand()->insert('{{collect_fields}}',
					array(
						'collect_fields_id' => 0,
						'collect_model_id' => $collect_model_id,
						'collect_fields_name' => '链接地址',
						'collect_fields_identify' => 'collect_content_url',
						'collect_fields_status' => CollectFields::STAT_STATUS_NORMAL,
						'collect_fields_rank' => 255,
						'collect_fields_type' => 1, //单行文本
						'collect_fields_belong' => 1, //列表页
						'collect_fields_system' => 1, //固定字段，不允许修改删除
						'collect_fields_lasttime' => $_SERVER['REQUEST_TIME'],
						'collect_fields_dateline' => $_SERVER['REQUEST_TIME'],
					)
				);
				$this->db->createCommand()->insert('{{collect_fields}}',
					array(
						'collect_fields_id' => 0,
						'collect_model_id' => $collect_model_id,
						'collect_fields_name' => '标题',
						'collect_fields_identify' => 'collect_content_title',
						'collect_fields_status' => CollectFields::STAT_STATUS_NORMAL,
						'collect_fields_rank' => 255,
						'collect_fields_type' => 1, //单行文本
						'collect_fields_belong' => 1, //列表页
						'collect_fields_system' => 1, //固定字段，不允许修改删除
						'collect_fields_lasttime' => $_SERVER['REQUEST_TIME'],
						'collect_fields_dateline' => $_SERVER['REQUEST_TIME'],
					)
				);
				$this->db->createCommand()->insert('{{collect_fields}}',
					array(
						'collect_fields_id' => 0,
						'collect_model_id' => $collect_model_id,
						'collect_fields_name' => '缩略图',
						'collect_fields_identify' => 'collect_content_thumb',
						'collect_fields_status' => CollectFields::STAT_STATUS_NORMAL,
						'collect_fields_rank' => 255,
						'collect_fields_type' => 1, //单行文本
						'collect_fields_belong' => 1, //列表页
						'collect_fields_system' => 1, //固定字段，不允许修改删除
						'collect_fields_lasttime' => $_SERVER['REQUEST_TIME'],
						'collect_fields_dateline' => $_SERVER['REQUEST_TIME'],
					)
				);
				$this->db->createCommand()->insert('{{collect_fields}}',
					array(
						'collect_fields_id' => 0,
						'collect_model_id' => $collect_model_id,
						'collect_fields_name' => '内容',
						'collect_fields_identify' => 'collect_content_body',
						'collect_fields_status' => CollectFields::STAT_STATUS_NORMAL,
						'collect_fields_rank' => 255,
						'collect_fields_type' => 2, //多行文本
						'collect_fields_belong' => 2, //内容页
						'collect_fields_system' => 1, //固定字段，不允许修改删除
						'collect_fields_lasttime' => $_SERVER['REQUEST_TIME'],
						'collect_fields_dateline' => $_SERVER['REQUEST_TIME'],
					)
				);
				$this->db->createCommand()->insert('{{collect_fields}}',
					array(
						'collect_fields_id' => 0,
						'collect_model_id' => $collect_model_id,
						'collect_fields_name' => '发布时间',
						'collect_fields_identify' => 'collect_content_publish_time',
						'collect_fields_status' => CollectFields::STAT_STATUS_NORMAL,
						'collect_fields_rank' => 255,
						'collect_fields_type' => 1, //多行文本
						'collect_fields_belong' => 3, //全部
						'collect_fields_system' => 1, //固定字段，不允许修改删除
						'collect_fields_lasttime' => $_SERVER['REQUEST_TIME'],
						'collect_fields_dateline' => $_SERVER['REQUEST_TIME'],
					)
				);
				//更新缓存
				CollectModelModel::inst()->updateCache();
				
				//记录操作日志
				$message = '{user_name}添加采集来源{collect_model_name}';
				$data = array(
					'collect_model_name' => $collect_model_name,
					'addons_data' => array('collect_model_id'=>$collect_model_id),
				);
				UserLogsModel::inst()->add('Collect/Model', $collect_model_id, 'Insert', 'success', $message, $data);
				
				if(!isset($_GET['ajax'])) {
					$this->redirect[] = array(
						'text' => '',
						'href' => '/collect_model/index',
					);
					$this->message('添加采集模型完成', self::MSG_SUCCESS, true);
				}
			} else {
				//记录操作日志
				$message = '{user_name}添加采集模型{$collect_model_name}失败';
				$data = array(
					'collect_model_name' => $collect_model_name,
					'addons_data' => array('model'=>$_POST['Model']),
				);
				UserLogsModel::inst()->add('Collect/Model', $collect_model_id, 'Insert', 'failure', $message, $data);
				
				$this->redirect[] = array(
					'text' => '',
					'href' => 'javascript:history.go(-1);',
				);
				$this->message('添加采集模型失败', self::MSG_ERROR, true);
			}
		}
		
		$model = array(
			'collect_model_id' => 0,
			'collect_model_name' => '',
			'collect_model_identify' => '',
			'content_model_id' => 0,
			'collect_model_status' => CollectModelModel::STAT_STATUS_NORMAL,
			'collect_model_rank' => 255,
			'collect_model_lasttime' => $_SERVER['REQUEST_TIME'],
			'collect_model_dateline' => $_SERVER['REQUEST_TIME'],
		);
		
		$this->render(
			'create',
			array(
				'model' => $model,
				'content_model' => CollectModelModel::inst()->getModelsByCache(),
			)
		);
	}
	public function actionDelete($id, $page = null)
	{
		$collect_model_name = CollectModelModel::getModelNameById($id);
		$collect_model_identify = CollectModelModel::getModelIdentifyById($id);
		$this->db->update('{{collect_model}}',
			array(
				'collect_model_status' => CollectModelModel::STAT_STATUS_DELETED,
			),
			'collect_model_id=:collect_model_id',
			array(':collect_model_id'=>$id)
		);
		$sql = "RENAME TABLE `collect_model_addons{$collect_model_identify}` TO `collect_model_addons{$collect_model_identify}_backup".time()."` ;";
		$this->db->execute($sql);
		
		//更新缓存
		CollectModelModel::inst()->updateCache();
			
		//记录操作日志
		$message = '{user_name}删除了采集来源{$collect_model_name}';
		$data = array(
			'collect_model_name' => $collect_model_name,
			'addons_data' => array('collect_model_id'=>$id),
		);
		UserLogsModel::inst()->add('Collect/Model', $id, 'Delete', 'success', $message, $data);
		if(!isset($_GET['ajax'])) {
			$this->redirect('/collect_model/index');
		}
	}
}

<?php
class Collect_TaskController extends SystemController {
	public function indexAction() {
		$models = CollectModelModel::inst()->getModelsByCache();
		$sources = CollectSourceModel::inst()->getSourcesByCache();
		$templates = CollectTemplateModel::inst()->getTemplatesByCache();
		
		$collect_template_id = intval($_GET['collect_template_id']);
		$collect_source_id = intval($_GET['collect_source_id']);
		$collect_model_id = intval($_GET['collect_model_id']);
        $collect_task_name = $_GET['collect_task_name'];
		
		$this->getView()->assign(
            array(
                'tasks' => CollectTaskModel::inst()->Pages(
                    array(
                        'allow_cache' => false,
                    )
                ),
                'collect_template_id' => $collect_template_id,
                'collect_source_id' => $collect_source_id,
                'collect_model_id' => $collect_model_id,
                'collect_task_name' => $collect_task_name,
                'models' => $models,
                'sources' => $sources,
                'templates' => $templates,
            )
        );
	}
	
	public function createAction() {
		if($_SERVER['REQUEST_METHOD'] == 'POST') {
			if(!isset($_POST['Task']) || !is_array($_POST['Task'])) {
				$this->redirect[] = array(
					'text' => '',
					'href' => 'javascript:history.go(-1);',
				);
				$this->message('保存采集规则信息错误', self::MSG_ERROR, true);
			}
			
			if($_POST['Task']['collect_task_name'] =='') {
				$this->redirect[] = array(
					'text' => '',
					'href' => 'javascript:history.go(-1);',
				);
				$this->message('采集任务名称不能为空', self::MSG_ERROR, true);
			}
			
			if($_POST['Task']['collect_task_urls'] =='') {
				$this->redirect[] = array(
					'text' => '',
					'href' => 'javascript:history.go(-1);',
				);
				$this->message('采集网址不能为空', self::MSG_ERROR, true);
			}
			if(empty($_POST['Task']['collect_template_id'])) {
				$this->redirect[] = array(
					'text' => '',
					'href' => 'javascript:history.go(-1);',
				);
				$this->message('请选择一个采集模板', self::MSG_ERROR, true);
			}
            
			$flag = $this->db->insert('{{collect_task}}',
				array(
					'collect_task_id' => 0,
					'collect_task_name' => $_POST['Task']['collect_task_name'],
					'collect_template_id' => $_POST['Task']['collect_template_id'],
					'collect_task_urls' => $_POST['Task']['collect_task_urls'],
					'collect_task_list_rules' => json_encode($_POST['Task']['List']),
					'collect_task_addons_rules' => json_encode($_POST['Task']['Addons']),
					'collect_task_rank' => isset($_POST['Task']['collect_task_rank']) ? $_POST['Task']['collect_task_rank'] : 255,
					'collect_task_status' => CollectTaskModel::STAT_STATUS_NORMAL,
					'collect_task_lasttime' => $_SERVER['REQUEST_TIME'],
					'collect_task_dateline' => $_SERVER['REQUEST_TIME'],
				)
			);
			
			if($flag) {
				$collect_task_id = $this->db->getLastInsertID();
				
				//记录操作日志
				$message = '{user_name}添加采集任务{collect_task_name}';
				$data = array(
					'collect_task_name' => $_POST['Task']['collect_task_name'],
					'data' => $_POST['Task'],
				);
				UserLogsModel::inst()->add('Collect/Task', $collect_task_id, 'Insert', 'success', $message, $data);
				
				if(!isset($_GET['ajax'])) {
					$this->redirect[] = array(
						'text' => '',
						'href' => '/collect/task/index',
					);
					$this->message('添加采集任务完成', self::MSG_SUCCESS, true);
				}
			} else {
				//记录操作日志
				$message = '{user_name}添加采集任务{collect_task_name}失败';
				$data = array(
					'collect_task_name' => $_POST['Task']['collect_task_name'],
					'addons_data' => array('task'=>$_POST['Task']),
				);
				UserLogsModel::inst()->add('Collect/Task', 0, 'Insert', 'failure', $message, $data);
				
				$this->redirect[] = array(
					'text' => '',
					'href' => 'javascript:history.go(-1);',
				);
				$this->message('添加采集任务失败', self::MSG_ERROR, true);
			}
		}
		
		$models = CollectModelModel::inst()->getModelsByCache();
		$sources = CollectSourceModel::inst()->getSourcesByCache();
		$templates = CollectTemplateModel::inst()->getTemplatesByCache();
		$charsets = CollectTaskModel::getCharsets();
		
		$task = array(
			'collect_task_id' => 0,
			'collect_task_name' => '',
			'collect_task_urls' => '',
			'collect_template_id' => 0,
			'collect_task_rank' => 255,
			'template' => array(),
		);
		
		$this->getView()->assign(
			array(
				'task' => $task,
				'models' => $models,
				'sources' => $sources,
				'templates' => $templates,
				'charsets' => $charsets,
			)
		);
	}
	
	public function updateAction($id) {
		$task = CollectTaskModel::inst()->getTaskById($id);
		if(empty($task)) {
			$this->redirect[] = array(
				'text' => '',
				'href' => '/collect/task/index',
			);
			$this->message('采集规则不存在或已被删除', self::MSG_ERROR, true);
		}
		
		if($_SERVER['REQUEST_METHOD'] == 'POST') {
			if(!isset($_POST['Task']) || !is_array($_POST['Task'])) {
				$this->redirect[] = array(
					'text' => '',
					'href' => 'javascript:history.go(-1);',
				);
				$this->message('保存采集任务信息错误', self::MSG_ERROR, true);
			}
			
			if($_POST['Task']['collect_task_name'] =='') {
				$this->redirect[] = array(
					'text' => '',
					'href' => 'javascript:history.go(-1);',
				);
				$this->message('采集任务名称不能为空', self::MSG_ERROR, true);
			}
			
			if($_POST['Task']['collect_template_id'] =='') {
				$this->redirect[] = array(
					'text' => '',
					'href' => 'javascript:history.go(-1);',
				);
				$this->message('请选择一个采集模板', self::MSG_ERROR, true);
			}
			
			$flag = $this->db->update('{{collect_task}}',
				array(
					'collect_task_name' => $_POST['Task']['collect_task_name'],
					'collect_template_id' => $_POST['Task']['collect_template_id'],
					'collect_task_urls' => $_POST['Task']['collect_task_urls'],
					'collect_task_list_rules' => json_encode($_POST['Task']['List']),
					'collect_task_addons_rules' => json_encode($_POST['Task']['Addons']),
					'collect_task_rank' => isset($_POST['Task']['collect_task_rank']) ? $_POST['Task']['collect_task_rank'] : 255,
					'collect_task_lasttime' => $_SERVER['REQUEST_TIME'],
				),
				'collect_task_id=:collect_task_id',
				array(':collect_task_id' => $id)
			);
			
			if($flag) {
				//记录操作日志
				$message = '{user_name}修改了采集任务{collect_task_name}';
				$data = array(
					'collect_task_name' => $_POST['Task']['collect_task_name'],
					'data' => array('ori' => $task, 'new' => $_POST['Task']),
				);
				UserLogsModel::inst()->add('Collect/Task', $id, 'Modify', 'success', $message, $data);
				
				if(!isset($_GET['ajax'])) {
					$this->redirect[] = array(
						'text' => '',
						'href' => '/collect/task/index',
					);
					$this->message('保存采集任务成功', self::MSG_SUCCESS, true);
				}
			} else {
				//记录操作日志
				$message = '{user_name}修改采集任务({collect_task_name})信息失败';
				$data = array(
					'collect_task_name' => $_POST['Task']['collect_task_name'],
					'data' => $_POST['Task'],
				);
				UserLogsModel::inst()->add('Collect/Task', $id, 'Modify', 'failure', $message, $data);
				
				$this->redirect[] = array(
					'text' => '',
					'href' => 'javascript:history.go(-1);',
				);
				$this->message('保存采集任务失败', self::MSG_ERROR, true);
			}
		}
		
		$models = CollectModelModel::inst()->getModelsByCache();
		$sources = CollectSourceModel::inst()->getSourcesByCache();
		$templates = CollectTemplateModel::inst()->getTemplatesByCache();
		$charsets = CollectTaskModel::getCharsets();
		
		// 
		$task['template'] = CollectTemplateModel::inst()->getTemplateById($task['collect_template_id']);
		
		$this->getView()->assign(
			array(
				'task' => $task,
				'models' => $models,
				'sources' => $sources,
				'templates' => $templates,
				'charsets' => $charsets,
			)
		);
	}
	
	public function deleteAction($id) {
		$collect_task_name = CollectTaskModel::inst()->getTaskNameById($id);
		$this->db->update('{{collect_task}}',
			array(
				'collect_task_status' => CollectTask::STAT_DELETED,
			),
			'collect_task_id=:collect_task_id',
			array(':collect_task_id'=>$id)
		);
		
		CollectTaskModel::inst()->updateCache();
			
		//记录操作日志
		$message = '{user_name}删除了采集任务{collect_task_name}';
		$data = array(
			'collect_task_name' => $collect_task_name,
			'addons_data' => array('collect_task_id'=>$id),
		);
		UserLogsModel::inst()->add('Collect/Task', $id, 'Delete', 'success', $message, $data);
		
		if(!isset($_GET['ajax'])) {
			$this->redirect('/collect/task/index');
		}
	}
	
	public function testAction($id)
	{
		$task = CollectTaskModel::inst()->getTaskById($id);
		if(empty($task)) {
			$this->redirect[] = array(
				'text' => '',
				'href' => '/collect/task/index',
			);
			$this->message('采集规则不存在或已被删除', self::MSG_ERROR, true);
		}
		
		$collect_list_urls = CollectTaskModel::getListUrls($task['collect_task_urls']);
		
		if($collect_list_urls) {
			$collect_content_urls = CollectTaskModel::getContentUrls($collect_list_urls[0], $task['collect_list_rules']['begin'], $task['collect_list_rules']['end']);
			
			// 如果采集内容地址不为空，则采集内容
			if($collect_content_urls) {
				$collect_content_charset = 'utf-8';
				$collect_content_body = CollectTaskModel::getUrlContents($collect_content_urls[0], $collect_content_charset);
				
				if(strtolower($collect_content_charset) != 'utf-8') {
					$collect_content_body = mb_convert_encoding($collect_content_body, 'UTF-8', $collect_content_charset);
				}
				
				$task['collect_content_data'] = array();
				foreach ($task['collect_content_rules'] as $_k=>$_v) {
					if($_v['begin'] && $_v['end']) {
						preg_match("/{$_v['begin']}(.+?){$_v['end']}/s", $collect_content_body, $_r);
						$task['collect_content_data'][] = array(
							'subject' => $task['collect_content_rules'][$_k]['collect_fields_name'],
							'content' => $_r[0],
						);
					}
				}
				
				$task['collect_list_urls'] = $collect_list_urls;
				$task['collect_content_urls'] = $collect_content_urls;
				
				$this->render(
					'test',
					array(
						'task' => $task,
					)
				);
			}
		}
	}
	
	public function collectAction($id)
	{
		$task = CollectTaskModel::inst()->getTaskById($id);
		if(empty($task)) {
			$this->redirect[] = array(
				'text' => '',
				'href' => '/collect/task/index',
			);
			$this->message('采集规则不存在或已被删除', self::MSG_ERROR, true);
		}
		
		if($_GET['type'] == 'list') {
			$cacheKey = "jvod.collect.list.urls";
			if($this->redis->llen($cacheKey) == 0) {
				$collect_list_urls = CollectTaskModel::getListUrls($task['collect_task_urls']);
				
				foreach ($collect_list_urls as $_k=>$_v) {
					$this->redis->lpush($cacheKey, $_v);
				}
			}
			
			for($i=0; $i<20 && $this->redis->llen($cacheKey)>0; $i++) {
				$collect_list_url = $this->redis->rpop($cacheKey);
				
				$collect_content_urls = CollectTaskModel::getContentUrls($collect_list_url, $task['collect_list_rules']['begin'], $task['collect_list_rules']['end']);
				
				$repeat_number = 0;
				$sql = "SELECT COUNT(collect_content_id) FROM {{collect_content}} WHERE collect_content_url=:collect_content_url";
				foreach ($collect_content_urls as $_k=>$_v) {
					if($this->db->queryScalar($sql, array(':collect_content_url' => $_v)) == 0) {
						$this->db->insert(
							'{{collect_content}}',
							array(
								'collect_content_id' => 0,
								'collect_task_id' => $id,
								'collect_content_url' => $_v,
								'is_published' => 0,
								'is_collected' => 0,
								'lasttime' => $_SERVER['REQUEST_TIME'],
								'dateline' => $_SERVER['REQUEST_TIME'],
							)
						);
					} else {
						$repeat_number ++;
					}
					
					if($repeat_number > 20) {
						// 超过20个重复地址，退出内容地址采集
						break;
					}
				}
				
			}
		} else {
			$cacheKey = "jvod.collect.content.urls";
			if($this->queue->llen($cacheKey) == 0) {
				$sql = "SELECT collect_content_id, collect_task_id, collect_content_url, is_collected, is_published, lasttime, dateline FROM {{collect_content}} WHERE collect_task_id=:collect_task_id AND is_collected=:is_collected";
				$ret = $this->db->queryAll($sql, 
					array(
						':collect_task_id' => $id,
						':is_collected' => 0,
					)
				);
				
				foreach ($ret as $_k=>$_v) {
					$this->queue->lpush($cacheKey, json_encode(array($_v['collect_content_id'], $_v['collect_content_url'])));
				}
			}
			
			for($i=0; $i<20 && $this->queue->llen($cacheKey)>0; $i++) {
				list($collect_content_id, $collect_content_url) = json_decode($this->queue->rpop($cacheKey));
				$collect_content_charset = 'utf-8';
				$collect_content_body = CollectTask::get_url_contents($collect_content_url, $collect_content_charset);
				
				if(strtolower($collect_content_charset) != 'utf-8') {
					$collect_content_body = mb_convert_encoding($collect_content_body, 'UTF-8', $collect_content_charset);
				}
				
				$task['collect_content_data'] = array();
				$data = array(
					'collect_content_id' => $collect_content_id,
				);
				foreach ($task['collect_content_rules'] as $_k=>$_v) {
					if($_v['begin'] && $_v['end']) {
						preg_match("/{$_v['begin']}(.+?){$_v['end']}/s", $collect_content_body, $ret);
						$data[$_v['collect_fields_identify']] = $ret[1] ? $ret[1] : '';
					} else {
						$data[$_v['collect_fields_identify']] = '';
					}
				}
				$flag = $this->db->insert(
					"{{collect_model_addons{$task['collect_model_identify']}}}",
					$data
				);
				
				if($flag) {
					$this->db->update(
						"{{collect_content}}",
						array(
							'is_collected' => 1,
						),
						'collect_content_url=:collect_content_url',
						array(
							':collect_content_url' => $collect_content_url,
						)
					);
				}
			}
		}
	}
	
	public function actionLoadTemplateRules($id)
	{
		$template = CollectTemplateModel::inst()->getTemplateById($id);
		
		if($template) {
			$rules = array(
				'ok' => true,
				'list' => $template['collect_template_list_rules'],
				'addons' => $template['collect_template_addons_rules'],
			);
		} else {
			$rules = array(
				'ok' => false,
			);
		}
		
		echo json_encode($rules);
		exit;
	}
}

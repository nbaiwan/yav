<?php

class Collect_SourceController extends SysController
{
	public function indexAction() {
		$this->getView()->assign(
            array(
			'sources' => CollectSourceModel::inst()->Pages(
				array(
					'allow_cache' => false,
					//'pagesize' => 1,
				)
			)
		));
	}
	
	public function updateAction($id, $page = null) {
		$source = CollectSourceModel::inst()->getSourceById($id, false);
		if($_SERVER['REQUEST_METHOD'] == 'POST') {
			if(!isset($_POST['Source']) || !is_array($_POST['Source'])) {
				$this->redirect[] = array(
					'text' => '',
					'href' => 'javascript:history.go(-1);',
				);
				$this->message('保存采集来源信息错误', self::MSG_ERROR, true);
			}
			
			if($_POST['Source']['collect_source_name'] =='') {
				$this->redirect[] = array(
					'text' => '',
					'href' => 'javascript:history.go(-1);',
				);
				$this->message('采集来源名称不能为空', self::MSG_ERROR, true);
			}
			if($_POST['Source']['collect_source_website'] =='') {
				$this->redirect[] = array(
					'text' => '',
					'href' => 'javascript:history.go(-1);',
				);
				$this->message('采集来源网站不能为空', self::MSG_ERROR, true);
			}
			
			$flag = $this->db->update('{{collect_source}}',
				array(
					'collect_source_name' => $_POST['Source']['collect_source_name'],
					'collect_source_website' => $_POST['Source']['collect_source_website'],
					'collect_source_remark' => $_POST['Source']['collect_source_remark'],
					'collect_source_rank' => $_POST['Source']['collect_source_rank'] ? intval($_POST['Source']['collect_source_rank']) : 255,
					'collect_source_lasttime' => $_SERVER['REQUEST_TIME'],
				),
				'collect_source_id=:collect_source_id',
				array(':collect_source_id'=>$id)
			);
			
			if($flag) {
				$collect_source_id = $this->db->getLastInsertID();
				$collect_source_name = $_POST['Source']['collect_source_name'];
				//更新缓存
				CollectSourceModel::inst()->updateCache();
				
				//记录操作日志
				$message = '{user_name}修改了采集来源{collect_source_name}';
				$data = array(
					'collect_source_name' => $collect_source_name,
					'addons_data' => array('collect_source_id'=>$collect_source_id),
				);
				UserLogsModel::inst()->add('Collect/Source', $collect_source_id, 'Insert', 'success', $message, $data);
				
				if(!isset($_GET['ajax'])) {
					$this->redirect[] = array(
						'text' => '',
						'href' => '/collect_source/index',
					);
					$this->message('保存采集来源成功', self::MSG_SUCCESS, true);
				}
			} else {
				//记录操作日志
				$message = '{user_name}修改采集来源({collect_source_name})信息失败';
				$data = array(
					'collect_source_name' => $collect_source_name,
					'addons_data' => array('source'=>$_POST['Source']),
				);
				UserLogsModel::inst()->add('Collect/Source', $collect_source_id, 'Modify', 'failure', $message, $data);
				
				$this->redirect[] = array(
					'text' => '',
					'href' => 'javascript:history.go(-1);',
				);
				$this->message('保存采集来源信息失败', self::MSG_ERROR, true);
			}
		}
		
		if(empty($source)) {
			$this->redirect[] = array(
				'text' => '',
				'href' => url($this->module->id . '/Collect/Source/Index'),
			);
			$this->message('采集来源不存在或已被删除', self::MSG_ERROR, true);
		}
        
		$this->getView()->assign(
			array(
				'source' => $source,
			)
		);
	}
	
	public function createAction() {
		if($_SERVER['REQUEST_METHOD'] == 'POST') {
			if(!isset($_POST['Source']) || !is_array($_POST['Source'])) {
				$this->redirect[] = array(
					'text' => '',
					'href' => 'javascript:history.go(-1);',
				);
				if(!isset($_GET['ajax'])) {
					$this->message('保存信息错误', self::MSG_ERROR, true);
				}else{
					echo json_encode(array("ok"=>false,"error"=>'保存信息错误'));
					exit;
				}
			}
			
			if($_POST['Source']['collect_source_name'] =='') {
				$this->redirect[] = array(
					'text' => '',
					'href' => 'javascript:history.go(-1);',
				);
				if(!isset($_GET['ajax'])) {
					$this->message('采集来源名称不能为空', self::MSG_ERROR, true);
				}else{
					echo json_encode(array("ok"=>false,"error"=>'采集来源名称不能为空'));
					exit;
				}
			}
			if($_POST['Source']['collect_source_website'] =='') {
				$this->redirect[] = array(
					'text' => '',
					'href' => 'javascript:history.go(-1);',
				);
				if(!isset($_GET['ajax'])) {
					$this->message('采集来源网站不能为空', self::MSG_ERROR, true);
				}else{
					echo json_encode(array("ok"=>false,"error"=>'采集来源网站不能为空'));
					exit;
				}
			}
			$flag = $this->db->insert('{{collect_source}}',
				array(
					'collect_source_id' => 0,
					'collect_source_name' => $_POST['Source']['collect_source_name'],
					'collect_source_website' => $_POST['Source']['collect_source_website'],
					'collect_source_remark' => $_POST['Source']['collect_source_remark'],
					'collect_source_status' => CollectSourceModel::STAT_STATUS_NORMAL,
					'collect_source_rank' => $_POST['Source']['collect_source_rank'],
					'collect_source_lasttime' => $_SERVER['REQUEST_TIME'],
					'collect_source_dateline' => $_SERVER['REQUEST_TIME'],
				)
			);
			
			if($flag) {
				$collect_source_id = $this->db->getLastInsertID();
				$collect_source_name = $_POST['Source']['collect_source_name'];
	
				
				//记录操作日志
				$message = '{user_name}添加采集来源{collect_source_name}';
				$data = array(
					'collect_source_name' => $collect_source_name,
					'addons_data' => array('collect_source_id'=>$collect_source_id),
				);
				UserLogsModel::inst()->add('Collect/Source', $collect_source_id, 'Insert', 'success', $message, $data);
				
				if(!isset($_GET['ajax'])) {
					$this->redirect[] = array(
						'text' => '',
						'href' => '/collect_source/index',
					);
					$this->message('添加采集来源完成', self::MSG_SUCCESS, true);
				}else{
					echo json_encode(array("ok"=>true,"id"=>$collect_source_id,"name"=>$collect_source_name));
					exit;
				}
			} else {
				//记录操作日志
				$message = '{user_name}添加采集来源{collect_source_name}失败';
				$data = array(
					'collect_source_name' => $collect_source_name,
					'addons_data' => array('source'=>$_POST['Source']),
				);
				UserLogsModel::inst()->add('Collect/Source', $collect_source_id, 'Insert', 'failure', $message, $data);
				
				$this->redirect[] = array(
					'text' => '',
					'href' => 'javascript:history.go(-1);',
				);
				$this->message('添加采集来源失败', self::MSG_ERROR, true);
			}
		}
		
		$source = array(
			'collect_source_id' => 0,
			'collect_source_name' => '',
			'collect_source_website' => '',
			'collect_source_remark' => '',
			'collect_source_status' => CollectSourceModel::STAT_STATUS_NORMAL,
			'collect_source_rank' => 255,
			'collect_source_lasttime' => $_SERVER['REQUEST_TIME'],
			'collect_source_dateline' => $_SERVER['REQUEST_TIME'],
		);
		
		$this->getView()->assign(
			array(
				'source' => $source,
			)
		);
	}
    
	public function deleteAction($id) {
		$collect_source_name = CollectSourceModel::inst()->getSourceNameById($id);
		$this->db->update('{{collect_source}}',
			array(
				'collect_source_status' => CollectSourceModel::STAT_STATUS_DELETED,
			),
			'collect_source_id=:collect_source_id',
			array(':collect_source_id'=>$id)
		);
		
		CollectSourceModel::inst()->updateCache();
			
		//记录操作日志
		$message = '{user_name}删除了采集来源{collect_source_name}';
		$data = array(
			'collect_source_name' => $collect_source_name,
			'addons_data' => array('collect_source_id'=>$id),
		);
		UserLogsModel::inst()->add('Collect/Source', $id, 'Delete', 'success', $message, $data);
		
		if(!isset($_GET['ajax'])) {
			$this->redirect('/collect_source/index');
		}
	}
}

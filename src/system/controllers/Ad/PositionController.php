<?php

class PositionController extends SysController
{
	public function actionIndex()
	{
		if($_SERVER['REQUEST_METHOD'] == 'POST') {
			//保存修改
			if(!is_array($_POST['Position']['ad_position_rank'])) $_POST['Position']['ad_position_rank'] = array();
			foreach($_POST['Position']['ad_position_rank'] as $_k=>$_v) {
				$flag = Yii::app()->db->createCommand()->update('{{ad_position}}',
					array(
						'ad_position_rank' => ($_POST['Position']['ad_position_rank'][$_k]) ? intval($_POST['Position']['ad_position_rank'][$_k]) : 1,
					),
					'ad_position_id=:ad_position_id',
					array(':ad_position_id'=>$_k)
				);
				if($flag) {
					//记录操作日志
					$message = '{user_name}修改了广告位({position_name})排序信息';
					$data = array(
						'user_id' => Yii::app()->user->id,
						'user_name' => Yii::app()->user->name,
						'position_name' => AdPosition::get_position_name_by_id($_k),
						'addons_data' => $_POST,
					);
					AdminLogs::add(Yii::app()->user->id, 'Ad/Position', $_k, 'Modify', 'Success', $message, $data);
				}
			}
			AdPosition::update_cache();
			
			//$this->refresh();
			
			$this->redirect[] = array(
				'text' => '',
				'href' => $this->forward,
			);
			$this->message('修改广告位排序完成', self::MSG_SUCCESS, true);
		}
		
		
		$c = AdCategories::Pages(
				array(
					'allow_cache' => false,
					//'pagesize' => 1,
				)
			);
		if(is_array($c)){
			foreach($c as $k=>$v){
				$cc[$v['ad_categories_id']] = $v['ad_categories_name'];
			}
		}
		$this->render('index',array(
			'datas' => AdPosition::Pages(
				array(
					'allow_cache' => false,
					'ad_categories_id' => $_GET['ad_categories_id'],
					//'pagesize' => 1,
				)
			),
			'categories' => $cc,
		));
	}
	
	public function actionUpdate($id, $page = null)
	{
		if($_SERVER['REQUEST_METHOD'] == 'POST') {
			if(!isset($_POST['F']) || !is_array($_POST['F'])) {
				$this->redirect[] = array(
					'text' => '',
					'href' => 'javascript:history.go(-1);',
				);
				$this->message('保存信息错误', self::MSG_ERROR, true);
			}
			
			if($_POST['F']['ad_position_name'] =='') {
				$this->redirect[] = array(
					'text' => '',
					'href' => 'javascript:history.go(-1);',
				);
				$this->message('名称不能为空', self::MSG_ERROR, true);
			}
			$flag = Yii::app()->db->createCommand()->update('{{ad_position}}',
				array(
					'ad_position_name' => $_POST['F']['ad_position_name'],
					'ad_categories_id' => $_POST['F']['ad_categories_id'],
					'ad_position_rank' => $_POST['F']['ad_position_rank'],
					'ad_position_remark' => $_POST['F']['ad_position_remark'],
					'ad_position_width' => $_POST['F']['ad_position_width'],
					'ad_position_height' => $_POST['F']['ad_position_height'],
					'ad_position_type' => $_POST['F']['ad_position_type'],
					'ad_position_target' => $_POST['F']['ad_position_target'],
					'ad_position_relative_type' => $_POST['F']['ad_position_relative_type'],
					'ad_position_dateline' => date("Y-m-d H:i:s"),
				),
				'ad_position_id=:ad_position_id',
				array(':ad_position_id'=>$id)
			);
			if($flag) {
				$ad_position_id = $id;
				$ad_position_name = $_POST['F']['ad_position_name'];
				//更新缓存
				AdPosition::update_cache();
				
				//记录操作日志
				$user = Yii::app()->user;
				$message = '{user_name}修改了广告位{ad_position_name}';
				$data = array(
					'user_id' => $user->id,
					'user_name' => $user->name,
					'ad_position_name' => $ad_position_name,
					'addons_data' => array('ad_position_id'=>$ad_position_id),
				);
				AdminLogs::add($user->id, 'Ad/Position', $ad_position_id, 'Modify', 'success', $message, $data);
				
				if(!isset($_GET['ajax'])) {
					$this->redirect[] = array(
						'text' => '',
						'href' => $this->forward, //url($this->module->id. '/Ad/Position/Index'),
					);
					$this->message('保存成功', self::MSG_SUCCESS, true);
				}
			} else {
				//记录操作日志
				$user = Yii::app()->user;
				$message = '{user_name}修改采集模型({ad_position_name})信息失败';
				$data = array(
					'user_id' => $user->id,
					'user_name' => $user->name,
					'ad_position_name' => $ad_position_name,
					'addons_data' => array('position'=>$_POST['F']),
				);
				AdminLogs::add($user->id, 'Ad/Position', $ad_position_id, 'Modify', 'failure', $message, $data);
				
				$this->redirect[] = array(
					'text' => '',
					'href' => 'javascript:history.go(-1);',
				);
				$this->message('保存信息失败', self::MSG_ERROR, true);
			}
		}
		
		$data = AdPosition::get_one_by_id($id,false);
		if(empty($data)) {
			$this->redirect[] = array(
				'text' => '',
				'href' => $this->forward,//url($this->module->id . '/Ad/Position/Index'),
			);
			$this->message('id不存在或已被删除', self::MSG_ERROR, true);
		}
		//print_r($data);
		$data['categories'] = AdCategories::Pages(
				array(
					'allow_cache' => false,
				)
			);
		$this->render('update',
			array(
				'data' => $data,
			)
		);
	}
	
	public function actionCreate()
	{
		if($_SERVER['REQUEST_METHOD'] == 'POST') {
			if(!isset($_POST['F']) || !is_array($_POST['F'])) {
				$this->redirect[] = array(
					'text' => '',
					'href' => 'javascript:history.go(-1);',
				);
				$this->message('保存信息错误', self::MSG_ERROR, true);
			}
			
			if($_POST['F']['ad_position_name'] =='') {
				$this->redirect[] = array(
					'text' => '',
					'href' => 'javascript:history.go(-1);',
				);
				$this->message('名称不能为空', self::MSG_ERROR, true);
			}
			if($_POST['F']['ad_position_identify'] =='') {
				$this->redirect[] = array(
					'text' => '',
					'href' => 'javascript:history.go(-1);',
				);
				$this->message('标识不能为空', self::MSG_ERROR, true);
			}
			if($_POST['F']['ad_position_identify'] =='') {
				$this->redirect[] = array(
					'text' => '',
					'href' => 'javascript:history.go(-1);',
				);
				$this->message('标识不能为空', self::MSG_ERROR, true);
			}
			if(preg_match("/^[0-9]+.*/",$_POST['F']['ad_position_identify'])) {
				$this->redirect[] = array(
					'text' => '',
					'href' => 'javascript:history.go(-1);',
				);
				$this->message('标识不能以数字开头', self::MSG_ERROR, true);
			}

			$sql = "SELECT `ad_position_identify` FROM {{`ad_position`}} WHERE ad_position_identify=:ad_position_identify AND `ad_position_status`!=:ad_position_status";
			$cmd = Yii::app()->db->createCommand($sql);
			
			$cmd->execute(array(':ad_position_identify'=>$_POST['F']['ad_position_identify'],":ad_position_status"=>AdPosition::STAT_DELETED));
			if($cmd->queryScalar()) {
				$this->redirect[] = array(
					'text' => '',
					'href' => 'javascript:history.go(-1);',
				);
				$this->message('标识不能重复', self::MSG_ERROR, true);
			}
			$flag = Yii::app()->db->createCommand()->insert('{{ad_position}}',
				array(
					'ad_position_id' => 0,
					'ad_position_name' => $_POST['F']['ad_position_name'],
					'ad_categories_id' => $_POST['F']['ad_categories_id'],
					'ad_position_identify' => $_POST['F']['ad_position_identify'],
					'ad_position_status' => AdPosition::STAT_NORMAL,
					'ad_position_rank' => $_POST['F']['ad_position_rank'],
					'ad_position_remark' => $_POST['F']['ad_position_remark'],
					'ad_position_width' => $_POST['F']['ad_position_width'],
					'ad_position_height' => $_POST['F']['ad_position_height'],
					'ad_position_type' => $_POST['F']['ad_position_type'],
					'ad_position_dateline' => date("Y-m-d H:i:s"),
					'ad_position_target' => $_POST['F']['ad_position_target'],
					'ad_position_relative_type' => $_POST['F']['ad_position_relative_type'],
				)
			);
			
			if($flag) {
				$ad_position_id = Yii::app()->db->getLastInsertID();
				$ad_position_name = $_POST['F']['ad_position_name'];
				//更新缓存
				AdPosition::update_cache();
				
				//记录操作日志
				$user = Yii::app()->user;
				$message = '{user_name}添加广告位{ad_position_name}';
				$data = array(
					'user_id' => $user->id,
					'user_name' => $user->name,
					'ad_position_name' => $ad_position_name,
					'addons_data' => array('ad_position_id'=>$ad_position_id),
				);
				AdminLogs::add($user->id, 'Ad/Position', $ad_position_id, 'Insert', 'success', $message, $data);
				
				if(!isset($_GET['ajax'])) {
					$this->redirect[] = array(
						'text' => '',
						'href' => url($this->module->id. '/Ad/Position/Index'),
					);
					$this->message('添加成功', self::MSG_SUCCESS, true);
				}
			} else {
				//记录操作日志
				$user = Yii::app()->user;
				$message = '{user_name}添加广告位{collect_model_name}失败';
				$data = array(
					'user_id' => $user->id,
					'user_name' => $user->name,
					'ad_position_name' => $ad_position_name,
					'addons_data' => array('position'=>$_POST['F']),
				);
				AdminLogs::add($user->id, 'Ad/Position', $ad_position_id, 'Insert', 'failure', $message, $data);
				
				$this->redirect[] = array(
					'text' => '',
					'href' => 'javascript:history.go(-1);',
				);
				$this->message('添加失败', self::MSG_ERROR, true);
			}
		}
		
		$data = array(
			'ad_position_id' => 0,
			'ad_position_name' => '',
			'ad_categories_id' => isset($_GET['ad_categories_id']) ? intval($_GET['ad_categories_id']) : 0,
			'ad_position_identify' => '',
			'ad_position_rank' => 255,
			'ad_position_remark' => '',
			'ad_position_width' => '',
			'ad_position_height' => '',
			'ad_position_type' => 0,
			'ad_position_target' => '',
			'ad_position_relative_type' => '',
			//
			'categories' => AdCategories::Pages(
				array(
					'allow_cache' => false,
				)
			),
		);
		
		$this->render('create',
			array(
				'data' => $data,
			)
		);
	}
	public function actionDelete($id, $page = null)
	{
		$data = AdPosition::get_one_by_id($id,false);
		if($data['ad_position_system']){
			if(!isset($_GET['ajax'])) {
				$this->redirect[] = array(
					'text' => '',
					'href' => url($this->module->id. '/Ad/Position/Index'),
				);
				$this->message('系统广告位，不允许删除！', self::MSG_SUCCESS, true);
			}
		}
		Yii::app()->db->createCommand()->update('{{ad_position}}',
			array(
				'ad_position_status' => AdPosition::STAT_DELETED,
			),
			'ad_position_id=:ad_position_id',
			array(':ad_position_id'=>$id)
		);
		//更新缓存
		AdPosition::update_cache();
			
		//记录操作日志
		$user = Yii::app()->user;
		$message = '{user_name}删除了广告位{ad_position_name}';
		$data = array(
			'user_id' => $user->id,
			'user_name' => $user->name,
			'ad_position_name' => $data['ad_position_name'],
			'addons_data' => array('ad_position_id'=>$id),
		);
		AdminLogs::add($user->id, 'Ad/Position', $id, 'Delete', 'success', $message, $data);
		if(!isset($_GET['ajax'])) {
			$this->redirect(array('Ad/Position/Index'));
		}
	}
	
	public function actionJs($id){
		$position = AdPosition::get_one_by_id($id);
		$sql = "SELECT * FROM {{ad_data}} WHERE `ad_position_id`=:ad_position_id";
		$cmd = Yii::app()->db->createCommand($sql);
		$cmd->execute(array(':ad_position_id'=>$id));
		$datas = $cmd->queryAll();
		if($position['ad_position_type'] == 1){ //固定
			if(is_array($datas) && count($datas)){
				$ad['data'] = $datas;
			}else{
				$ad['data'] = array();
			}
			$ad['position'] = $position;
			$content = 'var '.$position['ad_position_identify'].'_json = '.json_encode($ad);
		}else if($position['ad_position_type'] == 2){ //漂浮
			
		}else if($position['ad_position_type'] == 3){ //弹窗
		
		}else if($position['ad_position_type'] == 4){ //对联
		
		}
		$jspath = AdPosition::get_js_path();
		file_put_contents($jspath."/".$position['ad_position_identify'].".js", $content);
		if(!isset($_GET['ajax'])) {
			$this->redirect[] = array(
				'text' => '',
				'href' => url($this->module->id. '/Ad/Position/Index'),
			);
			$this->message('更新成功', self::MSG_SUCCESS, true);
		}
	}
}

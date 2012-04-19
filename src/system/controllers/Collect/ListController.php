<?php

class ListController extends SysController
{
	public function actionIndex()
	{
		$taskid = intval($_GET['taskid']);
		if($_GET['day']){
			$date = $_GET['day'];
		}elseif($_GET['datestart'] && $_GET['dateend']){
			$date = array($_GET['datestart'],$_GET['dateend']);
		}elseif($_GET['datestart']){
			$date = $_GET['datestart'];
		}
		$title = trim($_GET['title']);
		if(isset($_GET['check']) && $_GET['check']!=="") {
			$check = intval($_GET['check']);
		}elseif($_GET['check']!==""){
			$check = $_GET['check'] = "0";
		}else{
			$check = "";
		}
		$sql = "SELECT `collect_task_id`,`collect_task_name` FROM `collect_task` WHERE `collect_task_status`!=:collect_task_status";
		$cmd = Yii::app()->db->createCommand($sql);
		$cmd->execute(array(':collect_task_status'=>CollectTask::STAT_DELETED));
		$task_arr = $cmd->queryAll();
		$game_list = Game::get_game_name_list();
		$list = CollectList::Pages(
				array(
					'allow_cache' => false,
					'pagesize' => 3000,
					'collect_task_id' => $taskid,
					'collect_list_day' => $date,
					'collect_list_title' => $title,
					'collect_list_check' => $check
				)
			);
		foreach($list['rows'] as $k=>$v){
			if($v['game_id']){
				$list['rows'][$k]['game_name'] = $game_list[$v['game_id']][0];
			}
		}
		$this->render('index',array(
			'list' => $list,
			'task_arr'=>$task_arr
		));
	}
	
	
	public function actionDelete($id = null)
	{
		if($_SERVER['REQUEST_METHOD'] == 'POST') {
			$id = $_POST['List']['collect_list_id'];
		}
		
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
			$_sql_in .= ($_sql_in == '') ? ":id_{$id}" : ", :id_{$id}";
			$_sql_param[":id_{$id}"] = $id;
		}
		$_sql = "DELETE FROM {{collect_list}} WHERE collect_list_id IN ({$_sql_in})";
		$_cmd = Yii::app()->db->createCommand($_sql);
		//$_cmd->bindValue(':collect_list_id', $id);
		$_cmd->execute($_sql_param);
		
		if(!isset($_GET['ajax'])) {
			$this->redirect[] = array(
				'text' => '',
				//'href' => url($this->module->id . '/Collect/List/Index'),
				'href' => $_SERVER['HTTP_REFERER'],
			);
			$this->message('删除成功', self::MSG_SUCCESS, true);
		}
	}
	public function actionCheck($id = null)
	{
		if($_SERVER['REQUEST_METHOD'] == 'POST') {
			$id = $_POST['List']['collect_list_id'];
		}
		
		$ids = is_array($id) ? $id : array($id);
		$ids = array_filter($ids);
		if(!count($ids)){
			$this->redirect[] = array(
				'text' => '',
				//'href' => url($this->module->id . '/Collect/List/Index'),
				'href' => $_SERVER['HTTP_REFERER'],
			);
			$this->message('请选择至少一项进行操作！', self::MSG_SUCCESS, true);
		}
		$value = intval($_REQUEST['value']) ? 1 : 0; //1审核0取消审核
		foreach($ids as $id) {
			$list = CollectList::get_list_by_id($id, false);
			$task = CollectTask::get_task_by_id($list['collect_task_id'], false);
			$template = CollectTemplate::get_template_by_id($task['collect_template_id'], false);
			if($value){
				/*$sql = "INSERT INTO {{game_media_data}} SET 
						`media_data_subject`='{$list[collect_list_title]}',
						`media_data_href`='$list[collect_list_url]',
						`media_data_thumb`='$list[collect_list_thumb]',
						`media_data_dateline`=".time().",
						`game_id`='{$list[game_id]}',
						`content_class_id`='{$task[content_class_id]}',
						`media_data_source`='{$template[collect_source_id]}',
						`collect_data_time`=".strtotime($list['collect_list_day'])
				;
				$_cmd->execute();*/
				$_cmd = Yii::app()->db->createCommand()->insert(
					'{{game_media_data}}',
					array(
						'media_data_subject' => $list[collect_list_title],
						'media_data_href' => $list[collect_list_url],
						'media_data_thumb' => $list[collect_list_thumb],
						'media_data_dateline' => time(),
						'game_id' => $list[game_id],
						'content_class_id' => $task[content_class_id],
						'media_data_source' => $template[collect_source_id],
						'collect_data_time' => strtotime($list['collect_list_day'])
					)
				);
				$media_data_id = Yii::app()->db->getLastInsertID();
			}else{ //取消审核
				$media_data_id = 0;
				if($list["media_data_id"]){
					$sql = "DELETE FROM {{game_media_data}} WHERE media_data_id={$list[media_data_id]}";
					$_cmd = Yii::app()->db->createCommand($sql);
					$_cmd->execute();
				}
			}
			$_sql = "UPDATE {{collect_list}} SET `collect_list_check`={$value},`media_data_id`={$media_data_id} WHERE collect_list_id={$id}";
			$_cmd = Yii::app()->db->createCommand($_sql);
			$_cmd->execute();
		}
		if(!isset($_GET['ajax'])) {
			$this->redirect[] = array(
				'text' => '',
				//'href' => url($this->module->id . '/Collect/List/Index'),
				'href' => $_SERVER['HTTP_REFERER'],
			);
			$this->message('操作成功', self::MSG_SUCCESS, true);
		}
	}
}

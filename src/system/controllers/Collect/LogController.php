<?php

class LogController extends SysController
{
	public function actionIndex()
	{
		$where = "";
		if($_GET['date']){
			$date = $_GET['date'];
		}else{
			$date = $_GET['date'] = date("Y-m-d");
		}
		$this->render('index',array(
			'log_arr'=>CollectLog::Pages(array(
				'collect_log_insert_time' => $date,
				'collect_log_msg' => $_GET['msg']
			)),
		));
	}
}

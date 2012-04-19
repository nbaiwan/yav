<?php

class CategoriesController extends SysController
{
	public function actionIndex()
	{
		$this->render('index',array(
			'datas' => AdCategories::Pages(
				array(
					'allow_cache' => false,
					//'pagesize' => 1,
				)
			)
		));
	}
	
	public function actionBatUpdate(){
		if($_SERVER['REQUEST_METHOD'] == 'POST') {
			$ids = $_POST['ad_categorise_id'];
		}
		
		if(!is_array($ids) || !count($ids)){
			$this->redirect[] = array(
				'text' => '',
				'href' => 'javascript:history.go(-1);',
			);
			$this->message('请至少选择一项!', self::MSG_ERROR, true);
		}
		$ids = array_filter($ids);
		foreach($ids as $v){
			if($_POST['ad_categories_name'][$v] =='') {
				continue;
			}
			$flag = Yii::app()->db->createCommand()->update('{{game_ad_categories}}',
				array(
					'ad_categories_name' => $_POST['ad_categories_name'][$v],
					'ad_categories_rank' => $_POST['ad_categories_rank'][$v],
				),
				'ad_categories_id=:ad_categories_id',
				array(':ad_categories_id'=>$v)
			);
		}
		if(!isset($_GET['ajax'])) {
			$this->redirect[] = array(
				'text' => '',
				'href' => url($this->module->id. '/Ad/Categories/Index'),
			);
			$this->message('保存成功', self::MSG_SUCCESS, true);
		}
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
			
			if($_POST['F']['ad_categories_name'] =='') {
				$this->redirect[] = array(
					'text' => '',
					'href' => 'javascript:history.go(-1);',
				);
				$this->message('名称不能为空', self::MSG_ERROR, true);
			}
			$flag = Yii::app()->db->createCommand()->update('{{game_ad_categories}}',
				array(
					'ad_categories_name' => $_POST['F']['ad_categories_name'],
					'ad_categories_rank' => $_POST['F']['ad_categories_rank'],
				),
				'ad_categories_id=:ad_categories_id',
				array(':ad_categories_id'=>$id)
			);
			if($flag) {
				
				if(!isset($_GET['ajax'])) {
					$this->redirect[] = array(
						'text' => '',
						'href' => url($this->module->id. '/Ad/Categories/Index'),
					);
					$this->message('保存成功', self::MSG_SUCCESS, true);
				}
			} else {
				
				$this->redirect[] = array(
					'text' => '',
					'href' => 'javascript:history.go(-1);',
				);
				$this->message('保存信息失败', self::MSG_ERROR, true);
			}
		}
		
		$data = AdCategories::get_one_by_id($id,false);
		if(empty($data)) {
			$this->redirect[] = array(
				'text' => '',
				'href' => url($this->module->id . '/Ad/Categories/Index'),
			);
			$this->message('id不存在或已被删除', self::MSG_ERROR, true);
		}
		//print_r($data);
		$this->render('create',
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
				if(!isset($_GET['ajax'])) {
					$this->message('保存信息错误', self::MSG_ERROR, true);
				}else{
					echo json_encode(array("ok"=>false,"error"=>'保存信息错误'));
					exit;
				}
			}
			
			if($_POST['F']['ad_categories_name'] =='') {
				$this->redirect[] = array(
					'text' => '',
					'href' => 'javascript:history.go(-1);',
				);
				if(!isset($_GET['ajax'])) {
					$this->message('名称不能为空', self::MSG_ERROR, true);
				}else{
					echo json_encode(array("ok"=>false,"error"=>'名称不能为空'));
					exit;
				}
			}
			$flag = Yii::app()->db->createCommand()->insert('{{game_ad_categories}}',
				array(
					'ad_categories_id' => 0,
					'ad_categories_name' => $_POST['F']['ad_categories_name'],
					'ad_categories_rank' => $_POST['F']['ad_categories_rank'],
				)
			);
			
			if($flag) {
				$ad_categories_id = Yii::app()->db->getLastInsertID();
				$ad_categories_name = $_POST['F']['ad_categories_name'];
				if(!isset($_GET['ajax'])) {
					$this->redirect[] = array(
						'text' => '',
						'href' => url($this->module->id. '/Ad/Categories/Index'),
					);
					$this->message('添加成功', self::MSG_SUCCESS, true);
				}else{
					echo json_encode(array("ok"=>true,"id"=>$ad_categories_id,"name"=>$ad_categories_name));
					exit;
				}
			} else {
				
				$this->redirect[] = array(
					'text' => '',
					'href' => 'javascript:history.go(-1);',
				);
				$this->message('添加失败', self::MSG_ERROR, true);
			}
		}
		
		$this->render('create',
			array(
				'data' => $data,
			)
		);
	}
	public function actionDelete($id)
	{
		
		$_sql = "DELETE FROM {{game_ad_categories}} WHERE ad_categories_id=:ad_categories_id";
		$_cmd = Yii::app()->db->createCommand($_sql);
		$_cmd->bindValue(':ad_categories_id', $id);
		$_cmd->execute();
		if(!isset($_GET['ajax'])) {
			$this->redirect(array('Ad/Categories/Index'));
		}
	}
}

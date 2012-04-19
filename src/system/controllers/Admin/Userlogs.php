<?php

class Admin_UserLogsController extends SysController {

	/**
	 * Lists all models.
	 */
	public function indexAction() {
		/*
		$model=new Goods('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Goods']))
			$model->attributes=$_GET['Goods'];

		$this->render('index',array(
			'model'=>$model,
		));*/
		
		$this->getView()->assign(
            array(
				'data' => UserLogsModel::inst()->Pages(
					array(
						'pagesize' => 15,
					)
				),
			)
		);
	}
}

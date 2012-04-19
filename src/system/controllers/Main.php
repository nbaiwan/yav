<?php

class MainController extends SysController {

	//整体框架页
	public function frameAction() {
		$this->render('index', array(
				'menus' => $this->__config['menu'],
			)
		);
	}
    
	public function indexAction() {
        
	}
	
	public function headerAction() {
		$this->render('header');
	}
}
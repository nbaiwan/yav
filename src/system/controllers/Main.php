<?php

class MainController extends SystemController {

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
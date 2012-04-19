<?php
$this->breadcrumbs=array(
	'System Manager',
	'Admin Manager',
);

$this->menu=array(
	array('label'=>'Admin Index', 'url'=>url($this->module->id . '/Admin/Admin/Index')),
	array('label'=>'Create Admin', 'cur'=>true, 'url'=>url($this->module->id . '/Admin/Admin/Create')),
);

$this->renderPartial(
	'_form', 
	array(
		'user' => $user,
		'roles'=>$roles,
		'purviews'=>$purviews,
	)
);

?>
<?php
$this->breadcrumbs=array(
	'System Manager',
	'Admin Manager',
);

$this->menu=array(
	array('label'=>'Admin Index', 'url'=>url($this->module->id . '/Admin/Admin/Index')),
	array('label'=>'Create Admin', 'url'=>url($this->module->id . '/Admin/Admin/Create')),
	array('label'=>'Update Admin', 'cur'=>true, 'url'=>'#'),
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
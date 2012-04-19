<?php
$this->breadcrumbs=array(
	'System Manager',
	'Roles Manager',
);

$this->menu=array(
	array('label'=>'Roles Index', 'url'=>url($this->module->id . '/Admin/Role/Index')),
	array('label'=>'Create Role', 'cur'=>true, 'url'=>url($this->module->id . '/Admin/Role/Create')),
);

$this->renderPartial(
	'_form', 
	array(
		'role' => $role,
		'roles' => $roles,
		'purviews' => $purviews,
	)
);

?>
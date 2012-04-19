<?php
$this->breadcrumbs=array(
	'System Manager',
	'采集管理',
	'模型管理',
);

$this->menu=array(
	array('label'=>'模型列表', 'url'=>url($this->module->id . '/Collect/Model/Index')),
	array('label'=>'添加模型', 'cur'=>true, 'url'=>url($this->module->id . '/Collect/Model/Create')),
);

$this->renderPartial(
	'_form',
	array(
		'model' => $model,
		'content_model' => $content_model,
	)
);

?>
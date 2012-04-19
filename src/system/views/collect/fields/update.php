<?php
$this->breadcrumbs=array(
	'采集字段管理',
	'采集字段列表',
);

$this->menu=array(
	array('label'=>'字段管理'),
	array('label'=>'字段列表', 'url'=>url($this->module->id . "/Collect/Model/{$collect_model_id}/Fields/Index")),
	array('label'=>'添加字段', 'cur'=>true, 'url'=>url($this->module->id . "/Collect/Model/{$collect_model_id}/Fields/Create")),
);

$this->renderPartial(
	'_form',
	array(
		'field' => $field,
		'field_types' => $field_types,
		'content_model_fields' => $content_model_fields,
	)
);
?>
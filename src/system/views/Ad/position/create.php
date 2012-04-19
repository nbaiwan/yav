<?php
//默认URL参数
$_default_params = isset($_GET['ad_categories_id']) && !empty($_GET['ad_categories_id']) ? array(
	'ad_categories_id' => $_GET['ad_categories_id'],
) : array();

$this->breadcrumbs=array(
	'广告位管理',
	'添加广告位',
);

$this->menu=array(
	array('label'=>'广告位管理'),
	array('label'=>'广告位列表', 'url'=>url($this->module->id . '/Ad/Position/Index', $_default_params)),
	array('label'=>'添加广告位', 'cur'=>true, 'url'=>url($this->module->id . '/Ad/Position/Create', $_default_params)),
);

$this->renderPartial(
	'_form',
	array(
		'data'=>$data,
	)
);
?>
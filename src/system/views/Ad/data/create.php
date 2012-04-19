<?php
//默认URL参数
$_default_params = isset($_GET['ad_position_id']) && !empty($_GET['ad_position_id']) ? array(
	'ad_position_id' => $_GET['ad_position_id'],
) : array();

$this->breadcrumbs=array(
	'广告管理',
	'添加广告',
);

$this->menu=array(
	array('label'=>'广告管理'),
	array('label'=>'广告列表', 'url'=>url($this->module->id . '/Ad/Data/Index', $_default_params)),
	array('label'=>'添加广告', 'cur'=>true, 'url'=>url($this->module->id . '/Ad/Data/Create', $_default_params)),
);
?>
<?php $this->renderPartial('_form', array('data'=>$data,"position_arr"=>$position_arr,"pages"=>$pages)); ?>
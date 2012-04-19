<?php
$this->breadcrumbs=array(
	'广告位分类管理',
	'添加广告位分类',
);

$this->menu=array(
	array('label'=>'广告位分类管理'),
	array('label'=>'广告位分类列表', 'url'=>url($this->module->id . '/Ad/Categories/Index')),
	array('label'=>'添加广告位分类', 'cur'=>true, 'url'=>url($this->module->id . '/Ad/Categories/Create')),
);
?>
<?php $this->renderPartial('_form', array('data'=>$data)); ?>
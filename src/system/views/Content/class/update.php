<?php
$this->breadcrumbs=array(
	'档案栏目管理',
	'栏目修改',
);

$this->menu=array(
	array('label'=>'档案栏目管理'),
	array('label'=>'栏目列表', 'url'=>url($this->module->id . '/Content/Class/Index')),
	array('label'=>'栏目修改', 'cur'=>true, 'url'=>url($this->module->id . "/Content/Class/{$class['class_id']}/Update")),
);
?>
<?php echo $this->renderPartial('_form', array('class'=>$class, 'models'=>$models)); ?>
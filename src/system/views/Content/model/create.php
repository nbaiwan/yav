<?php
$this->breadcrumbs=array(
	'内容模型管理',
	'模型列表',
	'添加模型',
);

$this->menu=array(
	array('label'=>'内容模型管理'),
	array('label'=>'模型列表', 'url'=>url($this->module->id . '/Content/Model/Index')),
	array('label'=>'添加模型', 'cur'=>true, 'url'=>url($this->module->id . '/Content/Model/Create')),
);
?>
<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>
<?php
if($class['class_parent_id']>0) {
	$this->breadcrumbs=array(
		'档案栏目管理',
		$parent_class_name,
		'添加子栏目',
	);
	$this->menu=array(
		array('label'=>'档案栏目管理'),
		array('label'=>'栏目列表', 'url'=>url($this->module->id . '/Content/Class/Index')),
		array('label'=>'添加顶级栏目', 'url'=>url($this->module->id . '/Content/Class/Create')),
		array('label'=>'添加子栏目', 'cur'=>true, 'url'=>url($this->module->id . "/Content/Class/{$class['class_parent_id']}/Create")),
	);
} else {
	$this->breadcrumbs=array(
		'档案栏目管理',
		'添加栏目',
	);
	$this->menu=array(
		array('label'=>'档案栏目管理'),
		array('label'=>'栏目列表', 'url'=>url($this->module->id . '/Content/Class/Index')),
		array('label'=>'添加顶级栏目', 'cur'=>true, 'url'=>url($this->module->id . '/Content/Class/Create')),
	);
}
?>
<?php echo $this->renderPartial('_form', array('class'=>$class, 'models'=>$models)); ?>
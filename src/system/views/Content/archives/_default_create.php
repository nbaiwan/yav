<?php
$this->breadcrumbs=array(
	'档案管理',
	'档案列表',
);

$this->menu=array(
	array('label'=>'档案管理'),
	array('label'=>'档案列表', 'url'=>url($this->module->id . '/Content/Archives/Index')),
	array('label'=>'添加档案', 'cur'=>true, 'url'=>url($this->module->id . '/Content/Archives/Create')),
);

$this->renderPartial("_{$_edit_template}_form",
	array(
		'archive'=>$archive,
		'games'=>$games,
		'classes'=>$classes,
	)
);
?>
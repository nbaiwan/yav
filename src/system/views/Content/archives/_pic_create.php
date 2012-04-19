<?php
$this->breadcrumbs=array(
	'文档管理',
	'图库管理',
	'图片列表',
);

$this->menu=array(
	array('label'=>'文档管理'),
	array('label'=>'图库管理', 'url'=>url($this->module->id . '/Content/2/Archives/Index')),
	array('label'=>'添加图片', 'cur'=>true, 'url'=>url($this->module->id . '/Content/2/Archives/Create')),
);

$this->renderPartial("_{$_edit_template}_form",
	array(
		'archive'=>$archive,
		'games'=>$games,
		'classes'=>$classes,
	)
);
?>
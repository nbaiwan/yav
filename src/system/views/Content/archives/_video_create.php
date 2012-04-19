<?php
$this->breadcrumbs=array(
	'游戏管理',
	'游戏视频',
	'添加视频',
);

$this->menu=array(
	array('label'=>'游戏管理'),
	array('label'=>'游戏视频', 'url'=>url($this->module->id . '/Content/3/13/Archives/Index')),
	array('label'=>'添加视频', 'cur'=>true, 'url'=>url($this->module->id . '/Content/3/13/Archives/Create')),
);

$this->renderPartial("_{$_edit_template}_form",
	array(
		'archive'=>$archive,
		'games'=>$games,
		'classes'=>$classes,
	)
);
?>
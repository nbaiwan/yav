<?php
$this->breadcrumbs=array(
	'游戏管理',
	'游戏截图',
	'截图列表',
);

$this->menu=array(
	array('label'=>'游戏管理'),
	array('label'=>'游戏截图', 'url'=>url($this->module->id . '/Content/2/12/Archives/Index')),
	array('label'=>'添加截图', 'cur'=>true, 'url'=>url($this->module->id . '/Content/2/12/Archives/Create')),
	array('label'=>'批量上传', 'url'=>url($this->module->id . '/Game/ScreenShot/Create')),
);

$this->renderPartial("_{$_edit_template}_form",
	array(
		'archive'=>$archive,
		'games'=>$games,
		'classes'=>$classes,
	)
);
?>
<?php
$this->breadcrumbs=array(
	'游戏管理',
	'游戏截图',
	'修改截图',
);

$this->menu=array(
	array('label'=>'游戏管理'),
	array('label'=>'游戏截图', 'url'=>url($this->module->id . '/Content/Archives/Index')),
	array('label'=>'添加截图', 'url'=>url($this->module->id . '/Content/Archives/Create')),
	array('label'=>'修改截图', 'cur'=>true, 'url'=>url($this->module->id . "/Content/Archives/{$archive['content_archives_id']}/Update")),
);

$this->renderPartial("_{$_edit_template}_form",
	array(
		'archive'=>$archive,
		'games'=>$games,
		'classes'=>$classes,
	)
);
?>
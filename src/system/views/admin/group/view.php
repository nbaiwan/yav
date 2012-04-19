<?php
$this->breadcrumbs=array(
	'Role Manager',
	'View Role',
);

$this->menu=array(
	array('label'=>'Role Index', 'url'=>url($this->module->id . '/Admin/Role/Index')),
);
?>
<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'RID',
		'RName',
		'RPIDs',
		'RShow',
		'RState',
	),
)); ?>

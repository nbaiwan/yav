<?php
$this->breadcrumbs=array(
	'Purview Manager',
	'View Purview',
);

$this->menu=array(
	array('label'=>'Purview Index', 'url'=>url($this->module->id . '/Admin/Purview/Index')),
);
?>
<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'PID',
		'PName',
		'PIdentify',
		'PState',
	),
)); ?>

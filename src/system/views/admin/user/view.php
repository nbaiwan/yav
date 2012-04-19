<?php
$this->breadcrumbs=array(
	'Admin Manager',
	'View Admin',
);

$this->menu=array(
	array('label'=>'Admin Manager'),
	array('label'=>'Admin Index', 'url'=>url($this->module->id . '/Admin/Admin/Index')),
	array('label'=>'Create Admin', 'url'=>url($this->module->id . '/Admin/Admin/Create')),
	array('label'=>'View Admin', 'cur'=>true, 'url'=>'#'),
);
?>
<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'AID',
		'AUserName',
		//'AUserPwd',
		//'ASalt',
		array(
			'name' => 'role.RName',
		),
		'RPIDs',
		array(
			'name' => 'ALastTime',
			'value' => date('Y-m-d H:i:s', $model->ALastTime),
		),
		array(
			'name' => 'ALastIp',
			'value' => long2ip($model->ALastIp),
		),
		'ATimes',
		array(
			'name' => 'AState',
			'value' => Common::getState($model->AState),
		),
		array(
			'name' => 'AAddTime',
			'value' => date('Y-m-d H:i:s', $model->AAddTime),
		),
	),
)); ?>

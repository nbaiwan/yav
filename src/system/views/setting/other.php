<?php
$this->breadcrumbs = array (
	'System Manager',
	'Setting Manager',
);

$this->menu=array(
	array('label'=>'Base Setting', 'url'=>url($this->module->id . '/Setting/Base')),
	array('label'=>'Cache Setting', 'url'=>url($this->module->id . '/Setting/Cache')),
	array('label'=>'Other Setting', 'cur'=>true, 'url'=>url($this->module->id . '/Setting/Other')),
);
?>
<?php echo $this->renderPartial('index', array('settings'=>$settings)); ?>
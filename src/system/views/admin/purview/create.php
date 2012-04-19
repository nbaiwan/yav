<?php
$this->breadcrumbs=array(
	'Purview Manager',
	'Create Purview',
);

$this->menu=array(
	array('label'=>'Purview Index', 'url'=>url($this->module->id . '/Admin/Purview/Index')),
);
?>
<?php
/*$this->breadcrumbs=array(
	'Purviews'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List Purview', 'url'=>array('index')),
	array('label'=>'Manage Purview', 'url'=>array('admin')),
);*/
?>
<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>
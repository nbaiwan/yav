<?php
$this->breadcrumbs=array(
	'Purviews'=>array('index'),
	$model->PID=>array('view','id'=>$model->PID),
	'Update',
);

$this->menu=array(
	array('label'=>'List Purview', 'url'=>array('index')),
	array('label'=>'Create Purview', 'url'=>array('create')),
	array('label'=>'View Purview', 'url'=>array('view', 'id'=>$model->PID)),
	array('label'=>'Manage Purview', 'url'=>array('admin')),
);
?>

<h1>Update Purview <?php echo $model->PID; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>
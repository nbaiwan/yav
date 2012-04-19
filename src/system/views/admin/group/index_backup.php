<?php
$this->breadcrumbs=array(
	'Roles Manager',
	'Roles Index',
);

$this->menu=array(
	array('label'=>'Roles Manager'),
	array('label'=>'Roles Index', 'cur'=>true, 'url'=>url($this->module->id . '/Admin/Role/Index')),
	array('label'=>'Create Role', 'url'=>url($this->module->id . '/Admin/Role/Create')),
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('role-grid', {
		data: $(this).serialize()
	});
	return false;
});
");
Yii::app()->getClientScript()->registerCssFile($this->module->assetsUrl . '/css/form.css');
?>

<div class="search-form" style="display:none">
<?php $this->renderPartial('_search',array(
	'model'=>$model,
)); ?>
</div><!-- search-form -->

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'role-grid',
	'dataProvider'=>$model->search(),
	//'filter'=>$model,
	'columns'=>array(
		'RID',
		'RName',
		array(
			'name' => 'RShow',
			//'type' => 'boolean',
			'value'=>'Common::getShow($data->RShow)',
		),
		'RRank',
		array(
			'name' => 'RState',
			'value'=>'Common::getState($data->RState)',
		),
		array(
			'class'=>'CButtonColumn',
			'template' => '{update}&nbsp;{delete}',
		),
	),
)); ?>

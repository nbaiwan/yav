<?php
$this->breadcrumbs=array(
	'内容模型管理',
	'模型列表',
	'字段管理',
);

$this->menu=array(
	array('label'=>'内容模型管理'),
	array('label'=>'模型列表', 'url'=>url($this->module->id . '/Content/Model/Index')),
	array('label'=>'字段管理', 'url'=>url($this->module->id . "/Content/Model/{$content_model_id}/Field/Index")),
	array('label'=>'添加字段', 'url'=>url($this->module->id . "/Content/Model/{$content_model_id}/Field/Create")),
	array('label'=>'字段修改', 'cur'=>true, 'url'=>url($this->module->id . "/Content/Model/{$content_model_id}/Field/{$field['content_model_field_id']}/Update")),
);
?>
<?php echo $this->renderPartial('_form', array('field'=>$field, 'content_model_id'=>$content_model_id)); ?>
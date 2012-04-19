<?php
$this->breadcrumbs=array(
	'采集管理',
	'模型管理',
	'模型列表',
);

$this->menu=array(
	array('label'=>'模型管理'),
	array('label'=>'模型列表', 'cur'=>true, 'url'=>url($this->module->id . '/Collect/Model/Index')),
	array('label'=>'添加模型', 'url'=>url($this->module->id . '/Collect/Model/Create')),
);
?>
	<table class="tb tb2 " id="tips">
		<tr>
			<th class="partition">技巧提示</th>
		</tr>
		<tr>
			<td class="tipsblock">
			<ul id="tipslis">
				<li><!--版主用户名为粗体，则表示该版主权限可继承到下级版块--></li>
			</ul>
			</td>
		</tr>
	</table>
	<?php
		$form=$this->beginWidget('CActiveForm', array(
			'id'=>'base-form',
			'enableAjaxValidation'=>false,
		));
	?>
	<table class="tb tb2 ">
		<tr class="header">
			<th width="100"><?php echo Yii::t('admincp', '模型编号'); ?></th>
			<th width="120"><?php echo Yii::t('admincp', '显示顺序'); ?></th>
			<th width="200"><?php echo Yii::t('admincp', '模型名称'); ?></th>
			<th width="100"><?php echo Yii::t('admincp', '模型标识'); ?></th>
			<th width="150"><?php echo Yii::t('admincp', '操作'); ?></th>
		</tr>
		<?php
			foreach($model['rows'] as $model) {
		?>
		<tr class="hover">
			<td><?php echo $model['collect_model_id']; ?></td>
			<td><?php echo $model['collect_model_rank']; ?></td>
			<td><?php echo $model['collect_model_name']; ?></td>
			<td><?php echo $model['collect_model_identify']; ?></td>
			<td>
				
				<a href="<?php echo url($this->module->id . "/Collect/Model/{$model['collect_model_id']}/Fields");?>">字段管理</a>
                                <a href="<?php echo url($this->module->id . "/Collect/Model/{$model['collect_model_id']}/Update");?>">编辑</a>
				<a href="<?php echo url($this->module->id . "/Collect/Model/{$model['collect_model_id']}/Delete");?>" onclick="return confirm('确定要删除采集模型<<?php echo $model['collect_model_name']; ?>>吗');">删除</a>
			</td>
		</tr>
		<?php
			}
		?>
	</table>
	<?php
		$this->endWidget();
	?>
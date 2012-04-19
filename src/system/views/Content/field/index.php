<?php
$this->breadcrumbs=array(
	'内容模型管理',
	'模型列表',
	'字段管理',
);

$this->menu=array(
	array('label'=>'内容模型管理'),
	array('label'=>'模型列表', 'url'=>url($this->module->id . '/Content/Model/Index')),
	array('label'=>'字段管理', 'cur'=>true, 'url'=>url($this->module->id . "/Content/Model/{$content_model_id}/Field/Index")),
	array('label'=>'添加字段', 'url'=>url($this->module->id . "/Content/Model/{$content_model_id}/Field/Create")),
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
			'id'=>'game-server-form',
			'enableAjaxValidation'=>false,
		));
	?>
	<table class="tb tb2 ">
		<tr class="header">
			<th width="80"><?php echo Yii::t('admincp', '编号'); ?></th>
			<th width="120"><?php echo Yii::t('admincp', '显示顺序'); ?></th>
			<th width="160"><?php echo Yii::t('admincp', '字段名称'); ?></th>
			<th width="160"><?php echo Yii::t('admincp', '字段标识'); ?></th>
			<th width="160"><?php echo Yii::t('admincp', '字段类型'); ?></th>
			<th width="160"><?php echo Yii::t('admincp', '字段长度'); ?></th>
			<th><div style="text-align:center;"><?php echo Yii::t('admincp', '操作'); ?></div></th>
		</tr>
		<?php
			foreach($fields as $field) {
		?>
		<tr class="hover">
			<td><?php echo $field['content_model_field_id']; ?></td>
			<td><input type="text" name="Field[content_model_field_rank][<?php echo $field['content_model_field_id']; ?>]" value="<?php echo $field['content_model_field_rank']; ?>" size="3" maxlength="3" /></td>
			<td><?php echo $field['content_model_field_name']; ?></td>
			<td><?php echo $field['content_model_field_identify']; ?></td>
			<td><?php echo ContentModelField::get_field_data_types($field['content_model_field_type']); ?></td>
			<td><?php echo $field['content_model_field_max_length']; ?></td>
			<td align="center">
				&nbsp;
				<a href="<?php echo url($this->module->id . "/Content/Model/{$field['content_model_id']}/Field/{$field['content_model_field_id']}/Update");?>">编辑</a>
				<?php
					if($field['content_model_field_is_system'] == 1) {
						echo '删除';
					} else {
				?>
				<a href="<?php echo url($this->module->id . "/Content/Model/{$field['content_model_id']}/Field/{$field['content_model_field_id']}/Delete");?>" onclick="return confirm('确定要删除内容模型<<?php echo $field['content_model_field_name']; ?>>吗');">删除</a>
				<?php
					}
				?>
			</td>
		</tr>
		<?php
			}
		?>
		<tr>
			<td colspan="20">
			<?php
				if(count($fields)>0) {
			?>
			<div class="cuspages left"><input type="reset" name="reset" value="重置" class="btn" />&nbsp;<input type="submit" name="submit" value="保存" class="btn" /></div>
			<?php
				}
			?>
			<div class="fixsel"></div>
			</td>
		</tr>
	</table>
	<?php
		$this->endWidget();
	?>
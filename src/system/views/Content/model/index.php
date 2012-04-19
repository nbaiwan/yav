<?php
$this->breadcrumbs=array(
	'内容模型管理',
	'模型列表',
);

$this->menu=array(
	array('label'=>'内容模型管理'),
	array('label'=>'模型列表', 'cur'=>true, 'url'=>url($this->module->id . '/Content/Model/Index')),
	array('label'=>'添加模型', 'url'=>url($this->module->id . '/Content/Model/Create')),
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
			<th width="80"><?php echo Yii::t('admincp', '显示顺序'); ?></th>
			<th width="200"><?php echo Yii::t('admincp', '模型名称'); ?></th>
			<th width="100"><?php echo Yii::t('admincp', '模型标识'); ?></th>
			<th width="100"><?php echo Yii::t('admincp', '默认模型'); ?></th>
			<th width="100"><?php echo Yii::t('admincp', '附加表'); ?></th>
			<th width="100" class="center"><?php echo Yii::t('admincp', '状态'); ?></th>
			<th width="120"><?php echo Yii::t('admincp', '操作'); ?></th>
		</tr>
		<?php
			foreach($models as $model) {
		?>
		<tr class="hover">
			<td><?php echo $model['content_model_id']; ?></td>
			<td><input type="text" name="Model[content_model_rank][<?php echo $model['content_model_id']; ?>]" value="<?php echo $model['content_model_rank']; ?>" size="3" maxlength="3" /></td>
			<td><?php echo $model['content_model_name']; ?></td>
			<td><?php echo $model['content_model_identify']; ?></td>
			<td><?php echo $model['content_model_is_default'] == 1 ? '默认模型' : '<a href="'.(url($this->module->id . "/Content/Model/{$model['content_model_id']}/Default")).'">设置默认</a>'; ?></td>
			<td>content_addons<?php echo $model['content_model_identify']; ?></td>
			<td align="center"><?php echo ContentModel::get_model_status($model['content_model_status']); ?> >>
				<?php
					if($model['content_model_status'] == ContentModel::STAT_ALLOW_YES) {
				?>
				<a href="<?php echo url($this->module->id . "/Content/Model/{$model['content_model_id']}/Disable"); ?>">禁用</a>
				<?php
					} else {
				?>
				<a href="<?php echo url($this->module->id . "/Content/Model/{$model['content_model_id']}/Enable"); ?>">启用</a>
				<?php
					}
				?>
			</td>
			<td>
				<a href="<?php echo url($this->module->id . "/Content/Model/{$model['content_model_id']}/Field/Index");?>">字段管理</a>
				<a href="<?php echo url($this->module->id . "/Content/Model/{$model['content_model_id']}/Update");?>">编辑</a>
				<?php
					if($model['content_model_is_system'] == 1) {
						echo '&nbsp;删除&nbsp;';
					} else {
				?>
				<a href="<?php echo url($this->module->id . "/Content/Model/{$model['content_model_id']}/Delete");?>" onclick="return confirm('确定要删除内容模型<<?php echo $model['content_model_name']; ?>>吗');">删除</a>
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
				if(count($models)>0) {
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
	<script type="text/javascript">
	<!--
		function srchforum() {
			var game_id = $('select[name=game_id]').val();
			var game_operating_id = $('select[name=game_operating_id]').val();
			var search_key = $('input[name=search_key]').val();
			
			var search_url = '<?php echo url($this->module->id . '/Game{game_id}{game_operating_id}/Model{search_key}/Index'); ?>';
			
			if(parseInt(game_id)>0) {
				search_url = search_url.replace('{game_id}', '/G' + parseInt(game_id));
			} else {
				search_url = search_url.replace('{game_id}', '');
			}
			if(parseInt(game_operating_id)>0) {
				search_url = search_url.replace('{game_operating_id}', '/S' + parseInt(game_operating_id));
			} else {
				search_url = search_url.replace('{game_operating_id}', '');
			}
			if(search_key != '') {
				search_url = search_url.replace('{search_key}', '/skey_' + search_key);
			} else {
				search_url = search_url.replace('{search_key}', '');
			}

			window.location.href = search_url;
			return false;
		}
	//-->
	</script>
<?php
$this->breadcrumbs=array(
	'广告管理',
	'广告位分类管理',
	'广告位分类列表',
);

$this->menu=array(
	array('label'=>'广告位分类管理'),
	array('label'=>'广告位分类列表', 'cur'=>true, 'url'=>url($this->module->id . '/Ad/Categories/Index')),
	array('label'=>'添加广告位分类', 'url'=>url($this->module->id . '/Ad/Categories/Create')),
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
			<th width="10"></th>
			<th width="100"><?php echo Yii::t('admincp', '编号'); ?></th>
			<th width="100"><?php echo Yii::t('admincp', '排序'); ?></th>
			<th width="200"><?php echo Yii::t('admincp', '分类名称'); ?></th>
			<th width="150"><?php echo Yii::t('admincp', '操作'); ?></th>
		</tr>
		<?php
			foreach($datas as $data) {
		?>
		<tr class="hover">
			<td><input type="checkbox" name="ad_categorise_id[]" value="<?php echo $data['ad_categories_id']; ?>" /></td>
			<td><?php echo $data['ad_categories_id']; ?></td>
			<td><input type="text" value="<?php echo $data['ad_categories_rank'];?>" size="8" name="ad_categories_rank[<?php echo $data['ad_categories_id']; ?>]" /></td>
			<td><input type="text" value="<?php echo $data['ad_categories_name']; ?>" size="30" name="ad_categories_name[<?php echo $data['ad_categories_id']; ?>]" /></td>
			<td>
				<a href="<?php echo url($this->module->id . '/Ad/Position/Index?ad_categories_id='.$data['ad_categories_id']);?>">广告位管理</a>
				<a href="<?php echo url($this->module->id . "/Ad/Categories/{$data['ad_categories_id']}/Update");?>">编辑</a>
				<?php
					if(!$data['ad_position_system']) {
				?>
				<a href="<?php echo url($this->module->id . "/Ad/Categories/{$data['ad_categories_id']}/Delete");?>" onclick="return window.confirm('确定要删除吗？')">删除</a>
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
				<div class="cuspages left">
					<input type="checkbox" name="checkall" id="checkall" value="1" /><label for="checkall">全选</label>
					<input type="submit" name="submit" value="批量修改" class="btn" onclick="$('#base-form').attr('action','<?php echo url($this->module->id . "/Ad/Categories/BatUpdate");?>');" />
				</div>
			</td>
		</tr>
	</table>
	<?php
		$this->endWidget();
	?>
<script type="text/javascript">
<!--
$('input[name=checkall]').click(function(){
	//
	if($(this).attr('checked')) {
		$('input[name="ad_categorise_id[]"]').attr('checked', 'checked');
	} else {
		$('input[name="ad_categorise_id[]"]').removeAttr('checked', '');
	}
})
</script>
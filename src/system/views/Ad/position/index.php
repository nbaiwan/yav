<?php
//默认URL参数
$_default_params = isset($_GET['ad_categories_id']) && !empty($_GET['ad_categories_id']) ? array(
	'ad_categories_id' => $_GET['ad_categories_id'],
) : array();

$this->breadcrumbs=array(
	'广告管理',
	'广告位管理',
	'广告位列表',
);

$this->menu=array(
	array('label'=>'广告位管理'),
	array('label'=>'广告位列表', 'cur'=>true, 'url'=>url($this->module->id . '/Ad/Position/Index', $_default_params)),
	array('label'=>'添加广告位', 'url'=>url($this->module->id . '/Ad/Position/Create', $_default_params)),
);
?>
	<style>
	<!--
		.partition a { padding: 0px 10px; display:block; float:left; }
		.partition a.cur { color:#f00; }
	-->
	</style>
	<table class="tb tb2 " id="tips">
		<tr>
			<th class="partition">
				<a href="<?php echo url($this->module->id . '/Ad/Position/Index');?>"<?php echo !isset($_REQUEST['ad_categories_id']) || empty($_REQUEST['ad_categories_id']) ? ' class="cur"' : ''; ?>>全部</a>
				<?php
					if(is_array($categories)) {
						foreach($categories as $_k=>$_v) {
							$cur = (@$_REQUEST['ad_categories_id'] == $_k) ? ' class="cur"' : '';
				?>
				<a href="<?php echo url($this->module->id . "/Ad/Position/Index", array('ad_categories_id' => $_k));?>"<?php echo $cur; ?>><?php echo $_v;?></a>
				<?php
						}
					}
				?>
			</th>
		</tr>
	</table>
	<?php
		$form=$this->beginWidget('CActiveForm', array(
			'id'=>'ad-position-form',
			'enableAjaxValidation'=>false,
		));
		$_type = array(1=>"固定",2=>"漂浮",3=>"弹窗");
	?>
	<table class="tb tb2 ">
		<tr class="header">
			<th width="50"><?php echo Yii::t('admincp', '编号'); ?></th>
			<th width="80"><?php echo Yii::t('admincp', '显示顺序'); ?></th>
			<th width="200"><?php echo Yii::t('admincp', '广告位名称'); ?></th>
			<th width="200"><?php echo Yii::t('admincp', '广告位标识'); ?></th>
			<th width="100"><?php echo Yii::t('admincp', '分类'); ?></th>
			<th width="100"><?php echo Yii::t('admincp', '是否系统'); ?></th>
			<th><?php echo Yii::t('admincp', 'js地址'); ?></th>
			<th width="150"><?php echo Yii::t('admincp', '操作'); ?></th>
		</tr>
		<?php
			foreach($datas['rows'] as $data) {
		?>
		<tr class="hover">
			<td><?php echo $data['ad_position_id']; ?></td>
			<td><input type="text" name="Position[ad_position_rank][<?php echo $data['ad_position_id']; ?>]" value="<?php echo $data['ad_position_rank']; ?>" size="3" maxlength="3" /></td>
			<td><?php echo $data['ad_position_name']; ?></td>
			<td><?php echo $data['ad_position_identify']; ?></td>
			<td><?php echo $categories[$data['ad_categories_id']]; ?></td>
			<td><?php echo $data['ad_position_system'] ? '<font color="red">是</font>' : '否'; ?></td>
			<td>
				<?php
					if(file_exists(AdPosition::get_js_path()."/".$data['ad_position_identify'].".js")) {
				?>
				http://www.wan123.com/static/ad/js/<?php echo $data['ad_position_identify']; ?>.js
				<?php
					}
				?>
			</td>
			<td>
				<a href="<?php echo url($this->module->id . "/Ad/Data/Index", array('ad_position_id' => $data['ad_position_id']));?>">素材管理</a>
				<a href="<?php echo url($this->module->id . "/Ad/Position/Js", $_default_params + array('id' => $data['ad_position_id']));?>">更新js</a>
				<a href="<?php echo url($this->module->id . "/Ad/Position/Update", $_default_params + array('id' => $data['ad_position_id']));?>">编辑</a>
				<?php
					if(!$data['ad_position_system']) {
				?>
				<a href="<?php echo url($this->module->id . "/Ad/Position/Delete", array('ad_position_id' => $data['ad_position_id']));?>" onclick="return window.confirm('确定要删除吗？')">删除</a>
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
				if(count($datas['rows'])>0) {
			?>
			<div class="cuspages left"><input type="reset" name="reset" value="重置" class="btn" />&nbsp;<input type="submit" name="submit" value="保存" class="btn" /></div>
			<?php
				}
			?>
			<div class="cuspages right">
				<?php
					$this->widget('CPager',array(
							'pages'=>$datas['pages'],
						)
					);
				?>
			</div>
			<div class="fixsel"></div>
			</td>
		</tr>
	</table>
	<?php
		$this->endWidget();
	?>
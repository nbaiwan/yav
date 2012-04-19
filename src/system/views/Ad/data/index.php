<?php
//默认URL参数
$_default_params = isset($_GET['ad_position_id']) && !empty($_GET['ad_position_id']) ? array(
	'ad_position_id' => $_GET['ad_position_id'],
) : array();

$this->breadcrumbs=array(
	'广告管理',
	'广告列表',
);

$this->menu=array(
	array('label'=>'广告管理'),
	array('label'=>'广告列表', 'cur'=>true, 'url'=>url($this->module->id . '/Ad/Data/Index', $_default_params)),
	array('label'=>'添加广告', 'url'=>url($this->module->id . '/Ad/Data/Create', $_default_params)),
);
?>
	<form action="" id="search-form" name="search-form" method="GET" onsubmit="return srchforum()">
		<div style="height: 30px; line-height: 30px;">
			广告位：
		    <select name="ad_position_id" id="ad_position_id">
		    	<option value="">不限制</option>
            <?php
            	if(is_array($pos_arr)){
            		foreach($pos_arr as $k=>$v){
            ?>
            	<option  value="<?php echo $v['ad_position_id'];?>" <?php if($_GET['ad_position_id']==$v['ad_position_id']){echo "selected";}?>><?php echo $v['ad_position_name'];?></option>
            <?php
					}
            	}
            ?>
            </select>
			关键字：
			<input type="text" id="Search_search_key" name="Search[search_key]" class="txt" value="<?php echo isset($_GET['search_key']) ? $_GET['search_key'] : ''; ?>" />
			<input type="submit" class="btn" value="搜索" />
		</div>
	</form>
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
			'id'=>'ad-data-form',
			'enableAjaxValidation'=>false,
		));
		$_type = array(1=>"文本",2=>"图片",3=>"flash",4=>"html");
	?>
	<table class="tb tb2 ">
		<tr class="header">
			<th width="50"><?php echo Yii::t('admincp', '编号'); ?></th>
			<th width="80"><?php echo Yii::t('admincp', '显示顺序'); ?></th>
			<th><?php echo Yii::t('admincp', '名称'); ?></th>
			<th width="200"><?php echo Yii::t('admincp', '广告位'); ?></th>
			<th width="150"><?php echo Yii::t('admincp', '链接'); ?></th>
			<th width="50"><?php echo Yii::t('admincp', '类型'); ?></th>
			<th width="100" class="center"><?php echo Yii::t('admincp', '是否显示'); ?></th>
			<th width="70" class="center"><?php echo Yii::t('admincp', '最后修改'); ?></th>
			<th width="150"><?php echo Yii::t('admincp', '操作'); ?></th>
		</tr>
		<?php
			foreach($datas['rows'] as $data) {
		?>
		<tr class="hover">
			<td><?php echo $data['ad_data_id']; ?></td>
			<td><input type="text" name="Data[ad_data_rank][<?php echo $data['ad_data_id']; ?>]" value="<?php echo $data['ad_data_rank']; ?>" size="3" maxlength="3" /></td>
			<td><?php echo $data['ad_data_subject']; ?></td>
			<td><?php echo $data['ad_position_name']; ?></td>
			<td><?php echo $data['ad_data_link']; ?></td>
			<td><?php echo $_type[$data['ad_data_type']]; ?></td>
			<td class="center"><input type="checkbox" name="Data[ad_data_is_show][<?php echo $data['ad_data_id']; ?>]" id="Data_ad_data_is_show_<?php echo $data['ad_data_id']; ?>" value="1"<?php echo ($data['ad_data_is_show'] == 1) ? ' checked="true"' : ''; ?> /></td>
            <td class="center"><?php echo $data['realname']; ?></td>
			<td>
				<a href="<?php echo url($this->module->id . "/Ad/Data/Update", $_default_params + array('id' => $data['ad_data_id']));?>">编辑</a>
				<a href="<?php echo url($this->module->id . "/Ad/Data/Delete", $_default_params + array('id' => $data['ad_data_id']));?>" onclick="return window.confirm('确定要删除吗？')">删除</a>
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
<script type="text/javascript">
function srchforum(){
	var search_url = "<?php echo url($this->module->id . '/Ad{ad_position_id}/Data/Index{search_key}');?>";
	var ad_position_id = $("#ad_position_id").val();
	var search_key = $("#Search_search_key").val();
	
	if(parseInt(ad_position_id) > 0) {
		search_url = search_url.replace(/{ad_position_id}/, '/' + ad_position_id);
	} else {
		search_url = search_url.replace(/{ad_position_id}/, '');
	}
	if(search_key != '') {
		search_url = search_url.replace(/{search_key}/, '?search_key=' + search_key);
	} else {
		search_url = search_url.replace(/{search_key}/, '');
	}
	
	window.location.href = search_url;
	
	return false;
}
</script>
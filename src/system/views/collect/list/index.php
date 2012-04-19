<script type="text/javascript" src="<?php echo $this->module->assetsUrl; ?>/datepicker/WdatePicker.js"></script>
<?php
$this->breadcrumbs=array(
	'采集管理',
	'列表管理',
	'列表列表',
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
        <div style="height: 30px; line-height: 30px;">
	  任务：
	  <select name="taskid" id="taskid">
	    <option value="">不限制</option>
<?php
foreach($task_arr as $v){
?>
<option value="<?php echo $v['collect_task_id'];?>" <?php if($_GET['taskid']==$v['collect_task_id']){echo "selected";}?>><?php echo $v['collect_task_name'];?></option>
<?php
}
?>
	  </select>&nbsp;
	  标题：
	  <input type="text" id="title" name="title" class="txt" value="<?php echo isset($_GET['title']) ? $_GET['title'] : ''; ?>" />
	  
	  采集日期：
	  <input type="text" id="datestart" name="datestart" class="txt" value="<?php echo isset($_GET['datestart']) ? $_GET['datestart'] : $_GET['day']; ?>" onFocus="WdatePicker({maxDate:'#F{$dp.$D(\'dateend\')||\'2020-10-01\'}'})" />
           - 
	  <input type="text" id="dateend" name="dateend" class="txt" value="<?php echo isset($_GET['dateend']) ? $_GET['dateend'] : ''; ?>" onFocus="WdatePicker({minDate:'#F{$dp.$D(\'datestart\')}',maxDate:'2020-10-01'})" />
          审核：
          <select name="check" id="check">
          <option value=""></option>
          <option value="1" <?php if($_GET['check']==1){echo "selected";}?>>是</option>
          <option value="0" <?php if($_GET['check']!="" && $_GET['check']!=1){echo "selected";}?>>否</option>
          </select>
          <input type="submit" class="btn" value="搜索" onclick="return dosearch()" />
	</div>
	<?php
		$form=$this->beginWidget('CActiveForm', array(
			'id'=>'collect-list-form',
			'action'=>url($this->module->id . '/Collect/List/Delete'),
			'enableAjaxValidation'=>false,
		));
	?>
        
	<table class="tb tb2 ">
		<tr class="header">
                	<th width="10"></th>
			<th width="30"><?php echo Yii::t('admincp', '编号'); ?></th>
                        <th width="50"><?php echo Yii::t('admincp', '缩略图'); ?></th>
			<th ><?php echo Yii::t('admincp', '标题'); ?></th>
                        <th width="150"><?php echo Yii::t('admincp', '所属任务'); ?></th>
                        <th width="150"><?php echo Yii::t('admincp', '所属游戏'); ?></th>
                        <th width="100"><?php echo Yii::t('admincp', '内容是否已采集'); ?></th>
                        <th width="100"><?php echo Yii::t('admincp', '是否已审核'); ?></th>
                        <th width="100"><?php echo Yii::t('admincp', '是否已发布'); ?></th>
                        <th width="80"><?php echo Yii::t('admincp', '采集时间'); ?></th>
			<th width="150"><?php echo Yii::t('admincp', '操作'); ?></th>
		</tr>
		<?php
			foreach($list['rows'] as $v) {
		?>
		<tr class="hover">
                	<td><input type="checkbox" name="List[collect_list_id][]" value="<?php echo $v['collect_list_id'];?>" /></td>
                        <td><?php echo $v['collect_list_id']; ?></td>
                        <td><?php if($v['collect_list_thumb']){ ?><img src="<?php echo $v['collect_list_thumb'];?>" width="50" class="thumb" /><?php }?></td>
			<td><a href="<?php echo $v['collect_list_url']; ?>" target="_blank"><?php echo $v['collect_list_title']; ?></a></td>
                        <td><?php echo $v['collect_task_name']; ?></td>
                        <td><?php echo $v['game_name']; ?></td>
                        <td><?php echo $v['collect_list_gathered'] ? "<font color='green'>是</font>" : "否"; ?></td>
                        <td><?php echo $v['collect_list_check'] ? "<font color='green'>是</font>" : "否"; ?></td>
                        <td><?php echo $v['collect_list_publiced'] ? "<font color='green'>是</font>" : "否"; ?></td>
                        <td><?php echo $v['collect_list_day']; ?></td>
			<td>
				<?php
                                if(!$v['collect_list_check']){
				?>
                                <a href="<?php echo url($this->module->id . "/Collect/List/{$v['collect_list_id']}/Check?value=1");?>">审核</a>
                                <?php
				}else{
				?>
                                <a href="<?php echo url($this->module->id . "/Collect/List/{$v['collect_list_id']}/Check?value=0");?>">取消审核</a>
                                <?php
				}
				?>
                                <a href="<?php echo url($this->module->id . "/Collect/List/{$v['collect_list_id']}/Delete");?>" onclick="return confirm('确定要删除吗');">删除</a>
                                <a href="<?php echo url($this->module->id . "/Content/Archives/{$v['collect_list_id']}/Put?url=".urlencode($v['collect_list_url']));?>" onclick="return confirm('确定要入库吗');">入库</a>
			</td>
		</tr>
		<?php
			}
		?>
		<tr>
			<td colspan="20">
                        <div class="cuspages left">
                        	<input type="checkbox" name="checkall" id="checkall" value="1" /><label for="checkall">全选</label>
                                <?php if($_GET['taskid']){?>
                                <input type="submit" name="submit" value="采集内容" class="btn" onclick="$('#collect-list-form').attr('action',collect_action);" />
                                <?php
				}
				?>
                                <input type="submit" name="submit" value="批量审核" class="btn" onclick="$('#collect-list-form').attr('action',publish_action);" />
                                <input type="submit" name="submit" value="批量取消审核" class="btn" onclick="$('#collect-list-form').attr('action',unpublish_action);" />
                                <input type="submit" name="submit" value="批量删除" class="btn" onclick="$('#collect-list-form').attr('action',delete_action);" />
                        </div>
			<?php
				if(count($list['rows'])>0) {
			?>
			
			<?php
				}
			?>
			<div class="cuspages right">
				<?php
					$this->widget('CPager',array(
							'pages'=>$list['pages'],
						)
					);
				?>
			</div>
			<div class="fixsel"></div>
			</td>
		</tr>
	</table>
        <div style="position:absolute; z-index:99; display:none" id="sourceimg_div"><img src="" id="sourceimg" /></div>
	<?php
		$this->endWidget();
	?>
	
<script type="text/javascript">
<!--
var delete_action = "<?php echo url($this->module->id . '/Collect/List/Delete');?>";
var publish_action = "<?php echo url($this->module->id . '/Collect/List/Check?value=1');?>";
var unpublish_action = "<?php echo url($this->module->id . '/Collect/List/Check?value=0');?>";
$('input[name=checkall]').click(function(){
	//
	if($(this).attr('checked')) {
		$('input[name="List[collect_list_id][]"]').attr('checked', 'checked');
	} else {
		$('input[name="List[collect_list_id][]"]').removeAttr('checked', '');
	}
})
//-->
function dosearch(){
	var taskid = $("#taskid").val();
	var title = $("#title").val();
	var check = $("#check").val();
	var datestart = $("#datestart").val();
	var dateend = $("#dateend").val();
	var url = '<?php echo url($this->module->id . '/Collect/List/Index');?>?taskid=' + taskid + '&title=' + title + '&datestart=' + datestart + '&dateend=' + dateend + "&check=" + check;
	location.href = url;
}
$(document).ready(function(){
	$(".thumb").hover(function(){
		var src = $(this).attr("src");
		var p = $(this).position();
		$("#sourceimg").attr("src",src);
		$("#sourceimg_div").css("left",p.left+$(this).width()).css("top",p.top).show();
	},function(){
		$("#sourceimg_div").hide();
	});
});
</script>
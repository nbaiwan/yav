<?php
$this->breadcrumbs = array (
	'生成静态',
	'内容页' 
);
?>
	<?php
		$form=$this->beginWidget('CActiveForm', array(
			'id'=>'base-form',
			'enableAjaxValidation'=>false,
			'action'=>url($this->module->id . '/Tools/Static/CreateArchives'),
		));
	?>
	<table class="tb tb2 ">
		<tr>
			<td width="10%" class="td27">模型:</td>
			<td class="vtop rowform">
                        <select name="content_model_id">
                                <option value=""></option>
                                <?php
                                foreach($models as $v){
                                ?>
                                <option value="<?php echo $v['content_model_id'];?>"><?php echo $v['content_model_name'];?></option>
                                <?php
                                }
                                ?>
                        </select>
                        </td>
			<td class="vtop tips2"></td>
		</tr>
                <tr>
			<td width="10%" class="td27">游戏:</td>
			<td class="rowform" style="width:600px">
                        <select name="game_id" id="game_id">
                                <option value=""></option>
                                <?php
                                foreach($games as $v){
                                ?>
                                <option value="<?php echo $v['game_id'];?>"><?php echo $v['game_name'];?></option>
                                <?php
                                }
                                ?>
                        </select>
                        <input type="text" value="关键字" onkeyup="dosearch(this.value)" onfocus="if(this.value==this.defaultValue){this.value='';}" onblur="if(this.value==''){this.value=this.defaultValue;}" />
                        </td>
			<td class="vtop tips2"></td>
		</tr>
                 <tr>
			<td width="10%" class="td27">分类:</td>
			<td class="vtop rowform">
                        <select name="class_id">
                                <option value=""></option>
                                
                                <?php
                                foreach($classes as $v){
                                ?>
                                <optgroup label="<?php echo $v['class_name'];?>">
                                <?php
                                	foreach($v['sub'] as $_v){
				?>
                                <option value="<?php echo $_v['class_id'];?>"><?php echo $_v['class_name'];?></option>
                                <?php
					}
				?>
                                </optgroup>
                                <?php
                                }
                                ?>
                        </select>
                        </td>
			<td class="vtop tips2"></td>
		</tr>
	</table>
	
	<table class="tb tb2 ">
		<tr>
			<td colspan="15">
			<div class="fixsel">
				<input type="submit" class="btn" id="submit_settingsubmit" name="settingsubmit" title="按 Enter 键可随时提交你的修改" value="提交" />
			</div>
			</td>
		</tr>
	</table>
	<?php
		$this->endWidget();
	?>
	<script type="text/javascript">
	function dosearch(v){
		if(v){
			var opt = $("#game_id").find("option:contains('"+ v +"')");
			if(opt.length){
				$(opt[0]).attr("selected","selected");
			}
		}
	}
	</script>
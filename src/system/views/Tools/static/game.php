<?php
$this->breadcrumbs = array (
	'生成静态',
	'游戏' 
);
?>
	<?php
		$form=$this->beginWidget('CActiveForm', array(
			'id'=>'base-form',
			'enableAjaxValidation'=>false,
			'action'=>url($this->module->id . '/Tools/Static/CreateGame'),
		));
	?>
	<table class="tb tb2 ">
		<tr>
			<td width="10%" class="td27">游戏分类:</td>
			<td class="vtop rowform">
                        <select name="game_categories_id">
                                <option value=""></option>
                                <?php
                                foreach($categories as $v){
                                ?>
                                <option value="<?php echo $v['game_categories_id'];?>"><?php echo $v['game_categories_name'];?></option>
                                <?php
                                }
                                ?>
                        </select>
                        </td>
			<td class="vtop tips2"></td>
		</tr>
                <tr>
			<td width="10%" class="td27">游戏特色:</td>
			<td class="vtop rowform">
                        <select name="game_feature_id">
                                <option value=""></option>
                                <?php
                                foreach($features as $v){
                                ?>
                                <option value="<?php echo $v['game_feature_id'];?>"><?php echo $v['game_feature_name'];?></option>
                                <?php
                                }
                                ?>
                        </select>
                        </td>
			<td class="vtop tips2"></td>
		</tr>
                <tr>
			<td width="10%" class="td27">游戏标签:</td>
			<td class="vtop rowform">
                        <select name="game_label_id">
                                <option value=""></option>
                                <?php
                                foreach($labels as $v){
                                ?>
                                <option value="<?php echo $v['game_label_id'];?>"><?php echo $v['game_label_name'];?></option>
                                <?php
                                }
                                ?>
                        </select>
                        </td>
			<td class="vtop tips2"></td>
		</tr>
                <tr>
			<td width="10%" class="td27">游戏画面:</td>
			<td class="vtop rowform">
                        <select name="game_screen_id">
                                <option value=""></option>
                                <?php
                                foreach($screens as $v){
                                ?>
                                <option value="<?php echo $v['game_screen_id'];?>"><?php echo $v['game_screen_name'];?></option>
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
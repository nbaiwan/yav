	<div id="cc">
	<?php
		$form=$this->beginWidget('CActiveForm', array(
			'id'=>'ad-categories-form',
			'enableAjaxValidation'=>false,
		));
	?>
	<table class="tb tb2 ">
		<tr>
			<td colspan="2" class="td27">分类名称:</td>
		</tr>
		<tr class="noborder">
			<td class="vtop rowform"><input type="text" name="F[ad_categories_name]" id="F[ad_categories_name]" value="<?php echo $data['ad_categories_name']; ?>" class="txt" /></td>
			<td class="vtop tips2"></td>
		</tr>
                <tr>
			<td colspan="2" class="td27">排序:</td>
		</tr>
		<tr class="noborder">
			<td class="vtop rowform"><input type="text" name="F[ad_categories_rank]" id="F[ad_categories_rank]" value="<?php echo $data['ad_categories_rank'] ? $data['ad_categories_rank'] : 1; ?>" class="txt" /></td>
			<td class="vtop tips2">*数值越小越靠前</td>
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
        </div>

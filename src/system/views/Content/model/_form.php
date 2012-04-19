	<style>
		.rowform { width:600px; }
		.tb2 td dl dt, .tb2 td dl dd { float:left; line-height:28px; margin-left:5px; }
		.tb2 td dl dt { width:90px; font-weight: 700;}
	</style>	
	<?php
		$form=$this->beginWidget('CActiveForm', array(
			'id'=>'game-server-form',
			'enableAjaxValidation'=>false,
		));
	?>
	<table class="tb tb2 ">
		<tr>
			<td colspan="5">
				<dl>
					<dt>模型名称:</dt>
					<dd>
						<input type="text" name="Model[content_model_name]" id="Model[content_model_name]" value="<?php echo $model['content_model_name']; ?>" />
					</dd>
				</dl>
			 </td>
		</tr>

		<tr>
			<td colspan="5">
				<dl>
					<dt>模型标识:</dt>
					<dd>
						<?php
							if($model['content_model_id'] != 0) {
								echo $model['content_model_identify'];
							} else {
						?>
						<input type="text" name="Model[content_model_identify]" id="Model[content_model_identify]" value="<?php echo $model['content_model_identify']; ?>" />
						<?php
							}
						?>由英文字母组成，不可修改。
					</dd>
				</dl>
			 </td>
		</tr>

		<tr>
			<td colspan="5">
				<dl>
					<dt>编辑模版:</dt>
					<dd>
						<input type="text" name="Model[content_model_edit_template]" id="Model[content_model_edit_template]" value="<?php echo $model['content_model_edit_template']; ?>" />
					</dd>
				</dl>
			 </td>
		</tr>

		<tr>
			<td colspan="5">
				<dl>
					<dt>列表模版:</dt>
					<dd>
						<input type="text" name="Model[content_model_list_template]" id="Model[content_model_list_template]" value="<?php echo $model['content_model_list_template']; ?>" />
					</dd>
				</dl>
			 </td>
		</tr>
		
	   <tr>
			<td colspan="5">
				<dl>
					<dt>默认模型:</dt>
					<dd>
						<input type="radio" name="Model[content_model_is_default]" id="Model_content_model_is_default_1" value="1"<?php echo $model['content_model_is_default'] == 1 ? ' checked="checked"' : ''; ?> /><label for="Model_content_model_is_default_1">是</label>
						<input type="radio" name="Model[content_model_is_default]" id="Model_content_model_is_default_0" value="0"<?php echo $model['content_model_is_default'] != 1 ? ' checked="checked"' : ''; ?> /><label for="Model_content_model_is_default_0">否</label>
					</dd>
				</dl>
			 </td>
		</tr>
		
		<tr>
			<td colspan="5">
				<dl>
					<dt>模型排序:</dt>
					<dd>
						<input type="text" name="Model[content_model_rank]" id="Model[content_model_rank]" value="<?php echo $model['content_model_rank']; ?>" style="width:60px;" maxlength="3" />
					</dd>
				</dl>
			 </td>
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
	<!--
		$('#game-server-form').submit(function(){
			var content_model_name = $("input[name='Model[content_model_name]']").val();
			var content_model_identify = $("input[name='Model[content_model_identify]']").val();
			
			if(content_model_name == '') {
				alert('内容模型名称不能为空！');
				$("input[name='Model[content_model_name]']").focus();
				return false;
			}
			
			if(content_model_identify == '') {
				alert('内容模型标识不能为空！');
				$("input[name='Model[content_model_identify]']").focus();
				return false;
			}

			return true;
		});
	//-->
	</script>
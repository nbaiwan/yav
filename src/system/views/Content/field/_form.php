	<style>
		.rowform { width:600px; }
		.tb2 td dl dt, .tb2 td dl dd { float:left; line-height:28px; }
		.tb2 td dl dt { width:90px; font-weight: 700;margin-left:10px;}
		.radio_text{
			width:160px;
			float:left;
		}
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
					<dt>字段名称:</dt>
					<dd>
						<input type="text" name="Field[content_model_field_name]" id="Field[content_model_field_name]" value="<?php echo $field['content_model_field_name']; ?>" />
					</dd>
				</dl>
			 </td>
		</tr>
		<tr>
			<td colspan="5">
				<dl>
					<dt>字段标识:</dt>
					<dd>
						<?php
							if($field['content_model_field_id'] != 0) {
								echo $field['content_model_field_identify'];
							} else {
						?>
						<input type="text" name="Field[content_model_field_identify]" id="Field[content_model_field_identify]" value="<?php echo $field['content_model_identify']; ?>" />
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
					<dt>提示信息:</dt>
					<dd>
						<input type="text" name="Field[content_model_field_tips]" id="Field[content_model_field_tips]" value="<?php echo $field['content_model_field_tips']; ?>" />
						提示信息。
					</dd>
				</dl>
			 </td>
		</tr>
		
		<tr>
			<td colspan="5">
				<dl>
					<dt>数据类型:</dt>
					<dd>
						<?php
						$k = 0;
							foreach(ContentModelField::get_field_data_types() as $_k=>$_v) {
								$k++;
								$selected = ($_k == $field['content_model_field_type']) ? ' checked="checked"' : '';
								echo "<div class=\"radio_text\"><input type=\"radio\" name=\"Field[content_model_field_type]\" id=\"Field_content_model_field_type_{$_k}\" value=\"{$_k}\"{$selected} /><label for=\"Field_content_model_field_type_{$_k}\">{$_v}</label></div>";
								if($k%4==0){
									echo "<br />";
								}
							}
						?>
					</dd>
				</dl>
			 </td>
		</tr>
		
		<tr>
			<td colspan="5">
				<dl>
					<dt>字段长度:</dt>
					<dd>
						<input type="text" name="Field[content_model_field_max_length]" id="Field[content_model_field_max_length]" value="<?php echo $field['content_model_field_max_length']; ?>" />
						必须填写，大于255为text类型。
					</dd>
				</dl>
			 </td>
		</tr>
		
		<tr>
			<td colspan="5">
				<dl>
					<dt>默认值:</dt>
					<dd>
						<textarea class="tarea" cols="80" id="Field[content_model_field_default]" name="Field[content_model_field_default]" onkeyup="textareasize(this, 0)" ondblclick="textareasize(this, 1)" rows="6"><?php echo $field['content_model_field_default']; ?></textarea>
						 <br />如果定义数据类型为select、radio、checkbox时此处填写可选择的选项，每行一个选项
					</dd>
				</dl>
			 </td>
		</tr>
		
		<tr>
			<td colspan="5">
				<dl>
					<dt>字段排序:</dt>
					<dd>
						<input type="text" name="Field[content_model_field_rank]" id="Field[content_model_field_rank]" value="<?php echo $field['content_model_field_rank']; ?>" style="width:60px;" maxlength="3" />
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
			var field_name = $("input[name='Field[content_model_field_name]']").val();
			var field_identify = $("input[name='Field[content_model_field_identify]']").val();
			var field_type = $("input[name='Field[content_model_field_type]']").val();
			var field_length = $("input[name='Field[content_model_field_max_length]']").val();
			
			if(field_name == '') {
				alert('字段名称不能为空！');
				$("input[name='Field[content_model_field_name]']").focus();
				return false;
			}
			
			if(field_identify == '') {
				alert('字段标识不能为空！');
				$("input[name='Field[content_model_field_identify]']").focus();
				return false;
			}
			
			if(field_type == '') {
				alert('请选择一个字段类型！');
				$("input[name='Field[content_model_field_type]']").focus();
				return false;
			}
			if(field_length == '') {
				alert('字段长度不能为空！');
				$("input[name='Field[content_model_field_max_length]']").focus();
				return false;
			}

			<?php
				if($field['content_model_field_id']>0) {
					echo <<<EOQ
if(field_length < parseInt('{$field['content_model_field_max_length']}')) {
				if(!confirm('字段长度比原长度短，可能导致数据丢失，是否继续。')) {
					$("input[name='Field[content_model_field_max_length]']").focus();
					return false;
				}
			}
EOQ;
				}
			?>

			return true;
		});

		$('input[name="Field[content_model_field_identify]"]').keyup(function(){
			var self = this;
			var identify = $(this).val();
			$.getJSON(
				'<?php echo url($this->module->id . "/Content/Model/{$content_model_id}/Field".($field['content_model_field_id']>0 ? "/{$field['content_model_field_id']}" : '')."/CheckIdentify?identify="); ?>' + identify,
				function(r) {
					if(r.ok) {
						$(self).parent().next().html('由英文字母组成，不可修改。 ');
						$('#submit_settingsubmit').removeAttr('disabled');
					} else {
						$('#submit_settingsubmit').attr('disabled', true);
						$(self).parent().next().html('<span style="color:#f00;">'+r.reason+'</span>');
					}
				}
			);
		});
	//-->
	</script>
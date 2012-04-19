	<style>
	<!--
	.rowform { width: 360px; }	
	-->
	</style>
	<?php
		$form=$this->beginWidget('CActiveForm', array(
			'id'=>'content-archives-categories-form',
			'enableAjaxValidation'=>false,
		));
	?>
	<input type="hidden" name="Class[class_parent_id]" value="<?php echo $class['class_parent_id']; ?>" />
	<table class="tb tb2 ">
		<tr>
			<td colspan="2" class="td27">栏目名称:</td>
		</tr>
		<tr class="noborder">
			<td class="vtop rowform"><input type="text" name="Class[class_name]" id="Class[class_name]" value="<?php echo $class['class_name']; ?>" class="txt" /></td>
			<td class="vtop tips2"></td>
		</tr>
		<tr>
			<td colspan="2" class="td27">内容模型:</td>
		</tr>
		<tr class="noborder">
			<td class="vtop rowform">
				<select name="Class[content_model_id]">
				<?php
					foreach($models as $_k=>$_v) {
						if($class['class_id']>0) {
							$selected = ($_v['content_model_id'] == $class['content_model_id']) ? ' selected' : '';
						} else {
							$selected = $_v['content_model_is_default'] ? ' selected' : '';
						}
				?>
					<option value="<?php echo $_v['content_model_id']; ?>"<?php echo $selected; ?>><?php echo $_v['content_model_name']; ?></option>
				<?php
					}
				?>
				</select>
			</td>
			<td class="vtop tips2"></td>
		</tr>
		
		<tr>
			<td colspan="2" class="td27">栏目标识:</td>
		</tr>
		<tr class="noborder">
			<td class="vtop rowform">
				<input type="text" name="Class[class_identify]" id="Class[class_identify]" value="<?php echo $class['class_identify']; ?>" class="txt" />
			</td>
			<td class="vtop tips2">由英文、数字或下划线组成，不可修改。 </td>
		</tr>
		
		<tr>
			<td colspan="2" class="td27">栏目列表选项:</td>
		</tr>
		<tr class="noborder">
			<td class="vtop rowform">
				<input type="radio" name="Class[class_is_default]" id="Class_class_is_default_0" value="0"<?php echo $class['class_is_default']==0 ? ' checked="checked"' : ''; ?> /><label for="Class_class_is_default_0">链接到默认页</label>
				<input type="radio" name="Class[class_is_default]" id="Class_class_is_default_1" value="1"<?php echo $class['class_is_default']==1 ? ' checked="checked"' : ''; ?> /><label for="Class_class_is_default_1">链接到列表第一页</label>
				<input type="radio" name="Class[class_is_default]" id="Class_class_is_default_2" value="2"<?php echo $class['class_is_default']==2 ? ' checked="checked"' : ''; ?> /><label for="Class_class_is_default_2">使用动态页 </label>
			</td>
			<td class="vtop tips2"> </td>
		</tr>
		
		<tr>
			<td colspan="2" class="td27">默认页的名称:</td>
		</tr>
		<tr class="noborder">
			<td class="vtop rowform">
				<input type="text" name="Class[class_default]" id="Class[class_default]" value="<?php echo $class['class_default']; ?>" class="txt" />
			</td>
			<td class="vtop tips2">如：index.html </td>
		</tr>
		
		<tr>
			<td colspan="2" class="td27">栏目属性:</td>
		</tr>
		<tr class="noborder">
			<td class="vtop rowform">
				<input type="radio" name="Class[class_is_part]" id="Class_class_is_part_<?php echo ContentArchivesClass::STAT_PART_FINAL_CLASS; ?>" value="<?php echo ContentArchivesClass::STAT_PART_FINAL_CLASS; ?>"<?php echo $class['class_is_part']==ContentArchivesClass::STAT_PART_FINAL_CLASS ? ' checked="checked"' : ''; ?> /><label for="Class_class_is_part_<?php echo ContentArchivesClass::STAT_PART_FINAL_CLASS; ?>">最终列表栏目（允许在本栏目发布文档，并生成文档列表）</label><br />
				<input type="radio" name="Class[class_is_part]" id="Class_class_is_part_<?php echo ContentArchivesClass::STAT_PART_COVER_CLASS; ?>" value="<?php echo ContentArchivesClass::STAT_PART_COVER_CLASS; ?>"<?php echo $class['class_is_part']==ContentArchivesClass::STAT_PART_COVER_CLASS ? ' checked="checked"' : ''; ?> /><label for="Class_class_is_part_<?php echo ContentArchivesClass::STAT_PART_COVER_CLASS; ?>">频道封面（栏目本身不允许发布文档）</label><br />
				<input type="radio" name="Class[class_is_part]" id="Class_class_is_part_<?php echo ContentArchivesClass::STAT_PART_EXTERNAL_LINKS; ?>" value="<?php echo ContentArchivesClass::STAT_PART_EXTERNAL_LINKS; ?>"<?php echo $class['class_is_part']==ContentArchivesClass::STAT_PART_EXTERNAL_LINKS ? ' checked="checked"' : ''; ?> /><label for="Class_class_is_part_<?php echo ContentArchivesClass::STAT_PART_EXTERNAL_LINKS; ?>">外部连接（在"文件保存目录"处填写网址）</label><br />
			</td>
			<td class="vtop tips2"></td>
		</tr>
		
		<tr>
			<td colspan="2" class="td27">封面模板:</td>
		</tr>
		<tr class="noborder">
			<td class="vtop rowform">
				<input type="text" name="Class[class_tempindex]" id="Class[class_tempindex]" value="<?php echo $class['class_tempindex']; ?>" class="txt" />
			</td>
			<td class="vtop tips2">如：index.html </td>
		</tr>
		
		<tr>
			<td colspan="2" class="td27">列表模板:</td>
		</tr>
		<tr class="noborder">
			<td class="vtop rowform">
				<input type="text" name="Class[class_templist]" id="Class[class_templist]" value="<?php echo $class['class_templist']; ?>" class="txt" />
			</td>
			<td class="vtop tips2"></td>
		</tr>
		
		<tr>
			<td colspan="2" class="td27">档案模板:</td>
		</tr>
		<tr class="noborder">
			<td class="vtop rowform">
				<input type="text" name="Class[class_temparticle]" id="Class[class_temparticle]" value="<?php echo $class['class_temparticle']; ?>" class="txt" />
			</td>
			<td class="vtop tips2"></td>
		</tr>
		
		<tr>
			<td colspan="2" class="td27">关键字[SEO]:</td>
		</tr>
		<tr class="noborder">
			<td class="vtop rowform">
				<input type="text" name="Class[class_seo_keywords]" id="Class[class_seo_keywords]" value="<?php echo $class['class_seo_keywords']; ?>" class="txt" />
			</td>
			<td class="vtop tips2"></td>
		</tr>
		
		<tr>
			<td colspan="2" class="td27">描述[SEO]:</td>
		</tr>
		<tr class="noborder">
			<td class="vtop rowform">
				<input type="text" name="Class[class_seo_description]" id="Class[class_seo_description]" value="<?php echo $class['class_seo_description']; ?>" class="txt" />
			</td>
			<td class="vtop tips2"></td>
		</tr>
		
		<tr>
			<td colspan="2" class="td27">是否显示:</td>
		</tr>
		<tr class="noborder">
			<td class="vtop rowform">
				<input type="radio" name="Class[class_is_show]" id="Class_class_is_show_1" value="1"<?php echo $class['class_is_show']==1 ? ' checked="checked"' : ''; ?> /><label for="Class_class_is_show_1">显示</label>
				<input type="radio" name="Class[class_is_show]" id="Class_class_is_show_0" value="0"<?php echo $class['class_is_show']!=1 ? ' checked="checked"' : ''; ?> /><label for="Class_class_is_show_0">隐藏</label>
			</td>
			<td class="vtop tips2"></td>
		</tr>
		
		<tr>
			<td colspan="2" class="td27">字段排序:</td>
		</tr>
		<tr class="noborder">
			<td class="vtop rowform"><input type="text" name="Class[class_rank]" id="Class[class_rank]" value="<?php echo $class['class_rank']; ?>" class="txt" style="width:60px;" maxlength="3" /></td>
			<td class="vtop tips2">（由低 -> 高）</td>
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
			var class_name = $("input[name='Class[class_name]']").val();
			var class_identify = $("input[name='Class[class_identify]']").val();
			
			if(class_name == '') {
				alert('内容模型名称不能为空！');
				$("input[name='Class[class_name]']").focus();
				return false;
			}
			
			if(class_identify == '') {
				alert('内容模型标识不能为空！');
				$("input[name='Class[class_identify]']").focus();
				return false;
			}

			return true;
		});
	//-->
	</script>
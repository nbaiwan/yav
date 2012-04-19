	<style>
		.rowform { width:600px; }
		.tb2 td dl dt, .tb2 td dl dd { float:left; line-height:28px; }
		.tb2 td dl dt { width:80px; font-weight: 700;}
		.plupload { cursor: pointer; }
	</style>
	<script type="text/javascript" src="<?php echo $this->module->assetsUrl . '/js/content.js'; ?>"></script>
	<?php
		$form=$this->beginWidget('CActiveForm', array(
			'id'=>'game-server-form',
			'enableAjaxValidation'=>false,
		));
	?>
	<input type="hidden" name="forward" value="<?php echo $_SERVER['HTTP_REFERER']; ?>" />
	<table class="tb tb2 ">
		<tr>
			<td colspan="5">
				<dl>
					<dt>图片标题:</dt>
					<dd><input type="text" name="Archive[content_archives_subject]" value="<?php echo $archive['content_archives_subject']; ?>" class="txt" style="width:388px" /></dd>
					<dt>标题颜色:</dt>
					<dd id="color_dd" style="position:relative;">
						<input type="text" name="Archive[content_archives_color]" value="<?php echo $archive['content_archives_color']; ?>" class="txt" style="width:80px;<?php echo $archive['content_archives_color'] ? "background:#{$archive['content_archives_color']}" : ''; ?>"  />
					</dd>
				</dl>
			</td>
		</tr>
		
		<tr>
			<td colspan="5">
				<dl>
					<dt>标题长度:</dt>
					<dd>您已输入<font color="red" id="title_count">0</font>个字符（１个汉字＝２个字符）</dd>
				</dl>
			</td>
		</tr>
		
		<tr>
			<td colspan="5">
				<dl>
					<dt>图片短标题:</dt>
					<dd><input type="text" name="Archive[content_archives_short_subject]" value="<?php echo $archive['content_archives_short_subject']; ?>" class="txt" style="width:200px;" /></dd>
				</dl>
			</td>
		</tr>
		
		<tr>
			<td colspan="5">
				<dl>
					<dt>自定义属性:</dt>
					<dd>
					<?php
						foreach(ContentArchives::get_archives_flag() as $_k=>$_v) {
							$checked = in_array($_k, $archive['content_archives_flag']) ? ' checked="checked"' : '';
							echo "<input type=\"checkbox\" name=\"Archive[content_archives_flag][]\" id=\"content_archives_flag_{$_k}\" value=\"{$_k}\"{$checked} /><label for=\"content_archives_flag_{$_k}\">{$_v}</label>";
						}
					?>
					</dd>
				</dl>
				<script type="text/javascript">
				<!--
					$('#content_archives_flag_<?php echo ContentArchives::STAT_ARCHIVES_FLAG_J; ?>').click(function(){
						if($(this).attr('checked') == 'checked') {
							$('#jump_url_id').show();
						} else {
							$('#jump_url_id').hide();
						}
					});
				//-->
				</script>
			</td>
		</tr>
		
		<tbody id="jump_url_id" style="display:<?php echo in_array(ContentArchives::STAT_ARCHIVES_FLAG_J, $archive['content_archives_flag']) ? '' : 'none'; ?>">
		<tr>
			<td colspan="5">
				<dl>
					<dt>跳转网址:</dt>
					<dd>
						<input type="text" name="Archive[content_archives_jump_url]" value="<?php echo $archive['content_archives_jump_url']; ?>" class="txt" style="width:300px;" />
					</dd>
				</dl>
			</td>
		</tr>
		</tbody>
		
		<tr>
			<td colspan="5">
				<dl>
					<dt>TAG标签:</dt>
					<dd>
						<?php
							$content_archives_tags = "";
							foreach($archive['content_archives_tags'] as $_k=>$_v) {
								$content_archives_tags .= $content_archives_tags ? ",{$_v['tags_name']}" : "{$_v['tags_name']}";
							}
						?>
						<input type="text" name="Archive[content_archives_tags]" value="<?php echo $content_archives_tags; ?>" class="txt" style="width:300px;" />
						(用','号分开，单个标签小于12字节，最多五个标签)
					</dd>
				</dl>
			</td>
		</tr>
		
		<tr>
			<td colspan="5">
				<dl>
					<dt>缩略图:</dt>
					<dd style="position: relative;">
						<input type="text" id="content_archives_thumb" name="Archive[content_archives_thumb]" value="<?php echo $archive['content_archives_thumb']; ?>" readonly="true" class="txt" style="width:300px;" />
						(鼠标放上去可以预览图片内容)
						<div>
							<span id="upload_file" class="btn">上传</span>
							<span id="processer" style="border:1px solid #ccc; display:block; width: 50px; height:2px;"><div style="width:0px;height:2px;background:#0f0;"></div></span>
						</div>
						
						<div id="preview" style="display:<?php echo $archive['content_archives_thumb'] ? 'block' : 'none'; ?>; position: absolute; border: 1px solid #ccc; padding: 3px; background: #fff;">
							<img src="<?php echo $archive['content_archives_thumb'] ? UploadFile::get_file_path($archive['content_archives_thumb'], 'images') : ''; ?>" width="200" />
						</div>
					</dd>
				</dl>
			</td>
		</tr>
		
		<tr>
			<td colspan="5">
				<dl>
					<dt>文章来源:</dt>
					<dd><input type="text" name="Archive[content_archives_source]" value="<?php echo $archive['content_archives_source']; ?>" class="txt" style="width:160px;" /></dd>
					<dt>作　　者:</dt>
					<dd><input type="text" name="Archive[content_archives_author]" value="<?php echo $archive['content_archives_author']; ?>" class="txt" style="width:160px;" /></dd>
				</dl>
			</td>
		</tr>
		
		<tr>
			<td colspan="5">
				<dl>
					<dt>相关游戏:</dt>
					<dd>
						<select name="Archive[game_id]" style="width:160px;">
							<option value="0">--------------------</option>
						    <?php
						    	foreach($games as $_k=>$_v) {
						    		$selected = ($archive['game_id'] == $_v['game_id']) ? ' selected="selected"' : '';
						    ?>
						    <option value="<?php echo $_v['game_id']; ?>"<?php echo $selected; ?>><?php echo $_v['game_name']; ?></option>
					    	<?php
						    	}
						    ?>
						</select>
						&nbsp;&nbsp;<input type="text" size="20" name="game_search_key" id="game_search_key" value="搜索关键字" onfocus="if(this.value==this.defaultValue){this.value='';}" onblur="if(this.value==''){this.value=this.defaultValue}" />
					</dd>
				</dl>
			</td>
		</tr>
		
		<tr>
			<td colspan="5">
				<dl>
					<dt>文档栏目:</dt>
					<dd>
						<select name="Archive[class_id]" style="width:160px;">
							<option value="0">请选择文档栏目</option>
						    <?php
						    	foreach($classes as $_k=>$_v) {
						    		if($_v['class_is_part'] == ContentArchivesClass::STAT_PART_FINAL_CLASS) {
						    ?>
						    <option value="<?php echo $_v['class_id']; ?>"<?php echo $_v['class_id'] == $archive['class_id'] ? ' selected' : ''; ?>><?php echo substr('├───', 0, ($_v['deepth']-1)*6) . $_v['class_name']; ?></option>
						    <?php
						    		} else if($_v['class_is_part'] == ContentArchivesClass::STAT_PART_COVER_CLASS) {
						    ?>
						    <optgroup label="<?php echo substr('├───', 0, ($_v['deepth']-1)*6) . $_v['class_name']; ?>"></optgroup>
						    <?php
						    		}
						    	}
						    ?>
						</select>
						<br />
						<div class="has_game_label">
							 <?php 
							 	if(is_array($archive['content_archives_classes'])) {
							 		foreach($archive['content_archives_classes'] as $k => $v) {
							 	?>
							 		<label class="selected_game_label"><?php echo $v['class_name'] ; ?><span title="关闭" class="selected_game_label_redcloseTip"></span><input type="hidden" value="<?php echo $v['class_id'] ; ?>" name="Archive[class_id][]"></label>
							 	<?php 
							 		}
							 	} 
							 ?>
						</div>
					</dd>
				</dl>
			</td>
		</tr>
		
		<tr>
			<td colspan="5">
				<dl>
					<dt>关键字:</dt>
					<dd><input type="text" name="Archive[content_archives_keywords]" value="<?php echo $archive['content_archives_keywords']; ?>" class="txt" style="width:388px;" /></dd>
				</dl>
			</td>
		</tr>
		
		<tr>
			<td colspan="5">
				<dl>
					<dt>文档摘要:</dt>
					<dd><textarea class="tarea" name="Archive[content_archives_summary]" onkeyup="textareasize(this, 0)" ondblclick="textareasize(this, 1)" rows="6" cols="50" style="width:388px; padding:0px 4px;"><?php echo $archive['content_archives_summary']; ?></textarea></dd>
				</dl>
			</td>
		</tr>
		
		<?php
			$this->renderPartial('_model_form', 
				array(
					'archive'=>$archive,
				)
			);
		?>
		
		<tr>
			<td colspan="5">
				<dl>
					<dt>权　　重:</dt>
					<dd><input type="text" name="Archive[content_archives_rank]" value="<?php echo $archive['content_archives_rank']; ?>" maxlength="3" class="txt" style="width:60px;" />&nbsp;(越小越靠前)</dd>
				</dl>
			</td>
		</tr>
		
		<tr>
			<td colspan="5">
				<dl>
					<dt>发布时间:</dt>
					<dd><input type="text" name="Archive[content_archives_pubtime]" value="<?php echo $archive['content_archives_pubtime']; ?>" class="txt" style="width:160px;" /></dd>
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
	<link type="text/css" rel="stylesheet" href="<?php echo $this->module->assetsUrl . '/ui-darkness/jquery-ui.css'; ?>" media="all" />
	<script type="text/javascript" src="<?php echo $this->module->assetsUrl . '/js/jquery.ui.datepicker.js'; ?>"></script>
	<script type="text/javascript" src="<?php echo $this->module->assetsUrl . '/js/jquery.ui.datepicker-zh-CN.js'; ?>"></script>
	
	<script type="text/javascript" src="<?php echo $this->module->assetsUrl . '/js/plupload/plupload.full.js'; ?>"></script>
	<script type="text/javascript" src="<?php echo $this->module->assetsUrl . '/js/plupload/plupload.gears.js'; ?>"></script>
	<script type="text/javascript" src="<?php echo $this->module->assetsUrl . '/js/plupload/plupload.browserplus.js'; ?>"></script>

	<script type="text/javascript">
	<!--
		var classes = [];
<?php
	    	foreach($classes as $_k=>$_v) {
	    		echo <<<EOQ
		classes[{$_v['class_id']}] = {"class_id":{$_v['class_id']},"class_name":"{$_v['class_name']}","deepth":{$_v['deepth']}};\r\n
EOQ;
	    	}
	    ?>
		
		$('#game-server-form').submit(function(){
			var subject = $("input[name='Archive[content_archives_subject]']").val();
			var class_id = $("select[name='Archive[class_id][]']").val();
			var body = tinyMCE.get('Archive_content_archives_body').getContent();
			
			if(subject == '') {
				alert('文档名称不能为空！');
				$("input[name='Archive[content_archives_subject]']").focus();
				return false;
			}
			
			if($("input[name='Archive[class_id][]']").length <= 0) {
				alert('请至少选择一个文档栏目！');
				return false;
			}
			
			if(body == '') {
				alert('文档内容不能为空！');
				$("textarea[name='Archive[content_archives_body]']").focus();
				return false;
			}

			return true;
		});

		//搜索
		$('#game_search_key').live('keyup', function(e){
			var search_key = $(this).val();
			var url = '<?php echo url($this->module->id . '/Game/Game/Search?search_key='); ?>' + search_key +'&t='+Math.random();
			$.getJSON(url, function(r){
				var element = $("select[name='Archive[game_id]']");
				//删除原来的选项
				if(r.count>0) {
					element.children().not(':first').remove();
					for(i in r.items) {
						element.append('<option value="'+i+'">'+r.items[i]+'</option>');
					}
					if(search_key != '') {
						element.children().not(':first').eq(0).attr('selected', true);
					}
				}
			});
		})

		//
		$(function() {
			var uploader = new plupload.Uploader({
				runtimes : 'flash,html5,silverlight,gears,browserplus',
				browse_button : 'upload_file',
				max_file_size : '10mb',
				chunk_size : '1mb',
				unique_names : true,
				url : '<?php echo url($this->module->id . '/Content/Archives/UploadFile'); ?>',
				flash_swf_url : '<?php echo $this->module->assetsUrl; ?>/js/plupload/plupload.flash.swf',
				silverlight_xap_url : '<?php echo $this->module->assetsUrl; ?>/js/plupload/plupload.silverlight.xap',
				filters : [
					{title : "Image files", extensions : "jpg,gif,png"}
				],
				multipart_params:{"upload_dir":"images","YII_CSRF_TOKEN":"<?php echo Yii::app()->request->csrfToken; ?>"}
			});

			uploader.bind('Init', function(up, params) {
				
			});
			
			uploader.init();
			
			uploader.bind('FilesAdded', function(up, files) {
				uploader.start();
				up.refresh();
			});
			
			uploader.bind('UploadProgress', function(up, file) {
				var p_width = Math.floor(file.percent/2);
				$('#processer').css({"width":p_width+"px"});
				
			});
			
			uploader.bind('Error', function(up, err) {
				up.refresh();
			});
			
			uploader.bind('FileUploaded', function(up, file, response) {
				var r = $.parseJSON(response.response);
				if(r.result != 0) {
					$('#processer').css({"width":"50px","background":"#f00"});
					$('#content_archives_thumb').val(r.md5_filename);
					$('#preview').children().attr("src", r.filename).show();
				} else {
					$('#processer').css({"width":"50px","background":"#00f"});
				}
			});
		});

		$('#content_archives_thumb').mouseover(function(){
			if($(this).val() != '') {
				$('#preview').show();
				$(this).bind('mouseout', function(){
					$('#preview').hide();
					$(this).unbind('mouseout');
				});
			}
		});

		//增加标签,可增加多个标签
		$("select[name='Archive[class_id]']").change( function() {
			var val = $(this).val();
			if(val!=0){
				var name = $(this).find('option:selected').text();
				var oldhtml = $('.has_game_label').html();
				if(oldhtml.indexOf('value="'+val+'"')==-1){//去掉重复添加项
					name = name.replace('├─','');
					var html = '<label class="selected_game_label">'+name+'<span title="关闭" class="selected_game_label_redcloseTip"></span><input type="hidden" value="'+val+'" name="Archive[class_id][]"></label>';
					$('.has_game_label').append( html );
				}
			}
			$(this).children('option:selected').removeAttr("selected");
		});
		
		//删除已增加的标签
		$(".selected_game_label").live('click',function() {
			var self = $(this);
			self.remove();
		});

		//
		$.datepicker.setDefaults($.datepicker.regional['zh-CN']);
		$('input[name="Archive[content_archives_pubtime]"]').datepicker();
	//-->
	</script>
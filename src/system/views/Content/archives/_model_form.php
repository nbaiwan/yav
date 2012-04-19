		<?php
			foreach($archive['content_model_colums'] as $_k=>$_v) {
				$_archive_value = $archive[$_v['content_model_field_identify']];
		?>
		<tr>
			<td colspan="5">
				<dl>
					<dt><?php echo $_v['content_model_field_name']; ?>:</dt>
					<dd style="position: relative;">
		<?php
			switch ($_v['content_model_field_type']) {
				case ContentModelField::DATA_TYPE_SINGLE_TEXT_VARCHAR:
				case ContentModelField::DATA_TYPE_SINGLE_TEXT_CHAR:
					echo "<input type=\"text\" name=\"Archive[{$_v['content_model_field_identify']}]\" id=\"Archive_{$_v['content_model_field_identify']}\" value=\"{$_archive_value}\" class=\"txt\" style=\"width:300px;\" />";
					break;
				case ContentModelField::DATA_TYPE_MULTI_TEXT:
					echo "<textarea name=\"Archive[{$_v['content_model_field_identify']}]\" id=\"Archive_{$_v['content_model_field_identify']}\" onkeyup=\"textareasize(this, 0)\" ondblclick=\"textareasize(this, 1)\" rows=\"6\" cols=\"50\" class=\"tarea\" style=\"width:388px; padding:0px 4px;\">{$_archive_value}</textarea>";
					break;
				case ContentModelField::DATA_TYPE_HTML_TEXT:
					$this->widget('ext.tinymce.ETinyMce', array(
							'name'=>"Archive[{$_v['content_model_field_identify']}]",
							'id'=>"Archive_{$_v['content_model_field_identify']}",
							'value'=>$_archive_value,
							'EditorTemplate' => 'full',
							'width' => '985px',
							'height' => '450px',
						)
					);
					break;
				case ContentModelField::DATA_TYPE_INTEGER:
					echo "<input type=\"text\" name=\"Archive[{$_v['content_model_field_identify']}]\" id=\"Archive_{$_v['content_model_field_identify']}\" value=\"{$_archive_value}\" maxlength=\"11\" style=\"width:150px;\" />";
					Yii::app()->clientScript->registerScript(uniqid(), "
						$('#Archive_{$_v['content_model_field_identify']}').keypress(
							function(e){
								if(e.which>13){
									var c = String.fromCharCode(e.which);
									if(!/\d/.test(c)){
										if((c != '-') || ($(this).val() != '')){
											e.preventDefault();
										}
									}
								}
							}
						);");
					break;
				case ContentModelField::DATA_TYPE_INTEGER_UNSIGNED:
					echo "<input type=\"text\" name=\"Archive[{$_v['content_model_field_identify']}]\" id=\"Archive_{$_v['content_model_field_identify']}\" value=\"{$_archive_value}\" maxlength=\"11\" style=\"width:150px;\" />";
					Yii::app()->clientScript->registerScript(uniqid(), "
						$('#Archive_{$_v['content_model_field_identify']}').keypress(
							function(e){
								if(e.which>13){
									var c = String.fromCharCode(e.which);
									if(!/\d/.test(c)){
										e.preventDefault();
									}
								}
							}
						);");
					break;
				case ContentModelField::DATA_TYPE_FLOAT:
					echo "<input type=\"text\" name=\"Archive[{$_v['content_model_field_identify']}]\" id=\"Archive_{$_v['content_model_field_identify']}\" value=\"{$_archive_value}\" maxlength=\"11\" style=\"width:150px;\" />";
					Yii::app()->clientScript->registerScript(uniqid(), "
						$('#Archive_{$_v['content_model_field_identify']}').keypress(
							function(e){
								if(e.which>13){
									var c = String.fromCharCode(e.which);
									if (!/[\.\d]/.test(c)){
										if((c != '-') || ($(this).val() != '')){
											e.preventDefault();
										}
									} else if ((c == '.') && ($(this).val().indexOf('.')>=0)) {
										e.preventDefault();
									} else if ((c == '.') && (($(this).val() == '') || ($(this).val() == '-'))) {
										e.preventDefault();
									} else if ($(this).val().indexOf('.')>=0) {
										if($(this).val().substr($(this).val().indexOf('.')).length>2) {
											e.preventDefault();
										}
									}
								}
							}
						);");
					break;
				case ContentModelField::DATA_TYPE_DATE_TIME:
					echo "<input type=\"text\" name=\"Archive[{$_v['content_model_field_identify']}]\" id=\"Archive_{$_v['content_model_field_identify']}\" value=\"{$_archive_value}\" style=\"width:150px;\" readonly=\"true\" />";
					Yii::app()->clientScript->registerScript(uniqid(), "$.datepicker.setDefaults($.datepicker.regional['zh-CN']);\r\n$('#Archive_{$_v['content_model_field_identify']}').datepicker();");
					break;
				case ContentModelField::DATA_TYPE_IMAGE:
					$content_archives_thumb = $_archive_value ? UploadFile::get_file_path($_archive_value, 'images') : '';
					$preview_display = $_archive_value ? 'block' : 'none';
					echo "<input type=\"text\" name=\"Archive[{$_v['content_model_field_identify']}]\" id=\"Archive_{$_v['content_model_field_identify']}\" value=\"{$_archive_value}\" style=\"width:300px;\" readonly=\"true\" />
					<div>
						<span id=\"upload_file_{$_v['content_model_field_identify']}\" class=\"btn\">上传</span>
						<span id=\"processer_{$_v['content_model_field_identify']}\" style=\"border:1px solid #ccc; display:block; width: 50px; height:2px;\"><div style=\"width:0px;height:2px;background:#0f0;\"></div></span>
					</div>
					<div id=\"preview_{$_v['content_model_field_identify']}\" style=\"display:{$preview_display}; position: absolute; border: 1px solid #ccc; padding: 3px; background: #fff; z-index:".(300-$_k)."\">
						<img src=\"{$content_archives_thumb}\" width=\"200\" />
					</div>";
					
					$request_url = url($this->module->id . '/Content/Archives/UploadFile');
					$csrf_token = Yii::app()->request->csrfToken;
					Yii::app()->clientScript->registerScript(uniqid(), "
						$(function() {
							var uploader_{$_v['content_model_field_identify']} = new plupload.Uploader({
								runtimes : 'flash,html5,silverlight,gears,browserplus',
								browse_button : 'upload_file_{$_v['content_model_field_identify']}',
								max_file_size : '10mb',
								chunk_size : '1mb',
								unique_names : true,
								url : '{$request_url}',
								flash_swf_url : '{$this->module->assetsUrl}/js/plupload/plupload.flash.swf',
								silverlight_xap_url : '{$this->module->assetsUrl}/js/plupload/plupload.silverlight.xap',
								filters : [
									{title : \"Image files\", extensions : \"jpg,gif,png\"}
								],
								multipart_params:{\"upload_dir\":\"images\",\"YII_CSRF_TOKEN\":\"{$csrf_token}\"}
							});
				
							uploader_{$_v['content_model_field_identify']}.bind('Init', function(up, params) {
								
							});
							
							uploader_{$_v['content_model_field_identify']}.init();
							
							uploader_{$_v['content_model_field_identify']}.bind('FilesAdded', function(up, files) {
								$('#processer_{$_v['content_model_field_identify']}').css({\"background\":\"#0f0\"});
								uploader_{$_v['content_model_field_identify']}.start();
								up.refresh();
							});
							
							uploader_{$_v['content_model_field_identify']}.bind('UploadProgress', function(up, file) {
								var p_width = Math.floor(file.percent/2);
								$('#processer_{$_v['content_model_field_identify']}').css({\"width\":p_width+\"px\"});
								
							});
							
							uploader_{$_v['content_model_field_identify']}.bind('Error', function(up, err) {
								up.refresh();
							});
							
							uploader_{$_v['content_model_field_identify']}.bind('FileUploaded', function(up, file, response) {
								var r = \$.parseJSON(response.response);
								if(r.result != 0) {
									$('#processer_{$_v['content_model_field_identify']}').css({\"width\":\"50px\",\"background\":\"#f00\"});
									$('#Archive_{$_v['content_model_field_identify']}').val(r.md5_filename);
									$('#preview_{$_v['content_model_field_identify']}').children().attr(\"src\", r.filename).show();
								} else {
									$('#processer_{$_v['content_model_field_identify']}').css({\"width\":\"50px\",\"background\":\"#00f\"});
								}
							});
						});
				
						$('#Archive_{$_v['content_model_field_identify']}').mouseover(function(){
							if($(this).val() != '') {
								$('#preview_{$_v['content_model_field_identify']}').show();
								$(this).bind('mouseout', function(){
									$('#preview_{$_v['content_model_field_identify']}').hide();
									$(this).unbind('mouseout');
								});
							}
						});
					
					", CClientScript::POS_READY);
						
					break;
				case ContentModelField::DATA_TYPE_MEDIA:
					$preview_display = $_archive_value ? 'block' : 'none';
					echo "<input type=\"text\" name=\"Archive[{$_v['content_model_field_identify']}]\" id=\"Archive_{$_v['content_model_field_identify']}\" value=\"{$_archive_value}\" style=\"width:300px;\" readonly=\"true\" />
					<div>
						<span id=\"upload_file_{$_v['content_model_field_identify']}\" class=\"btn\">上传</span>
						<span id=\"processer_{$_v['content_model_field_identify']}\" style=\"border:1px solid #ccc; display:block; width: 50px; height:2px;\"><div style=\"width:0px;height:2px;background:#0f0;\"></div></span>
					</div>";
					
					$request_url = url($this->module->id . '/Content/Archives/UploadFile');
					$csrf_token = Yii::app()->request->csrfToken;
					Yii::app()->clientScript->registerScript(uniqid(), "
						$(function() {
							var uploader_{$_v['content_model_field_identify']} = new plupload.Uploader({
								runtimes : 'flash,html5,silverlight,gears,browserplus',
								browse_button : 'upload_file_{$_v['content_model_field_identify']}',
								max_file_size : '10mb',
								chunk_size : '1mb',
								unique_names : true,
								url : '{$request_url}',
								flash_swf_url : '{$this->module->assetsUrl}/js/plupload/plupload.flash.swf',
								silverlight_xap_url : '{$this->module->assetsUrl}/js/plupload/plupload.silverlight.xap',
								filters : [
									{title : \"Image files\", extensions : \"avi,mpg,mpeg,mov,wmv,rm,rmvb,mp4,swf,flv,mp3,wma\"}
								],
								multipart_params:{\"upload_dir\":\"video\",\"YII_CSRF_TOKEN\":\"{$csrf_token}\"}
							});
				
							uploader_{$_v['content_model_field_identify']}.bind('Init', function(up, params) {
								
							});
							
							uploader_{$_v['content_model_field_identify']}.init();
							
							uploader_{$_v['content_model_field_identify']}.bind('FilesAdded', function(up, files) {
								$('#processer_{$_v['content_model_field_identify']}').css({\"background\":\"#0f0\"});
								uploader_{$_v['content_model_field_identify']}.start();
								up.refresh();
							});
							
							uploader_{$_v['content_model_field_identify']}.bind('UploadProgress', function(up, file) {
								var p_width = Math.floor(file.percent/2);
								$('#processer_{$_v['content_model_field_identify']}').css({\"width\":p_width+\"px\"});
								
							});
							
							uploader_{$_v['content_model_field_identify']}.bind('Error', function(up, err) {
								up.refresh();
							});
							
							uploader_{$_v['content_model_field_identify']}.bind('FileUploaded', function(up, file, response) {
								var r = \$.parseJSON(response.response);
								if(r.result != 0) {
									$('#processer_{$_v['content_model_field_identify']}').css({\"width\":\"50px\",\"background\":\"#f00\"});
									$('#Archive_{$_v['content_model_field_identify']}').val(r.md5_filename);
								} else {
									$('#processer_{$_v['content_model_field_identify']}').css({\"width\":\"50px\",\"background\":\"#00f\"});
								}
							});
						});
					", CClientScript::POS_READY);
					break;
				case ContentModelField::DATA_TYPE_ATTACH_OTHER:
					$preview_display = $_archive_value ? 'block' : 'none';
					echo "<input type=\"text\" name=\"Archive[{$_v['content_model_field_identify']}]\" id=\"Archive_{$_v['content_model_field_identify']}\" value=\"{$_archive_value}\" style=\"width:300px;\" readonly=\"true\" />
					<div>
						<span id=\"upload_file_{$_v['content_model_field_identify']}\" class=\"btn\">上传</span>
						<span id=\"processer_{$_v['content_model_field_identify']}\" style=\"border:1px solid #ccc; display:block; width: 50px; height:2px;\"><div style=\"width:0px;height:2px;background:#0f0;\"></div></span>
					</div>";
					
					$request_url = url($this->module->id . '/Content/Archives/UploadFile');
					$csrf_token = Yii::app()->request->csrfToken;
					Yii::app()->clientScript->registerScript(uniqid(), "
						$(function() {
							var uploader_{$_v['content_model_field_identify']} = new plupload.Uploader({
								runtimes : 'flash,html5,silverlight,gears,browserplus',
								browse_button : 'upload_file_{$_v['content_model_field_identify']}',
								max_file_size : '10mb',
								chunk_size : '1mb',
								unique_names : true,
								url : '{$request_url}',
								flash_swf_url : '{$this->module->assetsUrl}/js/plupload/plupload.flash.swf',
								silverlight_xap_url : '{$this->module->assetsUrl}/js/plupload/plupload.silverlight.xap',
								filters : [
									{title : \"Image files\", extensions : \"zip,rar,doc,docx,xls,xlsx,ppt,pptx\"}
								],
								multipart_params:{\"upload_dir\":\"other\",\"YII_CSRF_TOKEN\":\"{$csrf_token}\"}
							});
				
							uploader_{$_v['content_model_field_identify']}.bind('Init', function(up, params) {
								
							});
							
							uploader_{$_v['content_model_field_identify']}.init();
							
							uploader_{$_v['content_model_field_identify']}.bind('FilesAdded', function(up, files) {
								$('#processer_{$_v['content_model_field_identify']}').css({\"background\":\"#0f0\"});
								uploader_{$_v['content_model_field_identify']}.start();
								up.refresh();
							});
							
							uploader_{$_v['content_model_field_identify']}.bind('UploadProgress', function(up, file) {
								var p_width = Math.floor(file.percent/2);
								$('#processer_{$_v['content_model_field_identify']}').css({\"width\":p_width+\"px\"});
								
							});
							
							uploader_{$_v['content_model_field_identify']}.bind('Error', function(up, err) {
								up.refresh();
							});
							
							uploader_{$_v['content_model_field_identify']}.bind('FileUploaded', function(up, file, response) {
								var r = \$.parseJSON(response.response);
								if(r.result != 0) {
									$('#processer_{$_v['content_model_field_identify']}').css({\"width\":\"50px\",\"background\":\"#f00\"});
									$('#Archive_{$_v['content_model_field_identify']}').val(r.md5_filename);
								} else {
									$('#processer_{$_v['content_model_field_identify']}').css({\"width\":\"50px\",\"background\":\"#00f\"});
								}
							});
						});
					", CClientScript::POS_READY);
					break;
				case ContentModelField::DATA_TYPE_SELECT:
					$_options = preg_split('/\r\n|\r|\n/', $_v['content_model_field_default']);
					$_r = "<select name=\"Archive[{$_v['content_model_field_identify']}]\" id=\"Archive_{$_v['content_model_field_identify']}\" style=\"width:200px;\">";
					$_r .= "	<option value=\"\">------------------------------</option>";
					foreach($_options as $__k=>$__v) {
						$selected = ($_archive_value == $__v) ? ' selected' : '';
						$_r .= "	<option value=\"{$__v}\"{$selected}>{$__v}</option>\r\n";
					}
					$_r .= "</select>";
					echo $_r;
					break;
				case ContentModelField::DATA_TYPE_RADIO:
					$_options = preg_split('/\r\n|\r|\n/', $_v['content_model_field_default']);
					foreach($_options as $__k=>$__v) {
						$checked = ($_archive_value == $__v) ? ' checked="true"' : '';
						echo "<input type=\"radio\" name=\"Archive[{$_v['content_model_field_identify']}]\" id=\"Archive_{$_v['content_model_field_identify']}_{$__k}\" value=\"{$__v}\"{$checked} /><label for=\"Archive_{$_v['content_model_field_identify']}_{$__k}\">$__v</label>";
					}
					break;
				case ContentModelField::DATA_TYPE_CHECKBOX:
					$_archive_value = is_array($_archive_value) ? $_archive_value : array();
					$_options = preg_split('/\r\n|\r|\n/', $_v['content_model_field_default']);
					foreach($_options as $__k=>$__v) {
						$checked = (in_array($__v, $_archive_value)) ? ' checked="true"' : '';
						echo "<input type=\"checkbox\" name=\"Archive[{$_v['content_model_field_identify']}][]\" id=\"Archive_{$_v['content_model_field_identify']}_{$__k}\" value=\"{$__v}\"{$checked} /><label for=\"Archive_{$_v['content_model_field_identify']}_{$__k}\">$__v</label>";
					}
					break;
			}
			
			if(!empty($_v['content_model_field_tips'])) {
				echo "({$_v['content_model_field_tips']})";
			}
		?>
					</dd>
				</dl>
			</td>
		</tr>
		<?php
			}
		?>
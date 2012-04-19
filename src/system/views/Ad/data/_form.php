	<?php
		$form=$this->beginWidget('CActiveForm', array(
			'id'=>'base-form',
			'enableAjaxValidation'=>false,
		));
	?>
        <style type="text/css">
	.rowform .txt{
		width:300px
	}
	</style>
        <script type="text/javascript" src="<?php echo $this->module->assetsUrl; ?>/datepicker/WdatePicker.js"></script>
	<table class="tb tb2 ">
        	<tr>
			<td colspan="2" class="td27">广告位:</td>
		</tr>
        	<tr class="noborder">
			<td class="vtop rowform">
                        <select name="F[ad_position_id]" onchange="load_relative(this.value)">
                        <option value=""></option>
                        <?php
			$position_relative_arr = array();
			if(is_array($position_arr) && count($position_arr)){
                        	foreach($position_arr as $v){
					if($v['ad_position_relative_type']){
						$position_relative_arr[$v['ad_position_id']] = $v['ad_position_relative_type'];
					}
			?>
                        <option value="<?php echo $v['ad_position_id'];?>" <?php if($data['ad_position_id']==$v['ad_position_id']){echo "selected";}?>><?php echo $v['ad_position_name'];?></option>
                        <?php
				}
			
			}
			?>
                        </select>
                        </td>
                        
			<td class="vtop tips2"></td>
		</tr>
                <tr class="relative" style="display:none">
			<td colspan="2" class="td27">关联:</td>
		</tr>
        	<tr class="noborder relative" style="display:none">
			<td class="vtop" colspan="2">
                        <select name="F[ad_data_relative_id]" onchange="load_info(this)" id="ad_data_relative_id">
                        <option value="">========</option>
                        </select>
                        <input type="text" size="20" name="search_key" id="search_key" value="搜索关键字" onfocus="if(this.value==this.defaultValue){this.value='';}" onblur="if(this.value==''){this.value=this.defaultValue}" />
                        </td>
		</tr>
                <tr>
			<td colspan="2" class="td27">所属页面(不选择则为全部):</td>
		</tr>
        	<tr class="noborder">
			<td class="vtop rowform" colspan="2">
                        <?php
			if(is_array($pages) && count($pages)){
                        	foreach($pages as $k=>$v){
			?>
                        <input type="checkbox" name="F[ad_data_page][]" value="<?php echo $k;?>" <?php if(is_array($data['ad_data_page']) && in_array($k,$data['ad_data_page'])){echo "checked";}?> /> <?php echo $v;?>
                        <?php
				}
			
			}
			?>
                        </td>

		</tr>
        	<tr>
			<td colspan="2" class="td27">类型:</td>
		</tr>
        	<tr class="noborder">
			<td class="vtop rowform">
                        <input type="radio" name="F[ad_data_type]" value="1" id="ad_data_type_1" <?php if($data['ad_data_type'] == 1 || !$data['ad_data_type']){echo "checked";}?> />文本
                        <input type="radio" name="F[ad_data_type]" value="2" id="ad_data_type_2" <?php if($data['ad_data_type'] == 2){echo "checked";}?> />图片
                        <input type="radio" name="F[ad_data_type]" value="3" id="ad_data_type_3" <?php if($data['ad_data_type'] == 3){echo "checked";}?> />flash
                        <input type="radio" name="F[ad_data_type]" value="4" id="ad_data_type_4" <?php if($data['ad_data_type'] == 4){echo "checked";}?> />html
                        </td>
                        
			<td class="vtop tips2"></td>
		</tr>
        	<tr>
			<td colspan="2" class="td27">名称:</td>
		</tr>
		<tr class="noborder">
			<td class="vtop rowform"><input type="text" name="F[ad_data_subject]" id="F[ad_data_subject]" value="<?php echo $data['ad_data_subject']; ?>" class="txt" /></td>
			<td class="vtop tips2" id="ad_data_subject_tip"></td>
		</tr>
        	<tr>
			<td colspan="2" class="td27">链接:</td>
		</tr>
		<tr class="noborder">
			<td class="vtop rowform">
                        <input type="text" name="F[ad_data_link]" id="F[ad_data_link]" value="<?php echo $data['ad_data_link']; ?>" size="100" />
                        </td>
                        <td class="vtop tips2" id="ad_data_link_tip"></td>
		</tr>
                <tr>
			<td colspan="2" class="td27">开始时间:</td>
		</tr>
		<tr class="noborder">
			<td class="vtop rowform" colspan="2">
                        <input type="text" name="F[ad_data_expire_start]" id="F[ad_data_expire_start]" value="<?php echo $data['ad_data_expire_start']; ?>" class="txt" onFocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss',maxDate:'#F{$dp.$D(\'F[ad_data_expire_end]\')||\'2020-10-01\'}'})" />
                        </td>
		</tr>
                <tr>
			<td colspan="2" class="td27">结束时间:</td>
		</tr>
		<tr class="noborder">
			<td class="vtop rowform" colspan="2">
                        <input type="text" name="F[ad_data_expire_end]" id="F[ad_data_expire_end]" value="<?php echo $data['ad_data_expire_end']; ?>" class="txt"  onFocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss',minDate:'#F{$dp.$D(\'F[ad_data_expire_start]\')||\'2020-10-01\'}'})" readonly="readonly" />
                        </td>
		</tr>
                <tr class="data_type_2 data_type" style="display:none">
			<td colspan="2" class="td27">上传图片(jpg、gif、png):</td>
		</tr>
		<tr class="noborder data_type_2 data_type" style="display:none;">
			<td class="vtop rowform" colspan="2">
                        <dl>
                        	<dt style="float:left;">
                        <input type="text" name="F[ad_data_image_md5]" id="F_ad_data_image_md5" value="<?php echo $data['ad_data_image_md5']; ?>" class="txt" readonly="readonly" />
                        	</dt>
                                <dd style="float:left; margin-left:5px">
				<span id="upload_img_file" class="btn">上传</span>
				<span id="img_processer" style="border:1px solid #ccc; display:block; width: 50px; height:2px;margin-top:3px;"><div style="width:0px;height:2px;background:#0f0;"></div></span>
                                </dd>
                        </dl>
                        </td>
                        
			<td class="vtop tips2"></td>
		</tr>
                
                <tr class="data_type_3 data_type" style="display:none">
			<td colspan="2" class="td27">上传flash(swf、flv):</td>
		</tr>
		<tr class="noborder data_type_3 data_type" style="display:none">
			<td class="vtop rowform" colspan="2">
                        <dl>
                        	<dt style="float:left;">
                        <input type="text" name="F[ad_data_flash_md5]" id="F_ad_data_flash_md5" value="<?php echo $data['ad_data_flash_md5']; ?>" class="txt" readonly="readonly" />
                        	</dt>
                                <dd style="float:left; margin-left:5px">
				<span id="upload_swf_file" class="btn">上传</span>
				<span id="swf_processer" style="border:1px solid #ccc; display:block; width: 50px; height:2px;margin-top:3px;"><div style="width:0px;height:2px;background:#0f0;"></div></span>
                                </dd>
                        </dl>
                        </td>
		</tr>
                <tr class="data_type_4 data_type" style="display:none">
			<td colspan="2" class="td27">html内容:</td>
		</tr>
		<tr class="noborder data_type_4 data_type" style="display:none">
			<td class="vtop rowform"><textarea rows="6" ondblclick="textareasize(this, 1)" onkeyup="textareasize(this, 0)" name="F[ad_data_html]" id="F[ad_data_html]" cols="50" class="tarea"><?php echo $data['ad_data_html']; ?></textarea></td>
			<td class="vtop tips2"><br />
			双击输入框可扩大/缩小</td>
		</tr>
		<tr>
			<td colspan="2" class="td27">排序:</td>
		</tr>
		<tr class="noborder">
			<td class="vtop rowform"><input type="text" name="F[ad_data_rank]" id="F[ad_data_rank]" value="<?php echo $data['ad_data_rank']?$data['ad_data_rank']:1; ?>" class="txt" style="width:60px;" maxlength="3" /></td>
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
         <div id="image_view_div" style="display:none;z-index:9; position:absolute"></div>
	<?php
		$this->endWidget();
	?>
<script type="text/javascript" src="<?php echo $this->module->assetsUrl; ?>/js/plupload/plupload.full.js"></script>
<script type="text/javascript" src="<?php echo $this->module->assetsUrl; ?>/js/plupload/plupload.gears.js"></script>
<script type="text/javascript" src="<?php echo $this->module->assetsUrl; ?>/js/plupload/plupload.browserplus.js"></script>
<script type="text/javascript">

$(function() {
	$('input[name=F\\[ad_data_type\\]]').click(function(){
		var v = $(this).val();
		show_type_form(v);
	});
});
function show_type_form(v){
	$(".data_type").hide();
	$(".data_type_"+v).show();
	$('.plupload').remove();
	if(v == 2) {
		var uploader = new plupload.Uploader({
			runtimes : 'flash,html5,silverlight,browserplus,gears',
			browse_button : 'upload_img_file',
			max_file_size : '10mb',
			chunk_size : '1mb',
			unique_names : true,
			url : '<?php echo url($this->module->id . '/Ad/Data/UploadFile'); ?>',
			flash_swf_url : '<?php echo $this->module->assetsUrl; ?>/js/plupload/plupload.flash.swf',
			silverlight_xap_url : '<?php echo $this->module->assetsUrl; ?>/js/plupload/plupload.silverlight.xap',
			filters : [
				{title : "Image files", extensions : "jpg,jpeg,gif,png"}
			],
			multipart_params:{"upload_dir":"ad/images","YII_CSRF_TOKEN":"<?php echo Yii::app()->request->csrfToken; ?>","type":"image"}
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
			$('#img_processer').css({"width":p_width+"px"});
			
		});
		
		uploader.bind('Error', function(up, err) {
			if(err.code == '-601') {
				alert('文件类型错误，只能上传jpg、gif或png文件！');
			}
			up.refresh();
		});
		
		uploader.bind('FileUploaded', function(up, file, response) {
			$('#img_processer').css({"width":"50px","background":"#f00"});
			var r = $.parseJSON(response.response);
			$('#F_ad_data_image_md5').val(r.md5_filename);
			//$('#preview').children().attr("src", r.filename).show();
		});
	}
	if(v == 3) {
		var uploader = new plupload.Uploader({
			runtimes : 'html5,flash,silverlight,gears,browserplus',
			browse_button : 'upload_swf_file',
			max_file_size : '10mb',
			chunk_size : '1mb',
			unique_names : true,
			url : '<?php echo url($this->module->id . '/Ad/Data/UploadFile'); ?>',
			flash_swf_url : '<?php echo $this->module->assetsUrl; ?>/js/plupload/plupload.flash.swf',
			silverlight_xap_url : '<?php echo $this->module->assetsUrl; ?>/js/plupload/plupload.silverlight.xap',
			filters : [
				{title : "Image files", extensions : "swf,flv"}
			],
			multipart_params:{"upload_dir":"ad/flash","YII_CSRF_TOKEN":"<?php echo Yii::app()->request->csrfToken; ?>","type":"flash"}
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
			$('#swf_processer').css({"width":p_width+"px"});
			
		});
		
		uploader.bind('Error', function(up, err) {
			if(err.code == '-601') {
				alert('文件类型错误，只能上传swf或flv文件！');
			}
			up.refresh();
		});
		
		uploader.bind('FileUploaded', function(up, file, response) {
			$('#swf_processer').css({"width":"50px","background":"#f00"});
			var r = $.parseJSON(response.response);
			$('#F_ad_data_flash_md5').val(r.md5_filename);
		});
	}
}
<?php if($data['ad_data_type']){
?>
show_type_form(<?php echo $data['ad_data_type'];?>);
<?php	
}
?>
<?php if($data['image_full_path']){?>
$("#F_ad_data_flash_md5").hover(
	function(){
		var off = $(this).offset();
		$("#image_view_div").html('<img src="<?php echo $data['image_full_path'];?>" />').show().css({"top":off.top + 30,"left":off.left});
	},
	function(){
		$("#image_view_div").hide();
	}
);
<?php
}
?>

var position_relative_arr = new Array();
<?php
	foreach($position_relative_arr as $k=>$v){
?>
position_relative_arr[<?php echo $k;?>] = '<?php echo $v;?>';
<?php
	}
?>
var relative_type = "";
function load_relative(ad_position_id){
	if(position_relative_arr[ad_position_id]){
		$(".relative").show();
		relative_type = position_relative_arr[ad_position_id];
	}else{
		$(".relative").hide();
		$('#search_key').val("");
		$("select[name='F[ad_data_relative_id]']").children().remove();
	}
	if(position_relative_arr[ad_position_id] == "game"){
		$('#search_key').unbind();
		$('#search_key').bind('keyup', function(e){
			var search_key = $(this).val();
			load_game(search_key);
		})
		
	}
	if(position_relative_arr[ad_position_id] == "archives"){
		$('#search_key').unbind();
		$('#search_key').bind('keyup', function(e){
			var search_key = $(this).val();
			load_archives(search_key);
		})
		
	}
}
function load_game(search_key){
	var url = '<?php echo url($this->module->id . '/Game/Game/Search?search_key='); ?>' + search_key +'&t='+Math.random();
	$.getJSON(url, function(r){
		var element = $("select[name='F[ad_data_relative_id]']");
		//删除原来的选项
		if(r.count>0) {
			element.children().not(':first').remove();
			for(i in r.items) {
				element.append('<option value="'+i+'">'+r.items[i]+'</option>');
			}
		}
	});
}
function load_archives(search_key){
	var url = '<?php echo url($this->module->id . '/Content/Archives/Search?search_key='); ?>' + search_key +'&t='+Math.random();
	$.getJSON(url, function(r){
		var element = $("select[name='F[ad_data_relative_id]']");
		//删除原来的选项
		if(r.count>0) {
			element.children().not(':first').remove();
			for(i in r.items) {
				element.append('<option value="'+i+'" <?php if($data['ad_data_id']){echo 'selected=true';}?>>'+r.items[i]+'</option>');
			}
			<?php if($data['ad_data_id']){
			?>
			var obj =$("select[name='F[ad_data_relative_id]']")[0];
			load_info(obj);
			<?php	
			}?>
		}
	});
}
function load_info(obj){
	$("#ad_data_subject_tip").text("如不填写则采用默认值："+obj.options[obj.selectedIndex].text);
	if(relative_type == "game"){
		var ad_data_link = "http://www.wan123.com/game/"+$(obj).val()+".html"
	}
	if(relative_type == "archives"){
		var ad_data_link = "http://www.wan123.com/news/"+$(obj).val()+".html"
	}
	$("#ad_data_link_tip").text("如不填写则采用默认值："+ad_data_link);
}
<?php
if($data['ad_position_id']){
?>
load_relative(<?php echo $data['ad_position_id'];?>);
<?php
}
?>
if(relative_type == "game"){
<?php
if($data['ad_data_relative_id']){
?>
	load_game(<?php echo $data[ad_data_relative_id];?>);
<?php
}
?>
}else if(relative_type == "archives"){
<?php
if($data['ad_data_relative_id']){
?>
	load_archives(<?php echo $data[ad_data_relative_id];?>);
<?php
}
?>
}
</script>
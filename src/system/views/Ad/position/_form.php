	<style>
		
		#fade {
		    display: none;
		    position:absolute;
		    top: 0%;
		    left: 0%;
		    width: 100%;
		    height: 100%;
		    z-index:998;
		    background-color: black;
    		    filter:alpha(opacity=70);
    		    opacity:0.70;
		}
	</style>
	<?php
		$form=$this->beginWidget('CActiveForm', array(
			'id'=>'base-form',
			'enableAjaxValidation'=>false,
		));
	?>
	<input type="hidden" name="forward" value="<?php echo $_SERVER['HTTP_REFERER']; ?>" />
	<table class="tb tb2 ">
		<tr>
			<td colspan="2" class="td27">广告位名称:</td>
		</tr>
		<tr class="noborder">
			<td class="vtop rowform"><input type="text" name="F[ad_position_name]" id="F[ad_position_name]" value="<?php echo $data['ad_position_name']; ?>" class="txt" /></td>
			<td class="vtop tips2"></td>
		</tr>
                <tr>
			<td colspan="2" class="td27">关联类型:</td>
		</tr>
		<tr class="noborder">
			<td class="vtop rowform">
                        <select name="F[ad_position_relative_type]">
                        <option value="">========================</option>
                        <option value="archives" <?php echo $data['ad_position_relative_type']=="archives" ? "selected" : "";?>>文章</option>
                        </select>
                        </td>
			<td class="vtop tips2"></td>
		</tr>
		<tr>
			<td colspan="2" class="td27">分类:</td>
		</tr>
		<tr class="noborder">
			<td class="vtop rowform">
				<select name="F[ad_categories_id]" id="F[ad_categories_id]">
				<?php
					if(is_array($data['categories'])){
						foreach($data['categories'] as $v){
				?>
				<option value="<?php echo $v['ad_categories_id'];?>" <?php if($data['ad_categories_id'] == $v['ad_categories_id']){echo "selected";}?>><?php echo $v['ad_categories_name'];?></option>
				<?php
						}
					}
				?>
				</select>
                                <a href="javascript:void(0)" onclick="load_form('categories_form')">添加</a>
			</td>
			<td class="vtop tips2"></td>
		</tr>
                <tr>
			<td colspan="2" class="td27">广告位标识:</td>
		</tr>
		<tr class="noborder">
			<td class="vtop rowform">
                        <?php if($data['ad_position_identify']){
				echo $data['ad_position_identify'];
			}else{?>
                        <input type="text" name="F[ad_position_identify]" id="F[ad_position_identify]" class="txt" style="ime-mode:disabled" />
                        
                        <?php
			}
			?>
                        </td>
                        
			<td class="vtop tips2">英文，而且不能重复，不能以数字开头，添加后不可修改</td>
		</tr>
                <tr>
			<td colspan="2" class="td27">类型:</td>
		</tr>
		<tr class="noborder">
			<td class="vtop rowform">
                        <select name="F[ad_position_type]">
                        <option value="1" <?php if($data['ad_position_type'] == 1){echo "selected";}?>>固定</option>
                        <option value="2" <?php if($data['ad_position_type'] == 2){echo "selected";}?>>漂浮</option>
                        <option value="3" <?php if($data['ad_position_type'] == 3){echo "selected";}?>>弹窗</option>
                        <option value="3" <?php if($data['ad_position_type'] == 4){echo "selected";}?>>对联</option>
                        </select>
                        </td>
                        
			<td class="vtop tips2"></td>
		</tr>
                <tr>
			<td colspan="2" class="td27">广告位宽度:</td>
		</tr>
		<tr class="noborder">
			<td class="vtop rowform">
                        <input type="text" name="F[ad_position_width]" id="F[ad_position_width]" value="<?php echo $data['ad_position_width']; ?>" class="txt" style="ime-mode:disabled" /> px
                        </td>
                        
			<td class="vtop tips2">填写整数</td>
		</tr>
                <tr>
			<td colspan="2" class="td27">广告位高度:</td>
		</tr>
		<tr class="noborder">
			<td class="vtop rowform">
                        <input type="text" name="F[ad_position_height]" id="F[ad_position_height]" value="<?php echo $data['ad_position_height']; ?>" class="txt" style="ime-mode:disabled" /> px
                        </td>
                        
			<td class="vtop tips2">填写整数</td>
		</tr>
                <tr>
			<td colspan="2" class="td27">介绍:</td>
		</tr>
		<tr class="noborder">
			<td class="vtop rowform"><textarea rows="6" ondblclick="textareasize(this, 1)" onkeyup="textareasize(this, 0)" name="F[ad_position_remark]" id="F[ad_position_remark]" cols="50" class="tarea"><?php echo $data['ad_position_remark']; ?></textarea></td>
			<td class="vtop tips2"><br />
			双击输入框可扩大/缩小</td>
		</tr>
                <tr>
			<td colspan="2" class="td27">排序:</td>
		</tr>
		<tr class="noborder">
			<td class="vtop rowform"><input type="text" name="F[ad_position_rank]" id="F[ad_position_rank]" value="<?php echo $data['ad_position_rank']?$data['ad_position_rank']:1; ?>" class="txt" style="width:60px;" maxlength="3" /></td>
			<td class="vtop tips2"></td>
		</tr>
		<tr>
			<td colspan="2" class="td27">目标窗口:</td>
		</tr>
		<tr class="noborder">
			<td class="vtop rowform">
                        <input type="radio" name="F[ad_position_target]" value="_blank" <?php if($data['ad_position_target'] == "_blank" || !$data['ad_position_target']){echo "checked";}?> /> 新窗口 
                        <input type="radio" name="F[ad_position_target]" value="_self" <?php if($data['ad_position_target'] == "_self"){echo "checked";}?> /> 原窗口 
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
function show_fade(){
	if(!$("#fade").length){
		$("body").append('<div id="fade" onclick="hide_fade()" style="display:none"></div>');
	}
	$("#fade").show();
	if ( $.browser.msie && $.browser.version == 6 ){
		$("#fade").height(($(document).height()));
	}
	$("#fade").css("height",document.body.scrollHeight);
	$(window).resize();
}

function hide_fade(){
	$("#fade").removeClass("fade_black").hide();
	$("#form_container").hide();
}

function body_size(){
	var a=new Array();
	a.st = document.body.scrollTop ? document.body.scrollTop : document.documentElement.scrollTop;
	a.sl = document.body.scrollLeft ? document.body.scrollLeft : document.documentElement.scrollLeft;
	a.sw = document.documentElement.clientWidth;
	a.sh = document.documentElement.clientHeight;
	return a;
}

function center_element(obj){
	var s = body_size();
	obj.style.top = parseInt((s.sh - 355)/2) + s.st + 'px';
}
function load_form(type){
	var c = $("#form_container");
	if(!c.length){
		$("body").append('<div id="form_container" style="position: absolute;z-index:999; margin:0 auto; background-color:#FFF;border:10px solid #999;"></div>');
	}else{
		$("#form_container").show();
	}
	center_element(document.getElementById('form_container') );
	var _left = window.screen.availWidth/2 - 375;
	show_fade();
	if(type == "categories_form"){
		$("#form_container").load("http://operation.wan123.com/SysManager/Ad/Categories/Create #cc","",function(data){
			$("#form_container").css({"left":_left,"width":"550px"});
			$("#ad-categories-form").attr("action","javascript:dosubmit('ad-categories-form')");
			
		});
	}
	
}

function dosubmit(formid){
	if(formid=="ad-categories-form"){
		var url = "http://operation.wan123.com/SysManager/Ad/Categories/Create?ajax=1";
	}
	$.post(url,$("#"+formid).serialize(), function(data) {
		if(!data.ok){
			alert(data.error);
			return false;
		}else{
			$("#F\\[ad_categories_id\\]").append('<option value="'+data.id+'" selected>'+data.name+'</option>');
			$("#form_container").hide();
			hide_fade();
			alert("添加完成！");
		}
	},"json");
}
</script>
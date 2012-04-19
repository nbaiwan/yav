<?php
$this->breadcrumbs = array (
	'生成静态',
	'游戏列表' 
);
?>
	<?php
		$form=$this->beginWidget('CActiveForm', array(
			'id'=>'base-form',
			'enableAjaxValidation'=>false,
			'action'=>url($this->module->id . '/Tools/Static/CreateGameList'),
		));
	?>
	<table class="tb tb2 ">
       		<tr>
			<td width="10%" class="td27">页面:</td>
			<td class="vtop rowform">
                        <select name="page" onchange="load_child_page(this.value)">
                                <option value=""></option>
                                <option value="feature">游戏特色</option>
                                <option value="type">游戏类型</option>
                                <option value="letter">字母排序</option>
                                <option value="webgame">网页游戏</option>
                        </select>
                        </td>
			<td class="vtop tips2"></td>
		</tr>
		<tr>
			<td width="10%" class="td27">子页:</td>
			<td class="vtop rowform" id="child_page_td">
                        
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
var categories = <?php echo $categories;?>;
var features = <?php echo $features;?>;
var letter = [{"letter_id":10,"letter_identify":"abc"},{"letter_id":20,"letter_identify":"def"},{"letter_id":30,"letter_identify":"ghi"},{"letter_id":40,"letter_identify":"jkl"},{"letter_id":50,"letter_identify":"mno"},{"letter_id":60,"letter_identify":"pqr"},{"letter_id":70,"letter_identify":"stw"},{"letter_id":80,"letter_identify":"xyz"},];
function load_child_page(page){
	var html = "";
	if(page == "type" || page == "webgame"){
		for(var i=0;i<categories.length;i++){
			var obj = categories[i];
			html = html + '<input type="checkbox" name="child_page[]" checked="checked" value="' + obj.game_categories_id + '|' + obj.game_categories_identify + '" id="categories_' + i + '" /> <label for="categories_'+ i +'">' + obj.game_categories_name + '</label><br />';
		}
	}
	if(page == "feature"){
		for(var i=0;i<features.length;i++){
			var obj = features[i];
			html = html + '<input type="checkbox" name="child_page[]" checked="checked" value="' + obj.game_feature_id + '|' + obj.game_feature_identify + '" id="features_' + i + '" /> <label for="features_'+ i +'">' + obj.game_feature_name + '</label><br />';
		}
	}
	if(page == "letter"){
		for(var i=0;i<letter.length;i++){
			var obj = letter[i];
			html = html + '<input type="checkbox" name="child_page[]" checked="checked" value="' + obj.letter_id + '|' + obj.letter_identify + '" id="letter_' + i + '" /> <label for="letter_'+ i +'">' + obj.letter_identify + '</label><br />';
		}
	}
	$("#child_page_td").html(html);
}
</script>
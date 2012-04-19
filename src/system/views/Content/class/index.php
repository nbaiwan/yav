<?php
$this->breadcrumbs=array(
	'档案栏目管理',
	'栏目列表',
);

$this->menu=array(
	array('label'=>'档案栏目管理'),
	array('label'=>'栏目列表', 'cur'=>true, 'url'=>url($this->module->id . '/Content/Class/Index')),
	array('label'=>'添加顶级栏目', 'url'=>url($this->module->id . "/Content/Class/Create")),
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('purview-grid', {
		data: $(this).serialize()
	});
	return false;
});
");
?>
	<table class="tb tb2 " id="tips">
		<tr>
			<th class="partition">技巧提示</th>
		</tr>
		<tr>
			<td class="tipsblock">
			<ul id="tipslis">
				<li>栏目标识会用在静态页面的生成中<!--版主用户名为粗体，则表示该版主权限可继承到下级版块--></li>
			</ul>
			</td>
		</tr>
	</table>
	<script type="text/JavaScript">
	var content_model_select_top = '';
	var content_model_select_sub = '';
	<?php
		echo "	content_model_select_top +='					<select name=\"content_model_id_new[]\" class=\"txt\" style=\"width: {2}px;\">';\r\n";
		echo "	content_model_select_sub +='					<select name=\"content_model_id_new[{1}][]\" class=\"txt\" style=\"width: {2}px;\">';\r\n";
		foreach($models as $__k=>$__v) {
			$selected = $__v['content_model_is_default'] ? ' selected' : '';
			echo "	content_model_select_top +='						<option value=\"{$__v['content_model_id']}\"{$selected}>{$__v['content_model_name']}</option>';\r\n";
			echo "	content_model_select_sub +='						<option value=\"{$__v['content_model_id']}\"{$selected}>{$__v['content_model_name']}</option>';\r\n";
		}
		echo "	content_model_select_top +='					</select>';\r\n";
		echo "	content_model_select_sub +='					</select>';\r\n";
	?>
	var rowtypedata = [
		[[1, ''], [1,'<input type="text" class="txt" name="class_rank_new[]" value="0" />', 'td25'], [1, '<input name="class_name_new[]" value="新栏目名称" size="20" type="text" class="txt" />'], [1, '<div class="parentboard">' + content_model_select_top + '</div>', 'td31'], [1, '<input name="class_identify_new[]" value="新栏目标识" size="20" type="text" class="txt" style="width: 210px;" />', 'td31'], [1, '<div style="text-align:center;"><input class="checkbox" type="checkbox" name="class_is_show_new[]" value="1" checked />'], [1, '']],
		[[1, ''], [1,'<input type="text" class="txt" name="class_rank_new[{1}][]" value="0" />', 'td25'], [1, '<div class="board"><input name="class_name_new[{1}][]" value="新栏目名称" size="20" type="text" class="txt" /></div>'], [1, '<div class="board">' + content_model_select_sub + '</div>', 'td31'], [1, '<div class="board"><input name="class_identify_new[{1}][]" value="新栏目标识" size="20" type="text" class="txt" style="width: 155px;" /></div>', 'td31'], [1, '<div style="text-align:center;"><input class="checkbox" type="checkbox" name="class_is_show_new[{1}][]" value="1" checked />'], [1, '']],
		[[1, ''], [1,'<input type="text" class="txt" name="class_rank_new[{1}][]" value="0" />', 'td25'], [1, '<div class="childboard"><input name="class_name_new[{1}][]" value="新栏目名称" size="20" type="text" class="txt" /></div>'], [1, '<div class="childboard">' + content_model_select_sub + '</div>', 'td31'], [1, '<div class="childboard"><input name="class_identify_new[{1}][]" value="新栏目标识" size="20" type="text" class="txt" style="width: 100px;" /></div>', 'td31'], [1, '<div style="text-align:center;"><input class="checkbox" type="checkbox" name="class_is_show_new[{1}][]" value="1" checked />'], [1, '']],
	];
	</script>
	<?php echo CHtml::form('', 'post', array('id'=>'cpform')); ?>
	<input type="hidden" id="formscrolltop" name="scrolltop" value="" />
	<input type="hidden" name="anchor" value="" />
	<!-- 
	<div style="height: 30px; line-height: 30px;">
	  <input type="text" id="srchforumipt" class="txt" /> <input type="submit" class="btn" value="搜索" onclick="return srchforum()" />
	</div> -->
	
	<table class="tb tb2 ">
		<tr class="header">
			<th></th>
			<th>显示顺序</th>
			<th>栏目名称</th>
			<th>内容模型</th>
			<th>栏目标识</th>
			<th>是否显示</th>
			<th></th>
		</tr>
		<?php
			$boardclass = array(
				1 => 'parentboard',
				2 => 'board',
				3 => 'childboard',
			);
			foreach($classes as $_k=>$_v) {
		?>
		<tr class="hover">
			<td class="td25" onclick="toggle_group('group_<?php echo $_v['class_id']; ?>', $('#a_group_<?php echo $_v['class_id']; ?>'))"><a href="javascript:;" id="a_group_<?php echo $_v['class_id']; ?>">[-]</a></td>
			<td class="td25"><input type="text" class="txt" name="class_rank[<?php echo $_v['class_id']; ?>]" value="<?php echo $_v['class_rank']; ?>" /></td>
			<td>
				<div class="<?php echo $boardclass[$_v['deepth']]; ?>">
					<input type="text" name="class_name[<?php echo $_v['class_id']; ?>]" value="<?php echo $_v['class_name']; ?>" class="txt" />
					<?php
						if(($_v['deepth'] == 2) && ($_v['class_is_part'] == ContentArchivesClass::STAT_PART_COVER_CLASS)) {
					?>
					<a href="###" onclick="addrowdirect = 1;addrow(this, 2, <?php echo $_v['class_id']; ?>, 100)" class="addchildboard">添加子栏目</a>
					<?php
						}
					?>
				</div>
			</td>
			<td class="td31">
				<div class="<?php echo $boardclass[$_v['deepth']]; ?>">
					<select name="content_model_id[<?php echo $_v['class_id']; ?>]" class="txt" style="width: <?php echo 210 - 55 * ($_v['deepth']-1); ?>px;">
						<?php
							foreach($models as $__k=>$__v) {
						?>
						<option value="<?php echo $__v['content_model_id']; ?>"<?php echo $__v['content_model_id']==$_v['content_model_id'] ? ' selected' : ''; ?>><?php echo $__v['content_model_name']; ?></option>
						<?php
							}
						?>
					</select>
				</div>
			</td>
			<td class="td31">
				<div class="<?php echo $boardclass[$_v['deepth']]; ?>"><input type="text" name="class_identify[<?php echo $_v['class_id']; ?>]" value="<?php echo $_v['class_identify']; ?>" class="txt" style="width: <?php echo 210 - 55 * ($_v['deepth']-1); ?>px;" /></div>
			</td>
			<td class="td25">
				<div style="text-align:center;"><input class="checkbox" type="checkbox" name="class_is_show[<?php echo $_v['class_id']; ?>]" value="1"<?php echo $_v['class_is_show'] ? ' checked' : '';?> /></div>
			</td>
			</td>
			<td width="220">
				<!-- <a href="javascript:void(0);" title="更新" class="act delete">更新</a> -->&nbsp;更新&nbsp;
				<a href="<?php echo url($this->module->id . "/Content/{$_v['content_model_id']}/{$_v['class_id']}/Archives/Index"); ?>" title="内容" class="act delete">内容</a>
				<a href="<?php echo url($this->module->id . "/Content/Class/{$_v['class_id']}/Update"); ?>" title="编辑" class="act delete">编辑</a>
				<!-- <a href="<?php echo url($this->module->id . "/Content/Class/{$_v['class_id']}/Move"); ?>" title="移动" class="act delete">移动</a> -->&nbsp;移动&nbsp;
				<?php
					if($_v['class_is_system']) {
						echo '&nbsp;删除&nbsp;';
					} else {
				?>
				<a href="<?php echo url($this->module->id . "/Content/Class/{$_v['class_id']}/Delete"); ?>" title="删除本权限" class="act delete" onclick="if(!confirm('确定删除档案栏目<<?php echo $_v['class_name']; ?>>吗？')){return false;}">删除</a>
				<?php
					}
				?>
			</td>
		</tr>
		<?php
				if($_v['deepth']==1 && isset($classes[$_k+1]) && $classes[$_k+1]['deepth']>1 ) {
					echo "<tbody id=\"group_{$_v['class_id']}\">\r\n";
				}
				if($_v['deepth']>1 && (!isset($classes[$_k+1]) || $classes[$_k+1]['deepth']==1)) {
					echo "</tbody>\r\n";
				}
				
				if((!isset($classes[$_k+1]) || $classes[$_k+1]['deepth']==1) && ($classes[$_v['parent_key']]['class_is_part'] == ContentArchivesClass::STAT_PART_COVER_CLASS)) {
		?>
		<tr>
			<td></td>
			<td colspan="5">
			<div class="lastboard"><a href="###" onclick="addrow(this, 1, <?php echo $classes[$_v['parent_key']]['class_id']; ?>, 155)"
				class="addtr">添加子栏目</a></div>
			</td>
			<td>&nbsp;</td>
		</tr>
		<?php
				}
			} 
		?>
		<tr>
			<td></td>
			<td colspan="5">
			<div><a href="###" onclick="addrow(this, 0, 0, 210)" class="addtr">添加栏目</a></div>
			</td>
			<td class="bold">
				<!-- <a href="javascript:;" onclick="if(getmultiids()) location.href='<?php echo url($this->module->id . '/Content/Class/Delete/'); ?>' + getmultiids(); return false;">批量删除</a> -->
			</td>
		</tr>
		<tr>
			<td colspan="7">
			<div class="fixsel"><input type="submit" class="btn" id="submit_editsubmit" name="editsubmit" title="按 Enter 键可随时提交你的修改" value="提交" /></div>
			</td>
		</tr>
	</table>
	</form>
<script type="text/JavaScript">
	var addrowdirect = 0;
	function addrow(obj, type) {
		var table = obj.parentNode.parentNode.parentNode.parentNode.parentNode;
		if(!addrowdirect) {
			var row = table.insertRow(obj.parentNode.parentNode.parentNode.rowIndex);
		} else {
			var row = table.insertRow(obj.parentNode.parentNode.parentNode.rowIndex + 1);
		}
		var typedata = rowtypedata[type];
		for(var i = 0; i <= typedata.length - 1; i++) {
			var cell = row.insertCell(i);
			cell.colSpan = typedata[i][0];
			var tmp = typedata[i][1];
			if(typedata[i][2]) {
				cell.className = typedata[i][2];
			}
			tmp = tmp.replace(/\{(\d+)\}/g, function($1, $2) {return addrow.arguments[parseInt($2) + 1];});
			cell.innerHTML = tmp;
		}
		addrowdirect = 0;
	}
	
	var multiids = new Array();
	function multiupdate(obj) {
		v = obj.value;
		if(obj.checked) {
			multiids[v] = v;
		} else {
			multiids[v] = null;
		}
	}
	
	function getmultiids() {
		var ids = '', comma = '';
		for(i in multiids) {
			if(multiids[i] != null) {
				ids += comma + multiids[i];
				comma = ',';
			}
		}
		return ids;
	}
	
	function toggle_group(oid, obj, conf) {
		obj = obj ? obj : $('#a_'+oid);
		if(!conf) {
			var conf = {'show':'[-]','hide':'[+]'};
		}
		var obody = $('#' + oid);
		if(obody.css('display') == 'none') {
			obody.css('display', '');
			$(obj).html(conf.show);
		} else {
			obody.css('display', 'none');
			$(obj).html(conf.hide);
		}
	}
	
	function show_all() {
		var tbodys = $("#cpform tbody");
		for(var i = 0; i < tbodys.size(); i++) {
			var re = /^group_(\d+)$/;
			var matches = re.exec($(tbodys[i]).attr('id'));
			if(matches != null) {
				$(tbodys[i]).css('display', '');
				$('#a_group_' + matches[1]).html('[-]');
			}
		}
	}
	function hide_all() {
		var tbodys = $("#cpform tbody");
		for(var i = 0; i < tbodys.size(); i++) {
			var re = /^group_(\d+)$/;
			var matches = re.exec($(tbodys[i]).attr('id'));
			if(matches != null) {
				$(tbodys[i]).css('display', 'none');
				$('#a_group_' + matches[1]).html('[+]');
			}
		}
	}
	
	function srchforum() {
		var isfirst = true;
		var fname = $('#srchforumipt').val();
		if(!fname) return false;
		var inputs = $("#cpform input");
		for(var i = 0; i < inputs.size(); i++) {
			$(inputs[i]).parent().parent().parent().css('background', '');
		}
		for(var i = 0; i < inputs.size(); i++) {
			if($(inputs[i]).attr('name').match(/^(PName\[\d+\])|(PIdentify\[\d+\])$/)) {
				if($(inputs[i]).val().toLowerCase().indexOf(fname.toLowerCase())!=-1) {
					$(inputs[i]).parent().parent().parent().css('display', '');
					$(inputs[i]).parent().parent().parent().css('background', '#eee');
					if(isfirst) {
						window.scrollTo(0, $(inputs[i]).offset().top - 100);
						isfirst=false;
					}
					//return false;
				}
			}
		}
		return false;
	}
</script>
</div>
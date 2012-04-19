<?php
$this->breadcrumbs=array(
	'游戏管理',
	'游戏截图',
	'截图列表',
);

$this->menu=array(
	array('label'=>'游戏管理'),
	array('label'=>'游戏截图', 'cur'=>true, 'url'=>url($this->module->id . '/Content/5/12/Archives/Index')),
	array('label'=>'添加截图', 'url'=>url($this->module->id . '/Content/5/12/Archives/Create')),
	array('label'=>'批量上传', 'url'=>url($this->module->id . '/Game/ScreenShot/Create')),
	array('label'=>'采集截图', 'url'=>url($this->module->id . '/Game/Collect/Shot')),
);
?>
	<script type="text/javascript">
	<!--
		var ARCHIVES_CSRFTOKEN = '<?php echo Yii::app()->request->csrfToken; ?>';
		var SYSTEM_ASSETS_URL = '<?php echo $this->module->assetsUrl; ?>';
		var ARCHIVES_COMMEND_URL = '<?php echo url($this->module->id . '/Content/Archives/Commend'); ?>';
		var ARCHIVES_DELETE_URL = '<?php echo url($this->module->id . '/Content/Archives/Delete'); ?>';
		var ARCHIVES_ATTR_URL = '<?php echo url($this->module->id . '/Content/Archives/Attr'); ?>';
		var ARCHIVES_ATTRS = '<?php
		    foreach(ContentArchives::get_archives_flag() as $_k=>$_v) {
				if($_k == ContentArchives::STAT_ARCHIVES_FLAG_J) {
					continue;
				}
				echo "<input type=\"radio\" id=\"archive_flag_{$_k}\" name=\"Archives[content_archives_flag]\" value=\"{$_k}\" class=\"np\" /><label for=\"archive_flag_{$_k}\">{$_v}</label>";
			}
		?>';
	//-->
	</script>
	<script type="text/javascript" src="<?php echo $this->module->assetsUrl; ?>/js/dialog.js"></script>
	<script type="text/javascript" src="<?php echo $this->module->assetsUrl; ?>/js/archives.js"></script>
	<table class="tb tb2 " id="tips">
		<tr>
			<th class="partition">技巧提示</th>
		</tr>
		<tr>
			<td class="tipsblock">
			<ul id="tipslis">
				<li><!--版主用户名为粗体，则表示该版主权限可继承到下级版块--></li>
			</ul>
			</td>
		</tr>
	</table>
	<?php
		$form=$this->beginWidget('CActiveForm', array(
			'id'=>'search-form',
			'method'=>'GET',
			'enableAjaxValidation'=>false,
		));
	?>
	<div style="height: 30px; line-height: 30px;">
	  游戏搜索：
	  <select name="game_id" style="width:180px;">
	    <option value="0">不限制</option>
	    <?php
	    	foreach($games as $_k=>$_v) {
	    ?>
	    <option value="<?php echo $_v['game_id']; ?>"<?php echo $_v['game_id'] == $_GET['game_id'] ? ' selected' : ''; ?>><?php echo $_v['game_name']; ?></option>
	    <?php
	    	}
	    ?>
	  </select>&nbsp;
	  <input type="text" id="game_search_key" name="game_search_key" class="txt" value="游戏快速查找" onblur="if(this.value==''){this.value=this.defaultValue}" onfocus="if(this.value==this.defaultValue){this.value='';}" />
	  关键字：<input type="text" id="search_key" name="search_key" class="txt" value="<?php echo isset($_GET['search_key']) ? $_GET['search_key'] : ''; ?>" />
	  <input type="submit" class="btn" value="搜索" onclick="return srchforum()" />
	</div>
	<?php
		$this->endWidget();
		
		$form=$this->beginWidget('CActiveForm', array(
			'id'=>'content-archives-form',
			'enableAjaxValidation'=>false,
		));
	?>
	<table class="tb tb2 ">
		<tr class="header">
			<th width="50"><div style="text-align:center"><?php echo Yii::t('admincp', '编号'); ?></div></th>
			<th width="50"><div style="text-align:center"><?php echo Yii::t('admincp', '选择'); ?></div></th>
			<th width=100><div style="text-align:center"><?php echo Yii::t('admincp', '缩略图'); ?></div></th>
			<th width="200"><?php echo Yii::t('admincp', '标题'); ?></th>
			<th width="100"><div style="text-align:center"><?php echo Yii::t('admincp', '更新时间'); ?></div></th>
			<th width="120"><div style="text-align:center"><?php echo Yii::t('admincp', 'HTML'); ?></div></th>
			<th width="120"><div style="text-align:center"><?php echo Yii::t('admincp', '发布人'); ?></div></th>
			<th width="100"><div style="text-align:center"><?php echo Yii::t('admincp', '状态'); ?></div></th>
			<th width="120"><div style="text-align:center"><?php echo Yii::t('admincp', '操作'); ?></div></th>
		</tr>
		<?php
			foreach($archives['rows'] as $archive) {
		?>
		<tr class="hover">
			<td><div style="text-align:center"><?php echo $archive['content_archives_id']; ?></div></td>
			<td><div style="text-align:center"><input type="checkbox" name="Archives[content_archives_id][<?php echo $archive['content_archives_id']; ?>]" value="<?php echo $archive['content_archives_id']; ?>" size="3" maxlength="3" /></div></td>
			<td><div style="text-align:center"><img src="<?php echo UploadFile::thumb($archive['content_archives_thumb'], 80, 80); ?>" rel="<?php echo $archive['content_archives_thumb']; ?>" width="80" /></div></td>
			<td>
				<span style="color:<?php echo $archive['content_archives_color'] ? $archive['content_archives_color'] : '#333'; ?>;">
					<a href="http://www.wan123.com<?php echo Common::sign_archives_url($archive['content_archives_id'])?>" target="_blank"><?php echo $archive['content_archives_subject']; ?></a>
					<?php echo $archive['content_archives_flag']? ContentArchives::get_archives_flag($archive['content_archives_flag']) : ''; ?>
				</span>
			</td>
			<td><div style="text-align:center"><?php echo date('Y-m-d', $archive['content_archives_lasttime']); ?></div></td>
			<td><div style="text-align:center"><?php echo $archive['content_archives_is_build'] == 1 ? '已生成' : '<span class="red">未生成</span>'; ?></div></td>
			<td><div style="text-align:center"><?php echo $archive['AUserName']; ?></div></td>
			<td><div style="text-align:center"><?php echo ContentArchives::get_archives_status($archive['content_archives_status']); ?></div></td>
			<td>
				<div style="text-align:center">
					&nbsp;
					<a href="<?php echo url($this->module->id . "/Content/Archives/{$archive['content_archives_id']}/Update".($archives['pages']->currentPage>0 ? '/' . ($archives['pages']->currentPage+1) : ''));?>">属性</a>
					<a href="<?php echo url($this->module->id . "/Content/Archives/{$archive['content_archives_id']}/Update".($archives['pages']->currentPage>0 ? '/' . ($archives['pages']->currentPage+1) : ''));?>">编辑</a>
					<a href="<?php echo url($this->module->id . "/Content/Archives/{$archive['content_archives_id']}/Preview"); ?>" target="_blank">预览</a>
					<a href="<?php echo url($this->module->id . "/Content/Archives/{$archive['content_archives_id']}/Delete".($archives['pages']->currentPage>0 ? '/' . ($archives['pages']->currentPage+1) : ''));?>" onclick="return confirm('确定要删除文档<<?php echo $archive['content_archives_subject']; ?>>吗');">删除</a>
				</div>
			</td>
		</tr>
		<?php
			}
		?>
		<tr>
			<td colspan="20">
			<?php
				if(count($archives['rows'])>0) {
			?>
			<div class="cuspages left">
				<input type="button" name="selAllBtn" value="全选" onclick="selAll()" class="btn" />
				<input type="button" name="cancelSelBtn" value="取消" onclick="cancelSel()" class="btn" />
				<!-- <input type="button" name="updateArchives" value="更新" onclick="updateArc()" class="btn" /> -->
				<input type="button" name="recomArchives" value="推荐" onclick="recomArc()" class="btn" />
				<!-- <input type="button" name="" value="移动" onclick="moveArc()" class="btn" /> -->
				<input type="button" name="delArchives" value="删除" onclick="delArc()" class="btn" />
				<input type="button" name="addAttrBtn" value="增加属性" onclick="changeAttr('add', event, this)" class="btn" />
				<input type="button" name="delAttrBtn" value="删除属性" onclick="changeAttr('del', event, this)" class="btn" />
			</div>
			<?php
				}
			?>
			<div class="cuspages right">
				<?php
					$this->widget('CPager',array(
							'pages'=>$archives['pages'],
						)
					);
				?>
			</div>
			<div class="fixsel"></div>
			</td>
		</tr>
	</table>
	<?php
		$this->endWidget();
	?>
	<script type="text/javascript">
	<!--
		function srchforum() {
		var game_id = $('select[name=game_id]').val();
		var search_key = $('input[name=search_key]').val();
		
		var search_url = '<?php echo url($this->module->id . '/Content/5/12{game_id}/Archives{search_key}/Index'); ?>';
		
		if(parseInt(game_id) > 0) {
			search_url = search_url.replace('{game_id}', '/' + game_id);
		} else {
			search_url = search_url.replace('{game_id}', '');
		}
		if(search_key != '') {
			search_url = search_url.replace('{search_key}', '/skey_' + search_key);
		} else {
			search_url = search_url.replace('{search_key}', '');
		}

			window.location.href = search_url;
			return false;
		}
		
		$('#game_search_key').live('keyup', function(e){
			var search_key = $(this).val();
			var url = '<?php echo url($this->module->id . '/Game/Game/Search?search_key='); ?>' + search_key +'&t='+Math.random();
			$.getJSON(url, function(r){
				var element = $("select[name=game_id]");
				//删除原来的选项
				if(r.count>0) {
					element.children().not(':first').remove();
					for(i in r.items) {
						element.append('<option value="'+i+'">'+r.items[i]+'</option>');
					}
					if(search_key != '') {
						element.children().eq(1).attr('selected', true);
					}
				}
			});
		})
	//-->
	</script>
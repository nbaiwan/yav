<?php
$this->breadcrumbs = array (
	'Cache Manager',
	'Update Cache' 
);
?>
	<div class="itemtitle">
		<h3>更新缓存</h3>
		<ul class="stepstat">
			<li class="current" id="step1">1.确认开始</li>
			<li id="step2">2.开始更新</li>
			<li id="step3">3.更新结果</li>
		</ul>
	</div>
	<table class="tb tb2 " id="tips">
		<tr>
			<th class="partition">技巧提示</th>
		</tr>
		<tr>
			<td class="tipsblock">
			<ul id="tipslis">
				<li>当站点进行了数据恢复、升级或者工作出现异常的时候，你可以使用本功能重新生成缓存。</li>
				<li>更新缓存的时候，可能让服务器负载升高</li>
				<!--<li>数据缓存：更新站点的全部数据缓存</li>
				<li>模板缓存：更新论坛模板、风格等缓存文件，当你修改了模板或者风格，但是没有立即生效的时候使用</li>
				<li>DIY模块分类缓存：更新DIY模块分类，当你安装或修改了DIY模块分类，但是没有立即生效的时候使用</li>-->
			</ul>
			</td>
		</tr>
	</table>
	
	<div class="infobox">
		<form action="<?php echo url($this->module->id . '/Tools/Cache/Index/2'); ?>" method="get">
			<input type="hidden" name="YII_CSRF_TOKEN" value="<?php echo Yii::app()->request->csrfToken; ?>" />
			<h4 class="marginbot normal">
				<input type="checkbox" name="type[]" value="all" id="allcache" class="checkbox" /><label for="allcache">所有缓存</label>
				<input type="checkbox" name="type[]" value="setting" id="settingcache" class="checkbox" /><label for="settingcache">系统设置</label>
				<input type="checkbox" name="type[]" value="logistics" id="logisticscache" class="checkbox" /><label for="logisticscache">物流设置</label>
				<input type="checkbox" name="type[]" value="draw" id="drawcache" class="checkbox" /><label for="drawcache">领取方式</label>
				<input type="checkbox" name="type[]" value="goodsclass" id="goodsclassache" class="checkbox" /><label for="goodsclassache">商品分类</label>
				<input type="checkbox" name="type[]" value="purview" id="purviewcache" class="checkbox" /><label for="purviewcache">权限缓存</label>
				<input type="checkbox" name="type[]" value="role" id="rolecache" class="checkbox" /><label for="rolecache">角色缓存</label>
				<input type="checkbox" name="type[]" value="announce" id="announcecache" class="checkbox" /><label for="announcecache">公告缓存</label>
				<input type="checkbox" name="type[]" value="tempfile" id="tempfilecache" class="checkbox" /><label for="tempfilecache">临时文件</label>
				<!-- <input type="checkbox" name="type[]" value="data" id="datacache" class="checkbox" checked /><label for="datacache">数据缓存</label>
				<input type="checkbox" name="type[]" value="tpl" id="tplcache" class="checkbox" checked /><label for="tplcache">模板缓存</label>
				<input type="checkbox" name="type[]" value="blockclass" id="blockclasscache" class="checkbox" /><label for="blockclasscache">DIY模块分类缓存</label> -->
			</h4>
			<br />
			<p class="margintop">
				<input type="submit" class="btn" name="confirmed" value="确定"> &nbsp; 
				<script type="text/javascript">
				<!--
					$(function(){
						$('#allcache').click(function(){
								if($(this).attr('checked')) {
									$('input[type=checkbox]').not('#allcache').attr('disabled', 'disabled');
								} else {
									$('input[type=checkbox]').attr('disabled', '');
								}
							});
					})
					if(history.length > ($.browser.msie ? 0 : 1)) {
						document.write('<input type="button" class="btn" value="取消" onClick="history.go(-1);">');
					}
				//-->
				</script>
			</p>
		</form>
		<br />
	</div>
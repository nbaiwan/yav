<?php
	$this->breadcrumbs = array (
		'Cache Manager',
		'Update Cache' 
	);
?>
	<div class="itemtitle">
		<h3>更新缓存</h3>
		<ul class="stepstat">
			<li id="step1">1.确认开始</li>
			<li class="current" id="step2">2.开始更新</li>
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
	
	
	<h3>系统提示</h3>
	<div class="infobox">
		<h4 class="infotitle1">正在更新缓存，请稍候......</h4>
		<img src="<?php echo $this->module->assetsUrl; ?>/images/ajax_loader.gif" class="marginbot" />
		<p class="marginbot">
			<a href="<?php echo url($this->module->id . '/Tools/Cache/Index/3'); ?>" class="lightlink">如果你的浏览器没有自动跳转，请点击这里</a>
		</p>
		<script type="text/JavaScript">setTimeout("window.location.href='<?php echo url($this->module->id . '/Tools/Cache/Index/3'); ?>';", 2000);</script>
	</div>

<script type="text/javascript">
	$.ajax({
		'url': '<?php echo url($this->module->id . '/Tools/Cache/UpdateCache/' . $type); ?>',
		'data': '',
		'success': function(response){
			//
		} 
	});
</script>
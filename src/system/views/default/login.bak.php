<?php
	$basePath = $this->module->assetsUrl;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>登录管理中心</title>
<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
<link rel="stylesheet" href="<?php echo $basePath; ?>/css/main.css" type="text/css" media="all" />
<meta content="Comsenz Inc." name="Copyright" />
</head>
<body>
<script language="JavaScript">
	if(self.parent.frames.length != 0) {
		self.parent.location=document.location;
	}
</script>
<table class="logintb">
<tr>
	<td class="login">
		<h1>Discuz! Administrator's Control Panel</h1>

		<!-- <p>Discuz! 是一个采用 PHP 和 MySQL 等多种数据库构建的高效建站解决方案, 是众多社区网站首选技术品牌!</p> -->
	</td>

	<td>
		<?php echo CHtml::beginForm('', 'post', array('id'=>'loginform')); ?>
			<p class="logintitle"><?php echo Yii::t('login', 'AUserName'); ?>: </p>
			<p class="loginform"><input name="AUserName" tabindex="1" type="text" class="txt" autocomplete="off" /></p>

			<p class="logintitle"><?php echo Yii::t('login', 'AUserPwd'); ?>:</p>
			<p class="loginform"><input name="AUserPwd" tabindex="2" type="password" class="txt" autocomplete="off" /></p>

			<p class="logintitle"><?php echo Yii::t('login', 'verifycode'); ?>:</p>
			<p class="loginform"><input name="verifycode" tabindex="3" type="text" class="txt" autocomplete="off" /></p>
			
			<p class="logintitle">&nbsp;</p>
			<p class="loginform"><img id="captcha" src="<?php echo url($this->module->id . '/Default/captcha', array('t'=>time())); ?>" height="25" alt="<?php echo Yii::t('login', 'Click here to refresh verifycode'); ?>" title="<?php echo Yii::t('login', 'Click here to refresh verifycode'); ?>" style="cursor:pointer;" /></p>

			<!--
			<p class="logintitle">提　问:</p>
			<p class="loginform">
				<select id="questionid" name="admin_questionid" tabindex="2">
					<option value="0">无安全提问</option>
					<option value="1">母亲的名字</option>
	
					<option value="2">爷爷的名字</option>
					<option value="3">父亲出生的城市</option>
					<option value="4">你其中一位老师的名字</option>
					<option value="5">你个人计算机的型号</option>
					<option value="6">你最喜欢的餐馆名称</option>
					<option value="7">驾驶执照最后四位数字</option>
	
				</select>
			</p>
			<p class="logintitle">回　答:</p>
			<p class="loginform"><input name="admin_answer" tabindex="3" type="text" class="txt" autocomplete="off" /></p>
			-->
			<p class="loginnofloat"><input name="submit" value="提交"  tabindex="3" type="submit" class="btn" /></p>
		<?php echo CHtml::endForm(); ?>
		<script type="text/JavaScript">document.getElementById('loginform').AUserName.focus();</script>
		<script type="text/JavaScript">
		<!--
			document.getElementById('captcha').onclick = function(){
				this.src = '<?php echo url($this->module->id . '/Default/captcha'); ?>?t=' + Math.random();
			}
		//-->
		</script>
	</td>
</tr>

</table>
<table class="logintb">
<tr>
	<td colspan="2" class="footer">
		<div class="copyright">
			<p>Powered by <a href="http://www.discuz.net/" target="_blank">Discuz!</a> X1.5 </p>
			<p>&copy; 2001-2010, <a href="http://www.comsenz.com/" target="_blank">Comsenz</a> Inc.</p>

		</div>
	</td>
</tr>
</table>
</body>
</html>

<?php
	$basePath = $this->module->assetsUrl;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
<title><?php echo CHtml::encode($this->pageTitle); ?></title>
<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE9" />
<link rel="stylesheet" href="<?php echo $basePath; ?>/css/form.css" type="text/css" media="all" />
<link rel="stylesheet" href="<?php echo $basePath; ?>/css/main.css" type="text/css" media="all" />
<?php Yii::app()->clientScript->registerCoreScript('jquery'); ?>
<script type="text/javascript">
<!--
var BROWSER = {};
var USERAGENT = navigator.userAgent.toLowerCase();
browserVersion({'ie':'msie','firefox':'','chrome':'','opera':'','safari':'','mozilla':'','webkit':'','maxthon':'','qq':'qqbrowser'});
if(BROWSER.safari) {
	BROWSER.firefox = true;
}
BROWSER.opera = BROWSER.opera ? opera.version() : 0;

HTMLNODE = document.getElementsByTagName('head')[0].parentNode;
if(BROWSER.ie) {
	BROWSER.iemode = parseInt(typeof document.documentMode != 'undefined' ? document.documentMode : BROWSER.ie);
	HTMLNODE.className = 'ie_all ie' + BROWSER.iemode;
}

function textareasize(obj, op) {
	if(!op) {
		if(obj.scrollHeight > 70) {
			obj.style.height = (obj.scrollHeight < 300 ? obj.scrollHeight - heightag: 300) + 'px';
			if(obj.style.position == 'absolute') {
				obj.parentNode.style.height = (parseInt(obj.style.height) + 20) + 'px';
			}
		}
	} else {
		if(obj.style.position == 'absolute') {
			obj.style.position = '';
			obj.style.width = '';
			obj.parentNode.style.height = '';
		} else {
			obj.parentNode.style.height = obj.parentNode.offsetHeight + 'px';
			obj.style.width = BROWSER.ie > 6 || !BROWSER.ie ? '90%' : '600px';
			obj.style.position = 'absolute';
		}
	}
}

function browserVersion(types) {
	var other = 1;
	for(i in types) {
		var v = types[i] ? types[i] : i;
		if(USERAGENT.indexOf(v) != -1) {
			var re = new RegExp(v + '(\\/|\\s)([\\d\\.]+)', 'ig');
			var matches = re.exec(USERAGENT);
			var ver = matches != null ? matches[2] : 0;
			other = ver !== 0 && v != 'mozilla' ? 0 : other;
		}else {
			var ver = 0;
		}
		eval('BROWSER.' + i + '= ver');
	}
	BROWSER.other = other;
}
//-->
</script>
<style>
.form .row label{float:left; margin-right:10px;}
.form .row input{margin:0px;}
.form .row select{margin:0px;height:20px;}
.form .note { padding-left: 110px; }
.form .purviewBox { margin-left:110px; line-height:23px;}
.form .purviewBox .subject, .form .purviewBox dl { border-top: 1px dotted #DEEFFB }
.form .purviewBox dl { margin:0px; padding:0px;}
.form .purviewBox .subject label,.form .purviewBox .row label { width:120px; }
.form .purviewBox .row dd label { width:60px; }
.form .purviewBox .subject { clear:both; height:30px; line-height:30px; font-size:14px; color:#00f; }
.form .purviewBox .row { vertical-align:middle; }
.form .purviewBox input { margin:0px 5px; }
.form .purviewBox .row dt { float:left; font-weight:bold; color:#f0f; }
.form .purviewBox .row dd { float:left; margin:0px 5px; }
</style>
</head>
<body>
<script>
<?php
	$navtitle = '系统后台管理';
	foreach($this->breadcrumbs as $key => $val)
	{
		$navtitle .=  ($navtitle) ? '&nbsp;&raquo;&nbsp;' . Yii::t('admincp', $val) : '' . Yii::t('admincp', $val);
	}
?>
if($(parent.document).find('#admincpnav').size()>0) {
	$(parent.document).find('#admincpnav').html('<?php echo $navtitle; ?>');
}

</script>
<style type="text/css">
/*
table,td,th,body,dt,dd,dl{ margin:0; padding:0; border:none;}
#nav { background: repeat-x url(<?php echo $this->module->assetsUrl; ?>/images/repeat.gif) 0 -209px ; font-size:12px; position:static; top:0; left:0;height:32px; line-height:26px; padding: 0 10px; }
#nav a { color:#666; text-decoration:none; }
#nav dt, #nav dd { float:right;}
#nav dd { color:#999;}
#nav dt,#nav dd.link {padding-right:16px; background:url(<?php echo $this->module->assetsUrl; ?>/images/img_bg.gif) no-repeat right -204px;}
*/
</style>

<div id="cpcontainer" class="container">
	<div id="append_parent"></div>
	<?php
		if(!empty($this->menu)) {
	?>
	<div class="floattop">
		<div class="itemtitle">
			<h3><?php echo Yii::t('admincp', $this->menu[0]['label']); ?></h3>
			<ul class="tab1">
				<?php
					for($i=1; $i<count($this->menu); $i++) {
						$item = $this->menu[$i];
				?>
				<li<?php echo isset($item['cur']) && $item['cur'] ? ' class="current"' : ''; ?>><a href="<?php echo $item['url']; ?>"><span><?php echo Yii::t('admincp', $item['label']); ?></span></a></li>
				<?php
					}
				?>
				<!--<li class="current"><a href="admin.php?action=announce"><span>管理</span></a></li>
				<li><a href="admin.php?action=announce&operation=add"><span>添加</span></a></li>-->
			</ul>
		</div>
	</div>
	<div class="floattopempty"></div>
	<?php
		}
		
		echo $content;
	?>
</div>
</body>
</html>

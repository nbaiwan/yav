<?php
	$basePath = $this->module->assetsUrl;
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta name="language" content="zh_cn" />
<title><?php echo CHtml::encode($this->pageTitle); ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta content="Comsenz Inc." name="Copyright" />
<link rel="stylesheet" href="<?php echo $basePath; ?>/css/main.css" type="text/css" media="all" />
<script src="<?php echo $basePath; ?>/js/jquery.min.js" type="text/javascript"></script>
<script src="<?php echo $basePath; ?>/js/jquery-ui.min.js" type="text/javascript"></script>
</head>
<body style="margin: 0px" scroll="no">
<div id="append_parent"></div>
<table id="frametable" cellpadding="0" cellspacing="0" width="100%" height="100%">
  <tr>
    <td colspan="2" height="90"><div class="mainhd">
        <div class="logo">快播科技</div>
        <div class="uinfo" id="frameuinfo">
          <p>你好,&nbsp;<em><?php echo Yii::app()->user->name; ?></em>&nbsp;[<?php echo Yii::app()->user->role_name; ?>] [ <a href="<?php echo url($this->module->id . '/Default/Logout'); ?>" target="_top">退出</a> ]</p>
          <p class="btnlink"><a href="index.php" target="_blank">站点首页</a></p>
        </div>
        <div class="navbg"></div>
        <div class="nav">
          <ul id="topmenu">
          	<?php
          		foreach($menus as $identify=>$menu) {
          			if(isset($menu['show']) && $menu['show']===false) {
          				continue;
          			}
          			echo "<li><em><a href=\"{$menu['url']}\" id=\"header_{$identify}\" hidefocus=\"true\" onMouseOver=\"previewheader('{$identify}')\" onMouseOut=\"previewheader()\" onClick=\"toggleMenu('{$identify}', this.href);doane(event);\">" . Yii::t('admincp', $menu['title']) . "</a></em></li>\r\n";
          		}
          	?>
          </ul>
          <div class="currentloca">
            <p id="admincpnav"></p>
          </div>
          <div class="navbd"></div>
          <div class="sitemapbtn">
          	<!-- 
            <div style="float: left; margin:-7px 10px 0 0">
              <form name="search" method="post" autocomplete="off" action="admin.php?action=search" target="main">
                <input type="text" name="keywords" value="" class="txt" />
                <input type="hidden" name="searchsubmit" value="yes" class="btn" />
                <input type="submit" name="searchsubmit" value="搜索" class="btn" style="margin-top: 5px;vertical-align:middle" />
              </form>
            </div>
            -->
            <span id="add2custom" style="display: none"></span> <a href="###" id="cpmap" onClick="showMap();return false;"><img src="<?php echo $basePath; ?>/images/btn_map.gif" title="管理中心导航(ESC键)" width="46" height="18" /></a> </div>
        </div>
      </div></td>
  </tr>
  <tr>
    <td valign="top" width="160" class="menutd">
      <div id="leftmenu" class="menu">
        <?php
          	foreach($menus as $identify=>$menu) {
          		if(!isset($menu['submenu']) || !is_array($menu['submenu'])) {
          			continue;
          		}
        ?>
        <ul id="menu_<?php echo $identify; ?>" style="display: none">
        <?php
          	foreach($menu['submenu'] as $subidentify=>$submenu) {
          		if(isset($submenu['show']) && $submenu['show']===false) {
          			continue;
          		}
        ?>
          <li><a href="<?php echo $submenu['url']; ?>" hidefocus="true" target="main"><em onClick="menuNewwin(this)" title="新窗口打开"></em><?php echo Yii::t('admincp', $submenu['title']); ?></a></li>
        <?php
        	}
        ?>
        </ul>
        <?php
        	}
        ?>
      </div>
    </td>
    <td valign="top" width="100%" class="mask">
	    <iframe src="<?php echo url($this->module->id . '/Main/Index'); ?>" id="main" name="main" width="100%" height="100%" frameborder="0" scrolling="yes" style="overflow: visible;display:"></iframe>
    </td>
  </tr>
</table>
<div id="scrolllink" style="display: none"> <span onClick="menuScroll(1)"><img src="static/image/admincp/scrollu.gif" /></span> <span onClick="menuScroll(2)"><img src="static/image/admincp/scrolld.gif" /></span> </div>
<div class="copyright">
  <p>Powered by <a href="http://www.discuz.net/" target="_blank">Discuz! </a>X1.5</p>
  <p>&copy; 2001-2010, <a href="http://www.comsenz.com/" target="_blank">Comsenz Inc.</a></p>
</div>
<div id="cpmap_menu" class="custom" style="display: none">
  <div class="cmain" id="cmain"></div>
  <div class="cfixbd"></div>
</div>
<script type="text/JavaScript"><!--
	<?php
		$headers = '';
		foreach($menus as $identify=>$menu) {
			$headers .= (($headers) ? ", '{$identify}'" : "'{$identify}'");
		}
	?>
	var headers = new Array(<?php echo $headers; ?>), menukey = '';
	function switchheader(key) {
		if(!key || ($('#header_' + key).size()==0)) {
			return;
		}
		for(var k in top.headers) {
			if($('#menu_' + headers[k]).size()>0) {
				$('#menu_' + headers[k]).css({'display' : headers[k] == key ? '' : 'none'});
			}
		}
		$('#topmenu').find('li').removeClass('navon');
		$('#header_' + key).parent().parent().addClass('navon');
	}
	var headerST = null;
	function previewheader(key) {
		if(key) {
			headerST = setTimeout(function() {
				for(var k in top.headers) {
					if($('#menu_' + headers[k]).size()>0) {
						$('#menu_' + headers[k]).css('display', headers[k] == key ? '' : 'none');
					}
				}
				var hrefs = $('#menu_' + key).find('a');
				for(var j = 0; j < hrefs.size(); j++) {
					$(hrefs[j]).attr('class', '');
				}
			}, 1000);
		} else {
			clearTimeout(headerST);
		}
	}
	function toggleMenu(key, url) {
		menukey = key;
		switchheader(key);
		if(typeof(url)!='undefined') {
			parent.main.location = url;
			var hrefs = $('#menu_' + key).find('a');
			for(var j = 0; j < hrefs.size(); j++) {
				$(hrefs[j]).attr('class', j ==0 ? 'tabon' : '');
			}
		}
		setMenuScroll();
	}
	function setMenuScroll() {
		$('#frametable').css("width", document.body.offsetWidth < 1000 ? '1000px' : '100%');
		var obj = $('#menu_' + menukey);
		if(obj.size()==0) {
			return;
		}
		var scrollh = document.body.offsetHeight - 160;
		obj.css('overflow', 'visible');
		obj.css('height', '');
		$('#scrolllink').css('display', 'none');
		if(obj.offsetHeight + 150 > document.body.offsetHeight && scrollh > 0) {
			obj.style.overflow = 'hidden';
			obj.style.height = scrollh + 'px';
			$('#scrolllink').css('display', '');
		}
	}
	function resizeHeadermenu() {
		var lis = $('#topmenu').find('li');
		var maxsize = $('#frameuinfo').offset().left - 160, widths = 0, moi = -1, mof = '';
		if($('#menu_mof').size()>0) {
			$('#topmenu').find('#menu_mof').remove();
		}
		if($('#menu_mof_menu').size()>0) {
			$('#append_parent').find('#menu_mof_menu').remove();
		}
		for(var i = 0; i < lis.size(); i++) {
			//widths += $(lis[i]).offsetWidth;
			widths += $(lis[i]).width();
			if(widths > maxsize) {
				$(lis[i]).css('visibility', 'hidden');
				//var sobj = lis[i].childNodes[0].childNodes[0];
				var sobj = $(lis[i]).children(':first-child').children(':first-child');
				if(sobj.size()>0) {
					mof += '<a href="'+ sobj.attr('href') + '" onclick="$(\'' + sobj.attr('id') + '\').onclick()">&rsaquo; ' + sobj.html() + '</a><br style="clear:both" />';
				}
			} else {
				$(lis[i]).css('visibility', 'visible');
			}
		}
		if(mof) {
			for(var i = 0; i < lis.length; i++) {
				if($(lis[i]).css('visibility') == 'hidden') {
					moi = i;
					break;
				}
			}
			mofli = document.createElement('li');
			mofli.innerHTML = '<em><a href="javascript:;">&raquo;</a></em>';
			mofli.onmouseover = function () { showMenu({'ctrlid':'menu_mof','pos':'43'}); }
			mofli.id = 'menu_mof';
			$('topmenu').insertBefore(mofli, lis[moi]);
			mofmli = document.createElement('li');
			mofmli.className = 'popupmenu_popup';
			mofmli.style.width = '150px';
			mofmli.innerHTML = mof;
			mofmli.id = 'menu_mof_menu';
			mofmli.style.display = 'none';
			$('append_parent').appendChild(mofmli);
		}
	}
	function menuScroll(op, e) {
		var obj = $('menu_' + menukey);
		var scrollh = document.body.offsetHeight - 160;
		if(op == 1) {
			obj.scrollTop = obj.scrollTop - scrollh;
		} else if(op == 2) {
			obj.scrollTop = obj.scrollTop + scrollh;
		} else if(op == 3) {
			if(!e) e = window.event;
			if(e.wheelDelta <= 0 || e.detail > 0) {
				obj.scrollTop = obj.scrollTop + 20;
			} else {
				obj.scrollTop = obj.scrollTop - 20;
			}
		}
	}
	function menuNewwin(obj) {
		window.open(obj.parentNode.href);
		doane();
	}
	function initCpMenus(menuContainerid) {
		var key = '', lasttabon1 = null, lasttabon2 = null, hrefs = $('#' + menuContainerid).find('a');
		for(var i = 0; i < hrefs.size(); i++) {
			if(menuContainerid == 'leftmenu' && $(hrefs[i]).attr('href').indexOf('Special/Index')!=-1) {
				if(lasttabon1) {
					$(lasttabon1).attr('class', '');
				}
				key = $(hrefs[i]).parent().parent().attr('id').substr(5);
				$(hrefs[i]).attr('class', 'tabon');
				lasttabon1 = hrefs[i];
			}
			if(!$(hrefs[i]).attr('ajaxtarget')) $(hrefs[i]).bind('click', function() {
				if(menuContainerid != 'custommenu') {
					$('#leftmenu').find('li').children(':first-child').filter('[class=tabon]').removeClass('tabon')
					$(this).addClass(menuContainerid == 'leftmenu' ? 'tabon' : '');
				}
				if(menuContainerid != 'leftmenu') {
					var hk, currentkey;
					var leftmenus = $('#leftmenu').find('a');
					for(var j = 0; j < leftmenus.size(); j++) {
						hk = $(leftmenus[j]).parent().parent().attr('id').substr(5);
						if(this.href.indexOf(leftmenus[j].href) != -1) {
							if(lasttabon2) {
								$(lasttabon2).attr('class', '');
							}
							$(leftmenus[j]).attr('class', 'tabon');
							lasttabon2 = leftmenus[j];
							if(hk != 'index') currentkey = hk;
						} else {
							$(leftmenus[j]).attr('class', '');
						}
					}
					if(currentkey) toggleMenu(currentkey);
					hideMap();
				}
					
			});
		
		}
		return key;
	}
	var header_key = initCpMenus('leftmenu');
	toggleMenu(header_key ? header_key : 'index');
	function initCpMap() {
		var ul, hrefs, s = '', count = 0;
		for(var k in headers) {
			if(headers[k] != 'index') {
				s += '<td valign="top"><ul class="cmblock"><li><h4>' + $('#header_' + headers[k]).html() + '</h4></li>';
				ul = $('#menu_' + headers[k]);
				if(ul.size()==0) {
					continue;
				}
				hrefs = ul.find('a');
				for(var i = 0; i < hrefs.size(); i++) {
					s += '<li><a href="' + $(hrefs[i]).attr('href') + '" target="' + $(hrefs[i]).attr('target') + '" k="' + headers[k] + '">' + $(hrefs[i]).html() + '</a></li>';
				}
				s += '<li></li></ul></td>';
				count++;
			}
		}
		var width = (count > 11 ? 11 : count) * 80;
		s = '<div class="cnote" style="width:' + width + 'px"><span class="right"><a href="###" class="flbc" onclick="hideMap();return false;"></a></span><h3>管理中心导航</h3></div>' +
			'<div class="cmlist" style="width:' + width + 'px"><table id="mapmenu" cellspacing="0" cellpadding="0" ><tr>' + s +
			'</tr></table></div>';
		$('#cmain').html(s);
		$('#cmain').css('width', (width > 1000 ? 1000 : width) + 'px');

		var width = $('#cpmap_menu').children().width()+30;
		$('#cpmap_menu').dialog({
			autoOpen: false,
			width: width + 'px',
			modal: true,
			buttons: {},
			open: function() {
				$('.ui-dialog-titlebar,.ui-resizable-handle').hide();
			}
		});
	}
	initCpMap();
	initCpMenus('mapmenu');
	var cmcache = false;
	function showMap() {
		$('#cpmap_menu').dialog('open');
	}
	
	function hideMap() {
		$('#cpmap_menu').dialog('close');
	}
	function resetEscAndF5(e) {
		e = e ? e : window.event;
		actualCode = e.keyCode ? e.keyCode : e.charCode;
		
		if(actualCode == 27) {
			if($('#cpmap_menu').parent().css('display') == 'none') {
				showMap();
			} else {
				hideMap();
			}
			if(document.all) {
				e.keyCode = 0;
				e.returnValue = false;
			} else {
				e.cancelBubble = true;
				e.preventDefault();
			}
		}
		if(actualCode == 116 && parent.main) {
			parent.main.location.reload();
			if(document.all) {
				e.keyCode = 0;
				e.returnValue = false;
			} else {
				e.cancelBubble = true;
				e.preventDefault();
			}
		}
	}
	
	function doane(event) {
		e = event ? event : window.event;
		if(!e) e = getEvent();
		if(e && navigator.userAgent.toLowerCase().indexOf('ie')!=-1) {
			e.returnValue = false;
			e.cancelBubble = true;
		} else if(e) {
			e.stopPropagation();
			e.preventDefault();
		}
	}
	$(document).children().keydown(resetEscAndF5);
	//_attachEvent(document.documentElement, 'keydown', resetEscAndF5);
	//_attachEvent(window, 'resize', setMenuScroll, document);
	//_attachEvent(window, 'resize', resizeHeadermenu, document);
	//if(BROWSER.ie){
	//	$('leftmenu').onmousewheel = function(e) { menuScroll(3, e) };
	//} else {
	//	$('leftmenu').addEventListener("DOMMouseScroll", function(e) { menuScroll(3, e) }, false);
	//}
	resizeHeadermenu();
--></script>
</body>
</html>

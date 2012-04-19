/**
 * 
 */

function updateArc() {
	
}

function selAll() {
	$('#content-archives-form').find('input[type=checkbox]').attr('checked', true);
}

function cancelSel() {
	$('#content-archives-form').find('input[type=checkbox]').removeAttr('checked');
}

function recomArc() {
	var selObj = $('input[type=checkbox]:checked');

	if(selObj.length <= 0) {
		alert('必须选择一款或多款游戏！');
		return false;
	}
	
	$('#content-archives-form')
		.attr('action', ARCHIVES_COMMEND_URL)
		.submit();
}

function delArc() {
	var selObj = $('input[type=checkbox]:checked');

	if(selObj.length <= 0) {
		alert('必须选择一款或多款游戏！');
		return false;
	}
	
	$('#content-archives-form')
	.attr('action', ARCHIVES_DELETE_URL)
	.submit();
}

/////////////////////////////////////////////
function changeAttr(act, event, self, csrfToken) {
	var qstr = '', alist = '', title = '', dopost = '';
	var selObj = $('input[type=checkbox]:checked');

	if(selObj.length <= 0) {
		alert('必须选择一款或多款游戏！');
		return false;
	}

	if(act == 'del') {
		$('#attribute_dialog .titLeft').html('批量删除属性');
		$('#attribute_dialog input[name=dopost]').val('del');
	} else {
		$('#attribute_dialog .titLeft').html('批量增加属性');
		$('#attribute_dialog input[name=dopost]').val('add');
	}
	
	for(i=0;i<selObj.length;i++) {
		qstr += qstr ? ',' + $(selObj[i]).val() : $(selObj[i]).val();
		alist += $(selObj[i]).parent().parent().next().html() + '<br />';
	}
	
	/*
	$('#attribute_dialog input[name=qstr]').val(qstr);
	$('#archives_list').html(alist);
	var top = $(window).scrollTop() + ($(window).height() - $('#attribute_dialog').height()) / 2;
	var left = $(window).scrollLeft() + ($(window).width() - $('#attribute_dialog').width()) / 2;
	$('#attribute_dialog').css({top:top+'px',left:left+'px'}).show();*/
	
	if(act == 'del') {
		title = '批量删除属性';
		dopost = 'del';
	} else {
		title = '批量增加属性';
		dopost = 'add';
	}
	
	$.dialog.show({
		id: 'dialog1',
		title: title,
		method: 'post',
		url: ARCHIVES_ATTR_URL,
		width: 450,
		height: 160,
		hidden: {"Archives[dopost]": dopost, "Archives[content_archives_id]": qstr, YII_CSRF_TOKEN: ARCHIVES_CSRFTOKEN},
		callback: function(){},
		data:[
			['属性',ARCHIVES_ATTRS],
			['文档列表',alist]
		]
	});
}
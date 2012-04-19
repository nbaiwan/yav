
/*****wan123后台颜色ＪＳ****/
$(function(){
	var colors = new Array('035990', '258CE4', 'FF3C00', 'DA0404', '9535B8', 'B01B7A', '03A33A', '017F07', '05A8A6', 'FFF600', '00FF12', '333333');
	var html = '';
	html += "<div class=\"ColorDiv\">";
	html += "<ul>";

	for(var i in colors) {
		html += "<li style=\"background:#"+colors[i]+";\" color=\""+colors[i]+"\"></li>";
	}

	html += "</ul>";
	html += "</div>";

	$('#color_dd').append(html);
	
	$("input[name='Archive[content_archives_color]']").live('focus', function(){
		$('.ColorDiv').show();
		
	});
	
	$('.ColorDiv ul li').each(function(){
		$(this).click(function(){
			$('input[name="Archive[content_archives_color]"]').val($(this).attr('color')).css({background:'#'+$(this).attr('color')});
			$('.ColorDiv').hide();
		});
	});
	
	$('input[name="Archive[content_archives_subject]"]').bind('propertychange', function(){
		title_len_stats();
		
	});
	

	$('input[name="Archive[content_archives_subject]"]').bind('input', function(){
		title_len_stats();
		
	});
	
	function title_len_stats() {
		$('#title_count').text(str_len($('input[name="Archive[content_archives_subject]"]').val()));
	}
	
	function str_len(str)
	{
		var len = 0;
		for (var i = 0; i < str.length; i++) {
			var c = str.charCodeAt(i);                
			if ((c >= 0x0001 && c <= 0x007e) || (0xff60 <= c && c <= 0xff9f)) {//单字节加1
				len++;
			} else {
				len += 2;
			}
		}
		return len;
	}
	
});

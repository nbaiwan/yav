/**
 * 
 * $('#dialog').dialog({
 * 	title: '',
 * 	method: 'get',
 * 	url: '',
 * 	width: '',
 * 	height: '',
 * 	hidden: {"Archives[dopost]":"add", "Archives[content_archives_id]":"1,2,3"},
 * 	callback: function(){},
 * 	data:{
 * 		['属性','']
 * 	}
 * });
 */

(function($){
	$.extend({
		dialog: {
			_defaults: {
				id: "dialog",
				title: "",
				method: "",
				url: "",
				callback: function(){},
				width: 450,
				height: 230,
				hidden: {},
				data: {}
			},
			show: function(options) {
				this.options = $.extend(this._defaults, options);
				
				var str = "";
				str += "<div id=\""+this.options.id+"\" class=\"pubdlg\" style=\"display: none; width:" + this.options.width + "px; min-height:" + this.options.height + "px; _height:" + this.options.height + "px\">";
				str += "	<div onmousedown=\"DropStartHand('dialog');\" class=\"title\">";
				str += "		<div class=\"titLeft\">" + this.options.title + "</div>";
				str += "		<div class=\"titRight\"><img src=\""+SYSTEM_ASSETS_URL+"/images/ico-close.gif\" title=\"关闭对话框\" alt=\"关闭对话框\" onclick=\"$.dialog.close()\" style=\"cursor: pointer;\" /></div>";
				str += "	</div>";
	
				if(this.options.method != "") {
					str += "	<form method=\"" + this.options.method + "\" action=\"" + this.options.url + "\" name=\"quickeditform\">";
					for(var i in this.options.hidden) {
						str += "<input type=\"hidden\" name=\"" + i + "\" value=\"" + this.options.hidden[i] + "\" />";
					}
				}
	
				str += "	<table width=\"100%\" style=\"margin-top: 6px; z-index: 9000;\">";
				str += "		<tbody>";
	
				if(this.options.data) {
					for(var i in this.options.data) {
						str += "			<tr height=\"32\">";
						str += "				<td width=\"80\" valign=\"top\" class=\"bline\">&nbsp;" + this.options.data[i][0] + "：</td>";
						str += "				<td class=\"bline\">" + this.options.data[i][1] + "</td>";
						str += "			</tr>";
					}
				}
				
				str += "			<tr height=\"32\">";
				str += "				<td align=\"center\" style=\"padding-top: 12px;\" colspan=\"2\">";
				str += "					<input name=\"imageField\" type=\"image\" src=\""+SYSTEM_ASSETS_URL+"/images/button_ok.gif\" width=\"60\" height=\"22\" border=\"0\" class=\"np\" style=\"padding:0px; cursor: pointer;\" />";
				str += "					&nbsp;&nbsp;";
				str += "					<img src=\""+SYSTEM_ASSETS_URL+"/images/button_back.gif\" width=\"60\" height=\"22\" border=\"0\" style=\"cursor: pointer;\" onclick=\"$.dialog.close();\" />";
				str += "				</td>";
				str += "			</tr>";
				str += "		</tbody>";
				str += "	</table>";
				str += "	</form>";
				str += "</div>";
				str += "<div id=\"fullpagediv\" style=\"display:none;\" class=\"fullpagediv\"></div>";
				
				$('body').append(str);
				var _t = $(window).scrollTop() + ($(window).height() - $('#' + this.options.id).height()) / 2;
				var _l = $(window).scrollLeft() + ($(window).width() - $('#' + this.options.id).width()) / 2;
				
				$('#' + this.options.id).css({top:_t+'px',left:_l+'px'}).show();
				ChangeFullDiv('show');
			},
			close: function() {
				$('#' + this.options.id).remove();
				$('#fullpagediv').remove();
			}
		}
	});
})(jQuery);

function $Nav()
{
	if(window.navigator.userAgent.indexOf("MSIE")>=1) return 'IE';
	else if(window.navigator.userAgent.indexOf("Firefox")>=1) return 'FF';
	else return "OT";
}
function $Obj(objname)
{
	return document.getElementById(objname);
}
//通用事件获取接口
function getEvent()
{ 
	if($Nav()=='IE')  return window.event;
	func=getEvent.caller;       
	while(func!=null)
	{ 
		var arg0 = func.arguments[0];
		if(arg0)
		{
			if((arg0.constructor==Event || arg0.constructor ==MouseEvent) 
			|| (typeof(arg0)=="object" && arg0.preventDefault && arg0.stopPropagation))
			{ 
				return arg0;
			}
		}
		func=func.caller;
	}
	return null;
}

function HideObj(objname)
{
	var obj = $Obj(objname);
	if(obj == null) return false;
	obj.style.display = "none";
}

function ChangeFullDiv(showhide,screenheigt)
{
	var newobj = $Obj('fullpagediv');
	if(showhide=='show')
	{
		if(!newobj)
		{
			newobj = document.createElement("DIV");
			newobj.id = 'fullpagediv';
			newobj.style.position='absolute';
			newobj.className = 'fullpagediv';
            newobj.style.height=screenheigt + 'px';
			document.body.appendChild(newobj);
		}
		else
		{
			newobj.style.display = 'block';
		}
	}
	else
	{
		if(newobj) newobj.style.display = 'none';
	}
}

var canMove = false;
var dropObj = null;
var mleftLeaning = 0;
function DropStartHand(objid)
{
	var event = getEvent();
	canMove = (canMove ? false : true);
	dropObj = objid;
	mleftLeaning = event.clientX - $('#'+objid).position().left;
	$(document).bind('mouseup', DropStopHand);
	$(document).bind('mousemove', DropMoveHand);
}

function DropStopHand()
{
	canMove = false;
	dropObj = null;
	mleftLeaning = 0;
	$(document).unbind('mouseup', DropStopHand);
	$(document).unbind('mousemove', DropMoveHand);
}

function DropMoveHand(objid)
{
	var event = getEvent();
	var obj = $Obj(dropObj);
	if(!canMove) return;
	
	if($Nav()=='IE')
	{ 
		var posLeft = event.clientX;
		var posTop = event.clientY-20;
		posTop += window.pageYOffset||document.documentElement.scrollTop||document.body.scrollTop;
	}
	else
	{
		var posLeft = event.pageX;
		var posTop = event.pageY-20;
	}
	obj.style.top = posTop+"px";
	obj.style.left = posLeft - mleftLeaning + "px";
}

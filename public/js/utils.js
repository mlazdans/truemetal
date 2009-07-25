var border1 = "1px solid #444444";
var border2 = "1px solid white";
var padding1 = "6px";
var padding2 = "5px";

function button_click(button)
{
	with (button.style)
	{
		paddingLeft = padding1;
		paddingTop = padding1;
		paddingRight = padding2;
		paddingBottom = padding2;
		borderLeft = border1;
		borderTop = border1;
		borderRight = border2;
		borderBottom = border2;
	}
	button_clear_sel();
}

function button_release(button)
{
	with (button.style)
	{
		paddingLeft = padding2;
		paddingTop = padding2;
		paddingRight = padding1;
		paddingBottom = padding1;
		borderRight = border1;
		borderBottom = border1;
		borderLeft = border2;
		borderTop = border2;
	}
	button_clear_sel();
}

function button_clear_sel()
{
	if(document.selection)
		document.selection.empty();
}

function resizeDialogToContent()
{
	// resize window so there are no scrollbars visible
	var dh = window.dialogHeight;
	while (isNaN(dh))
	{
		dh = dh.substr(0,dh.length-1);
	}
	var dw = window.dialogWidth;
	while (isNaN(dw))
	{
		dw = dw.substr(0,dw.length-1);
	}
	//alert('dh:' + dh + ' window.dialogHeight:' + window.dialogHeight + ' this.document.body.clientHeight:' + this.document.body.clientHeight);
	difh = dh - this.document.body.clientHeight;
	difw = dw - this.document.body.clientWidth;

	//window.dialogHeight = this.document.body.scrollHeight+difh+'px';
	//window.dialogWidth = this.document.body.scrollWidth+difw+'px';
	window.dialogHeight = this.document.body.scrollHeight+difh+'px';
	window.dialogWidth = this.document.body.scrollWidth+difw+'px';
	//alert(window.dialogWidth);
}

function checkAll(form, what)
{
	for (var i = 0; i < form.elements.length; i++)
	{
		var e = form.elements[i];
		if (e.type == 'checkbox')
			e.checked = form[what.name].checked;
	}
}

function search(http_root)
{
	if(search_q.value)
		location.replace(http_root + '/search/' + search_q.value)
	else {
		alert('J?vada mekl?mie v?i!');
		search_q.focus();
	}
}

function checkType()
{
	var l_type=document.getElementsByName('data[l_type]');

	var l_spec=document.getElementById('data[l_spec]');
	var l_sertnr=document.getElementById('data[l_sertnr]');
	var l_sertexpire=document.getElementById('data[l_sertexpire]');

	l_spec.disabled=!l_type[0].checked;
	l_sertnr.disabled=!l_type[0].checked;
	l_sertexpire.disabled=!l_type[0].checked;
}

function pop(theURL,w,h,name)
{
	var l = (screen.width - w) / 2;
	var t = (screen.height - h) / 2;

	window.open(theURL, name, 'fullscreen=0,toolbar=0,status=0,scrollbars=0,menubar=0,location=0,resizable=0,channelmode=0,directories=0,width=' + w + ',height=' + h + ',top=' + t +',left=' + l);
}

function checkDel(form, selName, msg)
{
	if(!msg)
		msg = 'Pārliecināts?';

	var el = form.elements[selName];
	if(el && (el.value == 'delete'))
		return confirm(msg);

	return true;
}

function checkDelSimple(msg)
{
	if(!msg)
		msg = 'Pārliecināts?';

	return confirm(msg);
}

function Vote(cId, value, voteXpath)
{
	$.getJSON("/vote/" + value + "/" + cId + "/json/",
		function(data)
		{
			if(!data || !data.Votes)
			{
				if(data.msg)
				{
					alert(data.msg);
				}
				return false;
			}

			if(data.Votes > 0)
				$(voteXpath).html('+' + data.Votes).removeClass("Comment-Vote-minus").addClass("Comment-Vote-plus");
			else if(data.Votes < 0)
				$(voteXpath).html(data.Votes).addClass("Comment-Vote-minus").removeClass("Comment-Vote-plus");
			else
				$(voteXpath).html(data.Votes).removeClass("Comment-Vote-minus").removeClass("Comment-Vote-plus").addClass("Comment-Vote");
		});
} // Vote

function ytEmbed()
{
	var videoId = this.className.split(" ")[1];
	if(videoId)
	{
		$(this).wrap('<' + 'div style="text-align: center; height: 395px;"' +'><' + '/div>');
		var params = { allowScriptAccess: "always", wmode: "transparent" };
		var atts = { align: 'center' };
		swfobject.embedSWF("http://www.youtube.com/v/" + videoId, this.id, "480", "395", "8", null, null, params, atts);
	}
} // ytEmbed


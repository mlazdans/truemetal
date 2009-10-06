var Truemetal = {
	/*
	checkAll: function(form, what){
		for (var i = 0; i < form.elements.length; i++){
			var e = form.elements[i];
			if (e.type == 'checkbox')
				e.checked = form[what.name].checked;
		}
	},*/
	checkAll: function(form, ref){
		if(form && ref)
		{
			if(typeof form != "object")
			{
				form = $(form);
			}

			$('input:checkbox', form).attr('checked', $(ref).attr('checked'));
		} else if(form) {
			ref = form;
			form = form.form;
			Truemetal.checkAll(form, ref);
		}
	},
	Pop: function(theURL,w,h,name) {
		var l = (screen.width - w) / 2;
		var t = (screen.height - h) / 2;

		window.open(theURL, name, 'fullscreen=0,toolbar=0,status=0,scrollbars=0,menubar=0,location=0,resizable=0,channelmode=0,directories=0,width=' + w + ',height=' + h + ',top=' + t +',left=' + l);
	},
	Vote: function(cId, value, voteXpath) {
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
					$(voteXpath).html('+' + data.Votes).removeClass("minus").addClass("plus");
				else if(data.Votes < 0)
					$(voteXpath).html(data.Votes).addClass("minus").removeClass("plus");
				else
					$(voteXpath).html(data.Votes).removeClass("minus").removeClass("plus").addClass("Comment-Vote");
			});
	}, // Vote
	wrapYouTube: function(el){
		var videoId = el.className.split(" ")[1];
		if(videoId)
		{
			$(el).wrap('<' + 'div style="text-align: center; height: 395px;"' +'><' + '/div>');
			var params = { allowScriptAccess: "always", wmode: "transparent" };
			var atts = { align: 'center' };
			swfobject.embedSWF("http://www.youtube.com/v/" + videoId, el.id, "480", "395", "8", null, null, params, atts);
		}
	},
	scrollYouTube: function(e){
		var yt = (typeof e.data == 'object' ? e.data : e);
		var scrollY = $(window).attr('scrollY');
		var WH = $(window).height();

		yt.each(function(i, el){
				if(yt[i].yt === true)
				{
					return;
				}

				var p = $(el).position();
				var h = $(el).height();
				if( (p.top >= scrollY) && ((p.top + h) <= (scrollY + WH)) )
				{
					Truemetal.wrapYouTube(el);
					yt[i].yt = true;
				}
		});
	},
	initYouTube: function() {
		var yt = $('div.youtube');
		$(window).bind('scroll', yt, Truemetal.scrollYouTube);
		Truemetal.scrollYouTube(yt);
		/*
		$('div.youtube').each(function(){
				var videoId = this.className.split(" ")[1];
				if(videoId)
				{
					$(this).wrap('<' + 'div style="text-align: center; height: 395px;"' +'><' + '/div>');
					var params = { allowScriptAccess: "always", wmode: "transparent" };
					var atts = { align: 'center' };
					swfobject.embedSWF("http://www.youtube.com/v/" + videoId, this.id, "480", "395", "8", null, null, params, atts);
				}
		});
		*/
	}, // ytEmbed
	initMenu: function(){
		$('.menu img').each(function(){
				var src = this.src;
				var parts = this.src.split('.');
				var ext = parts.pop();
				try {
					var srcOver = parts.join('.') + "_over.gif";
					$(this).mouseenter(function(){
							this.src = srcOver;
					});
					$(this).mouseleave(function(){
							this.src = src;
					});
				} catch(e1) {
				}
		});
	}
};


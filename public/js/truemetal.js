/**
* @author Samele Artuso <samuele.a@gmail.com>
*/
(function($) {
	$.fn.unselectable = function() {
		return this.each(function() {

			$(this)
				.css('-moz-user-select', 'none')		// FF
				.css('-khtml-user-select', 'none')		// Safari, Google Chrome
				.css('user-select', 'none');			// CSS 3

			if ($.browser.msie) {						// IE
				$(this).each(function() {
					this.ondrag = function() {
						return false;
					};
				});
				$(this).each(function() {
					this.onselectstart = function() {
						return (false);
					};
				});
			} else if($.browser.opera) {
				$(this).attr('unselectable', 'on');
			}
		});
	};
})(jQuery);

/**
* @author Martins Lazdans <marrtins@dqdp.net>
*/
var Truemetal = {
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
		var videoId = Truemetal.getUrlVars($(el).attr("href")).v;
		if(videoId)
		{
			$(el).attr("id", "yt-" + videoId);
			$(el).wrap('<' + 'div style="text-align: center; height: 395px;"' +'><' + '/div>');
			var params = { allowScriptAccess: "always", wmode: "transparent" };
			var atts = { align: 'center' };
			swfobject.embedSWF("http://www.youtube.com/v/" + videoId, "yt-" + videoId, "480", "395", "8", null, null, params, atts);
		}
		/*
		var videoId = Truemetal.getUrlVars($("a", el).attr("href")).v;
		if(videoId)
		{
			$(el).attr("id", "yt-" + videoId);
			$(el).wrap('<' + 'div style="text-align: center; height: 395px;"' +'><' + '/div>');
			var params = { allowScriptAccess: "always", wmode: "transparent" };
			var atts = { align: 'center' };
			swfobject.embedSWF("http://www.youtube.com/v/" + videoId, "yt-" + videoId, "480", "395", "8", null, null, params, atts);
		}
		*/
	},
	scrollYouTube: function(e){
		var yt = (typeof e.data == 'object' ? e.data : e);
		var scrollY = $(window).scrollTop();
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
		$('.col1 a').each(function(i,a){
				// youtube.com
				if(a.hostname.match(/youtube.com$/i))
				{
					$(a).addClass("youtube");
					/*
					Truemetal.wrapYouTube(a);
					var params = Truemetal.getUrlVars(a.href);
					if(params.v)
					{
						//var el = $(a).wrap('<' + 'div class="youtube"><' + '/div>');
						//Truemetal.wrapYouTube(el);
					}
					*/
				}
		});
		/*
		var yt = $('div.youtube');
		$(window).bind('scroll', yt, Truemetal.scrollYouTube);
		Truemetal.scrollYouTube(yt);
		*/
		var ytA = $("a.youtube");
		$(window).bind('scroll', ytA, Truemetal.scrollYouTube);
		Truemetal.scrollYouTube(ytA);
	},
	initMenu: function(){
		$('.menu img').each(function(){
				var src = this.src;
				var parts = this.src.split('.');
				var ext = parts.pop();
				try {
					var srcOver = parts.join('.');
					if(srcOver.substr(srcOver.length-5) != '_over')
					{
						srcOver = srcOver + "_over." + ext;
					} else {
						srcOver = srcOver + "." + ext;
					}
					$(this).mouseenter(function(){
							this.src = srcOver;
					});
					$(this).mouseleave(function(){
							this.src = src;
					});
				} catch(e1) {
				}
		});
	},
	initUnselectable: function(){
		$(".unselectable").unselectable();
		/*
		$(".unselectable").mousedown(function(){
				return false;
		});
		*/
	}, //initMenu
	getUrlVars: function(url){
		var vars = {}, hash;
		var hashes = url.slice(url.indexOf('?') + 1).split('&');
		for(var i = 0; i < hashes.length; i++)
		{
			hash = hashes[i].split('=');
			vars[hash[0]] = hash[1];
		}

		return vars;
	}, // getUrlVars
	viewProfile: function(login){
		var dOptions = {
			width: 400,
			dialogClass: "loading",
			buttons: {
					"Aizvērt": function(){
						$(this).dialog("destroy");
					}
			}
		};

		var dialog = $('<div/>').dialog(dOptions);
		$.ajax({
				url: "/user/profile/" + login + "/?json=1",
				dataType: 'json',
				success: function(data){
					$(dialog).dialog("option", "title", data.title);
					$(dialog).dialog("option", "dialogClass", "");
					$(dialog).html(data.html);
				}
		});
	},
	viewProfileImage: function(login, w, h, nick){
		var dOptions = {
			width: w + 20,
			//height: h + 35,
			dialogClass: "loading"
		};

		var dialog = $('<div/>').dialog(dOptions);
		$('<img/>', {
				src: "/user/image/" + login + "/",
				width: w,
				height: h,
				border: 0,
				click: function(){
					$(dialog).dialog("destroy");
				},
				load: function(){
					if(nick)
						$(dialog).dialog("option", "title", "[ TRUEMETAL " + nick + " bilde ]");
					$(dialog).dialog("option", "dialogClass", "");
				}
		}).appendTo(dialog);
	}
};


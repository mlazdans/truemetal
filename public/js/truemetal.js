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

			$('input:checkbox', form).prop('checked', $(ref).prop('checked'));
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

		// short youtu.be
		if(!videoId && el.hostname.match(/youtu.be$/i)){
			var parts = $(el).attr("href").split('/');
			videoId = parts[parts.length - 1];
		}

		if(videoId)
		{
			/*
			$(el).attr("id", "yt-" + videoId);
			$(el).wrap('<' + 'div style="text-align: center; height: 395px;"' +'><' + '/div>');
			var params = { allowScriptAccess: "always", wmode: "transparent" };
			var atts = { align: 'center' };
			swfobject.embedSWF("http://www.youtube.com/v/" + videoId, "yt-" + videoId, "480", "395", "8", null, null, params, atts);
			*/

			$(el).attr("id", "yt-" + videoId);
			$(el).html('<img class="lazy" src="http://img.youtube.com/vi/' + videoId + '/0.jpg" width="480" height="395" />');
			$(el).wrap('<' + 'div style="text-align: center; height: 395px;"' +'><' + '/div>');

			var params = { allowScriptAccess: "always", wmode: "transparent" };
			var atts = { align: 'center' };

			$(el).on('click', function () {
					var div = this.parentNode;
					$(div).html('<embed src="http://www.youtube.com/v/' + videoId + '?version=3&autoplay=1" type="application/x-shockwave-flash" width="480" height="395" allowscriptaccess="always" wmode="transparent"></embed>');
					return false;
			});
		}
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
	initYouTubeUrls: function(){
		$('.col1 a').each(function(i,a){
				// youtube.com
				if(a.hostname.match(/youtube.com$/i) || a.hostname.match(/youtu.be$/i)){
					$(a).addClass("youtube");
				}
		});
	},
	/*
	initNewYouTube: function() {
		Truemetal.initYouTubeUrls();
		var ytA = $("a.youtube");
		$(window).bind('scroll', ytA, Truemetal.scrollYouTube);
		Truemetal.scrollYouTube(ytA);

		return;

		$('.col1 a').each(function(i,a){
				// youtube.com
				if(!a.hostname.match(/youtube.com$/i) && !a.hostname.match(/youtu.be$/i)){
					return;
				}

				var videoId = Truemetal.getUrlVars($(a).attr("href")).v;
				// short youtu.be
				if(!videoId && a.hostname.match(/youtu.be$/i)){
					var parts = $(a).attr("href").split('/');
					videoId = parts[parts.length - 1];
				}

				if(!videoId){
					return;
				}

				var href = $('<a/>', {
						'class': "yt",
						href: "#"
				});

				var div = $('<div/>', {
						style: "text-align: center; height: 395px;",
						align: "center",
						videoId: videoId
				});

				var img = $('<img/>', {
						'class': 'lazy',
						'src': '/img/1x1.gif',
						'data-original': 'http://img.youtube.com/vi/' + videoId + '/0.jpg',
						'width': 480,
						'height': 395
				});

				$(a).replaceWith(div.append(href.append(img)));
				$('img.lazy').lazyload();
				//$(this).trigger('scroll');

				$(href).on('click', function () {
						var div = this.parentNode;
						$(div).html('<embed src="http://www.youtube.com/v/' + $(div).attr('videoId') + '?version=3&autoplay=1" type="application/x-shockwave-flash" width="480" height="395" allowscriptaccess="always" wmode="transparent"></embed>');
						return false;
				});
		});

		/*
		var liteDivs = $('.lite');
		var vid, w, h, myDiv, img, a, button;

		for (var i = 0; i < liteDivs.length; i++) {
			myDiv = liteDivs[i];
			vid = myDiv.id;
			w = myDiv.style.width;
			h = myDiv.style.height;

			img = $(document.createElement('img'));
			img.attr({
				'class': 'lazy',
				'data-original': 'http://img.youtube.com/vi/' + vid + '/0.jpg',
				'width': 480,
				'height': 395
			});
			img.css({'position': 'relative', 'top': '0', 'left': '0' });

			a = $(document.createElement('a'));
			a.href = '#';
			/*
			button = document.createElement('img');
			button.setAttribute('class', 'lite');
			button.src = 'http://lh4.googleusercontent.com/-QCeB6REIFlE/TuGUlY3N46I/AAAAAAAAAaI/9-urEUtpKcI/s800/youtube-play-button.png';
			button.style.position = 'absolute';
			button.style.top = Math.round((myDiv.clientHeight - 51) / 2) + 'px';
			button.style.left = Math.round((myDiv.clientWidth - 71) / 2) + 'px';
			/
			//$(myDiv)
			//	.html(a.append(img/*, button/));
			$(myDiv).html(img);

			/*
			$.ajax({
				url: 'http://gdata.youtube.com/feeds/api/videos/' + vid + '?v=2&fields=id,title&alt=json',
				dataType: 'json',
				success: function (data) {
				$(document.getElementById(data.entry.id.$t.split(':')[3]))
					.append('<div style="position:relative;margin:-' + h + ' 5px;padding:5px;background-color:rgba(0,0,0,0.3);-moz-border-radius:7px;-webkit-border-radius:7px;border-radius:7px"><span style="font-weight:bold;font-size:16px;color:#ffffff;font-family:sans-serif;text-align:left;">' + data.entry.title.$t + '</span></div>');
				}
			});
			/
		}
		return false;
		/
	},
	*/
	initYouTube: function() {
		Truemetal.initYouTubeUrls();
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
		//$(".unselectable").unselectable();
		$('.unselectable').on('selectstart dragstart select', function(evt){ evt.preventDefault(); return false; });
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


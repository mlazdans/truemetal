const truemetal = document;
var Truemetal = {
	SimpleDialog: function(msg, title = ""){
		 $('<div/>', {
			html: msg
		}).dialog({
			title: title,
			buttons: {
				"Aizvērt": function(){
					$(this).dialog("destroy");
				}
			}
		});
	},
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
	_attend_handler(req, status){
		if(req?.responseJSON?.OK){
			location.reload();
		} else {
			Truemetal.HandleJsonError(req, status);
		}
	},
	Attend(res_id){
		$.ajax({
			url: "/attend/" + res_id + "/?json",
			dataType: 'json',
			complete: _attend_handler
		});
	},
	AttendNo(res_id){
		$.ajax({
			url: "/attend/" + res_id + "/off/?json",
			dataType: 'json',
			complete: _attend_handler
		});
	},
	HandleJsonError: function(req, status){
		let data = req?.responseJSON;
		if(data === undefined){
			Truemetal.SimpleDialog("Kaut kas nogāja greizi", "Kļūda");
		} else if(data.html !== undefined) {
			Truemetal.SimpleDialog(data?.html, data?.title);
		} else {
			console.error("Nez kas te domāts", data);
		}
	},
	Vote: function(cId, value, voteXpath) {
		$.ajax({
			url: "/vote/" + value + "/" + cId + "/?json",
			dataType: 'json',
			complete: function(req, status){
				let data = req?.responseJSON;
				if(data.Votes === undefined){
					return Truemetal.HandleJsonError(req, status);
				}

				if(data.Votes > 0){
					$(voteXpath).html('+' + data.Votes).removeClass("minus").addClass("plus");
				} else if(data.Votes < 0) {
					$(voteXpath).html(data.Votes).addClass("minus").removeClass("plus");
				} else {
					$(voteXpath).html(data.Votes).removeClass("minus").removeClass("plus").addClass("Comment-Vote");
				}
			}
		});
	},
	wrapYouTube: function(el){
		var videoId = Truemetal.getUrlVars($(el).attr("href")).v;
		var q = '';
		var t = '';

		// short youtu.be
		if(!videoId && el.hostname.match(/youtu.be$/i)){
			var parts = $(el).attr("href").split('/');
			var qparts = parts[parts.length - 1].split('?');

			videoId = qparts[0];

			if(qparts.length > 1){
				q = qparts[1].split("=")[1];
				var m = q.split("m")[0];
				var s = q.split("m")[1].split('s')[0];
				var t = m * 60 + s * 1;
			}
		}

		if(videoId)
		{
			$(el).attr("id", "yt-" + videoId);
			$(el).html('<img class="lazy" src="https://img.youtube.com/vi/' + videoId + '/0.jpg" width="480" height="395" />');
			$(el).wrap('<' + 'div style="text-align: center; height: 395px;"' +'><' + '/div>');

			var params = { allowScriptAccess: "always", wmode: "transparent" };
			var atts = { align: 'center' };

			$(el).on('click', function () {
					var div = this.parentNode;
					$(div).html('<embed src="https://www.youtube.com/v/' + videoId + '?version=3&autoplay=1' + (t ? '&start=' + t : '') + '" type="application/x-shockwave-flash" width="480" height="395" allowscriptaccess="always" wmode="transparent"></embed>');
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
			url: "/user/profile/" + login + "/?json",
			dataType: 'json',
			complete: function(req, status){
				if (req.responseJSON){
					let data = req.responseJSON;
					$(dialog).dialog("option", "title", data.title);
					$(dialog).dialog("option", "dialogClass", "");
					$(dialog).html(data.html);
				}
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
	},
	initProfiles(){
		$(truemetal).on("click", ".Profile", function() {
			let hash = this?.dataset?.hash;

			if(hash){
				Truemetal.viewProfile(hash);
			}

			return false;
		});
	}
};


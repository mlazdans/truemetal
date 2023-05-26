const truemetal = document;
var Truemetal = {
	ajaxDialog(id){
		let el;
		if($('#' + id).length){
			el = $('#' + id);
		} else {
			el = $("<div>").attr("id", id);
		}

		return $(el).dialog({
			width: $('#main').width() * 0.75,
			// classes: {
			// 	'ui-dialog': 'loading'
			// },
			dialogClass: "loading1",
			title: "Ielāde...",
			height: "auto",
			// buttons: [{
			// 	text: "Aizvērt",
			// 	click: function(){
			// 		$(this).dialog("destroy");
			// 	}
			// }],
			close: function(){
				$(this).dialog("destroy");
			}
		});
	},
	SimpleDialog(msg, title, dialog){
		if(dialog){
			$(dialog).dialog("option", {
				position: { 'my': 'center', 'at': 'center', of: window },
				dialogClass: "",
				width: $('#main').width() * 0.75,
				height: "auto",
				title: title === undefined ? "" : title
			}).html(msg);
		} else {
			$('<div>').dialog({
				width: $('#main').width() * 0.5,
				title: title === undefined ? "" : title,
				dialogClass: "",
				// buttons: {
				// 	"Aizvērt": function(){
				// 		$(this).dialog("destroy");
				// 	}
				// }
			}).html(msg);
		}
	},
	HandleStandardJson(req, status, dialog){
		let data = req?.responseJSON;
		if(data === undefined){
			Truemetal.SimpleDialog("Kaut kas nogāja greizi", "Kļūda", dialog);
		} else if(data?.html !== undefined) {
			Truemetal.SimpleDialog(data?.html, data?.title, dialog);
		} else {
			Truemetal.SimpleDialog("Nezināma kļūda...", "Ooops...", dialog);
		}
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
	_attend_handler(res_id, req, status){
		if(req?.responseJSON?.OK){
			$.ajax({
				url: "/attend/" + res_id + "/?json&get",
				dataType: 'json',
				complete: function(req, status){
					let data = req?.responseJSON;
					if(data?.html === undefined){
						return Truemetal.HandleStandardJson(req, status);
					} else {
						$("#attendees" + res_id).replaceWith(data.html);
					}
				}
			});
		} else {
			Truemetal.HandleStandardJson(req, status);
		}
	},
	Attend(res_id){
		$.ajax({
			url: "/attend/" + res_id + "/?json",
			dataType: 'json',
			complete: function(req, status){
				Truemetal._attend_handler(res_id, req, status);
			}
		});
	},
	AttendNo(res_id){
		$.ajax({
			url: "/attend/" + res_id + "/off/?json",
			dataType: 'json',
			complete: function(req, status){
				Truemetal._attend_handler(res_id, req, status);
			}
		});
	},
	Vote(res_id, value){
		const voteXpath = "#votes-" + res_id;

		$(voteXpath).addClass('loading2');

		$.ajax({
			url: "/vote/" + value + "/" + res_id + "/?json",
			dataType: 'json',
			complete: function(req, status){
				$(voteXpath).removeClass('loading2');

				let data = req?.responseJSON;
				if(data?.Votes === undefined){
					return Truemetal.HandleStandardJson(req, status);
				}

				if(data.Votes > 0){
					$(voteXpath).html('+' + data.Votes).removeClass("vote-minus vote-zero").addClass("vote-plus");
				} else if(data.Votes < 0) {
					$(voteXpath).html(data.Votes).removeClass("vote-plus vote-zero").addClass("vote-minus");
				} else {
					$(voteXpath).html(data.Votes).removeClass("vote-minus vote-plus").addClass("vote-zero");
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

		if(videoId){
			// $(el).attr("id", "yt-" + videoId);
			let wrap = $('<div class="yt" style="background-image: url(https://img.youtube.com/vi/' + videoId + '/mqdefault.jpg")"></div>');
			// $(el).html('<img src="https://img.youtube.com/vi/' + videoId + '/mqdefault.jpg" />');

			$(wrap).on('click', function () {
				//$(div).html('<embed src="https://www.youtube.com/v/' + videoId + '?version=3&autoplay=1' + (t ? '&start=' + t : '') + '" type="application/x-shockwave-flash" width="480" height="395" allowscriptaccess="always" wmode="transparent"></embed>');
				//$(div).html('<embed src="https://www.youtube.com/v/' + videoId + '?version=3&autoplay=1' + (t ? '&start=' + t : '') + '" type="application/x-shockwave-flash" style="width: 90%; height: 500px;" allowscriptaccess="always" wmode="transparent"></embed>');
				$(this).replaceWith('<div class="yt"><iframe src="https://www.youtube.com/embed/' + videoId + '?&autoplay=1' + (t ? '&start=' + t : '') + '" frameborder="0" allowfullscreen style="width: 100%; height: 30vh"></iframe></div>');

				return false;
			});

			$(el).replaceWith(wrap);
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
		$('#main a').each(function(i,a){
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
	// initMenu: function(){
	// 	$('.menu img').each(function(){
	// 		var src = this.src;
	// 		var parts = this.src.split('.');
	// 		var ext = parts.pop();
	// 		try {
	// 			var srcOver = parts.join('.');
	// 			if(srcOver.substr(srcOver.length-5) != '_over')
	// 			{
	// 				srcOver = srcOver + "_over." + ext;
	// 			} else {
	// 				srcOver = srcOver + "." + ext;
	// 			}
	// 			$(this).mouseenter(function(){
	// 					this.src = srcOver;
	// 			});
	// 			$(this).mouseleave(function(){
	// 					this.src = src;
	// 			});
	// 		} catch(e1) {
	// 		}
	// 	});
	// },
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
	},
	initProfiles(){
		$(truemetal).on("click", ".ProfilePopup", function() {
			let hash = this?.dataset?.hash;
			if(hash){
				Truemetal.displayProfile(hash);
				return false;
			}
		});
		$(truemetal).on("click", ".ProfileImage", function() {
			let hash = this?.dataset?.hash;
			if(hash){
				Truemetal.displayProfileImage(hash, this?.dataset?.nick);
				return false;
			}
		});
		$(truemetal).on("click", ".SendVote", function() {
			let res_id = this?.dataset?.res_id;
			let vote = this?.dataset?.vote;
			if(res_id && vote){
				Truemetal.Vote(res_id, vote);
				return false;
			}
		});
	},
	displayProfile(hash){
		var dialog = Truemetal.ajaxDialog("profile" + hash);
		$.ajax({
			url: "/user/profile/" + hash + "?json",
			dataType: 'json',
			complete: function(req, status){
				Truemetal.HandleStandardJson(req, status, dialog);
			}
		});
	},
	displayProfileImage(hash, nick){
		var dialog = Truemetal.ajaxDialog("image" + hash).html(
			$('<img>', {
				src: "/user/image/" + hash,
				border: 0,
				on: {
					click: () => $(dialog).dialog("destroy"),
					load: () => {
						$(dialog).dialog("option", {
							position: { 'my': 'center', 'at': 'center', of: window },
							dialogClass: "",
							width: "auto",
							height: "auto",
							title: "[ TRUEMETAL " + nick + " bilde ]",
						});
					}
				}
			})
		);
	},
	clickSearchOptions(e){
		let include_comments = $("input[name=include_comments]", e.form).get(0);
		let only_titles = $("input[name=only_titles]", e.form).get(0);

		if(e.name == "include_comments"){
			if(only_titles.checked){
				only_titles.checked = !e.checked;
			}
		}

		if(e.name == "only_titles"){
			if(include_comments.checked){
				include_comments.checked = !e.checked;
			}
		}
	}
};

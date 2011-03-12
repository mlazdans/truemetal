var Admin = {
	viewCommentVotes: function(c_id) {
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

		$.getJSON("/admin/comment/votes/view/" + c_id + "/json/",
			function(data)
			{
				$(dialog).dialog("option", "title", data.title);
				$(dialog).dialog("option", "dialogClass", "");
				$(dialog).html(data.html);
			});
	}
};


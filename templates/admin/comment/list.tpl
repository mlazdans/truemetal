<!-- BEGIN BLOCK_nocomments disabled -->
<div class="TD-cat">Komentāri</div>
<div>Nav neviena komentāra</div>
<!-- END BLOCK_nocomments -->

<!-- BEGIN BLOCK_comments disabled -->
<form action="" method="post" id="comments">
<table class="Main">
<tr>
	<td class="TD-cat">
		<input type="checkbox" name="comment_check_all" onclick="Truemetal.checkAll(this)">
	</td>
	<td colspan="6" class="TD-cat">
		Komentāri
	</td>
</tr>
<!-- BEGIN BLOCK_comment_item -->
<tr>
	<th class="{c_color_class}">
		<input type="checkbox" name="res_ids[]" value="{res_id}">
	</th>
	<th class="{c_color_class} nowrap"><a href="/admin/logins/{login_id}/">{res_nickname}</a></th>
	<th class="{c_color_class}"><a href="/admin/reports/?report=ip&amp;ips={res_ip}">{res_ip}</a></th>
	<th class="{c_color_class} nowrap">{res_entered}</th>
	<th class="{c_color_class}">
		<!-- BEGIN BLOCK_c_visible disabled -->aktīvs<!-- END BLOCK_c_visible -->
		<!-- BEGIN BLOCK_c_invisible disabled -->neaktīvs<!-- END BLOCK_c_invisible -->
	</th>
	<th class="{c_color_class}"><a href="#" onclick="Admin.viewCommentOriginal({res_id}); return false;">orig</a></th>
	<th class="{c_color_class}"><a href="#" onclick="Admin.viewCommentVotes({res_id}); return false;">Votes: {res_votes}</a></th>
</tr>
<tr>
	<td></td>
	<td class="{c_color_class}" colspan="6">
		<div><a href="{c_origin_href}">{c_origin_name}</a></div>
		{res_data_compiled}
	</td>
</tr>
<!-- END BLOCK_comment_item -->
<tr>
	<td colspan="7">
		Iezīmētos: <select name="action" onchange="if(this.value == 'comment_move')$('#lnew_res_id').show(); else $('#lnew_res_id').hide();">
		<option value="">---</option>
		<option value="comment_delete">Dzēst</option>
		<option value="comment_show">Aktivizēt</option>
		<option value="comment_hide">Deaktivizēt</option>
		<option value="comment_move">Pārvietot</option>
		</select>
		<label for="new_res_id" id="lnew_res_id" style="display: none;">Tēma<input type="text" name="new_res_id" id="new_res_id" value=""></label>

		<input type="submit" value="  OK  ">
	</td>
</tr>
</table>
</form>
<!-- END BLOCK_comments -->

<script>
$(document).ready(function(){
		//if(this.value == 'comment_move')$('#lnew_res_id').show(); else $('#lnew_res_id').hide();
		if($('[name=action]').val() == 'comment_move')
			$('#lnew_res_id').show();
		else
			$('#lnew_res_id').hide();

		$('#new_res_id').autocomplete({
				source: function(request, response){
					$.ajax({
							url: "/admin/res/search/",
							dataType: "json",
							data: {
								q: request.term
							},
							success: function(data){
								response($.map(data.data, function (value, key) {
										return {
											label: value.doc_name,
											value: value.doc_id
										}
								}));
								//response(data.data);
							}
					});
				},
				minLength: 1,
				select: function( event, ui ){
					//console.log( "Selected: " + ui.item.value + " aka " + ui.item.id);
				}
		});
});
</script>

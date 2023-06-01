<div class="Forum-cat" id="attendees{res_id}">Solās ierasties:
	<!-- BEGIN BLOCK_attend_list disabled -->
		<a href="/user/profile/{l_hash}/" class="ProfilePopup" data-hash="{l_hash}">{l_nick}</a>{l_nick_sep}
	<!-- END BLOCK_attend_list -->
	<br>
	<!-- BEGIN BLOCK_attend_on disabled -->
	<a href="/attend/{res_id}/" type="button" onclick="Truemetal.Attend('{res_id}'); return false;">Es ar' nāks!!</a>
	<!-- END BLOCK_attend_on -->
	<!-- BEGIN BLOCK_attend_off disabled -->
	<a href="/attend/{res_id}/off/" onclick="Truemetal.AttendNo('{res_id}'); return false;">Es tomēr nenāks!</a>
	<!-- END BLOCK_attend_off -->
</div>

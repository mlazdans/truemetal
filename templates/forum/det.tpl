<div class="TD-cat">{forum_name}</div>

<!-- BEGIN BLOCK_attend disabled -->
	<div class="Forum-cat">Solās ierasties:
		<!-- BEGIN BLOCK_attend_list disabled -->
			<a href="/user/profile/{l_hash}/" class="Profile" data-hash="{l_hash}">{l_nick}</a>{l_nick_sep}
		<!-- END BLOCK_attend_list -->
	</div>
	<!-- BEGIN BLOCK_attend_on disabled -->
	<div class="List-item">
		<a href="/attend/{res_id}/" type="button" onclick="Truemetal.Attend('{res_id}'); return false;">Es ar' nāks!</a>
	</div>
	<!-- END BLOCK_attend_on -->

	<!-- BEGIN BLOCK_attend_off disabled -->
	<div class="List-item">
		<a href="/attend/{res_id}/off/" onclick="if(confirm('Tu ko?! Nezini, kas ir Amorphis???'))Truemetal.AttendNo('{res_id}'); return false;">Es tomēr nenāks!</a>
	</div>
	<!-- END BLOCK_attend_off -->
<!-- END BLOCK_attend -->


<!-- BEGIN BLOCK_noforum disabled -->
<div class="Info">
	Pagaidām šai tēmai nav neviena komentāra!
</div>
<!-- END BLOCK_noforum -->

<!-- BEGIN BLOCK_info_sort_A disabled -->
<div class="List-item">
	Ziņojumi sakārtoti pēc ievadīšanas datuma augoši
</div>
<!-- END BLOCK_info_sort_A -->
<!-- BEGIN BLOCK_info_sort_D disabled -->
<div class="List-item">
	Ziņojumi sakārtoti pēc ievadīšanas datuma dilstoši
</div>
<!-- END BLOCK_info_sort_D -->

<!-- BEGIN BLOCK_forum_error disabled -->
<div class="error">{error_msg}</div>
<!-- END BLOCK_forum_error -->

<!-- BEGIN BLOCK_forum_comments --><!-- END BLOCK_forum_comments -->

<!-- BEGIN BLOCK_forum_closed disabled -->
<div class="Info">
	Tēma slēgta
</div>
<!-- END BLOCK_forum_closed -->


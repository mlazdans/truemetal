<table cellpadding="1" cellspacing="1" border="0" width="100%">
	<tr>
		<td class="TD-cat">{searched}: {search_q}</td>
	</tr>
<!-- BEGIN BLOCK_search disabled -->
	<!-- BEGIN BLOCK_searchitem -->
	<tr>
		<td class="TD-search" style="padding-left: 21px">
			<table cellpadding="0" cellspacing="0" border="0" width="100%">
				<tr>
					<td width="100%"><a href="{http_root}{search_path}/?hl={search_q}">{search_name}&nbsp;</a></td>
					<td>{search_cat}</td>
				</tr>
			</table>
		</td>
	</tr>
	<!-- END BLOCK_searchitem -->
<!-- END BLOCK_search -->
<!-- BEGIN BLOCK_notfound disabled -->
	<tr>
		<td>{search_not_found}</td>
	</tr>
<!-- END BLOCK_notfound -->
<!-- BEGIN BLOCK_searcherror disabled -->
	<tr>
		<td>{search_error}</td>
	</tr>
<!-- END BLOCK_searcherror -->
</table>
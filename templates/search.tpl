<div class="TD-cat">Meklēšana: {search_q}</div>

<form method="post">
	<table class="Main">
		<tr>
			<td>Frāze:</td>
			<td><input type="text" name="search_q" id="search_q" value="{search_q}" size="64" style="width: 99%;" /></td>
		</tr>
		<tr>
			<td>Sadaļas:</td>
			<td>
				<!-- BEGIN BLOCK_search_sources -->
				<label for="section_{source_id}">
				<input{source_checked} type="checkbox" class="checkbox" name="sources[]" id="section_{source_id}" value="{source_id}" />
				{source_name}
				</label>
				<!-- END BLOCK_search_sources -->
			</td>
		</tr>
		<tr>
			<td>
				<label for="only_titles">Tikai virsrakstos:</label>
			</td>
			<td>
				<input{only_titles_checked} type="checkbox" class="checkbox" name="only_titles" id="only_titles" />
			</td>
		</tr>
		<tr>
			<td colspan="2"><input type="submit" value="Meklēt" /></td>
		</tr>
	</table>
</form>

<div class="List-sep"></div>

<!-- BEGIN BLOCK_search_msg disabled -->
<div class="Info">{search_msg}</div>
<!-- END BLOCK_search_msg -->

<!-- BEGIN BLOCK_search_help -->
<div class="TD-cat">Piemēri</div>
<dl>
	<dt>truemetal</dt>
		<dd>Atrodam truemetal</dd>
	<dt>true*</dt>
		<dd>Atrodam pašu labumu: viss, kas sākas ar true</dd>
	<dt>truemetal -gay</dt>
		<dd>Atrodam truemetal, bet tikai bez gejisma</dd>
	<dt>(true | metal) -gay -emo*</dt>
		<dd>Atrodam true vai metal bez gejisma un bez visa, kas sākas ar emo</dd>
</dl>
<!-- END BLOCK_search_help -->

<!-- BEGIN BLOCK_search disabled -->
<div class="List-sep"></div>
<div class="TD-cat">
	Rezultāti: {doc_count}
</div>

<table class="Main">
	<!-- BEGIN BLOCK_search_item disabled -->
	<tr class="List-item">
		<td>{doc_module_name}</td>
		<td style="text-align: left; width: 100%;"><a href="{res_route}">{doc_name}&nbsp;</a> ({doc_comment_count})</td>
		<td style="text-align: right;">{doc_comment_last_date}</td>
	</tr>
	<!-- END BLOCK_search_item -->
</table>
<!-- END BLOCK_search -->


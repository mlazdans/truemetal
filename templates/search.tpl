<div class="TD-cat">Meklēšana: {search_q}</div>

<form method="post">
	<table class="Main">
		<tr>
			<td>Frāze:</td>
			<td><input type="text" name="search_q" id="search_q" value="{search_q}" size="64" style="width: 99%;"></td>
		</tr>
		<tr>
			<td>Sadaļas:</td>
			<td>
				<!-- BEGIN BLOCK_search_sources -->
				<label>
				<input{source_checked} type="checkbox" class="checkbox" name="sources[]" value="{source_id}">
				{source_name}
				</label>
				<!-- END BLOCK_search_sources -->
			</td>
		</tr>
		<tr>
			<td>Opcijas:</td>
			<td>
				<label><input{include_comments_checked} type="checkbox" class="checkbox" name="include_comments" onclick="Truemetal.clickSearchOptions(this);"> komentāros</label>
				<label><input{only_titles_checked} type="checkbox" class="checkbox" name="only_titles" onclick="Truemetal.clickSearchOptions(this);"> tikai virsrakstos</label>
			</td>
		</tr>
		<tr>
			<td colspan="2"><input type="submit" value="Meklēt"></td>
		</tr>
	</table>
</form>

<div class="List-sep"></div>

<!-- BEGIN BLOCK_search_help -->
<div class="TD-cat">Piemēri</div>
<div class="TD-content">
	<dl>
		<dt>truemetal</dt>
			<dd>Atrodam precīzi &quot;truemetal&quot;</dd>
		<dt>true*</dt>
			<dd>Atrodam frāzes, kas sākas ar &quot;true&quot;</dd>
		<dt>truemetal -gay</dt>
			<dd>Atrodam &quot;truemetal&quot;, bet tikai bez līksmes</dd>
		<dt>(true | metal) -gay -emo*</dt>
			<dd>Atrodam precīzi &quot;true&quot; vai &quot;metal&quot; bez līksmes un frāzēm, kas sākas ar &quot;emo&quot;</dd>
	</dl>
</div>
<!-- END BLOCK_search_help -->

<!-- BEGIN BLOCK_search disabled -->
<div class="List-sep"></div>
<div class="TD-cat">
	Rezultāti: {doc_count}
</div>

<div class="TD-content">
	<!-- BEGIN BLOCK_search_msg disabled -->
	<div class="List-item">{search_msg}</div>
	<!-- END BLOCK_search_msg -->
</div>

<table class="Main">
	<!-- BEGIN BLOCK_search_item disabled -->
	<tr class="List-item">
		<td>{doc_module_name}</td>
		<td style="text-align: left; width: 100%;"><a href="{res_route}">{doc_name}&nbsp;</a> ({doc_comment_count})</td>
		<td style="text-align: right;">{doc_date}</td>
	</tr>
	<!-- END BLOCK_search_item -->
</table>
<!-- END BLOCK_search -->

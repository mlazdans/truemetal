<div class="TD-cat" title="Atbildes jautājumam: {poll_question}">
	{poll_question}
</div>

<!-- BEGIN BLOCK_poll_r_items disabled -->
<div class="List-item">
	{poll_name}
</div>
<div class="Poll-result">
	<div class="Poll-bar" style="width: {count_percent}%">&nbsp;</div>
	{count_votes} ({count_percent}%)
</div>
<!-- END BLOCK_poll_r_items -->

<div class="List-item" style="text-align: right;">
	<strong>Kopā: {total_votes}</strong>
</div>

<!-- BEGIN BLOCK_poll_archive disabled -->
<div class="TD-cat">
	Pārējie jautājumi
</div>

<!-- BEGIN BLOCK_poll_archive_item -->
<div class="List-item">
	<a href="{module_root}/results/{poll_id_a}/">{poll_name_a}</a>
</div>
<!-- END BLOCK_poll_archive_item -->
<!-- END BLOCK_poll_archive -->

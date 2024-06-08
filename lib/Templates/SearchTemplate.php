<?php declare(strict_types = 1);

class SearchTemplate extends AbstractTemplate
{
	public array $DOC_SOURCES;
	public array $checked_sources;
	public array $res;
	public bool $include_comments_checked = false;
	public bool $only_titles_checked = false;
	public bool $show_help = false;
	public string $search_q;
	public string $search_msg = "";

	private function header(): void
	{
		$search_q = specialchars($this->search_q); ?>
		<div class="TD-cat">Meklēšana: <?=$search_q ?></div>

		<form method="post">
			<table class="Main">
				<tr>
					<td>Frāze:</td>
					<td><input type="text" name="search_q" id="search_q" value="<?=$search_q ?>" size="64" style="width: 99%;"></td>
				</tr>
				<tr>
					<td>Sadaļas:</td>
					<td><?
						foreach($this->DOC_SOURCES as $id=>$sect)
						{
							$source_checked = empty($this->checked_sources) || in_array($id, $this->checked_sources); ?>
							<label>
								<input <?=checkedif($source_checked) ?> type="checkbox" class="checkbox" name="sources[]" value="<?=$id ?>">
								<?=$sect['name'] ?>
							</label><?
						} ?>
					</td>
				</tr>
				<tr>
					<td>Opcijas:</td>
					<td>
						<label><input <?=checkedif($this->include_comments_checked) ?> type="checkbox" class="checkbox" name="include_comments" onclick="Truemetal.clickSearchOptions(this);"> komentāros</label>
						<label><input <?=checkedif($this->only_titles_checked) ?> type="checkbox" class="checkbox" name="only_titles" onclick="Truemetal.clickSearchOptions(this);"> tikai virsrakstos</label>
					</td>
				</tr>
				<tr>
					<td colspan="2"><input type="submit" value="Meklēt"></td>
				</tr>
			</table>
		</form>

		<div class="List-sep"></div><?
	}

	protected function help(): void
	{ ?>
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
		</div><?
	}

	protected function out(): void
	{
		$doc_count = empty($this->res['matches']) ? 0 : $this->res['total_found'];
		$this->header();
		if($this->show_help){
			$this->help();
		}
		?>
		<div class="List-sep"></div>
		<div class="TD-cat">
			Rezultāti: <?=$doc_count ?>
		</div>

		<? if($this->search_msg) { ?>
			<div class="TD-content">
				<div class="List-item"><?=$this->search_msg ?></div>
			</div>
		<? } ?>

		<? if(!empty($this->res['matches'])) { ?>
			<table class="Main"><?
			foreach($this->res['matches'] as $doc){
				$item = $doc['attrs'];
				$item['doc_module_name'] = $this->DOC_SOURCES[$item['doc_source_id']]['name'];

				if($r = ResEntity::get((int)$item['res_id'])){
					$item['res_route'] = $r->res_route."?hl=".urlencode($this->search_q);
				} else {
					trigger_error("No res for search item:".printrr($item), E_USER_WARNING);
					$item['res_route'] = "/";
				}

				$item['doc_date'] = date('d.m.Y', $item['doc_entered']); ?>
				<tr class="List-item">
					<td><?=$item['doc_module_name'] ?></td>
					<td style="text-align: left; width: 100%;"><a href="<?=$item['res_route'] ?>"><?=$item['doc_name'] ?>&nbsp;</a> (<?=$item['doc_comment_count'] ?>)</td>
					<td style="text-align: right;"><?=$item['doc_date'] ?></td>
				</tr><?
			}
		} ?>
		</table><?
	}
}

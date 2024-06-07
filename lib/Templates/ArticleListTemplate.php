<?php declare(strict_types = 1);

class ArticleListTemplate extends AbstractTemplate
{
	public ViewMainpageCollection $articles;

	function mainpage(ViewMainpageType $item)
	{
		$res_date = date('d.m.Y', strtotime($item->res_entered));
		$comment_class = Res::not_seen($item->res_id, $item->res_comment_last_date??$item->res_entered) ? "Comment-count-new" : "Comment-count-old";

		if($item->res_kind == ResKind::FORUM)
		{
			$module_id = "forum";
		} else {
			$module_id = $item->module_id;
		}

		$item->res_entered = date('d.m.Y', strtotime($item->res_entered));

		if($item->res_kind == ResKind::FORUM)
		{
			if($item->type_id){
				$intro = mb_substr($item->res_data, 0, 300);
				$intro = specialchars($intro);
				if(mb_strlen($item->res_data) > 300){
					$intro .= "...";
				}
				$item->res_intro = $intro;
				$item->res_data = '';
			} else {
				$data_parts = preg_split("/<hr(\s+)?\/?>/", $item->res_data);

				if(isset($data_parts[0]))
					$item->res_intro = $data_parts[0];

				if(isset($data_parts[1]))
				{
					$item->res_data = $data_parts[1];
				} else {
					$item->res_data = '';
				}
			}
		} elseif($item->res_kind == ResKind::ARTICLE){
		} else {
			throw new InvalidArgumentException("Unexpected table ID: $item->res_kind");
		}

		?>
		<div class="TD-cat">
			<div class="res-date"><?=$res_date ?></div>
			<div class="res-name"><a href="<?=$item->res_route ?>"><?=$item->res_name ?></a></div>
			<div class="res-comments-link">
				<a href="<?=$item->res_route ?>#art-comments-<?=$item->doc_id ?>">Komentāri
				<span class="Comment-count <?=$comment_class ?>">(<?=$item->res_comment_count ?>)</span></a>
			</div>
		</div>

		<div class="Article-item">
			<div class="data">
				<?=$item->res_intro ?>
				<? if($item->res_data) { ?>
				<div>
					<a href="<?=$item->res_route ?>">..tālāk..</a>
				</div>
				<? } ?>
			</div>
		</div><?
	}

	protected function out(): void {
		foreach($this->articles as $item){
			$this->mainpage($item);
		}
	}
}

<?php declare(strict_types = 1);

require_once('../include/boot.php');
require_once('include/dbconnect.php');
require_once('include/console.php');

$F = (new ResFilter(
	res_visible:false,
));

printf("SET NAMES utf8;\n");
printf("START TRANSACTION;\n");
// printf("PREPARE update_route FROM '%s';\n", "UPDATE res SET res_route = ? WHERE res_id = ?");
// prepares     : command took 0:1:10.18 (70.18s total)
// non-prepares : command took 0:0:43.97 (43.97s total)

function res_route_dump(AbstractResEntity $E, ResFilter $F){
	if(!($q = $E->query($F)))
	{
		throw new Error("Could not query");
	}

	while($r = $E->fetch($q))
	{
		printf("UPDATE res SET res_route = '%s' WHERE res_id = %d;\n", DB::Quote($r->Route()), $r->res_id);
		// printf("SET @res_route = '%s'; SET @res_id = %d;\n", DB::Quote($r->Route()), $r->res_id);
		// printf("EXECUTE update_route USING @res_route, @res_id;\n");
	}
}

res_route_dump(new ViewResArticleEntity, $F);
res_route_dump(new ViewResForumEntity, $F);
res_route_dump(new ViewResCommentEntity, $F);
res_route_dump(new ViewResGalleryEntity, $F);
res_route_dump(new ViewResGdEntity, $F);

printf("COMMIT;\n");

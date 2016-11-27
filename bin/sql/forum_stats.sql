SELECT
	COUNT(*) AS theme_count
FROM
	`forum`
WHERE
	`forum_allowchilds`='N' AND
	`forum_active`='Y';


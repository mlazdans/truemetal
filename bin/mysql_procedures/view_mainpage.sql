CREATE OR REPLACE VIEW view_mainpage AS
(SELECT
	m.module_id,
	a.art_id,
	COALESCE(cm_comment_count, 0) AS art_comment_count,
	cm_comment_lastdate AS art_comment_lastdate,
	a.art_name,
	a.art_intro,
	a.art_data,
	a.art_entered,
	a.art_comments,
	'article' AS cm_table
FROM
	`article` a
JOIN `modules` m ON (a.art_modid = m.mod_id)
LEFT JOIN `comment_meta` ON (cm_table = 'article') AND (cm_table_id = a.art_id)
WHERE
	art_active = 'Y'
	)
UNION
(SELECT
	/*'forum' AS module_id,*/
	(SELECT m.`module_id` FROM `modules` m WHERE m.`mod_id` = forum_modid) AS module_id,
	forum_id AS art_id,
	COALESCE(cm_comment_count, 0) AS art_comment_count,
	cm_comment_lastdate AS art_comment_lastdate,
	forum_name,
	forum_data as art_intro,
	forum_data as art_data,
	forum_entered,
	'Y' AS art_comments,
	'forum' AS cm_table
FROM
	forum
LEFT JOIN comment_meta ON (cm_table = 'forum') AND (cm_table_id = forum_id)
WHERE
	forum_active = 'Y' AND
	forum_modid > 0
	)
ORDER BY
	art_entered DESC;
--


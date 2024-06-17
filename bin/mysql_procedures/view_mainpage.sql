CREATE OR REPLACE VIEW view_mainpage AS
(SELECT
	m.module_id,
	a.res_id,
	a.art_id AS doc_id,
	r.res_name,
	r.res_intro,
	r.res_data,
	r.res_entered,
	r.res_kind,
	rm.res_comment_count,
	rm.res_comment_last_date,
	NULL as type_id,
	r.res_route,
	r.res_hash
FROM
	`article` a
JOIN `modules` m ON (a.art_modid = m.mod_id)
JOIN `res` r ON r.`res_id` = a.`res_id`
LEFT JOIN `res_meta` rm ON rm.`res_id` = r.`res_id`
WHERE
	r.res_visible = 1 AND
	m.module_active = 1
)
UNION
(SELECT
	m.module_id,
	r.res_id,
	forum.forum_id AS doc_id,
	r.res_name,
	r.res_intro,
	r.res_data,
	r.res_entered,
	r.res_kind,
	rm.res_comment_count,
	rm.res_comment_last_date,
	NULL as type_id,
	r.res_route,
	r.res_hash
FROM
	forum
JOIN `modules` m ON (forum.forum_modid = m.mod_id)
JOIN `res` r ON r.`res_id` = forum.`res_id`
LEFT JOIN `res_meta` rm ON rm.`res_id` = r.`res_id`
WHERE
	r.res_visible = 1 AND
	m.module_active = 1
)
UNION
(SELECT
	'forum' AS module_id,
	r.res_id,
	forum.forum_id AS doc_id,
	r.res_name,
	r.res_intro,
	r.res_data,
	r.res_entered,
	r.res_kind,
	rm.res_comment_count,
	rm.res_comment_last_date,
	forum.type_id,
	r.res_route,
	r.res_hash
FROM
	forum
JOIN `res` r ON r.`res_id` = forum.`res_id`
LEFT JOIN `res_meta` rm ON rm.`res_id` = r.`res_id`
WHERE
	r.res_visible = 1 AND
	forum.type_id = 1 AND
	forum.forum_modid IS NULL
)
ORDER BY
	res_entered DESC;

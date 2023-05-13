CREATE OR REPLACE VIEW view_mainpage AS
(SELECT
	m.module_id,
	a.res_id,
	a.art_id,
	NULL AS forum_id,
	r.res_name,
	r.res_intro,
	r.res_data,
	r.res_entered,
	r.table_id,
	rm.res_comment_count,
	rm.res_comment_last_date
FROM
	`article` a
JOIN `modules` m ON (a.art_modid = m.mod_id)
JOIN `res` r ON r.`res_id` = a.`res_id`
LEFT JOIN `res_meta` rm ON rm.`res_id` = r.`res_id`
WHERE
	r.res_visible = 1
)
UNION
(SELECT
	m.module_id,
	r.res_id,
	NULL AS art_id,
	forum.forum_id,
	r.res_name,
	r.res_intro,
	r.res_data,
	r.res_entered,
	r.table_id,
	rm.res_comment_count,
	rm.res_comment_last_date
FROM
	forum
JOIN `modules` m ON (forum.forum_modid = m.mod_id)
JOIN `res` r ON r.`res_id` = forum.`res_id`
LEFT JOIN `res_meta` rm ON rm.`res_id` = r.`res_id`
WHERE
	r.res_visible = 1
)
ORDER BY
	res_entered DESC;

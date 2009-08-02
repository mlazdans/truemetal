CREATE OR REPLACE VIEW view_documents
AS
SELECT
	art_id + 0 AS doc_id,
	art_id AS doc_real_id,
	1 AS doc_source_id,
	art_name AS doc_name,
	CONCAT(art_intro, ' ',  art_data, ' ', GROUP_CONCAT(c_data SEPARATOR " ")) AS doc_content,
	COALESCE(cm_comment_count, 0) AS doc_comment_count,
	UNIX_TIMESTAMP(cm_comment_lastdate) AS doc_comment_lastdate,
	UNIX_TIMESTAMP(art_entered) AS doc_entered
FROM
	article
JOIN modules m ON (art_modid = mod_id)
JOIN comment_connect ON (cc_table = 'article') AND (cc_table_id = art_id)
JOIN comment ON c_id = cc_c_id
LEFT JOIN comment_meta ON (cm_table = 'article') AND (cm_table_id = art_id)
WHERE
	art_active = 'Y' AND module_id = 'article'
GROUP BY
	art_id, art_name, art_entered
UNION
SELECT
	art_id + 0 AS doc_id,
	art_id AS doc_real_id,
	1 AS doc_source_id,
	art_name AS doc_name,
	CONCAT(art_intro, ' ',  art_data, ' ', GROUP_CONCAT(c_data SEPARATOR " ")) AS doc_content,
	COALESCE(cm_comment_count, 0) AS doc_comment_count,
	UNIX_TIMESTAMP(cm_comment_lastdate) AS doc_comment_lastdate,
	UNIX_TIMESTAMP(art_entered) AS doc_entered
FROM
	article
JOIN modules m ON (art_modid = mod_id)
JOIN comment_connect ON (cc_table = 'article') AND (cc_table_id = art_id)
JOIN comment ON c_id = cc_c_id
LEFT JOIN comment_meta ON (cm_table = 'article') AND (cm_table_id = art_id)
WHERE
	art_active = 'Y' AND module_id = 'reviews'
GROUP BY
	art_id, art_name, art_entered
UNION
SELECT
	forum_id + 10000 AS doc_id,
	forum_id AS doc_real_id,
	3 AS doc_source_id,
	forum_name AS doc_name,
	GROUP_CONCAT(c_data SEPARATOR " ") AS doc_content,
	COALESCE(cm_comment_count, 0) AS doc_comment_count,
	UNIX_TIMESTAMP(cm_comment_lastdate) AS doc_comment_lastdate,
	UNIX_TIMESTAMP(forum_entered) AS doc_entered
FROM
	forum f
JOIN comment_connect ON (cc_table = 'forum') AND (cc_table_id = forum_id)
JOIN comment ON c_id = cc_c_id
LEFT JOIN comment_meta ON (cm_table = 'forum') AND (cm_table_id = forum_id)
WHERE
	forum_active = 'Y' AND
	c_visible = 'Y'
GROUP BY
	forum_id, forum_name, forum_entered


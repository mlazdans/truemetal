CREATE OR REPLACE VIEW view_documents
AS
-- Articles
SELECT
	res_id AS doc_id,
	table_id,
	res_id,
	res_resid,
	CASE
		WHEN module_id = 'article' THEN 1
		WHEN module_id = 'reviews' THEN 2
		WHEN module_id = 'interviews' THEN 3
		ELSE NULL
	END AS doc_source_id,
	res_name AS doc_name, -- res_name un doc_name vajag atdalīt, lai varētu atsevišķi indeksēt
	res_name,
	CONCAT_WS(' ', res_intro, res_data, ' ') AS doc_content,
	CONCAT_WS(' ', res_intro, res_data,
		(SELECT GROUP_CONCAT(rc.res_data SEPARATOR " ") FROM res rc JOIN comment c ON (c.res_id = rc.res_id) WHERE (rc.res_resid = view_res_article.res_id) AND (rc.res_visible = 1))
	) AS doc_content_with_comments,
	res_comment_count AS doc_comment_count,
	UNIX_TIMESTAMP(res_comment_last_date) AS doc_comment_last_date,
	UNIX_TIMESTAMP(res_entered) AS doc_entered
FROM
	view_res_article
WHERE
	res_visible = 1 AND
	-- TODO: nemeklēs citās sadaļās, ja tādas parādīsies
	module_id IN ('article', 'reviews', 'interviews')

UNION

-- Forum articles
SELECT
	res_id AS doc_id,
	table_id,
	res_id,
	res_resid,
	CASE
		WHEN module_id = 'article' THEN 1
		WHEN module_id = 'reviews' THEN 2
		WHEN module_id = 'interviews' THEN 3
		ELSE NULL
	END AS doc_source_id,
	res_name AS doc_name,
	res_name,
	CONCAT_WS(' ', res_intro, res_data, ' ') AS doc_content,
	CONCAT_WS(' ', res_intro, res_data,
		(SELECT GROUP_CONCAT(rc.res_data SEPARATOR " ") FROM res rc JOIN comment c ON (c.res_id = rc.res_id) WHERE (rc.res_resid = view_res_forum.res_id) AND (rc.res_visible = 1))
	) AS doc_content_with_comments,
	res_comment_count AS doc_comment_count,
	UNIX_TIMESTAMP(res_comment_last_date) AS doc_comment_last_date,
	UNIX_TIMESTAMP(res_entered) AS doc_entered
FROM
	view_res_forum
WHERE
	res_visible = 1 AND
	forum_allow_childs = 0 AND
	-- TODO: nemeklēs citās sadaļās, ja tādas parādīsies
	module_id IN ('article', 'reviews', 'interviews')

UNION

-- Forum events
SELECT
	res_id AS doc_id,
	table_id,
	res_id,
	res_resid,
	1 AS doc_source_id, -- Ziņas
	res_name AS doc_name,
	res_name,
	CONCAT_WS(' ', res_data, ' ') AS doc_content,
	CONCAT_WS(' ', res_intro, res_data,
		(SELECT GROUP_CONCAT(rc.res_data SEPARATOR " ") FROM res rc JOIN comment c ON (c.res_id = rc.res_id) WHERE (rc.res_resid = view_res_forum.res_id) AND (rc.res_visible = 1))
	) AS doc_content_with_comments,
	res_comment_count AS doc_comment_count,
	UNIX_TIMESTAMP(res_comment_last_date) AS doc_comment_last_date,
	UNIX_TIMESTAMP(res_entered) AS doc_entered
FROM
	view_res_forum
WHERE
	res_visible = 1 AND
	type_id = 1 AND
	forum_allow_childs = 0 AND
	forum_modid IS NULL

UNION

-- Forum themes
SELECT
	res_id AS doc_id,
	table_id,
	res_id,
	res_resid,
	4 AS doc_source_id, -- Forums
	res_name AS doc_name,
	res_name,
	CONCAT_WS(' ', res_data, ' ') AS doc_content,
	CONCAT_WS(' ', res_intro, res_data,
		(SELECT GROUP_CONCAT(rc.res_data SEPARATOR " ") FROM res rc JOIN comment c ON (c.res_id = rc.res_id) WHERE (rc.res_resid = view_res_forum.res_id) AND (rc.res_visible = 1))
	) AS doc_content_with_comments,
	res_comment_count AS doc_comment_count,
	UNIX_TIMESTAMP(res_comment_last_date) AS doc_comment_last_date,
	UNIX_TIMESTAMP(res_entered) AS doc_entered
FROM
	view_res_forum
WHERE
	res_visible = 1 AND
	type_id = 0 AND
	forum_allow_childs = 0 AND
	forum_modid IS NULL

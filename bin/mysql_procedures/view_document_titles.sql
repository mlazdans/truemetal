CREATE OR REPLACE VIEW view_document_titles
AS
SELECT
	a.res_id AS doc_res_id,
	CASE
		WHEN m.module_id = 'article' THEN 1
		WHEN m.module_id = 'reviews' THEN 2
		WHEN m.module_id = 'interviews' THEN 3
		ELSE 0
	END AS doc_source_id,
	r.res_name AS doc_name,
	r.res_name AS doc_name2,
	rm.res_comment_count AS doc_comment_count,
	UNIX_TIMESTAMP(rm.res_comment_last_date) AS doc_comment_last_date,
	rm.res_child_count AS doc_child_count,
	UNIX_TIMESTAMP(rm.res_child_last_date) AS doc_child_last_date,
	UNIX_TIMESTAMP(r.res_entered) AS doc_entered,
	NULL as forum_allow_childs
FROM
	article a
JOIN modules m ON (m.mod_id = a.art_modid)
JOIN res r ON (r.res_id = a.res_id)
JOIN res_meta rm ON (rm.res_id = r.res_id)
WHERE
	r.res_visible = 1 AND
	(m.module_id = 'article' OR m.module_id = 'reviews' OR m.module_id = 'interviews')
UNION
SELECT
	f.res_id AS doc_res_id,
	4 AS doc_source_id,
	r.res_name AS doc_name,
	r.res_name AS doc_name2,
	rm.res_comment_count AS doc_comment_count,
	UNIX_TIMESTAMP(rm.res_comment_last_date) AS doc_comment_last_date,
	rm.res_child_count AS doc_child_count,
	UNIX_TIMESTAMP(rm.res_child_last_date) AS doc_child_last_date,
	UNIX_TIMESTAMP(r.res_entered) AS doc_entered,
	f.forum_allow_childs
FROM
	forum f
JOIN res r ON (r.res_id = f.res_id)
JOIN res_meta rm ON (rm.res_id = r.res_id)
WHERE
	r.res_visible = 1

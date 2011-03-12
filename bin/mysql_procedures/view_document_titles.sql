CREATE OR REPLACE VIEW view_document_titles
AS
SELECT
	a.res_id AS doc_real_id,
	1 AS doc_source_id,
	a.art_name AS doc_name,
	r.res_comment_count AS doc_comment_count,
	UNIX_TIMESTAMP(r.res_comment_lastdate) AS doc_comment_lastdate,
	UNIX_TIMESTAMP(a.art_entered) AS doc_entered
FROM
	article a
JOIN modules m ON (m.mod_id = a.art_modid)
JOIN res r ON (r.res_id = a.res_id)
WHERE
	a.art_active = 'Y' AND
	m.module_id = 'article'
UNION
SELECT
	a.res_id AS doc_real_id,
	1 AS doc_source_id,
	a.art_name AS doc_name,
	r.res_comment_count AS doc_comment_count,
	UNIX_TIMESTAMP(r.res_comment_lastdate) AS doc_comment_lastdate,
	UNIX_TIMESTAMP(a.art_entered) AS doc_entered
FROM
	article a
JOIN modules m ON (m.mod_id = a.art_modid)
JOIN res r ON (r.res_id = a.res_id)
WHERE
	a.art_active = 'Y' AND
	m.module_id = 'reviews'
UNION
SELECT
	f.res_id AS doc_real_id,
	3 AS doc_source_id,
	f.forum_name AS doc_name,
	r.res_comment_count AS doc_comment_count,
	UNIX_TIMESTAMP(r.res_comment_lastdate) AS doc_comment_lastdate,
	UNIX_TIMESTAMP(f.forum_entered) AS doc_entered
FROM
	forum f
JOIN res r ON (r.res_id = f.res_id)
WHERE
	f.forum_active = 'Y'


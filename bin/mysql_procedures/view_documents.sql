CREATE OR REPLACE VIEW view_documents
AS
SELECT
	a.res_id AS doc_real_id,
	1 AS doc_source_id,
	a.art_name AS doc_name,
	CONCAT(a.art_intro, ' ',  a.art_data, ' ', GROUP_CONCAT(c.c_data SEPARATOR " ")) AS doc_content,
	r.res_comment_count AS doc_comment_count,
	UNIX_TIMESTAMP(r.res_comment_lastdate) AS doc_comment_lastdate,
	UNIX_TIMESTAMP(a.art_entered) AS doc_entered
FROM
	article a
JOIN modules m ON (m.mod_id = a.art_modid)
JOIN res r ON (r.res_id = a.res_id)
JOIN res_comment rc ON (rc.res_id = a.res_id)
JOIN comment c ON (c.c_id = rc.c_id)
WHERE
	a.art_active = 'Y' AND
	m.module_id = 'article'
GROUP BY
	a.art_id, a.art_name, a.art_entered
UNION
SELECT
	a.res_id AS doc_real_id,
	2 AS doc_source_id,
	a.art_name AS doc_name,
	CONCAT(a.art_intro, ' ',  a.art_data, ' ', GROUP_CONCAT(c.c_data SEPARATOR " ")) AS doc_content,
	r.res_comment_count AS doc_comment_count,
	UNIX_TIMESTAMP(r.res_comment_lastdate) AS doc_comment_lastdate,
	UNIX_TIMESTAMP(a.art_entered) AS doc_entered
FROM
	article a
JOIN modules m ON (m.mod_id = a.art_modid)
JOIN res r ON (r.res_id = a.res_id)
JOIN res_comment rc ON (rc.res_id = a.res_id)
JOIN comment c ON (c.c_id = rc.c_id)
WHERE
	a.art_active = 'Y' AND
	m.module_id = 'reviews'
GROUP BY
	a.art_id, a.art_name, a.art_entered
UNION
SELECT
	f.res_id AS doc_real_id,
	3 AS doc_source_id,
	f.forum_name AS doc_name,
	GROUP_CONCAT(c.c_data SEPARATOR " ") AS doc_content,
	r.res_comment_count AS doc_comment_count,
	UNIX_TIMESTAMP(r.res_comment_lastdate) AS doc_comment_lastdate,
	UNIX_TIMESTAMP(f.forum_entered) AS doc_entered
FROM
	forum f
JOIN res r ON (r.res_id = f.res_id)
JOIN res_comment rc ON (rc.res_id = f.res_id)
JOIN comment c ON (c.c_id = rc.c_id)
WHERE
	f.forum_active = 'Y'
GROUP BY
	f.forum_id, f.forum_name, f.forum_entered


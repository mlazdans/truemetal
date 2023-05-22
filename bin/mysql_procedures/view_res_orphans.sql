-- SELECT * FROM view_res JOIN view_res_orphans USING (res_id)
CREATE OR REPLACE VIEW view_res_orphans AS
SELECT r.res_id, 'article' FROM res r
LEFT JOIN article f ON r.res_id = f.res_id
WHERE
	r.table_id = 1 AND
	f.res_id is NULL
UNION
SELECT r.res_id, 'forum' FROM res r
LEFT JOIN forum f ON r.res_id = f.res_id
WHERE
	r.table_id = 2 AND
	f.res_id is NULL
UNION
SELECT r.res_id, 'comment' FROM res r
LEFT JOIN comment f ON r.res_id = f.res_id
WHERE
	r.table_id = 3 AND
	f.res_id is NULL
UNION
SELECT r.res_id, 'gallery' FROM res r
LEFT JOIN gallery f ON r.res_id = f.res_id
WHERE
	r.table_id = 4 AND
	f.res_id is NULL
UNION
SELECT r.res_id, 'gallery_data' FROM res r
LEFT JOIN gallery_data f ON r.res_id = f.res_id
WHERE
	r.table_id = 5 AND
	f.res_id is NULL

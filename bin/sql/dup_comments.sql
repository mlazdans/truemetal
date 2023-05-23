-- Pirms update
SELECT vf.*, comm.*
FROM forum AS vf
JOIN comment AS comm ON comm.c_id = (
	SELECT c.c_id FROM comment c
	JOIN res_comment AS rc ON rc.c_id = c.c_id
	WHERE rc.res_id = vf.res_id
	ORDER BY c.c_entered LIMIT 1
)
-- join not merged yet
-- LEFT JOIN res_merge AS rm ON rm.forum_res_id = vf.res_id
WHERE
	-- rm.forum_res_id IS NULL AND
	vf.forum_display = 0 AND
	vf.forum_allowchilds = 'N' AND
	(vf.forum_data = comm.c_data OR vf.forum_datacompiled = comm.c_datacompiled)
ORDER BY vf.forum_id

-- Pēc update
-- SELECT vf.*, comm.*
-- FROM view_res_forum AS vf
-- JOIN view_res_comment AS comm ON comm.res_id = (
-- 	SELECT res_id FROM res WHERE res_resid = vf.res_id AND res.table_id = 3 ORDER BY res.res_entered LIMIT 1
-- )
-- WHERE vf.forum_allow_childs = 0 AND (vf.res_data = comm.res_data OR vf.res_data_compiled = comm.res_data_compiled)
-- ORDER BY vf.forum_id

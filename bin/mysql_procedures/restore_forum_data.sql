UPDATE forum, (SELECT
	forum_id,
	forum_name,
	forum_data,
	(SELECT c_data FROM comment WHERE c_id = cc_c_id ORDER BY c_entered ASC LIMIT 1) c_data
FROM forum f
JOIN comment_connect ON (cc_table = 'forum') AND (cc_table_id = forum_id)
GROUP BY
	forum_id
HAVING
	forum_data <> c_data
ORDER BY
	forum_entered DESC
) AS forum_comments
SET forum.forum_data = forum_comments.c_data
WHERE
	forum.forum_id = forum_comments.forum_id
;
---


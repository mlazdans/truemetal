SELECT
	c_username,
	(SELECT l_id FROM logins WHERE BINARY l_nick = c_username) AS l_id
FROM
	`comment`
WHERE
	`login_id` IS NULL
GROUP BY
	c_username
HAVING
	l_id IS NOT NULL
ORDER BY
	sk DESC


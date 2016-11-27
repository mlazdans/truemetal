/* Plusotākie komenti */
SELECT
	l_nick,
	res_votes,
	c_data
FROM res r
JOIN logins ON l_id = r.login_id
JOIN comment f ON f.res_id = r.res_id
ORDER BY
	res_votes DESC
LIMIT 10


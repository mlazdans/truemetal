/* Plusotākie komenti */
SELECT
	r.login_id,
	r.res_nickname,
	rm.*
FROM res r
JOIN res_meta rm ON rm.res_id = r.res_id
ORDER BY rm.res_votes DESC
LIMIT 10


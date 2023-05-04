SELECT
	l_nick,
	comment_count,
	votes_plus-votes_minus AS Plusi,
	CONCAT(ROUND((votes_plus/(votes_plus+votes_minus))*100),'%') AS PlusiProc,
	CONCAT(ROUND(((votes_plus-votes_minus)/comment_count)*100),'%') AS PlusiPretKom
FROM
	logins
WHERE
	comment_count>100
ORDER BY
	Plusi DESC
LIMIT 10


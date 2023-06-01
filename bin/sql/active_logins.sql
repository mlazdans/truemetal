SELECT
	COUNT(*) AS active_logins
FROM
	logins
WHERE
	l_active=1 AND
	l_accepted=1 AND
	l_lastaccess >= DATE_SUB(NOW(),INTERVAL 1 YEAR);

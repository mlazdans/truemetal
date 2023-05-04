SELECT
	COUNT(*) AS active_logins
FROM
	`logins`
WHERE
	`l_active`='Y' AND
	`l_accepted`='Y' AND
	`l_lastaccess` >= DATE_SUB(NOW(),INTERVAL 1 YEAR);



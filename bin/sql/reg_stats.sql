SELECT
	DATE_FORMAT(periods, '%Y %M') periods,
	skaits2 AS skaits2008,
	skaits AS skaits2009
FROM (SELECT
	DATE_FORMAT(l1.l_entered, '%Y-%m-01') periods,
	COUNT(*) skaits,
	(SELECT
		COUNT(*)
	FROM
		`logins` l2
	WHERE
		l2.l_accepted='Y' AND
		l2.l_entered >= SUBDATE(periods, INTERVAL 1 YEAR) AND
		l2.l_entered <= LAST_DAY(SUBDATE(periods, INTERVAL 1 YEAR))
	) skaits2
FROM
	`logins` l1
WHERE
	l1.`l_accepted`='Y' AND
	l1.`l_entered` > '2009-01-01'
GROUP BY
	periods
ORDER BY
	l1.l_entered
) AS view_reg_stats


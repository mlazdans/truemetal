-- TODO: procedūrā un cache
CREATE OR REPLACE VIEW view_jubilars
AS
SELECT
	t.*,
	TIMESTAMPDIFF(YEAR, l_entered, l_entered_adjusted) age,
	DATEDIFF(l_entered_adjusted, CURRENT_DATE) entered_stamp
FROM
(
	SELECT
		CAST(l_entered AS DATE) l_entered,
		CAST(CONCAT_WS(
			'-',
			DATE_FORMAT(CURRENT_DATE, '%Y'),
			DATE_FORMAT(l_entered, '%m-%d')
		) AS DATE) AS l_entered_adjusted,
		l_id,
		l_nick,
		l_hash
	FROM logins
	WHERE
		l_active = 1
		AND l_accepted = 1
		AND l_lastaccess > DATE_SUB(CURRENT_DATE, INTERVAL 1 YEAR)
) AS t
HAVING
	entered_stamp >= -3 AND entered_stamp <= 3
ORDER BY
	l_entered
;

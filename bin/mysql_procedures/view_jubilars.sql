CREATE OR REPLACE VIEW view_jubilars
AS
SELECT
	DATE_FORMAT(l_entered, '%m%d') AS entered_stamp,
	-- NOTE: ja entered ir 30-30 dec, tad vecums 1-2 jan var būt par gadu lielāks
	CAST(DATE_FORMAT(CURRENT_TIMESTAMP, '%Y') - DATE_FORMAT(l_entered, '%Y') AS INT) AS age,
	l_id,
	l_nick,
	l_hash
FROM logins
WHERE
	l_active = 1 AND l_accepted = 1 AND
	l_lastaccess > DATE_SUB(CURRENT_TIMESTAMP, INTERVAL 1 YEAR) AND
	ABS(DATE_FORMAT(l_entered, '%m%d') - DATE_FORMAT(CURRENT_TIMESTAMP, '%m%d')) < 3
ORDER BY
	DATE_FORMAT(l_entered, '%m%d')
;

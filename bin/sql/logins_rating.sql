SELECT
	l_id, l_nick, votes_plus, votes_minus, rating
FROM logins
WHERE rating IS NOT NULL
ORDER BY rating;

ALTER TABLE `logins` DROP `rating`;
ALTER TABLE logins ADD rating NUMERIC(9,2) AS (
	CASE WHEN votes_plus + votes_minus >= 100 THEN
		votes_plus / (votes_plus + votes_minus) * 100
		-- (votes_plus / (votes_plus + votes_minus) - votes_minus / (votes_plus + votes_minus)) * 100
	ELSE NULL END
);
ALTER TABLE `logins` ADD INDEX(`rating`);

ALTER TABLE `logins` DROP `votes`;
ALTER TABLE logins ADD votes BIGINT AS (votes_plus - votes_minus);
ALTER TABLE `logins` ADD INDEX(`votes`);

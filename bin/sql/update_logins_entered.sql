UPDATE logins
JOIN (
	SELECT l.l_id, l.l_nick, l.l_entered entered, MIN(r.res_entered) first_comment_date FROM `logins` l
	JOIN res r on r.login_id = l_id
	GROUP BY l.l_id, l.l_nick, l.l_entered
	HAVING first_comment_date < entered
	ORDER by first_comment_date
) mins ON mins.l_id = logins.l_id
SET l_entered = mins.first_comment_date;

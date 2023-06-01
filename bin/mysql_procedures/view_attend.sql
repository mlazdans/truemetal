CREATE OR REPLACE VIEW view_attend AS
SELECT attend.*, logins.l_nick, logins.l_hash
FROM attend
JOIN logins ON logins.l_id = attend.l_id
ORDER BY a_entered

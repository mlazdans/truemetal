/*
-- Apvieno un izdzēš l_nick duplikātus
SELECT * FROM logins WHERE l_nick IN (SELECT l_nick FROM `logins` GROUP BY l_nick HAVING COUNT(*)>1) ORDER BY l_lastaccess DESC;
SELECT * FROM logins WHERE l_id IN (1651, 2079,1452, 1460,820, 264) ORDER BY l_lastaccess DESC;
*/
DELETE FROM logins WHERE l_id IN (819);

UPDATE attend SET l_id = 2079 WHERE l_id = 1651;
UPDATE res SET login_id = 2079 WHERE login_id = 1651;
UPDATE res_vote SET login_id = 2079 WHERE login_id = 1651;
UPDATE comment_disabled SET login_id = 2079 WHERE login_id = 1651;
UPDATE comment_disabled SET disable_login_id = 2079 WHERE disable_login_id = 1651;
DELETE FROM logins WHERE l_id = 1651;

UPDATE attend SET l_id = 1460 WHERE l_id = 1452;
UPDATE res SET login_id = 1460 WHERE login_id = 1452;
UPDATE res_vote SET login_id = 1460 WHERE login_id = 1452;
UPDATE comment_disabled SET login_id = 1460 WHERE login_id = 1452;
UPDATE comment_disabled SET disable_login_id = 1460 WHERE disable_login_id = 1452;
DELETE FROM logins WHERE l_id = 1452;

UPDATE attend SET l_id = 264 WHERE l_id = 820;
UPDATE res SET login_id = 264 WHERE login_id = 820;
UPDATE res_vote SET login_id = 264 WHERE login_id = 820;
UPDATE comment_disabled SET login_id = 264 WHERE login_id = 820;
UPDATE comment_disabled SET disable_login_id = 264 WHERE disable_login_id = 820;
DELETE FROM logins WHERE l_id = 820;

ALTER TABLE `logins` DROP INDEX `l_nick`;
ALTER TABLE `logins` ADD UNIQUE(`l_nick`);

-- Update l_lastaccess pēc pēdējā komenta datuma
UPDATE logins l SET l.l_lastaccess = (SELECT res.res_entered FROM res WHERE res.login_id = l.l_id AND res.table_id = 3 ORDER BY res.res_entered DESC LIMIT 1)
WHERE l.l_lastaccess IS NULL AND l.comment_count > 0

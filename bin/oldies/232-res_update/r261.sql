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

UPDATE attend SET l_id = 687 WHERE l_id = 651;
UPDATE res SET login_id = 687 WHERE login_id = 651;
UPDATE res_vote SET login_id = 687 WHERE login_id = 651;
UPDATE comment_disabled SET login_id = 687 WHERE login_id = 651;
UPDATE comment_disabled SET disable_login_id = 687 WHERE disable_login_id = 651;
DELETE FROM logins WHERE l_id = 651;

UPDATE attend SET l_id = 687 WHERE l_id = 735;
UPDATE res SET login_id = 687 WHERE login_id = 735;
UPDATE res_vote SET login_id = 687 WHERE login_id = 735;
UPDATE comment_disabled SET login_id = 687 WHERE login_id = 735;
UPDATE comment_disabled SET disable_login_id = 687 WHERE disable_login_id = 735;
DELETE FROM logins WHERE l_id = 735;

UPDATE attend SET l_id = 1787 WHERE l_id = 1342;
UPDATE res SET login_id = 1787 WHERE login_id = 1342;
UPDATE res_vote SET login_id = 1787 WHERE login_id = 1342;
UPDATE comment_disabled SET login_id = 1787 WHERE login_id = 1342;
UPDATE comment_disabled SET disable_login_id = 1787 WHERE disable_login_id = 1342;
DELETE FROM logins WHERE l_id = 1342;
UPDATE res SET res_nickname = '<Enemy>' WHERE res_nickname='&lt;Enemy&gt;';

UPDATE res SET res_nickname = 'Irēna', login_id = 28 WHERE res_nickname='Irēna,----&gt;';
UPDATE res SET res_nickname = 'RuncisRenars', login_id = 137 WHERE res_id = 10627;
UPDATE res SET res_nickname = 'Fauns', login_id = 50 WHERE res_id = 9104;

UPDATE res SET res_nickname = 'minka->lv pagani' WHERE res_id = 1859;
UPDATE res SET res_email = 'peace, love &drum\'n\'bass' WHERE res_id = 10757;
UPDATE res SET res_nickname = 'corpse&fucin Jew' WHERE res_id = 10775;
UPDATE res SET res_nickname = 'Termit&Apofeozs' WHERE res_id = 11056;
UPDATE res SET res_nickname = ';::\"::' WHERE res_id = 11845;
UPDATE res SET res_nickname = ';::\"::' WHERE res_id = 11847;
UPDATE res SET res_nickname = ';::\"::' WHERE res_id = 11848;
UPDATE res SET res_nickname = 'E&G.corp' WHERE res_id = 34334;
UPDATE res SET res_nickname = 'E&G.corp' WHERE res_id = 34520;
UPDATE res SET res_nickname = 'E&G.corp' WHERE res_id = 34532;
UPDATE res SET res_nickname = 'E&P' WHERE res_id = 56905;

ALTER TABLE `logins` DROP INDEX `l_nick`;
ALTER TABLE `logins` ADD UNIQUE(`l_nick`);

ALTER TABLE `logins`
	CHANGE `l_nick` `l_nick` VARCHAR(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
	CHANGE `l_login` `l_login` VARCHAR(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL
;

-- Daži res bez login_id
-- SELECT * FROM logins WHERE l_email IN (SELECT res_email FROM `res` WHERE table_id < 4 AND login_id IS NULL GROUP BY res_email)
-- UNION
-- SELECT * FROM logins WHERE l_nick IN (SELECT res_nickname FROM `res` WHERE table_id < 4 AND login_id IS NULL GROUP BY res_nickname)

-- SELECT * FROM res WHERE table_id < 4 AND login_id IS NULL AND EXISTS (SELECT * FROM logins WHERE l_email = res_email)
-- UNION
-- SELECT * FROM res WHERE table_id < 4 AND login_id IS NULL AND EXISTS (SELECT * FROM logins WHERE l_nick = res_nickname)

UPDATE res
JOIN logins l ON l.l_email = res.res_email
SET res.login_id = l.l_id
WHERE
	res.table_id < 4 AND
	res.login_id IS NULL
;

UPDATE res
JOIN logins l ON l.l_nick = res.res_nickname
SET res.login_id = l.l_id
WHERE
	res.table_id < 4 AND
	res.login_id IS NULL
;

-- Update l_lastaccess pēc pēdējā komenta datuma
UPDATE logins l SET l.l_lastaccess = (SELECT res.res_entered FROM res WHERE res.login_id = l.l_id AND res.table_id = 3 ORDER BY res.res_entered DESC LIMIT 1)
WHERE l.l_lastaccess IS NULL AND l.comment_count > 0

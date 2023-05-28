ALTER TABLE `logins`
CHANGE `votes_plus` `votes_plus` INT(11) UNSIGNED NULL DEFAULT 0,
CHANGE `votes_minus` `votes_minus` INT(11) UNSIGNED NULL DEFAULT 0,
CHANGE `comment_count` `comment_count` INT(11) UNSIGNED NULL DEFAULT 0;

ALTER TABLE `forum` CHANGE `forum_modid` `forum_modid` INT(10) UNSIGNED NULL DEFAULT NULL;
UPDATE forum SET forum_modid = NULL WHERE forum_modid = 0;
ALTER TABLE `forum` ADD FOREIGN KEY (`forum_modid`) REFERENCES `modules`(`mod_id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

DROP VIEW view_document_titles;

ALTER TABLE logins
	MODIFY l_hash VARCHAR(8) CHARACTER SET ascii COLLATE ascii_general_ci,
	MODIFY l_password VARCHAR(64) CHARACTER SET ascii COLLATE ascii_general_ci,
	MODIFY l_email VARCHAR(128) CHARACTER SET ascii COLLATE ascii_general_ci,
	MODIFY l_userip VARCHAR(32) CHARACTER SET ascii COLLATE ascii_general_ci,
	MODIFY l_sess_id VARCHAR(32) CHARACTER SET ascii COLLATE ascii_general_ci,
	MODIFY l_sess_ip VARCHAR(32) CHARACTER SET ascii COLLATE ascii_general_ci,
	MODIFY l_sessiondata MEDIUMTEXT NULL
;

ALTER TABLE res
	MODIFY res_email VARCHAR(128) CHARACTER SET ascii COLLATE ascii_general_ci,
	MODIFY res_ip VARCHAR(32) CHARACTER SET ascii COLLATE ascii_general_ci
;

-- Warning: #1366 Incorrect string value: '\xC4\x81ns' for column `truemetal_remote`.`res`.`res_email` at row 1347
-- Warning: #1366 Incorrect string value: '\xC4\x81ls \xE2...' for column `truemetal_remote`.`res`.`res_email` at row 3205
-- Warning: #1366 Incorrect string value: '\xC4\x81ls \xE2...' for column `truemetal_remote`.`res`.`res_email` at row 3206
-- Warning: #1366 Incorrect string value: '\xC4\x81nam' for column `truemetal_remote`.`res`.`res_email` at row 8958
-- Warning: #1366 Incorrect string value: '\xC4\x81' for column `truemetal_remote`.`res`.`res_email` at row 9171
-- Warning: #1366 Incorrect string value: '\xC5\xABdzu ...' for column `truemetal_remote`.`res`.`res_email` at row 9266
-- Warning: #1366 Incorrect string value: '\xC4\xABms' for column `truemetal_remote`.`res`.`res_email` at row 9733
-- Warning: #1366 Incorrect string value: '\xC4\x81vim ...' for column `truemetal_remote`.`res`.`res_email` at row 118133
-- Warning: #1366 Incorrect string value: '\xC4\x81ls \xE2...' for column `truemetal_remote`.`res`.`res_email` at row 236377

ALTER DATABASE CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
DROP TABLE poll_votes;
DROP TABLE poll;

-- Cleanup invalid session data
UPDATE logins SET l_sessiondata = NULL WHERE l_id IN (2385, 2564, 2680) OR l_sessiondata = '';

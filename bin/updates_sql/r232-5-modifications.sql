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
	MODIFY l_sess_ip VARCHAR(32) CHARACTER SET ascii COLLATE ascii_general_ci
;

ALTER DATABASE CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
DROP TABLE poll_votes;
DROP TABLE poll;

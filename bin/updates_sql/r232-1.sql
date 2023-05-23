-- CREATE TABLE `tables` (
--  `table_id` int unsigned NOT NULL,
--  `table_name` VARCHAR(16),
--  CONSTRAINT `table_pk` PRIMARY KEY (`table_id`),
--  CONSTRAINT `table_u1` UNIQUE KEY(`table_name`)
-- );
-- INSERT INTO `tables` VALUES (1, 'comment');
-- INSERT INTO `tables` VALUES (2, 'article');
-- INSERT INTO `tables` VALUES (3, 'gallery');

ALTER TABLE `res`
	CHANGE `table_id` `table_id` INTEGER UNSIGNED NOT NULL,
	ADD `res_nickname` VARCHAR(16),
	ADD `res_email` VARCHAR(64),
	ADD `res_ip` VARCHAR(32),
	ADD `res_visible` TINYINT UNSIGNED NOT NULL DEFAULT 1,
	CHANGE `res_entered` `res_entered` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
	DROP `res_votes`,
	DROP `res_votes_plus_count`,
	DROP `res_votes_minus_count`,
	ADD `res_resid` INTEGER UNSIGNED NULL AFTER `res_id`,
	ADD `res_name` TINYTEXT,
	ADD `res_intro` TEXT,
	ADD `res_data` MEDIUMTEXT,
	ADD `res_data_compiled` MEDIUMTEXT,
	DROP `res_comment_count`,
	DROP `res_comment_lastdate`;
ALTER TABLE `res`
	ADD CONSTRAINT `res_ibfk_2` FOREIGN KEY (`res_resid`) REFERENCES `res`(`res_id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
	ADD KEY `res_visible` (`res_visible`,`login_id`,`res_id`)
;

CREATE TABLE `res_meta` (
 `res_id` INTEGER UNSIGNED NOT NULL,
 `res_votes` INTEGER UNSIGNED DEFAULT 0,
 `res_votes_plus_count` INTEGER UNSIGNED DEFAULT 0,
 `res_votes_minus_count` INTEGER UNSIGNED DEFAULT 0,
 `res_child_count` INTEGER UNSIGNED DEFAULT 0,
 `res_child_last_date` DATETIME DEFAULT NULL,
 `res_comment_count` INTEGER UNSIGNED DEFAULT 0,
 `res_comment_last_date` DATETIME DEFAULT NULL,
 CONSTRAINT `res_meta_u1` UNIQUE KEY(`res_id`),
 CONSTRAINT `res_meta_fk1` FOREIGN KEY (`res_id`) REFERENCES `res` (`res_id`) ON DELETE CASCADE ON UPDATE CASCADE
);

-- delete/deactivate triggers (optional)
DROP TRIGGER IF EXISTS `comment_trigger1`;
DROP TRIGGER IF EXISTS `comment_trigger2`;
DROP TRIGGER IF EXISTS `comment_trigger3`;
DROP TRIGGER IF EXISTS `comment_trigger4`;
DROP TRIGGER IF EXISTS `res_comment_trigger1`;
DROP TRIGGER IF EXISTS `res_comment_trigger2`;
DROP TRIGGER IF EXISTS `res_comment_trigger3`;
DROP TRIGGER IF EXISTS `res_comment_trigger4`;
DROP TRIGGER IF EXISTS `res_vote_trigger1`;
DROP TRIGGER IF EXISTS `res_vote_trigger2`;
DROP TRIGGER IF EXISTS `res_vote_trigger3`;
-- add extlibs
-- config izmaiņas
-- r232-1.sql (šis fails)
-- palaist bin/res_update.php
-- r232-2.sql
-- r232-3.sql
-- r232-4-res_merge.sql
-- r232-5-null.sql
-- r235.sql
-- palaist bin/mysql_procedures/import_procedures.bat
-- CALL res_update_meta(NULL);
-- CALL logins_update_meta(NULL);
-- palaist bin/sess_update.php
-- palaist bin/theme_collect_dup_comment.php
-- ^ šiet manuāli pārcelti pirmie komentāri, kuriem pirmais ieraksts kaut kādu iemeslu dēļ atšķiras no tēmas ieraksta

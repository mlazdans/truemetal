ALTER TABLE `forum`
	-- DROP FOREIGN KEY `forum_ibfk_4`,
	DROP FOREIGN KEY `forum_ibfk_5`,
	DROP FOREIGN KEY `forum_ibfk_2`;
ALTER TABLE `forum`
	-- MODIFY `res_id` INT(10) UNSIGNED NOT NULL,
	-- ADD CONSTRAINT `forum_ibfk_4` FOREIGN KEY (`res_id`) REFERENCES `res`(`res_id`) ON DELETE RESTRICT ON UPDATE CASCADE,
	DROP `login_id`,
	DROP `forum_userlogin`,
	DROP `forum_username`,
	DROP `forum_useremail`,
	DROP `forum_userip`,
	DROP `forum_entered`,
	DROP `forum_active`,

	DROP `forum_name`,
	DROP `forum_data`,
	DROP `forum_datacompiled`,
	CHANGE `forum_allowchilds` `forum_allow_childs` TINYINT(3) UNSIGNED NOT NULL DEFAULT 0,
	DROP `forum_forumid`
;

UPDATE `forum` SET forum_allow_childs = 0;
UPDATE `forum` SET forum_allow_childs = 1 WHERE forum_id IN (1,14,15,16,107488);

ALTER TABLE `comment`
	DROP FOREIGN KEY `comment_ibfk_4`,
	DROP FOREIGN KEY `comment_ibfk_3`,
	-- MODIFY `res_id` INT(10) UNSIGNED NOT NULL,
	DROP `login_id`,
	DROP `c_entered`,
	DROP `c_visible`,
	DROP `c_userlogin`,
	DROP `c_username`,
	DROP `c_useremail`,
	DROP `c_userip`,

	DROP `c_data`,
	DROP `c_datacompiled`;
ALTER TABLE `comment`
	ADD CONSTRAINT `comment_ibfk_3` FOREIGN KEY (`res_id`) REFERENCES `res`(`res_id`) ON DELETE SET NULL ON UPDATE CASCADE,
	DROP INDEX `login_id`;
;

ALTER TABLE `article`
	-- DROP FOREIGN KEY `article_ibfk_3`,
	DROP FOREIGN KEY `article_ibfk_4`;
ALTER TABLE `article`
	-- MODIFY `res_id` INT(10) UNSIGNED NOT NULL,
	-- ADD CONSTRAINT `article_ibfk_3` FOREIGN KEY (`res_id`) REFERENCES `res` (`res_id`) ON DELETE RESTRICT ON UPDATE CASCADE,
	DROP `login_id`,
	DROP `art_active`,
	DROP `art_entered`,
	DROP `art_userip`,

	DROP `art_name`,
	DROP `art_intro`,
	DROP `art_data`
;

ALTER TABLE `gallery`
	DROP FOREIGN KEY `gallery_ibfk_1`,
	DROP FOREIGN KEY `gallery_ibfk_2`;
ALTER TABLE `gallery`
	CHANGE `res_id` `res_id` INTEGER UNSIGNED NULL DEFAULT NULL,
	DROP `login_id`,
	DROP `gal_visible`,
	DROP `gal_entered`,

	DROP `gal_name`,
	DROP `gal_data`;
ALTER TABLE `gallery`
	ADD CONSTRAINT `gallery_ibfk_2` FOREIGN KEY (`res_id`) REFERENCES `res`(`res_id`) ON DELETE SET NULL ON UPDATE CASCADE
;

ALTER TABLE `gallery_data`
	CHANGE `res_id` `res_id` INTEGER UNSIGNED NULL DEFAULT NULL,
	DROP FOREIGN KEY `gallery_data_ibfk_1`;
ALTER TABLE `gallery_data`
	DROP FOREIGN KEY `gallery_data_ibfk_2`,
	DROP `gd_visible`,
	DROP `gd_entered`,

	DROP `gd_filename`,
	DROP `gd_descr`,
	DROP `gal_id`;
ALTER TABLE `gallery_data`
	ADD CONSTRAINT `gallery_data_ibfk_2` FOREIGN KEY (`res_id`) REFERENCES `res`(`res_id`) ON DELETE SET NULL ON UPDATE CASCADE
;

DROP TABLE `comment_map`;
DROP TABLE `res_comment`;

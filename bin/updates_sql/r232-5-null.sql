ALTER TABLE `logins`
CHANGE `votes_plus` `votes_plus` INT(11) UNSIGNED NULL DEFAULT 0,
CHANGE `votes_minus` `votes_minus` INT(11) UNSIGNED NULL DEFAULT 0,
CHANGE `comment_count` `comment_count` INT(11) UNSIGNED NULL DEFAULT 0;

ALTER TABLE `forum` CHANGE `forum_modid` `forum_modid` INT(10) UNSIGNED NULL DEFAULT NULL;
UPDATE forum SET forum_modid = NULL WHERE forum_modid = 0;
ALTER TABLE `forum` ADD FOREIGN KEY (`forum_modid`) REFERENCES `modules`(`mod_id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

DROP VIEW view_document_titles;

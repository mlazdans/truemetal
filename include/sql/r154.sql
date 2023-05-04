RENAME TABLE `gallery`  TO `gallery_old`;
--
RENAME TABLE `gallery_group`  TO `gallery_group_old`;
--
ALTER TABLE `forum` ADD `forum_modid` INT NOT NULL AFTER `forum_userid`;
--
UPDATE `forum` SET `forum_modid` =1 WHERE `forum_display` =1 and `forum_showmainpage`=1;
--
ALTER TABLE `article` CHANGE `art_id` `art_id` INT( 11 ) UNSIGNED NOT NULL AUTO_INCREMENT;
--
ALTER TABLE `forum` CHANGE `forum_id` `forum_id` INT( 11 ) UNSIGNED NOT NULL AUTO_INCREMENT;
--


ALTER TABLE `article` ADD INDEX ( `art_active` , `art_entered` );
--
DROP TABLE `forum_badusers`;
--
ALTER TABLE `sessions` DROP `sess_lastpath`;
--


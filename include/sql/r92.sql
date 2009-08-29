ALTER TABLE `article` DROP INDEX `art_hash_date`;
--
ALTER TABLE `article`
DROP `art_hash`,
DROP `art_hash_date`;
--
ALTER TABLE `article` ADD INDEX ( `art_modid` , `art_entered` );
--
ALTER TABLE `forum` DROP INDEX `forum_hash_date`;
--
ALTER TABLE `forum`
DROP `forum_hash`,
DROP `forum_hash_date`;
--
DROP TRIGGER article_comments_trigger3;
--
DROP TRIGGER article_comments_trigger4;
--
DROP TRIGGER article_trigger3;
--
DROP TRIGGER article_trigger4;
--
DROP TRIGGER forum_trigger1;
--
DROP TRIGGER forum_trigger2;
--
DROP TRIGGER forum_trigger3;
--
DROP TRIGGER forum_trigger4;
--
DROP TRIGGER forum_trigger5;
--


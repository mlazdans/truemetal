ALTER TABLE `forum` CHANGE `forum_userid` `forum_userid` INT UNSIGNED NULL DEFAULT NULL;
ALTER TABLE `forum` CHANGE `forum_userid` `login_id` INT( 10 ) UNSIGNED NULL DEFAULT NULL;
UPDATE `forum` SET `login_id` =  NULL WHERE `login_id` =0;
UPDATE forum SET `login_id` =  NULL WHERE login_id NOT IN (SELECT l_id FROM logins);
ALTER TABLE `forum` ADD FOREIGN KEY ( `login_id` ) REFERENCES `logins` (
`l_id`
) ON DELETE SET NULL ON UPDATE CASCADE ;
--
RENAME TABLE `comment_meta`  TO `comment_meta_old`;
--


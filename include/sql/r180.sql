/* Old Tables */
RENAME TABLE `forum_meta`  TO `forum_meta_old`;

/* Old Proc */
DROP PROCEDURE `comment_update_meta`;
DROP PROCEDURE `comment_update_meta_all`;
DROP PROCEDURE `comment_update_votes`;

/* Old Triggers */
DROP TRIGGER IF EXISTS `truemetal`.`comment_connect_trigger1`;
DROP TRIGGER IF EXISTS `truemetal`.`comment_connect_trigger2`;
DROP TRIGGER IF EXISTS `truemetal`.`comment_connect_trigger3`;
DROP TRIGGER IF EXISTS `truemetal`.`comment_vote_trigger1`;
DROP TRIGGER IF EXISTS `truemetal`.`comment_vote_trigger2`;

/* Res */
CREATE TABLE IF NOT EXISTS `res` (
	`res_id` int(10) unsigned NOT NULL auto_increment,
	`table_id` smallint(5) unsigned default NULL,
	`login_id` int(10) unsigned NOT NULL,
	`res_comment_count` int(11) default NULL COMMENT 'meta',
	`res_comment_lastdate` datetime default NULL COMMENT 'meta',
	`res_votes` int(11) default NULL COMMENT 'meta',
	`res_entered` datetime default NULL,
	PRIMARY KEY  (`res_id`)
);

/* Article */
ALTER TABLE `article` ADD `res_id` INT UNSIGNED NULL AFTER `art_id` ,
ADD UNIQUE (
`res_id`
);
ALTER TABLE `article` ADD FOREIGN KEY ( `res_id` ) REFERENCES `truemetal`.`res` (
`id`
) ON DELETE SET NULL ON UPDATE SET NULL ;
ALTER TABLE `article` ADD `login_id` INT UNSIGNED NOT NULL AFTER `res_id`;
UPDATE `article` SET `login_id` = 3;
ALTER TABLE `article` ADD INDEX ( `login_id` );
ALTER TABLE `article` ADD FOREIGN KEY ( `login_id` ) REFERENCES `truemetal`.`logins` (
`l_id`
) ON DELETE RESTRICT ON UPDATE RESTRICT ;


/* Forum */
ALTER TABLE `forum` ADD `res_id` INT UNSIGNED NULL AFTER `forum_id` ,
ADD UNIQUE (
`res_id`
);
ALTER TABLE `forum` ADD FOREIGN KEY ( `res_id` ) REFERENCES `truemetal`.`res` (
`id`
) ON DELETE SET NULL ON UPDATE SET NULL ;

/* Logins */
ALTER TABLE `logins` CHANGE `l_id` `l_id` INT UNSIGNED NOT NULL AUTO_INCREMENT;

/* Comment */
ALTER TABLE `comment` ADD `res_id` INT UNSIGNED NULL AFTER `c_id` ,
ADD UNIQUE (
`res_id`
);
ALTER TABLE `comment` ADD FOREIGN KEY ( `res_id` ) REFERENCES `truemetal`.`res` (
`id`
) ON DELETE SET NULL ON UPDATE SET NULL ;
ALTER TABLE `comment` DROP `c_hash` ,
DROP `c_hash_date` ;
DROP TRIGGER IF EXISTS `truemetal`.`comment_trigger1`;
DROP TRIGGER IF EXISTS `truemetal`.`comment_trigger2`;
DROP TRIGGER IF EXISTS `truemetal`.`comment_trigger3`;
DROP TRIGGER IF EXISTS `truemetal`.`comment_trigger4`;
ALTER TABLE `comment` CHANGE `c_id` `c_id` INT UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `comment` CHANGE `c_userid` `login_id` INT UNSIGNED NOT NULL;
ALTER TABLE `comment` DROP INDEX `c_userid` ,
ADD INDEX ( `login_id` );
ALTER TABLE `comment` CHANGE `login_id` `login_id` INT( 10 ) UNSIGNED NULL;
UPDATE `comment` SET login_id =  NULL WHERE login_id =0;
-- fix some straginess --
UPDATE `comment` SET `login_id` =842 WHERE login_id =436;
UPDATE `comment` SET `login_id` =  NULL WHERE login_id =720
ALTER TABLE `comment` ADD FOREIGN KEY ( `login_id` ) REFERENCES `truemetal`.`logins` (
`l_id`
) ON DELETE SET NULL ON UPDATE SET NULL ;

/* Resource comments */
RENAME TABLE `comment_connect` TO `comment_connect_old`;
CREATE TABLE IF NOT EXISTS `res_comment` (
	`res_id` int(10) unsigned NOT NULL,
	`c_id` int(10) unsigned NOT NULL,
	UNIQUE KEY `res_id` (`res_id`,`c_id`),
	KEY `c_id` (`c_id`)
);
ALTER TABLE `res_comment`
	ADD CONSTRAINT `res_comment_ibfk_2` FOREIGN KEY (`c_id`) REFERENCES `comment` (`c_id`) ON DELETE CASCADE ON UPDATE CASCADE,
	ADD CONSTRAINT `res_comment_ibfk_3` FOREIGN KEY (`res_id`) REFERENCES `res` (`res_id`) ON DELETE CASCADE ON UPDATE CASCADE
;
-- Convert old comment_connect to new res_comment
INSERT INTO `res_comment`
SELECT
	a.`res_id`,
	cc.`cc_c_id` AS c_id
FROM
	`comment_connect_old` cc
JOIN `article` a ON a.`art_id` = cc.`cc_table_id`
WHERE
	cc.`cc_table` = 'article'
UNION
SELECT
	f.`res_id`,
	cc.`cc_c_id` AS c_id
FROM
	`comment_connect_old` cc
JOIN `forum` f ON f.`forum_id` = cc.`cc_table_id`
WHERE
	cc.`cc_table` = 'forum'
;

/* Votes */
ALTER TABLE `comment` DROP `c_votes`;
RENAME TABLE `comment_votes` TO `comment_votes_old`;
CREATE TABLE IF NOT EXISTS `res_vote` (
	`res_id` int(10) unsigned NOT NULL,
	`login_id` int(10) unsigned NOT NULL default '0',
	`rv_value` tinyint(4) NOT NULL,
	`rv_userip` varbinary(15) NOT NULL,
	`rv_entered` datetime NOT NULL,
	UNIQUE KEY `res_id` (`res_id`,`login_id`),
	KEY `login_id` (`login_id`)
);
ALTER TABLE `res_vote`
	ADD CONSTRAINT `res_vote_ibfk_2` FOREIGN KEY (`login_id`) REFERENCES `logins` (`l_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
	ADD CONSTRAINT `res_vote_ibfk_1` FOREIGN KEY (`res_id`) REFERENCES `res` (`res_id`) ON DELETE CASCADE ON UPDATE CASCADE
;
-- Convert old comment_votes to new res_votes
INSERT INTO `res_vote`
SELECT
	c.`res_id`,
	cv.`cv_userid`,
	cv.`cv_value`,
	cv.`cv_userip`,
	cv.`cv_entered`
FROM
	`comment_votes_old` cv
JOIN `comment` c ON c.`c_id` = cv.`cv_c_id`
;


/* New Procs */
-- PROC_res_update_meta.sql
-- PROC_res_update_meta_all.sql


/* Views */
-- view_mainpage.sql



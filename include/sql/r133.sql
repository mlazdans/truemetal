ALTER TABLE `comment` ADD INDEX ( `c_userid` );
--
ALTER TABLE `logins` ADD INDEX ( `l_entered` );
--
ALTER TABLE `truemetal`.`comment` DROP INDEX `c_userid` ,
ADD INDEX `c_userid` ( `c_userid` , `c_votes` );
--


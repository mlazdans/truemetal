ALTER TABLE `forum` DROP `forum_votes`;
--
ALTER TABLE `forum` ADD `forum_display` INT NOT NULL COMMENT '0=forum_datacompiled; 1=forum_data' AFTER `forum_datacompiled`;
--
ALTER TABLE `forum` ADD `forum_showmainpage` INT NOT NULL DEFAULT 0 AFTER `forum_display`;
--
ALTER TABLE `forum` ADD INDEX ( `forum_showmainpage` , `forum_active` );
--
CREATE VIEW view_mainpage AS
SELECT
	m.module_id,
	a.art_id,
	COALESCE(cm_comment_count, 0) AS art_comment_count,
	cm_comment_lastdate AS art_comment_lastdate,
	a.art_name,
	a.art_intro,
	a.art_data,
	a.art_entered
FROM
	`article` a
JOIN `modules` m ON (a.art_modid = m.mod_id)
LEFT JOIN `comment_meta` ON (cm_table = 'article') AND (cm_table_id = a.art_id)
WHERE
	art_active = 'Y' AND
	art_modid = 1
UNION
SELECT
	'forum' AS module_id,
	forum_id AS art_id,
	COALESCE(cm_comment_count, 0) AS art_comment_count,
	cm_comment_lastdate AS art_comment_lastdate,
	forum_name,
	forum_data as art_intro,
	forum_data as art_data,
	forum_entered
FROM
	forum
LEFT JOIN comment_meta ON (cm_table = 'forum') AND (cm_table_id = forum_id)
WHERE
	forum_active = 'Y' AND
	forum_showmainpage = 1
;
--


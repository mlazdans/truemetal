CREATE TABLE IF NOT EXISTS `comment_disabled` (
	`login_id` int(11) NOT NULL,
	`disable_login_id` int(11) NOT NULL,
	UNIQUE KEY `login_id` (`login_id`,`disable_login_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
--
INSERT INTO comment_disabled (login_id, disable_login_id)
SELECT l_id, 517 FROM logins WHERE l_disable_bobi = 1;
--


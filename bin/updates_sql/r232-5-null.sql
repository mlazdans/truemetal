ALTER TABLE `logins`
CHANGE `votes_plus` `votes_plus` INT(11) UNSIGNED NULL DEFAULT 0,
CHANGE `votes_minus` `votes_minus` INT(11) UNSIGNED NULL DEFAULT 0,
CHANGE `comment_count` `comment_count` INT(11) UNSIGNED NULL DEFAULT 0;

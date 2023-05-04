ALTER TABLE `res` CHANGE `login_id` `login_id` INT( 10 ) UNSIGNED NULL;
UPDATE `res` SET login_id =  NULL WHERE login_id =0;
ALTER TABLE `res` ADD FOREIGN KEY (`login_id`) REFERENCES `logins`(`l_id`) ON DELETE SET NULL ON UPDATE SET NULL;


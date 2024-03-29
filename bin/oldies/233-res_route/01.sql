-- 1) run oldies verify_specialchars.php
-- 2) apply 01.sql
-- 3) import_procedures.bat
-- 4) php.exe res_route_update.php > res_upd.sql
-- 5) mysql -uroot truemetal < res_upd.sql
-- 6)  res_route_update.php ar commentiem, jo tiem vajag parent vispirms
-- 6) apply 02.sql
-- 7) adjust shpinx

-- ALTER TABLE `res` ADD `res_modid` INT UNSIGNED NULL DEFAULT NULL AFTER `res_id`;
ALTER TABLE `res`
	ADD `res_route` VARCHAR(128) NULL DEFAULT NULL,
	RENAME COLUMN `table_id` TO `res_kind`
;

-- ALTER TABLE `res` ADD FOREIGN KEY (`res_modid`) REFERENCES `modules`(`mod_id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

-- INSERT INTO modules (mod_id, module_id, module_name, module_descr, module_pos, module_type) VALUES (2, 'forum', 'Forums', 'Metāliskās diskusijas', 4, 'O');
-- INSERT INTO modules (mod_id, module_id, module_name, module_descr, module_pos, module_type) VALUES (5, 'comments', 'Komentāri', '', 5, 'O');
-- INSERT INTO modules (mod_id, module_id, module_name, module_descr, module_pos, module_type) VALUES (6, 'gallery', 'Galerijas', '', 6, 'O');
-- INSERT INTO modules (mod_id, module_id, module_name, module_descr, module_pos, module_type) VALUES (7, 'gd', 'Galeriju bildes', '', 7, 'O');

-- Remove comments w/o parent or data
DELETE FROM `res` WHERE res_kind=3 and (res_data is null OR res_data = '' OR res_resid is null);

-- Remove ghosts
DELETE FROM `res` WHERE res_resid IN (249279, 289969);
DELETE FROM `res` WHERE res_id IN (249279, 289969);

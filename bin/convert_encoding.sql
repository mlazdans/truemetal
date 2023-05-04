USE information_schema;
SELECT CONCAT("ALTER DATABASE `",table_schema,"` CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci;") AS _sql
FROM `TABLES` WHERE table_schema LIKE "truemetal" AND TABLE_TYPE='BASE TABLE' GROUP BY table_schema
UNION
SELECT CONCAT("ALTER TABLE `",table_schema,"`.`",table_name,"` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;") AS _sql  
FROM `TABLES` WHERE table_schema LIKE "truemetal" AND TABLE_TYPE='BASE TABLE' GROUP BY table_schema, table_name
UNION
SELECT CONCAT("ALTER TABLE `",`COLUMNS`.table_schema,"`.`",`COLUMNS`.table_name, "` CHANGE `",column_name,"` `",column_name,"` ",data_type,"(",character_maximum_length,") CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci",IF(is_nullable="YES"," NULL"," NOT NULL"),";") AS _sql 
FROM `COLUMNS` INNER JOIN `TABLES` ON `TABLES`.table_name = `COLUMNS`.table_name WHERE `COLUMNS`.table_schema like "truemetal" and data_type in ('varchar','char') AND TABLE_TYPE='BASE TABLE'
UNION
SELECT CONCAT("ALTER TABLE `",`COLUMNS`.table_schema,"`.`",`COLUMNS`.table_name, "` CHANGE `",column_name,"` `",column_name,"` ",data_type," CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci",IF(is_nullable="YES"," NULL"," NOT NULL"),";") AS _sql 
FROM `COLUMNS` INNER JOIN `TABLES` ON `TABLES`.table_name = `COLUMNS`.table_name WHERE `COLUMNS`.table_schema like "truemetal" and data_type in ('text','tinytext','mediumtext','longtext') AND TABLE_TYPE='BASE TABLE';

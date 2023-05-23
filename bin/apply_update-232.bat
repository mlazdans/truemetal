@echo off
chcp 437

mysql -uroot truemetal_remote < updates_sql\r232-1.sql
C:\php82\php.exe res_update.php
mysql -uroot truemetal_remote < updates_sql\r232-2.sql
mysql -uroot truemetal_remote < updates_sql\r232-3.sql
mysql -uroot truemetal_remote < updates_sql\r232-4-res_merge.sql
mysql -uroot truemetal_remote < updates_sql\r232-5-null.sql
mysql -uroot truemetal_remote < updates_sql\r235.sql
call mysql_procedures/import_procedures.bat
C:\php82\php.exe sess_update.php
C:\php82\php.exe theme_collect_dup_comment.php > theme_comment_merged.log

mysql -uroot truemetal_remote < %~dp0%PROC_logins_update_meta.sql
mysql -uroot truemetal_remote < %~dp0%PROC_res_update_meta.sql
mysql -uroot truemetal_remote < %~dp0%TRIG_logins_trigger1_BEFORE_INSERT.sql
mysql -uroot truemetal_remote < %~dp0%TRIG_res_trigger1_AFTER_UPDATE.sql
mysql -uroot truemetal_remote < %~dp0%TRIG_res_trigger2_AFTER_DELETE.sql
mysql -uroot truemetal_remote < %~dp0%TRIG_res_trigger3_AFTER_INSERT.sql
mysql -uroot truemetal_remote < %~dp0%TRIG_res_vote_trigger1_AFTER_INSERT.sql
mysql -uroot truemetal_remote < %~dp0%TRIG_res_vote_trigger2_AFTER_DELETE.sql
mysql -uroot truemetal_remote < %~dp0%TRIG_res_vote_trigger3_AFTER_UPDATE.sql
mysql -uroot truemetal_remote < %~dp0%view_res.sql
mysql -uroot truemetal_remote < %~dp0%view_attend.sql
mysql -uroot truemetal_remote < %~dp0%view_jubilars.sql
mysql -uroot truemetal_remote < %~dp0%view_res_article.sql
mysql -uroot truemetal_remote < %~dp0%view_res_comment.sql
mysql -uroot truemetal_remote < %~dp0%view_res_forum.sql
mysql -uroot truemetal_remote < %~dp0%view_res_gallery.sql
mysql -uroot truemetal_remote < %~dp0%view_res_gd.sql
mysql -uroot truemetal_remote < %~dp0%view_res_gd_data.sql
mysql -uroot truemetal_remote < %~dp0%view_res_orphans.sql
mysql -uroot truemetal_remote < %~dp0%view_documents.sql
mysql -uroot truemetal_remote < %~dp0%view_mainpage.sql

@rem echo %~dp0
@rem dir /b %%~dp0\*sql

@REM for /F %%i in ('dir /b %~dp0\*sql') do (
@REM 	echo %%i
@REM 	mysql -uroot truemetal_remote < %~DP0%%~dp0%%i
@REM )

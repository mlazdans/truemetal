mysql -uroot truemetal_remote < %~dp0%PROC_res_meta_update_childs.sql
mysql -uroot truemetal_remote < %~dp0%PROC_res_meta_update_votes.sql
mysql -uroot truemetal_remote < %~dp0%PROC_res_meta_update.sql
mysql -uroot truemetal_remote < %~dp0%PROC_logins_meta_update.sql
mysql -uroot truemetal_remote < %~dp0%logins_trigger_BI.sql
@REM mysql -uroot truemetal_remote < %~dp0%res_trigger_AU.sql
@REM mysql -uroot truemetal_remote < %~dp0%res_trigger_AD.sql
@REM mysql -uroot truemetal_remote < %~dp0%res_trigger_AI.sql
@REM mysql -uroot truemetal_remote < %~dp0%res_vote_trigger_AI.sql
@REM mysql -uroot truemetal_remote < %~dp0%res_vote_trigger_AD.sql
@REM mysql -uroot truemetal_remote < %~dp0%res_vote_trigger_AU.sql
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

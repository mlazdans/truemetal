mysql -uroot truemetal < FUNC_urlize.sql

mysql -uroot truemetal < PROC_res_meta_update_childs.sql
mysql -uroot truemetal < PROC_res_meta_update_votes.sql
mysql -uroot truemetal < PROC_res_meta_update.sql

mysql -uroot truemetal < PROC_logins_meta_update_votes.sql
mysql -uroot truemetal < PROC_logins_meta_update_comments.sql
mysql -uroot truemetal < PROC_logins_meta_update.sql

mysql -uroot truemetal < logins_trigger_BI.sql

mysql -uroot truemetal < res_trigger_AU.sql
mysql -uroot truemetal < res_trigger_AD.sql
mysql -uroot truemetal < res_trigger_AI.sql
mysql -uroot truemetal < res_vote_trigger_AI.sql
mysql -uroot truemetal < res_vote_trigger_AD.sql
mysql -uroot truemetal < res_vote_trigger_AU.sql

mysql -uroot truemetal < view_res.sql
mysql -uroot truemetal < view_attend.sql
mysql -uroot truemetal < view_jubilars.sql
mysql -uroot truemetal < view_res_article.sql
mysql -uroot truemetal < view_res_comment.sql
mysql -uroot truemetal < view_res_forum.sql
mysql -uroot truemetal < view_res_gallery.sql
mysql -uroot truemetal < view_res_gd.sql
mysql -uroot truemetal < view_res_gd_data.sql
mysql -uroot truemetal < view_res_orphans.sql
mysql -uroot truemetal < view_documents.sql
mysql -uroot truemetal < view_mainpage.sql

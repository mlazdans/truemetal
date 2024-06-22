CREATE OR REPLACE VIEW view_res AS
SELECT
res.res_id,
res.res_hash,
res.res_resid,
res.res_kind,
res.login_id,
res.res_entered,
res.res_nickname,
res.res_email,
res.res_ip,
res.res_visible,
res.res_name,
res.res_intro,
res.res_data,
res.res_data_compiled,
res.res_route,
res_meta.res_votes,
res_meta.res_votes_plus_count,
res_meta.res_votes_minus_count,
res_meta.res_child_count,
res_meta.res_child_last_date,
res_meta.res_comment_count,
res_meta.res_comment_last_date,
logins.l_hash
FROM res
JOIN res_meta ON (res_meta.res_id = res.res_id)
LEFT JOIN logins ON (res.login_id = logins.l_id)

CREATE OR REPLACE VIEW view_res_comment AS
SELECT
comment.c_id,
parent.res_route AS parent_res_route,
view_res.*
FROM comment
JOIN view_res ON (view_res.res_id = comment.res_id)
JOIN res parent ON parent.res_id = view_res.res_resid
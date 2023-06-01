CREATE OR REPLACE VIEW view_res_comment AS
SELECT
comment.c_id,
view_res.*
FROM comment
JOIN view_res ON (view_res.res_id = comment.res_id)

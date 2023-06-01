CREATE OR REPLACE VIEW view_res_gd AS
SELECT
gallery_data.gd_id,
gallery_data.gd_mime,
view_res.*
FROM gallery_data
JOIN view_res ON (view_res.res_id = gallery_data.res_id)

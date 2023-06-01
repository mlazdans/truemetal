CREATE OR REPLACE VIEW view_res_gd_data AS
SELECT
gallery_data.gd_id,
gallery_data.gd_mime,
gallery_data.gd_data,
gallery_data.gd_thumb,
view_res.*
FROM gallery_data
JOIN view_res ON (view_res.res_id = gallery_data.res_id)

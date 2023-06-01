CREATE OR REPLACE VIEW view_res_gallery AS
SELECT
gallery.gal_id,
gallery.gal_ggid,
gg.gg_id,
gg.gg_name,
gg.gg_data,
gg.gg_date,
gg.gg_entered,
view_res.*
FROM gallery
JOIN view_res ON (view_res.res_id = gallery.res_id)
LEFT JOIN gallery_group_old gg ON gg.gg_id = gallery.gal_ggid

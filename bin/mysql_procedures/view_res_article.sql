CREATE OR REPLACE VIEW view_res_article AS
SELECT
article.art_id,
article.art_modid,
modules.mod_id,
modules.mod_modid,
modules.module_id,
modules.module_name,
modules.module_descr,
modules.module_active,
modules.module_visible,
modules.module_pos,
modules.module_data,
modules.module_entered,
modules.module_type,
view_res.*
FROM article
JOIN modules ON (article.art_modid = modules.mod_id)
JOIN view_res ON (view_res.res_id = article.res_id)
WHERE modules.module_active = 1

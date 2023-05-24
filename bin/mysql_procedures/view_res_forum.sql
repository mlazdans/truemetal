CREATE OR REPLACE VIEW view_res_forum AS
SELECT
forum.forum_id,
forum.forum_modid,
forum.forum_allow_childs,
forum.forum_closed,
forum.forum_display,
forum.type_id,
forum.event_startdate,
modules.mod_id,
modules.mod_modid,
modules.module_id,
modules.module_name,
view_res.*
FROM forum
JOIN view_res ON (view_res.res_id = forum.res_id)
LEFT JOIN modules ON (forum.forum_modid = modules.mod_id AND modules.module_active = 1)

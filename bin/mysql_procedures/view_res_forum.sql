CREATE OR REPLACE VIEW view_res_forum AS
SELECT
forum.forum_id,
forum.forum_modid,
forum.forum_allow_childs,
forum.forum_closed,
forum.forum_display,
forum.type_id,
forum.event_startdate,
view_res.*
FROM forum
JOIN view_res ON (view_res.res_id = forum.res_id)

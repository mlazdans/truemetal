-- LOGINS
ALTER TABLE logins CHANGE l_disable_youtube l_disable_youtube TINYINT UNSIGNED NOT NULL DEFAULT 0;

UPDATE logins SET l_forumsort_themes = 'T' WHERE l_forumsort_themes != 'C';
UPDATE logins SET l_forumsort_themes = '0' WHERE l_forumsort_themes = 'T';
UPDATE logins SET l_forumsort_themes = '1' WHERE l_forumsort_themes = 'C';
ALTER TABLE logins CHANGE l_forumsort_themes l_forumsort_themes TINYINT UNSIGNED NOT NULL DEFAULT 0;

UPDATE logins SET l_forumsort_msg = 'A' WHERE l_forumsort_msg != 'D';
UPDATE logins SET l_forumsort_msg = '0' WHERE l_forumsort_msg = 'A';
UPDATE logins SET l_forumsort_msg = '1' WHERE l_forumsort_msg = 'D';
ALTER TABLE logins CHANGE l_forumsort_msg l_forumsort_msg TINYINT UNSIGNED NOT NULL DEFAULT 0;

ALTER TABLE logins ADD CONSTRAINT l_active_check CHECK (l_active IN (0,1));
ALTER TABLE logins ADD CONSTRAINT l_accepted_check CHECK (l_accepted IN (0,1));
ALTER TABLE logins ADD CONSTRAINT l_logedin_check CHECK (l_logedin IN (0,1));
ALTER TABLE logins ADD CONSTRAINT l_forumsort_themes_check CHECK (l_forumsort_themes IN (0,1));
ALTER TABLE logins ADD CONSTRAINT l_forumsort_msg_check CHECK (l_forumsort_msg IN (0,1));
ALTER TABLE logins ADD CONSTRAINT l_disable_youtube_check CHECK (l_disable_youtube IN (0,1));

-- FORUM
ALTER TABLE forum CHANGE forum_display forum_display TINYINT UNSIGNED NOT NULL DEFAULT 0;
ALTER TABLE forum CHANGE type_id type_id TINYINT UNSIGNED NOT NULL DEFAULT 0;

ALTER TABLE forum ADD CONSTRAINT forum_allow_childs_check CHECK (forum_allow_childs IN (0,1));
ALTER TABLE forum ADD CONSTRAINT forum_closed_check CHECK (forum_closed IN (0,1));
ALTER TABLE forum ADD CONSTRAINT forum_display_check CHECK (forum_display IN (0,1));
ALTER TABLE forum ADD CONSTRAINT type_id_check CHECK (type_id IN (0,1));

-- RES
ALTER TABLE res ADD CONSTRAINT res_visible_check CHECK (res_visible IN (0,1));

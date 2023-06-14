ALTER TABLE forum CHANGE forum_display forum_display TINYINT UNSIGNED NOT NULL DEFAULT 0;
ALTER TABLE forum CHANGE type_id type_id TINYINT UNSIGNED NOT NULL DEFAULT 0;

ALTER TABLE forum ADD CONSTRAINT forum_allow_childs_check CHECK (forum_allow_childs IN (0,1));
ALTER TABLE forum ADD CONSTRAINT forum_closed_check CHECK (forum_closed IN (0,1));
ALTER TABLE forum ADD CONSTRAINT forum_display_check CHECK (forum_display IN (0,1));
ALTER TABLE forum ADD CONSTRAINT type_id_check CHECK (type_id IN (0,1));

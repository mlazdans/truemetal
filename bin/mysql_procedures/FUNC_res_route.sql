DELIMITER $$

DROP FUNCTION IF EXISTS res_route $$
CREATE FUNCTION res_route(
	p_res_id INTEGER UNSIGNED
) RETURNS VARCHAR(255)
BEGIN
	IF p_res_id IS NULL THEN
		SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'NULL is not allowed';
	END IF;

	SET @res_id = p_res_id;
	SET @res_route = NULL;

	SELECT res_kind, res_name INTO @res_kind, @res_name FROM res WHERE res_id = @res_id;

	-- Articles
	IF @res_kind = 1 THEN
		SELECT
			article_route(modules.module_id, article.art_id, @res_name) INTO @res_route
		FROM
			article
		JOIN modules ON article.art_modid = modules.mod_id
		WHERE
			article.res_id = @res_id;
	END IF;

	-- Forum
	IF @res_kind = 2 THEN
		SELECT
			forum_route(forum.forum_id, @res_name) INTO @res_route
		FROM
			forum
		WHERE
			forum.res_id = @res_id;
	END IF;

	-- Comment
	IF @res_kind = 3 THEN
		SELECT
			comment.c_id INTO @c_id
		FROM
			comment
		JOIN res ON res.res_id = comment.res_id
		WHERE
			comment.res_id = @res_id;

		SELECT
			comment_route(parent.res_route, comment.c_id) INTO @res_route
		FROM
			comment
		JOIN res ON res.res_id = comment.res_id
		JOIN res_meta parent ON parent.res_id = res.res_resid
		WHERE
			comment.res_id = @res_id;
	END IF;

	-- Gallery
	IF @res_kind = 4 THEN
		SELECT
			gallery_route(gallery.gal_id) INTO @res_route
		FROM
			gallery
		WHERE
			gallery.res_id = @res_id;
	END IF;

	-- Gallery data
	IF @res_kind = 5 THEN
		SELECT
			gallery_data_route(gallery_data.gd_id) INTO @res_route
		FROM
			gallery_data
		WHERE
			gallery_data.res_id = @res_id;
	END IF;

	RETURN @res_route;
END $$

DELIMITER ;

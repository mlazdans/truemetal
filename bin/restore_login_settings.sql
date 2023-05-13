SELECT
	CONCAT(
		'UPDATE logins SET l_emailvisible = ''', l_emailvisible,
		''', l_disable_youtube = ''', l_disable_youtube,
		''',  l_forumsort_themes = ''', l_forumsort_themes,
		''', l_forumsort_msg = ''', l_forumsort_msg,
		''' WHERE l_id = ', l_id,
		';'
	) AS s
FROM logins
WHERE l_id <= 4100
ORDER BY l_id DESC;

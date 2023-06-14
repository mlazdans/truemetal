DROP TABLE IF EXISTS res_history;
CREATE TABLE res_history (
	history_id int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	res_id int(10) UNSIGNED NOT NULL,
	login_id int(10) UNSIGNED NULL,
	res_data mediumtext DEFAULT NULL,
	res_data_compiled mediumtext DEFAULT NULL,
	entered TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (history_id),
	FOREIGN KEY (login_id) REFERENCES logins (l_id),
	FOREIGN KEY (res_id) REFERENCES res (res_id)
) ENGINE=InnoDB;

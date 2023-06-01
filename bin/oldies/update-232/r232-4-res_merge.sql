DROP TABLE IF EXISTS res_redirect;
CREATE TABLE `res_redirect` (
  `from_res_id` int(10) UNSIGNED NOT NULL,
  `to_res_id` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`from_res_id`,`to_res_id`),
  UNIQUE KEY `from_res_id` (`from_res_id`)
) ENGINE=InnoDB;

DROP TABLE IF EXISTS res_merge;
CREATE TABLE `res_merge` (
  `forum_res_id` INTEGER UNSIGNED NOT NULL,
  `comment_res_id` INTEGER UNSIGNED NOT NULL,
  `ignored` tinyint(4) NOT NULL DEFAULT 0,
  PRIMARY KEY (`forum_res_id`,`comment_res_id`)
) ENGINE=InnoDB;

INSERT INTO `res_merge` (`forum_res_id`, `comment_res_id`, `ignored`) VALUES
(246788, 141087, 1),
(246808, 141806, 0),
(246812, 141927, 0),
(246821, 142357, 0),
(246844, 230185, 1),
(246874, 144612, 0),
(246914, 145726, 0),
(246982, 148776, 0),
(247036, 151074, 0),
(247039, 151184, 1),
(247061, 152171, 1),
(247086, 152744, 1),
(247094, 153076, 0),
(247106, 153406, 0),
(247124, 154011, 0),
(247141, 155192, 1),
(247143, 154775, 0),
(247164, 155902, 0),
(247206, 158003, 0),
(247209, 158286, 0),
(247267, 161082, 0),
(247362, 165044, 0),
(247374, 165589, 0),
(247390, 166214, 0),
(247442, 168718, 0),
(247565, 174626, 0),
(247606, 176685, 0),
(247635, 178137, 0),
(247654, 179564, 0),
(247656, 179621, 0),
(247723, 181792, 0),
(247822, 185273, 0),
(247826, 185539, 0),
(247939, 191347, 0),
(248011, 195701, 0),
(248016, 195973, 0),
(248026, 196699, 0),
(248067, 198620, 0),
(248069, 198942, 0),
(248132, 202060, 0),
(248196, 205054, 0),
(248198, 205263, 0),
(248304, 210687, 0),
(248386, 214868, 0),
(248522, 222229, 0),
(248562, 223726, 0),
(248648, 227739, 0),
(248654, 228037, 0),
(248700, 229597, 0),
(248862, 236001, 0),
(249013, 241692, 0),
(249365, 249366, 0),
(252431, 252432, 0),
(255169, 255170, 0),
(264125, 264126, 0),
(265651, 265652, 0),
(282128, 282135, 1),
(282631, 282632, 0),
(285658, 285659, 0),
(285998, 285999, 0),
(286138, 285480, 0),
(286140, 286115, 0),
(289940, 289941, 1),
(290778, 290779, 0),
(292206, 292803, 1),
(293520, 293522, 1);
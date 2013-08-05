<?php
include 'db.php';

$db->exec(
"CREATE TABLE IF NOT EXISTS `continents` (
  `id` int UNSIGNED,
  `ui_id` int UNSIGNED,
  `name` varchar(50),
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;"
);

$db->exec(
"INSERT INTO continents (id,ui_id,name) values (1,209,'North America');"
);
$db->exec(
"INSERT INTO continents (id,ui_id,name) values (2,210,'South America');"
);
$db->exec(
"INSERT INTO continents (id,ui_id,name) values (3,211,'Europe');"
);
$db->exec(
"INSERT INTO continents (id,ui_id,name) values (4,212,'Asia');"
);
$db->exec(
"INSERT INTO continents (id,ui_id,name) values (5,213,'Africa');"
);
$db->exec(
"INSERT INTO continents (id,ui_id,name) values (6,214,'Australia');"
);

$db->exec(
"CREATE TABLE IF NOT EXISTS `languages` (
  `id` int UNSIGNED AUTO_INCREMENT,
  `ui_id` int UNSIGNED,
  `lang` varchar(10),
  `name` varchar(50),
  `signed` bool,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;"
);

$db->exec(
"INSERT INTO languages (ui_id,lang,name,signed) 
values (215,'en','English',0);"
);
$db->exec(
"INSERT INTO languages (ui_id,lang,name,signed) 
values (216,'ase','American Sign Language',1);"
);
$db->exec(
"INSERT INTO languages (ui_id,lang,name,signed) 
values (217,'de','German',0);"
);
$db->exec(
"INSERT INTO languages (ui_id,lang,name,signed) 
values (218,'gsg','German Sign Language',1);"
);
$db->exec(
"INSERT INTO languages (ui_id,lang,name,signed) 
values (219,'fr','French',0);"
);
$db->exec(
"INSERT INTO languages (ui_id,lang,name,signed) 
values (220,'ssr','Swiss-French Sign Language',1);"
);
$db->exec(
"INSERT INTO languages (ui_id,lang,name,signed) 
values (221,'no','Norwegian',0);"
);
$db->exec(
"INSERT INTO languages (ui_id,lang,name,signed) 
values (222,'nsl','Norwegian Sign Language',1);"
);
$db->exec(
"INSERT INTO languages (ui_id,lang,name,signed) 
values (223,'cs','Czech',0);"
);
$db->exec(
"INSERT INTO languages (ui_id,lang,name,signed) 
values (224,'cse','Czech Sign Language',1);"
);
$db->exec(
"INSERT INTO languages (ui_id,lang,name,signed) 
values (225,'pl','Polish',0);"
);
$db->exec(
"INSERT INTO languages (ui_id,lang,name,signed) 
values (226,'pso','Polish Sign Language',1);"
);
$db->exec(
"INSERT INTO languages (ui_id,lang,name,signed) 
values (227,'ca','Catalan',0);"
);
$db->exec(
"INSERT INTO languages (ui_id,lang,name,signed) 
values (228,'csc','Catalan Sign Language',1);"
);
$db->exec(
"INSERT INTO languages (ui_id,lang,name,signed) 
values (229,'es','Spanish',0);"
);
$db->exec(
"INSERT INTO languages (ui_id,lang,name,signed) 
values (230,'ssp','Spanish Sign Language',1);"
);
$db->exec(
"INSERT INTO languages (ui_id,lang,name,signed) 
values (231,'ar','Arabic',0);"
);
$db->exec(
"INSERT INTO languages (ui_id,lang,name,signed) 
values (232,'sdl','Saudi Abarian Sign Language',1);"
);
$db->exec(
"INSERT INTO languages (ui_id,lang,name,signed) 
values (233,'jos','Jordanian Sign Language',1);"
);
$db->exec(
"INSERT INTO languages (ui_id,lang,name,signed) 
values (234,'fcs','Quebec Sign Language',1);"
);
$db->exec(
"INSERT INTO languages (ui_id,lang,name,signed) 
values (235,'ja','Japanese',0);"
);
$db->exec(
"INSERT INTO languages (ui_id,lang,name,signed) 
values (236,'jsl','Japanese Sign Language',1);"
);
$db->exec(
"INSERT INTO languages (ui_id,lang,name,signed) 
values (237,'eth','Ethiopian Sign Language',1);"
);
$db->exec(
"INSERT INTO languages (ui_id,lang,name,signed) 
values (238,'sfb','French Belgian Sign Language',1);"
);
$db->exec(
"INSERT INTO languages (ui_id,lang,name,signed) 
values (239,'nl','Dutch',0);"
);
$db->exec(
"INSERT INTO languages (ui_id,lang,name,signed) 
values (240,'vgt','Flemish Sign Language',1);"
);
$db->exec(
"INSERT INTO languages (ui_id,lang,name,signed) 
values (241,'sgg','Swiss-German Sign Language',1);"
);
$db->exec(
"INSERT INTO languages (ui_id,lang,name,signed) 
values (242,'pt','Portuguese',0);"
);
$db->exec(
"INSERT INTO languages (ui_id,lang,name,signed) 
values (243,'bzs','Brazilian Sign Language',1);"
);
$db->exec(
"INSERT INTO languages (ui_id,lang,name,signed) 
values (244,'mfs','Mexican Sign Language',1);"
);
$db->exec(
"INSERT INTO languages (ui_id,lang,name,signed) 
values (245,'ncs','Nicaraguan Sign Language',1);"
);
$db->exec(
"INSERT INTO languages (ui_id,lang,name,signed) 
values (246,'psr','Portuguese Sign Language',1);"
);
$db->exec(
"INSERT INTO languages (ui_id,lang,name,signed) 
values (247,'da','Danish',0);"
);
$db->exec(
"INSERT INTO languages (ui_id,lang,name,signed) 
values (248,'dsl','Danish Sign Language',1);"
);
$db->exec(
"INSERT INTO languages (ui_id,lang,name,signed) 
values (249,'hu','Hungarian',0);"
);
$db->exec(
"INSERT INTO languages (ui_id,lang,name,signed) 
values (250,'hsh','Hungarian Sign Language',1);"
);
$db->exec(
"INSERT INTO languages (ui_id,lang,name,signed) 
values (251,'bfi','British Sign Language',1);"
);
$db->exec(
"INSERT INTO languages (ui_id,lang,name,signed) 
values (252,'fsl','French Sign Language',1);"
);
$db->exec(
"INSERT INTO languages (ui_id,lang,name,signed) 
values (253,'tse','Tunisian Sign Language',1);"
);
$db->exec(
"INSERT INTO languages (ui_id,lang,name,signed) 
values (254,'mt','Maltese',0);"
);
$db->exec(
"INSERT INTO languages (ui_id,lang,name,signed) 
values (255,'mdl','Matlese Sign Language',1);"
);
$db->exec(
"INSERT INTO languages (ui_id,lang,name,signed) 
values (256,'ro','Romanian',0);"
);
$db->exec(
"INSERT INTO languages (ui_id,lang,name,signed) 
values (257,'rms','Romanian Sign Language',1);"
);
$db->exec(
"INSERT INTO languages (ui_id,lang,name,signed) 
values (258,'ru','Russian',0);"
);
$db->exec(
"INSERT INTO languages (ui_id,lang,name,signed) 
values (259,'rsl','Russian Sign Language',1);"
);
$db->exec(
"INSERT INTO languages (ui_id,lang,name,signed) 
values (260,'ugy','Uruguayan Sign Language',1);"
);
$db->exec(
"INSERT INTO languages (ui_id,lang,name,signed) 
values (261,'aed','Argentine Sign Language',1);"
);
$db->exec(
"INSERT INTO languages (ui_id,lang,name,signed) 
values (262,'sl','Slovenian',0);"
);
$db->exec(
"INSERT INTO languages (ui_id,lang,name,signed) 
values (263,'ysl','Yugoslavian Sign Language',1);"
);
$db->exec(
"INSERT INTO languages (ui_id,lang,name,signed) 
values (264,'afg','Afghan Sign Language',1);"
);
$db->exec(
"INSERT INTO languages (ui_id,lang,name,signed) 
values (265,'lt','Lithuanian',0);"
);
$db->exec(
"INSERT INTO languages (ui_id,lang,name,signed) 
values (266,'lls','Lithuanian Sign Language',1);"
);
$db->exec(
"INSERT INTO languages (ui_id,lang,name,signed) 
values (267,'lv','Latvian',0);"
);
$db->exec(
"INSERT INTO languages (ui_id,lang,name,signed) 
values (268,'lsl','Latvian Sign Language',1);"
);
$db->exec(
"INSERT INTO languages (ui_id,lang,name,signed) 
values (269,'et','Estonian',0);"
);
$db->exec(
"INSERT INTO languages (ui_id,lang,name,signed) 
values (270,'eso','Estonian Sign Language',1);"
);
$db->exec(
"INSERT INTO languages (ui_id,lang,name,signed) 
values (271,'isr','Israeli Sign Language',1);"
);
$db->exec(
"INSERT INTO languages (ui_id,lang,name,signed) 
values (272,'gsm','Guatemalan Sign Language',1);"
);
$db->exec(
"INSERT INTO languages (ui_id,lang,name,signed) 
values (273,'ht','Haitian',0);"
);
$db->exec(
"INSERT INTO languages (ui_id,lang,name,signed) 
values (274,'pys','Paraguayan Sign Language',1);"
);
$db->exec(
"INSERT INTO languages (ui_id,lang,name,signed) 
values (275,'uk','Ukranian',0);"
);
$db->exec(
"INSERT INTO languages (ui_id,lang,name,signed) 
values (276,'ukl','Ukranian Sign Language',1);"
);
$db->exec(
"INSERT INTO languages (ui_id,lang,name,signed) 
values (277,'is','Icelandic',0);"
);
$db->exec(
"INSERT INTO languages (ui_id,lang,name,signed) 
values (278,'icl','Icelandic Sign Language',1);"
);
$db->exec(
"INSERT INTO languages (ui_id,lang,name,signed) 
values (279,'nsp','Nepalese Sign Language',1);"
);
$db->exec(
"INSERT INTO languages (ui_id,lang,name,signed) 
values (280,'bg','Bulgarian',0);"
);
$db->exec(
"INSERT INTO languages (ui_id,lang,name,signed) 
values (281,'bqn','Bulgarian Sign Language',1);"
);
$db->exec(
"INSERT INTO languages (ui_id,lang,name,signed) 
values (282,'csg','Chilean Sign Language',1);"
);
$db->exec(
"INSERT INTO languages (ui_id,lang,name,signed) 
values (283,'ecs','Ecuadorian Sign Language',1);"
);
$db->exec(
"INSERT INTO languages (ui_id,lang,name,signed) 
values (284,'esn','Salvadoran Sign Language',1);"
);
$db->exec(
"INSERT INTO languages (ui_id,lang,name,signed) 
values (285,'fcs','Quebec Sign Language',1);"
);
$db->exec(
"INSERT INTO languages (ui_id,lang,name,signed) 
values (286,'hds','Honduras Sign Language',1);"
);
$db->exec(
"INSERT INTO languages (ui_id,lang,name,signed) 
values (287,'hds','Austrian Sign Language',1);"
);
$db->exec(
"INSERT INTO languages (ui_id,lang,name,signed) 
values (288,'nsi','Nigerian Sign Language',1);"
);
$db->exec(
"INSERT INTO languages (ui_id,lang,name,signed) 
values (289,'th','Thai',0);"
);
$db->exec(
"INSERT INTO languages (ui_id,lang,name,signed) 
values (290,'tsq','Thai Sign Language',1);"
);
$db->exec(
"INSERT INTO languages (ui_id,lang,name,signed) 
values (291,'asf','Australian Sign Language',1);"
);
$db->exec(
"INSERT INTO languages (ui_id,lang,name,signed) 
values (292,'eo','Esperanto',0);"
);
$db->exec(
"INSERT INTO languages (ui_id,lang,name,signed) 
values (293,'ils','International Sign',1);"
);
$db->exec(
"INSERT INTO languages (ui_id,lang,name,signed) 
values (294,'bvl','Bolivian Sign Language',1);"
);
$db->exec(
"INSERT INTO languages (ui_id,lang,name,signed) 
values (295,'slf','Swiss-Italian Sign Language',1);"
);
$db->exec(
"INSERT INTO languages (ui_id,lang,name,signed) 
values (296,'it','Italian',0);"
);
$db->exec(
"INSERT INTO languages (ui_id,lang,name,signed) 
values (297,'csn','Colombian Sign Language',1);"
);
$db->exec(
"INSERT INTO languages (ui_id,lang,name,signed) 
values (298,'fi','Finnish',0);"
);
$db->exec(
"INSERT INTO languages (ui_id,lang,name,signed) 
values (299,'fse','Finnish Sign Language',1);"
);
$db->exec(
"INSERT INTO languages (ui_id,lang,name,signed) 
values (300,'bfi-IE','North Ireland Sign Language',1);"
);
$db->exec(
"INSERT INTO languages (ui_id,lang,name,signed) 
values (301,'gss','Greek Sign Language',1);"
);
$db->exec(
"INSERT INTO languages (ui_id,lang,name,signed) 
values (302,'isg','Irish Sign Language',1);"
);
$db->exec(
"INSERT INTO languages (ui_id,lang,name,signed) 
values (303,'ise','Italian Sign Language',1);"
);
$db->exec(
"INSERT INTO languages (ui_id,lang,name,signed) 
values (304,'xml','Malaysian Sign Language',1);"
);
$db->exec(
"INSERT INTO languages (ui_id,lang,name,signed) 
values (305,'dse','Dutch Sign Language',1);"
);
$db->exec(
"INSERT INTO languages (ui_id,lang,name,signed) 
values (306,'nzs','New Zealand Sign Language',1);"
);
$db->exec(
"INSERT INTO languages (ui_id,lang,name,signed) 
values (307,'prl','Peruvian Sign Language',1);"
);
$db->exec(
"INSERT INTO languages (ui_id,lang,name,signed) 
values (308,'psp','Philippine Sign Language',1);"
);
$db->exec(
"INSERT INTO languages (ui_id,lang,name,signed) 
values (309,'sv','Swedish',0);"
);
$db->exec(
"INSERT INTO languages (ui_id,lang,name,signed) 
values (310,'swl','Swedish Sign Language',1);"
);
$db->exec(
"INSERT INTO languages (ui_id,lang,name,signed) 
values (311,'tss','Taiwan Sign Language',1);"
);
$db->exec(
"INSERT INTO languages (ui_id,lang,name,signed) 
values (312,'vsl','Venezuelan Sign Language',1);"
);
$db->exec(
"INSERT INTO languages (ui_id,lang,name,signed) 
values (313,'sfs','South African Sign Language',1);"
);
$db->exec(
"INSERT INTO languages (ui_id,lang,name,signed) 
values (314,'ko','Korean',0);"
);
$db->exec(
"INSERT INTO languages (ui_id,lang,name,signed) 
values (315,'kvk','Korean Sign Language',1);"
);
$db->exec(
"INSERT INTO languages (ui_id,lang,name,signed) 
values (316,'xki','Kenyan Sign Language',1);"
);
$db->exec(
"INSERT INTO languages (ui_id,lang,name,signed) 
values (317,'zh','Chinese',0);"
);
$db->exec(
"INSERT INTO languages (ui_id,lang,name,signed) 
values (318,'kvk','Chinese Sign Language',1);"
);
$db->exec(
"INSERT INTO languages (ui_id,lang,name,signed) 
values (319,'esl','Egypt Sign Language',1);"
);
$db->exec(
"INSERT INTO languages (ui_id,lang,name,signed) 
values (320,'ins','Indian Sign Language',1);"
);
$db->exec(
"INSERT INTO languages (ui_id,lang,name,signed) 
values (321,'pks','Pakistan Sign Language',1);"
);
$db->exec(
"INSERT INTO languages (ui_id,lang,name,signed) 
values (322,'svk','Slovakian Sign Language',1);"
);
$db->exec(
"INSERT INTO languages (ui_id,lang,name,signed) 
values (323,'sk','Slovak',0);"
);
$db->exec(
"INSERT INTO languages (ui_id,lang,name,signed) 
values (324,'tsm','Turkish Sign Language',1);"
);
$db->exec(
"INSERT INTO languages (ui_id,lang,name,signed) 
values (325,'bxy','Bengali Sign Language',1);"
);

$db->exec(
"CREATE TABLE IF NOT EXISTS `securities` (
  `id` int UNSIGNED,
  `ui_id` int UNSIGNED,
  `name` varchar(50),
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;"
);

$db->exec(
"INSERT INTO securities (id,ui_id,name) values (1,87,'view');"
);
$db->exec(
"INSERT INTO securities (id,ui_id,name) values (2,88,'add');"
);
$db->exec(
"INSERT INTO securities (id,ui_id,name) values (3,89,'edit');"
);
$db->exec(
"INSERT INTO securities (id,ui_id,name) values (4,90,'manage');"
);
$db->exec(
"INSERT INTO securities (id,ui_id,name) values (5,91,'admin');"
);


$db->exec(
"CREATE TABLE IF NOT EXISTS `users` (
  `id` int UNSIGNED AUTO_INCREMENT,
  `security` int unsigned,
  `name` varchar(255),
  `display` varchar(255),
  `email` varchar(255),
  `password` varchar(255),
  `statement` TEXT,
  `public` bool,
  `sign_lang` varchar(10),
  `spoken_lang` varchar(10),
  `second_lang` varchar(10),
  `created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`),
  INDEX (`name`,`display`,`sign_lang`,`spoken_lang`,`second_lang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;"
);

$db->exec(
"CREATE TABLE IF NOT EXISTS `puddles` (
  `id` int UNSIGNED AUTO_INCREMENT,
  `namespace` varchar(4),
  `ns_id` int UNSIGNED,
  `sign_lang` varchar(10),
  `spoken_lang` varchar(10),
  `second_lang` varchar(10),
  `ui` int UNSIGNED,
  `view_security` BOOL,
  `add_security` BOOL,
  `edit_security` BOOL,
  `register_level` INT UNSIGNED,
  `upload_level` INT UNSIGNED,
  `created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`),
  INDEX (`namespace`,`ns_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;"
);

$db->exec(
"CREATE TABLE IF NOT EXISTS `puddle_users` (
  `id` int UNSIGNED AUTO_INCREMENT,
  `puddle_id` int UNSIGNED,
  `user_id` int UNSIGNED,
  `security` int UNSIGNED,
  PRIMARY KEY  (`id`),
  INDEX (`puddle_id`,`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;"
);

$db->exec(
"INSERT INTO puddles (id) values (1);"
);
$db->exec(
"INSERT INTO puddles (id,namespace) values (2,'ui');"
);
$db->exec(
"INSERT INTO puddles (id,namespace) values (3,'sgn');"
);


$db->exec(
"CREATE TABLE IF NOT EXISTS `partsofspeech` (
  `id` int UNSIGNED,
  `ui_id` int UNSIGNED,
  `name` varchar(50),
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;"
);

$db->exec(
"INSERT INTO partsofspeech (id,ui_id,name) values (1,199,'noun');"
);
$db->exec(
"INSERT INTO partsofspeech (id,ui_id,name) values (2,200,'verb');"
);
$db->exec(
"INSERT INTO partsofspeech (id,ui_id,name) values (3,201,'adjective');"
);
$db->exec(
"INSERT INTO partsofspeech (id,ui_id,name) values (4,202,'adverb');"
);
$db->exec(
"INSERT INTO partsofspeech (id,ui_id,name) values (5,203,'sentence');"
);
$db->exec(
"INSERT INTO partsofspeech (id,ui_id,name) values (6,204,'other');"
);

$db->exec(
"CREATE TABLE IF NOT EXISTS `status` (
  `id` int UNSIGNED,
  `ui_id` int UNSIGNED,
  `name` varchar(50),
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;"
);

$db->exec(
"INSERT INTO status (id,ui_id,name) values (1,205,'provisional');"
);
$db->exec(
"INSERT INTO status (id,ui_id,name) values (2,206,'approved');"
);
$db->exec(
"INSERT INTO status (id,ui_id,name) values (3,207,'nonstandard');"
);
$db->exec(
"INSERT INTO status (id,ui_id,name) values (4,208,'rejected');"
);

$db->exec(
"CREATE TABLE IF NOT EXISTS `entries` (
  `id` int UNSIGNED AUTO_INCREMENT,
  `puddle_id` int UNSIGNED,
  `sub_id` int UNSIGNED,
  `status_id` int UNSIGNED,
  `partofspeech_id` int UNSIGNED,
  `partofspeech` varchar(100),
  `source` varchar(500),
  `user` varchar(40),
  `created` DATETIME DEFAULT NULL,
  `modified` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`),
  INDEX (`puddle_id`,`sub_id`,`partofspeech_id`,`status_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;"
);
$db->exec(
"INSERT INTO entries (id,source) values (1,'namespaces config');"
);
$db->exec(
"INSERT INTO entries (id,puddle_id,source) values (2,1,'ui');"
);
$db->exec(
"INSERT INTO entries (id,puddle_id,source) values (3,1,'sgn');"
);

$db->exec(
"CREATE TABLE IF NOT EXISTS `terms` (
  `id` int UNSIGNED AUTO_INCREMENT,
  `entry_id` int UNSIGNED,
  `text` varchar(500),
  `lang` varchar(10),
  PRIMARY KEY  (`id`),
  INDEX (`entry_id`,`text`,`lang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;"
);

$db->exec(
"INSERT INTO terms (id,entry_id,text,lang) values (1,1,'Namespace config','en');"
);
$db->exec(
"INSERT INTO terms (id,entry_id,text,lang) values (2,2,'User Interfaces','en');"
);
$db->exec(
"INSERT INTO terms (id,entry_id,text,lang) values (3,3,'General sign languages','en');"
);

$db->exec(
"CREATE TABLE IF NOT EXISTS `texts` (
  `id` int UNSIGNED AUTO_INCREMENT,
  `entry_id` int UNSIGNED,
  `text` TEXT,
  `lang` varchar(10),
  PRIMARY KEY  (`id`),
  INDEX (`entry_id`,`lang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;"
);

$db->exec(
"CREATE TABLE IF NOT EXISTS `images` (
  `id` int UNSIGNED AUTO_INCREMENT,
  `entry_id` int UNSIGNED,
  `image` BLOB,
  `filename` varchar(255),
  `filesize` int,
  PRIMARY KEY  (`id`),
  INDEX (`entry_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;"
);

//video type: definition, illustration, demonstration

$db->exec(
"CREATE TABLE IF NOT EXISTS `videos` (
  `id` int UNSIGNED AUTO_INCREMENT,
  `entry_id` int UNSIGNED,
  `video` TEXT,
  PRIMARY KEY  (`id`),
  INDEX (`entry_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;"
);


?>
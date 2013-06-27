<?php
include 'db.php';
$db->exec(
"CREATE TABLE IF NOT EXISTS `signs` (
  `id` int UNSIGNED AUTO_INCREMENT,
  `text` varchar(500),
  `lang` varchar(3),
  `created` DATETIME DEFAULT NULL,
  `modified` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`),
  INDEX (`text`,`lang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;"
);
$db->exec(
"CREATE TABLE IF NOT EXISTS `terms` (
  `id` int UNSIGNED AUTO_INCREMENT,
  `text` varchar(500),
  `lang` varchar(3),
  `created` DATETIME DEFAULT NULL,
  `modified` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`),
  INDEX (`text`,`lang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;"
);
$db->exec(
"CREATE TABLE IF NOT EXISTS `links` (
  `id` int UNSIGNED AUTO_INCREMENT,
  `sign_id` int,
  `term_id` int,
  PRIMARY KEY  (`id`),
  INDEX (`sign_id`,`term_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;"
);
?>
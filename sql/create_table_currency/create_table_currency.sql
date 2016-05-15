CREATE TABLE IF NOT EXISTS `santeh_currency` (
  `id` int(5) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ключ',
  `name` varchar(20) NOT NULL COMMENT 'название например USD',
  `nick` varchar(20) DEFAULT NULL COMMENT 'ник например $',
  `rate` float unsigned DEFAULT '1' COMMENT 'текущий курс',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251 COMMENT='курсы валют' AUTO_INCREMENT=1 ;

INSERT INTO `santeh_currency` VALUES
  (null, 'USD', '$', 25),
  (null, 'EUR', 'E', 28),
  (null, 'RUB', 'R', 0.37)
;
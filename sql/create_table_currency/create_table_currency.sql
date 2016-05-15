CREATE TABLE IF NOT EXISTS `santeh_currency` (
  `id` int(5) unsigned NOT NULL AUTO_INCREMENT COMMENT '����',
  `name` varchar(20) NOT NULL COMMENT '�������� �������� USD',
  `nick` varchar(20) DEFAULT NULL COMMENT '��� �������� $',
  `rate` float unsigned DEFAULT '1' COMMENT '������� ����',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251 COMMENT='����� �����' AUTO_INCREMENT=1 ;

INSERT INTO `santeh_currency` VALUES
  (null, 'USD', '$', 25),
  (null, 'EUR', 'E', 28),
  (null, 'RUB', 'R', 0.37)
;
##### Струаткру базы MYSQL ######
CREATE TABLE IF NOT EXISTS `cmdb_hosts` (
  `host_id` int(11) NOT NULL AUTO_INCREMENT,
  `host` varchar(100) DEFAULT NULL,
  `dns` varchar(100) DEFAULT NULL,
  `ip` varchar(15) DEFAULT NULL,
  PRIMARY KEY (`hostid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `cmdb_fields` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `front` double NOT NULL,
  `name` varchar(50) NOT NULL,
  `type_id` smallint(5) NOT NULL,
  `sort` smallint(6) NOT NULL DEFAULT '999',
  `num` int(11) NOT NULL DEFAULT '0',
  KEY `id` (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=38 ;

CREATE TABLE IF NOT EXISTS `cmdb_hint` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `field_id` int(11) NOT NULL,
  `value` varchar(250) NOT NULL,
  `sort` smallint(6) NOT NULL DEFAULT '0',
  KEY `id` (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=91 ;

CREATE TABLE IF NOT EXISTS `cmdb_values` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `field_id` int(5) NOT NULL,
  `host_id` int(5) NOT NULL,
  `count` varchar(256) NOT NULL,
  `num` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1138 ;
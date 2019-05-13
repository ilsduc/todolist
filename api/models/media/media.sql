drop table if exists media;
CREATE TABLE `media` (
  `id` varchar(40),
  `name` varchar(255),
  `path` varchar(255),
  `url` varchar(255),
  PRIMARY KEY (`id`),
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

CREATE TABLE `comment` (
  `Signature` varchar(50) COLLATE utf8_bin NOT NULL,
  `Content` text COLLATE utf8_bin NOT NULL,
  `Date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `post` smallint(6) NOT NULL,
  PRIMARY KEY (`post`,`Date`),
  CONSTRAINT `comment_ibfk_1` FOREIGN KEY (`post`) REFERENCES `post` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

INSERT INTO `comment` (`Signature`, `Content`, `Date`, `post`) VALUES
('Anon',  'Anon troll #1',  '2015-12-01 13:24:55',  2),
('Anon noob', 'aksda',  '2015-12-01 13:25:16',  2),
('Anon #3', 'torloroorlroorl',  '2015-12-01 13:25:55',  2),
('hjkl',  'lkjh', '2015-12-01 13:49:46',  4),
('fsdajgfd',  'hfdsgfdsgfds', '2015-12-01 13:49:54',  5);

CREATE TABLE `post` (
  `id` smallint(6) NOT NULL AUTO_INCREMENT,
  `Title` varchar(40) COLLATE utf8_bin NOT NULL,
  `Content` text COLLATE utf8_bin NOT NULL,
  `Timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `Author` smallint(6) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `Author` (`Author`),
  CONSTRAINT `post_ibfk_1` FOREIGN KEY (`Author`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;


CREATE TABLE `Tag` (
  `Name` varchar(30) COLLATE utf8_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;


CREATE TABLE `user` (
  `id` smallint(6) NOT NULL AUTO_INCREMENT,
  `Username` varchar(30) COLLATE utf8_bin NOT NULL,
  `password` varchar(50) COLLATE utf8_bin NOT NULL,
  `Rank` varchar(50) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
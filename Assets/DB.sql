SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

-- DROP TABLE IF EXISTS `comment`;
CREATE TABLE `comment` (
  `Signature` varchar(50) COLLATE utf8_bin NOT NULL,
  `Content` text COLLATE utf8_bin NOT NULL,
  `Date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `post` smallint(6) NOT NULL,
  PRIMARY KEY (`post`,`Date`),
  CONSTRAINT `comment_ibfk_1` FOREIGN KEY (`post`) REFERENCES `post` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;


-- DROP TABLE IF EXISTS `post`;
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


-- DROP TABLE IF EXISTS `Tag`;
CREATE TABLE `Tag` (
  `Name` varchar(30) COLLATE utf8_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;


-- DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `id` smallint(6) NOT NULL AUTO_INCREMENT,
  `Username` varchar(30) COLLATE utf8_bin NOT NULL,
  `password` varchar(50) COLLATE utf8_bin NOT NULL,
  `Rank` varchar(50) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

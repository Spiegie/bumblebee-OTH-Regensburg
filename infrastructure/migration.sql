-- Adminer 4.8.1 MySQL 11.5.2-MariaDB-ubu2404 dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

SET NAMES utf8mb4;

CREATE DATABASE `bumblebee` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */;
USE `bumblebee`;

DROP TABLE IF EXISTS `new_bookings`;
CREATE TABLE `new_bookings` (
  `id` int(12) unsigned NOT NULL AUTO_INCREMENT,
  `instruments` int(8) unsigned NOT NULL,
  `bookwhen` datetime NOT NULL,
  `date` date NOT NULL,
  `startTime` time NOT NULL,
  `endTime` time NOT NULL,
  `bookedbyid` int(8) unsigned NOT NULL,
  `comments` text DEFAULT NULL,
  `log` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_uca1400_ai_ci;


DROP TABLE IF EXISTS `new_groups`;
CREATE TABLE `new_groups` (
  `id` int(8) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `supervisor` int(8) unsigned NOT NULL,
  `webaddress` varchar(255) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_uca1400_ai_ci;


DROP TABLE IF EXISTS `new_instruments`;
CREATE TABLE `new_instruments` (
  `id` int(8) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `usualclose` time DEFAULT NULL,
  `usualopen` time DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_uca1400_ai_ci;


DROP TABLE IF EXISTS `new_members`;
CREATE TABLE `new_members` (
  `userid` int(8) unsigned NOT NULL,
  `groupid` int(8) unsigned NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_uca1400_ai_ci;


DROP TABLE IF EXISTS `new_permissions`;
CREATE TABLE `new_permissions` (
  `userid` int(8) unsigned NOT NULL,
  `instrumentid` int(8) unsigned NOT NULL,
  UNIQUE KEY `userid_instrumentid` (`userid`,`instrumentid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_uca1400_ai_ci;


DROP TABLE IF EXISTS `new_supervisors`;
CREATE TABLE `new_supervisors` (
  `instrumentid` int(8) unsigned NOT NULL,
  `userid` int(8) unsigned NOT NULL,
  UNIQUE KEY `instrumentid_userid` (`instrumentid`,`userid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_uca1400_ai_ci;


DROP TABLE IF EXISTS `new_users`;
CREATE TABLE `new_users` (
  `id` int(8) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `nds` varchar(127) NOT NULL,
  `passwd` varchar(127) NOT NULL,
  `isAdmin` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `email` varchar(255) DEFAULT NULL,
  `suspended` tinyint(1) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nds` (`nds`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_uca1400_ai_ci;


-- 2024-11-02 10:14:09

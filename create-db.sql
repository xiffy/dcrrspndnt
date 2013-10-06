-- --------------------------------------------------------
-- Host:                         192.168.200.8
-- Server versie:                5.5.34-log - MySQL Community Server (GPL) by Remi
-- Server OS:                    Linux
-- HeidiSQL Versie:              8.0.0.4396
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- Structuur van  tabel decorrespondent.app_keys wordt geschreven
CREATE TABLE IF NOT EXISTS `app_keys` (
  `ID` int(10) NOT NULL AUTO_INCREMENT,
  `app_key` varchar(40) NOT NULL DEFAULT '',
  `app_value` mediumtext NOT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `app_key` (`app_key`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Data exporteren was gedeselecteerd


-- Structuur van  tabel decorrespondent.artikelen wordt geschreven
CREATE TABLE IF NOT EXISTS `artikelen` (
  `ID` int(20) NOT NULL AUTO_INCREMENT,
  `clean_url` varchar(255) NOT NULL DEFAULT '',
  `t_co` varchar(255) NOT NULL DEFAULT '',
  `share_url` varchar(255) NOT NULL DEFAULT '',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `og` mediumtext,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `clean_url` (`clean_url`),
  UNIQUE KEY `t_co` (`t_co`),
  KEY `created_at` (`created_at`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Data exporteren was gedeselecteerd


-- Structuur van  tabel decorrespondent.meta wordt geschreven
CREATE TABLE IF NOT EXISTS `meta` (
  `ID` int(10) NOT NULL AUTO_INCREMENT,
  `waarde` varchar(255) NOT NULL DEFAULT '',
  `type` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`ID`),
  KEY `waarde` (`waarde`),
  KEY `type` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Data exporteren was gedeselecteerd


-- Structuur van  tabel decorrespondent.meta_artikel wordt geschreven
CREATE TABLE IF NOT EXISTS `meta_artikel` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `art_id` bigint(20) DEFAULT '0',
  `meta_id` bigint(20) DEFAULT '0',
  PRIMARY KEY (`ID`),
  KEY `artikel` (`art_id`),
  KEY `meta` (`meta_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Data exporteren was gedeselecteerd
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;

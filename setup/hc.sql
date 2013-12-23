-- phpMyAdmin SQL Dump
-- version 4.0.2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Dec 23, 2013 at 02:27 AM
-- Server version: 5.5.31-0+wheezy1
-- PHP Version: 5.4.4-14+deb7u5

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `hc`
--
CREATE DATABASE IF NOT EXISTS `hc` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `hc`;

-- --------------------------------------------------------

--
-- Table structure for table `accounts`
--

CREATE TABLE IF NOT EXISTS `accounts` (
  `a_key` int(11) NOT NULL AUTO_INCREMENT,
  `a_username` varchar(64) NOT NULL,
  `a_password` varchar(64) NOT NULL,
  `a_role` varchar(1) NOT NULL DEFAULT 'U',
  `a_active` varchar(1) NOT NULL DEFAULT 'Y',
  PRIMARY KEY (`a_key`),
  UNIQUE KEY `a_username` (`a_username`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Table structure for table `devices`
--

CREATE TABLE IF NOT EXISTS `devices` (
  `d_key` int(11) NOT NULL AUTO_INCREMENT,
  `d_bus` varchar(6) NOT NULL,
  `d_type` varchar(32) NOT NULL,
  `d_room` varchar(32) NOT NULL,
  `d_name` varchar(32) NOT NULL,
  `d_id` varchar(32) NOT NULL,
  `d_visible` varchar(1) DEFAULT 'Y',
  `d_state` varchar(32) NOT NULL,
  `d_auto` varchar(1) NOT NULL DEFAULT 'A',
  `d_config` longtext,
  `d_alias` varchar(32) DEFAULT NULL,
  `d_statustext` varchar(128) DEFAULT NULL,
  `d_statuschanged` int(11) DEFAULT NULL,
  PRIMARY KEY (`d_key`),
  KEY `d_bus` (`d_bus`),
  KEY `d_type` (`d_type`),
  KEY `d_alias` (`d_alias`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2050 ;

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE IF NOT EXISTS `events` (
  `e_key` int(11) NOT NULL AUTO_INCREMENT,
  `e_type` varchar(1) NOT NULL DEFAULT 'C',
  `e_address` varchar(200) NOT NULL,
  `e_address_rev` varchar(200) DEFAULT NULL,
  `e_code` longtext NOT NULL,
  `e_lastcalled` int(11) DEFAULT '0',
  `e_order` int(11) DEFAULT '0',
  `e_cooldown` int(11) NOT NULL DEFAULT '2',
  PRIMARY KEY (`e_key`),
  KEY `e_type` (`e_type`),
  KEY `e_address` (`e_address`),
  KEY `e_lastcalled` (`e_lastcalled`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=19 ;

-- --------------------------------------------------------

--
-- Table structure for table `groups`
--

CREATE TABLE IF NOT EXISTS `groups` (
  `g_key` int(11) NOT NULL AUTO_INCREMENT,
  `g_name` varchar(32) NOT NULL,
  `g_states` varchar(200) NOT NULL DEFAULT 'off,on',
  `g_deviceconfig` longtext NOT NULL,
  PRIMARY KEY (`g_key`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE IF NOT EXISTS `messages` (
  `m_key` bigint(20) NOT NULL AUTO_INCREMENT,
  `m_type` varchar(16) NOT NULL,
  `m_time` bigint(20) NOT NULL,
  `m_text` longtext,
  `m_data` longtext,
  PRIMARY KEY (`m_key`),
  KEY `m_time` (`m_time`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=47 ;

-- --------------------------------------------------------

--
-- Table structure for table `stateinfo`
--

CREATE TABLE IF NOT EXISTS `stateinfo` (
  `si_bus` varchar(8) NOT NULL,
  `si_name` varchar(32) NOT NULL,
  `si_param` varchar(32) NOT NULL,
  `si_mode` varchar(2) NOT NULL,
  `si_devicekey` int(11) NOT NULL,
  `si_value` varchar(16) NOT NULL,
  `si_time` int(11) NOT NULL,
  `si_by` varchar(8) NOT NULL,
  `si_event` varchar(64) NOT NULL,
  `si_ip` varchar(32) NOT NULL,
  `si_uid` int(11) NOT NULL,
  PRIMARY KEY (`si_bus`,`si_name`,`si_param`,`si_mode`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

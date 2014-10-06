-- phpMyAdmin SQL Dump
-- version 4.0.2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Oct 06, 2014 at 12:03 PM
-- Server version: 5.5.37-0+wheezy1
-- PHP Version: 5.4.4-14+deb7u12

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

--
-- Dumping data for table `accounts`
--

INSERT INTO `accounts` (`a_key`, `a_username`, `a_password`, `a_role`, `a_active`) VALUES
(1, 'udo.schroeter@gmail.com', 'dd2a154125927d3c75358f29eeef99c5db82b31b', 'A', 'Y');

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
  `d_icon` varchar(32) DEFAULT NULL,
  `d_config` longtext,
  `d_alias` varchar(32) DEFAULT NULL,
  `d_statustext` varchar(128) DEFAULT NULL,
  `d_statuschanged` int(11) DEFAULT NULL,
  `d_priority` int(11) NOT NULL DEFAULT '50',
  PRIMARY KEY (`d_key`),
  KEY `d_bus` (`d_bus`),
  KEY `d_type` (`d_type`),
  KEY `d_alias` (`d_alias`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2069 ;

--
-- Dumping data for table `devices`
--

INSERT INTO `devices` (`d_key`, `d_bus`, `d_type`, `d_room`, `d_name`, `d_id`, `d_visible`, `d_state`, `d_auto`, `d_icon`, `d_config`, `d_alias`, `d_statustext`, `d_statuschanged`, `d_priority`) VALUES
(2002, 'HE', 'Light', 'Office', 'Corner Lights', '2002', 'Y', '0', '', '', '', 'OfficeLamp', '#22 MODE-NIGHT-ON', 1412555296, 50),
(2005, 'HE', 'Light', 'Living Room', 'Ceiling Lights', '2005', 'Y', '0', '', '', '', 'LivingRoomCeiling', 'UI', 1412470295, 50),
(2006, 'HE', 'Light', 'Hallway', 'Cupboard', '2006', 'Y', '1', '', '', '', 'CupboardLight', '#21 MODE-MINIMAL-ON', 1412594883, 50),
(2017, 'HE', 'Light', 'Other', 'Guest Nightstand', '2017', 'Y', '0', '', '', '', 'GuestNightstand', 'UI', 1412594906, 50),
(2018, 'HM', 'Light', 'Hallway', 'Hallway', 'JEQ0738696:1', 'Y', '0', '', '', '', 'HallwayLight', 'UI', 1412559206, 50),
(2019, 'HM', 'Blinds', 'Living Room', 'Right Wind', 'JEQ0259329:1', 'Y', '0', '', '', '', 'LivingRoomBlindsRight', '#7 SUNRISE-30', 1412572093, 50),
(2024, 'HM', 'Blinds', 'Office', 'Door', 'JRT0002934:1', 'Y', '0', '', '', '', 'OfficeDoorBlinds', '#7 SUNRISE-30', 1412572093, 50),
(2025, 'HM', 'Light', 'Hallway', 'Porch', 'JEQ0738696:2', 'Y', '0', '', '', '', 'PorchLight', 'UI', 1412594910, 50),
(2026, 'HM', 'Light', 'unknown', 'Switch 2013-11-29 16:01:27', 'JEQ0738696:3', 'N', '0', '', '', '', '', '#21 MODE-MINIMAL-ONLY', 1412533610, 50),
(2027, 'HM', 'Light', 'unknown', 'Switch 2013-11-29 16:01:27', 'JEQ0738696:4', 'N', '0', '', '', '', '', '#21 MODE-MINIMAL-ONLY', 1412533610, 50),
(2028, 'HM', 'Blinds', 'Office', 'Window', 'JRT0003197:1', 'Y', '0', '', '', '', 'OfficeWindowBlinds', '#7 SUNRISE-30', 1412572093, 50),
(2029, 'HM', 'Key', 'Hallway', 'Hallway Button', 'KEQ0180768:1', 'N', '0', '', '', '', 'HallwayButton1', '#21 MODE-MINIMAL-ONLY', 1412541954, 50),
(2030, 'HM', 'Key', 'Hallway', 'Hallway Button', 'KEQ0180768:2', 'N', '0', '', '', '', 'HallwayButton2', '#21 MODE-MINIMAL-ONLY', 1412541954, 50),
(2031, 'HM', 'Key', 'Hallway', 'Hallway Button', 'KEQ0180768:3', 'N', '0', '', '', '', '', '#21 MODE-MINIMAL-ONLY', 1412541954, 50),
(2032, 'HM', 'Key', 'Hallway', 'Hallway Button', 'KEQ0180768:4', 'N', '0', '', '', '', '', '#21 MODE-MINIMAL-ONLY', 1412541955, 50),
(2033, 'HM', 'Key', 'Hallway', 'Hallway Button', 'KEQ0180768:5', 'N', '0', '', '', '', '', '#21 MODE-MINIMAL-ONLY', 1412541955, 50),
(2034, 'HM', 'Key', 'Hallway', 'Hallway Button', 'KEQ0180768:6', 'N', '0', '', '', '', '', '#21 MODE-MINIMAL-ONLY', 1412541955, 50),
(2035, 'HM', 'HM-Sec-MDIR', 'unknown', 'New HM-Sec-MDIR 2013-12-05 14:01', 'JEQ0155347', 'N', '0', '', '', '', '', '#21 MODE-MINIMAL-ONLY', 1412541955, 50),
(2036, 'HM', 'MAINTENANCE', 'unknown', 'New MAINTENANCE 2013-12-05 14:01', 'JEQ0155347:0', 'N', '0', '', '', '', '', '#21 MODE-MINIMAL-ONLY', 1412541955, 50),
(2037, 'HM', 'MOTION_DETECTOR', 'Hallway', 'Motion Det.1', 'JEQ0155347:1', 'Y', '1', '', '', '', 'HallwayMotion', '#21 MODE-MINIMAL-ON', 1412594883, 50),
(2038, 'HM', 'ZEL STG RM FEP 230V', 'unknown', 'New ZEL STG RM FEP 230V 2013-12-', 'JRT0003197', 'N', '0', '', '', '', '', '#21 MODE-MINIMAL-ONLY', 1412541955, 50),
(2039, 'HM', 'MAINTENANCE', 'unknown', 'New MAINTENANCE 2013-12-05 14:01', 'JRT0003197:0', 'N', '0', '', '', '', '', '#21 MODE-MINIMAL-ONLY', 1412541955, 50),
(2040, 'HM', 'ZEL STG RM FEP 230V', 'unknown', 'New ZEL STG RM FEP 230V 2013-12-', 'JRT0002934', 'N', '0', '', '', '', '', '#21 MODE-MINIMAL-ONLY', 1412541955, 50),
(2041, 'HM', 'MAINTENANCE', 'unknown', 'New MAINTENANCE 2013-12-05 14:01', 'JRT0002934:0', 'N', '0', '', '', '', '', '#21 MODE-MINIMAL-ONLY', 1412541955, 50),
(2042, 'HM', 'HM-LC-Bl1-FM', 'unknown', 'New HM-LC-Bl1-FM 2013-12-05 14:0', 'JEQ0259329', 'N', '0', '', '', '', '', '#21 MODE-MINIMAL-ONLY', 1412541956, 50),
(2043, 'HM', 'MAINTENANCE', 'unknown', 'New MAINTENANCE 2013-12-05 14:01', 'JEQ0259329:0', 'N', '0', '', '', '', '', '#21 MODE-MINIMAL-ONLY', 1412541956, 50),
(2044, 'HM', 'HM-PB-6-WM55', 'Hallway', 'Remote Button', 'KEQ0180768', 'N', '0', '', '', '', '', '#21 MODE-MINIMAL-ONLY', 1412541956, 50),
(2045, 'HM', 'MAINTENANCE', 'Hallway', 'Remote Button', 'KEQ0180768:0', 'N', '0', '', '', '', '', '#21 MODE-MINIMAL-ONLY', 1412541956, 50),
(2046, 'HM', 'HM-LC-Sw4-DR', 'unknown', 'New HM-LC-Sw4-DR 2013-12-05 14:0', 'JEQ0738696', 'N', '0', '', '', '', '', '#21 MODE-MINIMAL-ONLY', 1412541956, 50),
(2047, 'HM', 'MAINTENANCE', 'unknown', 'New MAINTENANCE 2013-12-05 14:01', 'JEQ0738696:0', 'N', '0', '', '', '', '', '#21 MODE-MINIMAL-ONLY', 1412541956, 50),
(2049, 'GPIO', 'Blinds', 'Living Room', 'Main Blinds', '15:13:16', 'Y', 'open', '', '', '', 'LivingRoomBlindsMain', '#7 SUNRISE-30', 1412572094, 50),
(2050, 'HM', 'HM-LC-Bl1-FM', 'unknown', 'New HM-LC-Bl1-FM 2014-01-10 14:3', 'JEQ0302533', 'N', '0', '', '', '', '', '#21 MODE-MINIMAL-ONLY', 1412541957, 50),
(2051, 'HM', 'MAINTENANCE', 'unknown', 'New MAINTENANCE 2014-01-10 14:34', 'JEQ0302533:0', 'N', '0', '', '', '', '', '#21 MODE-MINIMAL-ONLY', 1412541957, 50),
(2052, 'HM', 'Blinds', 'Bedroom', 'Window', 'JEQ0302533:1', 'Y', '0', '', '', '', 'BedroomBlinds', '#22 MODE-NIGHT-ON', 1412555297, 50),
(2053, 'HE', 'Light', 'Bedroom', 'Nightstand', '2001', 'Y', '0', '', '', '', 'BedroomNightstand', 'UI', 1412594907, 50),
(2054, 'HM', 'HM-LC-Sw1-FM', 'unknown', 'New HM-LC-Sw1-FM 2014-07-23 17:0', 'KEQ0632488', 'N', '0', '', '', '', '', '#21 MODE-MINIMAL-ONLY', 1412541957, 50),
(2055, 'HM', 'MAINTENANCE', 'unknown', 'New MAINTENANCE 2014-07-23 17:02', 'KEQ0632488:0', 'N', '0', '', '', '', '', '#21 MODE-MINIMAL-ONLY', 1412541957, 50),
(2056, 'HM', 'Light', 'Hallway', 'Cellar', 'KEQ0632488:1', 'Y', '0', '', '', '{"timer_STATE_1":{"seconds":600,"value":0}}', 'CellarLight', 'API', 1412593272, 50),
(2065, 'HE', 'Light', 'Living Room', 'Bluetooth Audio', '2014', 'Y', '0', '', 'microphone', '', 'LRAudio', 'UI', 1411440632, 50),
(2064, 'HE', 'Light', 'Other', 'Attic', '2016', 'Y', '0', '', '', '', '', 'UI', 1411386195, 50),
(2057, 'HM', 'HM-TC-IT-WM-W-EU', 'Living Room', 'Sensor', 'LEQ0417192', 'Y', '0', '', '', '', '', '#21 MODE-MINIMAL-ONLY', 1412541957, 50),
(2058, 'HM', 'MAINTENANCE', 'unknown', 'New MAINTENANCE 2014-08-06 22:12', 'LEQ0417192:0', 'Y', '0', '', '', '', '', '#21 MODE-MINIMAL-ONLY', 1412541957, 50),
(2059, 'HM', 'WEATHER_TRANSMIT', 'unknown', 'New WEATHER_TRANSMIT 2014-08-06 ', 'LEQ0417192:1', 'Y', '0', '', '', '', '', '#21 MODE-MINIMAL-ONLY', 1412541958, 50),
(2060, 'HM', 'THERMALCONTROL_TRANSMIT', 'unknown', 'New THERMALCONTROL_TRANSMIT 2014', 'LEQ0417192:2', 'Y', '0', '', '', '', '', '#21 MODE-MINIMAL-ONLY', 1412541958, 50),
(2061, 'HM', 'WINDOW_SWITCH_RECEIVER', 'unknown', 'New WINDOW_SWITCH_RECEIVER 2014-', 'LEQ0417192:3', 'Y', '0', '', '', '', '', '#21 MODE-MINIMAL-ONLY', 1412541958, 50),
(2062, 'HM', 'REMOTECONTROL_RECEIVER', 'unknown', 'New REMOTECONTROL_RECEIVER 2014-', 'LEQ0417192:6', 'Y', '0', '', '', '', '', '#21 MODE-MINIMAL-ONLY', 1412541958, 50),
(2063, 'HM', 'SWITCH_TRANSMIT', 'unknown', 'New SWITCH_TRANSMIT 2014-08-06 2', 'LEQ0417192:7', 'Y', '0', '', '', '', '', '#21 MODE-MINIMAL-ONLY', 1412541958, 50),
(2066, 'HM', 'ZEL STG RM FEP 230V', 'unknown', 'New ZEL STG RM FEP 230V 2014-08-', 'JRT0003099', 'Y', '0', '', '', '', '', '#21 MODE-MINIMAL-ONLY', 1412541958, 50),
(2067, 'HM', 'MAINTENANCE', 'unknown', 'New MAINTENANCE 2014-08-07 20:19', 'JRT0003099:0', 'Y', '0', '', '', '', '', '#21 MODE-MINIMAL-ONLY', 1412541958, 50),
(2068, 'HM', 'Blinds', 'Other', 'Bathroom', 'JRT0003099:1', 'Y', '0', '', '', '', 'BathroomBlinds', '#7 SUNRISE-30', 1412572093, 50);

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=28 ;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`e_key`, `e_type`, `e_address`, `e_address_rev`, `e_code`, `e_lastcalled`, `e_order`, `e_cooldown`) VALUES
(18, 'C', 'HM-JEQ0155347:1-NEXT_TRANSMISSION', 'HM-JEQ0155347:1-MOTION', ':CellarLight:STATE:1', 1412596036, 0, 2),
(1, 'C', 'HM-KEQ0180768:1-PRESSED', 'HM-KEQ0180768:2-PRESSED', ':LivingRoomCeiling:STATE:0:1\r\n:LRAudio:STATE:0', 1412380568, 0, 2),
(3, 'C', 'HM-KEQ0180768:3-PRESSED', 'HM-KEQ0180768:4-PRESSED', ':HallwayLight:STATE:0:1', 1412300184, 0, 2),
(17, 'C', 'HM-KEQ0180768:5-PRESSED', 'HM-KEQ0180768:6-PRESSED', ':HAL:LivingRoomBlindsMain:open:closed', 1412417191, 0, 2),
(24, 'C', 'MODE-ALARM-ON', 'MODE-ALARM-OFF', 'ACTION?/SELECT:TYPE=HM-Blinds/SET:Level:0\r\nACTION?/SELECT:TYPE=Light/SET:STATE:1\r\n', 1412530631, 0, 2),
(20, 'C', 'MODE-AWAY-ON', 'MODE-AWAY-OFF', 'NIGHT?/CALL:MODE-MINIMAL-ONLY\r\nSELECT:GROUP!=minimal/AUTO:M:A\r\n#', 1412552077, 0, 2),
(23, 'C', 'MODE-LOCKDOWN-ON', 'MODE-LOCKDOWN-OFF', 'ACTION?/SELECT:TYPE=HM-Blinds/SET:Level:0.5\r\nACTION?/SELECT:TYPE=Light/SET:STATE:1\r\n', 1412530631, 0, 2),
(21, 'C', 'MODE-MINIMAL-ON', 'MODE-MINIMAL-ONLY', 'SELECT:GROUP=minimal/SET:STATE:1:1\r\nREV?/SELECT:GROUP!=minimal/REMOVE:TYPE!=Light/SET:STATE:0', 1412594883, 0, 2),
(22, 'C', 'MODE-NIGHT-ON', 'MODE-NIGHT-OFF', ':PorchLight:STATE:1\r\nACTION?/SELECT:OTHER/REMOVE:TYPE!=Light/SET:STATE:0\r\n:BedroomBlinds:LEVEL:0', 1412594459, 0, 2),
(26, 'C', 'MODE-NO AUTO-ON', 'MODE-NO AUTO-OFF', 'SELECT:ALL/AUTO:M:A', 1412594899, 0, 2),
(25, 'C', 'MODE-SHUTDOWN-ON', 'MODE-SHUTDOWN-OFF', 'SELECT:TYPE=Light/SET:STATE:0\r\nSELECT:ALL/AUTO:M:A', 1412552006, 0, 2),
(7, 'T', 'SUNRISE-30', 'SUNSET', 'SELECT:GROUP=minimal/SET:STATE:0:1\r\nSELECT:TYPE=HM-Blinds/SET:LEVEL:0:0.5\r\nACTION?/MODE?:Night/MODE:At Home\r\n:HAL:LivingRoomBlindsMain:open:closed', 1412572094, 0, 2),
(27, 'C', 'TEST', '', 'ACTION?/MODE?:Night/MODE:At Home', 1412594530, 0, 2);

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

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`m_key`, `m_type`, `m_time`, `m_text`, `m_data`) VALUES
(1, 'test', 1385594906, '(no text)', '[]'),
(2, 'test', 1385594919, '(no text)', '[]'),
(3, 'test', 1385594929, '(no text)', '[]'),
(4, 'test', 1385594940, '(no text)', '[]'),
(5, 'test', 1385594951, '(no text)', '[]'),
(6, 'test', 1385594962, '(no text)', '[]'),
(7, 'test', 1385594973, '(no text)', '[]'),
(8, 'test', 1385594984, '(no text)', '[]'),
(9, 'test', 1385595018, '(no text)', '[]'),
(10, 'test', 1385595029, '(no text)', '[]'),
(11, 'test', 1385595527, '359671173', '[]'),
(12, 'test', 1385595531, '1379055069', '[]'),
(13, 'test', 1385595565, '400306384', '[]'),
(14, 'test', 1385595685, '274164907', '[]'),
(15, 'test', 1385598688, '921402650', '[]'),
(16, 'test', 1385598690, '408268395', '[]'),
(17, 'test', 1385598707, '5000754', '[]'),
(18, 'test', 1385598715, '844233219', '[]'),
(19, 'test', 1385598720, '49474721', '[]'),
(20, 'test', 1385598735, '963631311', '[]'),
(21, 'test', 1385598736, '693443495', '[]'),
(22, 'test', 1385598737, '901553396', '[]'),
(23, 'test', 1385598740, '939895566', '[]'),
(24, 'test', 1385598824, '1082945947', '[]'),
(25, 'test', 1385598826, '236786071', '[]'),
(26, 'test', 1385598831, '300140684', '[]'),
(27, 'test', 1385598832, '228030986', '[]'),
(28, 'test', 1385598835, '668854079', '[]'),
(29, 'test', 1385598837, '1269311378', '[]'),
(30, 'test', 1385598838, '249577893', '[]'),
(31, 'test', 1385598839, '616557722', '[]'),
(32, 'test', 1385598841, '1018960135', '[]'),
(33, 'test', 1385598842, '276403324', '[]'),
(34, 'test', 1385598842, '365809509', '[]'),
(35, 'test', 1385599771, '839871966', '[]'),
(36, 'test', 1385599868, '841805334', '[]'),
(37, 'test', 1385599900, '194324840', '[]'),
(38, 'test', 1385599922, '884704868', '[]'),
(39, 'test', 1385600030, '141005156', '[]'),
(40, 'test', 1385600407, '426325554', '[]'),
(41, 'test', 1385600427, '676743353', '[]'),
(42, 'test', 1385600447, '643734151', '[]'),
(43, 'test', 1385600498, '469549774', '[]'),
(44, 'test', 1385600528, '1038331283', '[]'),
(45, 'test', 1385600611, 'hello world', '[]'),
(46, 'test', 1385600939, 'hello world', '[]');

-- --------------------------------------------------------

--
-- Table structure for table `nvstore`
--

CREATE TABLE IF NOT EXISTS `nvstore` (
  `nv_key` varchar(64) NOT NULL,
  `nv_data` longtext,
  `nv_lastupdate` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`nv_key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `nvstore`
--

INSERT INTO `nvstore` (`nv_key`, `nv_data`, `nv_lastupdate`) VALUES
('client/10.32.4.105', '{"lastseen":1407243561,"Office":"1","hideHallway":"0","hideBedroom":"1","hideGuest Room":"0","hideOffice":"1","name":"HallwayPad"}', 1407243561),
('client/10.32.3.123', '{"lastseen":1410979809,"name":"Callisto"}', 1410979809),
('client/10.32.4.106', '{"lastseen":1405197237}', 1405197237),
('client/10.32.3.102', '{"lastseen":1412580125}', 1412580125),
('client/10.32.3.122', '{"lastseen":1412554742,"name":"iMacGuestroom"}', 1412554742),
('client/10.32.3.100', '{"lastseen":1412558342}', 1412558342),
('client/10.32.3.116', '{"lastseen":1405451755}', 1405451755),
('client/10.32.2.2', '{"lastseen":1405699707}', 1405699707),
('client/95.211.224.44', '{"lastseen":1412305328}', 1412305328),
('client/194.231.54.236', '{"lastseen":1409942426}', 1409942426),
('client/212.43.76.1', '{"lastseen":1405805254}', 1405805254),
('client/10.32.0.141', '{"lastseen":1405969521}', 1405969521),
('client/10.32.0.134', '{"lastseen":1406143360}', 1406143360),
('client/10.32.3.107', '{"lastseen":1407182765}', 1407182765),
('client/10.32.3.109', '{"lastseen":1408056247,"name":"HallwayPad","hideLiving Room":"0","hideOffice":"1","hideBedroom":"1"}', 1408056247),
('client/10.32.3.131', '{"lastseen":1407540627}', 1407540627),
('client/10.32.3.103', '{"lastseen":1409107449}', 1409107449),
('client/212.43.76.0', '{"lastseen":1412281998}', 1412281998),
('client/2.206.1.242', '{"lastseen":1411141737}', 1411141737),
('client/78.46.43.39', '{"lastseen":1411145347}', 1411145347),
('client/176.67.169.141', '{"lastseen":1411200346}', 1411200346),
('client/10.32.3.124', '{"lastseen":1412594547}', 1412594547),
('client/88.150.191.226', '{"lastseen":1411838670}', 1411838670),
('client/158.255.215.219', '{"lastseen":1411868101}', 1411868101),
('pref/modes', '["Away","At Home","Night","No Auto","Alarm","Lockdown","Shutdown"]', 1412081917),
('state/current', '{"mode":"At Home"}', 1412594897),
('client/10.32.3.114', '{"lastseen":1412196312}', 1412196312),
('group/minimal', '["2037","2006","2017","2053","2025"]', 1412546866);

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

--
-- Dumping data for table `stateinfo`
--

INSERT INTO `stateinfo` (`si_bus`, `si_name`, `si_param`, `si_mode`, `si_devicekey`, `si_value`, `si_time`, `si_by`, `si_event`, `si_ip`, `si_uid`) VALUES
('HE', '2002', 'STATE', 'TX', 2002, '0', 1412555296, 'API', '#22 MODE-NIGHT-ON', '10.32.3.100', 0),
('HE', '2003', 'STATE', 'TX', 2003, '0', 1392002268, 'UI', '', '10.32.3.101', 0),
('HE', '2005', 'STATE', 'TX', 2005, '0', 1412470295, 'UI', '', '10.32.3.102', 0),
('HE', '2006', 'STATE', 'TX', 2006, '1', 1412594883, 'API', '#21 MODE-MINIMAL-ON', '10.32.3.124', 0),
('HE', '2008', 'STATE', 'TX', 2008, '0', 1404947064, 'UI', '', '10.32.3.123', 0),
('HE', '2017', 'STATE', 'TX', 2017, '0', 1412594906, 'UI', '', '10.32.3.124', 0),
('HM', 'JEQ0259329:0', 'CONFIG_PENDING', 'RX', 0, '', 1385551759, 'BIDCOS', '', '127.0.0.1', 0),
('HM', 'JEQ0259329:1', 'DIRECTION', 'RX', 2019, '0', 1412528967, 'BIDCOS', '', '127.0.0.1', 0),
('HM', 'JEQ0259329:1', 'LEVEL', 'RX', 2019, '0', 1412529100, 'BIDCOS', '', '127.0.0.1', 0),
('HM', 'JEQ0259329:1', 'LEVEL', 'TX', 2019, '0', 1412572093, '', '#7 SUNRISE-30', '127.0.0.1', 0),
('HM', 'JEQ0259329:1', 'STOP', 'RX', 0, '', 1385558333, 'BIDCOS', '', '127.0.0.1', 0),
('HM', 'JEQ0259329:1', 'WORKING', 'RX', 2019, '', 1412572128, 'BIDCOS', '', '127.0.0.1', 0),
('HM', 'JEQ0738696:1', 'STATE', 'RX', 2018, 'false', 1412559207, 'BIDCOS', '', '127.0.0.1', 0),
('HM', 'JEQ0738696:1', 'STATE', 'TX', 2018, '0', 1412559206, '', '', '10.32.3.100', 0),
('HM', 'JEQ0738696:1', 'WORKING', 'RX', 2018, '', 1412559207, 'BIDCOS', '', '127.0.0.1', 0),
('HM', 'KEQ0180768:1', 'INSTALL_TEST', 'RX', 2029, '1', 1411546772, 'BIDCOS', '', '127.0.0.1', 0),
('HM', 'KEQ0180768:1', 'PRESS_SHORT', 'RX', 2029, '1', 1412076632, 'BIDCOS', '', '127.0.0.1', 0),
('HM', 'KEQ0180768:2', 'INSTALL_TEST', 'RX', 2030, '1', 1411580448, 'BIDCOS', '', '127.0.0.1', 0),
('HM', 'KEQ0180768:2', 'PRESS_SHORT', 'RX', 2030, '1', 1412380568, 'BIDCOS', '', '127.0.0.1', 0),
('HM', 'KEQ0180768:3', 'INSTALL_TEST', 'RX', 2031, '1', 1411779149, 'BIDCOS', '', '127.0.0.1', 0),
('HM', 'KEQ0180768:3', 'PRESS_SHORT', 'RX', 2031, '1', 1412076638, 'BIDCOS', '', '127.0.0.1', 0),
('HM', 'KEQ0180768:4', 'INSTALL_TEST', 'RX', 2032, '1', 1411779135, 'BIDCOS', '', '127.0.0.1', 0),
('HM', 'KEQ0180768:4', 'PRESS_SHORT', 'RX', 2032, '1', 1412300184, 'BIDCOS', '', '127.0.0.1', 0),
('HM', 'JRT0002934:0', 'CONFIG_PENDING', 'RX', 2041, '', 1407399869, 'BIDCOS', '', '127.0.0.1', 0),
('HM', 'JRT0002934:1', 'LEVEL', 'TX', 2024, '0', 1412572093, '', '#7 SUNRISE-30', '127.0.0.1', 0),
('HM', 'JRT0002934:1', 'LEVEL', 'RX', 2024, '0', 1412572125, 'BIDCOS', '', '127.0.0.1', 0),
('HM', 'JRT0003197:1', 'LEVEL', 'TX', 2028, '0', 1412572093, '', '#7 SUNRISE-30', '127.0.0.1', 0),
('HM', 'JRT0003197:1', 'LEVEL', 'RX', 2028, '0.485', 1412572095, 'BIDCOS', '', '127.0.0.1', 0),
('HM', 'JRT0002934:1', 'DIRECTION', 'RX', 2024, '2', 1412572094, 'BIDCOS', '', '127.0.0.1', 0),
('HM', 'JRT0002934:1', 'WORKING', 'RX', 2024, '', 1412572125, 'BIDCOS', '', '127.0.0.1', 0),
('HM', 'JRT0003197:1', 'DIRECTION', 'RX', 2028, '0', 1412528966, 'BIDCOS', '', '127.0.0.1', 0),
('HM', 'JRT0003197:1', 'WORKING', 'RX', 2028, '', 1412572127, 'BIDCOS', '', '127.0.0.1', 0),
('HE', '2017', '', 'TX', 2017, '0', 1386055729, 'API', '#7 SUNRISE+20', '127.0.0.1', 0),
('HM', 'JEQ0738696:2', 'STATE', 'TX', 2025, '0', 1412594910, '', '', '10.32.3.124', 0),
('HM', 'JEQ0155347:0', 'CONFIG_PENDING', 'RX', 2036, '', 1412526082, 'BIDCOS', '', '127.0.0.1', 0),
('HM', 'KEQ0180768:5', 'INSTALL_TEST', 'RX', 2033, '1', 1411752678, 'BIDCOS', '', '127.0.0.1', 0),
('HM', 'KEQ0180768:5', 'PRESS_SHORT', 'RX', 2033, '1', 1412417191, 'BIDCOS', '', '127.0.0.1', 0),
('HM', 'JEQ0155347:1', 'BRIGHTNESS', 'RX', 2037, '75', 1412596528, 'BIDCOS', '', '127.0.0.1', 0),
('HM', 'JEQ0155347:1', 'INSTALL_TEST', 'RX', 2037, '1', 1412592665, 'BIDCOS', '', '127.0.0.1', 0),
('HM', 'JEQ0155347:1', 'MOTION', 'RX', 2037, '', 1412596036, 'BIDCOS', '', '127.0.0.1', 0),
('HM', 'JEQ0155347:1', 'NEXT_TRANSMISSION', 'RX', 2037, '70', 1412595961, 'BIDCOS', '', '127.0.0.1', 0),
('HM', 'JEQ0155347:1', 'ERROR', 'RX', 2037, '0', 1412596805, 'BIDCOS', '', '127.0.0.1', 0),
('GPIO', '21:22:27', '21', 'TX', 2049, 'open', 1387813619, '', '', '10.32.3.101', 0),
('HE', '2009', 'STATE', 'TX', 2048, '0', 1405031996, 'UI', '', '10.32.3.123', 0),
('HM', 'JEQ0738696:2', 'WORKING', 'RX', 2025, '', 1412594911, 'BIDCOS', '', '127.0.0.1', 0),
('HM', 'JEQ0155347:0', 'UNREACH', 'RX', 2036, '', 1407443554, 'BIDCOS', '', '127.0.0.1', 0),
('HM', 'JEQ0738696:2', 'STATE', 'RX', 2025, '', 1412594911, 'BIDCOS', '', '127.0.0.1', 0),
('HE', '2006', '1', 'TX', 2006, '0', 1387479037, '', '', '10.32.3.101', 0),
('HM', 'JEQ0738696:1', 'INHIBIT', 'RX', 2018, '', 1404665548, 'BIDCOS', '', '127.0.0.1', 0),
('HM', 'JRT0002934:1', 'STATE', 'TX', 2024, '0', 1412541954, '', '#21 MODE-MINIMAL-ONLY', '10.32.3.124', 0),
('HM', 'LEQ0417192:2', 'ACTUAL_TEMPERATURE', 'RX', 2060, '21.4', 1412596850, 'BIDCOS', '', '127.0.0.1', 0),
('HM', 'KEQ0180768:5', 'PRESS_LONG', 'RX', 2033, '1', 1408908155, 'BIDCOS', '', '127.0.0.1', 0),
('HM', 'JEQ0155347:0', 'STICKY_UNREACH', 'RX', 2036, '', 1412070897, 'BIDCOS', '', '127.0.0.1', 0),
('HM', 'JEQ0738696:2', 'INHIBIT', 'RX', 2025, '', 1404665548, 'BIDCOS', '', '127.0.0.1', 0),
('HM', 'JEQ0738696:3', 'STATE', 'RX', 2026, '', 1412533612, 'BIDCOS', '', '127.0.0.1', 0),
('HM', 'JEQ0738696:3', 'INHIBIT', 'RX', 2026, '', 1404665548, 'BIDCOS', '', '127.0.0.1', 0),
('HM', 'JEQ0738696:3', 'WORKING', 'RX', 2026, '', 1412533612, 'BIDCOS', '', '127.0.0.1', 0),
('HM', 'JEQ0738696:4', 'INHIBIT', 'RX', 2027, '', 1404665549, 'BIDCOS', '', '127.0.0.1', 0),
('HM', 'JEQ0738696:4', 'STATE', 'RX', 2027, '', 1412533613, 'BIDCOS', '', '127.0.0.1', 0),
('HM', 'JEQ0738696:4', 'WORKING', 'RX', 2027, '', 1412533613, 'BIDCOS', '', '127.0.0.1', 0),
('GPIO', '21:22:27', '27', 'TX', 2049, 'closed', 1387813538, '', '', '10.32.3.101', 0),
('HM', 'JEQ0259329:0', 'UNREACH', 'RX', 2043, '', 1412528884, 'BIDCOS', '', '127.0.0.1', 0),
('HM', 'JEQ0259329:0', 'STICKY_UNREACH', 'RX', 2043, '1', 1412139624, 'BIDCOS', '', '127.0.0.1', 0),
('GPIO', '15:13:16', '16', 'TX', 2049, 'closed', 1412528942, '', '#19 SUNSET+16', '127.0.0.1', 0),
('HM', 'KEQ0180768:1', 'PRESS_CONT', 'RX', 2029, '1', 1405123331, 'BIDCOS', '', '127.0.0.1', 0),
('GPIO', '15:13:16', '15', 'TX', 2049, 'open', 1412572094, '', '#7 SUNRISE-30', '127.0.0.1', 0),
('HM', 'JEQ0302533:0', 'CONFIG_PENDING', 'RX', 2051, '', 1407436386, 'BIDCOS', '', '127.0.0.1', 0),
('GPIO', '15:13:16', 'LEVEL', 'TX', 2049, '', 1404354948, 'UI', '', '10.32.3.100', 0),
('HM', 'JEQ0302533:1', 'DIRECTION', 'RX', 2052, '0', 1412529080, 'BIDCOS', '', '127.0.0.1', 0),
('HM', 'JEQ0302533:1', 'LEVEL', 'RX', 2052, '0', 1412529142, 'BIDCOS', '', '127.0.0.1', 0),
('HM', 'JEQ0302533:1', 'WORKING', 'RX', 2052, '', 1412555330, 'BIDCOS', '', '127.0.0.1', 0),
('HM', 'JEQ0302533:1', 'LEVEL', 'TX', 2052, '0', 1412555297, '', '#22 MODE-NIGHT-ON', '10.32.3.100', 0),
('HM', 'KEQ0180768:6', 'INSTALL_TEST', 'RX', 2034, '1', 1411411599, 'BIDCOS', '', '127.0.0.1', 0),
('HE', '2001', 'STATE', 'TX', 2053, '0', 1412594907, 'UI', '', '10.32.3.124', 0),
('HM', 'KEQ0180768:6', 'PRESS_SHORT', 'RX', 2034, '1', 1411933869, 'BIDCOS', '', '127.0.0.1', 0),
('HM', 'KEQ0180768:5', 'PRESS_CONT', 'RX', 2033, '1', 1408908155, 'BIDCOS', '', '127.0.0.1', 0),
('HE', '2014', 'STATE', 'TX', 2065, '0', 1411440632, 'UI', '', '10.32.3.102', 0),
('HM', 'KEQ0180768:5', 'PRESS_LONG_RELEASE', 'RX', 2033, '1', 1408908155, 'BIDCOS', '', '127.0.0.1', 0),
('HM', 'KEQ0180768:1', 'PRESS_LONG', 'RX', 2029, '1', 1405123331, 'BIDCOS', '', '127.0.0.1', 0),
('HM', 'KEQ0632488:0', 'CONFIG_PENDING', 'RX', 2055, '', 1407358465, 'BIDCOS', '', '127.0.0.1', 0),
('HM', 'KEQ0180768:1', 'PRESS_LONG_RELEASE', 'RX', 2029, '1', 1405123349, 'BIDCOS', '', '127.0.0.1', 0),
('HM', 'KEQ0632488:1', 'STATE', 'RX', 2056, '', 1412593273, 'BIDCOS', '', '127.0.0.1', 0),
('HM', 'JRT0002934:0', 'UNREACH', 'RX', 2041, '', 1411063649, 'BIDCOS', '', '127.0.0.1', 0),
('HM', 'JRT0002934:0', 'STICKY_UNREACH', 'RX', 2041, '', 1412070948, 'BIDCOS', '', '127.0.0.1', 0),
('HM', 'KEQ0632488:1', 'WORKING', 'RX', 2056, '', 1412593273, 'BIDCOS', '', '127.0.0.1', 0),
('HM', 'KEQ0632488:1', 'STATE', 'TX', 2056, '0', 1412593272, '', '', '127.0.0.1', 0),
('HM', 'KEQ0632488:1', 'INHIBIT', 'RX', 2056, '', 1406728942, 'BIDCOS', '', '127.0.0.1', 0),
('HM', 'JRT0003099:0', 'RSSI_DEVICE', 'RX', 2067, '-69', 1412572127, 'BIDCOS', '', '127.0.0.1', 0),
('HM', 'LEQ0417192:2', 'BATTERY_STATE', 'RX', 2060, '2.9', 1412591115, 'BIDCOS', '', '127.0.0.1', 0),
('HM', 'LEQ0417192:0', 'RSSI_DEVICE', 'RX', 2058, '-71', 1412597001, 'BIDCOS', '', '127.0.0.1', 0),
('HM', 'LEQ0417192:2', 'COMMUNICATION_REPORTING', 'RX', 2060, '', 1412591115, 'BIDCOS', '', '127.0.0.1', 0),
('HM', 'LEQ0417192:2', 'BOOST_STATE', 'RX', 2060, '0', 1412591115, 'BIDCOS', '', '127.0.0.1', 0),
('HM', 'LEQ0417192:2', 'CONTROL_MODE', 'RX', 2060, '0', 1412591115, 'BIDCOS', '', '127.0.0.1', 0),
('HM', 'LEQ0417192:2', 'LOWBAT_REPORTING', 'RX', 2060, '', 1412591115, 'BIDCOS', '', '127.0.0.1', 0),
('HM', 'LEQ0417192:2', 'PARTY_START_TIME', 'RX', 2060, '0', 1412591115, 'BIDCOS', '', '127.0.0.1', 0),
('HM', 'LEQ0417192:2', 'SET_TEMPERATURE', 'RX', 2060, '17', 1412596860, 'BIDCOS', '', '127.0.0.1', 0),
('HM', 'LEQ0417192:2', 'WINDOW_OPEN_REPORTING', 'RX', 2060, '', 1412596860, 'BIDCOS', '', '127.0.0.1', 0),
('HM', 'LEQ0417192:2', 'ACTUAL_HUMIDITY', 'RX', 2060, '62', 1412596371, 'BIDCOS', '', '127.0.0.1', 0),
('HM', 'LEQ0417192:1', 'TEMPERATURE', 'RX', 2059, '21.5', 1412597001, 'BIDCOS', '', '127.0.0.1', 0),
('HM', 'LEQ0417192:1', 'HUMIDITY', 'RX', 2059, '62', 1412596391, 'BIDCOS', '', '127.0.0.1', 0),
('HM', 'KEQ0180768:0', 'RSSI_DEVICE', 'RX', 2045, '-71', 1412417191, 'BIDCOS', '', '127.0.0.1', 0),
('HM', 'KEQ0180768:0', 'CONFIG_PENDING', 'RX', 2045, '', 1407356816, 'BIDCOS', '', '127.0.0.1', 0),
('HM', 'JEQ0155347:0', 'RSSI_DEVICE', 'RX', 2036, '-67', 1412596805, 'BIDCOS', '', '127.0.0.1', 0),
('HM', 'KEQ0632488:0', 'RSSI_DEVICE', 'RX', 2055, '-83', 1412593272, 'BIDCOS', '', '127.0.0.1', 0),
('HM', 'KEQ0632488:0', 'UNREACH', 'RX', 2055, '', 1407438205, 'BIDCOS', '', '127.0.0.1', 0),
('HM', 'KEQ0632488:0', 'STICKY_UNREACH', 'RX', 2055, '', 1412070910, 'BIDCOS', '', '127.0.0.1', 0),
('HM', 'JRT0002934:0', 'RSSI_DEVICE', 'RX', 2041, '-73', 1412572125, 'BIDCOS', '', '127.0.0.1', 0),
('HM', 'JRT0003197:0', 'RSSI_DEVICE', 'RX', 2039, '-79', 1412572126, 'BIDCOS', '', '127.0.0.1', 0),
('HM', 'JRT0003197:0', 'CONFIG_PENDING', 'RX', 2039, '', 1407399404, 'BIDCOS', '', '127.0.0.1', 0),
('HM', 'JEQ0738696:0', 'RSSI_DEVICE', 'RX', 2047, '-64', 1412594911, 'BIDCOS', '', '127.0.0.1', 0),
('HM', 'JEQ0738696:0', 'CONFIG_PENDING', 'RX', 2047, '', 1407400781, 'BIDCOS', '', '127.0.0.1', 0),
('HE', '2016', 'STATE', 'TX', 2064, '0', 1411386195, 'UI', '', '10.32.3.122', 0),
('HM', 'JEQ0302533:0', 'RSSI_DEVICE', 'RX', 2051, '-67', 1412555330, 'BIDCOS', '', '127.0.0.1', 0),
('HM', 'JRT0003099:0', 'CONFIG_PENDING', 'RX', 0, '', 1407435551, 'BIDCOS', '', '127.0.0.1', 0),
('HM', 'JRT0003099:1', 'LEVEL', 'TX', 2068, '0', 1412572093, '', '#7 SUNRISE-30', '127.0.0.1', 0),
('HM', 'JRT0003099:1', 'LEVEL', 'RX', 2068, '0.49', 1412572095, 'BIDCOS', '', '127.0.0.1', 0),
('HM', 'JRT0003099:1', 'DIRECTION', 'RX', 2068, '0', 1412528968, 'BIDCOS', '', '127.0.0.1', 0),
('HM', 'JRT0003099:1', 'WORKING', 'RX', 2068, '', 1412572127, 'BIDCOS', '', '127.0.0.1', 0),
('HM', 'JEQ0259329:0', 'RSSI_DEVICE', 'RX', 2043, '-65', 1412572128, 'BIDCOS', '', '127.0.0.1', 0),
('HM', 'LEQ0417192:0', 'UNREACH', 'RX', 2058, '', 1407443445, 'BIDCOS', '', '127.0.0.1', 0),
('HM', 'LEQ0417192:0', 'STICKY_UNREACH', 'RX', 2058, '', 1412070937, 'BIDCOS', '', '127.0.0.1', 0),
('HM', 'JRT0003099:0', 'STICKY_UNREACH', 'RX', 2067, '', 1412070974, 'BIDCOS', '', '127.0.0.1', 0),
('HM', 'JRT0003197:0', 'UNREACH', 'RX', 2039, '', 1412528884, 'BIDCOS', '', '127.0.0.1', 0),
('HM', 'JEQ0302533:0', 'STICKY_UNREACH', 'RX', 2051, '', 1412070873, 'BIDCOS', '', '127.0.0.1', 0),
('HM', 'JRT0003197:0', 'STICKY_UNREACH', 'RX', 2039, '1', 1412097497, 'BIDCOS', '', '127.0.0.1', 0),
('HM', 'JRT0003099:0', 'UNREACH', 'RX', 2067, '', 1412528885, 'BIDCOS', '', '127.0.0.1', 0),
('HM', 'JEQ0738696:3', 'STATE', 'TX', 2026, '0', 1412533610, '', '#21 MODE-MINIMAL-ONLY', '10.32.3.124', 0),
('HM', 'JEQ0302533:0', 'UNREACH', 'RX', 2051, '', 1412528943, 'BIDCOS', '', '127.0.0.1', 0),
('HE', '2017', '1', 'TX', 2017, '1', 1412531104, 'API', '#21 MODE-MINIMAL-ON', '10.32.3.124', 0),
('HM', 'JEQ0259329:1', 'STATE', 'TX', 2019, '0', 1412550025, '', '#21 MODE-MINIMAL-ONLY', '10.32.3.124', 0),
('HM', 'JEQ0738696:4', 'STATE', 'TX', 2027, '0', 1412533610, '', '#21 MODE-MINIMAL-ONLY', '10.32.3.124', 0),
('HM', 'JRT0003197:1', 'STATE', 'TX', 2028, '0', 1412541954, '', '#21 MODE-MINIMAL-ONLY', '10.32.3.124', 0),
('HM', 'KEQ0180768:1', 'STATE', 'TX', 2029, '0', 1412541954, '', '#21 MODE-MINIMAL-ONLY', '10.32.3.124', 0),
('HM', 'KEQ0180768:2', 'STATE', 'TX', 2030, '0', 1412541954, '', '#21 MODE-MINIMAL-ONLY', '10.32.3.124', 0),
('HM', 'KEQ0180768:3', 'STATE', 'TX', 2031, '0', 1412541954, '', '#21 MODE-MINIMAL-ONLY', '10.32.3.124', 0),
('HM', 'KEQ0180768:4', 'STATE', 'TX', 2032, '0', 1412541955, '', '#21 MODE-MINIMAL-ONLY', '10.32.3.124', 0),
('HM', 'KEQ0180768:5', 'STATE', 'TX', 2033, '0', 1412541955, '', '#21 MODE-MINIMAL-ONLY', '10.32.3.124', 0),
('HM', 'KEQ0180768:6', 'STATE', 'TX', 2034, '0', 1412541955, '', '#21 MODE-MINIMAL-ONLY', '10.32.3.124', 0),
('HM', 'JEQ0155347', 'STATE', 'TX', 2035, '0', 1412541955, '', '#21 MODE-MINIMAL-ONLY', '10.32.3.124', 0),
('HM', 'JEQ0155347:0', 'STATE', 'TX', 2036, '0', 1412541955, '', '#21 MODE-MINIMAL-ONLY', '10.32.3.124', 0),
('HM', 'JEQ0155347:1', 'STATE', 'TX', 2037, '1', 1412594883, '', '#21 MODE-MINIMAL-ON', '10.32.3.124', 0),
('HM', 'JRT0003197', 'STATE', 'TX', 2038, '0', 1412541955, '', '#21 MODE-MINIMAL-ONLY', '10.32.3.124', 0),
('HM', 'JRT0003197:0', 'STATE', 'TX', 2039, '0', 1412541955, '', '#21 MODE-MINIMAL-ONLY', '10.32.3.124', 0),
('HM', 'JRT0002934', 'STATE', 'TX', 2040, '0', 1412541955, '', '#21 MODE-MINIMAL-ONLY', '10.32.3.124', 0),
('HM', 'JRT0002934:0', 'STATE', 'TX', 2041, '0', 1412541955, '', '#21 MODE-MINIMAL-ONLY', '10.32.3.124', 0),
('HM', 'JEQ0259329', 'STATE', 'TX', 2042, '0', 1412541955, '', '#21 MODE-MINIMAL-ONLY', '10.32.3.124', 0),
('HM', 'JEQ0259329:0', 'STATE', 'TX', 2043, '0', 1412541956, '', '#21 MODE-MINIMAL-ONLY', '10.32.3.124', 0),
('HM', 'KEQ0180768', 'STATE', 'TX', 2044, '0', 1412541956, '', '#21 MODE-MINIMAL-ONLY', '10.32.3.124', 0),
('HM', 'KEQ0180768:0', 'STATE', 'TX', 2045, '0', 1412541956, '', '#21 MODE-MINIMAL-ONLY', '10.32.3.124', 0),
('HM', 'JEQ0738696', 'STATE', 'TX', 2046, '0', 1412541956, '', '#21 MODE-MINIMAL-ONLY', '10.32.3.124', 0),
('HM', 'JEQ0738696:0', 'STATE', 'TX', 2047, '0', 1412541956, '', '#21 MODE-MINIMAL-ONLY', '10.32.3.124', 0),
('GPIO', '15:13:16', 'STATE', 'TX', 2049, '', 1412541956, 'API', '#21 MODE-MINIMAL-ONLY', '10.32.3.124', 0),
('HM', 'JEQ0302533', 'STATE', 'TX', 2050, '0', 1412541957, '', '#21 MODE-MINIMAL-ONLY', '10.32.3.124', 0),
('HM', 'JEQ0302533:0', 'STATE', 'TX', 2051, '0', 1412541957, '', '#21 MODE-MINIMAL-ONLY', '10.32.3.124', 0),
('HM', 'JEQ0302533:1', 'STATE', 'TX', 2052, '0', 1412541957, '', '#21 MODE-MINIMAL-ONLY', '10.32.3.124', 0),
('HM', 'KEQ0632488', 'STATE', 'TX', 2054, '0', 1412541957, '', '#21 MODE-MINIMAL-ONLY', '10.32.3.124', 0),
('HM', 'KEQ0632488:0', 'STATE', 'TX', 2055, '0', 1412541957, '', '#21 MODE-MINIMAL-ONLY', '10.32.3.124', 0),
('HM', 'LEQ0417192', 'STATE', 'TX', 2057, '0', 1412541957, '', '#21 MODE-MINIMAL-ONLY', '10.32.3.124', 0),
('HM', 'LEQ0417192:0', 'STATE', 'TX', 2058, '0', 1412541957, '', '#21 MODE-MINIMAL-ONLY', '10.32.3.124', 0),
('HM', 'LEQ0417192:1', 'STATE', 'TX', 2059, '0', 1412541957, '', '#21 MODE-MINIMAL-ONLY', '10.32.3.124', 0),
('HM', 'LEQ0417192:2', 'STATE', 'TX', 2060, '0', 1412541958, '', '#21 MODE-MINIMAL-ONLY', '10.32.3.124', 0),
('HM', 'LEQ0417192:3', 'STATE', 'TX', 2061, '0', 1412541958, '', '#21 MODE-MINIMAL-ONLY', '10.32.3.124', 0),
('HM', 'LEQ0417192:6', 'STATE', 'TX', 2062, '0', 1412541958, '', '#21 MODE-MINIMAL-ONLY', '10.32.3.124', 0),
('HM', 'LEQ0417192:7', 'STATE', 'TX', 2063, '0', 1412541958, '', '#21 MODE-MINIMAL-ONLY', '10.32.3.124', 0),
('HM', 'JRT0003099', 'STATE', 'TX', 2066, '0', 1412541958, '', '#21 MODE-MINIMAL-ONLY', '10.32.3.124', 0),
('HM', 'JRT0003099:0', 'STATE', 'TX', 2067, '0', 1412541958, '', '#21 MODE-MINIMAL-ONLY', '10.32.3.124', 0),
('HM', 'JRT0003099:1', 'STATE', 'TX', 2068, '0', 1412541958, '', '#21 MODE-MINIMAL-ONLY', '10.32.3.124', 0);

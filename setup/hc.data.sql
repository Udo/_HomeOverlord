SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";
CREATE DATABASE IF NOT EXISTS `hc` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `hc`;

DROP TABLE IF EXISTS `accounts`;
CREATE TABLE IF NOT EXISTS `accounts` (
  `a_key` int(11) NOT NULL AUTO_INCREMENT,
  `a_username` varchar(64) NOT NULL,
  `a_password` varchar(64) NOT NULL,
  `a_role` varchar(1) NOT NULL DEFAULT 'U',
  `a_active` varchar(1) NOT NULL DEFAULT 'Y',
  PRIMARY KEY (`a_key`),
  UNIQUE KEY `a_username` (`a_username`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

DROP TABLE IF EXISTS `devices`;
CREATE TABLE IF NOT EXISTS `devices` (
  `d_key` int(11) NOT NULL AUTO_INCREMENT,
  `d_bus` varchar(6) NOT NULL,
  `d_type` varchar(32) NOT NULL,
  `d_room` varchar(32) NOT NULL,
  `d_name` varchar(32) NOT NULL,
  `d_id` varchar(32) NOT NULL,
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

INSERT INTO `devices` (`d_key`, `d_bus`, `d_type`, `d_room`, `d_name`, `d_id`, `d_state`, `d_auto`, `d_config`, `d_alias`, `d_statustext`, `d_statuschanged`) VALUES
(2002, 'HE', 'Light', 'Office', 'Big Lamp', '2002', '1', 'A', '', 'OfficeLamp', 'UI', 1387465956),
(2003, 'HE', 'IT', 'Office', 'Monitor Wall', '2003', '0', 'A', '', '', 'UI', 1387040737),
(2005, 'HE', 'Light', 'Living Room', 'Ceiling Lights', '2005', '1', 'A', '', 'LivingRoomCeiling', '#1 Hallway Button', 1387470167),
(2006, 'HE', 'Light', 'Office', 'Corner Lights', '2006', '1', 'A', '', 'OfficeCornerLights', 'because test', 1387479230),
(2008, 'HE', 'Light', 'Outside', 'Entrance Lantern', '2008', '0', 'A', '', 'Entrance', 'UI', 1387466635),
(2017, 'HE', 'Light', 'Guest Room', 'Nightstand', '2017', '0', 'A', '', '', 'UI', 1386830929),
(2018, 'HM', 'Light', 'Hallway', 'Hallway Light 1', 'JEQ0738696:1', '1', 'A', '', 'HallwayLight', '#3 Hallway Button', 1387470172),
(2019, 'HM', 'Blinds', 'Living Room', 'Right Window', 'JEQ0259329:1', '0.5', 'A', '', 'LivingRoomBlindsRight', 'UI', 1387466722),
(2027, 'HM', 'Light', 'unknown', 'Switch 2013-11-29 16:01:27', 'JEQ0738696:4', '', 'A', '', NULL, '', NULL),
(2026, 'HM', 'Light', 'unknown', 'Switch 2013-11-29 16:01:27', 'JEQ0738696:3', '', 'A', '', NULL, '', NULL),
(2025, 'HM', 'Light', 'Outside', 'Porch Lighting', 'JEQ0738696:2', '1', 'A', '', 'PorchLight', 'UI', 1387466681),
(2024, 'HM', 'Blinds', 'Office', 'Door', 'JRT0002934:1', '0.5', 'A', '', 'OfficeDoorBlinds', 'because test', 1387479750),
(2028, 'HM', 'Blinds', 'Office', 'Window', 'JRT0003197:1', '0.5', 'A', '', 'OfficeWindowBlinds', 'UI', 1387466685),
(2029, 'HM', 'Key', 'unknown', 'Hallway Button', 'KEQ0180768:1', '', 'A', NULL, 'HallwayButton1', NULL, NULL),
(2030, 'HM', 'Key', 'unknown', 'Hallway Button', 'KEQ0180768:2', '', 'A', NULL, 'HallwayButton2', NULL, NULL),
(2031, 'HM', 'Key', 'unknown', 'Hallway Button', 'KEQ0180768:3', '', 'A', NULL, NULL, NULL, NULL),
(2032, 'HM', 'Key', 'unknown', 'Hallway Button', 'KEQ0180768:4', '', 'A', NULL, NULL, NULL, NULL),
(2033, 'HM', 'Key', 'unknown', 'Hallway Button', 'KEQ0180768:5', '', 'A', NULL, NULL, NULL, NULL),
(2034, 'HM', 'Key', 'unknown', 'Hallway Button', 'KEQ0180768:6', '', 'A', NULL, NULL, NULL, NULL),
(2035, 'HM', 'HM-Sec-MDIR', 'unknown', 'New HM-Sec-MDIR 2013-12-05 14:01', 'JEQ0155347', '', 'A', NULL, NULL, NULL, NULL),
(2036, 'HM', 'MAINTENANCE', 'unknown', 'New MAINTENANCE 2013-12-05 14:01', 'JEQ0155347:0', '', 'A', NULL, NULL, NULL, NULL),
(2037, 'HM', 'MOTION_DETECTOR', 'Hallway', 'Hallway 2 Motion Detector', 'JEQ0155347:1', '', 'A', NULL, 'HallwayMotion', NULL, NULL),
(2038, 'HM', 'ZEL STG RM FEP 230V', 'unknown', 'New ZEL STG RM FEP 230V 2013-12-', 'JRT0003197', '', 'A', NULL, NULL, NULL, NULL),
(2039, 'HM', 'MAINTENANCE', 'unknown', 'New MAINTENANCE 2013-12-05 14:01', 'JRT0003197:0', '', 'A', NULL, NULL, NULL, NULL),
(2040, 'HM', 'ZEL STG RM FEP 230V', 'unknown', 'New ZEL STG RM FEP 230V 2013-12-', 'JRT0002934', '', 'A', NULL, NULL, NULL, NULL),
(2041, 'HM', 'MAINTENANCE', 'unknown', 'New MAINTENANCE 2013-12-05 14:01', 'JRT0002934:0', '', 'A', NULL, NULL, NULL, NULL),
(2042, 'HM', 'HM-LC-Bl1-FM', 'unknown', 'New HM-LC-Bl1-FM 2013-12-05 14:0', 'JEQ0259329', '', 'A', NULL, NULL, NULL, NULL),
(2043, 'HM', 'MAINTENANCE', 'unknown', 'New MAINTENANCE 2013-12-05 14:01', 'JEQ0259329:0', '', 'A', NULL, NULL, NULL, NULL),
(2044, 'HM', 'HM-PB-6-WM55', 'unknown', 'New HM-PB-6-WM55 2013-12-05 14:0', 'KEQ0180768', '', 'A', NULL, NULL, NULL, NULL),
(2045, 'HM', 'MAINTENANCE', 'unknown', 'New MAINTENANCE 2013-12-05 14:01', 'KEQ0180768:0', '', 'A', NULL, NULL, NULL, NULL),
(2046, 'HM', 'HM-LC-Sw4-DR', 'unknown', 'New HM-LC-Sw4-DR 2013-12-05 14:0', 'JEQ0738696', '', 'A', NULL, NULL, NULL, NULL),
(2047, 'HM', 'MAINTENANCE', 'unknown', 'New MAINTENANCE 2013-12-05 14:01', 'JEQ0738696:0', '', 'A', NULL, NULL, NULL, NULL),
(2048, 'HE', 'Light', 'Hallway', 'Hallway Light 2', '2009', '0', 'A', '', 'Hallway2Light', '#18 Hallway 2 Motion Detector', 1387477569),
(2049, 'GPIO', 'Blinds', 'Living Room', 'Main Blinds', '21:22:27', '0', 'A', NULL, 'LivingRoomBlindsMain', NULL, NULL);

DROP TABLE IF EXISTS `events`;
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

INSERT INTO `events` (`e_key`, `e_type`, `e_address`, `e_address_rev`, `e_code`, `e_lastcalled`, `e_order`, `e_cooldown`) VALUES
(1, 'C', 'HM-KEQ0180768:1-PRESSED', 'HM-KEQ0180768:2-PRESSED', ':LivingRoomCeiling:STATE:0:1', 1387470167, 0, 2),
(3, 'C', 'HM-KEQ0180768:3-PRESSED', 'HM-KEQ0180768:4-PRESSED', ':HallwayLight:STATE:0:1', 1387470172, 0, 2),
(7, 'T', 'SUNRISE', 'SUNSET', ':OfficeWindowBlinds:LEVEL:0:0.5\n:OfficeDoorBlinds:LEVEL:0:0.5\n:LivingRoomBlindsRight:LEVEL:0:0.5\n:PorchLight:STATE:0:1', 1387466562, 0, 2),
(18, 'C', 'HM-JEQ0155347:1-NEXT_TRANSMISSION', 'HM-JEQ0155347:1-MOTION', ':Hallway2Light:STATE:1:0', 1387477570, 0, 2),
(11, 'T', 'SUNSET-60', 'SUNRISE-240', ':OfficeCornerLights:STATE:1:0\r\n:HallwayLight:STATE:1:0', 1387423426, 0, 2),
(17, 'C', 'HM-KEQ0180768:5-PRESSED', 'HM-KEQ0180768:6-PRESSED', ':Entrance:STATE:0:1', 1386026958, 0, 2),
(13, 'T', 'SUNRISE-300', NULL, ':OfficeLamp:STATE:0\r\n:OfficeCornerLight:STATE:0', 1387419821, 0, 2),
(14, 'T', 'DAY-DARK', NULL, ':OfficeCornerLights:STATE:1:0', 1387466501, 0, 2);

DROP TABLE IF EXISTS `groups`;
CREATE TABLE IF NOT EXISTS `groups` (
  `g_key` int(11) NOT NULL AUTO_INCREMENT,
  `g_name` varchar(32) NOT NULL,
  `g_states` varchar(200) NOT NULL DEFAULT 'off,on',
  `g_deviceconfig` longtext NOT NULL,
  PRIMARY KEY (`g_key`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

INSERT INTO `groups` (`g_key`, `g_name`, `g_states`, `g_deviceconfig`) VALUES
(1, 'OfficeLighting', 'off,down,on', '{"2017":{"off":"","down":"","on":""},"2005":{"off":"","down":"","on":""},"2002":{"off":"0","down":"0","on":"1"},"2003":{"off":"","down":"","on":""},"2006":{"off":"0","down":"1","on":"1"},"2008":{"off":"","down":"","on":""}}'),
(2, 'CommonLighting', 'off,down,on', '{"2017":{"off":"0","down":"1","on":"1"},"2005":{"off":"0","down":"","on":"1"},"2002":{"off":"0","down":"","on":"1"},"2003":{"off":"","down":"","on":""},"2006":{"off":"0","down":"1","on":"1"},"2008":{"off":"0","down":"1","on":"1"},"2018":{"off":"0","down":"1","on":"1"},"2019":{"off":"","down":"","on":""},"2024":{"off":"","down":"","on":""},"2028":{"off":"","down":"","on":""}}');

DROP TABLE IF EXISTS `messages`;
CREATE TABLE IF NOT EXISTS `messages` (
  `m_key` bigint(20) NOT NULL AUTO_INCREMENT,
  `m_type` varchar(16) NOT NULL,
  `m_time` bigint(20) NOT NULL,
  `m_text` longtext,
  `m_data` longtext,
  PRIMARY KEY (`m_key`),
  KEY `m_time` (`m_time`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=47 ;

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

DROP TABLE IF EXISTS `stateinfo`;
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

INSERT INTO `stateinfo` (`si_bus`, `si_name`, `si_param`, `si_mode`, `si_devicekey`, `si_value`, `si_time`, `si_by`, `si_event`, `si_ip`, `si_uid`) VALUES
('HE', '2002', 'STATE', 'TX', 2002, '1', 1387465956, 'UI', '', '10.32.3.101', 0),
('HE', '2003', 'STATE', 'TX', 2003, '0', 1387040737, 'UI', '', '10.32.3.101', 0),
('HE', '2005', 'STATE', 'TX', 2005, '1', 1387470167, 'API', '#1 Hallway Button', '127.0.0.1', 0),
('HE', '2006', 'STATE', 'TX', 2006, '1', 1387479230, '', '', '10.32.3.101', 0),
('HE', '2008', 'STATE', 'TX', 2008, '0', 1387466635, 'UI', '', '10.32.3.101', 0),
('HE', '2017', 'STATE', 'TX', 2017, '0', 1386830929, 'UI', '', '10.32.3.100', 0),
('HM', 'JEQ0259329:0', 'CONFIG_PENDING', 'RX', 0, '', 1385551759, 'BIDCOS', '', '127.0.0.1', 0),
('HM', 'JEQ0259329:1', 'DIRECTION', 'RX', 2019, '0', 1387466646, 'BIDCOS', '', '127.0.0.1', 0),
('HM', 'JEQ0259329:1', 'LEVEL', 'RX', 2019, '0.05', 1387466725, 'BIDCOS', '', '127.0.0.1', 0),
('HM', 'JEQ0259329:1', 'LEVEL', 'TX', 2019, '0.5', 1387466722, 'UI', '', '10.32.3.101', 0),
('HM', 'JEQ0259329:1', 'STOP', 'RX', 0, '', 1385558333, 'BIDCOS', '', '127.0.0.1', 0),
('HM', 'JEQ0259329:1', 'WORKING', 'RX', 2019, '1', 1387466750, 'BIDCOS', '', '127.0.0.1', 0),
('HM', 'JEQ0738696:1', 'STATE', 'RX', 2018, '1', 1387470173, 'BIDCOS', '', '127.0.0.1', 0),
('HM', 'JEQ0738696:1', 'STATE', 'TX', 2018, 'true', 1387470172, 'API', '#3 Hallway Button', '127.0.0.1', 0),
('HM', 'JEQ0738696:1', 'WORKING', 'RX', 2018, '1', 1387470173, 'BIDCOS', '', '127.0.0.1', 0),
('HM', 'KEQ0180768:1', 'INSTALL_TEST', 'RX', 2029, '1', 1387470164, 'BIDCOS', '', '127.0.0.1', 0),
('HM', 'KEQ0180768:1', 'PRESS_SHORT', 'RX', 2029, '1', 1387470164, 'BIDCOS', '', '127.0.0.1', 0),
('HM', 'KEQ0180768:2', 'INSTALL_TEST', 'RX', 2030, '1', 1387466773, 'BIDCOS', '', '127.0.0.1', 0),
('HM', 'KEQ0180768:2', 'PRESS_SHORT', 'RX', 2030, '1', 1387470167, 'BIDCOS', '', '127.0.0.1', 0),
('HM', 'KEQ0180768:3', 'INSTALL_TEST', 'RX', 2031, '1', 1387470170, 'BIDCOS', '', '127.0.0.1', 0),
('HM', 'KEQ0180768:3', 'PRESS_SHORT', 'RX', 2031, '1', 1387470171, 'BIDCOS', '', '127.0.0.1', 0),
('HM', 'KEQ0180768:4', 'INSTALL_TEST', 'RX', 2032, '1', 1387466777, 'BIDCOS', '', '127.0.0.1', 0),
('HM', 'KEQ0180768:4', 'PRESS_SHORT', 'RX', 2032, '1', 1387470172, 'BIDCOS', '', '127.0.0.1', 0),
('HM', 'JRT0002934:0', 'CONFIG_PENDING', 'RX', 0, '', 1385735960, 'BIDCOS', '', '127.0.0.1', 0),
('HM', 'JRT0002934:1', 'LEVEL', 'TX', 2024, '0.5', 1387479750, '', '', '10.32.3.101', 0),
('HM', 'JRT0002934:1', 'LEVEL', 'RX', 2024, '0.44', 1387479739, 'BIDCOS', '', '127.0.0.1', 0),
('HM', 'JRT0003197:1', 'LEVEL', 'TX', 2028, '0.5', 1387466685, 'UI', '', '10.32.3.101', 0),
('HM', 'JRT0003197:1', 'LEVEL', 'RX', 2028, '0.12', 1387466691, 'BIDCOS', '', '127.0.0.1', 0),
('HM', 'JRT0002934:1', 'DIRECTION', 'RX', 2024, '0', 1387466685, 'BIDCOS', '', '127.0.0.1', 0),
('HM', 'JRT0002934:1', 'WORKING', 'RX', 2024, '1', 1387479759, 'BIDCOS', '', '127.0.0.1', 0),
('HM', 'JRT0003197:1', 'DIRECTION', 'RX', 2028, '0', 1387466646, 'BIDCOS', '', '127.0.0.1', 0),
('HM', 'JRT0003197:1', 'WORKING', 'RX', 2028, '1', 1387466713, 'BIDCOS', '', '127.0.0.1', 0),
('HE', '2017', '', 'TX', 2017, '0', 1386055729, 'API', '#7 SUNRISE+20', '127.0.0.1', 0),
('HM', 'JEQ0738696:2', 'STATE', 'TX', 2025, 'true', 1387466681, 'UI', '', '10.32.3.101', 0),
('HM', 'JEQ0155347:0', 'CONFIG_PENDING', 'RX', 2036, '', 1386861228, 'BIDCOS', '', '127.0.0.1', 0),
('HM', 'KEQ0180768:5', 'INSTALL_TEST', 'RX', 2033, '1', 1386016227, 'BIDCOS', '', '127.0.0.1', 0),
('HM', 'KEQ0180768:5', 'PRESS_SHORT', 'RX', 2033, '1', 1386026958, 'BIDCOS', '', '127.0.0.1', 0),
('HM', 'JEQ0155347:1', 'BRIGHTNESS', 'RX', 2037, '35', 1387477010, 'BIDCOS', '', '127.0.0.1', 0),
('HM', 'JEQ0155347:1', 'INSTALL_TEST', 'RX', 2037, '1', 1387466287, 'BIDCOS', '', '127.0.0.1', 0),
('HM', 'JEQ0155347:1', 'MOTION', 'RX', 2037, '', 1387477570, 'BIDCOS', '', '127.0.0.1', 0),
('HM', 'JEQ0155347:1', 'NEXT_TRANSMISSION', 'RX', 2037, '70', 1387477496, 'BIDCOS', '', '127.0.0.1', 0),
('HM', 'JEQ0155347:1', 'ERROR', 'RX', 2037, '0', 1387480205, 'BIDCOS', '', '127.0.0.1', 0),
('HE', '2009', 'STATE', 'TX', 2048, '0', 1387477569, 'API', '#18 Hallway 2 Motion Detector', '127.0.0.1', 0),
('HM', 'JEQ0738696:2', 'WORKING', 'RX', 2025, '1', 1387466681, 'BIDCOS', '', '127.0.0.1', 0),
('HM', 'JEQ0155347:0', 'UNREACH', 'RX', 2036, '', 1387465946, 'BIDCOS', '', '127.0.0.1', 0),
('HM', 'JEQ0738696:2', 'STATE', 'RX', 2025, '1', 1387466681, 'BIDCOS', '', '127.0.0.1', 0),
('HE', '2006', '1', 'TX', 2006, '0', 1387479037, '', '', '10.32.3.101', 0),
('HM', 'JRT0002934:1', 'STATE', 'TX', 2024, '0.375', 1387479424, '', '', '10.32.3.101', 0);

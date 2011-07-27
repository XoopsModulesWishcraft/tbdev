-- phpMyAdmin SQL Dump
-- version 3.2.0.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jan 06, 2010 at 09:51 PM
-- Server version: 5.1.37
-- PHP Version: 5.3.0

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `tb`
--

-- --------------------------------------------------------

--
-- Table structure for table `avps`
--

CREATE TABLE `avps` (
  `arg` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `value_s` text COLLATE utf8_unicode_ci NOT NULL,
  `value_i` int(11) NOT NULL DEFAULT '0',
  `value_u` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`arg`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


INSERT INTO `avps` VALUES 
('lastcleantime', '', 0, 1247059621),
('seeders', '', 0, 1),
('leechers', '', 0, 0),
('loadlimit', '12.5-1246045258', 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `bans`
--

CREATE TABLE `bans` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `added` int(11) NOT NULL,
  `addedby` int(10) unsigned NOT NULL DEFAULT '0',
  `comment` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `first` int(11) DEFAULT NULL,
  `last` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `first_last` (`first`,`last`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `image` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `cat_desc` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'No Description',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


INSERT INTO `categories` VALUES (1, 'Appz/PC ISO', 'cat_apps.gif', 'No Description');
INSERT INTO `categories` VALUES (2, 'Games/PC ISO', 'cat_games.gif', 'No Description');
INSERT INTO `categories` VALUES (3, 'Movies/SVCD', 'cat_movies.gif', 'No Description');
INSERT INTO `categories` VALUES (4, 'Music', 'cat_music.gif', 'No Description');
INSERT INTO `categories` VALUES (5, 'Episodes', 'cat_episodes.gif', 'No Description');
INSERT INTO `categories` VALUES (6, 'XXX', 'cat_xxx.gif', 'No Description');
INSERT INTO `categories` VALUES (7, 'Games/GBA', 'cat_games.gif', 'No Description');
INSERT INTO `categories` VALUES (8, 'Games/PS2', 'cat_games.gif', 'No Description');
INSERT INTO `categories` VALUES (9, 'Anime', 'cat_anime.gif', 'No Description');
INSERT INTO `categories` VALUES (10, 'Movies/XviD', 'cat_movies.gif', 'No Description');
INSERT INTO `categories` VALUES (11, 'Movies/DVD-R', 'cat_movies.gif', 'No Description');
INSERT INTO `categories` VALUES (12, 'Games/PC Rips', 'cat_games.gif', 'No Description');
INSERT INTO `categories` VALUES (13, 'Appz/misc', 'cat_apps.gif', 'No Description');

----------------------------------------

--
-- Table structure for table `countries`
--

CREATE TABLE `countries` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `flagpic` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


INSERT INTO `countries` VALUES (1, 'Sweden', 'sweden.gif');
INSERT INTO `countries` VALUES (2, 'United States of America', 'usa.gif');
INSERT INTO `countries` VALUES (3, 'Russia', 'russia.gif');
INSERT INTO `countries` VALUES (4, 'Finland', 'finland.gif');
INSERT INTO `countries` VALUES (5, 'Canada', 'canada.gif');
INSERT INTO `countries` VALUES (6, 'France', 'france.gif');
INSERT INTO `countries` VALUES (7, 'Germany', 'germany.gif');
INSERT INTO `countries` VALUES (8, 'China', 'china.gif');
INSERT INTO `countries` VALUES (9, 'Italy', 'italy.gif');
INSERT INTO `countries` VALUES (10, 'Denmark', 'denmark.gif');
INSERT INTO `countries` VALUES (11, 'Norway', 'norway.gif');
INSERT INTO `countries` VALUES (12, 'United Kingdom', 'uk.gif');
INSERT INTO `countries` VALUES (13, 'Ireland', 'ireland.gif');
INSERT INTO `countries` VALUES (14, 'Poland', 'poland.gif');
INSERT INTO `countries` VALUES (15, 'Netherlands', 'netherlands.gif');
INSERT INTO `countries` VALUES (16, 'Belgium', 'belgium.gif');
INSERT INTO `countries` VALUES (17, 'Japan', 'japan.gif');
INSERT INTO `countries` VALUES (18, 'Brazil', 'brazil.gif');
INSERT INTO `countries` VALUES (19, 'Argentina', 'argentina.gif');
INSERT INTO `countries` VALUES (20, 'Australia', 'australia.gif');
INSERT INTO `countries` VALUES (21, 'New Zealand', 'newzealand.gif');
INSERT INTO `countries` VALUES (22, 'Spain', 'spain.gif');
INSERT INTO `countries` VALUES (23, 'Portugal', 'portugal.gif');
INSERT INTO `countries` VALUES (24, 'Mexico', 'mexico.gif');
INSERT INTO `countries` VALUES (25, 'Singapore', 'singapore.gif');
INSERT INTO `countries` VALUES (67, 'India', 'india.gif');
INSERT INTO `countries` VALUES (62, 'Albania', 'albania.gif');
INSERT INTO `countries` VALUES (26, 'South Africa', 'southafrica.gif');
INSERT INTO `countries` VALUES (27, 'South Korea', 'southkorea.gif');
INSERT INTO `countries` VALUES (28, 'Jamaica', 'jamaica.gif');
INSERT INTO `countries` VALUES (29, 'Luxembourg', 'luxembourg.gif');
INSERT INTO `countries` VALUES (30, 'Hong Kong', 'hongkong.gif');
INSERT INTO `countries` VALUES (31, 'Belize', 'belize.gif');
INSERT INTO `countries` VALUES (32, 'Algeria', 'algeria.gif');
INSERT INTO `countries` VALUES (33, 'Angola', 'angola.gif');
INSERT INTO `countries` VALUES (34, 'Austria', 'austria.gif');
INSERT INTO `countries` VALUES (35, 'Yugoslavia', 'yugoslavia.gif');
INSERT INTO `countries` VALUES (36, 'Western Samoa', 'westernsamoa.gif');
INSERT INTO `countries` VALUES (37, 'Malaysia', 'malaysia.gif');
INSERT INTO `countries` VALUES (38, 'Dominican Republic', 'dominicanrep.gif');
INSERT INTO `countries` VALUES (39, 'Greece', 'greece.gif');
INSERT INTO `countries` VALUES (40, 'Guatemala', 'guatemala.gif');
INSERT INTO `countries` VALUES (41, 'Israel', 'israel.gif');
INSERT INTO `countries` VALUES (42, 'Pakistan', 'pakistan.gif');
INSERT INTO `countries` VALUES (43, 'Czech Republic', 'czechrep.gif');
INSERT INTO `countries` VALUES (44, 'Serbia', 'serbia.gif');
INSERT INTO `countries` VALUES (45, 'Seychelles', 'seychelles.gif');
INSERT INTO `countries` VALUES (46, 'Taiwan', 'taiwan.gif');
INSERT INTO `countries` VALUES (47, 'Puerto Rico', 'puertorico.gif');
INSERT INTO `countries` VALUES (48, 'Chile', 'chile.gif');
INSERT INTO `countries` VALUES (49, 'Cuba', 'cuba.gif');
INSERT INTO `countries` VALUES (50, 'Congo', 'congo.gif');
INSERT INTO `countries` VALUES (51, 'Afghanistan', 'afghanistan.gif');
INSERT INTO `countries` VALUES (52, 'Turkey', 'turkey.gif');
INSERT INTO `countries` VALUES (53, 'Uzbekistan', 'uzbekistan.gif');
INSERT INTO `countries` VALUES (54, 'Switzerland', 'switzerland.gif');
INSERT INTO `countries` VALUES (55, 'Kiribati', 'kiribati.gif');
INSERT INTO `countries` VALUES (56, 'Philippines', 'philippines.gif');
INSERT INTO `countries` VALUES (57, 'Burkina Faso', 'burkinafaso.gif');
INSERT INTO `countries` VALUES (58, 'Nigeria', 'nigeria.gif');
INSERT INTO `countries` VALUES (59, 'Iceland', 'iceland.gif');
INSERT INTO `countries` VALUES (60, 'Nauru', 'nauru.gif');
INSERT INTO `countries` VALUES (61, 'Slovenia', 'slovenia.gif');
INSERT INTO `countries` VALUES (63, 'Turkmenistan', 'turkmenistan.gif');
INSERT INTO `countries` VALUES (64, 'Bosnia Herzegovina', 'bosniaherzegovina.gif');
INSERT INTO `countries` VALUES (65, 'Andorra', 'andorra.gif');
INSERT INTO `countries` VALUES (66, 'Lithuania', 'lithuania.gif');
INSERT INTO `countries` VALUES (68, 'Netherlands Antilles', 'nethantilles.gif');
INSERT INTO `countries` VALUES (69, 'Ukraine', 'ukraine.gif');
INSERT INTO `countries` VALUES (70, 'Venezuela', 'venezuela.gif');
INSERT INTO `countries` VALUES (71, 'Hungary', 'hungary.gif');
INSERT INTO `countries` VALUES (72, 'Romania', 'romania.gif');
INSERT INTO `countries` VALUES (73, 'Vanuatu', 'vanuatu.gif');
INSERT INTO `countries` VALUES (74, 'Vietnam', 'vietnam.gif');
INSERT INTO `countries` VALUES (75, 'Trinidad & Tobago', 'trinidadandtobago.gif');
INSERT INTO `countries` VALUES (76, 'Honduras', 'honduras.gif');
INSERT INTO `countries` VALUES (77, 'Kyrgyzstan', 'kyrgyzstan.gif');
INSERT INTO `countries` VALUES (78, 'Ecuador', 'ecuador.gif');
INSERT INTO `countries` VALUES (79, 'Bahamas', 'bahamas.gif');
INSERT INTO `countries` VALUES (80, 'Peru', 'peru.gif');
INSERT INTO `countries` VALUES (81, 'Cambodia', 'cambodia.gif');
INSERT INTO `countries` VALUES (82, 'Barbados', 'barbados.gif');
INSERT INTO `countries` VALUES (83, 'Bangladesh', 'bangladesh.gif');
INSERT INTO `countries` VALUES (84, 'Laos', 'laos.gif');
INSERT INTO `countries` VALUES (85, 'Uruguay', 'uruguay.gif');
INSERT INTO `countries` VALUES (86, 'Antigua Barbuda', 'antiguabarbuda.gif');
INSERT INTO `countries` VALUES (87, 'Paraguay', 'paraguay.gif');
INSERT INTO `countries` VALUES (89, 'Thailand', 'thailand.gif');
INSERT INTO `countries` VALUES (88, 'Union of Soviet Socialist Republics', 'ussr.gif');
INSERT INTO `countries` VALUES (90, 'Senegal', 'senegal.gif');
INSERT INTO `countries` VALUES (91, 'Togo', 'togo.gif');
INSERT INTO `countries` VALUES (92, 'North Korea', 'northkorea.gif');
INSERT INTO `countries` VALUES (93, 'Croatia', 'croatia.gif');
INSERT INTO `countries` VALUES (94, 'Estonia', 'estonia.gif');
INSERT INTO `countries` VALUES (95, 'Colombia', 'colombia.gif');
INSERT INTO `countries` VALUES (96, 'Lebanon', 'lebanon.gif');
INSERT INTO `countries` VALUES (97, 'Latvia', 'latvia.gif');
INSERT INTO `countries` VALUES (98, 'Costa Rica', 'costarica.gif');
INSERT INTO `countries` VALUES (99, 'Egypt', 'egypt.gif');
INSERT INTO `countries` VALUES (100, 'Bulgaria', 'bulgaria.gif');

-- --------------------------------------------------------

--
-- Table structure for table `files`
--

CREATE TABLE `files` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `torrent` int(10) unsigned NOT NULL DEFAULT '0',
  `filename` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `size` bigint(20) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `torrent` (`torrent`),
  FULLTEXT KEY `filename` (`filename`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `friends`
--

CREATE TABLE `friends` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `userid` int(10) unsigned NOT NULL DEFAULT '0',
  `friendid` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `userfriend` (`userid`,`friendid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `news`
--

CREATE TABLE `news` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `userid` int(11) NOT NULL DEFAULT '0',
  `added` int(11) NOT NULL,
  `body` text COLLATE utf8_unicode_ci NOT NULL,
  `headline` varchar(150) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'TBDEV.NET News',
  PRIMARY KEY (`id`),
  KEY `added` (`added`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `peers`
--

CREATE TABLE `peers` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `torrent` int(10) unsigned NOT NULL DEFAULT '0',
  `passkey` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `peer_id` varchar(20) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `ip` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `port` smallint(5) unsigned NOT NULL DEFAULT '0',
  `uploaded` bigint(20) unsigned NOT NULL DEFAULT '0',
  `downloaded` bigint(20) unsigned NOT NULL DEFAULT '0',
  `to_go` bigint(20) unsigned NOT NULL DEFAULT '0',
  `seeder` enum('yes','no') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'no',
  `started` int(11) NOT NULL,
  `last_action` int(11) NOT NULL,
  `connectable` enum('yes','no') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'yes',
  `userid` int(10) unsigned NOT NULL DEFAULT '0',
  `agent` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
  `finishedat` int(10) unsigned NOT NULL DEFAULT '0',
  `downloadoffset` bigint(20) unsigned NOT NULL DEFAULT '0',
  `uploadoffset` bigint(20) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `torrent_peer_id` (`torrent`,`peer_id`),
  KEY `torrent` (`torrent`),
  KEY `torrent_seeder` (`torrent`,`seeder`),
  KEY `last_action` (`last_action`),
  KEY `connectable` (`connectable`),
  KEY `userid` (`userid`),
  KEY `passkey` (`passkey`),
  KEY `torrent_connect` (`torrent`,`connectable`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reputation`
--

CREATE TABLE `reputation` (
  `reputationid` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `reputation` int(10) NOT NULL DEFAULT '0',
  `whoadded` int(10) NOT NULL DEFAULT '0',
  `reason` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `dateadd` int(10) NOT NULL DEFAULT '0',
  `postid` int(10) NOT NULL DEFAULT '0',
  `userid` mediumint(8) NOT NULL DEFAULT '0',
  PRIMARY KEY (`reputationid`),
  KEY `userid` (`userid`),
  KEY `whoadded` (`whoadded`),
  KEY `multi` (`postid`,`userid`),
  KEY `dateadd` (`dateadd`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reputationlevel`
--

CREATE TABLE `reputationlevel` (
  `reputationlevelid` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `minimumreputation` int(10) NOT NULL DEFAULT '0',
  `level` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`reputationlevelid`),
  KEY `reputationlevel` (`minimumreputation`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


INSERT INTO `reputationlevel` VALUES (1, -999999, 'is infamous around these parts');
INSERT INTO `reputationlevel` VALUES (2, -50, 'can only hope to improve');
INSERT INTO `reputationlevel` VALUES (3, -10, 'has a little shameless behaviour in the past');
INSERT INTO `reputationlevel` VALUES (4, 0, 'is an unknown quantity at this point');
INSERT INTO `reputationlevel` VALUES (5, 10, 'is on a distinguished road');
INSERT INTO `reputationlevel` VALUES (6, 50, 'will become famous soon enough');
INSERT INTO `reputationlevel` VALUES (7, 150, 'has a spectacular aura about');
INSERT INTO `reputationlevel` VALUES (8, 250, 'is a jewel in the rough');
INSERT INTO `reputationlevel` VALUES (9, 350, 'is just really nice');
INSERT INTO `reputationlevel` VALUES (10, 450, 'is a glorious beacon of light');
INSERT INTO `reputationlevel` VALUES (11, 550, 'is a name known to all');
INSERT INTO `reputationlevel` VALUES (12, 650, 'is a splendid one to behold');
INSERT INTO `reputationlevel` VALUES (13, 1000, 'has much to be proud of');
INSERT INTO `reputationlevel` VALUES (14, 1500, 'has a brilliant future');
INSERT INTO `reputationlevel` VALUES (15, 2000, 'has a reputation beyond repute');

-- --------------------------------------------------------

--
-- Table structure for table `searchcloud`
--

CREATE TABLE `searchcloud` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `searchedfor` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `howmuch` int(10) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `searchedfor` (`searchedfor`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `searchcloud` VALUES (1, 'bob', 1);
INSERT INTO `searchcloud` VALUES (2, 'testing', 4);
INSERT INTO `searchcloud` VALUES (3, 'blackadder', 1);
INSERT INTO `searchcloud` VALUES (4, '24', 2);
-- --------------------------------------------------------

--
-- Table structure for table `sitelog`
--

CREATE TABLE `sitelog` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `added` int(11) NOT NULL,
  `txt` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `added` (`added`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `stylesheets`
--

CREATE TABLE `stylesheets` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uri` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `stylesheets` VALUES (1, '1.css', '(default)');
INSERT INTO `stylesheets` VALUES (2, '2.css', 'Large text');

-- --------------------------------------------------------

--
-- Table structure for table `torrents`
--

CREATE TABLE `torrents` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `info_hash` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `filename` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `save_as` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `search_text` text COLLATE utf8_unicode_ci NOT NULL,
  `descr` text COLLATE utf8_unicode_ci NOT NULL,
  `ori_descr` text COLLATE utf8_unicode_ci NOT NULL,
  `category` int(10) unsigned NOT NULL DEFAULT '0',
  `size` bigint(20) unsigned NOT NULL DEFAULT '0',
  `added` int(11) NOT NULL,
  `type` enum('single','multi') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'single',
  `numfiles` int(10) unsigned NOT NULL DEFAULT '0',
  `comments` int(10) unsigned NOT NULL DEFAULT '0',
  `views` int(10) unsigned NOT NULL DEFAULT '0',
  `hits` int(10) unsigned NOT NULL DEFAULT '0',
  `times_completed` int(10) unsigned NOT NULL DEFAULT '0',
  `leechers` int(10) unsigned NOT NULL DEFAULT '0',
  `seeders` int(10) unsigned NOT NULL DEFAULT '0',
  `last_action` int(11) NOT NULL,
  `visible` enum('yes','no') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'yes',
  `banned` enum('yes','no') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'no',
  `owner` int(10) unsigned NOT NULL DEFAULT '0',
  `numratings` int(10) unsigned NOT NULL DEFAULT '0',
  `ratingsum` int(10) unsigned NOT NULL DEFAULT '0',
  `nfo` text COLLATE utf8_unicode_ci NOT NULL,
  `client_created_by` char(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'unknown',
  PRIMARY KEY (`id`),
  UNIQUE KEY `info_hash` (`info_hash`),
  KEY `owner` (`owner`),
  KEY `visible` (`visible`),
  KEY `category_visible` (`category`,`visible`),
  FULLTEXT KEY `ft_search` (`search_text`,`ori_descr`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(13) unsigned DEFAULT '0',
  `username` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `passhash` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `secret` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `passkey` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `email` varchar(80) COLLATE utf8_unicode_ci NOT NULL,
  `status` enum('pending','confirmed') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'pending',
  `added` int(11) NOT NULL,
  `last_login` int(11) NOT NULL,
  `last_access` int(11) NOT NULL,
  `editsecret` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `privacy` enum('strong','normal','low') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'normal',
  `stylesheet` int(10) DEFAULT '1',
  `info` text COLLATE utf8_unicode_ci,
  `acceptpms` enum('yes','friends','no') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'yes',
  `ip` varchar(15) COLLATE utf8_unicode_ci NOT NULL,
  `class` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `language` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'en',
  `avatar` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `av_w` smallint(3) unsigned NOT NULL DEFAULT '0',
  `av_h` smallint(3) unsigned NOT NULL DEFAULT '0',
  `uploaded` bigint(20) unsigned NOT NULL DEFAULT '0',
  `downloaded` bigint(20) unsigned NOT NULL DEFAULT '0',
  `title` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `country` int(10) unsigned NOT NULL DEFAULT '0',
  `notifs` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `modcomment` text COLLATE utf8_unicode_ci NOT NULL,
  `enabled` enum('yes','no') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'yes',
  `avatars` enum('yes','no') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'yes',
  `donor` enum('yes','no') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'no',
  `warned` enum('yes','no') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'no',
  `warneduntil` int(11) NOT NULL DEFAULT '0',
  `torrentsperpage` int(3) unsigned NOT NULL DEFAULT '0',
  `topicsperpage` int(3) unsigned NOT NULL DEFAULT '0',
  `postsperpage` int(3) unsigned NOT NULL DEFAULT '0',
  `deletepms` enum('yes','no') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'yes',
  `savepms` enum('yes','no') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'no',
  `reputation` int(10) NOT NULL DEFAULT '10',
  `time_offset` varchar(5) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `dst_in_use` tinyint(1) NOT NULL DEFAULT '0',
  `auto_correct_dst` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  KEY `ip` (`ip`),
  KEY `uploaded` (`uploaded`),
  KEY `downloaded` (`downloaded`),
  KEY `country` (`country`),
  KEY `last_access` (`last_access`),
  KEY `enabled` (`enabled`),
  KEY `warned` (`warned`),
  KEY `pkey` (`passkey`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

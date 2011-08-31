<?php
/**
 * @package     xortify
 * @subpackage  module
 * @description	Sector Network Security Drone
 * @author	    Simon Roberts WISHCRAFT <simon@chronolabs.coop>
 * @copyright	copyright (c) 2010-2013 XOOPS.org
 * @licence		GPL 2.0 - see docs/LICENCE.txt
 */

function xoops_module_update_tb(&$module) {
	
	$sql[] = "RENAME TABLE `".$GLOBALS['xoopsDB']->prefix('avps')."` TO `".$GLOBALS['xoopsDB']->prefix('tb_avps')."`";
	$sql[] = "RENAME TABLE `".$GLOBALS['xoopsDB']->prefix('bans')."` TO `".$GLOBALS['xoopsDB']->prefix('tb_bans')."`";
	$sql[] = "RENAME TABLE `".$GLOBALS['xoopsDB']->prefix('blocks')."` TO `".$GLOBALS['xoopsDB']->prefix('tb_blocks')."`";
	$sql[] = "RENAME TABLE `".$GLOBALS['xoopsDB']->prefix('categories')."` TO `".$GLOBALS['xoopsDB']->prefix('tb_categories')."`";
	$sql[] = "RENAME TABLE `".$GLOBALS['xoopsDB']->prefix('countries')."` TO `".$GLOBALS['xoopsDB']->prefix('tb_countries')."`";
	$sql[] = "RENAME TABLE `".$GLOBALS['xoopsDB']->prefix('files')."` TO `".$GLOBALS['xoopsDB']->prefix('tb_files')."`";
	$sql[] = "RENAME TABLE `".$GLOBALS['xoopsDB']->prefix('friends')."` TO `".$GLOBALS['xoopsDB']->prefix('tb_friends')."`";
	$sql[] = "RENAME TABLE `".$GLOBALS['xoopsDB']->prefix('news')."` TO `".$GLOBALS['xoopsDB']->prefix('tb_news')."`";
	$sql[] = "RENAME TABLE `".$GLOBALS['xoopsDB']->prefix('peers')."` TO `".$GLOBALS['xoopsDB']->prefix('tb_peers')."`";								
	$sql[] = "RENAME TABLE `".$GLOBALS['xoopsDB']->prefix('reputation')."` TO `".$GLOBALS['xoopsDB']->prefix('tb_reputation')."`";
	$sql[] = "RENAME TABLE `".$GLOBALS['xoopsDB']->prefix('reputationlevel')."` TO `".$GLOBALS['xoopsDB']->prefix('tb_reputationlevel')."`";
	$sql[] = "RENAME TABLE `".$GLOBALS['xoopsDB']->prefix('searchcloud')."` TO `".$GLOBALS['xoopsDB']->prefix('tb_searchcloud')."`";
	$sql[] = "RENAME TABLE `".$GLOBALS['xoopsDB']->prefix('stylesheets')."` TO `".$GLOBALS['xoopsDB']->prefix('tb_stylesheets')."`";
	$sql[] = "RENAME TABLE `".$GLOBALS['xoopsDB']->prefix('sitelog')."` TO `".$GLOBALS['xoopsDB']->prefix('tb_sitelog')."`";
	$sql[] = "RENAME TABLE `".$GLOBALS['xoopsDB']->prefix('torrents')."` TO `".$GLOBALS['xoopsDB']->prefix('tb_torrents')."`";	
	
	$sql[] = "CREATE TABLE `".$GLOBALS['xoopsDB']->prefix('tb_trackers')."` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `added` int(11) NOT NULL,
  `tracker` varchar(500),
  PRIMARY KEY (`id`),
  KEY `added` (`added`, `tracker`(25))
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

	$sql[] = "CREATE TABLE `".$GLOBALS['xoopsDB']->prefix('tb_trackers_to_torrents')."` (
  `id` INT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `tracker_id` INT(10) UNSIGNED NOT NULL,
  `torrent_id` INT(10) UNSIGNED NOT NULL,
  `seeders` INT(10) UNSIGNED NOT NULL,
  `leechers` INT(10) UNSIGNED NOT NULL,
  `completed` INT(10) UNSIGNED NOT NULL,
  `lastchecked` INT(12) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `common` (`tracker_id`,`torrent_id`,`lastchecked`)
) ENGINE=INNODB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
	
	foreach($sql as $id => $question)
		if ($GLOBALS['xoopsDB']->queryF($question))
			xoops_error($question, 'SQL Executed Successfully!!!');
			
	xoops_load("xoopscache");	
	XoopsCache::delete('xortify_bans_protector');
	return true;				
}

?>
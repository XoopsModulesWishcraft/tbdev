<?php

	require_once '../../mainfile.php';
	require_once "include/functions.php";
	require_once "include/form.objects.php";
	
	$avps_handler = xoops_getmodulehandler('avps', 'tb');
	$bans_handler = xoops_getmodulehandler('bans', 'tb');
	$categories_handler = xoops_getmodulehandler('categories', 'tb');
	$countries_handler = xoops_getmodulehandler('countries', 'tb');
	$files_handler = xoops_getmodulehandler('files', 'tb');
	$friends_handler = xoops_getmodulehandler('friends', 'tb');
	$news_handler = xoops_getmodulehandler('news', 'tb');
	$peers_handler = xoops_getmodulehandler('peers', 'tb');
	$reputation_handler = xoops_getmodulehandler('reputation', 'tb');
	$reputationlevel_handler = xoops_getmodulehandler('reputationlevel', 'tb');
	$searchcloud_handler = xoops_getmodulehandler('searchcloud', 'tb');
	$sitelog_handler = xoops_getmodulehandler('sitelog', 'tb');
	$stylesheets_handler = xoops_getmodulehandler('stylesheets', 'tb');
	$torrents_handler = xoops_getmodulehandler('torrents', 'tb');
	$trackers_handler = xoops_getmodulehandler('trackers', 'tb');
	$trackerstotorrents_handler = xoops_getmodulehandler('trackerstotorrents', 'tb');
	$users_handler = xoops_getmodulehandler('users', 'tb');
    
    if (!strpos($_SERVER['PHP_SELF'], 'login.php')&&!strpos($_SERVER['PHP_SELF'], 'logout.php'))	
		loggedinorreturn();

    $GLOBALS['lang'] = array_merge( load_language('global') );
    
?>
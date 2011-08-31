<?php
/*
+------------------------------------------------
|   TBDev.net BitTorrent Tracker PHP
|   =============================================
|   by CoLdFuSiOn
|   (c) 2003 - 2009 TBDev.Net
|   http://www.tbdev.net
|   =============================================
|   svn: http://sourceforge.net/projects/tbdevnet/
|   Licence Info: GPL
+------------------------------------------------
|   $Date$
|   $Revision$
|   $Author$
|   $URL$
+------------------------------------------------
*/
require_once "../../mainfile.php";
require "include/bittorrent.php";
require_once "include/html_functions.php";
require_once "include/user_functions.php";

dbconn();
    
    $GLOBALS['lang'] = array_merge( load_language('global'), load_language('useragreement') );
    
    $xoopsOption['template_main'] = 'tb_useragreement.html';
	include $GLOBALS['xoops']->path('header.php');
	$GLOBALS['xoopsTpl']->assign('xoConfig', $GLOBALS['TBDEV']);
	$GLOBALS['xoopsTpl']->assign('xoops_pagetitle', $GLOBALS['lang']['stdhead_usragrmnt']);
	include $GLOBALS['xoops']->path('footer.php');
    
?>
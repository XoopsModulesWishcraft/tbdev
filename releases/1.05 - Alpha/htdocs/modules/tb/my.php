<?php
/*
+------------------------------------------------
|   TBDev.net BitTorrent Tracker PHP for XOOPS
|   =============================================
|   by CoLdFuSiOn
|   (c) 2003 - 2009 TBDev.Net
|   http://www.tbdev.net
|   =============================================
|   svn: http://sourceforge.net/projects/tbdevnet/
|   Licence Info: GPL
+------------------------------------------------
|   2011-09-05 12:20 AM AEST
|   1.05
|   Wishcraft
|   http://chronolabs.coop/
+------------------------------------------------
*/

	require_once 'header.php';
	
	$lang = array_merge( load_language('global'), load_language('my') );
	
	$xoopsOption['template_main'] = 'tb_my.html';
	stdhead($title, '', '', 0);
	$GLOBALS['xoopsTpl']->assign('form', $GLOBALS['CURUSER']['object']->getProfileForm());
    stdfoot();
    
?>
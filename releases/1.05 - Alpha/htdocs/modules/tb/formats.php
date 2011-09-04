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

$GLOBALS['lang'] = array_merge( $GLOBALS['lang'], load_language('formats') );
    
$xoopsOption['template_main'] = 'tb_formats.html';
stdhead($title, '', '', 0);
$GLOBALS['xoopsTpl']->assign('xoConfig', $GLOBALS['TBDEV']);
stdfoot();
	
?>
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
require_once '../../mainfile.php';
require "include/bittorrent.php";
require "include/user_functions.php";

dbconn(false);

$GLOBALS['lang'] = array_merge( load_language('global'), load_language('formats') );
    
$xoopsOption['template_main'] = 'tb_formats.html';
include $GLOBALS['xoops']->path('header.php');
$GLOBALS['xoopsTpl']->assign('xoConfig', $GLOBALS['TBDEV']);
include $GLOBALS['xoops']->path('footer.php');
	
?>
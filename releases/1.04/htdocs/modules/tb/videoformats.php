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
require "include/user_functions.php";

dbconn(false);

    $GLOBALS['lang'] = array_merge( load_language('global'), load_language('videoformats') );
    
    $xoopsOption['template_main'] = 'tb_videoformats.html';
	include $GLOBALS['xoops']->path('header.php');
	$GLOBALS['xoopsTpl']->assign('xoConfig', $GLOBALS['TBDEV']);
	$GLOBALS['xoopsTpl']->assign('xoops_pagetitle', $GLOBALS['lang']['videoformats_header']);
	include $GLOBALS['xoops']->path('footer.php');

?>
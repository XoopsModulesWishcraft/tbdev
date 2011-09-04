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

loggedinorreturn();

    $GLOBALS['lang'] = array_merge( $GLOBALS['lang'], load_language('mytorrents') );
    $GLOBALS['lang'] = array_merge( $GLOBALS['lang'], load_language( 'torrenttable_functions' ));
    $GLOBALS['lang'] = array_merge( $GLOBALS['lang'], load_language( 'formats' ));
    $HTMLOUT = '';

    $where = "WHERE owner = " . $GLOBALS['CURUSER']["id"] . " AND banned != 'yes'";
    $res = $GLOBALS['xoopsDB']->queryF("SELECT COUNT(*) FROM ".$GLOBALS['xoopsDB']->prefix("tb_torrents")." $where");
    $row = mysql_fetch_array($res,MYSQL_NUM);
    $count = $row[0];

    $browse = $torrents_handler->getMyTorrents((isset($_REQUEST['start'])?$_REQUEST['start']:0), (isset($_REQUEST['limit'])?$_REQUEST['limit']:30), true, $where);
    
    $xoopsOption['template_main'] = 'tb_mytorrents.html';
	stdhead($title, '', '', 0);
	$GLOBALS['xoopsTpl']->assign('xoConfig', $GLOBALS['TBDEV']);
	$GLOBALS['xoopsTpl']->assign('count', $count);
	$GLOBALS['xoopsTpl']->assign('torrents', $browse['torrents']);
	$GLOBALS['xoopsTpl']->assign('pagenav', $browse['pagenav']['data']);
    $GLOBALS['xoopsTpl']->assign('xoops_pagetitle', $GLOBALS['CURUSER']["username"] . "'s torrents");
	stdfoot();
?>
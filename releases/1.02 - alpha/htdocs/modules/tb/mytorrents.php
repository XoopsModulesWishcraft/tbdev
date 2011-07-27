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
require_once "include/bittorrent.php";
require_once "include/html_functions.php";
require_once "include/user_functions.php";
require_once "include/pager_functions.php";
require_once "include/torrenttable_functions.php";

dbconn(false);

loggedinorreturn();

    $GLOBALS['lang'] = array_merge( load_language('global'), load_language('mytorrents') );
    $GLOBALS['lang'] = array_merge( $GLOBALS['lang'], load_language( 'torrenttable_functions' ));
    $HTMLOUT = '';

    $where = "WHERE owner = " . $GLOBALS['CURUSER']["id"] . " AND banned != 'yes'";
    $res = $GLOBALS['xoopsDB']->queryF("SELECT COUNT(*) FROM ".$GLOBALS['xoopsDB']->prefix("torrents")." $where");
    $row = mysql_fetch_array($res,MYSQL_NUM);
    $count = $row[0];

    $xoopsOption['template_main'] = 'tb_formats.html';
	include $GLOBALS['xoops']->path('header.php');
	$GLOBALS['xoopsTpl']->assign('xoConfig', $GLOBALS['TBDEV']);
	$GLOBALS['xoopsTpl']->assign('count', $count);


    if ($count) 
    {
      $pager = pager(20, $count, "mytorrents.php?");

      $res = $GLOBALS['xoopsDB']->queryF("SELECT torrents.type, torrents.comments, torrents.leechers, torrents.seeders, IF(torrents.numratings < {$GLOBALS['TBDEV']['minvotes']}, NULL, ROUND(torrents.ratingsum / torrents.numratings, 1)) AS rating, torrents.id, categories.name AS cat_name, categories.image AS cat_pic, torrents.name, save_as, numfiles, added, size, views, visible, hits, times_completed, category FROM ".$GLOBALS['xoopsDB']->prefix("torrents")." LEFT JOIN ".$GLOBALS['xoopsDB']->prefix("categories")." ON torrents.category = categories.id $where ORDER BY id DESC ".$pager['limit']);

      $GLOBALS['xoopsTpl']->assign('pager', $pager);
      $GLOBALS['xoopsTpl']->assign('torrenttable', torrenttable($res, "mytorrents"));
    }

    $GLOBALS['xoopsTpl']->assign('xoops_pagetitle', $GLOBALS['CURUSER']["username"] . "'s torrents");
	include $GLOBALS['xoops']->path('footer.php');
?>
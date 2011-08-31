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
require "include/bbcode_functions.php";
dbconn(false);
loggedinorreturn();

    $GLOBALS['lang'] = array_merge( load_language('global'), load_language('viewnfo') );
    
    $id = 0 + $_GET["id"];
    if ($GLOBALS['CURUSER']['class'] < UC_POWER_USER || !is_valid_id($id))
      die;

    $r = $GLOBALS['xoopsDB']->queryF("SELECT name,nfo FROM ".$GLOBALS['xoopsDB']->prefix("tb_torrents")." WHERE id=$id") or sqlerr();
    $a = $GLOBALS['xoopsDB']->fetchArray($r) or die("{$GLOBALS['lang']['text_puke']}");
    //$nfo = htmlspecialchars($a["nfo"]);
    
    $xoopsOption['template_main'] = 'tb_viewnfo.html';
	include $GLOBALS['xoops']->path('header.php');
	$GLOBALS['xoopsTpl']->assign('xoConfig', $GLOBALS['TBDEV']);
	$GLOBALS['xoopsTpl']->assign('a', $a);
	$GLOBALS['xoopsTpl']->assign('id', $id);
	include $GLOBALS['xoops']->path('footer.php');
    
?>
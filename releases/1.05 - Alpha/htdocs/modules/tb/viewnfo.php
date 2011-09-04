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

    $GLOBALS['lang'] = array_merge( $GLOBALS['lang'], load_language('viewnfo') );
    
    $id = 0 + $_GET["id"];
    if ($GLOBALS['CURUSER']['class'] < UC_POWER_USER || !is_valid_id($id))
      die;

    $r = $GLOBALS['xoopsDB']->queryF("SELECT name,nfo FROM ".$GLOBALS['xoopsDB']->prefix("tb_torrents")." WHERE id=$id") or sqlerr();
    $a = $GLOBALS['xoopsDB']->fetchArray($r) or die("{$GLOBALS['lang']['text_puke']}");
    //$nfo = htmlspecialchars($a["nfo"]);
    
    $xoopsOption['template_main'] = 'tb_viewnfo.html';
	stdhead($title, '', '', 0);
	$GLOBALS['xoopsTpl']->assign('xoConfig', $GLOBALS['TBDEV']);
	$GLOBALS['xoopsTpl']->assign('a', $a);
	$GLOBALS['xoopsTpl']->assign('id', $id);
	stdfoot();
    
?>
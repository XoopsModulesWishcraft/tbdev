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
	
	if (!mkglobal("id"))
		die();
	
	$id = 0 + $id;
	if (!$id)
		die();


	loggedinorreturn();

    $GLOBALS['lang'] = array_merge( $GLOBALS['lang'], load_language('edit') );
    
    $torrents_handler = xoops_getmodulehandler('torrents', 'td');
    $torrent = $torrents_handler->get($id);

    if (!is_object($torrent))
      stderr($GLOBALS['lang']['edit_user_error'], $GLOBALS['lang']['edit_no_torrent']);
    
    if (!isset($GLOBALS['CURUSER']) || ($GLOBALS['CURUSER']["id"] != $torrent->getVar("owner") && get_user_class() < UC_MODERATOR))   {
      stderr($GLOBALS['lang']['edit_user_error'], sprintf($GLOBALS['lang']['edit_no_permission'], urlencode($_SERVER['REQUEST_URI'])));
    }

    $xoopsOption['template_main'] = 'tb_edit.html';
	stdhead($title, '', '', 0);
	$GLOBALS['xoopsTpl']->assign('xoConfig', $GLOBALS['TBDEV']);
	$GLOBALS['xoopsTpl']->assign('id', $id);
	$GLOBALS['xoopsTpl']->assign('form', $torrent->getForm());
	stdfoot();
    
?>
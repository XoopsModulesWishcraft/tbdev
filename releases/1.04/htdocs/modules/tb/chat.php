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
require_once 'include/bittorrent.php';
require_once 'include/user_functions.php';

dbconn();

loggedinorreturn();
    
    $GLOBALS['lang'] = array_merge( load_language('global'), load_language('chat') );
    
    $nick = ($GLOBALS['CURUSER'] ? $GLOBALS['CURUSER']['username'] : ('Guest' . rand(1000, 9999)));

    if (is_object($GLOBALS['xoopsUser']))
    	$fullname = $GLOBALS['xoopsUser']->getVar('name');
    	
	$xoopsOption['template_main'] = 'tb_chat.html';
	include $GLOBALS['xoops']->path('header.php');
	$GLOBALS['xoopsTpl']->assign('xoConfig', $GLOBALS['TBDEV']);
	$GLOBALS['xoopsTpl']->assign('nick', $nick);
	$GLOBALS['xoopsTpl']->assign('fullname', $fullname);
	include $GLOBALS['xoops']->path('footer.php');
    
?>
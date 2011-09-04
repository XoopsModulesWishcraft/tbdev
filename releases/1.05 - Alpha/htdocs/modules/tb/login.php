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

if (is_object($GLOBALS['xoopsUser'])) {
	$users_handler->userlogin();
	$returnto = $_REQUEST['returnto'];
	if (!empty($returnto))
    	header("Location: ".$returnto);
    else
    	header("Location: {$TBDEV['baseurl']}/my.php");  
} else {
	redirect_header(XOOPS_URL.'/user.php', 10, 'You have to log-in first!');
}
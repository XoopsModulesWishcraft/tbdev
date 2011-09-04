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

// Include the random string file
//require 'rand.php';
$str = '';
	for($i=0; $i<6; $i++){
$str .= chr(rand(0,25)+65);
}

// Begin a new session
session_start();

// Set the session contents
$_SESSION['captcha_id'] = $str;

?>
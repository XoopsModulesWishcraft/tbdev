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

// Begin the session
session_start();

// To avoid case conflicts, make the input uppercase and check against the session value
// If it's correct, echo '1' as a string
if(strtoupper($_GET['captcha']) == $_SESSION['captcha_id'])
	echo '1';
// Else echo '0' as a string
else
	echo '0';

?>
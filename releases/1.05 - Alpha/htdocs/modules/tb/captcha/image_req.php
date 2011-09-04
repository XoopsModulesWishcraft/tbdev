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

// Echo the image - timestamp appended to prevent caching
echo '<a href="index.php" onclick="refreshimg(); return false;" title="Click to refresh image"><img class="cimage" src="captcha/GD_Security_image.php?' . time() . '" alt="Captcha image" /></a>';

?>
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

if ( ! defined( 'IN_TBDEV_ADMIN' ) )
{
	print "<h1>{$GLOBALS['lang']['text_incorrect']}</h1>{$GLOBALS['lang']['text_cannot']}";
	exit();
}

require_once "include/user_functions.php";

    $GLOBALS['lang'] = array_merge( $GLOBALS['lang'], load_language('ad_docleanup') );
    
    if( get_user_class() != UC_SYSOP )
      stderr("{$GLOBALS['lang']['stderr_error']}", "{$GLOBALS['lang']['text_denied']}");
      
    //docleanup();
    register_shutdown_function("docleanup");

    stderr("{$GLOBALS['lang']['text_done']}", "{$GLOBALS['lang']['text_done']}");

?>

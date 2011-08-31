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
	xoops_cp_footer();
	exit();
}

include('header.php');
xoops_cp_header();
loadModuleAdminMenu(11);
include_once $GLOBALS['xoops']->path( "/class/template.php" );
$GLOBALS['tbTpl'] = new XoopsTpl();
$GLOBALS['tbTpl']->assign('php_self', $_SERVER['PHP_SELF']);

xoops_loadLanguage('admin', 'tb');

require_once "include/user_functions.php";

    $GLOBALS['lang'] = array_merge( $GLOBALS['lang'], load_language('ad_docleanup') );
    
    if( get_user_class() != UC_SYSOP )
      stderr("{$GLOBALS['lang']['stderr_error']}", "{$GLOBALS['lang']['text_denied']}");
      
    //docleanup();
    register_shutdown_function("docleanup");

    stderr("{$GLOBALS['lang']['text_done']}", "{$GLOBALS['lang']['text_done']}");

    
xoops_cp_footer();
exit(0);
?>

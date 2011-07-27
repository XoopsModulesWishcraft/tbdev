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
require "include/bittorrent.php";
require_once "include/html_functions.php";
require_once "include/user_functions.php";

dbconn();
    
    $GLOBALS['lang'] = array_merge( load_language('global'), load_language('useragreement') );
    
    $HTMLOUT = '';
    
    $HTMLOUT .= begin_main_frame();
    $HTMLOUT .= begin_frame($GLOBALS['TBDEV']['site_name']." {$GLOBALS['lang']['frame_usragrmnt']}");

    $HTMLOUT .= "<p></p> {$GLOBALS['lang']['text_usragrmnt']}"; 

    $HTMLOUT .= end_frame();
    $HTMLOUT .= end_main_frame();
    print stdhead("{$GLOBALS['lang']['stdhead_usragrmnt']}") . $HTMLOUT . stdfoot();
?>
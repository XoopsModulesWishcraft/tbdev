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
require "include/user_functions.php";
require "include/bbcode_functions.php";
dbconn(false);
loggedinorreturn();

    $GLOBALS['lang'] = array_merge( load_language('global'), load_language('viewnfo') );
    
    $id = 0 + $_GET["id"];
    if ($GLOBALS['CURUSER']['class'] < UC_POWER_USER || !is_valid_id($id))
      die;

    $r = $GLOBALS['xoopsDB']->queryF("SELECT name,nfo FROM ".$GLOBALS['xoopsDB']->prefix("torrents")." WHERE id=$id") or sqlerr();
    $a = $GLOBALS['xoopsDB']->fetchArray($r) or die("{$GLOBALS['lang']['text_puke']}");
    //$nfo = htmlspecialchars($a["nfo"]);
    $HTMLOUT = '';
    
    
    $HTMLOUT .= "<h1>{$GLOBALS['lang']['text_nfofor']}<a href='details.php?id=$id'>".htmlspecialchars($a['name'])."</a></h1>\n";
    $HTMLOUT .= "<table border='1' cellspacing='0' cellpadding='5'><tr><td class='text'>\n";
    $HTMLOUT .= "<pre>" . format_urls(htmlentities($a['nfo'], ENT_QUOTES, 'UTF-8')) . "</pre>\n";
    $HTMLOUT .= "</td></tr></table>\n";
    $HTMLOUT .= "<p align='center'>{$GLOBALS['lang']['text_forbest']}" .
      "<a href='ftp://{$_SERVER['HTTP_HOST']}/misc/linedraw.ttf'>{$GLOBALS['lang']['text_linedraw']}</a>{$GLOBALS['lang']['text_font']}</p>\n";
    
    print stdhead() . $HTMLOUT . stdfoot();
?>
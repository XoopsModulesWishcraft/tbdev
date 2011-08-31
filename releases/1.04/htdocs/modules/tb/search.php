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
require_once "../../mainfile.php";
require_once "include/bittorrent.php";
require_once "include/user_functions.php";

dbconn();
loggedinorreturn();

    $GLOBALS['lang'] = array_merge( load_language('global'), load_language('search') );
    

    $cats = genrelist();
    $catdropdown = "";
    foreach ($cats as $cat) {
        $catdropdown .= "<option value=\"" . $cat["id"] . "\"";
        $getcat = (isset($_GET["cat"])?$_GET["cat"]:'');
        if ($cat["id"] == $getcat)
            $catdropdown .= " selected='selected'";
        $catdropdown .= ">" . htmlspecialchars($cat["name"]) . "</option>\n";
    }

    $deadchkbox = "<input type='checkbox' name='incldead' value='1'";
    if (isset($_GET["incldead"]))
        $deadchkbox .= " checked='checked'";
    $deadchkbox .= " /> {$GLOBALS['lang']['search_inc_dead']}";


   	$xoopsOption['template_main'] = 'tb_search.html';
	include $GLOBALS['xoops']->path('header.php');
	$GLOBALS['xoopsTpl']->assign('xoConfig', $GLOBALS['TBDEV']);
	$GLOBALS['xoopsTpl']->assign('catdropdown', $catdropdown);
	$GLOBALS['xoopsTpl']->assign('deadchkbox', $deadchkbox);
	$GLOBALS['xoopsTpl']->assign('xoops_pagetitle', $GLOBALS['lang']['search_search']);
		
	include $GLOBALS['xoops']->path('footer.php');



?>
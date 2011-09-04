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
loggedinorreturn();

    $GLOBALS['lang'] = array_merge( $GLOBALS['lang'], load_language('search') );
    

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
	stdhead($title, '', '', 0);
	$GLOBALS['xoopsTpl']->assign('xoConfig', $GLOBALS['TBDEV']);
	$GLOBALS['xoopsTpl']->assign('catdropdown', $catdropdown);
	$GLOBALS['xoopsTpl']->assign('deadchkbox', $deadchkbox);
	$GLOBALS['xoopsTpl']->assign('xoops_pagetitle', $GLOBALS['lang']['search_search']);
		
	stdfoot();



?>
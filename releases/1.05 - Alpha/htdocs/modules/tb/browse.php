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
|   2011-09-01
|   0.01
|   Wishcraft
|   http://chronolabs.coop/
+------------------------------------------------
*/
	require_once 'header.php';
	
	$torrents_handler = xoops_getmodulehandler('torrents', 'td');
	
	loggedinorreturn();
	
	$GLOBALS['lang'] = array_merge( load_language('global'), load_language('browse'), load_language('torrenttable_functions') );
	   
	$cats = genrelist();
	
	$browse = $torrents_handler->getBrowse($_GET, $cats);
	    
    if (isset($GLOBALS['cleansearchstr']))
      $title = "{$GLOBALS['lang']['browse_search']}\"".$_GET["search"]."\"";
    else
      $title = '';


	$xoopsOption['template_main'] = 'tb_browse.html';
	stdhead($title, '', '', 0);
	$GLOBALS['xoopsTpl']->assign('xoConfig', $GLOBALS['TBDEV']);
	$GLOBALS['xoopsTpl']->assign('xoops_pagetitle', $title);
    $GLOBALS['xoopsTpl']->assign('lastrowcols', count($cats)%$GLOBALS['xoopsModuleConfig']['catsperrow']);
	$GLOBALS['xoopsTpl']->assign('npos', $GLOBALS['xoopsModuleConfig']['catsperrow']-(count($cats)%$GLOBALS['xoopsModuleConfig']['catsperrow']));
	$GLOBALS['xoopsTpl']->assign('rowspan', $GLOBALS['xoopsModuleConfig']['catsperrow']-(count($cats)%$GLOBALS['xoopsModuleConfig']['catsperrow'])-1);
	$GLOBALS['xoopsTpl']->assign('cloud', cloud());
	$GLOBALS['xoopsTpl']->assign('cats', $cats);
    $GLOBALS['xoopsTpl']->assign('torrenttable', torrenttable($browse));
    $GLOBALS['xoopsTpl']->assign('pager', $browse['pagenav']['data']);
	$GLOBALS['xoopsTpl']->assign('searchstr', htmlentities($_GET["search"], ENT_QUOTES));
	stdfoot()
    
?>
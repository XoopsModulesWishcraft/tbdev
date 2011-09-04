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

    $GLOBALS['lang'] = array_merge( $GLOBALS['lang'], load_language('index') );
    
    $HTMLOUT = '';

    $users_handler = xoops_getmodulehandler('users', 'tb');
    $torrents_handler = xoops_getmodulehandler('torrents', 'tb');
    $avps_handler = xoops_getmodulehandler('avps', 'tb');
    
    $registered = number_format($users_handler->getCount());
    $torrents = number_format($torrents_handler->getCount());

    $criteria= new Criteria('arg','seeders');
    $avps = $avps_handler->getObjects($criteria, false);
    if (is_object($avps[0]))
	    $seeders = 0 + $avps[0]->getVar('value_u');
	else 
	    $seeders = 0;
	    
	$criteria= new Criteria('arg','leechers');
    $avps = $avps_handler->getObjects($criteria, false);
    if (is_object($avps[0]))
	    $leechers = 0 + $avps[0]->getVar('value_u');
	else 
		$leechers = 0;

	if ($leechers == 0)
      $ratio = 0;
    else
      $ratio = round($seeders / $leechers * 100);
    
    $peers = number_format($seeders + $leechers);
    $seeders = number_format($seeders);
    $leechers = number_format($leechers);

	$xoopsOption['template_main'] = 'tb_index.html';
	stdhead($title, '', '', 0);
	$GLOBALS['xoopsTpl']->assign('xoConfig', $GLOBALS['TBDEV']);
	    
    if (get_user_class() >= UC_ADMINISTRATOR)
    	$GLOBALS['xoopsTpl']->assign('adminbutton', "&nbsp;<span style='float:right;'><a href='".XOOPS_URL."/modules/tb/admin.php?action=news'>News page</a></span>\n");
      
    $news_handler = xoops_getmodulehandler('news', 'tb');
    if ($news = $news_handler->getCurrentNews(true, 10)){
    	$button = "";
      	foreach($news as $id => $article) {
        	if (get_user_class() >= UC_ADMINISTRATOR) {
          		$button = "<div style='float:right;'><a href='".XOOPS_URL."/modules/tb/admin.php?action=news&amp;mode=edit&amp;newsid=".$id."'>{$GLOBALS['lang']['news_edit']}</a>&nbsp;<a href='admin.php?action=news&amp;mode=delete&amp;newsid=".$id."'>{$GLOBALS['lang']['news_delete']}</a></div>";
        	}
      		$GLOBALS['xoopsTpl']->append('news', array('headline'=>$article->getVar('headline'), 'added'=>get_date( $article->getVar('added'),'DATE'), 'button'=>$button, 'body' => $article->getVar('body')));  
      	}
    }
    
    $GLOBALS['xoopsTpl']->assign('registered', $registered);
    $GLOBALS['xoopsTpl']->assign('unverified', $unverified);
    $GLOBALS['xoopsTpl']->assign('torrents', $torrents);
      
    if (isset($peers)) 
    {
    	$GLOBALS['xoopsTpl']->assign('peers', $peers);
	    $GLOBALS['xoopsTpl']->assign('seeders', $seeders);
	    $GLOBALS['xoopsTpl']->assign('leechers', $leechers);
	    $GLOBALS['xoopsTpl']->assign('ratio', $ratio);
    } 
    stdfoot();
?>
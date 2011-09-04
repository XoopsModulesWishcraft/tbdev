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

    $GLOBALS['lang'] = array_merge( $GLOBALS['lang'], load_language('upload') );
    
    if ($GLOBALS['CURUSER']['class'] < UC_UPLOADER)
    {
        stderr($GLOBALS['lang']['upload_sorry'], $GLOBALS['lang']['upload_no_auth']);
    }

    $query = @$GLOBALS['xoopsDB']->queryF( "SELECT * FROM ".$GLOBALS['xoopsDB']->prefix('tb_categories').'' );
    while($row = $GLOBALS['xoopsDB']->fetchArray($query))
    {
    	$cats[$row['id']]['id'] = $row['id'];
      	$cats[$row['id']]['name'] = $row['name']; 
    }
    $xoopsOption['template_main'] = 'tb_upload.html';
	stdhead($title, '', '', 0);
	$GLOBALS['xoopsTpl']->assign('cats', $cats);
	$GLOBALS['xoopsTpl']->assign('xoConfig', $GLOBALS['TBDEV']);
	$GLOBALS['xoopsTpl']->assign('xoops_pagetitle', $GLOBALS['lang']['upload_stdhead']);
	stdfoot();

?>
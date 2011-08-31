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
require_once "include/html_functions.php";

dbconn(false);

loggedinorreturn();

    $GLOBALS['lang'] = array_merge( load_language('global'), load_language('upload') );
    
    if ($GLOBALS['CURUSER']['class'] < UC_UPLOADER)
    {
        stderr($GLOBALS['lang']['upload_sorry'], $GLOBALS['lang']['upload_no_auth']);
    }

    $query = @$GLOBALS['xoopsDB']->queryF( "SELECT * FROM ".$GLOBALS['xoopsDB']->prefix('categories').'' );
    while($row = $GLOBALS['xoopsDB']->fetchArray($query))
    {
    	$cats[$row['id']]['id'] = $row['id'];
      	$cats[$row['id']]['name'] = $row['name']; 
    }
    $xoopsOption['template_main'] = 'tb_upload.html';
	include $GLOBALS['xoops']->path('header.php');
	$GLOBALS['xoopsTpl']->assign('cats', $cats);
	$GLOBALS['xoopsTpl']->assign('xoConfig', $GLOBALS['TBDEV']);
	$GLOBALS['xoopsTpl']->assign('xoops_pagetitle', $GLOBALS['lang']['upload_stdhead']);
	include $GLOBALS['xoops']->path('footer.php');

?>
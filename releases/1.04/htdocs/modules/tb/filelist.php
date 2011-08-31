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
require_once '../../mainfile.php';
require_once "include/bittorrent.php";
require_once "include/user_functions.php";
require_once "include/html_functions.php";

dbconn(false);

loggedinorreturn();

    $GLOBALS['lang'] = array_merge( load_language('global'), load_language('filelist') );
    
    $id = isset($_GET["id"]) ? (int)$_GET["id"] : 0;

    if (!is_valid_id($id)) {
    	include $GLOBALS['xoops']->path('header.php');
        stderr('USER ERROR', 'Bad id');
        include $GLOBALS['xoops']->path('footer.php');
    }

    $xoopsOption['template_main'] = 'tb_files.html';
	include $GLOBALS['xoops']->path('header.php');
	$GLOBALS['xoopsTpl']->assign('xoConfig', $GLOBALS['TBDEV']);
	$GLOBALS['xoopsTpl']->assign('id', $id);
	$subres = $GLOBALS['xoopsDB']->queryF("SELECT * FROM ".$GLOBALS['xoopsDB']->prefix("tb_files")." WHERE torrent = $id ORDER BY id");
	while ($subrow = $GLOBALS['xoopsDB']->fetchArray($subres)) {
		$GLOBALS['xoopsTpl']->append('rows', array('filename'=>$subrow['filename'], 'size'=>mksize($subrow['size'])));
	}
	include $GLOBALS['xoops']->path('footer.php');
?>
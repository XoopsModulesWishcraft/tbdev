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

    $GLOBALS['lang'] = array_merge( $GLOBALS['lang'], load_language('filelist') );
    
    $id = isset($_GET["id"]) ? (int)$_GET["id"] : 0;

    if (!is_valid_id($id)) {
    	stdhead($title, '', '', 0);
        stderr('USER ERROR', 'Bad id');
        stdfoot();
    }

    $xoopsOption['template_main'] = 'tb_files.html';
	stdhead($title, '', '', 0);
	$GLOBALS['xoopsTpl']->assign('xoConfig', $GLOBALS['TBDEV']);
	$GLOBALS['xoopsTpl']->assign('id', $id);
	$files_handler = xoops_getmodulehandler('files', 'tb');
	$criteria = new Criteria('torrent', $id);
	foreach($files_handler->getObjects($criteria) as $fileid => $file) {
		$GLOBALS['xoopsTpl']->append('rows', array('filename'=>$file->getVar('filename'), 'size'=>mksize($file->getVar('size'))));
	}
	stdfoot();
?>
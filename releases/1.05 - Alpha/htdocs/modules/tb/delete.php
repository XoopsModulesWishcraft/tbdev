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

    $GLOBALS['lang'] = array_merge( $GLOBALS['lang'], load_language('delete') );

    if( !$GLOBALS['CURUSER']['group']['g_delete_torrents'] )
        stderr($GLOBALS['lang']['gl_user_error'], $GLOBALS['lang']['gl_perm_denied']);
    
    if (!mkglobal("id"))
      stderr($GLOBALS['lang']['delete_failed'], $GLOBALS['lang']['delete_missing_data']);

    $id = 0 + $id;
    if (!is_valid_id($id))
      stderr($GLOBALS['lang']['delete_failed'], $GLOBALS['lang']['delete_missing_data']);
      

    $res = $GLOBALS['xoopsDB']->queryF("SELECT name,owner,seeders FROM ".$GLOBALS['xoopsDB']->prefix("tb_torrents")." WHERE id = $id");
    $row = $GLOBALS['xoopsDB']->fetchArray($res);
    if (!$row)
      stderr("{$GLOBALS['lang']['delete_failed']}", "{$GLOBALS['lang']['delete_not_exist']}");

    if ( $GLOBALS['CURUSER']["id"] != $row["owner"] && !$GLOBALS['CURUSER']['ismod'] )
      stderr("{$GLOBALS['lang']['delete_failed']}", "{$GLOBALS['lang']['delete_not_owner']}\n");

    $rt = 0 + $_POST["reasontype"];

    if (!is_int($rt) || $rt < 1 || $rt > 5)
      stderr($GLOBALS['lang']['delete_failed'], $GLOBALS['lang']['delete_invalid']);

    //$r = $_POST["r"]; // whats this
    $reason = $_POST["reason"];

    if ($rt == 1)
      $reasonstr = "{$GLOBALS['lang']['delete_dead']}";
    elseif ($rt == 2)
      $reasonstr = "{$GLOBALS['lang']['delete_dupe']}" . ($reason[0] ? (": " . trim($reason[0])) : "!");
    elseif ($rt == 3)
      $reasonstr = "{$GLOBALS['lang']['delete_nuked']}" . ($reason[1] ? (": " . trim($reason[1])) : "!");
    elseif ($rt == 4)
    {
      if (!$reason[2])
        stderr("{$GLOBALS['lang']['delete_failed']}", "{$GLOBALS['lang']['delete_violated']}");
      $reasonstr = $GLOBALS['TBDEV']['site_name']."{$GLOBALS['lang']['delete_rules']}" . trim($reason[2]);
    }
    else
    {
      if (!$reason[3])
        stderr("{$GLOBALS['lang']['delete_failed']}", "{$GLOBALS['lang']['delete_reason']}");
      $reasonstr = trim($reason[3]);
    }

    deletetorrent($id);

    write_log("{$GLOBALS['lang']['delete_torrent']} $id ({$row['name']}){$GLOBALS['lang']['delete_deleted_by']}{$GLOBALS['CURUSER']['username']} ($reasonstr)\n");


    if (isset($_POST["returnto"]))
      $ret = "<a href='" . htmlspecialchars($_POST["returnto"]) . "'>{$GLOBALS['lang']['delete_go_back']}</a>";
    else
      $ret = "<a href='{$GLOBALS['TBDEV']['baseurl']}/index.php'>{$GLOBALS['lang']['delete_back_index']}</a>";

    $xoopsOption['template_main'] = 'tb_delete.html';
	stdhead($title, '', '', 0);
	$GLOBALS['xoopsTpl']->assign('xoConfig', $GLOBALS['TBDEV']);
	$GLOBALS['xoopsTpl']->assign('ret', $ret);
	stdfoot();
	
	exit(0);

function deletetorrent($id) {
	$torrents_handler = xoops_getmodulehandler('torrents', 'td');
	$peers_handler = xoops_getmodulehandler('peers', 'td');
	$files_handler = xoops_getmodulehandler('files', 'td');
	//$rating_handler = xoops_getmodulehandler('rating', 'td');
	$criteria = new Criteria('id', $id);
	$torrents_handler->deleteAll($criteria);
	$criteria = new Criteria('torrent', $id);
	$peers_handler->deleteAll($criteria);
	$files_handler->deleteAll($criteria);
	//$rating_handler->deleteAll($criteria);
    unlink("{$GLOBALS['TBDEV']['torrent_dir']}/$id.torrent");
}

?>
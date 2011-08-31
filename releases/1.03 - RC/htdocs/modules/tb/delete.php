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

dbconn();

loggedinorreturn();

    $GLOBALS['lang'] = array_merge( load_language('global'), load_language('delete') );

    if( !$GLOBALS['CURUSER']['group']['g_delete_torrents'] )
        stderr($GLOBALS['lang']['gl_user_error'], $GLOBALS['lang']['gl_perm_denied']);
    
    if (!mkglobal("id"))
      stderr($GLOBALS['lang']['delete_failed'], $GLOBALS['lang']['delete_missing_data']);

    $id = 0 + $id;
    if (!is_valid_id($id))
      stderr($GLOBALS['lang']['delete_failed'], $GLOBALS['lang']['delete_missing_data']);
      

    $res = $GLOBALS['xoopsDB']->queryF("SELECT name,owner,seeders FROM ".$GLOBALS['xoopsDB']->prefix("torrents")." WHERE id = $id");
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
	include $GLOBALS['xoops']->path('header.php');
	$GLOBALS['xoopsTpl']->assign('xoConfig', $GLOBALS['TBDEV']);
	$GLOBALS['xoopsTpl']->assign('ret', $ret);
	include $GLOBALS['xoops']->path('footer.php');
	
	exit(0);

function deletetorrent($id) {
    
    $GLOBALS['xoopsDB']->queryF("DELETE FROM ".$GLOBALS['xoopsDB']->prefix("torrents")." WHERE id = $id");
    foreach(explode(".",$GLOBALS['xoopsDB']->prefix("peers").'.'.$GLOBALS['xoopsDB']->prefix("files").'.'.$GLOBALS['xoopsDB']->prefix("rating")) as $x)
        @$GLOBALS['xoopsDB']->queryF("DELETE FROM $x WHERE torrent = $id");
    unlink("{$GLOBALS['TBDEV']['torrent_dir']}/$id.torrent");
}

?>
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
ob_start("ob_gzhandler");

require_once '../../mainfile.php';
require_once("include/bittorrent.php");
require_once "include/user_functions.php";
require_once "include/bbcode_functions.php";
require_once "include/pager_functions.php";
require_once "include/torrenttable_functions.php";
require_once "include/html_functions.php";


function ratingpic($num) {
    
    $r = round($num * 2) / 2;
    if ($r < 1 || $r > 5)
        return;
    return "<img src=\"{$GLOBALS['TBDEV']['pic_base_url']}{$r}.gif\" border=\"0\" alt=\"rating: $num / 5\" />";
}


dbconn(false);

loggedinorreturn();

    $GLOBALS['lang'] = array_merge( load_language('global'), load_language('details') );

    if (!isset($_GET['id']) || !is_valid_id($_GET['id']))
      stderr("{$GLOBALS['lang']['details_user_error']}", "{$GLOBALS['lang']['details_bad_id']}"); 
      
    $id = (int)$_GET["id"];
    
    if (isset($_GET["hit"])) 
    {
      $GLOBALS['xoopsDB']->queryF("UPDATE ".$GLOBALS['xoopsDB']->prefix("tb_torrents")." SET views = views + 1 WHERE id = $id");
      /* if ($_GET["tocomm"])
        header("Location: {$GLOBALS['TBDEV']['baseurl']}/details.php?id=$id&page=0#startcomments");
      elseif ($_GET["filelist"])
        header("Location: {$GLOBALS['TBDEV']['baseurl']}/details.php?id=$id&filelist=1#filelist");
      elseif ($_GET["toseeders"])
        header("Location: {$GLOBALS['TBDEV']['baseurl']}/peerlist.php?id=$id#seeders");
      elseif ($_GET["todlers"])
        header("Location: {$GLOBALS['TBDEV']['baseurl']}/peerlist.php?id=$id#leechers");
      else */
        header("Location: {$GLOBALS['TBDEV']['baseurl']}/details.php?id=$id");
      exit();
    }
	
$res = $GLOBALS['xoopsDB']->queryF("SELECT ".$GLOBALS['xoopsDB']->prefix("tb_torrents").".seeders, ".$GLOBALS['xoopsDB']->prefix("tb_torrents").".banned, ".$GLOBALS['xoopsDB']->prefix("tb_torrents").".leechers, ".$GLOBALS['xoopsDB']->prefix("tb_torrents").".info_hash, ".$GLOBALS['xoopsDB']->prefix("tb_torrents").".filename, LENGTH(".$GLOBALS['xoopsDB']->prefix("tb_torrents").".nfo) AS nfosz, ".$GLOBALS['xoopsDB']->prefix("tb_torrents").".last_action AS lastseed, ".$GLOBALS['xoopsDB']->prefix("tb_torrents").".numratings, ".$GLOBALS['xoopsDB']->prefix("tb_torrents").".name, IF(".$GLOBALS['xoopsDB']->prefix("tb_torrents").".numratings < {$GLOBALS['TBDEV']['minvotes']}, NULL, ROUND(".$GLOBALS['xoopsDB']->prefix("tb_torrents").".ratingsum / ".$GLOBALS['xoopsDB']->prefix("tb_torrents").".numratings, 1)) AS rating, ".$GLOBALS['xoopsDB']->prefix("tb_torrents").".comments, ".$GLOBALS['xoopsDB']->prefix("tb_torrents").".owner, ".$GLOBALS['xoopsDB']->prefix("tb_torrents").".save_as, ".$GLOBALS['xoopsDB']->prefix("tb_torrents").".descr, ".$GLOBALS['xoopsDB']->prefix("tb_torrents").".visible, ".$GLOBALS['xoopsDB']->prefix("tb_torrents").".size, ".$GLOBALS['xoopsDB']->prefix("tb_torrents").".added, ".$GLOBALS['xoopsDB']->prefix("tb_torrents").".views, ".$GLOBALS['xoopsDB']->prefix("tb_torrents").".hits, ".$GLOBALS['xoopsDB']->prefix("tb_torrents").".times_completed, ".$GLOBALS['xoopsDB']->prefix("tb_torrents").".id, ".$GLOBALS['xoopsDB']->prefix("tb_torrents").".type, ".$GLOBALS['xoopsDB']->prefix("tb_torrents").".numfiles, ".$GLOBALS['xoopsDB']->prefix("tb_categories").".name AS cat_name, ".$GLOBALS['xoopsDB']->prefix("tb_users").".username FROM ".$GLOBALS['xoopsDB']->prefix("tb_torrents")." LEFT JOIN ".$GLOBALS['xoopsDB']->prefix("tb_categories")." ON ".$GLOBALS['xoopsDB']->prefix("tb_torrents").".category = ".$GLOBALS['xoopsDB']->prefix("tb_categories").".id LEFT JOIN ".$GLOBALS['xoopsDB']->prefix("tb_users")." ON ".$GLOBALS['xoopsDB']->prefix("tb_torrents").".owner = ".$GLOBALS['xoopsDB']->prefix("tb_users").".id WHERE ".$GLOBALS['xoopsDB']->prefix("tb_torrents").".id = $id")
	or sqlerr();
$row = $GLOBALS['xoopsDB']->fetchArray($res);

$owned = $moderator = 0;
	if (get_user_class() >= UC_MODERATOR)
		$owned = $moderator = 1;
	elseif ($GLOBALS['CURUSER']["id"] == $row["owner"])
		$owned = 1;
//}

if (!$row || ($row["banned"] == "yes" && !$moderator))
	stderr("{$GLOBALS['lang']['details_error']}", "{$GLOBALS['lang']['details_torrent_id']}");


    $xoopsOption['template_main'] = 'tb_details.html';
	include $GLOBALS['xoops']->path('header.php');
	$GLOBALS['xoopsTpl']->assign('xoConfig', $GLOBALS['TBDEV']);
	$GLOBALS['xoopsTpl']->assign('row', $row);
	

	if ($GLOBALS['CURUSER']["id"] == $row["owner"] || get_user_class() >= UC_MODERATOR)
		$owned = 1;
	else
		$owned = 0;

		
	$GLOBALS['xoopsTpl']->assign('info_hash', $row["info_hash"]);
	$GLOBALS['xoopsTpl']->assign('owned', $owned);
	
	$spacer = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";

	$url = "edit.php?id=" . $row["id"];
	if (isset($_GET["returnto"])) {
		$addthis = "&amp;returnto=" . urlencode($_GET["returnto"]);
		$url .= $addthis;
		$keepget = $addthis;
	}
	$editlink = "<a href=\"$url\" class=\"sublink\">";

	$GLOBALS['xoopsTpl']->assign('editlink', $editlink);
	$GLOBALS['xoopsTpl']->assign('tr_info_hash', tr("{$GLOBALS['lang']['details_info_hash']}", $row["info_hash"]));
     
	if ($row["visible"] == "no")
		$GLOBALS['xoopsTpl']->assign('tr_visible', tr("{$GLOBALS['lang']['details_visible']}", "<b>{$GLOBALS['lang']['details_no']}</b>{$GLOBALS['lang']['details_dead']}", 1));
	if ($moderator)
		$GLOBALS['xoopsTpl']->assign('tr_banned', tr("{$GLOBALS['lang']['details_banned']}", $row["banned"]));
	$GLOBALS['xoopsTpl']->assign('moderation', $moderator);
	if (isset($row["cat_name"]))
		$GLOBALS['xoopsTpl']->assign('tr_cat', tr("{$GLOBALS['lang']['details_type']}", $row["cat_name"]));	
	else
		$GLOBALS['xoopsTpl']->assign('tr_cat', tr("{$GLOBALS['lang']['details_type']}", "{$GLOBALS['lang']['details_none']}"));
			
	$GLOBALS['xoopsTpl']->assign('tr_lastseed', tr("{$GLOBALS['lang']['details_last_seeder']}", "{$GLOBALS['lang']['details_last_activity']}" .get_date( $row['lastseed'],'',0,1)));
	$GLOBALS['xoopsTpl']->assign('tr_size', tr("{$GLOBALS['lang']['details_size']}",mksize($row["size"]) . " (" . number_format($row["size"]) . "{$GLOBALS['lang']['details_bytes']})"));
	$GLOBALS['xoopsTpl']->assign('tr_added', tr("{$GLOBALS['lang']['details_added']}", get_date( $row['added'],"{$GLOBALS['lang']['details_long']}")));
	$GLOBALS['xoopsTpl']->assign('tr_views', tr("{$GLOBALS['lang']['details_views']}", $row["views"]));
	$GLOBALS['xoopsTpl']->assign('tr_hits', tr("{$GLOBALS['lang']['details_hits']}", $row["hits"]));
	$GLOBALS['xoopsTpl']->assign('tr_snatched', tr("{$GLOBALS['lang']['details_snatched']}", $row["times_completed"] . "{$GLOBALS['lang']['details_times']}"));

	//$keepget = "";
	$uprow = (isset($row["username"]) ? ("<a href='userdetails.php?id=" . $row["owner"] . "'><b>" . htmlspecialchars($row["username"]) . "</b></a>") : "<i>{$GLOBALS['lang']['details_unknown']}</i>");
	if ($owned)
		$uprow .= " $spacer$editlink<b>{$GLOBALS['lang']['details_edit']}</b></a>";
		
	$GLOBALS['xoopsTpl']->assign('tr_upped', tr("Upped by", $uprow, 1));

	if ($row["type"] == "multi") {
		if (!isset($_GET["filelist"]))
			$GLOBALS['xoopsTpl']->assign('tr_files', tr("{$GLOBALS['lang']['details_num_files']}<br /><a href=\"filelist.php?id=$id\" class=\"sublink\">{$GLOBALS['lang']['details_list']}</a>", $row["numfiles"] . " files", 1));
		else {
			$GLOBALS['xoopsTpl']->assign('tr_files', tr("{$GLOBALS['lang']['details_num-files']}", $row["numfiles"] . "{$GLOBALS['lang']['details_files']}", 1));
		}
	}

	$GLOBALS['xoopsTpl']->assign('tr_peers', tr("{$GLOBALS['lang']['details_peers']}<br /><a href=\"peerlist.php?id=$id#seeders\" class=\"sublink\">{$GLOBALS['lang']['details_list']}</a>", $row["seeders"] . " seeder(s), " . $row["leechers"] . " leecher(s) = " . ($row["seeders"] + $row["leechers"]) . "{$GLOBALS['lang']['details_peer_total']}", 1));
		
    $GLOBALS['xoopsTpl']->assign('xoops_pagetitle', $row['filename']);
    include $GLOBALS['xoops']->path('/include/comment_view.php');
	include $GLOBALS['xoops']->path('footer.php');
    
?>
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
	
	
	function ratingpic($num) {
	    
	    $r = round($num * 2) / 2;
	    if ($r < 1 || $r > 5)
	        return;
	    return "<img src=\"{$GLOBALS['TBDEV']['pic_base_url']}{$r}.gif\" border=\"0\" alt=\"rating: $num / 5\" />";
	}


	loggedinorreturn();

    $GLOBALS['lang'] = array_merge( $GLOBALS['lang'], load_language('details') );

    if (!isset($_GET['id']) || !is_valid_id($_GET['id']))
      stderr("{$GLOBALS['lang']['details_user_error']}", "{$GLOBALS['lang']['details_bad_id']}"); 
      
    $id = (int)$_GET["id"];
    
    if (isset($_GET["hit"]))   {
    	$GLOBALS['xoopsDB']->queryF("UPDATE ".$GLOBALS['xoopsDB']->prefix("tb_torrents")." SET views = views + 1 WHERE id = $id");
        header("Location: {$GLOBALS['TBDEV']['baseurl']}/details.php?id=$id");
	    exit();
    }
	
    $torrents_handler = xoops_getmodulehandler('torrents', 'td');
    $torrent = $torrents_handler->get($id);
	$row = $torrent->toArray();
	 
	$owned = $moderator = 0;
	if (get_user_class() >= UC_MODERATOR)
		$owned = $moderator = 1;
	elseif ($GLOBALS['CURUSER']["id"] == $torrent->getVar('owner'))
		$owned = 1;


	if (!is_object($torrent) || ($torrent->getVar('banned') == "yes" && !$moderator))
		stderr("{$GLOBALS['lang']['details_error']}", "{$GLOBALS['lang']['details_torrent_id']}");


    $xoopsOption['template_main'] = 'tb_details.html';
	stdhead($title, '', '', 0);
	$GLOBALS['xoopsTpl']->assign('xoConfig', $GLOBALS['TBDEV']);
	$GLOBALS['xoopsTpl']->assign('row', $torrent->toArray());
	
	if ($GLOBALS['CURUSER']["id"] == $row["owner"] || get_user_class() >= UC_MODERATOR)
		$owned = 1;
	else
		$owned = 0;

		
	$GLOBALS['xoopsTpl']->assign('info_hash', $torrent->getVar("info_hash"));
	$GLOBALS['xoopsTpl']->assign('owned', $owned);
	
	$spacer = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";

	$url = XOOPS_URL."/modules/tb/edit.php?id=" . $id;
	if (isset($_GET["returnto"])) {
		$addthis = "&amp;returnto=" . urlencode($_GET["returnto"]);
		$url .= $addthis;
		$keepget = $addthis;
	}
	$editlink = "<a href=\"$url\" class=\"sublink\">";

	$GLOBALS['xoopsTpl']->assign('editlink', $editlink);
	$GLOBALS['xoopsTpl']->assign('tr_info_hash', tr("{$GLOBALS['lang']['details_info_hash']}", $torrent->getVar("info_hash")));
     
	if ($torrent->getVar("visible") == "no")
		$GLOBALS['xoopsTpl']->assign('tr_visible', tr("{$GLOBALS['lang']['details_visible']}", "<b>{$GLOBALS['lang']['details_no']}</b>{$GLOBALS['lang']['details_dead']}", 1));
	if ($moderator)
		$GLOBALS['xoopsTpl']->assign('tr_banned', tr("{$GLOBALS['lang']['details_banned']}", $torrent->getVar("banned")));
	$GLOBALS['xoopsTpl']->assign('moderation', $moderator);
	if (isset($row['category']["cat_name"]))
		$GLOBALS['xoopsTpl']->assign('tr_cat', tr("{$GLOBALS['lang']['details_type']}", $row['category']["cat_name"]));	
	else
		$GLOBALS['xoopsTpl']->assign('tr_cat', tr("{$GLOBALS['lang']['details_type']}", "{$GLOBALS['lang']['details_none']}"));
			
	$GLOBALS['xoopsTpl']->assign('tr_lastseed', tr("{$GLOBALS['lang']['details_last_seeder']}", "{$GLOBALS['lang']['details_last_activity']}" .$row['date']["lastseed"]));
	$GLOBALS['xoopsTpl']->assign('tr_size', tr("{$GLOBALS['lang']['details_size']}",$row['mksize']["size"] . " (" . number_format($row["size"]) . "{$GLOBALS['lang']['details_bytes']})"));
	$GLOBALS['xoopsTpl']->assign('tr_added', tr("{$GLOBALS['lang']['details_added']}", $row['date']["added"]));
	$GLOBALS['xoopsTpl']->assign('tr_views', tr("{$GLOBALS['lang']['details_views']}", $row['number_format']["views"]));
	$GLOBALS['xoopsTpl']->assign('tr_hits', tr("{$GLOBALS['lang']['details_hits']}", $row['number_format']["hits"]));
	$GLOBALS['xoopsTpl']->assign('tr_snatched', tr("{$GLOBALS['lang']['details_snatched']}", $row['number_format']["times_completed"] . "{$GLOBALS['lang']['details_times']}"));

	//$keepget = "";
	$uprow = (isset($row['user']["uname"]) ? ("<a href='".XOOPS_URL."/modules/tb/userdetails.php?id=" . $row["owner"] . "'><b>" . htmlspecialchars($row['user']["uname"]) . "</b></a>") : "<i>{$GLOBALS['lang']['details_unknown']}</i>");
	if ($owned)
		$uprow .= " $spacer$editlink<b>{$GLOBALS['lang']['details_edit']}</b></a>";
		
	$GLOBALS['xoopsTpl']->assign('tr_upped', tr("Upped by", $uprow, 1));

	if ($row["type"] == "multi") {
		if (!isset($_GET["filelist"]))
			$GLOBALS['xoopsTpl']->assign('tr_files', tr("{$GLOBALS['lang']['details_num_files']}<br /><a href=\"".XOOPS_URL."/modules/tb/filelist.php?id=$id\" class=\"sublink\">{$GLOBALS['lang']['details_list']}</a>", $row["numfiles"] . " files", 1));
		else {
			$GLOBALS['xoopsTpl']->assign('tr_files', tr("{$GLOBALS['lang']['details_num-files']}", $row["numfiles"] . "{$GLOBALS['lang']['details_files']}", 1));
		}
	}

	$GLOBALS['xoopsTpl']->assign('tr_peers', tr("{$GLOBALS['lang']['details_peers']}<br /><a href=\"".XOOPS_URL."/modules/tb/peerlist.php?id=$id#seeders\" class=\"sublink\">{$GLOBALS['lang']['details_list']}</a>", $row["seeders"] . " seeder(s), " . $row["leechers"] . " leecher(s) = " . ($row["seeders"] + $row["leechers"]) . "{$GLOBALS['lang']['details_peer_total']}", 1));
		
    $GLOBALS['xoopsTpl']->assign('xoops_pagetitle', $row['filename']);
    include $GLOBALS['xoops']->path('/include/comment_view.php');
	stdfoot();
    
?>
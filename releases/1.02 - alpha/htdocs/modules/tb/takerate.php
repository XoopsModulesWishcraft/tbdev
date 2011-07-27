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
require_once("include/bittorrent.php");

dbconn();
loggedinorreturn();
$GLOBALS['lang'] = array_merge( load_language('global'), load_language('takerate') );


if (!isset($GLOBALS['CURUSER']))
	stderr("{$GLOBALS['lang']['rate_fail']}", "{$GLOBALS['lang']['rate_login']}");

if (!mkglobal("rating:id"))
	stderr("{$GLOBALS['lang']['rate_fail']}", "{$GLOBALS['lang']['rate_miss_form_data']}");

$id = 0 + $id;
if (!$id)
	stderr("{$GLOBALS['lang']['rate_fail']}", "{$GLOBALS['lang']['rate_invalid_id']}");

$rating = 0 + $rating;
if ($rating <= 0 || $rating > 5)
	stderr("{$GLOBALS['lang']['rate_fail']}", "{$GLOBALS['lang']['rate_invalid']}");

$res = $GLOBALS['xoopsDB']->queryF("SELECT owner FROM ".$GLOBALS['xoopsDB']->prefix("torrents")." WHERE id = $id");
$row = $GLOBALS['xoopsDB']->fetchArray($res);
if (!$row)
	stderr("{$GLOBALS['lang']['rate_fail']}", "{$GLOBALS['lang']['rate_torrent_not_found']}");

//if ($row["owner"] == $GLOBALS['CURUSER']["id"])
//	bark("{$GLOBALS['lang']['rate_not_vote_own_torrent']}");
$time_now = time();
$res = $GLOBALS['xoopsDB']->queryF("INSERT INTO ratings (torrent, user, rating, added) VALUES ($id, " . $GLOBALS['CURUSER']["id"] . ", $rating, $time_now)");
if (!$res) {
	if (mysql_errno() == 1062)
		stderr("{$GLOBALS['lang']['rate_fail']}", "{$GLOBALS['lang']['rate_already_voted']}");
	else
		stderr("{$GLOBALS['lang']['rate_fail']}", mysql_error());
}

$GLOBALS['xoopsDB']->queryF("UPDATE ".$GLOBALS['xoopsDB']->prefix("torrents")." SET numratings = numratings + 1, ratingsum = ratingsum + $rating WHERE id = $id");

header("Refresh: 0; url=details.php?id=$id&rated=1");

?>
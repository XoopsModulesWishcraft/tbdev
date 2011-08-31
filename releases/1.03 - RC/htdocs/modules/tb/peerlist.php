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
require_once("include/bittorrent.php");
require_once "include/user_functions.php";
require_once "include/bt_client_functions.php";
require_once "include/html_functions.php";

dbconn(false);

loggedinorreturn();

    $GLOBALS['lang'] = array_merge( load_language('global'), load_language('peerlist') );
    
    $id = (int)$_GET['id'];

    if (!isset($id) || !is_valid_id($id))
      stderr($GLOBALS['lang']['edit_user_error'], $GLOBALS['lang']['peerslist_invalid_id']);

    $HTMLOUT = '';
    
function dltable($name, $arr, $torrent)
{

    

    $htmlout = '';

    if (!count($arr))
      return $htmlout = "<div align='left'><b>{$GLOBALS['lang']['peerslist_no']} $name {$GLOBALS['lang']['peerslist_data_available']}</b></div>\n";
    $htmlout = "\n";
    $htmlout .= "<table width='100%' class='main' border='1' cellspacing='0' cellpadding='5'>\n";
    $htmlout .= "<tr><td colspan='11' class='colhead'>" . count($arr) . " $name</td></tr>" .
        "<tr><td class='colhead'>{$GLOBALS['lang']['peerslist_user_ip']}</td>" .
            "<td class='colhead' align='center'>{$GLOBALS['lang']['peerslist_connectable']}</td>".
            "<td class='colhead' align='right'>{$GLOBALS['lang']['peerslist_uploaded']}</td>".
            "<td class='colhead' align='right'>{$GLOBALS['lang']['peerslist_rate']}</td>".
            "<td class='colhead' align='right'>{$GLOBALS['lang']['peerslist_downloaded']}</td>" .
            "<td class='colhead' align='right'>{$GLOBALS['lang']['peerslist_rate']}</td>" .
            "<td class='colhead' align='right'>{$GLOBALS['lang']['peerslist_ratio']}</td>" .
            "<td class='colhead' align='right'>{$GLOBALS['lang']['peerslist_complete']}</td>" .
            "<td class='colhead' align='right'>{$GLOBALS['lang']['peerslist_connected']}</td>" .
            "<td class='colhead' align='right'>{$GLOBALS['lang']['peerslist_idle']}</td>" .
            "<td class='colhead' align='left'>{$GLOBALS['lang']['peerslist_client']}</td></tr>\n";
         
    $now = time();
    //$moderator = (isset($GLOBALS['CURUSER']) && get_user_class() >= UC_MODERATOR);
    //$mod = get_user_class() >= UC_MODERATOR;
    foreach ($arr as $e) {


                  // user/ip/port
                  // check if anyone has this ip
                  //($unr = $GLOBALS['xoopsDB']->queryF("SELECT username, privacy FROM ".$GLOBALS['xoopsDB']->prefix("tb_users")." WHERE id=$e[userid] ORDER BY last_access DESC LIMIT 1")) or die;
                  //$una = $GLOBALS['xoopsDB']->fetchArray($unr);
          if ($e["privacy"] == "strong") continue;
      $htmlout .= "<tr>\n";
                  if ($e["username"])
                    $htmlout .= "<td><a href='userdetails.php?id=$e[userid]'><b>$e[username]</b></a></td>\n";
                  else
                    $htmlout .= "<td>" . ($mod ? $e["ip"] : preg_replace('/\.\d+$/', ".xxx", $e["ip"])) . "</td>\n";
      $secs = max(1, ($now - $e["st"]) - ($now - $e["la"]));
      //$revived = $e["revived"] == "yes";
          $htmlout .= "<td align='center'>" . ($e['connectable'] == "yes" ? "{$GLOBALS['lang']['peerslist_yes']}" : "<font color='red'>{$GLOBALS['lang']['peerslist_no']}</font>") . "</td>\n";
      $htmlout .= "<td align='right'>" . mksize($e["uploaded"]) . "</td>\n";
      $htmlout .= "<td align='right'><span style=\"white-space: nowrap;\">" . mksize(($e["uploaded"] - $e["uploadoffset"]) / $secs) . "/s</span></td>\n";
      $htmlout .= "<td align='right'>" . mksize($e["downloaded"]) . "</td>\n";
      if ($e["seeder"] == "no")
        $htmlout .= "<td align='right'><span style=\"white-space: nowrap;\">" . mksize(($e["downloaded"] - $e["downloadoffset"]) / $secs) . "/s</span></td>\n";
      else
        $htmlout .= "<td align='right'><span style=\"white-space: nowrap;\">" . mksize(($e["downloaded"] - $e["downloadoffset"]) / max(1, $e["finishedat"] - $e['st'])) .	"/s</span></td>\n";
                  if ($e["downloaded"])
          {
                    $ratio = floor(($e["uploaded"] / $e["downloaded"]) * 1000) / 1000;
                      $htmlout .= "<td align=\"right\"><font color='" . get_ratio_color($ratio) . "'>" . number_format($ratio, 3) . "</font></td>\n";
          }
                   else
                    if ($e["uploaded"])
                      $htmlout .= "<td align='right'>{$GLOBALS['lang']['peerslist_inf']}</td>\n";
                    else
                      $htmlout .= "<td align='right'>---</td>\n";
      $htmlout .= "<td align='right'>" . sprintf("%.2f%%", 100 * (1 - ($e["to_go"] / $torrent["size"]))) . "</td>\n";
      $htmlout .= "<td align='right'>" . mkprettytime($now - $e["st"]) . "</td>\n";
      $htmlout .= "<td align='right'>" . mkprettytime($now - $e["la"]) . "</td>\n";
      $htmlout .= "<td align='left'>" . htmlspecialchars(getagent($e["agent"], $e['peer_id'])) . "</td>\n";
      $htmlout .= "</tr>\n";
    }
    $htmlout .= "</table>\n";
    return $htmlout;
}

    $res = $GLOBALS['xoopsDB']->queryF("SELECT * FROM ".$GLOBALS['xoopsDB']->prefix("torrents")." WHERE id = $id")
      or sqlerr();

    if(mysql_num_rows($res) == 0)
      stderr("{$GLOBALS['lang']['peerslist_error']}", "{$GLOBALS['lang']['peerslist_nothing']}");
      
      $row = $GLOBALS['xoopsDB']->fetchArray($res);
      


          $downloaders = array();
          $seeders = array();
          $subres = $GLOBALS['xoopsDB']->queryF("SELECT u.username, u.privacy, p.seeder, p.finishedat, p.downloadoffset, p.uploadoffset, p.ip, p.port, p.uploaded, p.downloaded, p.to_go, p.started AS st, p.connectable, p.agent, p.last_action AS la, p.userid, p.peer_id
    FROM ".$GLOBALS['xoopsDB']->prefix("peers")." p
    LEFT JOIN ".$GLOBALS['xoopsDB']->prefix("tb_users")." u ON p.userid = u.id
    WHERE p.torrent = $id") or sqlerr();
          
          if(mysql_num_rows($subres) == 0)
            stderr("{$GLOBALS['lang']['peerslist_warning']}", "{$GLOBALS['lang']['peerslist_no_data']}");
      
          while ($subrow = $GLOBALS['xoopsDB']->fetchArray($subres)) {
            if ($subrow["seeder"] == "yes")
              $seeders[] = $subrow;
            else
              $downloaders[] = $subrow;
          }

          function leech_sort($a,$b) {
                                    if ( isset( $_GET["usort"] ) ) return seed_sort($a,$b);				
                                    $x = $a["to_go"];
            $y = $b["to_go"];
            if ($x == $y)
              return 0;
            if ($x < $y)
              return -1;
            return 1;
          }
          function seed_sort($a,$b) {
            $x = $a["uploaded"];
            $y = $b["uploaded"];
            if ($x == $y)
              return 0;
            if ($x < $y)
              return 1;
            return -1;
          }

          usort($seeders, "seed_sort");
          usort($downloaders, "leech_sort");

    $xoopsOption['template_main'] = 'tb_peerlist.html';
	include $GLOBALS['xoops']->path('header.php');
	$GLOBALS['xoopsTpl']->assign('xoConfig', $GLOBALS['TBDEV']);
	$GLOBALS['xoopsTpl']->assign('id', $id);
	$GLOBALS['xoopsTpl']->assign('row', $row);
	$GLOBALS['xoopsTpl']->assign('dltable_seeds', dltable("{$GLOBALS['lang']['peerslist_seeders']}<a name='seeders'></a>", $seeders, $row));
	$GLOBALS['xoopsTpl']->assign('dltable_leechs', dltable("{$GLOBALS['lang']['peerslist_leechers']}<a name='leechers'></a>", $downloaders, $row));
	include $GLOBALS['xoops']->path('footer.php');
      
?>
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
  require_once "../../mainfile.php";
  require_once "include/bittorrent.php";
  require_once "include/html_functions.php";
  require_once "include/user_functions.php";
  
  dbconn(false);
  
  loggedinorreturn();
  
  $GLOBALS['lang'] = array_merge( load_language('global'), load_language('topten') );

/*
  function donortable($res, $frame_caption)
  {
    begin_frame($frame_caption, true);
    begin_table();
?>
<tr>
<td class='colhead'>Rank</td>
<td class='colhead' align=left>User</td>
<td class='colhead' align=right>Donated</td>
</tr>
<?php
    $num = 0;
    while ($a = $GLOBALS['xoopsDB']->fetchArray($res))
    {
        ++$num;
		$this = $a["donated"];
		if ($this == $last)
			$rank = "";
		else
		{
		  $rank = $num;
		}
	if ($rank && $num > 10)
    	break;
      print("<tr><td>$rank</td><td align='left'><a href='userdetails.php?id=$a[id]'><b>$a[username]" .
         "</b></a></td><td align='right'>$this</td></tr>");
		$last = $this;
    }
    end_table();
    end_frame();
  }
*/

function usertable($res, $frame_caption)
  {
  	
  	
  	$htmlout = '';
  	
    $htmlout .= begin_frame($frame_caption, true);
    $htmlout .= begin_table();

    $htmlout .= "<tr>
    <td class='colhead'>{$GLOBALS['lang']['common_rank']}</td>
    <td class='colhead' align='left'>{$GLOBALS['lang']['user']}</td>
    <td class='colhead'>{$GLOBALS['lang']['user_ul']}</td>
    <td class='colhead' align='left'>{$GLOBALS['lang']['user_ulspeed']}</td>
    <td class='colhead'>{$GLOBALS['lang']['user_dl']}</td>
    <td class='colhead' align='left'>{$GLOBALS['lang']['user_dlspeed']}</td>
    <td class='colhead' align='right'>{$GLOBALS['lang']['common_ratio']}</td>
    <td class='colhead' align='left'>{$GLOBALS['lang']['user_joined']}</td>

    </tr>";

        $num = 0;
        while ($a = $GLOBALS['xoopsDB']->fetchArray($res))
        {
          ++$num;
          $highlight = $GLOBALS['CURUSER']["id"] == $a["userid"] ? " bgcolor='#BBAF9B'" : "";
          if ($a["downloaded"])
          {
            $ratio = $a["uploaded"] / $a["downloaded"];
            $color = get_ratio_color($ratio);
            $ratio = number_format($ratio, 2);
            if ($color)
              $ratio = "<font color='$color'>$ratio</font>";
          }
          else
            $ratio = $GLOBALS['lang']['common_infratio'];
          $htmlout .= "<tr$highlight><td align='center'>$num</td><td align='left'$highlight><a href='userdetails.php?id=" .
              $a["userid"] . "'><b>" . $a["username"] . "</b></a>" .
              "</td><td align='right'$highlight>" . mksize($a["uploaded"]) .
              "</td><td align='right'$highlight>" . mksize($a["upspeed"]) . "/s" .
              "</td><td align='right'$highlight>" . mksize($a["downloaded"]) .
              "</td><td align='right'$highlight>" . mksize($a["downspeed"]) . "/s" .
              "</td><td align='right'$highlight>" . $ratio .
              "</td><td align='left'>" . get_date( $a['added'],'') . " (" .
              get_date( $a['added'],'',0,1) . ")</td></tr>";
        }
        $htmlout .= end_table();
        $htmlout .= end_frame();
        
     return $htmlout;
  }

function _torrenttable($res, $frame_caption)
    {
              
      $htmlout = '';
      
      $htmlout .= begin_frame($frame_caption, true);
      $htmlout .= begin_table();

      $htmlout .= "<tr>
      <td class='colhead' align='center'>{$GLOBALS['lang']['common_rank']}</td>
      <td class='colhead' align='left'>{$GLOBALS['lang']['torrent_name']}</td>
      <td class='colhead' align='right'>{$GLOBALS['lang']['torrent_snatch']}</td>
      <td class='colhead' align='right'>{$GLOBALS['lang']['torrent_data']}</td>
      <td class='colhead' align='right'>{$GLOBALS['lang']['torrent_seed']}</td>
      <td class='colhead' align='right'>{$GLOBALS['lang']['torrent_leech']}</td>
      <td class='colhead' align='right'>{$GLOBALS['lang']['torrent_total']}</td>
      <td class='colhead' align='right'>{$GLOBALS['lang']['common_ratio']}</td>
      </tr>";

          $num = 0;
          while ($a = $GLOBALS['xoopsDB']->fetchArray($res))
          {
            ++$num;
            if ($a["leechers"])
            {
              $r = $a["seeders"] / $a["leechers"];
              $ratio = "<font color='" . get_ratio_color($r) . "'>" . number_format($r, 2) . "</font>";
            }
            else
              $ratio = $GLOBALS['lang']['common_infratio'];
            $htmlout .= "<tr><td align='center'>$num</td><td align='left'><a href='details.php?id=" . $a["id"] . "&hit=1'><b>" .
              $a["name"] . "</b></a></td><td align='right'>" . number_format($a["times_completed"]) .
              "</td><td align='right'>" . mksize($a["data"]) . "</td><td align='right'>" . number_format($a["seeders"]) .
              "</td><td align='right'>" . number_format($a["leechers"]) . "</td><td align='right'>" . ($a["leechers"] + $a["seeders"]) .
              "</td><td align='right'>$ratio</td>\n";
          }
          $htmlout .= end_table();
          $htmlout .= end_frame();
          
      return $htmlout;
  }

  function countriestable($res, $frame_caption, $what)
  {
    
    
    $htmlout = '';
    
    $htmlout .= begin_frame($frame_caption, true);
    $htmlout .= begin_table();

      $htmlout .= "<tr>
      <td class='colhead'>{$GLOBALS['lang']['common_rank']}</td>
      <td class='colhead' align='left'>{$GLOBALS['lang']['country']}</td>
      <td class='colhead' align='right'><?php echo $what?></td>
      </tr>";

          $num = 0;
          while ($a = $GLOBALS['xoopsDB']->fetchArray($res))
          {
            ++$num;
            if ($what == "Users")
              $value = number_format($a["num"]);
            elseif ($what == "Uploaded")
              $value = mksize($a["ul"]);
            elseif ($what == "Average")
              $value = mksize($a["ul_avg"]);
            elseif ($what == "Ratio")
              $value = number_format($a["r"],2);
            $htmlout .= "<tr><td align='center'>$num</td><td align='left'><table border='0' class='main' cellspacing='0' cellpadding='0'><tr><td class='embedded'>".
              "<img src=\"{$GLOBALS['TBDEV']['pic_base_url']}flag/{$a['flagpic']}\" alt='' /></td><td class='embedded' style='padding-left: 5px'><b>$a[name]</b></td>".
              "</tr></table></td><td align='right'>$value</td></tr>\n";
          }
          $htmlout .= end_table();
          $htmlout .= end_frame();
          
      return $htmlout;
  }

  function peerstable($res, $frame_caption)
  {
       
    $htmlout = '';
    
    $htmlout .= begin_frame($frame_caption, true);
    $htmlout .= begin_table();

		$htmlout .= "<tr><td class='colhead'>{$GLOBALS['lang']['common_rank']}</td><td class='colhead'>{$GLOBALS['lang']['peers_uname']}</td><td class='colhead'>{$GLOBALS['lang']['peers_ulrate']}</td><td class='colhead'>{$GLOBALS['lang']['peers_dlrate']}</td></tr>";

		$n = 1;
		while ($arr = $GLOBALS['xoopsDB']->fetchArray($res))
		{
      $highlight = $GLOBALS['CURUSER']["id"] == $arr["userid"] ? " bgcolor='#BBAF9B'" : "";
			$htmlout .= "<tr><td$highlight>$n</td><td$highlight><a href='userdetails.php?id=" . $arr["userid"] . "'><b>" . $arr["username"] . "</b></a></td><td$highlight>" . mksize($arr["uprate"]) . "/s</td><td$highlight>" . mksize($arr["downrate"]) . "/s</td></tr>\n";
			++$n;
		}

    $htmlout .= end_table();
    $htmlout .= end_frame();
    
    return $htmlout;
  }


      $HTMLOUT = '';
      
      $HTMLOUT .= begin_main_frame();
    //  $r = $GLOBALS['xoopsDB']->queryF("SELECT * FROM ".$GLOBALS['xoopsDB']->prefix("tb_users")." ORDER BY donated DESC, username LIMIT 100") or die;
    //  donortable($r, "Top 10 Donors");
      $type = isset($_GET["type"]) ? 0 + $_GET["type"] : 0;
      if (!in_array($type,array(1,2,3)))
        $type = 1;
      $limit = isset($_GET["lim"]) ? 0 + $_GET["lim"] : false;
      $subtype = isset($_GET["subtype"]) ? $_GET["subtype"] : false;

      $HTMLOUT .= "<p align='center'>"  .
        ($type == 1 && !$limit ? "<b>{$GLOBALS['lang']['common_users']}</b>" : "<a href='topten.php?type=1'>{$GLOBALS['lang']['common_users']}</a>") .	" | " .
        ($type == 2 && !$limit ? "<b>{$GLOBALS['lang']['nav_torrents']}</b>" : "<a href='topten.php?type=2'>{$GLOBALS['lang']['nav_torrents']}</a>") . " | " .
        ($type == 3 && !$limit ? "<b>{$GLOBALS['lang']['nav_countries']}</b>" : "<a href='topten.php?type=3'>{$GLOBALS['lang']['nav_countries']}</a>") . " | " .
        ($type == 4 && !$limit ? "<b>{$GLOBALS['lang']['nav_peers']}</b>" : "<a href='topten.php?type=4'>{$GLOBALS['lang']['nav_peers']}</a>") . "</p>\n";

      $pu = get_user_class() >= UC_POWER_USER;

      if (!$pu)
        $limit = 10;

      if ($type == 1)
      {
        $mainquery = "SELECT id as userid, username, added, uploaded, downloaded, uploaded / (".time()." - added) AS upspeed, downloaded / (".time()." - added) AS downspeed FROM ".$GLOBALS['xoopsDB']->prefix("tb_users")." WHERE enabled = 'yes'";

        if (!$limit || $limit > 250)
          $limit = 10;

        if ($limit == 10 || $subtype == "ul")
        {
          $order = "uploaded DESC";
          $r = $GLOBALS['xoopsDB']->queryF($mainquery . " ORDER BY $order " . " LIMIT $limit") or sqlerr();
          $HTMLOUT .= usertable($r, sprintf($GLOBALS['lang']['user_topulers'], $limit) . ($limit == 10 && $pu ? " <font class='small'> - [<a href='topten.php?type=1&amp;lim=100&amp;subtype=ul'>{$GLOBALS['lang']['common_top100']}</a>] - [<a href='topten.php?type=1&amp;lim=250&amp;subtype=ul'>{$GLOBALS['lang']['common_top250']}</a>]</font>" : ""));
        }

        if ($limit == 10 || $subtype == "dl")
        {
          $order = "downloaded DESC";
          $r = $GLOBALS['xoopsDB']->queryF($mainquery . " ORDER BY $order " . " LIMIT $limit") or sqlerr();
          $HTMLOUT .= usertable($r, sprintf($GLOBALS['lang']['user_topdlers'], $limit) . ($limit == 10 && $pu ? " <font class='small'> - [<a href='topten.php?type=1&amp;lim=100&amp;subtype=dl'>{$GLOBALS['lang']['common_top100']}</a>] - [<a href='topten.php?type=1&amp;lim=250&amp;subtype=dl'>{$GLOBALS['lang']['common_top250']}</a>]</font>" : ""));
        }

        if ($limit == 10 || $subtype == "uls")
        {
          $order = "upspeed DESC";
          $r = $GLOBALS['xoopsDB']->queryF($mainquery . " ORDER BY $order " . " LIMIT $limit") or sqlerr();
          $HTMLOUT .= usertable($r, sprintf($GLOBALS['lang']['user_fastestup'], $limit) . ($limit == 10 && $pu ? " <font class='small'> - [<a href='topten.php?type=1&amp;lim=100&amp;subtype=uls'>{$GLOBALS['lang']['common_top100']}</a>] - [<a href='topten.php?type=1&amp;lim=250&amp;subtype=uls'>{$GLOBALS['lang']['common_top250']}</a>]</font>" : ""));
        }

        if ($limit == 10 || $subtype == "dls")
        {
          $order = "downspeed DESC";
          $r = $GLOBALS['xoopsDB']->queryF($mainquery . " ORDER BY $order " . " LIMIT $limit") or sqlerr();
          $HTMLOUT .= usertable($r, sprintf($GLOBALS['lang']['user_fastestdown'], $limit) . ($limit == 10 && $pu ? " <font class='small'> - [<a href='topten.php?type=1&amp;lim=100&amp;subtype=dls'>{$GLOBALS['lang']['common_top100']}</a>] - [<a href='topten.php?type=1&amp;lim=250&amp;subtype=dls'>{$GLOBALS['lang']['common_top250']}</a>]</font>" : ""));
        }

        if ($limit == 10 || $subtype == "bsh")
        {
          $order = "uploaded / downloaded DESC";
          $extrawhere = " AND downloaded > 1073741824";
          $r = $GLOBALS['xoopsDB']->queryF($mainquery . $extrawhere . " ORDER BY $order " . " LIMIT $limit") or sqlerr();
          $HTMLOUT .= usertable($r, sprintf($GLOBALS['lang']['user_bestshare'], $limit) . ($limit == 10 && $pu ? " <font class='small'> - [<a href='topten.php?type=1&amp;lim=100&amp;subtype=bsh'>{$GLOBALS['lang']['common_top100']}</a>] - [<a href='topten.php?type=1&amp;lim=250&amp;subtype=bsh'>{$GLOBALS['lang']['common_top250']}</a>]</font>" : ""));
        }

        if ($limit == 10 || $subtype == "wsh")
        {
          $order = "uploaded / downloaded ASC, downloaded DESC";
          $extrawhere = " AND downloaded > 1073741824";
          $r = $GLOBALS['xoopsDB']->queryF($mainquery . $extrawhere . " ORDER BY $order " . " LIMIT $limit") or sqlerr();
          $HTMLOUT .= usertable($r, sprintf($GLOBALS['lang']['user_worstshare'], $limit) . ($limit == 10 && $pu ? " <font class='small'> - [<a href='topten.php?type=1&amp;lim=100&amp;subtype=wsh'>{$GLOBALS['lang']['common_top100']}</a>] - [<a href='topten.php?type=1&amp;lim=250&amp;subtype=wsh'>{$GLOBALS['lang']['common_top250']}</a>]</font>" : ""));
        }
      }

      elseif ($type == 2)
      {
        if (!$limit || $limit > 50)
          $limit = 10;

        if ($limit == 10 || $subtype == "act")
        {
          $r = $GLOBALS['xoopsDB']->queryF("SELECT t.*, (t.size * t.times_completed + SUM(p.downloaded)) AS data FROM ".$GLOBALS['xoopsDB']->prefix("torrents")." AS t LEFT JOIN ".$GLOBALS['xoopsDB']->prefix("peers")." AS p ON t.id = p.torrent WHERE p.seeder = 'no' GROUP BY t.id ORDER BY seeders + leechers DESC, seeders DESC, added ASC LIMIT $limit") or sqlerr();
          $HTMLOUT .= _torrenttable($r, sprintf($GLOBALS['lang']['torrent_mostact'], $limit) . ($limit == 10 && $pu ? " <font class='small'> - [<a href='topten.php?type=2&amp;lim=25&amp;subtype=act'>{$GLOBALS['lang']['common_top25']}</a>] - [<a href='topten.php?type=2&amp;lim=50&amp;subtype=act'>{$GLOBALS['lang']['common_top50']}</a>]</font>" : ""));
        }

        if ($limit == 10 || $subtype == "sna")
        {
          $r = $GLOBALS['xoopsDB']->queryF("SELECT t.*, (t.size * t.times_completed + SUM(p.downloaded)) AS data FROM ".$GLOBALS['xoopsDB']->prefix("torrents")." AS t LEFT JOIN ".$GLOBALS['xoopsDB']->prefix("peers")." AS p ON t.id = p.torrent WHERE p.seeder = 'no' GROUP BY t.id ORDER BY times_completed DESC LIMIT $limit") or sqlerr();
          $HTMLOUT .= _torrenttable($r, sprintf($GLOBALS['lang']['torrent_mostsna'], $limit) . ($limit == 10 && $pu ? " <font class='small'> - [<a href='topten.php?type=2&amp;lim=25&amp;subtype=sna'>{$GLOBALS['lang']['common_top25']}</a>] - [<a href='topten.php?type=2&amp;lim=50&amp;subtype=sna'>{$GLOBALS['lang']['common_top50']}</a>]</font>" : ""));
        }

        if ($limit == 10 || $subtype == "mdt")
        {
          $r = $GLOBALS['xoopsDB']->queryF("SELECT t.*, (t.size * t.times_completed + SUM(p.downloaded)) AS data FROM ".$GLOBALS['xoopsDB']->prefix("torrents")." AS t LEFT JOIN ".$GLOBALS['xoopsDB']->prefix("peers")." AS p ON t.id = p.torrent WHERE p.seeder = 'no' AND leechers >= 5 AND times_completed > 0 GROUP BY t.id ORDER BY data DESC, added ASC LIMIT $limit") or sqlerr();
          $HTMLOUT .= _torrenttable($r, sprintf($GLOBALS['lang']['torrent_datatrans'], $limit) . ($limit == 10 && $pu ? " <font class='small'> - [<a href='topten.php?type=2&amp;lim=25&amp;subtype=mdt'>{$GLOBALS['lang']['common_top25']}</a>] - [<a href='topten.php?type=2&amp;lim=50&amp;subtype=mdt'>{$GLOBALS['lang']['common_top50']}</a>]</font>" : ""));
        }

        if ($limit == 10 || $subtype == "bse")
        {
          $r = $GLOBALS['xoopsDB']->queryF("SELECT t.*, (t.size * t.times_completed + SUM(p.downloaded)) AS data FROM ".$GLOBALS['xoopsDB']->prefix("torrents")." AS t LEFT JOIN ".$GLOBALS['xoopsDB']->prefix("peers")." AS p ON t.id = p.torrent WHERE p.seeder = 'no' AND seeders >= 5 GROUP BY t.id ORDER BY seeders / leechers DESC, seeders DESC, added ASC LIMIT $limit") or sqlerr();
          $HTMLOUT .= _torrenttable($r, sprintf($GLOBALS['lang']['torrent_bestseed'], $limit) . ($limit == 10 && $pu ? " <font class='small'> - [<a href='topten.php?type=2&amp;lim=25&amp;subtype=bse'>{$GLOBALS['lang']['common_top25']}</a>] - [<a href='topten.php?type=2&amp;lim=50&amp;subtype=bse'>{$GLOBALS['lang']['common_top50']}</a>]</font>" : ""));
        }

        if ($limit == 10 || $subtype == "wse")
        {
          $r = $GLOBALS['xoopsDB']->queryF("SELECT t.*, (t.size * t.times_completed + SUM(p.downloaded)) AS data FROM ".$GLOBALS['xoopsDB']->prefix("torrents")." AS t LEFT JOIN ".$GLOBALS['xoopsDB']->prefix("peers")." AS p ON t.id = p.torrent WHERE p.seeder = 'no' AND leechers >= 5 AND times_completed > 0 GROUP BY t.id ORDER BY seeders / leechers ASC, leechers DESC LIMIT $limit") or sqlerr();
          $HTMLOUT .= _torrenttable($r, sprintf($GLOBALS['lang']['torrent_worstseed'], $limit) . ($limit == 10 && $pu ? " <font class='small'> - [<a href='topten.php?type=2&amp;lim=25&amp;subtype=wse'>{$GLOBALS['lang']['common_top25']}</a>] - [<a href='topten.php?type=2&amp;lim=50&amp;subtype=wse'>{$GLOBALS['lang']['common_top50']}</a>]</font>" : ""));
        }
      }
      elseif ($type == 3)
      {
        if (!$limit || $limit > 25)
          $limit = 10;

        if ($limit == 10 || $subtype == "us")
        {
          $r = $GLOBALS['xoopsDB']->queryF("SELECT name, flagpic, COUNT(".$GLOBALS['xoopsDB']->prefix("tb_users").".country) as num FROM ".$GLOBALS['xoopsDB']->prefix("countries")." LEFT JOIN ".$GLOBALS['xoopsDB']->prefix("tb_users")." ON ".$GLOBALS['xoopsDB']->prefix("tb_users").".country = countries.id GROUP BY name ORDER BY num DESC LIMIT $limit") or sqlerr();
          $HTMLOUT .= countriestable($r, sprintf($GLOBALS['lang']['country_mostact'], $limit) . ($limit == 10 && $pu ? " <font class='small'> - [<a href='topten.php?type=3&amp;lim=25&amp;subtype=us'>{$GLOBALS['lang']['common_top25']}</a>]</font>" : ""),$GLOBALS['lang']['common_users']);
        }

        if ($limit == 10 || $subtype == "ul")
        {
          $r = $GLOBALS['xoopsDB']->queryF("SELECT c.name, c.flagpic, sum(u.uploaded) AS ul FROM ".$GLOBALS['xoopsDB']->prefix("tb_users")." AS u LEFT JOIN ".$GLOBALS['xoopsDB']->prefix("countries")." AS c ON u.country = c.id WHERE u.enabled = 'yes' GROUP BY c.name ORDER BY ul DESC LIMIT $limit") or sqlerr();
          $HTMLOUT .= countriestable($r, sprintf($GLOBALS['lang']['country_totalul'], $limit) . ($limit == 10 && $pu ? " <font class='small'> - [<a href='topten.php?type=3&amp;lim=25&amp;subtype=ul'>{$GLOBALS['lang']['common_top25']}</a>]</font>" : ""),$GLOBALS['lang']['common_ul']);
        }

        if ($limit == 10 || $subtype == "avg")
        {
          $r = $GLOBALS['xoopsDB']->queryF("SELECT c.name, c.flagpic, sum(u.uploaded)/count(u.id) AS ul_avg FROM ".$GLOBALS['xoopsDB']->prefix("tb_users")." AS u LEFT JOIN ".$GLOBALS['xoopsDB']->prefix("countries")." AS c ON u.country = c.id WHERE u.enabled = 'yes' GROUP BY c.name HAVING sum(u.uploaded) > 1099511627776 AND count(u.id) >= 100 ORDER BY ul_avg DESC LIMIT $limit") or sqlerr();
          $HTMLOUT .= countriestable($r, sprintf($GLOBALS['lang']['country_avperuser'], $limit) . ($limit == 10 && $pu ? " <font class='small'> - [<a href='topten.php?type=3&amp;lim=25&amp;subtype=avg'>{$GLOBALS['lang']['common_top25']}</a>]</font>" : ""),$GLOBALS['lang']['country_avg']);
        }

        if ($limit == 10 || $subtype == "r")
        {
          $r = $GLOBALS['xoopsDB']->queryF("SELECT c.name, c.flagpic, sum(u.uploaded)/sum(u.downloaded) AS r FROM ".$GLOBALS['xoopsDB']->prefix("tb_users")." AS u LEFT JOIN ".$GLOBALS['xoopsDB']->prefix("countries")." AS c ON u.country = c.id WHERE u.enabled = 'yes' GROUP BY c.name HAVING sum(u.uploaded) > 1099511627776 AND sum(u.downloaded) > 1099511627776 AND count(u.id) >= 100 ORDER BY r DESC LIMIT $limit") or sqlerr();
          $HTMLOUT .= countriestable($r, sprintf($GLOBALS['lang']['country_ratio'], $limit) . ($limit == 10 && $pu ? " <font class='small'> - [<a href='topten.php?type=3&amp;lim=25&amp;subtype=r'>{$GLOBALS['lang']['common_top25']}</a>]</font>" : ""),$GLOBALS['lang']['common_ratio']);
        }
      }
      elseif ($type == 4)
      {
    //		print("<h1 align='center'><font color=''red''>Under construction!</font></h1>\n");
        if (!$limit || $limit > 250)
          $limit = 10;

          if ($limit == 10 || $subtype == "ul")
          {
    //				$r = $GLOBALS['xoopsDB']->queryF("SELECT ".$GLOBALS['xoopsDB']->prefix("tb_users").".id AS userid, ".$GLOBALS['xoopsDB']->prefix("peers").".id AS peerid, username, ".$GLOBALS['xoopsDB']->prefix("peers").".uploaded, ".$GLOBALS['xoopsDB']->prefix("peers").".downloaded, ".$GLOBALS['xoopsDB']->prefix("peers").".uploaded / (UNIX_TIMESTAMP(NOW()) - (UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(last_action)) - UNIX_TIMESTAMP(started)) AS uprate, ".$GLOBALS['xoopsDB']->prefix("peers").".downloaded / (UNIX_TIMESTAMP(NOW()) - (UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(last_action)) - UNIX_TIMESTAMP(started)) AS downrate FROM ".$GLOBALS['xoopsDB']->prefix("peers")." LEFT JOIN ".$GLOBALS['xoopsDB']->prefix("tb_users")." ON ".$GLOBALS['xoopsDB']->prefix("peers").".userid = ".$GLOBALS['xoopsDB']->prefix("tb_users").".id ORDER BY uprate DESC LIMIT $limit") or sqlerr();
    //				peerstable($r, "Top $limit Fastest Uploaders" . ($limit == 10 && $pu ? " <font class='small'> - [<a href='topten.php?type=4&amp;lim=100&amp;subtype=ul'>Top 100</a>] - [<a href='topten.php?type=4&amp;lim=250&amp;subtype=ul'>Top 250</a>]</font>" : ""));

    //				$r = $GLOBALS['xoopsDB']->queryF("SELECT ".$GLOBALS['xoopsDB']->prefix("tb_users").".id AS userid, ".$GLOBALS['xoopsDB']->prefix("peers").".id AS peerid, username, ".$GLOBALS['xoopsDB']->prefix("peers").".uploaded, ".$GLOBALS['xoopsDB']->prefix("peers").".downloaded, (".$GLOBALS['xoopsDB']->prefix("peers").".uploaded - ".$GLOBALS['xoopsDB']->prefix("peers").".uploadoffset) / (UNIX_TIMESTAMP(last_action) - UNIX_TIMESTAMP(started)) AS uprate, (".$GLOBALS['xoopsDB']->prefix("peers").".downloaded - ".$GLOBALS['xoopsDB']->prefix("peers").".downloadoffset) / (UNIX_TIMESTAMP(last_action) - UNIX_TIMESTAMP(started)) AS downrate FROM ".$GLOBALS['xoopsDB']->prefix("peers")." LEFT JOIN ".$GLOBALS['xoopsDB']->prefix("tb_users")." ON ".$GLOBALS['xoopsDB']->prefix("peers").".userid = ".$GLOBALS['xoopsDB']->prefix("tb_users").".id ORDER BY uprate DESC LIMIT $limit") or sqlerr();
    //				peerstable($r, "Top $limit Fastest Uploaders (timeout corrected)" . ($limit == 10 && $pu ? " <font class='small'> - [<a href='topten.php?type=4&amp;lim=100&amp;subtype=ul'>Top 100</a>] - [<a href='topten.php?type=4&amp;lim=250&amp;subtype=ul'>Top 250</a>]</font>" : ""));

            $r = $GLOBALS['xoopsDB']->queryF( "SELECT ".$GLOBALS['xoopsDB']->prefix("tb_users").".id AS userid, username, (".$GLOBALS['xoopsDB']->prefix("peers").".uploaded - ".$GLOBALS['xoopsDB']->prefix("peers").".uploadoffset) / (last_action - started) AS uprate, IF(seeder = 'yes',(".$GLOBALS['xoopsDB']->prefix("peers").".downloaded - ".$GLOBALS['xoopsDB']->prefix("peers").".downloadoffset)  / (finishedat - started),(".$GLOBALS['xoopsDB']->prefix("peers").".downloaded - ".$GLOBALS['xoopsDB']->prefix("peers").".downloadoffset) / (last_action - started)) AS downrate FROM ".$GLOBALS['xoopsDB']->prefix("peers")." LEFT JOIN ".$GLOBALS['xoopsDB']->prefix("tb_users")." ON ".$GLOBALS['xoopsDB']->prefix("peers").".userid = ".$GLOBALS['xoopsDB']->prefix("tb_users").".id ORDER BY uprate DESC LIMIT $limit") or sqlerr();
            $HTMLOUT .= peerstable($r, sprintf($GLOBALS['lang']['peers_fastestup'], $limit) . ($limit == 10 && $pu ? " <font class='small'> - [<a href='topten.php?type=4&amp;lim=100&amp;subtype=ul'>{$GLOBALS['lang']['common_top100']}</a>] - [<a href='topten.php?type=4&amp;lim=250&amp;subtype=ul'>{$GLOBALS['lang']['common_top250']}</a>]</font>" : ""));
          }

          if ($limit == 10 || $subtype == "dl")
          {
    //				$r = $GLOBALS['xoopsDB']->queryF("SELECT ".$GLOBALS['xoopsDB']->prefix("tb_users").".id AS userid, ".$GLOBALS['xoopsDB']->prefix("peers").".id AS peerid, username, ".$GLOBALS['xoopsDB']->prefix("peers").".uploaded, ".$GLOBALS['xoopsDB']->prefix("peers").".downloaded, (".$GLOBALS['xoopsDB']->prefix("peers").".uploaded - ".$GLOBALS['xoopsDB']->prefix("peers").".uploadoffset) / (UNIX_TIMESTAMP(last_action) - UNIX_TIMESTAMP(started)) AS uprate, (".$GLOBALS['xoopsDB']->prefix("peers").".downloaded - ".$GLOBALS['xoopsDB']->prefix("peers").".downloadoffset) / (UNIX_TIMESTAMP(last_action) - UNIX_TIMESTAMP(started)) AS downrate FROM ".$GLOBALS['xoopsDB']->prefix("peers")." LEFT JOIN ".$GLOBALS['xoopsDB']->prefix("tb_users")." ON ".$GLOBALS['xoopsDB']->prefix("peers").".userid = ".$GLOBALS['xoopsDB']->prefix("tb_users").".id ORDER BY downrate DESC LIMIT $limit") or sqlerr();
    //				peerstable($r, "Top $limit Fastest Downloaders (timeout corrected)" . ($limit == 10 && $pu ? " <font class='small'> - [<a href='topten.php?type=4&amp;lim=100&amp;subtype=dl'>Top 100</a>] - [<a href='topten.php?type=4&amp;lim=250&amp;subtype=dl'>Top 250</a>]</font>" : ""));

            $r = $GLOBALS['xoopsDB']->queryF("SELECT ".$GLOBALS['xoopsDB']->prefix("tb_users").".id AS userid, ".$GLOBALS['xoopsDB']->prefix("peers").".id AS peerid, username, ".$GLOBALS['xoopsDB']->prefix("peers").".uploaded, ".$GLOBALS['xoopsDB']->prefix("peers").".downloaded,(".$GLOBALS['xoopsDB']->prefix("peers").".uploaded - ".$GLOBALS['xoopsDB']->prefix("peers").".uploadoffset) / (last_action - started) AS uprate, IF(seeder = 'yes',(".$GLOBALS['xoopsDB']->prefix("peers").".downloaded - ".$GLOBALS['xoopsDB']->prefix("peers").".downloadoffset)  / (finishedat - started),(".$GLOBALS['xoopsDB']->prefix("peers").".downloaded - ".$GLOBALS['xoopsDB']->prefix("peers").".downloadoffset) / (last_action - started)) AS downrate FROM ".$GLOBALS['xoopsDB']->prefix("peers")." LEFT JOIN ".$GLOBALS['xoopsDB']->prefix("tb_users")." ON ".$GLOBALS['xoopsDB']->prefix("peers").".userid = ".$GLOBALS['xoopsDB']->prefix("tb_users").".id ORDER BY downrate DESC LIMIT $limit") or sqlerr();
            $HTMLOUT .= peerstable($r, sprintf($GLOBALS['lang']['peers_fastestdown'], $limit) . ($limit == 10 && $pu ? " <font class='small'> - [<a href='topten.php?type=4&amp;lim=100&amp;subtype=dl'>{$GLOBALS['lang']['common_top100']}</a>] - [<a href='topten.php?type=4&amp;lim=250&amp;subtype=dl'>{$GLOBALS['lang']['common_top250']}</a>]</font>" : ""));
          }
      }
      $HTMLOUT .= end_main_frame();

    $xoopsOption['template_main'] = 'tb_topten.html';
	include $GLOBALS['xoops']->path('header.php');
	$GLOBALS['xoopsTpl']->assign('xoConfig', $GLOBALS['TBDEV']);
	$GLOBALS['xoopsTpl']->assign('html', $HTMLOUT);
	$GLOBALS['xoopsTpl']->assign('xoops_pagetitle', $GLOBALS['lang']['head_title']);
	include $GLOBALS['xoops']->path('footer.php');
	
?>



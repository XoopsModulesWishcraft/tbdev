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
require_once "include/bittorrent.php";
require_once "include/user_functions.php";
require_once "include/html_functions.php";
require_once "include/bbcode_functions.php";

dbconn(false);

loggedinorreturn();

$GLOBALS['lang'] = array_merge( load_language('global'), load_language('userdetails') );


function maketable($res)
    {
      global $GLOBALS['TBDEV'], $GLOBALS['lang'];
      
      $htmlout = '';
      
      $htmlout .= "<table class='main' border='1' cellspacing='0' cellpadding='5'>" .
        "<tr><td class='colhead' align='center'>{$GLOBALS['lang']['userdetails_type']}</td><td class='colhead'>{$GLOBALS['lang']['userdetails_name']}</td><td class='colhead' align='center'>{$GLOBALS['lang']['userdetails_ttl']}</td><td class='colhead' align='center'>{$GLOBALS['lang']['userdetails_size']}</td><td class='colhead' align='right'>{$GLOBALS['lang']['userdetails_se']}</td><td class='colhead' align='right'>{$GLOBALS['lang']['userdetails_le']}</td><td class='colhead' align='center'>{$GLOBALS['lang']['userdetails_upl']}</td>\n" .
        "<td class='colhead' align='center'>{$GLOBALS['lang']['userdetails_downl']}</td><td class='colhead' align='center'>{$GLOBALS['lang']['userdetails_ratio']}</td></tr>\n";
      foreach ($res as $arr)
      {
        if ($arr["downloaded"] > 0)
        {
          $ratio = number_format($arr["uploaded"] / $arr["downloaded"], 3);
          $ratio = "<font color='" . get_ratio_color($ratio) . "'>$ratio</font>";
        }
        else
          if ($arr["uploaded"] > 0)
            $ratio = "{$GLOBALS['lang']['userdetails_inf']}";
          else
            $ratio = "---";
      $catimage = "{$GLOBALS['TBDEV']['pic_base_url']}caticons/{$arr['image']}";
      $catname = htmlspecialchars($arr["catname"]);
      $catimage = "<img src=\"".htmlspecialchars($catimage) ."\" title=\"$catname\" alt=\"$catname\" width='42' height='42' />";
      $ttl = (28*24) - floor((time() - $arr["added"]) / 3600);
      if ($ttl == 1) $ttl .= "<br />{$GLOBALS['lang']['userdetails_hour']}"; else $ttl .= "<br />{$GLOBALS['lang']['userdetails_hours']}";
      $size = str_replace(" ", "<br />", mksize($arr["size"]));
      $uploaded = str_replace(" ", "<br />", mksize($arr["uploaded"]));
      $downloaded = str_replace(" ", "<br />", mksize($arr["downloaded"]));
      $seeders = number_format($arr["seeders"]);
      $leechers = number_format($arr["leechers"]);
        $htmlout .= "<tr><td style='padding: 0px'>$catimage</td>\n" .
        "<td><a href='details.php?id=$arr[torrent]&amp;hit=1'><b>" . htmlspecialchars($arr["torrentname"]) .
        "</b></a></td><td align='center'>$ttl</td><td align='center'>$size</td><td align='right'>$seeders</td><td align='right'>$leechers</td><td align='center'>$uploaded</td>\n" .
        "<td align='center'>$downloaded</td><td align='center'>$ratio</td></tr>\n";
      }
      $htmlout .= "</table>\n";
      return $htmlout;
    }

    $id = 0 + $_GET["id"];

    if (!is_valid_id($id))
      stderr("{$GLOBALS['lang']['userdetails_error']}", "{$GLOBALS['lang']['userdetails_bad_id']}");
    
    
    
    $r = @$GLOBALS['xoopsDB']->queryF("SELECT * FROM ".$GLOBALS['xoopsDB']->prefix("users")." WHERE id=$id") or sqlerr();
    $user = $GLOBALS['xoopsDB']->fetchArray($r) or stderr("{$GLOBALS['lang']['userdetails_error']}", "{$GLOBALS['lang']['userdetails_no_user']}");
    if ($user["status"] == "pending") die;
    $r = $GLOBALS['xoopsDB']->queryF("SELECT t.id, t.name, t.seeders, t.leechers, c.name AS cname, c.image FROM ".$GLOBALS['xoopsDB']->prefix("torrents")." t LEFT JOIN ".$GLOBALS['xoopsDB']->prefix("categories")." c ON t.category = c.id WHERE t.owner = $id ORDER BY t.name") or sqlerr(__FILE__,__LINE__);
    if (mysql_num_rows($r) > 0)
    {
      $torrents = "<table class='main' border='1' cellspacing='0' cellpadding='5'>\n" .
        "<tr><td class='colhead'>{$GLOBALS['lang']['userdetails_type']}</td><td class='colhead'>{$GLOBALS['lang']['userdetails_name']}</td><td class='colhead'>{$GLOBALS['lang']['userdetails_seeders']}</td><td class='colhead'>{$GLOBALS['lang']['userdetails_leechers']}</td></tr>\n";
      while ($a = $GLOBALS['xoopsDB']->fetchArray($r))
      {
        //$r2 = $GLOBALS['xoopsDB']->queryF("SELECT name, image FROM ".$GLOBALS['xoopsDB']->prefix("categories")." WHERE id=$a[category]") or sqlerr(__FILE__, __LINE__);
        //$a2 = $GLOBALS['xoopsDB']->fetchArray($r2);
        $cat = "<img src=\"". htmlspecialchars("{$GLOBALS['TBDEV']['pic_base_url']}caticons/{$a['image']}") ."\" title=\"{$a['cname']}\" alt=\"{$a['cname']}\" />";
          $torrents .= "<tr><td style='padding: 0px'>$cat</td><td><a href='details.php?id=" . $a['id'] . "&amp;hit=1'><b>" . htmlspecialchars($a["name"]) . "</b></a></td>" .
            "<td align='right'>{$a['seeders']}</td><td align='right'>{$a['leechers']}</td></tr>\n";
      }
      $torrents .= "</table>";
    }

    if ($user['ip'] && ($GLOBALS['CURUSER']['class'] >= UC_MODERATOR || $user['id'] == $GLOBALS['CURUSER']['id']))
    {
        $dom = @gethostbyaddr($user['ip']);
        $addr = ($dom == $user['ip'] || @gethostbyname($dom) != $user['ip']) ? $user['ip'] : $user['ip'].' ('.$dom.')';
    }


    if ($user['added'] == 0)
      $joindate = "{$GLOBALS['lang']['userdetails_na']}";
    else
      $joindate = get_date( $user['added'],'');
    $lastseen = $user["last_access"];
    if ($lastseen == 0)
      $lastseen = "{$GLOBALS['lang']['userdetails_never']}";
    else
    {
      $lastseen = get_date( $user['last_access'],'',0,1);
    }


      $res = $GLOBALS['xoopsDB']->queryF("SELECT COUNT(*) FROM comments WHERE user=" . $user['id']) or sqlerr();
      $arr3 = mysql_fetch_row($res);
      $torrentcomments = $arr3[0];
      $res = $GLOBALS['xoopsDB']->queryF("SELECT COUNT(*) FROM posts WHERE userid=" . $user['id']) or sqlerr();
      $arr3 = mysql_fetch_row($res);
      $forumposts = $arr3[0];

    //if ($user['donated'] > 0)
    //  $don = "<img src='{$GLOBALS['TBDEV']['pic_base_url']}starbig.gif' alt='' />";
    $country = '';
    $res = $GLOBALS['xoopsDB']->queryF("SELECT name,flagpic FROM ".$GLOBALS['xoopsDB']->prefix("countries")." WHERE id=".$user['country']." LIMIT 1") or sqlerr();
    if (mysql_num_rows($res) == 1)
    {
      $arr = $GLOBALS['xoopsDB']->fetchArray($res);
      $country = "<td class='embedded'><img src=\"{$GLOBALS['TBDEV']['pic_base_url']}flag/{$arr['flagpic']}\" alt=\"". htmlspecialchars($arr['name']) ."\" style='margin-left: 8pt' /></td>";
    }

    //if ($user["donor"] == "yes") $donor = "<td class='embedded'><img src='{$GLOBALS['TBDEV']['pic_base_url']}starbig.gif' alt='Donor' style='margin-left: 4pt' /></td>";
    //if ($user["warned"] == "yes") $warned = "<td class='embedded'><img src=\"{$GLOBALS['TBDEV']['pic_base_url']}warnedbig.gif\" alt='Warned' style='margin-left: 4pt' /></td>";

    $res = $GLOBALS['xoopsDB']->queryF("SELECT p.torrent, p.uploaded, p.downloaded, p.seeder, t.added, t.name as torrentname, t.size, t.category, t.seeders, t.leechers, c.name as catname, c.image FROM ".$GLOBALS['xoopsDB']->prefix("peers")." p LEFT JOIN ".$GLOBALS['xoopsDB']->prefix("torrents")." t ON p.torrent = t.id LEFT JOIN ".$GLOBALS['xoopsDB']->prefix("categories")." c ON t.category = c.id WHERE p.userid=$id") or sqlerr();

    while ($arr = $GLOBALS['xoopsDB']->fetchArray($res))
    {
        if ($arr['seeder'] == 'yes')
            $seeding[] = $arr;
        else
            $leeching[] = $arr;
    }

    
    $HTMLOUT = '';
    
    $enabled = $user["enabled"] == 'yes';
    $HTMLOUT .= "<p></p><table class='main' border='0' cellspacing='0' cellpadding='0'>".
    "<tr><td class='embedded'><h1 style='margin:0px'>{$user['username']}" . get_user_icons($user, true) . "</h1></td>$country</tr></table><p></p>\n";

    if (!$enabled)
      $HTMLOUT .= "<p><b>{$GLOBALS['lang']['userdetails_disabled']}</b></p>\n";
    elseif ($GLOBALS['CURUSER']["id"] <> $user["id"])
    {
      $r = $GLOBALS['xoopsDB']->queryF("SELECT id FROM ".$GLOBALS['xoopsDB']->prefix("friends")." WHERE userid=$GLOBALS['CURUSER'][id] AND friendid=$id") or sqlerr(__FILE__, __LINE__);
      $friend = mysql_num_rows($r);
      $r = $GLOBALS['xoopsDB']->queryF("SELECT id FROM blocks WHERE userid=$GLOBALS['CURUSER'][id] AND blockid=$id") or sqlerr(__FILE__, __LINE__);
      $block = mysql_num_rows($r);

      if ($friend)
        $HTMLOUT .= "<p>(<a href='friends.php?action=delete&amp;type=friend&amp;targetid=$id'>{$GLOBALS['lang']['userdetails_remove_friends']}</a>)</p>\n";
      elseif($block)
        $HTMLOUT .= "<p>(<a href='friends.php?action=delete&amp;type=block&amp;targetid=$id'>{$GLOBALS['lang']['userdetails_remove_blocks']}</a>)</p>\n";
      else
      {
        $HTMLOUT .= "<p>(<a href='friends.php?action=add&amp;type=friend&amp;targetid=$id'>{$GLOBALS['lang']['userdetails_add_friends']}</a>)";
        $HTMLOUT .= " - (<a href='friends.php?action=add&amp;type=block&amp;targetid=$id'>{$GLOBALS['lang']['userdetails_add_blocks']}</a>)</p>\n";
      }
    }

    $HTMLOUT .= begin_main_frame();

    $HTMLOUT .= "<table width='100%' border='1' cellspacing='0' cellpadding='5'>
    <tr><td class='rowhead' width='1%'>{$GLOBALS['lang']['userdetails_joined']}</td><td align='left' width='99%'>{$joindate}</td></tr>
    <tr><td class='rowhead'>{$GLOBALS['lang']['userdetails_seen']}</td><td align='left'>{$lastseen}</td></tr>";

    if ($GLOBALS['CURUSER']['class'] >= UC_MODERATOR)
      $HTMLOUT .= "<tr><td class='rowhead'>{$GLOBALS['lang']['userdetails_email']}</td><td align='left'><a href='{$GLOBALS['TBDEV']['baseurl']}/email-gateway.php?id={$user['id']}'>{$user['email']}</a></td></tr>\n";
    if (isset($addr))
      $HTMLOUT .= "<tr><td class='rowhead'>{$GLOBALS['lang']['userdetails_address']}</td><td align='left'>$addr</td></tr>\n";

    //  if ($user["id"] == $GLOBALS['CURUSER']["id"] || $GLOBALS['CURUSER']['class'] >= UC_MODERATOR)
    //	{

    $HTMLOUT .= "<tr><td class='rowhead'>{$GLOBALS['lang']['userdetails_uploaded']}</td><td align='left'>".mksize($user["uploaded"])."</td></tr>
    <tr><td class='rowhead'>{$GLOBALS['lang']['userdetails_downloaded']}</td><td align='left'>".mksize($user["downloaded"])."</td></tr>";

    if ($user["downloaded"] > 0)
    {
      $sr = $user["uploaded"] / $user["downloaded"];
      if ($sr >= 4)
        $s = "w00t";
      else if ($sr >= 2)
        $s = "grin";
      else if ($sr >= 1)
        $s = "smile1";
      else if ($sr >= 0.5)
        $s = "noexpression";
      else if ($sr >= 0.25)
        $s = "sad";
      else
        $s = "cry";
      $sr = floor($sr * 1000) / 1000;
      $sr = "<table border='0' cellspacing='0' cellpadding='0'><tr><td class='embedded'><font color='" . get_ratio_color($sr) . "'>" . number_format($sr, 3) . "</font></td><td class='embedded'>&nbsp;&nbsp;<img src=\"{$GLOBALS['TBDEV']['pic_base_url']}smilies/{$s}.gif\" alt='' /></td></tr></table>";
      $HTMLOUT .= "<tr><td class='rowhead' style='vertical-align: middle'>Share ratio</td><td align='left' valign='middle' style='padding-top: 1px; padding-bottom: 0px'>$sr</td></tr>\n";
    }
    //}

    //if ($user['donated'] > 0 && ($GLOBALS['CURUSER']['class'] >= UC_MODERATOR || $GLOBALS['CURUSER']["id"] == $user["id"]))
    //  print("<tr><td class='rowhead'>Donated</td><td align='left'>$user[donated]</td></tr>\n");
    if ($user["avatar"])
    $HTMLOUT .= "<tr><td class='rowhead'>{$GLOBALS['lang']['userdetails_avatar']}</td><td align='left'><img src='" . htmlspecialchars($user["avatar"]) . "' width='{$user['av_w']}' height='{$user['av_h']}' alt='' /></td></tr>\n";
    $HTMLOUT .= "<tr><td class='rowhead'>{$GLOBALS['lang']['userdetails_class']}</td><td align='left'>" . get_user_class_name($user["class"]) . "</td></tr>\n";
    $HTMLOUT .= "<tr><td class='rowhead'>{$GLOBALS['lang']['userdetails_comments']}</td>";
    if ($torrentcomments && (($user["class"] >= UC_POWER_USER && $user["id"] == $GLOBALS['CURUSER']["id"]) || $GLOBALS['CURUSER']['class'] >= UC_MODERATOR))
      $HTMLOUT .= "<td align='left'><a href='userhistory.php?action=viewcomments&amp;id=$id'>$torrentcomments</a></td></tr>\n";
    else
      $HTMLOUT .= "<td align='left'>$torrentcomments</td></tr>\n";
    $HTMLOUT .= "<tr><td class='rowhead'>{$GLOBALS['lang']['userdetails_posts']}</td>";

    if ($forumposts && (($user["class"] >= UC_POWER_USER && $user["id"] == $GLOBALS['CURUSER']["id"]) || $GLOBALS['CURUSER']['class'] >= UC_MODERATOR))
      $HTMLOUT .= "<td align='left'><a href='userhistory.php?action=viewposts&amp;id=$id'>$forumposts</a></td></tr>\n";
    else
      $HTMLOUT .= "<td align='left'>$forumposts</td></tr>\n";

    if (isset($torrents))
      $HTMLOUT .= "<tr valign='top'><td class='rowhead'>{$GLOBALS['lang']['userdetails_uploaded_t']}</td><td align='left'>$torrents</td></tr>\n";
      
    if (isset($seeding))
      $HTMLOUT .= "<tr valign=top><td class=rowhead>{$GLOBALS['lang']['userdetails_cur_seed']}</td><td align=left>".maketable($seeding)."</td></tr>\n";
      
    if (isset($leeching))
       $HTMLOUT .= "<tr valign=top><td class=rowhead>{$GLOBALS['lang']['userdetails_cur_leech']}</td><td align=left>".maketable($leeching)."</td></tr>\n";
       
    if ($user["info"])
     $HTMLOUT .= "<tr valign='top'><td align='left' colspan='2' class='text' bgcolor='#F4F4F0'>" . format_comment($user["info"]) . "</td></tr>\n";

    if ($GLOBALS['CURUSER']["id"] != $user["id"])
      if ($GLOBALS['CURUSER']['class'] >= UC_MODERATOR)
        $showpmbutton = 1;
      elseif ($user["acceptpms"] == "yes")
      {
        $r = $GLOBALS['xoopsDB']->queryF("SELECT id FROM blocks WHERE userid={$user['id']} AND blockid={$GLOBALS['CURUSER']['id']}") or sqlerr(__FILE__,__LINE__);
        $showpmbutton = (mysql_num_rows($r) == 1 ? 0 : 1);
      }
      elseif ($user["acceptpms"] == "friends")
      {
        $r = $GLOBALS['xoopsDB']->queryF("SELECT id FROM ".$GLOBALS['xoopsDB']->prefix("friends")." WHERE userid=$user[id] AND friendid=$GLOBALS['CURUSER'][id]") or sqlerr(__FILE__,__LINE__);
        $showpmbutton = (mysql_num_rows($r) == 1 ? 1 : 0);
      }
    if (isset($showpmbutton))
      $HTMLOUT .= "<tr>
      <td colspan='2' align='center'>
      <form method='get' action='sendmessage.php'>
        <input type='hidden' name='receiver' value='{$user["id"]}' />
        <input type='submit' value='{$GLOBALS['lang']['userdetails_msg_btn']}' class='btn' />
      </form>
      </td></tr>";

    $HTMLOUT .= "</table>\n";

    if ($GLOBALS['CURUSER']['class'] >= UC_MODERATOR && $user["class"] < $GLOBALS['CURUSER']['class'])
    {
      $HTMLOUT .= begin_frame("{$GLOBALS['lang']['userdetails_edit_user']}", true);
      $HTMLOUT .= "<form method='post' action='modtask.php'>\n";
      $HTMLOUT .= "<input type='hidden' name='action' value='edituser' />\n";
      $HTMLOUT .= "<input type='hidden' name='userid' value='$id' />\n";
      $HTMLOUT .= "<input type='hidden' name='returnto' value='userdetails.php?id=$id' />\n";
      $HTMLOUT .= "<table class='main' border='1' cellspacing='0' cellpadding='5'>\n";
      $HTMLOUT .= "<tr><td class='rowhead'>{$GLOBALS['lang']['userdetails_title']}</td><td colspan='2' align='left'><input type='text' size='60' name='title' value='" . htmlspecialchars($user['title']) . "' /></td></tr>\n";
      $avatar = htmlspecialchars($user["avatar"]);
      $HTMLOUT .= "<tr><td class='rowhead'>{$GLOBALS['lang']['userdetails_avatar_url']}</td><td colspan='2' align='left'><input type='text' size='60' name='avatar' value='$avatar' /></td></tr>\n";
      // we do not want mods to be able to change user classes or amount donated...
      if ($GLOBALS['CURUSER']["class"] < UC_ADMINISTRATOR)
        $HTMLOUT .= "<input type='hidden' name='donor' value='$user[donor]' />\n";
      else
      {
        $HTMLOUT .= "<tr><td class='rowhead'>{$GLOBALS['lang']['userdetails_donor']}</td><td colspan='2' align='left'><input type='radio' name='donor' value='yes'" .($user["donor"] == "yes" ? " checked='checked'" : "")." />{$GLOBALS['lang']['userdetails_yes']} <input type='radio' name='donor' value='no'" .($user["donor"] == "no" ? " checked='checked'" : "")." />{$GLOBALS['lang']['userdetails_no']}</td></tr>\n";
      }

      if ($GLOBALS['CURUSER']['class'] == UC_MODERATOR && $user["class"] > UC_VIP)
        $HTMLOUT .= "<input type='hidden' name='class' value='{$user['class']}' />\n";
      else
      {
        $HTMLOUT .= "<tr><td class='rowhead'>Class</td><td colspan='2' align='left'><select name='class'>\n";
        if ($GLOBALS['CURUSER']['class'] == UC_MODERATOR)
          $maxclass = UC_VIP;
        else
          $maxclass = $GLOBALS['CURUSER']['class'] - 1;
        for ($i = 0; $i <= $maxclass; ++$i)
          $HTMLOUT .= "<option value='$i'" . ($user["class"] == $i ? " selected='selected'" : "") . ">" . get_user_class_name($i) . "</option>\n";
        $HTMLOUT .= "</select></td></tr>\n";
      }

      $modcomment = htmlspecialchars($user["modcomment"]);
      $HTMLOUT .= "<tr><td class='rowhead'>{$GLOBALS['lang']['userdetails_comment']}</td><td colspan='2' align='left'><textarea cols='60' rows='6' name='modcomment'>$modcomment</textarea></td></tr>\n";
      $warned = $user["warned"] == "yes";

      $HTMLOUT .= "<tr><td class='rowhead'" . (!$warned ? " rowspan='2'": "") . ">{$GLOBALS['lang']['userdetails_warned']}</td>
      <td align='left' width='20%'>" .
      ( $warned
      ? "<input name=warned value='yes' type='radio' checked='checked' />{$GLOBALS['lang']['userdetails_yes']}<input name='warned' value='no' type='radio' />{$GLOBALS['lang']['userdetails_no']}"
      : "{$GLOBALS['lang']['userdetails_no']}" ) ."</td>";

      if ($warned)
      {
        $warneduntil = $user['warneduntil'];
        if ($warneduntil == 0)
          $HTMLOUT .= "<td align='center'>{$GLOBALS['lang']['userdetails_dur']}</td></tr>\n";
        else
        {
          $HTMLOUT .= "<td align='center'>{$GLOBALS['lang']['userdetails_until']} ".get_date($warneduntil, 'DATE');
          $HTMLOUT .= " (" . mkprettytime($warneduntil - time())  . " {$GLOBALS['lang']['userdetails_togo']})</td></tr>\n";
        }
      }
      else
      {
        $HTMLOUT .= "<td>{$GLOBALS['lang']['userdetails_warn_for']} <select name='warnlength'>\n";
        $HTMLOUT .= "<option value='0'>{$GLOBALS['lang']['userdetails_warn0']}</option>\n";
        $HTMLOUT .= "<option value='1'>{$GLOBALS['lang']['userdetails_warn1']}</option>\n";
        $HTMLOUT .= "<option value='2'>{$GLOBALS['lang']['userdetails_warn2']}</option>\n";
        $HTMLOUT .= "<option value='4'>{$GLOBALS['lang']['userdetails_warn4']}</option>\n";
        $HTMLOUT .= "<option value='8'>{$GLOBALS['lang']['userdetails_warn8']}</option>\n";
        $HTMLOUT .= "<option value='255'>{$GLOBALS['lang']['userdetails_warninf']}</option>\n";
        $HTMLOUT .= "</select>{$GLOBALS['lang']['userdetails_pm_comm']}</td></tr>\n";
        $HTMLOUT .= "<tr><td colspan='2' align='left'><input type='text' size='60' name='warnpm' /></td></tr>";
      }
      $HTMLOUT .= "<tr><td class='rowhead'>{$GLOBALS['lang']['userdetails_enabled']}</td><td colspan='2' align='left'><input name='enabled' value='yes' type='radio'" . ($enabled ? " checked='checked'" : "") . " />{$GLOBALS['lang']['userdetails_yes']} <input name='enabled' value='no' type='radio'" . (!$enabled ? " checked='checked'" : "") . " />{$GLOBALS['lang']['userdetails_no']}</td></tr>\n";
      $HTMLOUT .= "<tr><td class='rowhead'>{$GLOBALS['lang']['userdetails_reset']}</td><td colspan='2'><input type='checkbox' name='resetpasskey' value='1' /><font class='small'>{$GLOBALS['lang']['userdetails_pass_msg']}</font></td></tr>";
      //$HTMLOUT .= "</td></tr>";
      $HTMLOUT .= "<tr><td colspan='3' align='center'><input type='submit' class='btn' value='{$GLOBALS['lang']['userdetails_okay']}' /></td></tr>\n";
      $HTMLOUT .= "</table>\n";
      $HTMLOUT .= "</form>\n";
      $HTMLOUT .= end_frame();
    }
    $HTMLOUT .= end_main_frame();
    
    
    print stdhead("{$GLOBALS['lang']['userdetails_details']} " . $user["username"]) . $HTMLOUT . stdfoot();

?>
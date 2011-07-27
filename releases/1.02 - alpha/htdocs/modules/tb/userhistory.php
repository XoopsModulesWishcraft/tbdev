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
require_once "include/bittorrent.php";
require_once "include/user_functions.php";
require_once "include/pager_functions.php";
require_once "include/html_functions.php";
require_once "include/bbcode_functions.php";

dbconn(false);

loggedinorreturn();

    $GLOBALS['lang'] = array_merge( load_language('global'), load_language('userhistory') );
    
    $userid = (int)$_GET["id"];

    if (!is_valid_id($userid)) stderr($GLOBALS['lang']['stderr_errorhead'], $GLOBALS['lang']['stderr_invalidid']);

    if ($GLOBALS['CURUSER']['class']< UC_POWER_USER || ($GLOBALS['CURUSER']["id"] != $userid && $GLOBALS['CURUSER']['class'] < UC_MODERATOR))
      stderr($GLOBALS['lang']['stderr_errorhead'], $GLOBALS['lang']['stderr_perms']);

    $page = (isset($_GET['page'])?$_GET["page"]:''); // not used?

    $action = (isset($_GET['action'])?$_GET["action"]:'');

    //-------- Global variables

    $perpage = 25;
    
    $HTMLOUT = '';

    //-------- Action: View posts

    if ($action == "viewposts")
    {
      $select_is = "COUNT(DISTINCT p.id)";

      $from_is = "posts AS p LEFT JOIN topics as t ON p.topicid = t.id LEFT JOIN forums AS f ON t.forumid = f.id";

      $where_is = "p.userid = $userid AND f.minclassread <= " . $GLOBALS['CURUSER']['class'];

      $order_is = "p.id DESC";

      $query = "SELECT $select_is FROM $from_is WHERE $where_is";

      $res = $GLOBALS['xoopsDB']->queryF($query) or sqlerr(__FILE__, __LINE__);

      $arr = mysql_fetch_row($res) or stderr($GLOBALS['lang']['stderr_errorhead'], $GLOBALS['lang']['top_noposts']);

      $postcount = $arr[0];

      //------ Make page menu

      $pager = pager($perpage, $postcount, "userhistory.php?action=viewposts&amp;id=$userid&amp;");

      //------ Get user data

      $res = $GLOBALS['xoopsDB']->queryF("SELECT username, donor, warned, enabled FROM ".$GLOBALS['xoopsDB']->prefix("users")." WHERE id=$userid") or sqlerr(__FILE__, __LINE__);

      if (mysql_num_rows($res) == 1)
      {
        $arr = $GLOBALS['xoopsDB']->fetchArray($res);

        $subject = "<a href='userdetails.php?id=$userid'><b>$arr[username]</b></a>" . get_user_icons($arr, true);
      }
      else
          $subject = $GLOBALS['lang']['posts_unknown'].'['.$userid.']';

      //------ Get posts

      $from_is = "posts AS p LEFT JOIN topics as t ON p.topicid = t.id LEFT JOIN forums AS f ON t.forumid = f.id LEFT JOIN readposts as r ON p.topicid = r.topicid AND p.userid = r.userid";

      $select_is = "f.id AS f_id, f.name, t.id AS t_id, t.subject, t.lastpost, r.lastpostread, p.*";

      $query = "SELECT $select_is FROM $from_is WHERE $where_is ORDER BY $order_is {$pager['limit']}";

      $res = $GLOBALS['xoopsDB']->queryF($query) or sqlerr(__FILE__, __LINE__);

      if (mysql_num_rows($res) == 0) stderr($GLOBALS['lang']['stderr_errorhead'], $GLOBALS['lang']['top_noposts']);

      

      $HTMLOUT .= "<h1>{$GLOBALS['lang']['top_posthfor']} $subject</h1>\n";

      if ($postcount > $perpage) 
        $HTMLOUT .= $pager['pagertop'];

      //------ Print table

      $HTMLOUT .= begin_main_frame();

      $HTMLOUT .= begin_frame();

      while ($arr = $GLOBALS['xoopsDB']->fetchArray($res))
      {
          $postid = $arr["id"];

          $posterid = $arr["userid"];

          $topicid = $arr["t_id"];

          $topicname = $arr["subject"];

          $forumid = $arr["f_id"];

          $forumname = $arr["name"];

        $dt = (time() - $GLOBALS['TBDEV']['readpost_expiry']);

        $newposts = 0;

        if ($arr['added'] > $dt)
          $newposts = ($arr["lastpostread"] < $arr["lastpost"]) && $GLOBALS['CURUSER']["id"] == $userid;

        $added = get_date( $arr['added'],'');

          $HTMLOUT .= "<div class='sub'><table border='0' cellspacing='0' cellpadding='0'>
          <tr><td class='embedded'>
          $added&nbsp;--&nbsp;<b>{$GLOBALS['lang']['posts_forum']}:&nbsp;</b>
          <a href='forums.php?action=viewforum&amp;forumid=$forumid'>$forumname</a>
          &nbsp;--&nbsp;<b>{$GLOBALS['lang']['posts_topic']}:&nbsp;</b>
          <a href='forums.php?action=viewtopic&amp;topicid=$topicid'>$topicname</a>
          &nbsp;--&nbsp;<b>{$GLOBALS['lang']['posts_post']}:&nbsp;</b>
          #<a href='forums.php?action=viewtopic&amp;topicid=$topicid&amp;page=p$postid#$postid'>$postid</a>" .
          ($newposts ? " &nbsp;<b>(<font color='red'>{$GLOBALS['lang']['posts_new']}</font>)</b>" : "") .
          "</td></tr></table></div>\n";

          $HTMLOUT .= begin_table(true);

          $body = format_comment($arr["body"]);

          if (is_valid_id($arr['editedby']))
          {
              $subres = $GLOBALS['xoopsDB']->queryF("SELECT username FROM ".$GLOBALS['xoopsDB']->prefix("users")." WHERE id=$arr[editedby]");
              if (mysql_num_rows($subres) == 1)
              {
                  $subrow = $GLOBALS['xoopsDB']->fetchArray($subres);
                  $body .= "<p><font size='1' class='small'>{$GLOBALS['lang']['posts_lasteditedby']} <a href='userdetails.php?id=$arr[editedby]'><b>$subrow[username]</b></a> {$GLOBALS['lang']['posts_at']} $arr[editedat] GMT</font></p>\n";
              }
          }

          $HTMLOUT .= "<tr valign='top'><td class='comment'>$body</td></tr>\n";

          $HTMLOUT .= end_table();
      }

      $HTMLOUT .= end_frame();

      $HTMLOUT .= end_main_frame();

      if ($postcount > $perpage) 
        $HTMLOUT .= $pager['pagerbottom'];

      print stdhead($GLOBALS['lang']['head_post']) . $HTMLOUT . stdfoot();

      die;
    }

    //-------- Action: View comments

    if ($action == "viewcomments")
    {
      $select_is = "COUNT(*)";

      // LEFT due to orphan comments
      $from_is = "comments AS c LEFT JOIN ".$GLOBALS['xoopsDB']->prefix("torrents")." as t
                  ON c.torrent = t.id";

      $where_is = "c.user = $userid";
      $order_is = "c.id DESC";

      $query = "SELECT $select_is FROM $from_is WHERE $where_is ORDER BY $order_is";

      $res = $GLOBALS['xoopsDB']->queryF($query) or sqlerr(__FILE__, __LINE__);

      $arr = mysql_fetch_row($res) or stderr($GLOBALS['lang']['stderr_errorhead'], $GLOBALS['lang']['top_nocomms']);

      $commentcount = $arr[0];

      //------ Make page menu

      $pager = pager($perpage, $commentcount, "userhistory.php?action=viewcomments&amp;id=$userid&amp;");

      //------ Get user data

      $res = $GLOBALS['xoopsDB']->queryF("SELECT username, donor, warned, enabled FROM ".$GLOBALS['xoopsDB']->prefix("users")." WHERE id=$userid") or sqlerr(__FILE__, __LINE__);

      if (mysql_num_rows($res) == 1)
      {
        $arr = $GLOBALS['xoopsDB']->fetchArray($res);

        $subject = "<a href='userdetails.php?id=$userid'><b>$arr[username]</b></a>" . get_user_icons($arr, true);
      }
      else
        $subject = $GLOBALS['lang']['posts_unknown'].'['.$userid.']';

      //------ Get comments

      $select_is = "t.name, c.torrent AS t_id, c.id, c.added, c.text";

      $query = "SELECT $select_is FROM $from_is WHERE $where_is ORDER BY $order_is {$pager['limit']}";

      $res = $GLOBALS['xoopsDB']->queryF($query) or sqlerr(__FILE__, __LINE__);

      if (mysql_num_rows($res) == 0) stderr($GLOBALS['lang']['stderr_errorhead'], $GLOBALS['lang']['top_nocomms']);

      

      $HTMLOUT .= "<h1>{$GLOBALS['lang']['top_commhfor']} $subject</h1>\n";

      if ($commentcount > $perpage) 
        $HTMLOUT .= $pager['pagertop'];

      //------ Print table

      $HTMLOUT .= begin_main_frame();

      $HTMLOUT .= begin_frame();

      while ($arr = $GLOBALS['xoopsDB']->fetchArray($res))
      {

        $commentid = $arr["id"];

        $torrent = $arr["name"];

        // make sure the line doesn't wrap
        if (strlen($torrent) > 55) $torrent = substr($torrent,0,52) . "...";

        $torrentid = $arr["t_id"];

        //find the page; this code should probably be in details.php instead

        $subres = $GLOBALS['xoopsDB']->queryF("SELECT COUNT(*) FROM comments WHERE torrent = $torrentid AND id < $commentid")
          or sqlerr(__FILE__, __LINE__);
        $subrow = mysql_fetch_row($subres);
        $count = $subrow[0];
        $comm_page = floor($count/20);
        $page_url = $comm_page?"&amp;page=$comm_page":"";

        $added = get_date( $arr['added'],'') . " (" . get_date( $arr['added'],'',0,1) . ")";

        $HTMLOUT .= "<div class='sub'><table border='0' cellspacing='0' cellpadding='0'><tr><td class='embedded'>".
        "$added&nbsp;---&nbsp;<b>{$GLOBALS['lang']['posts_torrent']}:&nbsp;</b>".
        ($torrent?("<a href='details.php?id=$torrentid&amp;tocomm=1'>$torrent</a>"):" [{$GLOBALS['lang']['posts_del']}] ").
        "&nbsp;---&nbsp;<b>{$GLOBALS['lang']['posts_comment']}:&nbsp;</b>#<a href='details.php?id=$torrentid&amp;tocomm=1$page_url'>$commentid</a>
        </td></tr></table></div>\n";

        $HTMLOUT .= begin_table(true);

        $body = format_comment($arr["text"]);

        $HTMLOUT .= "<tr valign='top'><td class='comment'>$body</td></tr>\n";

        $HTMLOUT .= end_table();
      }

      $HTMLOUT .= end_frame();

      $HTMLOUT .= end_main_frame();

      if ($commentcount > $perpage) 
        $HTMLOUT .= $pager['pagerbottom'];

      print stdhead($GLOBALS['lang']['head_comm']) . $HTMLOUT . stdfoot();

      die;
    }

    //-------- Handle unknown action

    if ($action != "")
      stderr($GLOBALS['lang']['stderr_histerrhead'], $GLOBALS['lang']['stderr_unknownact']);

    //-------- Any other case

    stderr($GLOBALS['lang']['stderr_histerrhead'], $GLOBALS['lang']['stderr_invalidq']);

?>
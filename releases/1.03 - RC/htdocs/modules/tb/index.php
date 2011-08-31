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
//ob_start("ob_gzhandler");
require_once '../../mainfile.php';
require_once "include/bittorrent.php";
require_once "include/user_functions.php";

dbconn(true);

loggedinorreturn();

    $GLOBALS['lang'] = array_merge( load_language('global'), load_language('index') );
    //$GLOBALS['lang'] = ;
    
    $HTMLOUT = '';
/*
$a = @$GLOBALS['xoopsDB']->fetchArray(@$GLOBALS['xoopsDB']->queryF("SELECT id,username FROM ".$GLOBALS['xoopsDB']->prefix("tb_users")." WHERE status='confirmed' ORDER BY id DESC LIMIT 1")) or die(mysql_error());
if ($GLOBALS['CURUSER'])
  $latestuser = "<a href='userdetails.php?id=" . $a["id"] . "'>" . $a["username"] . "</a>";
else
  $latestuser = $a['username'];
*/

    $registered = number_format(get_row_count("".$GLOBALS['xoopsDB']->prefix("tb_users").""));
    //$unverified = number_format(get_row_count("".$GLOBALS['xoopsDB']->prefix("tb_users")."", "WHERE status='pending'"));
    $torrents = number_format(get_row_count("".$GLOBALS['xoopsDB']->prefix("torrents").""));
    //$dead = number_format(get_row_count("torrents", "WHERE visible='no'"));

    $r = $GLOBALS['xoopsDB']->queryF("SELECT value_u FROM ".$GLOBALS['xoopsDB']->prefix("avps")." WHERE arg='seeders'") or sqlerr(__FILE__, __LINE__);
    $a = mysql_fetch_row($r);
    $seeders = 0 + $a[0];
    $r = $GLOBALS['xoopsDB']->queryF("SELECT value_u FROM ".$GLOBALS['xoopsDB']->prefix("avps")." WHERE arg='leechers'") or sqlerr(__FILE__, __LINE__);
    $a = mysql_fetch_row($r);
    $leechers = 0 + $a[0];
    if ($leechers == 0)
      $ratio = 0;
    else
      $ratio = round($seeders / $leechers * 100);
    $peers = number_format($seeders + $leechers);
    $seeders = number_format($seeders);
    $leechers = number_format($leechers);


	$xoopsOption['template_main'] = 'tb_index.html';
	
	include $GLOBALS['xoops']->path('header.php');
	
	$GLOBALS['xoopsTpl']->assign('xoConfig', $GLOBALS['TBDEV']);
	    
    if (get_user_class() >= UC_ADMINISTRATOR)
    	$GLOBALS['xoopsTpl']->assign('adminbutton', "&nbsp;<span style='float:right;'><a href='admin.php?action=news'>News page</a></span>\n");
      
    $res = $GLOBALS['xoopsDB']->queryF("SELECT * FROM ".$GLOBALS['xoopsDB']->prefix("news")." WHERE added + ( 3600 *24 *45 ) >
					".time()." ORDER BY added DESC LIMIT 10") or sqlerr(__FILE__, __LINE__);
					
    if (mysql_num_rows($res) > 0)
    {
      $button = "";
      while($array = $GLOBALS['xoopsDB']->fetchArray($res))
      {
        if (get_user_class() >= UC_ADMINISTRATOR)
        {
          $button = "<div style='float:right;'><a href='admin.php?action=news&amp;mode=edit&amp;newsid={$array['id']}'>{$GLOBALS['lang']['news_edit']}</a>&nbsp;<a href='admin.php?action=news&amp;mode=delete&amp;newsid={$array['id']}'>{$GLOBALS['lang']['news_delete']}</a></div>";
        }
      	$GLOBALS['xoopsTpl']->append('news', array('headline'=>$array['headline'], 'added'=>get_date( $array['added'],'DATE'), 'button'=>$button, 'body' => $array['body']));  
      }
    }

    
    $GLOBALS['xoopsTpl']->assign('registered', $registered);
    $GLOBALS['xoopsTpl']->assign('unverified', $unverified);
    $GLOBALS['xoopsTpl']->assign('torrents', $torrents);
      
    if (isset($peers)) 
    {
    	$GLOBALS['xoopsTpl']->assign('peers', $peers);
	    $GLOBALS['xoopsTpl']->assign('seeders', $seeders);
	    $GLOBALS['xoopsTpl']->assign('leechers', $leechers);
	    $GLOBALS['xoopsTpl']->assign('ratio', $ratio);
    } 
    include $GLOBALS['xoops']->path('footer.php');
?>
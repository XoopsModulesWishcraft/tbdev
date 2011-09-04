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

    $GLOBALS['lang'] = array_merge( $GLOBALS['lang'], load_language('ok') );
    
    $type = isset($_GET['type']) ? $_GET['type'] : '';
    
    $xoopsOption['template_main'] = 'tb_ok.html';
	stdhead($title, '', '', 0);
	$GLOBALS['xoopsTpl']->assign('xoConfig', $GLOBALS['TBDEV']);
	$GLOBALS['xoopsTpl']->assign('type', $type);
    
    if ( $type == "signup" && isset($_GET['email']) ) 
    {
      stderr( "{$GLOBALS['lang']['ok_success']}", sprintf($GLOBALS['lang']['ok_email'], htmlentities($_GET['email'], ENT_QUOTES)) );
    }
    elseif ($type == "sysop") 
    {
	  $xoopsOption['template_main'] = 'tb_ok.html';	
      $GLOBALS['xoopsTpl']->assign('header', $GLOBALS['lang']['ok_sysop_account']);
      $GLOBALS['xoopsTpl']->assign('messagea', $GLOBALS['lang']['ok_sysop_activated']);
      if (isset($GLOBALS['CURUSER']))
      {
      	$GLOBALS['xoopsTpl']->assign('messageb', $GLOBALS['lang']['ok_account_activated']);
      }
      else
      {
      	$GLOBALS['xoopsTpl']->assign('messageb', $GLOBALS['lang']['ok_account_login']);
      }
    }
    elseif ($type == "confirmed") 
    {
      $xoopsOption['template_main'] = 'tb_ok.html';
      $GLOBALS['xoopsTpl']->assign('header', $GLOBALS['lang']['ok_confirmed']);
      $GLOBALS['xoopsTpl']->assign('messagea', $GLOBALS['lang']['ok_user_confirmed']);
     }
    elseif ($type == "confirm") 
    {
      if (isset($GLOBALS['CURUSER'])) 
      {
        $xoopsOption['template_main'] = 'tb_ok.html';
        $GLOBALS['xoopsTpl']->assign('header', $GLOBALS['lang']['ok_signup_confirm']);
        $GLOBALS['xoopsTpl']->assign('headerb', $GLOBALS['lang']['ok_success_confirmed']);
        $GLOBALS['xoopsTpl']->assign('messagea', "<p>".sprintf($GLOBALS['lang']['ok_account_active_login'], "<a href='{$GLOBALS['TBDEV']['baseurl']}/index.php'><b>{$GLOBALS['lang']['ok_account_active_login_link']}</b></a>")."</p>\n");
 		$GLOBALS['xoopsTpl']->assign('messageb',sprintf($GLOBALS['lang']['ok_read_rules'], $GLOBALS['TBDEV']['site_name']));         
       }
      else 
      {
        $xoopsOption['template_main'] = 'tb_ok.html';
        $GLOBALS['xoopsTpl']->assign('header', $GLOBALS['lang']['ok_signup_confirm']);
        $GLOBALS['xoopsTpl']->assign('headerb', $GLOBALS['lang']['ok_success_confirmed']);
        $GLOBALS['xoopsTpl']->assign('messagea', $GLOBALS['lang']['ok_account_cookies']);
       }
    }
    else
    {
    stderr("{$GLOBALS['lang']['ok_user_error']}", "{$GLOBALS['lang']['ok_no_action']}");
    }
    
 stdfoot();
?>
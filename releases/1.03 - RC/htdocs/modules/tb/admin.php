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
    define('IN_TBDEV_ADMIN', TRUE);

    require_once '../../include/cp_header.php';
    require_once "include/bittorrent.php";
    require_once "include/user_functions.php";

    xoops_cp_header();
	
    dbconn(false);

    loggedinorreturn();
    
    $GLOBALS['lang'] = array_merge( load_language('global'), load_language('admin') );
  
    if ($GLOBALS['CURUSER']['class'] < UC_MODERATOR)
      stderr("{$GLOBALS['lang']['admin_user_error']}", "{$GLOBALS['lang']['admin_unexpected']}");
  
  
    $action = isset($_GET["action"]) ? $_GET["action"] : '';
    $forum_pic_url = $GLOBALS['TBDEV']['pic_base_url'] . 'forumicons/';
  
    define('F_IMAGES', $GLOBALS['TBDEV']['pic_base_url'] . 'forumicons');
    define('POST_ICONS', F_IMAGES.'/post_icons');
    
    $ad_actions = array('bans'            => 'bans', 
                        'adduser'         => 'adduser', 
                        'stats'           => 'stats', 
                        'delacct'         => 'delacct', 
                        'testip'          => 'testip', 
                        'usersearch'      => 'usersearch', 
                        'mysql_overview'  => 'mysql_overview', 
                        'mysql_stats'     => 'mysql_stats', 
                        'categories'      => 'categories', 
                        'newusers'        => 'newusers', 
                        'docleanup'       => 'docleanup',
                        'log'             => 'log',
                        'news'            => 'news'
                        );

    if( in_array($action, $ad_actions) AND file_exists( "admin/{$ad_actions[ $action ]}.php" ) )
    {
      require_once "admin/{$ad_actions[ $action ]}.php";
    }
    else
    {
      require_once "admin/index.php";
    }
    xoops_cp_footer();
?>
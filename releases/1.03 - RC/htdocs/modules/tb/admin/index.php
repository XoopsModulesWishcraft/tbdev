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
if ( ! defined( 'IN_TBDEV_ADMIN' ) )
{
	print "<h1>Incorrect access</h1>You cannot access this file directly.";
	exit();
}

include('header.php');
xoops_cp_header();
loadModuleAdminMenu(1);
include_once $GLOBALS['xoops']->path( "/class/template.php" );
$GLOBALS['tbTpl'] = new XoopsTpl();
$GLOBALS['tbTpl']->assign('php_self', $_SERVER['PHP_SELF']);

xoops_loadLanguage('admin', 'tb');

require_once "include/html_functions.php";
require_once "include/user_functions.php";


    $GLOBALS['lang'] = array_merge( $GLOBALS['lang'], load_language('ad_index') );

    $HTMLOUT = '';

    $HTMLOUT .= "<br />

    <br />
		<table width='75%' cellpadding='10px'>
		<tr><td class='colhead'>Staff Tools</td></tr>
		<!-- row 1 -->
		<tr><td>
		
			
			<span class='btn'><a href='admin.php?action=bans'>{$GLOBALS['lang']['index_bans']}</a></span>
			
			<span class='btn'><a href='admin.php?action=adduser'>{$GLOBALS['lang']['index_new_user']}</a></span>
			
			<span class='btn'><a href='admin.php?action=log'>{$GLOBALS['lang']['index_log']}</a></span>
			
			<span class='btn'><a href='admin.php?action=docleanup'>{$GLOBALS['lang']['index_mcleanup']}</a></span>
			
			<span class='btn'><a href='".$GLOBALS['xoopsDB']->prefix("tb_users").".php'>{$GLOBALS['lang']['index_user_list']}</a></span>
			
			</td></tr>
			<!-- row 2 -->
			<tr><td>
			
			<span class='btn'><a href='tags.php'>{$GLOBALS['lang']['index_tags']}</a></span>
			

			<span class='btn'><a href='smilies.php'>{$GLOBALS['lang']['index_emoticons']}</a></span>
			
			<span class='btn'><a href='admin.php?action=delacct'>{$GLOBALS['lang']['index_delacct']}</a></span>
			

			<span class='btn'><a href='admin.php?action=stats'>{$GLOBALS['lang']['index_stats']}</a></span>
			
			</td></tr>
			<!-- roow 3 -->
			<tr><td>
			
			<span class='btn'><a href='admin.php?action=testip'>{$GLOBALS['lang']['index_testip']}</a></span>
			

			<span class='btn'><a href='admin.php?action=usersearch'>{$GLOBALS['lang']['index_user_search']}</a></span>
			

			<span class='btn'><a href='admin.php?action=mysql_overview'>{$GLOBALS['lang']['index_mysql_overview']}</a></span>
			

			<span class='btn'><a href='admin.php?action=mysql_stats'>{$GLOBALS['lang']['index_mysql_stats']}</a></span>
			
			
			</td></tr>
			<!-- row 4 -->
			<tr><td>
			
			<span class='btn'><a href='admin.php?action=forummanage'>{$GLOBALS['lang']['index_forummanage']}</a></span>
			

			<span class='btn'><a href='admin.php?action=categories'>{$GLOBALS['lang']['index_categories']}</a></span>
			
			</td></tr>
			<!-- row 5 -->
			<tr><td>
			
			<span class='btn'><a href='reputation_ad.php'>{$GLOBALS['lang']['index_rep_system']}</a></span>
			
			<span class='btn'><a href='reputation_settings.php'>{$GLOBALS['lang']['index_rep_settings']}</a></span>
			
			<span class='btn'><a href='admin.php?action=news'>{$GLOBALS['lang']['index_news']}</a></span>
			
			
		</td></tr></table>";
 

    $GLOBALS['tbTpl']->assign('html', $HTMLOUT);
    $GLOBALS['tbTpl']->display('db:tb_cpanel_index.html');
	xoops_cp_footer();
	exit(0);

?>
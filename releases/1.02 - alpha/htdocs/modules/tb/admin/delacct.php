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
	print "<h1>{$GLOBALS['lang']['text_incorrect']}</h1>{$GLOBALS['lang']['text_cannot']}";
	exit();
}

include('header.php');
xoops_cp_header();
loadModuleAdminMenu(5);
include_once $GLOBALS['xoops']->path( "/class/template.php" );
$GLOBALS['tbTpl'] = new XoopsTpl();
$GLOBALS['tbTpl']->assign('php_self', $_SERVER['PHP_SELF']);

xoops_loadLanguage('admin', 'tb');

require_once "include/user_functions.php";

    $GLOBALS['lang'] = array_merge( $GLOBALS['lang'], load_language('ad_delacct') );
    
    if ($_SERVER["REQUEST_METHOD"] == "POST")
    {
      $username = trim($_POST["username"]);
      $password = trim($_POST["password"]);
      if (!$username || !$password)
        stderr("{$GLOBALS['lang']['text_error']}", "{$GLOBALS['lang']['text_please']}");
        
      $res = @$GLOBALS['xoopsDB']->queryF("SELECT * FROM ".$GLOBALS['xoopsDB']->prefix("users")." WHERE username=" . sqlesc($username) 
                          . "AND passhash=md5(concat(secret,concat(" . sqlesc($password) . ",secret)))") 
                          or sqlerr();
      if (mysql_num_rows($res) != 1)
        stderr("{$GLOBALS['lang']['text_error']}", "{$GLOBALS['lang']['text_bad']}");
      $arr = $GLOBALS['xoopsDB']->fetchArray($res);

      $id = $arr['id'];
      $res = @$GLOBALS['xoopsDB']->queryF("DELETE FROM ".$GLOBALS['xoopsDB']->prefix("users")." WHERE id=$id") or sqlerr();
      if (mysql_affected_rows() != 1)
        stderr("{$GLOBALS['lang']['text_error']}", "{$GLOBALS['lang']['text_unable']}");
        
      stderr("{$GLOBALS['lang']['stderr_success']}", "{$GLOBALS['lang']['text_success']}");
    }
    
    $HTMLOUT = "
    <h1>{$GLOBALS['lang']['text_delete']}</h1>
    <form method='post' action='admin.php?action=delacct'>
    <table border='1' cellspacing='0' cellpadding='5'>
      <tr>
        <td class='rowhead'>{$GLOBALS['lang']['table_username']}</td>
        <td><input size='40' name='username' /></td>
      </tr>
      <tr>
        <td class='rowhead'>{$GLOBALS['lang']['table_password']}</td>
        <td><input type='password' size='40' name='password' /></td>
      </tr>
      <tr>
        <td colspan='2'><input type='submit' class='btn' value='{$GLOBALS['lang']['btn_delete']}' /></td>
      </tr>
    </table>
    </form>";

    $GLOBALS['tbTpl']->assign('html', $HTMLOUT);
    $GLOBALS['tbTpl']->display('db:tb_cpanel_delacct.html');
	xoops_cp_footer();
	exit(0);
?>
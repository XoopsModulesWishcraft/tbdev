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
loadModuleAdminMenu(3);
include_once $GLOBALS['xoops']->path( "/class/template.php" );
$GLOBALS['tbTpl'] = new XoopsTpl();
$GLOBALS['tbTpl']->assign('php_self', $_SERVER['PHP_SELF']);

xoops_loadLanguage('admin', 'tb');

require_once "include/user_functions.php";
require_once "include/password_functions.php";

    $GLOBALS['lang'] = array_merge( $GLOBALS['lang'], load_language('ad_adduser') );
    
    if (get_user_class() < UC_ADMINISTRATOR)
      stderr("{$GLOBALS['lang']['stderr_error']}", "{$GLOBALS['lang']['text_denied']}");
      
      
    if ($_SERVER["REQUEST_METHOD"] == "POST")
    {
      if ($_POST["username"] == "" || $_POST["password"] == "" || $_POST["email"] == "")
        stderr("{$GLOBALS['lang']['stderr_error']}", "{$GLOBALS['lang']['text_missing']}");
      if ($_POST["password"] != $_POST["password2"])
        stderr("{$GLOBALS['lang']['stderr_error']}", "{$GLOBALS['lang']['text_passwd']}");
      if (!validemail($_POST['email']))
        stderr("{$GLOBALS['lang']['stderr_error']}", "{$GLOBALS['lang']['text_email']}");
      
      $username = sqlesc($_POST["username"]);
      $password = $_POST["password"];
      $email = sqlesc($_POST["email"]);
      $secret = mksecret();
      $passhash = sqlesc( make_passhash( $secret, md5($password) ) );
      $secret = sqlesc($secret);
      $time_now = time();
      
      @$GLOBALS['xoopsDB']->queryF("INSERT INTO ".$GLOBALS['xoopsDB']->prefix("tb_users")." (added, last_access, secret, username, passhash, status, email) VALUES($time_now, $time_now, $secret, $username, $passhash, 'confirmed', $email)") or sqlerr(__FILE__, __LINE__);
      $res = @$GLOBALS['xoopsDB']->queryF("SELECT id FROM ".$GLOBALS['xoopsDB']->prefix("tb_users")." WHERE username=$username");
      $arr = mysql_fetch_row($res);
      if (!$arr)
        stderr("{$GLOBALS['lang']['stderr_error']}", "{$GLOBALS['lang']['text_username']}");
      header("Location: {$GLOBALS['TBDEV']['baseurl']}/userdetails.php?id=$arr[0]");
      die;
    }
    

    $HTMLOUT = '';
    
    $HTMLOUT .= "<h1>{$GLOBALS['lang']['text_adduser']}</h1>
    <br />
    <form method='post' action='admin.php?action=adduser'>
    <table border='1' cellspacing='0' cellpadding='5'>
    <tr><td class='rowhead'>{$GLOBALS['lang']['table_username']}</td><td><input type='text' name='username' size='40' /></td></tr>
    <tr><td class='rowhead'>{$GLOBALS['lang']['table_password']}</td><td><input type='password' name='password' size='40' /></td></tr>
    <tr><td class='rowhead'>{$GLOBALS['lang']['table_repasswd']}</td><td><input type='password' name='password2' size='40' /></td></tr>
    <tr><td class='rowhead'>{$GLOBALS['lang']['table_email']}</td><td><input type='text' name='email' size='40' /></td></tr>
    <tr><td colspan='2' align='center'><input type='submit' value='{$GLOBALS['lang']['btn_okay']}' class='btn' /></td></tr>
    </table>
    </form>";
    
    $GLOBALS['tbTpl']->assign('html', $HTMLOUT);
   	$GLOBALS['tbTpl']->display('db:tb_cpanel_adduser.html');
	xoops_cp_footer();
	exit(0);
?>
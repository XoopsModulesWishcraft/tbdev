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
require_once "include/bittorrent.php";
require_once "include/user_functions.php";

    $GLOBALS['lang'] = array_merge( load_language('global'), load_language('confirmemail') );
    
    if ( !isset($_GET['tbuid']) OR !isset($_GET['key']) OR !isset($_GET['email']) )
      stderr("{$GLOBALS['lang']['confirmmail_user_error']}", "{$GLOBALS['lang']['confirmmail_idiot']}");

    if (! preg_match( "/^(?:[\d\w]){32}$/", $_GET['key'] ) )
		{
			stderr( "{$GLOBALS['lang']['confirmmail_user_error']}", "{$GLOBALS['lang']['confirmmail_no_key']}" );
		}
		
		if (! preg_match( "/^(?:\d){1,}$/", $_GET['tbuid'] ) )
		{
			stderr( "{$GLOBALS['lang']['confirmmail_user-error']}", "{$GLOBALS['lang']['confirmmail_no_id']}" );
		}

    $id = intval($_GET['tbuid']);
    $md5 = $_GET['key'];
    $email = urldecode($_GET['email']);
    
    if( !validemail($email) )
      stderr("{$GLOBALS['lang']['confirmmail_user_error']}", "{$GLOBALS['lang']['confirmmail_false_email']}");

dbconn();


    $res = $GLOBALS['xoopsDB']->queryF("SELECT editsecret FROM ".$GLOBALS['xoopsDB']->prefix("tb_users")." WHERE id = $id");
    $row = $GLOBALS['xoopsDB']->fetchArray($res);

    if (!$row)
      stderr("{$GLOBALS['lang']['confirmmail_user_error']}", "{$GLOBALS['lang']['confirmmail_not_complete']}");

    //$sec = hash_pad($row["editsecret"]);
    $sec = $row['editsecret'];
    if (preg_match('/^ *$/s', $sec))
      stderr("{$GLOBALS['lang']['confirmmail_user_error']}", "{$GLOBALS['lang']['confirmmail_not_complete']}");
      
    if ($md5 != md5($sec . $email . $sec))
      stderr("{$GLOBALS['lang']['confirmmail_user_error']}", "{$GLOBALS['lang']['confirmmail_not_complete']}");

   @$GLOBALS['xoopsDB']->queryF("UPDATE ".$GLOBALS['xoopsDB']->prefix("tb_users")." SET editsecret='', email=" . sqlesc($email) . " WHERE id=$id AND editsecret=" . sqlesc($row["editsecret"]));

    if (!mysql_affected_rows())
      stderr("{$GLOBALS['lang']['confirmmail_user_error']}", "{$GLOBALS['lang']['confirmmail_not_complete']}");

    header("Refresh: 0; url={$GLOBALS['TBDEV']['baseurl']}/my.php?emailch=1");


?>
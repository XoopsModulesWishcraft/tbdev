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

    $GLOBALS['lang'] = array_merge( $GLOBALS['lang'], load_language('confirmemail') );
    
    if ( !isset($_GET['tbuid']) OR !isset($_GET['key']) OR !isset($_GET['email']) )
      stderr("{$GLOBALS['lang']['confirmmail_user_error']}", "{$GLOBALS['lang']['confirmmail_idiot']}");

    if (! preg_match( "/^(?:[\d\w]){32}$/", $_GET['key'] )) {
			stderr( "{$GLOBALS['lang']['confirmmail_user_error']}", "{$GLOBALS['lang']['confirmmail_no_key']}" );
	}
		
	if (! preg_match( "/^(?:\d){1,}$/", $_GET['tbuid'] )) {
			stderr( "{$GLOBALS['lang']['confirmmail_user-error']}", "{$GLOBALS['lang']['confirmmail_no_id']}" );
	}

	$id = intval($_GET['tbuid']);
    $md5 = $_GET['key'];
    $email = urldecode($_GET['email']);
    
    if( !validemail($email) )
      stderr("{$GLOBALS['lang']['confirmmail_user_error']}", "{$GLOBALS['lang']['confirmmail_false_email']}");


	$users_handler = xoops_getmodulehandler('users', 'td');
	$user = $users_handler->get($id);

    if (!is_object($user))
      stderr("{$GLOBALS['lang']['confirmmail_user_error']}", "{$GLOBALS['lang']['confirmmail_not_complete']}");

    $sec = $user->getVar('editsecret');
    if (preg_match('/^ *$/s', $sec))
      stderr("{$GLOBALS['lang']['confirmmail_user_error']}", "{$GLOBALS['lang']['confirmmail_not_complete']}");
      
    if ($md5 != md5($sec . $email . $sec))
      stderr("{$GLOBALS['lang']['confirmmail_user_error']}", "{$GLOBALS['lang']['confirmmail_not_complete']}");

   	$user->setVar('status', 'confirmed');
   	$user->setVar('editsecret', '');

    if (!$users_handler->insert($user))
      stderr("{$GLOBALS['lang']['confirmmail_user_error']}", "{$GLOBALS['lang']['confirmmail_not_complete']}");

    header("Refresh: 0; url={$GLOBALS['TBDEV']['baseurl']}/my.php?emailch=1");


?>
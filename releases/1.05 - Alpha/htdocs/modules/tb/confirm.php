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

    $GLOBALS['lang'] = array_merge( $GLOBALS['lang'], load_language('confirm') );
    
    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    $md5 = isset($_GET['secret']) ? $_GET['secret'] : '';

    if (!is_valid_id($id))
      stderr("{$GLOBALS['lang']['confirm_user_error']}", "{$GLOBALS['lang']['confirm_invalid_id']}");
    
    if (! preg_match( "/^(?:[\d\w]){32}$/", $md5 ))	{
		stderr("{$GLOBALS['lang']['confirm_user_error']}", "{$GLOBALS['lang']['confirm_invalid_key']}");
	}
		
	$users_handler = xoops_getmodulehandler('users', 'td');
	$user = $users_handler->get($id);

    if (!is_object($user))
      stderr("{$GLOBALS['lang']['confirm_user_error']}", "{$GLOBALS['lang']['confirm_invalid_id']}");

    if ($user->getVar('status') != 'pending') {
      header("Refresh: 0; url={$GLOBALS['TBDEV']['baseurl']}/ok.php?type=confirmed");
      exit();
    }

    $sec = $user->getVar('editsecret');
    if ($md5 != $sec)
      stderr("{$GLOBALS['lang']['confirm_user_error']}", "{$GLOBALS['lang']['confirm_cannot_confirm']}");
      
   	$user->setVar('status', 'confirmed');
   	$user->setVar('editsecret', '');

    if (!$users_handler->insert($user))
      stderr("{$GLOBALS['lang']['confirm_user_error']}", "{$GLOBALS['lang']['confirm_cannot_confirm']}");

    logincookie($id, $user->getVar('passhash'));

    header("Refresh: 0; url={$GLOBALS['TBDEV']['baseurl']}/ok.php?type=confirm");

?>
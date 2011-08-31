<?php
require_once '../../mainfile.php';
require_once 'include/bittorrent.php';
require_once "include/password_functions.php";

if (is_object($GLOBALS['xoopsUser'])) {
	
	$res = $GLOBALS['xoopsDB']->queryF("SELECT id, passhash, secret, enabled FROM ".$GLOBALS['xoopsDB']->prefix("tb_users")." WHERE username = " . sqlesc($GLOBALS['xoopsUser']->getVar('uname')) . " AND status = 'confirmed'");
    $row = $GLOBALS['xoopsDB']->fetchArray($res);
    if (!$row) {
    	$lang = array_merge( load_language('global'), load_language('takesignup') );
    
	    $res = $GLOBALS['xoopsDB']->queryF("SELECT COUNT(*) FROM ".$GLOBALS['xoopsDB']->prefix("tb_users")."") or sqlerr(__FILE__, __LINE__);
	    $arr = mysql_fetch_row($res);
	    
	    if ($arr[0] >= $TBDEV['maxusers'])
	      stderr($lang['takesignup_error'], $lang['takesignup_limit']);
	      
        $secret = mksecret();
	    $wantpasshash = make_passhash( $secret, $GLOBALS['xoopsUser']->getVar('pass') );
	    $editsecret = ( !$arr[0] ? "" : make_passhash_login_key() );

	    $ret = $GLOBALS['xoopsDB']->queryF("INSERT INTO ".$GLOBALS['xoopsDB']->prefix("tb_users")." (username, uid, passhash, passkey, secret, editsecret, email, status, class, added, time_offset, dst_in_use) VALUES (" .
			implode(",", array_map("sqlesc", array($GLOBALS['xoopsUser']->getVar('uname'), $GLOBALS['xoopsUser']->getVar('tbuid'), $wantpasshash, md5($wantpasshash.$secret.XOOPS_LICENSE_KEY.microtime()),$secret, $editsecret, $GLOBALS['xoopsUser']->getVar('email'), 'confirmed'))).
			", ". ($GLOBALS['xoopsUser']->isAdmin()?UC_SYSOP:UC_USER).",". time() .", \"".$time_offset."\", \"".$dst_in_use['tm_isdst'].'")');

	    if (!$ret) 
	    {
	      if (mysql_errno() == 1062)
	        stderr($lang['takesignup_user_error'], $lang['takesignup_user_exists']);
	      stderr($lang['takesignup_user_error'], $lang['takesignup_fatal_error']);
	    }
	
		$res = $GLOBALS['xoopsDB']->queryF("SELECT id, passhash, secret, enabled FROM users WHERE username = " . sqlesc($GLOBALS['xoopsUser']->getVar('uname')) . " AND status = 'confirmed'");
	    $row = $GLOBALS['xoopsDB']->fetchArray($res);
    }
	
    if ($row['enabled'] == 'no')
      stderr($lang['tlogin_failed'], $lang['tlogin_disabled']);

    logincookie($row['id'], $row['passhash']);
	$returnto = $_POST['returnto'];
	if (!empty($returnto))
    	header("Location: ".$returnto);
    else
    	header("Location: {$TBDEV['baseurl']}/my.php");  
} else {
	redirect_header(XOOPS_URL.'/user.php', 10, 'You have to log-in first!');
}
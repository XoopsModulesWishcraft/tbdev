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
	
	loggedinorreturn();

    $GLOBALS['lang'] = array_merge( $GLOBALS['lang'], load_language('email-gateway') );
    
    $id = 0 + $_GET["id"];
    
    if ( !is_valid_id($id) )
      stderr("{$GLOBALS['lang']['email_error']}", "{$GLOBALS['lang']['email_bad_id']}");

    $users_handler = xoops_getmodulehandler('users', 'tb');
    $user = $users_handler->get($id);
    if (!is_object($user))
    	stderr("{$GLOBALS['lang']['email_error']}", "{$GLOBALS['lang']['email_no_user']}");
    
    if ($user->getVar("class") < UC_MODERATOR)
      stderr("{$GLOBALS['lang']['email_error']}", "{$GLOBALS['lang']['email_email_staff']}");

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
	    $to = $user->getVar("email");
	
	    $from = substr(trim($_POST["from"]), 0, 80);
	    if ($from == "") $from = "{$GLOBALS['lang']['email_anon']}";
	
	    $from_email = substr(trim($_POST["from_email"]), 0, 80);
	      
	    if ($from_email == "") $from_email = "{$GLOBALS['TBDEV']['site_email']}";
	    if (!strpos($from_email, "@")) stderr("{$GLOBALS['lang']['email_error']}", "{$GLOBALS['lang']['email_invalid']}");
	
	    $from = "$from <$from_email>";
	
	    $subject = substr(trim($_POST["subject"]), 0, 80);
	    if ($subject == "") $subject = "(No subject)";
	    
	    $subject = "Fw: $subject";
	
	    $message = trim($_POST["message"]);
	    
	    if ($message == "") stderr("{$GLOBALS['lang']['email_error']}", "{$GLOBALS['lang']['email_no_text']}");
	        
	    xoops_load('xoopsmailer');
				
		$xoopsMailer =& getMailer();
		$xoopsMailer->setHTML(false);
		$xoopsMailer->setTemplateDir($GLOBALS['xoops']->path('/modules/tb/language/'.$GLOBALS['xoopsConfig']['language'].'/mail_templates/'));
		$xoopsMailer->setTemplate('tb_email_gateway.tpl');
		$xoopsMailer->setSubject($subject);
		$xoopsMailer->assign("REMOTE_ADDR", $_SERVER['REMOTE_ADDR']);
		$xoopsMailer->assign("DATETIME", gmdate("Y-m-d H:i:s"));
		$xoopsMailer->assign("SITENAME", $GLOBALS['TBDEV']['site_name']);
	    $xoopsMailer->assign("MESSAGE", $message);
	    $xoopsMailer->assign("EMAIL_NOTE", $GLOBALS['lang']['email_note']);
	    $xoopsMailer->assign("EMAIL_GATEWAY", $GLOBALS['lang']['email_gateway']);
	    $xoopsMailer->setFromEmail($GLOBALS['TBDEV']['site_email']);
	    $xoopsMailer->setFromName($GLOBALS['TBDEV']['site_name']);
	    $xoopsMailer->setToEmails($to);
	
	    if ($xoopsMailer->send())
	      	stderr("{$GLOBALS['lang']['email_success']}", "{$GLOBALS['lang']['email_queued']}");
		else
			stderr("{$GLOBALS['lang']['email_error']}", "{$GLOBALS['lang']['email_failed']}");
	}

    $xoopsOption['template_main'] = 'tb_email_gateway.html';
	stdhead($title, '', '', 0);
	$GLOBALS['xoopsTpl']->assign('xoConfig', $GLOBALS['TBDEV']);
	$GLOBALS['xoopsTpl']->assign('username', $user->getVar('username'));
	stdfoot();
	 
?>
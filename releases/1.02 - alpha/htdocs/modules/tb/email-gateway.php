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

dbconn();

loggedinorreturn();

    $GLOBALS['lang'] = array_merge( load_language('global'), load_language('email-gateway') );
    
    $id = 0 + $_GET["id"];
    
    if ( !is_valid_id($id) )
      stderr("{$GLOBALS['lang']['email_error']}", "{$GLOBALS['lang']['email_bad_id']}");

    $res = $GLOBALS['xoopsDB']->queryF("SELECT username, class, email FROM ".$GLOBALS['xoopsDB']->prefix("users")." WHERE id=$id");
    $arr = $GLOBALS['xoopsDB']->fetchArray($res) or stderr("{$GLOBALS['lang']['email_error']}", "{$GLOBALS['lang']['email_no_user']}");
    $username = $arr["username"];
    
    if ($arr["class"] < UC_MODERATOR)
      stderr("{$GLOBALS['lang']['email_error']}", "{$GLOBALS['lang']['email_email_staff']}");

    if ($_SERVER["REQUEST_METHOD"] == "POST")
    {
      $to = $arr["email"];

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
	include $GLOBALS['xoops']->path('header.php');
	$GLOBALS['xoopsTpl']->assign('xoConfig', $GLOBALS['TBDEV']);
	$GLOBALS['xoopsTpl']->assign('username', $username);
	include $GLOBALS['xoops']->path('footer.php');
	 
?>
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

//loggedinorreturn();
    $GLOBALS['lang'] = array_merge( $GLOBALS['lang'], load_language('rules') );

    $xoopsOption['template_main'] = 'tb_rules.html';
	stdhead($title, '', '', 0);
	$GLOBALS['xoopsTpl']->assign('xoConfig', $GLOBALS['TBDEV']);
		
    $HTMLOUT = '';
    
    $HTMLOUT .= begin_main_frame();

   $HTMLOUT .= begin_frame("{$GLOBALS['lang']['rules_general_header']}");
   $HTMLOUT .= "{$GLOBALS['lang']['rules_general_body']}";
    $HTMLOUT .= end_frame();
    
    $HTMLOUT .= begin_frame("{$GLOBALS['lang']['rules_downloading_header']}");
    $HTMLOUT .= "{$GLOBALS['lang']['rules_downloading_body']}";
    $HTMLOUT .= end_frame();
    
    $HTMLOUT .= begin_frame("{$GLOBALS['lang']['rules_forum_header']}");
    $HTMLOUT .= "{$GLOBALS['lang']['rules_forum_body']}";
    $HTMLOUT .= end_frame();
    
    $HTMLOUT .= begin_frame("{$GLOBALS['lang']['rules_avatar_header']}");
    $HTMLOUT .= "{$GLOBALS['lang']['rules_avatar_body']}";
    $HTMLOUT .= end_frame();

    if (isset($GLOBALS['CURUSER']) AND $GLOBALS['CURUSER']['class'] >= UC_UPLOADER) 
    {

      $HTMLOUT .= begin_frame("{$GLOBALS['lang']['rules_uploading_header']}");
      $HTMLOUT .= "{$GLOBALS['lang']['rules_uploading_body']}";

      $HTMLOUT .= end_frame();

    }
    
    if (isset($GLOBALS['CURUSER']) AND $GLOBALS['CURUSER']['class'] >= UC_MODERATOR) 
    {

     $HTMLOUT .= begin_frame("{$GLOBALS['lang']['rules_moderating_header']}");
     $HTMLOUT .= "<br />
      <table border='0' cellspacing='3' cellpadding='0'>
      {$GLOBALS['lang']['rules_moderating_body']}
      </table>
      <br />";

      $HTMLOUT .= end_frame();
      $HTMLOUT .= begin_frame("{$GLOBALS['lang']['rules_mod_rules_header']}");

      $HTMLOUT .= "{$GLOBALS['lang']['rules_mod_rules_body']}";


      $HTMLOUT .= end_frame();
      $HTMLOUT .= begin_frame("{$GLOBALS['lang']['rules_mod_options_header']}");

      $HTMLOUT .= "{$GLOBALS['lang']['rules_mod_options_body']}";

      $HTMLOUT .= end_frame(); 
    }
    
    $HTMLOUT .= end_main_frame();
    
    print $HTMLOUT;
    
    stdfoot();
?>
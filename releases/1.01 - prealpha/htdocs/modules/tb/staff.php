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
require_once "../../mainfile.php";
require_once "include/bittorrent.php";
require_once "include/html_functions.php";
require_once "include/user_functions.php";


dbconn();

loggedinorreturn();
    
    $GLOBALS['lang'] = array_merge( load_language('global'), load_language('staff') );
    
    $HTMLOUT = '';
    
    $query = $GLOBALS['xoopsDB']->queryF("SELECT users.id, username, email, last_access, class, title, country, status, countries.flagpic, countries.name FROM ".$GLOBALS['xoopsDB']->prefix("users")." LEFT  JOIN ".$GLOBALS['xoopsDB']->prefix("countries")." ON countries.id = users.country WHERE class >=4 AND status='confirmed' ORDER BY username") or sqlerr();

    while($arr2 = $GLOBALS['xoopsDB']->fetchArray($query)) {
      
    /*	if($arr2["class"] == UC_VIP)
        $vips[] =  $arr2;
    */	
      if($arr2["class"] == UC_MODERATOR)
        $mods[] =  $arr2;
        
      if($arr2["class"] == UC_ADMINISTRATOR)
        $admins[] =  $arr2;
        
      if($arr2["class"] == UC_SYSOP)
        $sysops[] =  $arr2;
      }
    /*
    print_r($sysops);
    print("<br />");
    print_r($admins);
    print("<br />");
    print_r($mods);
    print("<br />");
    print(count($mods));
    */
    function DoStaff($staff, $staffclass, $cols = 2) 
    {
            
      $dt = time() - 180;
      $htmlout = '';
      
      if($staff===false) 
      {
        $htmlout .= "<br /><table width='75%' border='1' cellpadding='3'>";
        $htmlout .= "<tr><td class='colhead'><h2>{$staffclass}</h2></td></tr>";
        $htmlout .= "<tr><td>{$GLOBALS['lang']['text_none']}</td></tr></table>";
        return;
      }
      $counter = count($staff);
        
      $rows = ceil($counter/$cols);
      $cols = ($counter < $cols) ? $counter : $cols;
      //echo "<br />" . $cols . "   " . $rows;
      $r = 0;
      $htmlout .= "<br /><table width='75%' border='1' cellpadding='3'>";
      $htmlout .= "<tr><td class='colhead' colspan='{$counter}'><h2>{$staffclass}</h2></td></tr>";
      
      for($ia = 0; $ia < $rows; $ia++)
      {

            $htmlout .= "<tr>";
            for($i = 0; $i < $cols; $i++)
            {
              if( isset($staff[$r]) )  
              {
                $htmlout .= "<td><a href='userdetails.php?id={$staff[$r]['id']}'>".$staff[$r]["username"]."</a>".
          "   <img style='vertical-align: middle;' src='{$GLOBALS['TBDEV']['pic_base_url']}staff".
          ($staff[$r]['last_access']>$dt?"/online.gif":"/offline.gif" )."' border='0' alt='' />".
          "<a href='sendmessage.php?receiver={$staff[$r]['id']}'>".
          "   <img style='vertical-align: middle;' src='{$GLOBALS['TBDEV']['pic_base_url']}staff/users.png' border='0' title=\"{$GLOBALS['lang']['alt_pm']}\" alt='' /></a>".
          "<a href='email-gateway.php?id={$staff[$r]['id']}'>".
          "   <img style='vertical-align: middle;' src='{$GLOBALS['TBDEV']['pic_base_url']}staff/mail.png' border='0' alt='{$staff[$r]['username']}' title=\"{$GLOBALS['lang']['alt_sm']}\" /></a>".
          "   <img style='vertical-align: middle;' src='{$GLOBALS['TBDEV']['pic_base_url']}flag/{$staff[$r]['flagpic']}' border='0' alt='{$staff[$r]['name']}' /></td>";
          $r++;
              }
              else
              {
                $htmlout .= "<td>&nbsp;</td>";
              }
            }
            $htmlout .= "</tr>";
        
      }
      $htmlout .= "</table>";
    
      return $htmlout;
    }

   	$xoopsOption['template_main'] = 'tb_staff.html';
	include $GLOBALS['xoops']->path('header.php');
	$GLOBALS['xoopsTpl']->assign('xoConfig', $GLOBALS['TBDEV']);
	$GLOBALS['xoopsTpl']->assign('sysops', DoStaff($sysops, "{$GLOBALS['lang']['header_sysops']}"));
	$GLOBALS['xoopsTpl']->assign('admins', isset($admins) ? DoStaff($admins, "{$GLOBALS['lang']['header_admins']}") : DoStaff($admins=false, "{$GLOBALS['lang']['header_admins']}"));
	$GLOBALS['xoopsTpl']->assign('mods', isset($mods) ? DoStaff($mods, "{$GLOBALS['lang']['header_mods']}") : DoStaff($mods=false, "{$GLOBALS['lang']['header_mods']}"));
	$GLOBALS['xoopsTpl']->assign('xoops_pagetitle', $GLOBALS['lang']['stdhead_staff']);
		
	include $GLOBALS['xoops']->path('footer.php');
	    
?>
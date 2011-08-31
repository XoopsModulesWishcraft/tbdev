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
	print "<h1>Incorrect access</h1>You cannot access this file directly.";
	exit();
}
include('header.php');
xoops_cp_header();
loadModuleAdminMenu(6);
include_once $GLOBALS['xoops']->path( "/class/template.php" );
$GLOBALS['tbTpl'] = new XoopsTpl();
$GLOBALS['tbTpl']->assign('php_self', $_SERVER['PHP_SELF']);

xoops_loadLanguage('admin', 'tb');

require_once "include/user_functions.php";

    $GLOBALS['lang'] = array_merge( $GLOBALS['lang'], load_language('ad_testip') );
    
    $HTMLOUT = '';
    
    if ($_SERVER["REQUEST_METHOD"] == "POST")
    {
      $ip = isset($_POST["ip"]) ? $_POST["ip"] : false;
    }
    else
    {
      $ip = isset($_GET["ip"]) ? $_GET["ip"] : false;
    }
    
    if ($ip)
    {
      $nip = ip2long($ip);
      if ($nip == -1)
        stderr($GLOBALS['lang']['testip_error'], $GLOBALS['lang']['testip_error1']);
      
      $res = $GLOBALS['xoopsDB']->queryF("SELECT * FROM ".$GLOBALS['xoopsDB']->prefix("tb_bans")." WHERE $nip >= first AND $nip <= last") or sqlerr(__FILE__, __LINE__);
      
      if (mysql_num_rows($res) == 0)
      {
        stderr($GLOBALS['lang']['testip_result'], sprintf($GLOBALS['lang']['testip_notice'],htmlentities($ip, ENT_QUOTES)));
      }
      else
      {
        $HTMLOUT .= "<table class='main' border='0' cellspacing='0' cellpadding='5'>
        <tr>
          <td class='colhead'>{$GLOBALS['lang']['testip_first']}</td>
          <td class='colhead'>{$GLOBALS['lang']['testip_last']}</td>
          <td class='colhead'>{$GLOBALS['lang']['testip_comment']}</td>
        </tr>\n";
        
        while ($arr = $GLOBALS['xoopsDB']->fetchArray($res))
        {
          $first = long2ip($arr["first"]);
          $last = long2ip($arr["last"]);
          $comment = htmlspecialchars($arr["comment"]);
          $HTMLOUT .= "<tr><td>$first</td><td>$last</td><td>$comment</td></tr>\n";
        }
        
        $HTMLOUT .= "</table>\n";
        
        stderr($GLOBALS['lang']['testip_result'], "<table border='0' cellspacing='0' cellpadding='0'><tr><td class='embedded' style='padding-right: 5px'><img src='{$GLOBALS['TBDEV']['pic_base_url']}smilies/excl.gif' alt='' /></td><td class='embedded'>".sprintf($GLOBALS['lang']['testip_notice2'],$ip)."</td></tr></table><p>$HTMLOUT</p>");
      }
    }
    

    $HTMLOUT .= "
    <h1>{$GLOBALS['lang']['testip_title']}</h1>
    <form method='post' action='admin.php?action=testip'>
    <table border='1' cellspacing='0' cellpadding='5'>
    <tr><td class='rowhead'>{$GLOBALS['lang']['testip_address']}</td><td><input type='text' name='ip' /></td></tr>
    <tr><td colspan='2' align='center'><input type='submit' class='btn' value='{$GLOBALS['lang']['testip_ok']}' /></td></tr>
    </table>
    </form>";


    $GLOBALS['tbTpl']->assign('html', $HTMLOUT);
    $GLOBALS['tbTpl']->display('db:tb_cpanel_testip.html');
	xoops_cp_footer();
	exit(0);
    
?>
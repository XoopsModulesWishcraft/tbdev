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
loadModuleAdminMenu(2);
include_once $GLOBALS['xoops']->path( "/class/template.php" );
$GLOBALS['tbTpl'] = new XoopsTpl();
$GLOBALS['tbTpl']->assign('php_self', $_SERVER['PHP_SELF']);

xoops_loadLanguage('admin', 'tb');

require_once "include/user_functions.php";


    $GLOBALS['lang'] = array_merge( $GLOBALS['lang'], load_language('ad_bans') );
    
    $doUpdate = false;

    $remove = isset($_GET['remove']) ? (int)$_GET['remove'] : 0;
    if (is_valid_id($remove))
    {
      @$GLOBALS['xoopsDB']->queryF("DELETE FROM ".$GLOBALS['xoopsDB']->prefix("tb_bans")." WHERE id=$remove") or sqlerr();
      $removed = sprintf($GLOBALS['lang']['text_banremoved'], $remove);
      write_log("{$removed}".$GLOBALS['CURUSER']['id']." (".$GLOBALS['CURUSER']['username'].")");
      $doUpdate = true;
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST" && $GLOBALS['CURUSER']['class'] >= UC_ADMINISTRATOR)
    {
      //we doing just a cache rewrite or an add & rewrite?
      if(isset($_POST['cacheit'])) 
      {
        $doUpdate = true;
      }
      else
      {
        $first = trim($_POST["first"]);
        $last = trim($_POST["last"]);
        $comment = trim($_POST["comment"]);
        if (!$first || !$last || !$comment)
          stderr("{$GLOBALS['lang']['stderr_error']}", "{$GLOBALS['lang']['text_missing']}");
        $first = ip2long($first);
        $last = ip2long($last);
        if ($first == -1 || $first === FALSE || $last == -1 || $last === FALSE)
          stderr("{$GLOBALS['lang']['stderr_error']}", "{$GLOBALS['lang']['text_badip.']}");
        $comment = sqlesc($comment);
        $added = time();

        $GLOBALS['xoopsDB']->queryF("INSERT INTO ".$GLOBALS['xoopsDB']->prefix("tb_bans")." (added, addedby, first, last, comment) 
                      VALUES($added, {$GLOBALS['CURUSER']['id']}, $first, $last, $comment)") or sqlerr(__FILE__, __LINE__);
        $doUpdate = true;
        //header("Location: {$GLOBALS['TBDEV']['baseurl']}/bans.php");
        //die;
      }
    }



    $res = $GLOBALS['xoopsDB']->queryF("SELECT b.*, u.username FROM ".$GLOBALS['xoopsDB']->prefix("tb_bans")." b LEFT JOIN ".$GLOBALS['xoopsDB']->prefix("tb_users")." u on b.addedby = u.id ORDER BY added DESC") or sqlerr(__FILE__,__LINE__);

    $configfile="<"."?php\n\n\$bans = array(\n";

    $HTMLOUT = '';
    

    $HTMLOUT .= "<h1>{$GLOBALS['lang']['text_current']}</h1>\n";

    if (mysql_num_rows($res) == 0)
    {
      $HTMLOUT .= "<p align='center'><b>{$GLOBALS['lang']['text_nothing']}</b></p>\n";
    }
    else
    {
      $HTMLOUT .= "<table border='1' cellspacing='0' cellpadding='5'>\n";
      $HTMLOUT .= "<tr>
        <td class='colhead'>{$GLOBALS['lang']['header_added']}</td><td class='colhead' align='left'>{$GLOBALS['lang']['header_firstip']}</td>
        <td class='colhead' align='left'>{$GLOBALS['lang']['header_lastip']}</td>
        <td class='colhead' align='left'>{$GLOBALS['lang']['header_by']}</td>
        <td class='colhead' align='left'>{$GLOBALS['lang']['header_comment']}</td>
        <td class='colhead'>{$GLOBALS['lang']['header_remove']}</td>
      </tr>\n";
        


      while ($arr = $GLOBALS['xoopsDB']->fetchArray($res))
      {
        if($doUpdate) 
        {
          $configfile .= "array('id'=> '{$arr['id']}', 'first'=> {$arr['first']}, 'last'=> {$arr['last']}),\n";
        }
        
        $arr["first"] = long2ip($arr["first"]);
        $arr["last"] = long2ip($arr["last"]);
        
        $HTMLOUT .= "<tr>
          <td>".get_date($arr['added'],'')."</td>
          <td align='left'>{$arr['first']}</td>
          <td align='left'>{$arr['last']}</td>
          <td align='left'><a href='userdetails.php?id={$arr['addedby']}'>{$arr['username']}</a></td>
          <td align='left'>".htmlentities($arr['comment'], ENT_QUOTES)."</td>
          <td><a href='admin.php?action=bans&amp;remove={$arr['id']}'>{$GLOBALS['lang']['text_remove']}</a></td>
         </tr>\n";
      }
      
      $HTMLOUT .= "</table>\n";
      
    }

    if($doUpdate) 
    {
      $configfile .= "\n);\n\n?".">";
      $filenum = fopen ("cache/bans_cache.php","w");
      ftruncate($filenum, 0);
      fwrite($filenum, $configfile);
      fclose($filenum);
    }
          
    if ($GLOBALS['CURUSER']['class'] >= UC_ADMINISTRATOR)
    {
      $HTMLOUT .= "<h2>{$GLOBALS['lang']['text_addban']}</h2>
      <form method='post' action='admin.php?action=bans'>
      <table border='1' cellspacing='0' cellpadding='5'>
        <tr>
          <td class='rowhead'>{$GLOBALS['lang']['table_firstip']}</td>
          <td><input type='text' name='first' size='40' /></td>
        </tr>
        <tr>
          <td class='rowhead'>{$GLOBALS['lang']['table_lastip']}</td>
          <td><input type='text' name='last' size='40' /></td>
        </tr>
        <tr>
          <td class='rowhead'>{$GLOBALS['lang']['table_comment']}</td><td><input type='text' name='comment' size='40' /></td>
        </tr>
        <tr>
          <td colspan='2' align='center'><input type='submit' name='okay' value='{$GLOBALS['lang']['btn_add']}' class='btn' /></td>
        </tr>
        <tr>
          <td colspan='2' align='center'><input type='submit' name='cacheit' value='{$GLOBALS['lang']['btn_cache']}' class='btn' /></td>
        </tr>
      </table>
      </form>";
      
    }

    $GLOBALS['tbTpl']->assign('html', $HTMLOUT);
    $GLOBALS['tbTpl']->display('db:tb_cpanel_bans.html');
	xoops_cp_footer();
	exit(0);

?>
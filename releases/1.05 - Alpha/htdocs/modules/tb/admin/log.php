<?php

if ( ! defined( 'IN_TBDEV_ADMIN' ) )
{
	print "<h1>{$GLOBALS['lang']['text_incorrect']}</h1>{$GLOBALS['lang']['text_cannot']}";
	exit();
}

include('header.php');
xoops_cp_header();
loadModuleAdminMenu(12);
include_once $GLOBALS['xoops']->path( "/class/template.php" );
$GLOBALS['tbTpl'] = new XoopsTpl();
$GLOBALS['tbTpl']->assign('php_self', $_SERVER['PHP_SELF']);

xoops_loadLanguage('admin', 'tb');

require_once "include/user_functions.php";
  
    $GLOBALS['lang'] = array_merge( $GLOBALS['lang'], load_language('ad_log') );
    
    // delete items older than a week
    $secs = 24 * 60 * 60;
    
    @$GLOBALS['xoopsDB']->queryF("DELETE FROM ".$GLOBALS['xoopsDB']->prefix("tb_sitelog")." WHERE " . time() . " - added > $secs") or sqlerr(__FILE__, __LINE__);
    
    $res = $GLOBALS['xoopsDB']->queryF("SELECT added, txt FROM ".$GLOBALS['xoopsDB']->prefix("tb_sitelog")." ORDER BY added DESC") or sqlerr(__FILE__, __LINE__);
    
    $HTMLOUT = "<h1>{$GLOBALS['lang']['text_sitelog']}</h1>\n";
    
    if (mysql_num_rows($res) == 0)
    {
      $HTMLOUT .= "<b>{$GLOBALS['lang']['text_logempty']}</b>\n";
    }
    else
    {
      $HTMLOUT .= "<table border='1' cellspacing='0' cellpadding='5'>
      <tr>
        <td class='colhead' align='left'>{$GLOBALS['lang']['header_date']}</td>
        <td class='colhead' align='left'>{$GLOBALS['lang']['header_time']}</td>
        <td class='colhead' align='left'>{$GLOBALS['lang']['header_event']}</td>
      </tr>\n";
      
      while ($arr = $GLOBALS['xoopsDB']->fetchArray($res))
      {
        $date = explode( ',', get_date( $arr['added'], 'LONG' ) );
        $HTMLOUT .= "<tr><td>{$date[0]}</td>
        <td>{$date[1]}</td>
        <td align='left'>".htmlentities($arr['txt'], ENT_QUOTES)."</td>
        </tr>\n";
      }
      
      $HTMLOUT .= "</table>\n";
    }
    $HTMLOUT .= "<p>{$GLOBALS['lang']['text_times']}</p>\n";
    
    $GLOBALS['tbTpl']->assign('html', $HTMLOUT);
    $GLOBALS['tbTpl']->display('db:tb_cpanel_log.html');
	xoops_cp_footer();
	exit(0);
    

?>
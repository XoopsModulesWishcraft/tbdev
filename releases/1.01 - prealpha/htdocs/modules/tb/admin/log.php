<?php

if ( ! defined( 'IN_TBDEV_ADMIN' ) )
{
	print "<h1>{$GLOBALS['lang']['text_incorrect']}</h1>{$GLOBALS['lang']['text_cannot']}";
	exit();
}

require_once "include/user_functions.php";
  
    $GLOBALS['lang'] = array_merge( $GLOBALS['lang'], load_language('ad_log') );
    
    // delete items older than a week
    $secs = 24 * 60 * 60;
    
    @$GLOBALS['xoopsDB']->queryF("DELETE FROM ".$GLOBALS['xoopsDB']->prefix("sitelog")." WHERE " . time() . " - added > $secs") or sqlerr(__FILE__, __LINE__);
    
    $res = $GLOBALS['xoopsDB']->queryF("SELECT added, txt FROM ".$GLOBALS['xoopsDB']->prefix("sitelog")." ORDER BY added DESC") or sqlerr(__FILE__, __LINE__);
    
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
    
    print stdhead("{$GLOBALS['lang']['stdhead_log']}") . $HTMLOUT . stdfoot();

?>
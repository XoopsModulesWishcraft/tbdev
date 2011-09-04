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

    $GLOBALS['lang'] = array_merge( $GLOBALS['lang'], load_language('takefilesearch') );

    if(isset($_POST['search']) && !empty($_POST['search'])) {
      
      $cleansearchstr = sqlesc($_POST['search']);
      //print $cleansearchstr;
      }
      else
      stderr($GLOBALS['lang']['tfilesearch_oops'], $GLOBALS['lang']['tfilesearch_nuffin']);


    $query = $GLOBALS['xoopsDB']->queryF("SELECT id, filename, MATCH (filename)
                AGAINST ($cleansearchstr IN BOOLEAN MODE) AS score
                FROM ".$GLOBALS['xoopsDB']->prefix("tb_files")." WHERE MATCH (filename) AGAINST ($cleansearchstr IN BOOLEAN MODE)
                ORDER BY score DESC");

    if(mysql_num_rows($query) == 0)
      stderr($GLOBALS['lang']['tfilesearch_error'], $GLOBALS['lang']['tfilesearch_nothing']);

    $HTMLOUT = '';
  	
    $HTMLOUT .= begin_table();

    $HTMLOUT .= "<tr>
    <td class='colhead'>{$GLOBALS['lang']['tID']}</td>
    <td class='colhead' align='left'>{$GLOBALS['lang']['tfilename']}</td>
    <td class='colhead' align='left'>{$GLOBALS['lang']['tscore']}</td>";
    
    while($row = $GLOBALS['xoopsDB']->fetchArray($query)) 
    {
      $HTMLOUT .= "<tr><td>{$row['id']}</td><td>".htmlspecialchars($row['filename'])."</td><td>{$row['score']}</td></tr>";
    }
    
    $HTMLOUT .= end_table();
    
   	$xoopsOption['template_main'] = 'tb_takefilesearch.html';
	stdhead($title, '', '', 0);
	$GLOBALS['xoopsTpl']->assign('xoConfig', $GLOBALS['TBDEV']);
	$GLOBALS['xoopsTpl']->assign('html', $HTMLOUT);
		
	stdfoot();
?>
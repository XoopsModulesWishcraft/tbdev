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
ob_start('ob_gzhandler');
require_once "../../mainfile.php";
require_once 'include/bittorrent.php';
require_once 'include/user_functions.php';
require_once "include/html_functions.php";
//require_once "include/pager_functions.php";

dbconn(false);

loggedinorreturn();

    $GLOBALS['lang'] = array_merge( load_language('global'), load_language('takefilesearch') );

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
	include $GLOBALS['xoops']->path('header.php');
	$GLOBALS['xoopsTpl']->assign('xoConfig', $GLOBALS['TBDEV']);
	$GLOBALS['xoopsTpl']->assign('html', $HTMLOUT);
		
	include $GLOBALS['xoops']->path('footer.php');
?>
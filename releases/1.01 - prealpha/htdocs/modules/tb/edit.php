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
require_once "include/bittorrent.php" ;
require_once "include/user_functions.php" ;
require_once "include/html_functions.php" ;

if (!mkglobal("id"))
	die();

$id = 0 + $id;
if (!$id)
	die();

dbconn();

loggedinorreturn();

    $GLOBALS['lang'] = array_merge( load_language('global'), load_language('edit') );
    
    $res = $GLOBALS['xoopsDB']->queryF("SELECT * FROM ".$GLOBALS['xoopsDB']->prefix("torrents")." WHERE id = $id");
    $row = $GLOBALS['xoopsDB']->fetchArray($res);
    if (!$row)
      stderr($GLOBALS['lang']['edit_user_error'], $GLOBALS['lang']['edit_no_torrent']);


    
    if (!isset($GLOBALS['CURUSER']) || ($GLOBALS['CURUSER']["id"] != $row["owner"] && get_user_class() < UC_MODERATOR)) 
    {
      stderr($GLOBALS['lang']['edit_user_error'], sprintf($GLOBALS['lang']['edit_no_permission'], urlencode($_SERVER['REQUEST_URI'])));
    }


    $xoopsOption['template_main'] = 'tb_edit.html';
	include $GLOBALS['xoops']->path('header.php');
	$GLOBALS['xoopsTpl']->assign('xoConfig', $GLOBALS['TBDEV']);
	$GLOBALS['xoopsTpl']->assign('id', $id);
	$GLOBALS['xoopsTpl']->assign('row', $row);

    $GLOBALS['xoopsTpl']->assign('tr_torrent_name', tr($GLOBALS['lang']['edit_torrent_name'], "<input type='text' name='name' value='" . htmlspecialchars($row["name"]) . "' size='80' />", 1));
    $GLOBALS['xoopsTpl']->assign('tr_nfo', tr($GLOBALS['lang']['edit_nfo'], "<input type='radio' name='nfoaction' value='keep' checked='checked' />{$GLOBALS['lang']['edit_keep_current']}<br />".
	"<input type='radio' name='nfoaction' value='update' />{$GLOBALS['lang']['edit_update']}<br /><input type='file' name='nfo' size='80' />", 1));
    
    if ((strpos($row["ori_descr"], "<") === false) || (strpos($row["ori_descr"], "&lt;") !== false))
    {
      $c = "";
    }
    else
    {
      $c = " checked";
    }
    
	$GLOBALS['xoopsTpl']->assign('tr_description', tr($GLOBALS['lang']['edit_description'], "<textarea name='descr' rows='10' cols='80'>" . htmlspecialchars($row["ori_descr"]) . "</textarea><br />({$GLOBALS['lang']['edit_tags']})", 1));
	
    $s = "<select name='type'>\n";

    $cats = genrelist();
	$GLOBALS['xoopsTpl']->assign('cats', $cats);
	    
    foreach ($cats as $subrow) 
    {
      $s .= "<option value='" . $subrow["id"] . "'";
      if ($subrow["id"] == $row["category"])
        $s .= " selected='selected'";
      $s .= ">" . htmlspecialchars($subrow["name"]) . "</option>\n";
    }

    $s .= "</select>\n";
 
    $GLOBALS['xoopsTpl']->assign('tr_type', tr($GLOBALS['lang']['edit_type'], $s, 1));
    $GLOBALS['xoopsTpl']->assign('tr_visible', tr($GLOBALS['lang']['edit_visible'], "<input type='checkbox' name='visible'" . (($row["visible"] == "yes") ? " checked='checked'" : "" ) . " value='1' /> {$GLOBALS['lang']['edit_visible_mainpage']}<br /><table border='0' cellspacing='0' cellpadding='0' width='420'><tr><td class='embedded'>{$GLOBALS['lang']['edit_visible_info']}</td></tr></table>", 1));
    	
    if (get_user_class() >= UC_MODERATOR) //($GLOBALS['CURUSER']["admin"] == "yes")
    {
    	$GLOBALS['xoopsTpl']->assign('tr_banned', tr($GLOBALS['lang']['edit_banned'], "<input type='checkbox' name='banned'" . (($row["banned"] == "yes") ? " checked='checked'" : "" ) . " value='1' /> {$GLOBALS['lang']['edit_banned']}", 1));
    }   

	include $GLOBALS['xoops']->path('footer.php');
    
?>
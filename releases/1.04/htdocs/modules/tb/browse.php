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
ob_start("ob_gzhandler");
require_once '../../mainfile.php';
require_once("include/bittorrent.php");
require_once "include/user_functions.php";
require_once "include/torrenttable_functions.php";
require_once "include/pager_functions.php";

dbconn(false);

loggedinorreturn();

    $GLOBALS['lang'] = array_merge( load_language('global'), load_language('browse'), load_language('torrenttable_functions') );
    
    $HTMLOUT = '';
    
    $cats = genrelist();

    if(isset($_GET["search"])) 
    {
      $searchstr = unesc($_GET["search"]);
      $cleansearchstr = searchfield($searchstr);
      if (empty($cleansearchstr))
        unset($cleansearchstr);
    }

    $orderby = "ORDER BY ".$GLOBALS['xoopsDB']->prefix("tb_torrents").".id DESC";

    $addparam = "";
    $wherea = array();
    $wherecatina = array();

    if (isset($_GET["incldead"]) &&  $_GET["incldead"] == 1)
    {
      $addparam .= "incldead=1&amp;";
      if (!isset($GLOBALS['CURUSER']) || get_user_class() < UC_ADMINISTRATOR)
        $wherea[] = "banned != 'yes'";
    }
    else
    {
      if (isset($_GET["incldead"]) && $_GET["incldead"] == 2)
      {
      $addparam .= "incldead=2&amp;";
        $wherea[] = "visible = 'no'";
      }
      else
        $wherea[] = "visible = 'yes'";
    }
    
    $category = (isset($_GET["cat"])) ? (int)$_GET["cat"] : false;

    $all = isset($_GET["all"]) ? $_GET["all"] : false;

    if (!$all)
    {
      if (!$_GET && $GLOBALS['CURUSER']["notifs"])
      {
        $all = True;
        foreach ($cats as $cat)
        {
          $all &= $cat['id'];
          if (strpos($GLOBALS['CURUSER']["notifs"], "[cat" . $cat['id'] . "]") !== False)
          {
            $wherecatina[] = $cat['id'];
            $addparam .= "c{$cat['id']}=1&amp;";
          }
        }
      }
      elseif ($category)
      {
        if (!is_valid_id($category))
          stderr("{$GLOBALS['lang']['browse_error']}", "{$GLOBALS['lang']['browse_invalid_cat']}");
        $wherecatina[] = $category;
        $addparam .= "cat=$category&amp;";
      }
      else
      {
        $all = True;
        foreach ($cats as $cat)
        {
          $all &= isset($_GET["c{$cat['id']}"]);
          if (isset($_GET["c{$cat['id']}"]))
          {
            $wherecatina[] = $cat['id'];
            $addparam .= "c{$cat['id']}=1&amp;";
          }
        }
      }
    }
    
    if ($all)
    {
      $wherecatina = array();
      $addparam = "";
    }

    if (count($wherecatina) > 1)
      $wherecatin = implode(",",$wherecatina);
    elseif (count($wherecatina) == 1)
      $wherea[] = "category = $wherecatina[0]";

    $wherebase = $wherea;

    if (isset($cleansearchstr))
    {
      $wherea[] = "MATCH (search_text, ori_descr) AGAINST (" . sqlesc($searchstr) . ")";
      //$wherea[] = "0";
      $addparam .= "search=" . urlencode($searchstr) . "&amp;";
      $orderby = "";
      
      /////////////// SEARCH CLOUD MALARKY //////////////////////

        $searchcloud = sqlesc($cleansearchstr);
       // $r = mysql_fetch_array($GLOBALS['xoopsDB']->queryF("SELECT COUNT(*) FROM ".$GLOBALS['xoopsDB']->prefix("tb_searchcloud")." WHERE searchedfor = $searchcloud"), MYSQL_NUM);
        //$a = $r[0];
        //if ($a)
           // $GLOBALS['xoopsDB']->queryF("UPDATE ".$GLOBALS['xoopsDB']->prefix("tb_searchcloud")." SET howmuch = howmuch + 1 WHERE searchedfor = $searchcloud");
        //else
           // $GLOBALS['xoopsDB']->queryF("INSERT INTO ".$GLOBALS['xoopsDB']->prefix("tb_searchcloud")." (searchedfor, howmuch) VALUES ($searchcloud, 1)");
        @$GLOBALS['xoopsDB']->queryF("INSERT INTO ".$GLOBALS['xoopsDB']->prefix("tb_searchcloud")." (searchedfor, howmuch) VALUES ($searchcloud, 1)
                    ON DUPLICATE KEY UPDATE howmuch=howmuch+1");
      /////////////// SEARCH CLOUD MALARKY END ///////////////////
    }

    $where = implode(" AND ", $wherea);
    
    if (isset($wherecatin))
      $where .= ($where ? " AND " : "") . "category IN(" . $wherecatin . ")";

    if ($where != "")
      $where = "WHERE $where";

    $res = $GLOBALS['xoopsDB']->queryF("SELECT COUNT(*) FROM ".$GLOBALS['xoopsDB']->prefix("tb_torrents")." $where") or die(mysql_error());
    $row = mysql_fetch_array($res,MYSQL_NUM);
    $count = $row[0];

    if (!$count && isset($cleansearchstr)) 
    {
      $wherea = $wherebase;
      $orderby = "ORDER BY id DESC";
      $searcha = explode(" ", $cleansearchstr);
      $sc = 0;
      foreach ($searcha as $searchss) 
      {
        if (strlen($searchss) <= 1)
          continue;
        $sc++;
        if ($sc > 5)
          break;
        $ssa = array();
        foreach (array("search_text", "ori_descr") as $sss)
          $ssa[] = "$sss LIKE '%" . sqlwildcardesc($searchss) . "%'";
        $wherea[] = "(" . implode(" OR ", $ssa) . ")";
      }
    
      if ($sc) 
      {
        $where = implode(" AND ", $wherea);
        if ($where != "")
          $where = "WHERE $where";
        $res = $GLOBALS['xoopsDB']->queryF("SELECT COUNT(*) FROM ".$GLOBALS['xoopsDB']->prefix("tb_torrents")." $where");
        $row = mysql_fetch_array($res,MYSQL_NUM);
        $count = $row[0];
      }
    }

    $torrentsperpage = $GLOBALS['CURUSER']["torrentsperpage"];
    if (!$torrentsperpage)
      $torrentsperpage = 15;

    if ($count)
    {
      //list($pagertop, $pagerbottom, $limit) = pager($torrentsperpage, $count, "browse.php?" . $addparam);
      $pager = pager($torrentsperpage, $count, "browse.php?" . $addparam);

      $query = "SELECT ".$GLOBALS['xoopsDB']->prefix("tb_torrents").".id, ".$GLOBALS['xoopsDB']->prefix("tb_torrents").".category, ".$GLOBALS['xoopsDB']->prefix("tb_torrents").".leechers, ".$GLOBALS['xoopsDB']->prefix("tb_torrents").".seeders, ".$GLOBALS['xoopsDB']->prefix("tb_torrents").".name, ".$GLOBALS['xoopsDB']->prefix("tb_torrents").".times_completed, ".$GLOBALS['xoopsDB']->prefix("tb_torrents").".size, ".$GLOBALS['xoopsDB']->prefix("tb_torrents").".added, ".$GLOBALS['xoopsDB']->prefix("tb_torrents").".type,  ".$GLOBALS['xoopsDB']->prefix("tb_torrents").".comments,".$GLOBALS['xoopsDB']->prefix("tb_torrents").".numfiles,".$GLOBALS['xoopsDB']->prefix("tb_torrents").".filename,".$GLOBALS['xoopsDB']->prefix("tb_torrents").".owner,IF(".$GLOBALS['xoopsDB']->prefix("tb_torrents").".nfo <> '', 1, 0) as nfoav," .
    //	"IF(".$GLOBALS['xoopsDB']->prefix("tb_torrents").".numratings < {$GLOBALS['TBDEV']['minvotes']}, NULL, ROUND(".$GLOBALS['xoopsDB']->prefix("tb_torrents").".ratingsum / ".$GLOBALS['xoopsDB']->prefix("tb_torrents").".numratings, 1)) AS rating, ".$GLOBALS['xoopsDB']->prefix("tb_categories").".name AS cat_name, ".$GLOBALS['xoopsDB']->prefix("tb_categories").".image AS cat_pic, ".$GLOBALS['xoopsDB']->prefix("tb_users").".username FROM ".$GLOBALS['xoopsDB']->prefix("tb_torrents")." LEFT JOIN ".$GLOBALS['xoopsDB']->prefix("tb_categories")." ON category = ".$GLOBALS['xoopsDB']->prefix("tb_categories").".id LEFT JOIN ".$GLOBALS['xoopsDB']->prefix("tb_users")." ON ".$GLOBALS['xoopsDB']->prefix("tb_torrents").".owner = ".$GLOBALS['xoopsDB']->prefix("tb_users").".id $where $orderby $limit";
      "".$GLOBALS['xoopsDB']->prefix("tb_categories").".name AS cat_name, ".$GLOBALS['xoopsDB']->prefix("tb_categories").".image AS cat_pic, ".$GLOBALS['xoopsDB']->prefix("tb_users").".username FROM ".$GLOBALS['xoopsDB']->prefix("tb_torrents")." LEFT JOIN ".$GLOBALS['xoopsDB']->prefix("tb_categories")." ON category = ".$GLOBALS['xoopsDB']->prefix("tb_categories").".id LEFT JOIN ".$GLOBALS['xoopsDB']->prefix("tb_users")." ON ".$GLOBALS['xoopsDB']->prefix("tb_torrents").".owner = ".$GLOBALS['xoopsDB']->prefix("tb_users").".id $where $orderby {$pager['limit']}";
      $res = $GLOBALS['xoopsDB']->queryF($query) or die(mysql_error());
    }
    else
    {
      unset($res);
    }
    
    if (isset($cleansearchstr))
      $title = "{$GLOBALS['lang']['browse_search']}\"$searchstr\"";
    else
      $title = '';


	$xoopsOption['template_main'] = 'tb_browse.html';
	include $GLOBALS['xoops']->path('header.php');
	$GLOBALS['xoopsTpl']->assign('xoConfig', $GLOBALS['TBDEV']);
	$GLOBALS['xoopsTpl']->assign('xoops_pagetitle', $title);
	$GLOBALS['xoopsTpl']->assign('wherecatina', $wherecatina);

    require_once "include/searchcloud_functions.php";
    
    $GLOBALS['xoopsTpl']->assign('lastrowcols', count($cats)%$GLOBALS['xoopsModuleConfig']['catsperrow']);
	$GLOBALS['xoopsTpl']->assign('npos', $GLOBALS['xoopsModuleConfig']['catsperrow']-(count($cats)%$GLOBALS['xoopsModuleConfig']['catsperrow']));
	$GLOBALS['xoopsTpl']->assign('rowspan', $GLOBALS['xoopsModuleConfig']['catsperrow']-(count($cats)%$GLOBALS['xoopsModuleConfig']['catsperrow'])-1);
	$GLOBALS['xoopsTpl']->assign('cloud', cloud());
	$GLOBALS['xoopsTpl']->assign('cats', $cats);
    $GLOBALS['xoopsTpl']->assign('torrenttable', torrenttable($res));
    $GLOBALS['xoopsTpl']->assign('pager', $pager);
	$GLOBALS['xoopsTpl']->assign('searchstr', htmlentities($searchstr, ENT_QUOTES));
	include $GLOBALS['xoops']->path('footer.php');
    
?>
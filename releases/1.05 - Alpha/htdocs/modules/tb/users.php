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

    $GLOBALS['lang'] = array_merge( $GLOBALS['lang'], load_language('users') );
    
    $search = isset($_GET['search']) ? strip_tags(trim($_GET['search'])) : '';
    $class = isset($_GET['class']) ? $_GET['class'] : '-';
    $letter = '';
    $q = '';
    if ($class == '-' || !ctype_digit($class))
      $class = '';

    if ($search != '' || $class)
    {
      $query = "username LIKE " . sqlesc("%$search%") . " AND status='confirmed'";
      if ($search)
          $q = "search=" . htmlspecialchars($search);
    }
    else
    {
      $letter = isset($_GET['letter']) ? trim((string)$_GET["letter"]) : '';
      if (strlen($letter) > 1)
        die;

      if ($letter == "" || strpos("abcdefghijklmnopqrstuvwxyz0123456789", $letter) === false)
        $letter = "";
      $query = "username LIKE '$letter%' AND status='confirmed'";
      $q = "letter=$letter";
    }

    if (ctype_digit($class))
    {
      $query .= " AND class=$class";
      $q .= ($q ? "&amp;" : "") . "class=$class";
    }

    
    $HTMLOUT = '';
    
    $HTMLOUT  .= "<h1>Users</h1>\n";

    $HTMLOUT  .= "<form method='get' action='".$GLOBALS['xoopsDB']->prefix("tb_users").".php?'>\n";
    $HTMLOUT  .= "{$GLOBALS['lang']['form_search']} <input type='text' size='30' name='search' />\n";
    $HTMLOUT  .= "<select name='class'>\n";
    $HTMLOUT  .= "<option value='-'>(any class)</option>\n";
    for ($i = 0;;++$i)
    {
      if ($c = get_user_class_name($i))
        $HTMLOUT  .= "<option value='$i'" . (ctype_digit($class) && $class == $i ? " selected='selected'" : "") . ">$c</option>\n";
      else
        break;
    }
    $HTMLOUT  .= "</select>\n";
    $HTMLOUT  .= "<input type='submit' value='{$GLOBALS['lang']['form_btn']}' class='btn' />\n";
    $HTMLOUT  .= "</form>\n";

    $HTMLOUT  .= "<br />\n";


      $aa = range('0','9');
      $bb = range('a','z');
      $cc = array_merge($aa,$bb);
      unset($aa,$bb);
      
      $HTMLOUT  .= "<div align='center'>";
      $count = 0;
      foreach($cc as $L) 
      {
        $HTMLOUT .= ($count == 10) ? "<br /><br />" : '';
        if(!strcmp($L,$letter))
          $HTMLOUT  .= "<span class='btn' style='background:orange;'>".strtoupper($L)."</span>\n";
        else
          $HTMLOUT  .= "<a href='".$GLOBALS['xoopsDB']->prefix("tb_users").".php?letter=$L'><span class='btn'>".strtoupper($L)."</span></a>\n";
          $count++;
      }
      
    $HTMLOUT  .= "</div>";

    $HTMLOUT  .= "<br />\n";
      
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $perpage = 25;
    $browsemenu = '';
    $pagemenu = '';

    $res = $GLOBALS['xoopsDB']->queryF("SELECT COUNT(*) FROM ".$GLOBALS['xoopsDB']->prefix("tb_users")." WHERE $query") or sqlerr(__FILE__,__LINE__);
    $arr = mysql_fetch_row($res);

    if($arr[0] > $perpage) {
    $pages = floor($arr[0] / $perpage);
    if ($pages * $perpage < $arr[0])
      ++$pages;

    if ($page < 1)
      $page = 1;
    else
      if ($page > $pages)
        $page = $pages;

    for ($i = 1; $i <= $pages; ++$i) 
    {
      $PageNo = $i+1;
      if($PageNo < ($page - 2))
        continue;
      
      if ($i == $page)
        $pagemenu .= "&nbsp;<span class='btn' style='background:orange;'>$i</span>\n";
      else
        $pagemenu .= "&nbsp;<a href='".$GLOBALS['xoopsDB']->prefix("tb_users").".php?$q&amp;page=$i'><span class='btn'>$i</span></a>\n";
      if($PageNo > ($page + 3)) break;
    }

    if ($page == 1)
      $browsemenu .= "<span class='btn' style='background:orange;'>&lsaquo;</span>$pagemenu";
    else
      $browsemenu .= "<a href='".$GLOBALS['xoopsDB']->prefix("tb_users").".php?$q&amp;page=1' title='{$GLOBALS['lang']['pager_first']}(1)'><span class='btn'>&laquo;</span></a>&nbsp;<a href='".$GLOBALS['xoopsDB']->prefix("tb_users").".php?$q&amp;page=" . ($page - 1) . "'><span class='btn'>&lsaquo;</span></a>$pagemenu";

    //$browsemenu .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";

    if ($page == $pages)
      $browsemenu .= "<span class='btn' style='background:orange;'>&rsaquo;</span>";
    else
      $browsemenu .= "<a href='".$GLOBALS['xoopsDB']->prefix("tb_users").".php?$q&amp;page=" . ($page + 1) . "'><span class='btn'>&rsaquo;</span></a>&nbsp;<a href='".$GLOBALS['xoopsDB']->prefix("tb_users").".php?$q&amp;page=" . $pages . "' title='{$GLOBALS['lang']['pager_last']}($pages)'><span class='btn'>&raquo;</span></a>";
    }

    $HTMLOUT .= ($arr[0] > $perpage) ? "<p>$browsemenu<br /><br /></p>" : '<br /><br />';

    $offset = ($page * $perpage) - $perpage;

    if($arr[0] > 0) {
        $res = $GLOBALS['xoopsDB']->queryF("SELECT ".$GLOBALS['xoopsDB']->prefix("tb_users").".*, countries.name, countries.flagpic FROM ".$GLOBALS['xoopsDB']->prefix("tb_users")." FORCE INDEX ( username ) LEFT JOIN ".$GLOBALS['xoopsDB']->prefix("tb_countries")." ON country = countries.id WHERE $query ORDER BY username LIMIT $offset,$perpage") or sqlerr(__FILE__,__LINE__);
    //$num = mysql_num_rows($res);

    $HTMLOUT  .= "<table border='1' cellspacing='0' cellpadding='5'>\n";
    $HTMLOUT  .= "<tr><td class='colhead' align='left'>User name</td><td class='colhead'>{$GLOBALS['lang']['users_regd']}</td><td class='colhead'>{$GLOBALS['lang']['users_la']}</td><td class='colhead' align='left'>{$GLOBALS['lang']['users_class']}</td><td class='colhead'>{$GLOBALS['lang']['users_country']}</td></tr>\n";
    
    while($row = $GLOBALS['xoopsDB']->fetchArray($res))
    {
      
      $country = ($row['name'] != NULL) ? "<td style='padding: 0px' align='center'><img src='{$GLOBALS['TBDEV']['pic_base_url']}flag/{$row['flagpic']}' alt='". htmlspecialchars($row['name']) ."' /></td>" : "<td align='center'>---</td>";
    /*    
      if ($row['added'] == '0000-00-00 00:00:00')
        $row['added'] = '-';
      if ($row['last_access'] == '0000-00-00 00:00:00')
        $row['last_access'] = '-';
    */    
      $HTMLOUT .= "<tr><td align='left'><a href='userdetails.php?id={$row['id']}'><b>{$row['username']}</b></a>" .
      ($row["donor"] > 0 ? "<img src='{$GLOBALS['TBDEV']['pic_base_url']}star.gif' border='0' alt='Donor' />" : "")."</td>" .
      "<td>".get_date( $row['added'],'' )."</td><td>".get_date( $row['last_access'], '')."</td>".
        "<td align='left'>" . get_user_class_name($row["class"]) . "</td>$country</tr>\n";
    }
    $HTMLOUT  .= "</table>\n";
    }

    $HTMLOUT  .= ($arr[0] > $perpage) ? "<br /><p>$browsemenu</p>" : '<br /><br />';

    $xoopsOption['template_main'] = 'tb_users.html';
	stdhead($title, '', '', 0);
	$GLOBALS['xoopsTpl']->assign('xoConfig', $GLOBALS['TBDEV']);
	$GLOBALS['xoopsTpl']->assign('html', $HTMLOUT);
	$GLOBALS['xoopsTpl']->assign('xoops_pagetitle', $GLOBALS['lang']['head_users']);
	stdfoot();
    
?>
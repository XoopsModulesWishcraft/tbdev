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

require_once("config.php");

if (!function_exists("searchcloud_tag_info")) {
	function searchcloud_tag_info() {
		$criteria = new Criteria('1','1');
		$criteria->setOrder('DESC');
		$criteria->setSort('RAND()');
		$criteria->setLimit($GLOBALS['xoopsModuleConfig']['num_in_cloud']);
		$searchcloud_handler = xoops_getmodulehandler('searchcloud');
  		$arr = array();
		foreach($searchcloud_handler->getObjects($criteria, false) as $id => $cloud) {
			$arr[$cloud->getVar('searchedfor')] = $cloud->getVar('howmuch');
		}
		if (isset($arr)) {
			ksort($arr);
			return $arr;
		}
	}
}

if (!function_exists("searchcloud_cloud")) {
	function searchcloud_cloud() {
		//min / max font sizes
		$small = $GLOBALS['xoopsModuleConfig']['cloud_font_small'];
		$big = $GLOBALS['xoopsModuleConfig']['cloud_font_big'];
		//get tag info from worker function
		$tags = searchcloud_tag_info();
		//amounts
		if (isset($tags)) {
			$minimum_count = min(array_values($tags));
			$maximum_count = max(array_values($tags));
			$spread = $maximum_count - $minimum_count;
			if($spread == 0) {
				$spread = 1;
			}
			$cloud_html = '';
			$cloud_tags = array();
			$colour_array = explode('|', $GLOBALS['xoopsModuleConfig']['colors_cloud']);
			foreach ($tags as $tag => $count) {
				$size = $small + ($count - $minimum_count) * ($big - $small) / $spread;
				$cloud_tags[] = '<a style="color:'.$colour_array[mt_rand(0, sizeof($colour_array))].'; font-size: '. floor($size) . 'px'
    . '" class="tag_cloud" href="'.XOOPS_URL.'/modules/tb/browse.php?search=' . urlencode($tag) . '&amp;cat=0&amp;incldead=1'
    . '" title="\'' . htmlentities($tag)  . '\' returned a count of ' . $count . '">'
    . htmlentities(stripslashes($tag)) . '</a>';
			}
			$cloud_html = join("\n", $cloud_tags) . "\n";
			return $cloud_html;
		}
	}
}

if (!function_exists("is_valid_user_class")) {
	function is_valid_user_class($class)
	{
	  return is_numeric($class) && floor($class) == $class && $class >= UC_USER && $class <= UC_SYSOP;
	}
}

if (!function_exists("is_valid_id")) {
	function is_valid_id($id)
	{
	  return is_numeric($id) && ($id > 0) && (floor($id) == $id);
	}
}

if (!function_exists("linkcolor")) {
	function linkcolor($num) {
	    if (!$num)
	        return "red";
	    if ($num == 1)
	        return "yellow";
	    return "green";
	}
}
if (!function_exists("torrenttable")) {
	function torrenttable($res, $variant = "index") {
		if (is_object($GLOBALS['xoopsTpl'])) {
			$GLOBALS['xoopsTpl']->assign('variant', $variant);
			if (is_array($res['torrents']))
				$GLOBALS['xoopsTpl']->assign('torrent', $res['torrents']);
			return true;
		}
		return false;
	}
}

if (!function_exists("tr")) {
	function tr($x,$y,$noesc=0) {
		if ($noesc)
			$a = $y;
		else {
			$a = htmlspecialchars($y);
			$a = str_replace("\n", "<br />\n", $a);
		}
		
		return "<tr><td class='heading' valign='top' align='right'>$x</td><td valign='top' align='left'>$a</td></tr>\n";
	}
}

if (!function_exists("deadtime")) {
	function deadtime() {
	    
	    return time() - floor($GLOBALS['TBDEV']['announce_interval'] * 1.3);
	}
}

if (!function_exists("docleanup")) {
	function docleanup() {
		
		set_time_limit(240);
		ignore_user_abort(1);
	
		do {
			$res = $GLOBALS['xoopsDB']->queryF("SELECT id FROM torrents");
			$ar = array();
			while ($row = mysql_fetch_array($res,MYSQL_NUM)) {
				$id = $row[0];
				$ar[$id] = 1;
			}
	
			if (!count($ar))
				break;
	
			$dp = @opendir($GLOBALS['TBDEV']['torrent_dir']);
			if (!$dp)
				break;
	
			$ar2 = array();
			while (($file = readdir($dp)) !== false) {
				if (!preg_match('/^(\d+)\.torrent$/', $file, $m))
					continue;
				$id = $m[1];
				$ar2[$id] = 1;
				if (isset($ar[$id]) && $ar[$id])
					continue;
				$ff = $GLOBALS['TBDEV']['torrent_dir'] . "/$file";
				unlink($ff);
			}
			closedir($dp);
	
			if (!count($ar2))
				break;
	
			$delids = array();
			foreach (array_keys($ar) as $k) {
				if (isset($ar2[$k]) && $ar2[$k])
					continue;
				$delids[] = $k;
				unset($ar[$k]);
			}
			if (count($delids))
				$GLOBALS['xoopsDB']->queryF("DELETE FROM ".$GLOBALS['xoopsDB']->prefix("tb_torrents")." WHERE id IN (" . join(",", $delids) . ")");
	
			$res = $GLOBALS['xoopsDB']->queryF("SELECT torrent FROM ".$GLOBALS['xoopsDB']->prefix("tb_peers")." GROUP BY torrent");
			$delids = array();
			while ($row = mysql_fetch_array($res,MYSQL_NUM)) {
				$id = $row[0];
				if (isset($ar[$id]) && $ar[$id])
					continue;
				$delids[] = $id;
			}
			if (count($delids))
				$GLOBALS['xoopsDB']->queryF("DELETE FROM ".$GLOBALS['xoopsDB']->prefix("tb_peers")." WHERE torrent IN (" . join(",", $delids) . ")");
	
			$res = $GLOBALS['xoopsDB']->queryF("SELECT torrent FROM ".$GLOBALS['xoopsDB']->prefix("tb_files")." GROUP BY torrent");
			$delids = array();
			while ($row = mysql_fetch_array($res,MYSQL_NUM)) {
				$id = $row[0];
				if (isset($ar[$id]) && $ar[$id])
					continue;
				$delids[] = $id;
			}
			if (count($delids))
				$GLOBALS['xoopsDB']->queryF("DELETE FROM ".$GLOBALS['xoopsDB']->prefix("tb_files")." WHERE torrent IN (" . join(",", $delids) . ")");
		} while (0);
	
		$deadtime = deadtime();
		@$GLOBALS['xoopsDB']->queryF("DELETE FROM ".$GLOBALS['xoopsDB']->prefix("tb_peers")." WHERE last_action < $deadtime");
	
		$deadtime -= $GLOBALS['TBDEV']['max_dead_torrent_time'];
		@$GLOBALS['xoopsDB']->queryF("UPDATE ".$GLOBALS['xoopsDB']->prefix("tb_torrents")." SET visible='no' WHERE visible='yes' AND last_action < $deadtime");
	
		$deadtime = time() - $GLOBALS['TBDEV']['signup_timeout'];
		@$GLOBALS['xoopsDB']->queryF("DELETE FROM ".$GLOBALS['xoopsDB']->prefix("tb_users")." WHERE status = 'pending' AND added < $deadtime AND last_login < $deadtime AND last_access < $deadtime");
	
		$torrents = array();
		$res = @$GLOBALS['xoopsDB']->queryF("SELECT torrent, seeder, COUNT(*) AS c FROM ".$GLOBALS['xoopsDB']->prefix("tb_peers")." GROUP BY torrent, seeder");
		while ($row = $GLOBALS['xoopsDB']->fetchArray($res)) {
			if ($row["seeder"] == "yes")
				$key = "seeders";
			else
				$key = "leechers";
			$torrents[$row["torrent"]][$key] = $row["c"];
		}
	
		$res = @$GLOBALS['xoopsDB']->queryF("SELECT torrent, COUNT(*) AS c FROM comments GROUP BY torrent");
		while ($row = $GLOBALS['xoopsDB']->fetchArray($res)) {
			$torrents[$row["torrent"]]["comments"] = $row["c"];
		}
		
		
		$fields = explode(":", "comments:leechers:seeders");
		$res = @$GLOBALS['xoopsDB']->queryF("SELECT id, seeders, leechers, comments FROM torrents");
		while ($row = $GLOBALS['xoopsDB']->fetchArray($res)) {
			$id = $row["id"];
			if(isset($torrents[$id]))
			$torr = $torrents[$id];
			foreach ($fields as $field) {
				if (!isset($torr[$field]))
					$torr[$field] = 0;
			}
			$update = array();
			foreach ($fields as $field) {
				if ($torr[$field] != $row[$field])
					$update[] = "$field = " . $torr[$field];
			}
			if (count($update))
				@$GLOBALS['xoopsDB']->queryF("UPDATE ".$GLOBALS['xoopsDB']->prefix("tb_torrents")." SET " . implode(",", $update) . " WHERE id = $id");
		}
	
		//delete inactive user accounts
		$secs = 42*86400;
		$dt = (time() - $secs);
		$maxclass = UC_POWER_USER;
		@$GLOBALS['xoopsDB']->queryF("DELETE FROM ".$GLOBALS['xoopsDB']->prefix("tb_users")." WHERE status='confirmed' AND class <= $maxclass AND last_access < $dt");
	
		// lock topics where last post was made more than x days ago
	/*	$secs = 7*86400;
		$res = $GLOBALS['xoopsDB']->queryF("SELECT topics.id FROM topics LEFT JOIN posts ON topics.lastpost = posts.id WHERE topics.locked = 'no' AND topics.sticky = 'no' AND " . gmtime() . " - UNIX_TIMESTAMP(posts.added) > $secs") or sqlerr(__FILE__, __LINE__);
	  if(mysql_num_rows($res) > 0) {
		while ($arr = $GLOBALS['xoopsDB']->fetchArray($res))
	    $pids[] = $arr['id'];
			$GLOBALS['xoopsDB']->queryF("UPDATE topics SET locked='yes' WHERE id IN (".join(',', $pids).")") or sqlerr(__FILE__, __LINE__);
	  }
	*/  
	  //remove expired warnings
	  $res = @$GLOBALS['xoopsDB']->queryF("SELECT id FROM ".$GLOBALS['xoopsDB']->prefix("tb_users")." WHERE warned='yes' AND warneduntil < ".time()." AND warneduntil <> 0") or sqlerr(__FILE__, __LINE__);
	  if (mysql_num_rows($res) > 0)
	  {
	    $dt = time();
	    $msg = sqlesc("Your warning has been removed. Please keep in your best behaviour from now on.\n");
	    while ($arr = $GLOBALS['xoopsDB']->fetchArray($res))
	    {
	      @$GLOBALS['xoopsDB']->queryF("UPDATE ".$GLOBALS['xoopsDB']->prefix("tb_users")." SET warned = 'no', warneduntil = 0 WHERE id = {$arr['id']}") or sqlerr(__FILE__, __LINE__);
	      //@$GLOBALS['xoopsDB']->queryF("INSERT INTO messages (sender, receiver, added, msg, poster) VALUES(0, {$arr['id']}, $dt, $msg, 0)") or sqlerr(__FILE__, __LINE__);
	    }
	  }
	
		// promote power users
		$limit = 25*1024*1024*1024;
		$minratio = 1.05;
		$maxdt = (time() - 86400*28);
		$res = @$GLOBALS['xoopsDB']->queryF("SELECT id FROM ".$GLOBALS['xoopsDB']->prefix("tb_users")." WHERE class = 0 AND uploaded >= $limit AND uploaded / downloaded >= $minratio AND added < $maxdt") or sqlerr(__FILE__, __LINE__);
		if (mysql_num_rows($res) > 0)
		{
			$dt = time();
			$msg = sqlesc("Congratulations, you have been auto-promoted to [b]Power User[/b]. :)\nYou can now download dox over 1 meg and view torrent NFOs.\n");
			while ($arr = $GLOBALS['xoopsDB']->fetchArray($res))
			{
				@$GLOBALS['xoopsDB']->queryF("UPDATE ".$GLOBALS['xoopsDB']->prefix("tb_users")." SET class = 1 WHERE id = {$arr['id']}") or sqlerr(__FILE__, __LINE__);
				//@$GLOBALS['xoopsDB']->queryF("INSERT INTO messages (sender, receiver, added, msg, poster) VALUES(0, {$arr['id']}, $dt, $msg, 0)") or sqlerr(__FILE__, __LINE__);
			}
		}
	
		// demote power users
		$minratio = 0.95;
		$res = $GLOBALS['xoopsDB']->queryF("SELECT id FROM ".$GLOBALS['xoopsDB']->prefix("tb_users")." WHERE class = 1 AND uploaded / downloaded < $minratio") or sqlerr(__FILE__, __LINE__);
		if (mysql_num_rows($res) > 0)
		{
			$dt = time();
			$msg = sqlesc("You have been auto-demoted from [b]Power User[/b] to [b]User[/b] because your share ratio has dropped below $minratio.\n");
			while ($arr = $GLOBALS['xoopsDB']->fetchArray($res))
			{
				@$GLOBALS['xoopsDB']->queryF("UPDATE ".$GLOBALS['xoopsDB']->prefix("tb_users")." SET class = 0 WHERE id = {$arr['id']}") or sqlerr(__FILE__, __LINE__);
				//@$GLOBALS['xoopsDB']->queryF("INSERT INTO messages (sender, receiver, added, msg, poster) VALUES(0, {$arr['id']}, $dt, $msg, 0)") or sqlerr(__FILE__, __LINE__);
			}
		}
	
		// Update stats
		$seeders = get_row_count($GLOBALS['xoopsDB']->prefix("tb_peers"), "WHERE seeder='yes'");
		$leechers = get_row_count($GLOBALS['xoopsDB']->prefix("tb_peers"), "WHERE seeder='no'");
		@$GLOBALS['xoopsDB']->queryF("UPDATE ".$GLOBALS['xoopsDB']->prefix("tb_avps")." SET value_u=$seeders WHERE arg='seeders'") or sqlerr(__FILE__, __LINE__);
		@$GLOBALS['xoopsDB']->queryF("UPDATE ".$GLOBALS['xoopsDB']->prefix("tb_avps")." SET value_u=$leechers WHERE arg='leechers'") or sqlerr(__FILE__, __LINE__);
	
		// update forum post/topic count
		//$forums = @$GLOBALS['xoopsDB']->queryF("SELECT t.forumid, count( DISTINCT p.topicid ) AS topics, count( * ) AS posts FROM posts p LEFT JOIN topics t ON t.id = p.topicid LEFT JOIN forums f ON f.id = t.forumid GROUP BY t.forumid");
		$forums = @$GLOBALS['xoopsDB']->queryF("SELECT f.id, count( DISTINCT t.id ) AS topics, count( * ) AS posts
	                          FROM forums f
	                          LEFT JOIN topics t ON f.id = t.forumid
	                          LEFT JOIN posts p ON t.id = p.topicid
	                          GROUP BY f.id");
		while ($forum = $GLOBALS['xoopsDB']->fetchArray($forums))
		{/*
			$postcount = 0;
			$topiccount = 0;
			$topics = $GLOBALS['xoopsDB']->queryF("select id from topics where forumid=$forum[id]");
			while ($topic = $GLOBALS['xoopsDB']->fetchArray($topics))
			{
				$res = $GLOBALS['xoopsDB']->queryF("select count(*) from posts where topicid=$topic[id]");
				$arr = mysql_fetch_row($res);
				$postcount += $arr[0];
				++$topiccount;
			} */
			$forum['posts'] = $forum['topics'] > 0 ? $forum['posts'] : 0;
			@$GLOBALS['xoopsDB']->queryF("update forums set postcount={$forum['posts']}, topiccount={$forum['topics']} where id={$forum['id']}");
		}
	
		// delete old torrents
		$days = 28;
		$dt = (time() - ($days * 86400));
		$res = $GLOBALS['xoopsDB']->queryF("SELECT id, name FROM ".$GLOBALS['xoopsDB']->prefix("tb_torrents")." WHERE added < $dt");
		while ($arr = $GLOBALS['xoopsDB']->fetchArray($res))
		{
			@unlink("{$GLOBALS['TBDEV']['torrent_dir']}/{$arr['id']}.torrent");
			@$GLOBALS['xoopsDB']->queryF("DELETE FROM ".$GLOBALS['xoopsDB']->prefix("tb_torrents")." WHERE id={$arr['id']}");
			@$GLOBALS['xoopsDB']->queryF("DELETE FROM ".$GLOBALS['xoopsDB']->prefix("tb_peers")." WHERE torrent={$arr['id']}");
			@$GLOBALS['xoopsDB']->queryF("DELETE FROM comments WHERE torrent={$arr['id']}");
			@$GLOBALS['xoopsDB']->queryF("DELETE FROM ".$GLOBALS['xoopsDB']->prefix("tb_files")." WHERE torrent={$arr['id']}");
			write_log("Torrent {$arr['id']} ({$arr['name']}) was deleted by system (older than $days days)");
		}
	
	}
}

if (!function_exists("unesc")) {
	function unesc($x) {
	    if (get_magic_quotes_gpc())
	        return stripslashes($x);
	    return $x;
	}
}

if (!function_exists("mksize")) {
	function mksize($bytes)
	{
		if ($bytes < 1000 * 1024)
			return number_format($bytes / 1024, 2) . " kB";
		elseif ($bytes < 1000 * 1048576)
			return number_format($bytes / 1048576, 2) . " MB";
		elseif ($bytes < 1000 * 1073741824)
			return number_format($bytes / 1073741824, 2) . " GB";
		else
			return number_format($bytes / 1099511627776, 2) . " TB";
	}
}

if (!function_exists("mksizeint")) {
	function mksizeint($bytes)
	{
		$bytes = max(0, $bytes);
		if ($bytes < 1000)
			return floor($bytes) . " B";
		elseif ($bytes < 1000 * 1024)
			return floor($bytes / 1024) . " kB";
		elseif ($bytes < 1000 * 1048576)
			return floor($bytes / 1048576) . " MB";
		elseif ($bytes < 1000 * 1073741824)
			return floor($bytes / 1073741824) . " GB";
		else
			return floor($bytes / 1099511627776) . " TB";
	}
}

if (!function_exists("mkprettytime")) {
	function mkprettytime($s) {
	    if ($s < 0)
	        $s = 0;
	    $t = array();
	    foreach (array("60:sec","60:min","24:hour","0:day") as $x) {
	        $y = explode(":", $x);
	        if ($y[0] > 1) {
	            $v = $s % $y[0];
	            $s = floor($s / $y[0]);
	        }
	        else
	            $v = $s;
	        $t[$y[1]] = $v;
	    }
	
	    if ($t["day"])
	        return $t["day"] . "d " . sprintf("%02d:%02d:%02d", $t["hour"], $t["min"], $t["sec"]);
	    if ($t["hour"])
	        return sprintf("%d:%02d:%02d", $t["hour"], $t["min"], $t["sec"]);
	    if ($t["min"])
	        return sprintf("%d:%02d", $t["min"], $t["sec"]);
	    return $t["sec"] . " secs";
	}
}

if (!function_exists("mkglobal")) {
	function mkglobal($vars) {
	    if (!is_array($vars))
	        $vars = explode(":", $vars);
	    foreach ($vars as $v) {
	        if (isset($_GET[$v]))
	            $GLOBALS[$v] = unesc($_GET[$v]);
	        elseif (isset($_POST[$v]))
	            $GLOBALS[$v] = unesc($_POST[$v]);
	        else
	            return 0;
	    }
	    return 1;
	}
}

if (!function_exists("validfilename")) {
	function validfilename($name) {
	    return preg_match('/^[^\0-\x1f:\\\\\/?*\xff#<>|]+$/si', $name);
	}
}

if (!function_exists("validemail")) {
	function validemail($email) {
	    return preg_match('/^[\w.-]+@([\w.-]+\.)+[a-z]{2,6}$/is', $email);
	}
}

if (!function_exists("sqlwildcardesc")) {
	function sqlwildcardesc($x) {
	    return str_replace(array("%","_"), array("\\%","\\_"), mysql_real_escape_string($x));
	}
}

if (!function_exists("stdhead")) {
	function stdhead( $title = "", $js='', $css='', $adminindex=0 ) {
		if (defined('IN_TBDEV_ADMIN')) {
			xoops_cp_header();
			loadModuleAdminMenu($adminindex);
		} else 
	    	include $GLOBALS['xoops']->path('header.php');
	    	
	    if (!$GLOBALS['TBDEV']['site_online'])
	      die("Site is down for maintenance, please check back again later... thanks<br />");
	
	    if ($title == "")
	        $title = $GLOBALS['TBDEV']['site_name'] .(isset($_GET['tbv'])?" (".TBVERSION.")":'');
	    else
	        $title = $GLOBALS['TBDEV']['site_name'].(isset($_GET['tbv'])?" (".TBVERSION.")":''). " :: " . htmlspecialchars($title);
	        
	    if ($GLOBALS['CURUSER'])  {
	      $GLOBALS['TBDEV']['stylesheet'] = isset($GLOBALS['CURUSER']['stylesheet']) ? "{$GLOBALS['CURUSER']['stylesheet']}.css" : $GLOBALS['TBDEV']['stylesheet'];
	    }
	    
	    if (is_object($GLOBALS['xoTheme'])) {
	    	if (file_exists($GLOBALS['xoops']->path('/modules/tb/language/'.$GLOBALS['xoopsConfig']['language'].'/'.$GLOBALS['TBDEV']['stylesheet']))) {
	    		$GLOBALS['xoTheme']->addStylesheet(XOOPS_URL.'/modules/tb/language/'.$GLOBALS['xoopsConfig']['language'].'/'.$GLOBALS['TBDEV']['stylesheet'], array('type'=>'text/css'));	
	    	} elseif (file_exists($GLOBALS['xoops']->path('/modules/tb/language/english/'.$GLOBALS['TBDEV']['stylesheet']))) {
	    		$GLOBALS['xoTheme']->addStylesheet(XOOPS_URL.'/modules/tb/language/english/'.$GLOBALS['TBDEV']['stylesheet'], array('type'=>'text/css'));
	    	}
	    	if (!empty($css)) {
				if (is_array($css)) {
					foreach($css as $stylesheet)
						$GLOBALS['xoTheme']->addStylesheet($stylesheet, array('type'=>'text/css'));
				} elseif (is_string($css)) {
					$GLOBALS['xoTheme']->addStylesheet('', array('type'=>'text/css'), $css);
				}    	
	    	}
	    	if (!empty($js)) {
				if (is_array($js)) {
					foreach($js as $script)
						$GLOBALS['xoTheme']->addScript($script, array('type'=>'text/javascript'));
				} elseif (is_string($js)) {
					$GLOBALS['xoTheme']->addScript('', array('type'=>'text/javascript'), $js);
				}    	
	    	}
	    }
	    
	    if (is_object($GLOBALS['xoopsTpl'])) {
	    	$GLOBALS['xoopsTpl']->assign('curuser', $GLOBALS['CURUSER']);
	    	$GLOBALS['xoopsTpl']->assign('xoConfig', $GLOBALS['xoopsModuleConfig']);
	    	if (!empty($title))
	    		$GLOBALS['xoopsTpl']->assign('xoops_pagetitle', $title);
	    }
	} 
}
if (!function_exists("stdfoot")) {
	function stdfoot() {
	    if (defined('IN_TBDEV_ADMIN'))
	    	xoops_cp_footer();
	    else 
	    	include $GLOBALS['xoops']->path('footer.php');
	}
}

if (!function_exists("httperr")) {
	function httperr($code = 404) {
	    header("HTTP/1.0 404 Not found");
	    print("<h1>Not Found</h1>\n");
	    print("<p>Sorry pal :(</p>\n");
	    exit();
	}
}

if (!function_exists("logincookie")) {
	function logincookie($id, $passhash, $updatedb = 1, $expires = 0x7fffffff) {
	    set_mycookie( "tbuid", $id, $expires );
	    set_mycookie( "pass", $passhash, $expires );
	}
}

if (!function_exists("set_mycookie")) {
	function set_mycookie( $name, $value="", $expires_in=0, $sticky=1 )  {
		if ( $sticky == 1 )    {
	      	$expires = time() + 60*60*24*365;
	    } else if ( $expires_in ) {
			$expires = time() + ( $expires_in * 86400 );
		} else {
			$expires = FALSE;
		}
			
		$GLOBALS['TBDEV']['cookie_domain'] = $GLOBALS['TBDEV']['cookie_domain'] == "" ? ""  : $GLOBALS['TBDEV']['cookie_domain'];
	    $GLOBALS['TBDEV']['cookie_path']   = $GLOBALS['TBDEV']['cookie_path']   == "" ? "/" : $GLOBALS['TBDEV']['cookie_path'];
	      	
		if ( PHP_VERSION < 5.2 ) {
	    	if ( $GLOBALS['TBDEV']['cookie_domain'] ) {
	        	@setcookie( $GLOBALS['TBDEV']['cookie_prefix'].$name, $value, $expires, $GLOBALS['TBDEV']['cookie_path'], $GLOBALS['TBDEV']['cookie_domain'] . '; HttpOnly' );
	      	} else {
	        	@setcookie( $GLOBALS['TBDEV']['cookie_prefix'].$name, $value, $expires, $GLOBALS['TBDEV']['cookie_path'] );
	      	}
	    } else {
	      @setcookie( $GLOBALS['TBDEV']['cookie_prefix'].$name, $value, $expires, $GLOBALS['TBDEV']['cookie_path'], $GLOBALS['TBDEV']['cookie_domain'], NULL, TRUE );
	    }
				
	}
}

if (!function_exists("get_mycookie")) {
	function get_mycookie($name) {
	   	if ( isset($_COOKIE[$GLOBALS['TBDEV']['cookie_prefix'].$name]) AND !empty($_COOKIE[$GLOBALS['TBDEV']['cookie_prefix'].$name]) )	{
	    	return urldecode($_COOKIE[$GLOBALS['TBDEV']['cookie_prefix'].$name]);
	    } else 	{
	    	return FALSE;
	    }
	}
}

if (!function_exists("logoutcookie")) {
	function logoutcookie() {
	    set_mycookie('tbuid', '-1');
	    set_mycookie('pass', '-1');
	}
}

if (!function_exists("loggedinorreturn")) {
	function loggedinorreturn() {
	    if (!$GLOBALS['CURUSER']) {
	        header("Location: {$GLOBALS['TBDEV']['baseurl']}/login.php?returnto=" . urlencode($_SERVER["REQUEST_URI"]));
	        exit();
	    }
	}
}

if (!function_exists("searchfield")) {
	function searchfield($s) {
	    return preg_replace(array('/[^a-z0-9]/si', '/^\s*/s', '/\s*$/s', '/\s+/s'), array(" ", "", "", " "), $s);
	}
}

if (!function_exists("genrelist")) {
	function genrelist() {
		$categories_handler = xoops_getmodulehandler('categories', 'tb');
	    foreach ($categories_handler->getObjects(NULL, true) as $id => $category)
	        $ret[$id] = array('id'=>$category->getVar('id'), 'name'=>$category->getVar('name'));
	    return $ret;
	}
}

if (!function_exists("stdmsg")) {
	function stdmsg($heading, $text)
	{
	    return  xoops_error($text, $heading);
	}
}

if (!function_exists("stderr")) {
	function stderr($heading, $text)
	{
		if (!defined('IN_TBDEV_ADMIN')&&!is_object($GLOBALS['xoopsTpl']))
			include $GLOBALS['xoops']->path('header.php');
			
		xoops_error($text, $heading);
	    
	    if (defined('IN_TBDEV_ADMIN'))
	    	xoops_cp_footer();
	    else 
	    	include $GLOBALS['xoops']->path('footer.php');
	    
	    exit();
	}
}

if (!function_exists("sqlerr")) {
	function sqlerr($file = '', $line = '') {
	   
		$the_error    = mysql_error();
		$the_error_no = mysql_errno();
	   	if ( SQL_DEBUG == 0 ) 	{
			exit();
	    } elseif ( $GLOBALS['TBDEV']['sql_error_log'] AND SQL_DEBUG == 1 ) {
			$_error_string  = "\n===================================================";
			$_error_string .= "\n Date: ". date( 'r' );
			$_error_string .= "\n Error Number: " . $the_error_no;
			$_error_string .= "\n Error: " . $the_error;
			$_error_string .= "\n IP Address: " . $_SERVER['REMOTE_ADDR'];
			$_error_string .= "\n in file ".$file." on line ".$line;
			$_error_string .= "\n URL:".$_SERVER['REQUEST_URI'];
			$_error_string .= "\n Username: {$GLOBALS['CURUSER']['username']}[{$GLOBALS['CURUSER']['id']}]";
			if ( $FH = @fopen( $GLOBALS['TBDEV']['sql_error_log'], 'a' ) )	{
				@fwrite( $FH, $_error_string );
				@fclose( $FH );
			}
	
			xoops_error($_error_string, 'MySQL Error');
	    
		} else {
			$the_error = "\nSQL error: ".$the_error."\n";
		    $the_error .= "SQL error code: ".$the_error_no."\n";
		    $the_error .= "Date: ".date("l dS \of F Y h:i:s A");
	    	xoops_error($the_error, 'MySQL Error');
		}
			
		if (defined('IN_TBDEV_ADMIN'))
			xoops_cp_footer();
		else
			include $GLOBALS['xoops']->path('footer.php');
	    exit();
	}
}
    
// Returns the current time in GMT in MySQL compatible format.
if (!function_exists("get_date_time")) {
	function get_date_time($timestamp = 0)
	{
	  if ($timestamp)
	    return date("Y-m-d H:i:s", $timestamp);
	  else
	    return gmdate("Y-m-d H:i:s");
	}
}

if (!function_exists("get_dt_num")) {
	function get_dt_num()
	{
	  return gmdate("YmdHis");
	}
}

if (!function_exists("write_log")) {
	function write_log($text)
	{
		$sitelog_handler = xoops_getmodulehandler('sitelog','tb');
		$log = $sitelog_handler->create();
		$log->setVar('txt', $text);
		$log->setVar('added', time());
		return $sitelog_handler->insert($log, true);
	}
}

if (!function_exists("sql_timestamp_to_unix_timestamp")) {
	function sql_timestamp_to_unix_timestamp($s)
	{
	  return mktime(substr($s, 11, 2), substr($s, 14, 2), substr($s, 17, 2), substr($s, 5, 2), substr($s, 8, 2), substr($s, 0, 4));
	}
}

if (!function_exists("get_elapsed_time")) {
	function get_elapsed_time($ts)
	{
		$mins = floor((gmtime() - $ts) / 60);
		$hours = floor($mins / 60);
		$mins -= $hours * 60;
		$days = floor($hours / 24);
		$hours -= $days * 24;
		$weeks = floor($days / 7);
		$days -= $weeks * 7;
		if ($weeks > 0)
		  return "$weeks week" . ($weeks > 1 ? "s" : "");
		if ($days > 0)
		  return "$days day" . ($days > 1 ? "s" : "");
		if ($hours > 0)
		  return "$hours hour" . ($hours > 1 ? "s" : "");
		if ($mins > 0)
		  return "$mins min" . ($mins > 1 ? "s" : "");
		return "< 1 min";
	}
}

if (!function_exists("unixstamp_to_human")) {
	function unixstamp_to_human( $unix=0 ) {
		$offset = get_time_offset();
	    $tmp    = gmdate( 'j,n,Y,G,i', $unix + $offset );
	    list( $day, $month, $year, $hour, $min ) = explode( ',', $tmp );
	  	return array( 	'day'    => $day,
	                    'month'  => $month,
	                    'year'   => $year,
	                    'hour'   => $hour,
	                    'minute' => $min );
	}
}    

if (!function_exists("get_time_offset")) {
	function get_time_offset() {
	   	$r = 0;
	   	$r = ( ($GLOBALS['CURUSER']['time_offset'] != "") ? $GLOBALS['CURUSER']['time_offset'] : $GLOBALS['TBDEV']['time_offset'] ) * 3600;
		if ( $GLOBALS['TBDEV']['time_adjust'] ) {
	        $r += ($GLOBALS['TBDEV']['time_adjust'] * 60);
	    }
	    if ( $GLOBALS['CURUSER']['dst_in_use'] ) {
	    	$r += 3600;
	    }
	    return $r;
	}
}

if (!function_exists("get_date")) {
	function get_date($date, $method, $norelative=0, $full_relative=0) {
		static $offset_set = 0;
	    static $today_time = 0;
	    static $yesterday_time = 0;
	    $time_options = array( 
	        					'JOINED' => $GLOBALS['TBDEV']['time_joined'],
	        					'SHORT'  => $GLOBALS['TBDEV']['time_short'],
								'LONG'   => $GLOBALS['TBDEV']['time_long'],
								'TINY'   => $GLOBALS['TBDEV']['time_tiny'] ? $GLOBALS['TBDEV']['time_tiny'] : 'j M Y - G:i',
								'DATE'   => $GLOBALS['TBDEV']['time_date'] ? $GLOBALS['TBDEV']['time_date'] : 'j M Y'
					);
	        
	    if ( ! $date ) {
	    	return '--';
	    }
	    if ( empty($method) ) {
	        $method = 'LONG';
	    }
	    if ($offset_set == 0) {
	        $GLOBALS['offset'] = get_time_offset();
			if ( $GLOBALS['TBDEV']['time_use_relative'] ) {
	        	$today_time     = gmdate('d,m,Y', ( time() + $GLOBALS['offset']) );
	        	$yesterday_time = gmdate('d,m,Y', ( (time() - 86400) + $GLOBALS['offset']) );
	        }	
	        $offset_set = 1;
	    }
	    if ( $GLOBALS['TBDEV']['time_use_relative'] == 3 ) {
			$full_relative = 1;
		}
	    if ( $full_relative and ( $norelative != 1 ) ) {
			$diff = time() - $date;
	        if ( $diff < 3600 ) {
				if ( $diff < 120 ) {
	            	return '< 1 minute ago';
	            } else {
	            	return sprintf( '%s minutes ago', intval($diff / 60) );
	            }
	        } elseif ( $diff < 7200 ) {
	            return '< 1 hour ago';
	        } elseif ( $diff < 86400 ) {
	            return sprintf( '%s hours ago', intval($diff / 3600) );
	        } elseif ( $diff < 172800 ) {
	            return '< 1 day ago';
	        } elseif ( $diff < 604800 ) {
	            return sprintf( '%s days ago', intval($diff / 86400) );
	        } elseif ( $diff < 1209600 ) {
	            return '< 1 week ago';
	        } elseif ( $diff < 3024000 ) {
	            return sprintf( '%s weeks ago', intval($diff / 604900) );
	        } else {
	            return gmdate($time_options[$method], ($date + $GLOBALS['offset']) );
	        }
	    } elseif ( $GLOBALS['TBDEV']['time_use_relative'] and ( $norelative != 1 ) ) {
	        $this_time = gmdate('d,m,Y', ($date + $GLOBALS['offset']) );
	        if ( $GLOBALS['TBDEV']['time_use_relative'] == 2 ) {
	            $diff = time() - $date;          
				if ( $diff < 3600 ) {
	            	if ( $diff < 120 ) {
	                	return '< 1 minute ago';
	              	} else {
	                	return sprintf( '%s minutes ago', intval($diff / 60) );
	              	}
	            }
	       	}
	        if ( $this_time == $today_time ) {
	        	return str_replace( '{--}', 'Today', gmdate($GLOBALS['TBDEV']['time_use_relative_format'], ($date + $GLOBALS['offset']) ) );
	        } elseif  ( $this_time == $yesterday_time ) {
	            return str_replace( '{--}', 'Yesterday', gmdate($GLOBALS['TBDEV']['time_use_relative_format'], ($date + $GLOBALS['offset']) ) );
	        } else {
	            return gmdate($time_options[$method], ($date + $GLOBALS['offset']) );
	        }
	    } else {
	    	return gmdate($time_options[$method], ($date + $GLOBALS['offset']) );
	    }
	}
}

if (!function_exists("hash_pad")) {
	function hash_pad($hash) {
	    return str_pad($hash, 20);
	}
}

if (!function_exists("StatusBar")) {
	function StatusBar() {
	
		
		$ret = array();
		if (!$GLOBALS['CURUSER'])
			return false;
	
		$ret['upped'] = mksize($GLOBALS['CURUSER']['uploaded']);
		$ret['downed'] = mksize($GLOBALS['CURUSER']['downloaded']);
		$ret['ratio'] = $GLOBALS['CURUSER']['downloaded'] > 0 ? $GLOBALS['CURUSER']['uploaded']/$GLOBALS['CURUSER']['downloaded'] : 0;
		$ret['ratio'] = number_format($ret['ratio'], 2);
	
		$ret['IsDonor'] = '';
		if ($GLOBALS['CURUSER']['donor'] == "yes")
			$ret['IsDonor'] = "<img src='pic/star.gif' alt='donor' title='donor' />";
	
		$ret['warn'] = '';
		if ($GLOBALS['CURUSER']['warned'] == "yes")
			$ret['warn'] = "<img src='pic/warned.gif' alt='warned' title='warned' />";
		
		if ($GLOBALS['TBDEV']['suport_mod_pm'] == true) {
			$messages_handler = xoops_getmodulehandler('message', 'pm');
			$criteria = new CriteriaCompo(new Criteria('to_userid', $GLOBALS['CURUSER']['uid']));
			$criteria->add(new Criteria('read_msg', '0'), "AND");
			$ret['unread'] = $messages_handler->getCount($criteria);	
		} else {
			$ret['unread'] = 0;
		}
			
		$ret['inbox'] = ($ret['unread'] == 1 ? $ret['unread']."&nbsp;{$GLOBALS['lang']['gl_msg_singular']}" : $ret['unread']."&nbsp;{$GLOBALS['lang']['gl_msg_plural']}");
		
		$ret['warn_inbox'] = sprintf($GLOBALS['lang']['gl_msg_alert'], $unread) . ($unread > 1 ? "s" : "");
		
		$peers_handler = xoops_getmodulehandler('peers', 'tb');
		$ret['seedleech'] = array('yes' => '0', 'no' => '0');
		foreach( $peers_handler->getPeersbyUser() as $pcount ) {
			if($pcount->getVar('seeder') == 'yes')
				$ret['seedleech']['yes'] = $pcount->getVar('pCount');
			else
				$ret['seedleech']['no'] = $pcount->getVar('pCount');
		}
		
		$users_handler = xoops_getmodulehandler('users', 'tb');
		$ret['reputation'] = $users_handler->get_reputation($GLOBALS['CURUSER']['object'], 1);
	
		$ret['date'] = date(DATE_RFC822);
		
		if (is_object($GLOBALS['xoopsTpl']))
			$GLOBALS['xoopsTpl']->assign('statusbar', $ret);
			
		return $ret;
	
	}
}

if (!function_exists("load_language")) {
	function load_language($file='') {
	    xoops_loadLanguage('lang_'.$file, 'tb');
		foreach($GLOBALS['lang'] as $key => $value)
			if (!defined(strtoupper('_TBDEV_LANG_'.$key)))
				define(strtoupper('_TBDEV_LANG_'.$key), $value);
	    
	    return $GLOBALS['lang'];
	}
}

?>
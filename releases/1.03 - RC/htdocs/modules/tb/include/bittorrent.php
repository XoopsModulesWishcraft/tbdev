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
require_once("include/config.php");
require_once("cleanup.php");
require_once("include/password_functions.php");


/**** validip/getip courtesy of manolete <manolete@myway.com> ****/

// IP Validation
function validip($ip)
{
	if (!empty($ip) && $ip == long2ip(ip2long($ip)))
	{
		// reserved IANA IPv4 addresses
		// http://www.iana.org/assignments/ipv4-address-space
		$reserved_ips = array (
				array('0.0.0.0','2.255.255.255'),
				array('10.0.0.0','10.255.255.255'),
				array('127.0.0.0','127.255.255.255'),
				array('169.254.0.0','169.254.255.255'),
				array('172.16.0.0','172.31.255.255'),
				array('192.0.2.0','192.0.2.255'),
				array('192.168.0.0','192.168.255.255'),
				array('255.255.255.0','255.255.255.255')
		);

		foreach ($reserved_ips as $r)
		{
				$min = ip2long($r[0]);
				$max = ip2long($r[1]);
				if ((ip2long($ip) >= $min) && (ip2long($ip) <= $max)) return false;
		}
		return true;
	}
	else return false;
}

// Patched function to detect REAL IP address if it's valid
function getip() {
   if (isset($_SERVER)) {
     if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && validip($_SERVER['HTTP_X_FORWARDED_FOR'])) {
       $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
     } elseif (isset($_SERVER['HTTP_CLIENT_IP']) && validip($_SERVER['HTTP_CLIENT_IP'])) {
       $ip = $_SERVER['HTTP_CLIENT_IP'];
     } else {
       $ip = $_SERVER['REMOTE_ADDR'];
     }
   } else {
     if (getenv('HTTP_X_FORWARDED_FOR') && validip(getenv('HTTP_X_FORWARDED_FOR'))) {
       $ip = getenv('HTTP_X_FORWARDED_FOR');
     } elseif (getenv('HTTP_CLIENT_IP') && validip(getenv('HTTP_CLIENT_IP'))) {
       $ip = getenv('HTTP_CLIENT_IP');
     } else {
       $ip = getenv('REMOTE_ADDR');
     }
   }

   return $ip;
 }

function dbconn($autoclean = false)
{
    

    if (!is_object($GLOBALS['xoopsDB']))
    {
	  switch (mysql_errno())
	  {
        default:
    	    die("[" . mysql_errno() . "] dbconn: mysql_connect: " . mysql_error());
      }
    }
    
    userlogin();

    if ($autoclean)
        register_shutdown_function("autoclean");
}


function userlogin() {
    
    unset($GLOBALS["CURUSER"]);

    $ip = getip();
	$nip = ip2long($ip);

    require_once "cache/bans_cache.php";
    if(count($bans) > 0)
    {
      foreach($bans as $k) {
        if($nip >= $k['first'] && $nip <= $k['last']) {
        header("HTTP/1.0 403 Forbidden");
        print "<html><body><h1>403 Forbidden</h1>Unauthorized IP address.</body></html>\n";
        exit();
        }
      }
      unset($bans);
    }
    
    if ( !$GLOBALS['TBDEV']['site_online'] || !get_mycookie('tbuid') || !get_mycookie('pass') ) {
    	return;
    }
		
	if (!get_mycookie('tbuid') && is_object($GLOBALS['xoopsUser'])) {
		$res = $GLOBALS['xoopsDB']->queryF("SELECT id, passhash, secret, enabled FROM users WHERE username = " . sqlesc($GLOBALS['xoopsUser']->getVar('uname')) . " AND status = 'confirmed'");
    	if ($GLOBALS['xoopsDB']->getRowsNum($res)==0) {
	        $secret = mksecret();
		    $wantpasshash = make_passhash( $secret, $GLOBALS['xoopsUser']->getVar('pass') );
		    $editsecret = ( !$arr[0] ? "" : make_passhash_login_key() );
	
		    $ret = $GLOBALS['xoopsDB']->queryF("INSERT INTO ".$GLOBALS['xoopsDB']->prefix("tb_users")." (username, uid, passhash, secret, editsecret, email, status, class, added, time_offset, dst_in_use) VALUES (" .
				implode(",", array_map("sqlesc", array($GLOBALS['xoopsUser']->getVar('uname'), $GLOBALS['xoopsUser']->getVar('uid'), $GLOBALS['xoopsUser']->getVar('pass'), $secret, $editsecret, $GLOBALS['xoopsUser']->getVar('email'), 'confirmed'))).
				", ". ($GLOBALS['xoopsUser']->isAdmin()?UC_SYSOP:UC_USER).",". time() .", \"".$time_offset."\", \"".$dst_in_use['tm_isdst'].'")');
			logincookie($GLOBALS['xoopsDB']->getInsertId(), $GLOBALS['xoopsUser']->getVar('pass'));
    	} else {
    		$row = $GLOBALS['xoopsDB']->fetchArray($res);
   			logincookie($row['id'], make_passhash( $row['secret'], $GLOBALS['xoopsUser']->getVar('pass') ));
    	}
	} elseif (!is_object($GLOBALS['xoopsUser'])) {
		return;
	}
    $id = 0 + get_mycookie('tbuid');
    if (!$id || strlen( get_mycookie('pass') ) != 32) {
        return;
    }
    $res = $GLOBALS['xoopsDB']->queryF("SELECT * FROM ".$GLOBALS['xoopsDB']->prefix("tb_users")." WHERE id = $id AND enabled='yes' AND status = 'confirmed'");// or die(mysql_error());
    $row = $GLOBALS['xoopsDB']->fetchArray($res);
    if (!$row) {
    	return;
    }
    
    //$sec = hash_pad($row["secret"]);
    $GLOBALS['xoopsDB']->queryF("UPDATE ".$GLOBALS['xoopsDB']->prefix("tb_users")." SET last_access='" . TIME_NOW . "', passhash = ".sqlesc(make_passhash( $row['secret'], $GLOBALS['xoopsUser']->getVar('pass') )).", ip=".sqlesc($ip)." WHERE id=" . $row["id"]);// or die(mysql_error());
    $row['ip'] = $ip;
    $GLOBALS["CURUSER"] = $row;
    
    //$GLOBALS['CURUSER']['group'] = $GLOBALS['TBDEV']['groups'][$row['class']];
    //$GLOBALS['CURUSER']['ismod'] = ( $GLOBALS['CURUSER']['group']['g_is_mod'] OR $GLOBALS['CURUSER']['group']['g_is_supmod'] ) ? 1:0;
}

function autoclean() {
    

    $now = time();
    //$docleanup = 0;

    $res = $GLOBALS['xoopsDB']->queryF("SELECT value_u FROM ".$GLOBALS['xoopsDB']->prefix("avps")." WHERE arg = 'lastcleantime'");
    $row = mysql_fetch_array($res);
    if (!$row) {
        $GLOBALS['xoopsDB']->queryF("INSERT INTO ".$GLOBALS['xoopsDB']->prefix("avps")." (arg, value_u) VALUES ('lastcleantime',$now)");
        return;
    }
    $ts = $row[0];
    if ($ts + $GLOBALS['TBDEV']['autoclean_interval'] > $now)
        return;
    $GLOBALS['xoopsDB']->queryF("UPDATE ".$GLOBALS['xoopsDB']->prefix("avps")." SET value_u=$now WHERE arg='lastcleantime' AND value_u = $ts");
    if (!mysql_affected_rows())
        return;

    
    docleanup();
}

function unesc($x) {
    if (get_magic_quotes_gpc())
        return stripslashes($x);
    return $x;
}

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
/*
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
*/

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
//    if ($t["min"])
        return sprintf("%d:%02d", $t["min"], $t["sec"]);
//    return $t["sec"] . " secs";
}

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


function validfilename($name) {
    return preg_match('/^[^\0-\x1f:\\\\\/?*\xff#<>|]+$/si', $name);
}

function validemail($email) {
    return preg_match('/^[\w.-]+@([\w.-]+\.)+[a-z]{2,6}$/is', $email);
}

function sqlesc($x) {
    return "'".mysql_real_escape_string($x)."'";
}

function sqlwildcardesc($x) {
    return str_replace(array("%","_"), array("\\%","\\_"), mysql_real_escape_string($x));
}


function stdhead( $title = "", $js='', $css='' ) {
    
    if (!$GLOBALS['TBDEV']['site_online'])
      die("Site is down for maintenance, please check back again later... thanks<br />");

    //header("Content-Type: text/html; charset=iso-8859-1");
    //header("Pragma: No-cache");
    if ($title == "")
        $title = $GLOBALS['TBDEV']['site_name'] .(isset($_GET['tbv'])?" (".TBVERSION.")":'');
    else
        $title = $GLOBALS['TBDEV']['site_name'].(isset($_GET['tbv'])?" (".TBVERSION.")":''). " :: " . htmlspecialchars($title);
        
    if ($GLOBALS['CURUSER'])
    {
      $GLOBALS['TBDEV']['stylesheet'] = isset($GLOBALS['CURUSER']['stylesheet']) ? "{$GLOBALS['CURUSER']['stylesheet']}.css" : $GLOBALS['TBDEV']['stylesheet'];
    }
    
  /* Deprecate this.
    if ($GLOBALS['TBDEV']['msg_alert'] && $msgalert && $GLOBALS['CURUSER'])
    {
      $res = $GLOBALS['xoopsDB']->queryF("SELECT COUNT(*) FROM messages WHERE receiver=" . $GLOBALS['CURUSER']["id"] . " && unread='yes'") or sqlerr(__FILE__,__LINE__);
      $arr = mysql_fetch_row($res);
      $unread = $arr[0];
    }
  */
    $htmlout = "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\"
		\"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">
		
		<html xmlns='http://www.w3.org/1999/xhtml'>
		<head>

			<meta name='generator' content='TBDev.net' />
			<meta http-equiv='Content-Language' content='en-us' />
			<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
			
			<title>{$title}</title>
			<link rel='stylesheet' href='{$GLOBALS['TBDEV']['stylesheet']}' type='text/css' />
			{$css}\n
			{$js}\n
		</head>
    
    <body>

      <table width='100%' cellspacing='0' cellpadding='0' style='background: transparent'>
      <tr>

      <td class='clear'>
      <div id='logostrip'>
      <img src='{$GLOBALS['TBDEV']['pic_base_url']}logo.jpg' alt='' />

      <a href='donate.php'><img src='{$GLOBALS['TBDEV']['pic_base_url']}x-click-but04.gif' border='0' alt='{$GLOBALS['lang']['gl_donate']}' title='{$GLOBALS['lang']['gl_donate']}' style='margin-top: 5px' /></a>
      </div>
      </td>

      </tr></table>

      <table class='mainouter' width='100%' border='1' cellspacing='0' cellpadding='10'>
<!-- STATUSBAR -->";

    $htmlout .= StatusBar();

    $htmlout .= "<!-- MENU -->
      <tr><td class='outer'>
      <div id='submenu'>";

    if ($GLOBALS['CURUSER']) 
    { 
      $htmlout .= "<div class='tb-top-left-link'>
      <a href='index.php'>{$GLOBALS['lang']['gl_home']}</a>
      <a href='browse.php'>{$GLOBALS['lang']['gl_browse']}</a>
      <a href='search.php'>{$GLOBALS['lang']['gl_search']}</a>
      <a href='upload.php'>{$GLOBALS['lang']['gl_upload']}</a>
      <a href='chat.php'>{$GLOBALS['lang']['gl_chat']}</a>
      <a href='forums.php'>{$GLOBALS['lang']['gl_forums']}</a>
      <!--<a href='misc/dox.php'>DOX</a>-->
      <a href='topten.php'>{$GLOBALS['lang']['gl_top_10']}</a>
      <a href='rules.php'>{$GLOBALS['lang']['gl_rules']}</a>
      <a href='faq.php'>{$GLOBALS['lang']['gl_faq']}</a>
      <a href='links.php'>{$GLOBALS['lang']['gl_links']}</a>
      <a href='staff.php'>{$GLOBALS['lang']['gl_staff']}</a>
      </div>
      <div class='tb-top-right-link'>";

      if( $GLOBALS['CURUSER']['class'] >= UC_MODERATOR )
      {
        $htmlout .= "<a href='admin.php'>{$GLOBALS['lang']['gl_admin']}</a>";
      }

    $htmlout .= "<a href='my.php'>{$GLOBALS['lang']['gl_profile']}</a>
      <a href='logout.php'>{$GLOBALS['lang']['gl_logout']}</a>
      </div>";
    } 
    else
    {
      $htmlout .= "<div class='tb-top-left-link'>
      <a href='login.php'>{$GLOBALS['lang']['gl_login']}</a>
      <a href='signup.php'>{$GLOBALS['lang']['gl_signup']}</a>
      <a href='recover.php'>{$GLOBALS['lang']['gl_recover']}</a>
      </div>";
    }

    $htmlout .= "</div>
    </td>
    </tr>
    <tr><td align='center' class='outer' style='padding-top: 20px; padding-bottom: 20px'>";


    /*if ($GLOBALS['TBDEV']['msg_alert'] && isset($unread) && !empty($unread))
    {
      $htmlout .= "<p><table border='0' cellspacing='0' cellpadding='10' bgcolor='red'>
                  <tr><td style='padding: 10px; background: red'>\n
                  <b><a href='messages.php'><font color='white'>".sprintf($GLOBALS['lang']['gl_msg_alert'], $unread) . ($unread > 1 ? "s" : "") . "!</font></a></b>
                  </td></tr></table></p>\n";
    }*/

    return $htmlout;
    
} // stdhead

function stdfoot() {
  
  
    return "<p align='center'>Remember, if you see any specific instance of this software running publicly, it's within your rights under gpl to garner a copy of that derivative from the person responsible for that webserver.<br />
    <a href='http://www.tbdev.net'><img src='{$GLOBALS['TBDEV']['pic_base_url']}tbdev_btn_red.png' border='0' alt='Powered By TBDev &copy;2010' title='Powered By TBDev &copy;2010' /></a></p>
    </td></tr></table>\n
    </body></html>\n";
}


function httperr($code = 404) {
    header("HTTP/1.0 404 Not found");
    print("<h1>Not Found</h1>\n");
    print("<p>Sorry pal :(</p>\n");
    exit();
}
/*
function gmtime()
{
    return strtotime(get_date_time());
}
*/
/*
function logincookie($id, $password, $secret, $updatedb = 1, $expires = 0x7fffffff) {
    $md5 = md5($secret . $password . $secret);
    setcookie("tbuid", $id, $expires, "/");
    setcookie("pass", $md5, $expires, "/");

    if ($updatedb)
        $GLOBALS['xoopsDB']->queryF("UPDATE ".$GLOBALS['xoopsDB']->prefix("tb_users")." SET last_login = NOW() WHERE id = $id");
}
*/

function logincookie($id, $passhash, $updatedb = 1, $expires = 0x7fffffff)
{
    //setcookie("tbuid", $id, $expires, "/");
    //setcookie("pass", $passhash, $expires, "/");
    set_mycookie( "tbuid", $id, $expires );
    set_mycookie( "pass", $passhash, $expires );
    
    if ($updatedb)
      @$GLOBALS['xoopsDB']->queryF("UPDATE ".$GLOBALS['xoopsDB']->prefix("tb_users")." SET last_login = ".TIME_NOW." WHERE id = $id");
}

function set_mycookie( $name, $value="", $expires_in=0, $sticky=1 )
    {
		
		
		if ( $sticky == 1 )
    {
      $expires = time() + 60*60*24*365;
    }
		else if ( $expires_in )
		{
			$expires = time() + ( $expires_in * 86400 );
		}
		else
		{
			$expires = FALSE;
		}
		
		$GLOBALS['TBDEV']['cookie_domain'] = $GLOBALS['TBDEV']['cookie_domain'] == "" ? ""  : $GLOBALS['TBDEV']['cookie_domain'];
    	$GLOBALS['TBDEV']['cookie_path']   = $GLOBALS['TBDEV']['cookie_path']   == "" ? "/" : $GLOBALS['TBDEV']['cookie_path'];
      	
		if ( PHP_VERSION < 5.2 )
		{
      if ( $GLOBALS['TBDEV']['cookie_domain'] )
      {
        @setcookie( $GLOBALS['TBDEV']['cookie_prefix'].$name, $value, $expires, $GLOBALS['TBDEV']['cookie_path'], $GLOBALS['TBDEV']['cookie_domain'] . '; HttpOnly' );
      }
      else
      {
        @setcookie( $GLOBALS['TBDEV']['cookie_prefix'].$name, $value, $expires, $GLOBALS['TBDEV']['cookie_path'] );
      }
    }
    else
    {
      @setcookie( $GLOBALS['TBDEV']['cookie_prefix'].$name, $value, $expires, $GLOBALS['TBDEV']['cookie_path'], $GLOBALS['TBDEV']['cookie_domain'], NULL, TRUE );
    }
			
}
function get_mycookie($name) 
    {
      
      
    	if ( isset($_COOKIE[$GLOBALS['TBDEV']['cookie_prefix'].$name]) AND !empty($_COOKIE[$GLOBALS['TBDEV']['cookie_prefix'].$name]) )
    	{
    		return urldecode($_COOKIE[$GLOBALS['TBDEV']['cookie_prefix'].$name]);
    	}
    	else
    	{
    		return FALSE;
    	}
}

function logoutcookie() {
    //setcookie("tbuid", "", 0x7fffffff, "/");
    //setcookie("pass", "", 0x7fffffff, "/");
    set_mycookie('tbuid', '-1');
    set_mycookie('pass', '-1');
}

function loggedinorreturn() {
    
    if (!$GLOBALS['CURUSER']) {
        header("Location: {$GLOBALS['TBDEV']['baseurl']}/login.php?returnto=" . urlencode($_SERVER["REQUEST_URI"]));
        exit();
    }
}


function searchfield($s) {
    return preg_replace(array('/[^a-z0-9]/si', '/^\s*/s', '/\s*$/s', '/\s+/s'), array(" ", "", "", " "), $s);
}

function genrelist() {
    $ret = array();
    $res = $GLOBALS['xoopsDB']->queryF("SELECT id, name FROM ".$GLOBALS['xoopsDB']->prefix("categories")." ORDER BY name");
    while ($row = mysql_fetch_array($res))
        $ret[] = $row;
    return $ret;
}


function get_row_count($table, $suffix = "")
{
  if ($suffix)
    $suffix = " $suffix";
  ($r = $GLOBALS['xoopsDB']->queryF("SELECT COUNT(*) FROM $table$suffix")) or die(mysql_error());
  ($a = mysql_fetch_row($r)) or die(mysql_error());
  return $a[0];
}

function stdmsg($heading, $text)
{
    $htmlout = "<table class='main' width='750' border='0' cellpadding='0' cellspacing='0'>
    <tr><td class='embedded'>\n";
    
    if ($heading)
      $htmlout .= "<h2>$heading</h2>\n";
    
    $htmlout .= "<table width='100%' border='1' cellspacing='0' cellpadding='10'><tr><td class='text'>\n";
    $htmlout .= "{$text}</td></tr></table></td></tr></table>\n";
  
    return $htmlout;
}


function stderr($heading, $text)
{
    $htmlout = stdhead();
    $htmlout .= stdmsg($heading, $text);
    $htmlout .= stdfoot();
    
    print $htmlout;
    exit();
}
	
// Basic MySQL error handler

function sqlerr($file = '', $line = '') {
    
    
		$the_error    = mysql_error();
		$the_error_no = mysql_errno();

    	if ( SQL_DEBUG == 0 )
    	{
			exit();
    	}
     	else if ( $GLOBALS['TBDEV']['sql_error_log'] AND SQL_DEBUG == 1 )
		{
			$_error_string  = "\n===================================================";
			$_error_string .= "\n Date: ". date( 'r' );
			$_error_string .= "\n Error Number: " . $the_error_no;
			$_error_string .= "\n Error: " . $the_error;
			$_error_string .= "\n IP Address: " . $_SERVER['REMOTE_ADDR'];
			$_error_string .= "\n in file ".$file." on line ".$line;
			$_error_string .= "\n URL:".$_SERVER['REQUEST_URI'];
			$_error_string .= "\n Username: {$GLOBALS['CURUSER']['username']}[{$GLOBALS['CURUSER']['id']}]";
			
			if ( $FH = @fopen( $GLOBALS['TBDEV']['sql_error_log'], 'a' ) )
			{
				@fwrite( $FH, $_error_string );
				@fclose( $FH );
			}
			
			print "<html><head><title>MySQL Error</title>
					<style>P,BODY{ font-family:arial,sans-serif; font-size:11px; }</style></head><body>
		    		   <blockquote><h1>MySQL Error</h1><b>There appears to be an error with the database.</b><br />
		    		   You can try to refresh the page by clicking <a href=\"javascript:window.location=window.location;\">here</a>
				  </body></html>";
		}
		else
		{
    		$the_error = "\nSQL error: ".$the_error."\n";
	    	$the_error .= "SQL error code: ".$the_error_no."\n";
	    	$the_error .= "Date: ".date("l dS \of F Y h:i:s A");
    	
	    	$out = "<html>\n<head>\n<title>MySQL Error</title>\n
	    		   <style>P,BODY{ font-family:arial,sans-serif; font-size:11px; }</style>\n</head>\n<body>\n
	    		   <blockquote>\n<h1>MySQL Error</h1><b>There appears to be an error with the database.</b><br />
	    		   You can try to refresh the page by clicking <a href=\"javascript:window.location=window.location;\">here</a>.
	    		   <br /><br /><b>Error Returned</b><br />
	    		   <form name='mysql'><textarea rows=\"15\" cols=\"60\">".htmlentities($the_error, ENT_QUOTES)."</textarea></form><br>We apologise for any inconvenience</blockquote></body></html>";
    		   
    
	       	print $out;
		}
		
		if (defined('IN_TBDEV_ADMIN'))
			xoops_cp_footer();
		else
			include $GLOBALS['xoops']->path('/footer.php');
        exit();
}
    
/*    
// Returns the current time in GMT in MySQL compatible format.
function get_date_time($timestamp = 0)
{
  if ($timestamp)
    return date("Y-m-d H:i:s", $timestamp);
  else
    return gmdate("Y-m-d H:i:s");
}
*/

function get_dt_num()
{
  return gmdate("YmdHis");
}



function write_log($text)
{
  $text = sqlesc($text);
  $added = TIME_NOW;
  $GLOBALS['xoopsDB']->queryF("INSERT INTO ".$GLOBALS['xoopsDB']->prefix("sitelog")." (added, txt) VALUES($added, $text)") or sqlerr(__FILE__, __LINE__);
}


function sql_timestamp_to_unix_timestamp($s)
{
  return mktime(substr($s, 11, 2), substr($s, 14, 2), substr($s, 17, 2), substr($s, 5, 2), substr($s, 8, 2), substr($s, 0, 4));
}

/*
function get_elapsed_time($ts)
{
  $mins = floor((gmtime() - $ts) / 60);
  $hours = floor($mins / 60);
  $mins -= $hours * 60;
  $days = floor($hours / 24);
  $hours -= $days * 24;
  $weeks = floor($days / 7);
  $days -= $weeks * 7;
//  $t = "";
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
*/


function unixstamp_to_human( $unix=0 )
    {
    	$offset = get_time_offset();
    	$tmp    = gmdate( 'j,n,Y,G,i', $unix + $offset );
    	
    	list( $day, $month, $year, $hour, $min ) = explode( ',', $tmp );
  
    	return array( 'day'    => $day,
                    'month'  => $month,
                    'year'   => $year,
                    'hour'   => $hour,
                    'minute' => $min );
    }
    


function get_time_offset() {
    
    	
    	$r = 0;
    	
    	$r = ( ($GLOBALS['CURUSER']['time_offset'] != "") ? $GLOBALS['CURUSER']['time_offset'] : $GLOBALS['TBDEV']['time_offset'] ) * 3600;
			
      if ( $GLOBALS['TBDEV']['time_adjust'] )
      {
        $r += ($GLOBALS['TBDEV']['time_adjust'] * 60);
      }
      
      if ( $GLOBALS['CURUSER']['dst_in_use'] )
      {
        $r += 3600;
      }
        
        return $r;
}
    

function get_date($date, $method, $norelative=0, $full_relative=0)
    {
        
        
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
        
        if ( ! $date )
        {
            return '--';
        }
        
        if ( empty($method) )
        {
        	$method = 'LONG';
        }
        
        if ($offset_set == 0)
        {
        	$GLOBALS['offset'] = get_time_offset();
			
          if ( $GLOBALS['TBDEV']['time_use_relative'] )
          {
            $today_time     = gmdate('d,m,Y', ( time() + $GLOBALS['offset']) );
            $yesterday_time = gmdate('d,m,Y', ( (time() - 86400) + $GLOBALS['offset']) );
          }	
        
          $offset_set = 1;
        }
        
        if ( $GLOBALS['TBDEV']['time_use_relative'] == 3 )
        {
        	$full_relative = 1;
        }
        
        if ( $full_relative and ( $norelative != 1 ) )
        {
          $diff = time() - $date;
          
          if ( $diff < 3600 )
          {
            if ( $diff < 120 )
            {
              return '< 1 minute ago';
            }
            else
            {
              return sprintf( '%s minutes ago', intval($diff / 60) );
            }
          }
          else if ( $diff < 7200 )
          {
            return '< 1 hour ago';
          }
          else if ( $diff < 86400 )
          {
            return sprintf( '%s hours ago', intval($diff / 3600) );
          }
          else if ( $diff < 172800 )
          {
            return '< 1 day ago';
          }
          else if ( $diff < 604800 )
          {
            return sprintf( '%s days ago', intval($diff / 86400) );
          }
          else if ( $diff < 1209600 )
          {
            return '< 1 week ago';
          }
          else if ( $diff < 3024000 )
          {
            return sprintf( '%s weeks ago', intval($diff / 604900) );
          }
          else
          {
            return gmdate($time_options[$method], ($date + $GLOBALS['offset']) );
          }
        }
        else if ( $GLOBALS['TBDEV']['time_use_relative'] and ( $norelative != 1 ) )
        {
          $this_time = gmdate('d,m,Y', ($date + $GLOBALS['offset']) );
          
          if ( $GLOBALS['TBDEV']['time_use_relative'] == 2 )
          {
            $diff = time() - $date;
          
            if ( $diff < 3600 )
            {
              if ( $diff < 120 )
              {
                return '< 1 minute ago';
              }
              else
              {
                return sprintf( '%s minutes ago', intval($diff / 60) );
              }
            }
          }
          
            if ( $this_time == $today_time )
            {
              return str_replace( '{--}', 'Today', gmdate($GLOBALS['TBDEV']['time_use_relative_format'], ($date + $GLOBALS['offset']) ) );
            }
            else if  ( $this_time == $yesterday_time )
            {
              return str_replace( '{--}', 'Yesterday', gmdate($GLOBALS['TBDEV']['time_use_relative_format'], ($date + $GLOBALS['offset']) ) );
            }
            else
            {
              return gmdate($time_options[$method], ($date + $GLOBALS['offset']) );
            }
        }
        else
        {
          return gmdate($time_options[$method], ($date + $GLOBALS['offset']) );
        }
}


function hash_pad($hash) {
    return str_pad($hash, 20);
}


function StatusBar() {

	
	
	if (!$GLOBALS['CURUSER'])
		return "<tr><td colspan='2'>Yeah Yeah!</td></tr>";


	$upped = mksize($GLOBALS['CURUSER']['uploaded']);
	
	$downed = mksize($GLOBALS['CURUSER']['downloaded']);
	
	$ratio = $GLOBALS['CURUSER']['downloaded'] > 0 ? $GLOBALS['CURUSER']['uploaded']/$GLOBALS['CURUSER']['downloaded'] : 0;
	
	$ratio = number_format($ratio, 2);

	$IsDonor = '';
	if ($GLOBALS['CURUSER']['donor'] == "yes")
	
	$IsDonor = "<img src='pic/star.gif' alt='donor' title='donor' />";


	$warn = '';
	if ($GLOBALS['CURUSER']['warned'] == "yes")
	
	$warn = "<img src='pic/warned.gif' alt='warned' title='warned' />";
	
	//$res1 = @$GLOBALS['xoopsDB']->queryF("SELECT COUNT(*) FROM messages WHERE receiver=" . $GLOBALS['CURUSER']["id"] . " AND unread='yes'") or sqlerr(__LINE__,__FILE__);
	
	//$arr1 = mysql_fetch_row($res1);
	
	$unread = 0;
	
	$inbox = ($unread == 1 ? "$unread&nbsp;{$GLOBALS['lang']['gl_msg_singular']}" : "$unread&nbsp;{$GLOBALS['lang']['gl_msg_plural']}");

	
	$res2 = @$GLOBALS['xoopsDB']->queryF("SELECT seeder, COUNT(*) AS pCount FROM ".$GLOBALS['xoopsDB']->prefix("peers")." WHERE userid=".$GLOBALS['CURUSER']['id']." GROUP BY seeder") or sqlerr(__LINE__,__FILE__);
	
	$seedleech = array('yes' => '0', 'no' => '0');
	
	while( $row = $GLOBALS['xoopsDB']->fetchArray($res2) ) {
		if($row['seeder'] == 'yes')
			$seedleech['yes'] = $row['pCount'];
		else
			$seedleech['no'] = $row['pCount'];
		
	}
	
/////////////// REP SYSTEM /////////////
//$GLOBALS['CURUSER']['reputation'] = 49;

	$member_reputation = get_reputation($GLOBALS['CURUSER'], 1);
////////////// REP SYSTEM END //////////

	$StatusBar = '';
		$StatusBar = "<tr>".

		"<td colspan='2' style='padding: 2px;'>".

		"<div id='statusbar'>".
		"<div style='float:left;color:black;'>{$GLOBALS['lang']['gl_msg_welcome']}, <a href='userdetails.php?id={$GLOBALS['CURUSER']['id']}'>{$GLOBALS['CURUSER']['username']}</a>".
		  
		"$IsDonor$warn&nbsp; [<a href='logout.php'>{$GLOBALS['lang']['gl_logout']}</a>]&nbsp;$member_reputation
		<br />{$GLOBALS['lang']['gl_ratio']}:$ratio".
		"&nbsp;&nbsp;{$GLOBALS['lang']['gl_uploaded']}:$upped".
		"&nbsp;&nbsp;{$GLOBALS['lang']['gl_downloaded']}:$downed".
		
		"&nbsp;&nbsp;{$GLOBALS['lang']['gl_act_torrents']}:&nbsp;<img alt='{$GLOBALS['lang']['gl_seed_torrents']}' title='{$GLOBALS['lang']['gl_seed_torrents']}' src='pic/arrowup.gif' />&nbsp;{$seedleech['yes']}".
		
		"&nbsp;&nbsp;<img alt='{$GLOBALS['lang']['gl_leech_torrents']}' title='{$GLOBALS['lang']['gl_leech_torrents']}' src='pic/arrowdown.gif' />&nbsp;{$seedleech['no']}</div>".
    
		"<div><p style='text-align:right;'>".date(DATE_RFC822)."<br />".

    //"<a href='messages.php'>$inbox</a></p></div>".
    "</div></td></tr>";
	
	return $StatusBar;

}


function load_language($file='') {

    
  
/*    if( !isset($GLOBALS['CURUSER']) OR empty($GLOBALS['CURUSER']['language']) )
    {
      if( !file_exists(ROOT_PATH."/language/{$GLOBALS['xoopsConfig']['language']}/lang_{$file}.php") )
      {
        stderr('SYSTEM ERROR', 'Can\'t find language files - missing '.ROOT_PATH."/language/{$GLOBALS['xoopsConfig']['language']}/lang_{$file}.php");
      }
      


    }
    
    if( !file_exists(ROOT_PATH."/language/{$GLOBALS['CURUSER']['language']}/lang_{$file}.php") )
    {
      stderr('SYSTEM ERROR', 'Can\'t find language files - missing '.ROOT_PATH."/language/{$GLOBALS['CURUSER']['language']}/lang_{$file}.php");
    }
    else
    {
      xoops_loadLanguage('lang_'.$file, 'tb'); 
    }*/
	
    xoops_loadLanguage('lang_'.$file, 'tb');

	foreach($GLOBALS['lang'] as $key => $value)
		if (!defined(strtoupper('_TBDEV_LANG_'.$key)))
			define(strtoupper('_TBDEV_LANG_'.$key), $value);
    
    return $GLOBALS['lang'];
}


?>
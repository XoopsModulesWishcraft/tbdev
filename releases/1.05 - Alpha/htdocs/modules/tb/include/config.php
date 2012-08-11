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

error_reporting(E_ALL);

define('SQL_DEBUG', 2);

/* Compare php version for date/time stuff etc! */
	if (version_compare(PHP_VERSION, "5.1.0RC1", ">="))
		date_default_timezone_set('Europe/London');


define('TIME_NOW', time());

include_once(dirname(dirname(dirname(dirname(__FILE__)))).'/mainfile.php');

$module_handler = xoops_gethandler('module');
$config_handler = xoops_gethandler('config');
$xomod = $module_handler->getByDirname('tb');
if (is_object($xomod))
	$GLOBALS['TBDEV'] = $config_handler->getConfigList($xomod->getVar('mid'));

if (!empty($GLOBALS['TBDEV']))
	$GLOBALS['xoopsModuleConfig'] = $GLOBALS['TBDEV'];

$GLOBALS['TBDEV']['mysql_db'] = XOOPS_DB_NAME;

if ( strtoupper( substr(PHP_OS, 0, 3) ) == 'WIN' )
  {
    $file_path = str_replace( "\\", "/", dirname(__FILE__) );
    $file_path = str_replace( "/include", "", $file_path );
  }
  else
  {
    $file_path = dirname(__FILE__);
    $file_path = str_replace( "/include", "", $file_path );
  }
  
define('ROOT_PATH', $file_path);

# the first one will be displayed on the pages
$GLOBALS['TBDEV']['announce_urls'] = explode('|', $GLOBALS['TBDEV']['announce_urlss']);

if ($_SERVER["HTTP_HOST"] == "")
  $_SERVER["HTTP_HOST"] = $_SERVER["SERVER_NAME"];
  
//charset
if (ini_get('default_charset') != $GLOBALS['TBDEV']['char_set']) {
ini_set('default_charset',$GLOBALS['TBDEV']['char_set']);
}

$GLOBALS['TBDEV']['allowed_ext'] = explode('|', $GLOBALS['TBDEV']['allowed_exts']);

define('UC_USER', 0);
define('UC_POWER_USER', 1);
define('UC_VIP', 2);
define('UC_UPLOADER', 3);
define('UC_MODERATOR', 4);
define('UC_ADMINISTRATOR', 5);
define('UC_SYSOP', 6);

//Do not modify -- versioning system
//This will help identify code for support issues at tbdev.net
define('TBVERSION','TBDev_XOOPS2011_svn');

?>
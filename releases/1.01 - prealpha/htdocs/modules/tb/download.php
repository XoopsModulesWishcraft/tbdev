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
$GLOBALS['xoopsLogger']->activated = false;
require_once "include/bittorrent.php";
require_once "include/user_functions.php";

dbconn();

loggedinorreturn();

  //$GLOBALS['lang'] = load_language('download');
  $GLOBALS['lang'] = array_merge( load_language('global'), load_language('download') );
  
  $id = isset($_GET['torrent']) ? intval($_GET['torrent']) : 0;

  if ( !is_valid_id($id) )
    stderr("{$GLOBALS['lang']['download_user_error']}", "{$GLOBALS['lang']['download_no_id']}");


  $res = $GLOBALS['xoopsDB']->queryF("SELECT name, filename FROM ".$GLOBALS['xoopsDB']->prefix("torrents")." WHERE id = $id") or sqlerr(__FILE__, __LINE__);
  $row = $GLOBALS['xoopsDB']->fetchArray($res);

  $fn = "{$GLOBALS['TBDEV']['torrent_dir']}/$id.torrent";

  if (!$row || !is_file($fn) || !is_readable($fn))
    httperr();


  @$GLOBALS['xoopsDB']->queryF("UPDATE ".$GLOBALS['xoopsDB']->prefix("torrents")." SET hits = hits + 1 WHERE id = $id");

  require_once "include/benc.php";

  if (!isset($GLOBALS['CURUSER']['passkey']) || strlen($GLOBALS['CURUSER']['passkey']) != 32) 
  {
    $GLOBALS['CURUSER']['passkey'] = md5($GLOBALS['CURUSER']['username'].time().$GLOBALS['CURUSER']['passhash']);
    @$GLOBALS['xoopsDB']->queryF("UPDATE ".$GLOBALS['xoopsDB']->prefix("users")." SET passkey='{$GLOBALS['CURUSER']['passkey']}' WHERE id={$GLOBALS['CURUSER']['id']}");
  }


  $dict = bdec_file($fn, filesize($fn));

  $dict['value']['announce']['value'] = "{$GLOBALS['TBDEV']['announce_urls'][0]}?passkey={$GLOBALS['CURUSER']['passkey']}";

  $dict['value']['announce']['string'] = strlen($dict['value']['announce']['value']).":".$dict['value']['announce']['value'];

  $dict['value']['announce']['strlen'] = strlen($dict['value']['announce']['string']);



  header('Content-Disposition: attachment; filename="'.$row['filename'].'"');

  header("Content-Type: application/x-bittorrent");



  print(benc($dict));



?>
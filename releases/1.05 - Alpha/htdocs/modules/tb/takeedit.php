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

    $GLOBALS['lang'] = array_merge( $GLOBALS['lang'], load_language('takeedit') );


    if (!mkglobal('name:descr:type'))
      stderr($GLOBALS['lang']['takedit_failed'], $GLOBALS['lang']['takedit_no_data']);

    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    if ( !is_valid_id($id) )
      stderr($GLOBALS['lang']['takedit_failed'], $GLOBALS['lang']['takedit_no_data']);
        
    
    $res = $GLOBALS['xoopsDB']->queryF("SELECT owner, filename, save_as FROM ".$GLOBALS['xoopsDB']->prefix("tb_torrents")." WHERE id = $id");
    
    if ( false == mysql_num_rows($res) )
      stderr($GLOBALS['lang']['takedit_failed'], $GLOBALS['lang']['takedit_no_data']);
      
    $row = $GLOBALS['xoopsDB']->fetchArray($res);

    if ($GLOBALS['CURUSER']['id'] != $row['owner'] && $GLOBALS['CURUSER']['class'] < UC_MODERATOR)
      stderr($GLOBALS['lang']['takedit_failed'], $GLOBALS['lang']['takedit_not_owner']);

    $updateset = array();

    $fname = $row['filename'];
    preg_match('/^(.+)\.torrent$/si', $fname, $matches);
    $shortfname = $matches[1];
    $dname = $row['save_as'];

    $nfoaction = $_POST['nfoaction'];
    if ($nfoaction == 'update')
    {
      $nfofile = $_FILES['nfo'];
      if (!$nfofile) die("No data " . var_dump($_FILES));
      if ($nfofile['size'] > 65535)
        stderr($GLOBALS['lang']['takedit_failed'], $GLOBALS['lang']['takedit_nfo_error']);
      $nfofilename = $nfofile['tmp_name'];
      if (@is_uploaded_file($nfofilename) && @filesize($nfofilename) > 0)
        $updateset[] = "nfo = " . sqlesc(str_replace("\x0d\x0d\x0a", "\x0d\x0a", file_get_contents($nfofilename)));
    }
    else
      if ($nfoaction == 'remove')
        $updateset[] = 'nfo = ""';

    $updateset[] = "name = " . sqlesc($name);
    $updateset[] = "search_text = " . sqlesc(searchfield("$shortfname $dname $name"));
    $updateset[] = "descr = " . sqlesc($descr);
    $updateset[] = "ori_descr = " . sqlesc($descr);
    $updateset[] = "category = " . (0 + $type);
    //if ($GLOBALS['CURUSER']["admin"] == "yes") {
    if ($GLOBALS['CURUSER']['class'] > UC_MODERATOR) {
      if ( isset($_POST['banned']) ) {
        $updateset[] = 'banned = "yes"';
        $_POST['visible'] = 0;
      }
      else
        $updateset[] = 'banned = "no"';
    }
    $updateset[] = "visible = '" . ( isset($_POST['visible']) ? 'yes' : 'no') . "'";

    $GLOBALS['xoopsDB']->queryF("UPDATE ".$GLOBALS['xoopsDB']->prefix("tb_torrents")." SET " . join(",", $updateset) . " WHERE id = $id");

    write_log(sprintf($GLOBALS['lang']['takedit_log'], $id, $name, $GLOBALS['CURUSER']['username']));
    
    $returnto = "{$GLOBALS['TBDEV']['baseurl']}/details.php?id=$id&amp;edited=1";
    
    header("Location: $returnto");


?>
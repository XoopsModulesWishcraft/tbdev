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
require_once "../../mainfile.php";
require_once("include/benc.php");
require_once("include/bittorrent.php");
require_once "include/user_functions.php";

//@ini_set("upload_max_filesize",$GLOBALS['TBDEV']['max_torrent_size']);


dbconn(); 

loggedinorreturn();
    
    $GLOBALS['lang'] = array_merge( load_language('global'), load_language('takeupload') );
    
    if ($GLOBALS['CURUSER']['class'] < UC_UPLOADER)
      header( "Location: {$GLOBALS['TBDEV']['baseurl']}/upload.php" );

    foreach(explode(":","descr:type:name") as $v) {
      if (!isset($_POST[$v])) {
        include $GLOBALS['xoops']->path('header.php');
      	stderr($GLOBALS['lang']['takeupload_failed'], $GLOBALS['lang']['takeupload_no_formdata']);
      	include $GLOBALS['xoops']->path('footer.php');
      }
    }

    if (!isset($_FILES["file"])) {
    	include $GLOBALS['xoops']->path('header.php');
      	stderr($GLOBALS['lang']['takeupload_failed'], $GLOBALS['lang']['takeupload_no_formdata']);
      	include $GLOBALS['xoops']->path('footer.php');
    }

    $f = $_FILES["file"];
    $fname = unesc($f["name"]);
    if (empty($fname)) {
    	include $GLOBALS['xoops']->path('header.php');
      	stderr($GLOBALS['lang']['takeupload_failed'], $GLOBALS['lang']['takeupload_no_filename']);
      	include $GLOBALS['xoops']->path('footer.php');
    }
      
    $nfo = sqlesc('');
    /////////////////////// NFO FILE ////////////////////////	
    if(isset($_FILES['nfo']) && !empty($_FILES['nfo']['name'])) {
    $nfofile = $_FILES['nfo'];
    if ($nfofile['name'] == '') {
    	include $GLOBALS['xoops']->path('header.php');
        stderr($GLOBALS['lang']['takeupload_failed'], $GLOBALS['lang']['takeupload_no_nfo']);
        include $GLOBALS['xoops']->path('footer.php');
    }

    if ($nfofile['size'] == 0) {
    	include $GLOBALS['xoops']->path('header.php');
        stderr($GLOBALS['lang']['takeupload_failed'], $GLOBALS['lang']['takeupload_0_byte']);
        include $GLOBALS['xoops']->path('footer.php');
    }

    if ($nfofile['size'] > 65535) {
    	include $GLOBALS['xoops']->path('header.php');
        stderr($GLOBALS['lang']['takeupload_failed'], $GLOBALS['lang']['takeupload_nfo_big']);
        include $GLOBALS['xoops']->path('footer.php');
    }

    $nfofilename = $nfofile['tmp_name'];

    if (@!is_uploaded_file($nfofilename)) {
    	include $GLOBALS['xoops']->path('header.php');
        stderr($GLOBALS['lang']['takeupload_failed'], $GLOBALS['lang']['takeupload_nfo_failed']);
        include $GLOBALS['xoops']->path('footer.php');
    }

    $nfo = sqlesc(str_replace("\x0d\x0d\x0a", "\x0d\x0a", @file_get_contents($nfofilename)));
    }
    /////////////////////// NFO FILE END /////////////////////

    $descr = unesc($_POST["descr"]);
    if (!$descr) {
    	include $GLOBALS['xoops']->path('header.php');
        stderr($GLOBALS['lang']['takeupload_failed'], $GLOBALS['lang']['takeupload_no_descr']);
        include $GLOBALS['xoops']->path('footer.php');
    }

    $catid = (0 + $_POST["type"]);
    if (!is_valid_id($catid)) {
    	include $GLOBALS['xoops']->path('header.php');
        stderr($GLOBALS['lang']['takeupload_failed'], $GLOBALS['lang']['takeupload_no_cat']);
        include $GLOBALS['xoops']->path('footer.php');
    }
      
    if (!validfilename($fname)) {
    	include $GLOBALS['xoops']->path('header.php');
		stderr($GLOBALS['lang']['takeupload_failed'], $GLOBALS['lang']['takeupload_invalid']);
		include $GLOBALS['xoops']->path('footer.php');
    }
    if (!preg_match('/^(.+)\.torrent$/si', $fname, $matches)) {
    	include $GLOBALS['xoops']->path('header.php');
	    stderr($GLOBALS['lang']['takeupload_failed'], $GLOBALS['lang']['takeupload_not_torrent']);
	    include $GLOBALS['xoops']->path('footer.php');
    }
    $shortfname = $torrent = $matches[1];
    if (!empty($_POST["name"]))
      $torrent = unesc($_POST["name"]);

    $tmpname = $f["tmp_name"];
    if (!is_uploaded_file($tmpname))
      stderr($GLOBALS['lang']['takeupload_failed'], $GLOBALS['lang']['takeupload_eek']);
    if (!filesize($tmpname)){
    	include $GLOBALS['xoops']->path('header.php');
        stderr($GLOBALS['lang']['takeupload_failed'], $GLOBALS['lang']['takeupload_no_file']);
        include $GLOBALS['xoops']->path('footer.php');
     }

    $dict = bdec_file($tmpname, $GLOBALS['TBDEV']['max_torrent_size']);
    if (!isset($dict)) {
    	include $GLOBALS['xoops']->path('header.php');
    	stderr($GLOBALS['lang']['takeupload_failed'], $GLOBALS['lang']['takeupload_not_benc']);
    	include $GLOBALS['xoops']->path('footer.php');
    }


    function dict_check($d, $s) {
      if ($d["type"] != "dictionary") {
      	include $GLOBALS['xoops']->path('header.php');
        stderr($GLOBALS['lang']['takeupload_failed'], $GLOBALS['lang']['takeupload_not_dict']);
        include $GLOBALS['xoops']->path('footer.php');
      }
      $a = explode(":", $s);
      $dd = $d["value"];
      $ret = array();
      $t='';
      foreach ($a as $k) {
        unset($t);
        if (preg_match('/^(.*)\((.*)\)$/', $k, $m)) {
          $k = $m[1];
          $t = $m[2];
        }
        if (!isset($dd[$k])) {
        	include $GLOBALS['xoops']->path('header.php');
            stderr($GLOBALS['lang']['takeupload_failed'], $GLOBALS['lang']['takeupload_no_keys']);
            include $GLOBALS['xoops']->path('footer.php');
        }
        if (isset($t)) {
          if ($dd[$k]["type"] != $t) {
          	include $GLOBALS['xoops']->path('header.php');
            stderr($GLOBALS['lang']['takeupload_failed'], $GLOBALS['lang']['takeupload_invalid_entry']);
            include $GLOBALS['xoops']->path('footer.php');
          }
          $ret[] = $dd[$k]["value"];
        }
        else
          $ret[] = $dd[$k];
      }
      return $ret;
    }

    function dict_get($d, $k, $t) {
      if ($d["type"] != "dictionary") {
      	include $GLOBALS['xoops']->path('header.php');
        stderr($GLOBALS['lang']['takeupload_failed'], $GLOBALS['lang']['takeupload_not_dict']);
        include $GLOBALS['xoops']->path('footer.php');
      }
      $dd = $d["value"];
      if (!isset($dd[$k]))
        return;
      $v = $dd[$k];
      if ($v["type"] != $t) {
      	include $GLOBALS['xoops']->path('header.php');
      	stderr($GLOBALS['lang']['takeupload_failed'], $GLOBALS['lang']['takeupload_dict_type']);
      	include $GLOBALS['xoops']->path('footer.php');
      }
      return $v["value"];
    }

    function extract_tracker_list($al, $ret = array(), $key = '', $depth=0) {
    	foreach($al as $key => $value)
    		if ($key=='type'&&$value=='string') {
    			$ret[] = $al['value'];	
    		} elseif ($key=='type'&&$value=='list') {
    			foreach($al['value'] as $keyb => $valueb) {
	    			$depth++;
	    			$ret = extract_tracker_list($al['value'][$keyb], $ret, $key, $depth);
	    			$depth--;
    			}
    		}
    	return $ret;
    }
    
    function exist_insert_tracker($tracker, $torrent_id) {
    	if (empty($tracker))
    		return false;
    	$res = $GLOBALS['xoopsDB']->queryF("SELECT `id` FROM ".$GLOBALS['xoopsDB']->prefix("tb_trackers")." WHERE `tracker` = '".$tracker."'");
    	if ($GLOBALS['xoopsDB']->getRowsNum($res)==0) {
    		$res = $GLOBALS['xoopsDB']->queryF("INSERT INTO ".$GLOBALS['xoopsDB']->prefix("tb_trackers")." (added, tracker) VALUES ('".time()."',".sqlesc($tracker).")");
    		$tracker_id = $GLOBALS['xoopsDB']->getInsertId();
    	} else {
    		list($tracker_id) = $GLOBALS['xoopsDB']->fetchRow($res);
    	}
    	$res = $GLOBALS['xoopsDB']->queryF("DELETE FROM ".$GLOBALS['xoopsDB']->prefix("tb_trackers_to_torrents")." WHERE `torrent_id` = ".$torrent_id." AND `tracker_id` = ".$tracker_id);
    	$res = $GLOBALS['xoopsDB']->queryF("INSERT INTO ".$GLOBALS['xoopsDB']->prefix("tb_trackers_to_torrents")." (tracker_id, torrent_id) VALUES ('".$tracker_id."',".sqlesc($torrent_id).")");    		
    }
    
    if ($GLOBALS['TBDEV']['super_torrents']) {
    	$trackers = extract_tracker_list($dict['value']['announce-list'], array(), '' ,0);
    }
    
    list($ann, $info) = dict_check($dict, "announce(string):info");

    
    $tmaker = (isset($dict['value']['created by']) && !empty($dict['value']['created by']['value'])) ? sqlesc($dict['value']['created by']['value']) : sqlesc($GLOBALS['lang']['takeupload_unkown']);

    unset($dict);

    list($dname, $plen, $pieces) = dict_check($info, "name(string):piece length(integer):pieces(string)");
	;

	if (!$GLOBALS['TBDEV']['super_torrents']&&!in_array($ann, $GLOBALS['TBDEV']['announce_urls'], 1)) {
    	include $GLOBALS['xoops']->path('header.php');
		stderr($GLOBALS['lang']['takeupload_failed'], sprintf($GLOBALS['lang']['takeupload_url'], $GLOBALS['TBDEV']['announce_urls'][0]));
     	include $GLOBALS['xoops']->path('footer.php');
    } 
    
    if (strlen($pieces) % 20 != 0) {
    	
    	include $GLOBALS['xoops']->path('header.php');
      	stderr($GLOBALS['lang']['takeupload_failed'], $GLOBALS['lang']['takeupload_pieces']);
      	include $GLOBALS['xoops']->path('footer.php');
    }

    $filelist = array();
    $totallen = dict_get($info, "length", "integer");
    if (isset($totallen)) {
      $filelist[] = array($dname, $totallen);
      $type = "single";
    }
    else {
      $flist = dict_get($info, "files", "list");
      if (!isset($flist)) {
      	include $GLOBALS['xoops']->path('header.php');
        stderr($GLOBALS['lang']['takeupload_failed'], $GLOBALS['lang']['takeupload_both']);
        include $GLOBALS['xoops']->path('footer.php');
      }
      if (!count($flist)) {
      	include $GLOBALS['xoops']->path('header.php');
        stderr($GLOBALS['lang']['takeupload_failed'], $GLOBALS['lang']['takeupload_no_files']);
        include $GLOBALS['xoops']->path('footer.php');
      }
      $totallen = 0;
      foreach ($flist as $fn) {
        list($ll, $ff) = dict_check($fn, "length(integer):path(list)");
        $totallen += $ll;
        $ffa = array();
        foreach ($ff as $ffe) {
          if ($ffe["type"] != "string")
            stderr($GLOBALS['lang']['takeupload_failed'], $GLOBALS['lang']['takeupload_error']);
          $ffa[] = $ffe["value"];
        }
        if (!count($ffa)){
        	include $GLOBALS['xoops']->path('header.php');
          	stderr($GLOBALS['lang']['takeupload_failed'], $GLOBALS['lang']['takeupload_error']);
          	include $GLOBALS['xoops']->path('footer.php');
        }
        $ffe = implode("/", $ffa);
        $filelist[] = array($ffe, $ll);
      }
      $type = "multi";
    }


    //$infohash = pack("H*", sha1($info["string"]));
    $infohash = sha1($info["string"]);

    unset($info);
    // Replace punctuation characters with spaces

    $torrent = str_replace("_", " ", $torrent);


    $ret = $GLOBALS['xoopsDB']->queryF("INSERT INTO ".$GLOBALS['xoopsDB']->prefix("tb_torrents")." (search_text, filename, owner, visible, info_hash, name, size, numfiles, type, descr, ori_descr, category, save_as, added, last_action, nfo, client_created_by) VALUES (" .
        implode(",", array_map("sqlesc", array(searchfield("$shortfname $dname $torrent"), $fname, $GLOBALS['CURUSER']["id"], "no", $infohash, $torrent, $totallen, count($filelist), $type, $descr, $descr, 0 + $_POST["type"], $dname))) .
        ", " . time() . ", " . time() . ", $nfo, $tmaker)");
    if (!$ret) {
      if (mysql_errno() == 1062)
      	include $GLOBALS['xoops']->path('header.php');
        stderr($GLOBALS['lang']['takeupload_failed'], $GLOBALS['lang']['takeupload_already']);
      	stderr($GLOBALS['lang']['takeupload_failed'], "mysql puked: ".mysql_error());
      	include $GLOBALS['xoops']->path('footer.php');
    }
    $id = mysql_insert_id();

    foreach($trackers as $tracker) {
    	exist_insert_tracker($tracker, $id);
	}
    exist_insert_tracker($ann, $id);
    
    @$GLOBALS['xoopsDB']->queryF("DELETE FROM ".$GLOBALS['xoopsDB']->prefix("tb_files")." WHERE torrent = $id");
   
    function file_list($arr,$id)
    {
        foreach($arr as $v)
            $new[] = "($id,".sqlesc($v[0]).",".$v[1].")";
        return join(",",$new);
    }

    $GLOBALS['xoopsDB']->queryF("INSERT INTO ".$GLOBALS['xoopsDB']->prefix("tb_files")." (torrent, filename, size) VALUES ".file_list($filelist,$id));

    if (!is_dir($GLOBALS['TBDEV']['torrent_dir']))
	    foreach(explode(DS, $GLOBALS['TBDEV']['torrent_dir']) as $folder) {
	    	$path .= DS . $folder;
	    	mkdir($path, 0777);
	    }
    
    move_uploaded_file($tmpname, "{$GLOBALS['TBDEV']['torrent_dir']}/$id.torrent");
    
    if (!file_exists("{$GLOBALS['TBDEV']['torrent_dir']}/$id.torrent")) {
    	@$GLOBALS['xoopsDB']->queryF("DELETE FROM ".$GLOBALS['xoopsDB']->prefix("tb_files")." WHERE torrent = $id");
    	@$GLOBALS['xoopsDB']->queryF("DELETE FROM ".$GLOBALS['xoopsDB']->prefix("tb_trackers_to_torrents")." WHERE torrent_id = $id");
    	@$GLOBALS['xoopsDB']->queryF("DELETE FROM ".$GLOBALS['xoopsDB']->prefix("tb_torrents")." WHERE id = $id");
		unlink($tmpname);
    	include $GLOBALS['xoops']->path('header.php');
        xoops_error('File was unable to be moved, check that path "'.$GLOBALS['TBDEV']['torrent_dir'].'/" exists and there is enough room on the drive!', 'File failed movement');
        include $GLOBALS['xoops']->path('footer.php');
        exit(0);
    }

   write_log(sprintf($GLOBALS['lang']['takeupload_log'], $id, $torrent, $GLOBALS['CURUSER']['username']));


    /* RSS feeds */

    if (($fd1 = @fopen("rss.xml", "w")) && ($fd2 = fopen("rssdd.xml", "w")))
    {
      $cats = "";
      $res = $GLOBALS['xoopsDB']->queryF("SELECT id, name FROM categories");
      while ($arr = $GLOBALS['xoopsDB']->fetchArray($res))
        $cats[$arr["id"]] = $arr["name"];
      $s = "<?xml version=\"1.0\" encoding=\"iso-8859-1\" ?>\n<rss version=\"0.91\">\n<channel>\n" .
        "<title>{$GLOBALS['TBDEV']['site_name']}</title>\n<description>TBDev is the best!</description>\n<link>{$GLOBALS['TBDEV']['baseurl']}/</link>\n";
      @fwrite($fd1, $s);
      @fwrite($fd2, $s);
      $r = $GLOBALS['xoopsDB']->queryF("SELECT id,name,descr,filename,category FROM ".$GLOBALS['xoopsDB']->prefix("tb_torrents")." ORDER BY added DESC LIMIT 15") or sqlerr(__FILE__, __LINE__);
      while ($a = $GLOBALS['xoopsDB']->fetchArray($r))
      {
        $cat = $cats[$a["category"]];
        $s = "<item>\n<title>" . htmlspecialchars($a["name"] . " ($cat)") . "</title>\n" .
          "<description>" . htmlspecialchars($a["descr"]) . "</description>\n";
        @fwrite($fd1, $s);
        @fwrite($fd2, $s);
        @fwrite($fd1, "<link>{$GLOBALS['TBDEV']['baseurl']}/details.php?id=$a[id]&amp;hit=1</link>\n</item>\n");
        $filename = htmlspecialchars($a["filename"]);
        @fwrite($fd2, "<link>{$GLOBALS['TBDEV']['baseurl']}/download.php/$a[id]/$filename</link>\n</item>\n");
      }
      $s = "</channel>\n</rss>\n";
      @fwrite($fd1, $s);
      @fwrite($fd2, $s);
      @fclose($fd1);
      @fclose($fd2);
    }

    /* Email notifs */
    /*******************

    $res = $GLOBALS['xoopsDB']->queryF("SELECT name FROM ".$GLOBALS['xoopsDB']->prefix("tb_categories")." WHERE id=$catid") or sqlerr();
    $arr = $GLOBALS['xoopsDB']->fetchArray($res);
    $cat = $arr["name"];
    $res = $GLOBALS['xoopsDB']->queryF("SELECT email FROM ".$GLOBALS['xoopsDB']->prefix("tb_users")." WHERE enabled='yes' AND notifs LIKE '%[cat$catid]%'") or sqlerr();
    $uploader = $GLOBALS['CURUSER']['username'];

    $size = mksize($totallen);
    $description = ($html ? strip_tags($descr) : $descr);

    $body = <<<EOD
A new torrent has been uploaded.

Name: $torrent
Size: $size
Category: $cat
Uploaded by: $uploader

Description
-------------------------------------------------------------------------------
$description
-------------------------------------------------------------------------------

You can use the URL below to download the torrent (you may have to login).

{$GLOBALS['TBDEV']['baseurl']}/details.php?id=$id&hit=1

-- 
{$GLOBALS['TBDEV']['site_name']}
EOD;

    $to = "";
    $nmax = 100; // Max recipients per message
    $nthis = 0;
    $ntotal = 0;
    $total = mysql_num_rows($res);
    while ($arr = mysql_fetch_row($res))
    {
      if ($nthis == 0)
        $to = $arr[0];
      else
        $to .= "," . $arr[0];
      ++$nthis;
      ++$ntotal;
      if ($nthis == $nmax || $ntotal == $total)
      {
        if (!mail("Multiple recipients <{$GLOBALS['TBDEV']['site_email']}>", "New torrent - $torrent", $body,
        "From: {$GLOBALS['TBDEV']['site_email']}\r\nBcc: $to"))
        stderr("Error", "Your torrent has been been uploaded. DO NOT RELOAD THE PAGE!\n" .
          "There was however a problem delivering the e-mail notifcations.\n" .
          "Please let an administrator know about this error!\n");
        $nthis = 0;
      }
    }
    *******************/

    header("Location: {$GLOBALS['TBDEV']['baseurl']}/details.php?id=$id&uploaded=1");

?>
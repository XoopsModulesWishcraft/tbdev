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
	$GLOBALS['xoopsLogger']->activated = false;
	
	loggedinorreturn();

	$GLOBALS['lang'] = array_merge( $GLOBALS['lang'], load_language('download') );
	$id = isset($_GET['torrent']) ? intval($_GET['torrent']) : 0;
	if ( !is_valid_id($id) ) {
		stdhead($title, '', '', 0);
	    stderr("{$GLOBALS['lang']['download_user_error']}", "{$GLOBALS['lang']['download_no_id']}");
	    stdfoot();
	}

	$torrents_handler = xoops_getmodulehandler('torrents', 'td');
	$torrent = $torrents_handler->get($id);
  
  	$fn = "{$GLOBALS['TBDEV']['torrent_dir']}/$id.torrent";
  
  	if (!is_object($torrent) || !is_file($fn) || !is_readable($fn))
    	httperr();

	$torrent->setVar('hits', $torrent->getVar('hits')+1);
    
  	if (!isset($GLOBALS['CURUSER']['passkey']) || strlen($GLOBALS['CURUSER']['passkey']) != 32)  {
    	$GLOBALS['CURUSER']['passkey'] = md5($GLOBALS['CURUSER']['username'].time().$GLOBALS['CURUSER']['passhash'].XOOPS_LICENSE_KEY);
    	@$GLOBALS['xoopsDB']->queryF("UPDATE ".$GLOBALS['xoopsDB']->prefix("tb_users")." SET passkey='{$GLOBALS['CURUSER']['passkey']}' WHERE id={$GLOBALS['CURUSER']['id']}");
  	}

	$dict = $torrents_handler->bdec_file($fn, filesize($fn));
	$dict['value']['announce']['value'] = "{$GLOBALS['TBDEV']['announce_urls'][0]}?passkey={$GLOBALS['CURUSER']['passkey']}";
	$dict['value']['announce']['string'] = strlen($dict['value']['announce']['value']).":".$dict['value']['announce']['value'];
	$dict['value']['announce']['strlen'] = strlen($dict['value']['announce']['string']);
   
  	if ($GLOBALS['TBDEV']['super_torrents']&&$torrent->getVar('visible')=='yes') {
  		$trackers_handler = xoops_getmodulehandler('trackers', 'td');
  		$i=0;
 		$al[$i]['type'] = 'string'; 
		$al[$i]['value'] = $dict['value']['announce']['value'];
		$al[$i]['string'] = $dict['value']['announce']['string'];
		$al[$i]['strlen'] = $dict['value']['announce']['strlen'];
		$list[0] .= $al[$i]['string'];
		$i++;
  		foreach($trackers_handler->recommendTrackers($id) as $tracker) {
  			$al[$i]['type'] = 'string'; 
  			$al[$i]['value'] = $tracker;
  			$al[$i]['string'] = strlen($tracker->getVar('tracker')).":".$tracker->getVar('tracker');
	  		$al[$i]['strlen'] = strlen($al[$i]['string']);
  			$list[0] .= $al[$i]['string'];
  			$i++;
  		}
  		$list[0] = 'l'.$list[0].'e';
  		$list[1] = 'l'.$list[0].'e';
  		$dict['value']['announce-list'] = array('type'=>'list', 'value' => array(array('type'=>'list', 'value'=>$al, 'strlen' => strlen($list[0]), 'string' => $list[0])), 'strlen' => strlen($list[1]), 'string' => $list[1]);
  	}
  
  	$torrents_handler->insert($torrent);
    header('Content-Disposition: attachment; filename="'.$torrent->getVar('filename').'"');
  	header("Content-Type: application/x-bittorrent");

	print($torrents_handler->benc($dict));



?>
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
error_reporting(0);
require_once 'header.php';
$GLOBALS['xoopsLogger']->activated = false;

$users_handler = xoops_getmodulehandler('users', 'td');
$torrents_handler = xoops_getmodulehandler('torrents', 'td');
$peers_handler = xoops_getmodulehandler('peers', 'td');

xoops_loadLanguage('errors', 'tb');

define ('UC_VIP', 2);

// DO NOT EDIT BELOW UNLESS YOU KNOW WHAT YOU'RE DOING!!

$agent = $_SERVER["HTTP_USER_AGENT"];

// Deny access made with a browser...
foreach(explode('|', $GLOBALS['TBDEV']['agents_denied']) as $agentregex){
	if (ereg($agentregex, $agent)) {
	    $torrents_handler->err("torrent not registered with this tracker CODE 1");
	}
}
foreach(explode('|', $GLOBALS['TBDEV']['server_denied']) as $serverregex){
	if (isset($_SERVER[$serverregex])) {
	    $torrents_handler->err(_TBDEV_ERR_TRACKER_DENY);
	}
}

function portblacklisted($port)
{
	foreach(explode('|', $GLOBALS['TBDEV']['ports_blacklisted']) as $portsblacklisted) {
		$ports = explode('-', $portsblacklisted);
		if (isset($ports[1])) {
			if ($port >= $ports[0] && $port <= $ports[1]) return true;
		} elseif (isset($ports[0])) { 
			if ($port == $ports[0]) return true;
		}
	}
	return false;
}
/////////////////////// FUNCTION DEFS END ///////////////////////////////

$parts = array();
$pattern = '[0-9a-fA-F]{32}';
if( !isset($_GET['passkey']) OR !ereg($pattern, $_GET['passkey'], $parts) ) 
		$torrents_handler->err(_TBDEV_ERR_TRACKER_PASSKEY);
	else
		$GLOBALS['passkey'] = $parts[0];
		
foreach (array("info_hash","peer_id","event","ip","localip") as $x) {
	if(isset($_GET["$x"]))
		$GLOBALS[$x] = "" . $_GET[$x];
	}

foreach (array("port","downloaded","uploaded","left") as $x) {
	$GLOBALS[$x] = 0 + $_GET[$x];
}

foreach (array("passkey","info_hash","peer_id","port","downloaded","uploaded","left") as $x)
	if (!isset($x)) $torrents_handler->err(sprintf(_TBDEV_ERR_TRACKER_MISSINGKEY, $x));

foreach (array("info_hash","peer_id") as $x)
	if (strlen($GLOBALS[$x]) != 20) 
		$torrents_handler->err(sprintf(_TBDEV_ERR_TRACKER_VARIABLE, $x, strlen($GLOBALS[$x]), urlencode($GLOBALS[$x])));

unset($x);

$info_hash = bin2hex($info_hash);

$ip = $_SERVER['REMOTE_ADDR'];

$port = 0 + $port;
$downloaded = 0 + $downloaded;
$uploaded = 0 + $uploaded;
$left = 0 + $left;

$rsize = 50;
foreach(array("num want", "numwant", "num_want") as $k)
{
	if (isset($_GET[$k]))
	{
		$rsize = 0 + $_GET[$k];
		break;
	}
}


if (!$port || $port > 0xffff)
	$torrents_handler->err(_TBDEV_ERR_TRACKER_INVALIDPORT);

if (!isset($event))
	$event = "";

$seeder = ($left == 0) ? "yes" : "no";

$criteria = new CriteriaCompo(new Criteria('passkey', $passkey));
if ( $users_handler->getCount($criteria) != 1 )
	$torrents_handler->err(sprintf(_TBDEV_ERR_TRACKER_UNKNOWNPASSKEY,$GLOBALS['TBDEV']['baseurl']));
 
$users = $users_handler->getObjects($criteria, false);

if( $users[0]->getVar('enabled') == 'no' ) 
	$torrents_handler->err(_TBDEV_ERR_TRACKER_PERMDENIED);
	

$criteria = new CriteriaCompo(new Criteria('info_hash', $info_hash));
//$res = $GLOBALS['xoopsDB']->queryF("SELECT id, banned, seeders + leechers AS numpeers, added AS ts FROM ".$GLOBALS['xoopsDB']->prefix("tb_torrents")." WHERE info_hash = " .sqlesc($info_hash));//" . hash_where("info_hash", $info_hash));
$torrents = $torrents_handler->getObjects($criteria, false);
if (!is_object($torrents[0]))
	$torrents_handler->err(_TBDEV_ERR_TRACKER_NOTTORRENT);

$torrentid = $torrents[0]->getVar("id");
$fields = "seeder, peer_id, ip, port, uploaded, downloaded, userid";
$numpeers = $torrents[0]->getVar("seeders")+$torrents[0]->getVar("leechers");

$criteria = new CriteriaCompo(new Criteria('torrent', $torrentid));
$criteria->add(new Criteria('connectable', 'yes'));
if ($numpeers > $rsize) {
	$criteria->setSort('RAND()');
	$criteria->setOrder('DESC');
	$criteria->settLimit($rsize);
}
$peers = $peers_handler->getObjects($criteria, false);

if($_GET['compact'] != 1){
	$resp = "d" . $torrents_handler->benc_str("interval") . "i" . $GLOBALS['TBDEV']['announce_interval'] . "e" . $torrents_handler->benc_str("peers") . "l";
} else {
	$resp = "d" . $torrents_handler->benc_str("interval") . "i" . $GLOBALS['TBDEV']['announce_interval'] ."e" . $torrents_handler->benc_str("min interval") . "i" . 300 ."e5:"."peers" ;
}

$peer = array();

$peer_num = 0;
foreach ($peers as $key => $peerobj) {
	if($_GET['compact'] != 1) {
		if (str_pad($peerobj->getVar("peer_id"), 20) === $peer_id) {
			$self = $peerobj;
			 continue;
		}
		$resp .= "d" . $torrents_handler->benc_str("ip") . $torrents_handler->benc_str($peerobj->getVar("ip"));
        if (!$_GET['no_peer_id']) {
			$resp .= $torrents_handler->benc_str("peer id") . $torrents_handler->benc_str($peerobj->getVar("peer_id"));
		}
		$resp .= $torrents_handler->benc_str("port") . "i" . $peerobj->getVar("port") . "e" . "e";
    } else {
        $peer_ip = explode('.', $peerobj->getVar("ip"));
		$peer_ip = pack("C*", $peer_ip[0], $peer_ip[1], $peer_ip[2], $peer_ip[3]);
		$peer_port = pack("n*", (int)$peerobj->getVar("port"));
		$time = intval((time() % 7680) / 60);
		if($_GET['left'] == 0) {
			$time += 128;
		}
		$time = pack("C", $time);
	   	$peer[] = $time . $peer_ip . $peer_port;
		$peer_num++;
    }
}

if ($_GET['compact']!=1)
	$resp .= "ee";
else {
	$o = "";
	for($i=0;$i<$peer_num;$i++)
	{
		$o .= substr($peer[$i], 1, 6);
	}
	$resp .= strlen($o) . ':' . $o . 'e';
}

$criteria = new CriteriaCompo(new Criteria('torrent', $torrentid));
$criteria->add(new Criteria('peer_id', $peer_id));

//////////////////////////// END NEW COMPACT MODE////////////////////////////////

if (!isset($self)) {
	$peers = $peers_handler->getObjects($criteria, false);
	if (is_object($peers[0])) {
		$userid = $peers[0]->getVar("userid");
		$self = $peers[0];
	}
}

//// Up/down stats ////////////////////////////////////////////////////////////



if (!isset($self)) {
	
	$criteria = new CriteriaCompo(new Criteria('torrent', $torrentid));
	$criteria->add(new Criteria('passkey', $passkey));
	
	if ($peers_handler->getCount($criteria) >= 1 && $seeder == 'no') 
		$torrents_handler->err(_TBDEV_ERR_TRACKER_LIMITDONE);

	if ($peers_handler->getCount($criteria) >= 3 && $seeder == 'yes') 
		$torrents_handler->err(_TBDEV_ERR_TRACKER_LIMITEXCEEDED);

	if ($left > 0 && $users[0]->getVar('class') < UC_VIP && $GLOBALS['TBDEV']['user_ratios'])
	{
		$gigs = $users[0]->getVar('uploaded') / (1024*1024*1024);
		$elapsed = floor((time() - $torrents[0]->getVar("ts")) / 3600);
		$ratio = (($users[0]->getVar('downloaded') > 0) ? ($users[0]->getVar('uploaded') / $users[0]->getVar('downloaded')) : 1);
		if ($ratio < 0.5 || $gigs < 5) $wait = 48;
		elseif ($ratio < 0.65 || $gigs < 6.5) $wait = 24;
		elseif ($ratio < 0.8 || $gigs < 8) $wait = 12;
		elseif ($ratio < 0.95 || $gigs < 9.5) $wait = 6;
		else $wait = 0;
		if ($elapsed < $wait)
				$torrents_handler->err(sprintf(_TBDEV_ERR_TRACKER_NOTAUTHORISED, ($wait - $elapsed)));
	}
} else {
	$upthis = max(0, $uploaded - $self->getVar("uploaded"));
	$downthis = max(0, $downloaded - $self->getVar("downloaded"));

	if ($upthis > 0 || $downthis > 0) {
		$users[0]->setVar('uploaded', $users[0]->getVar('uploaded')+$upthis);
		$users[0]->setVar('downloaded', $users[0]->getVar('downloaded')+$downthis);
		$users_handler->insert($users[0], true);
	}
}

///////////////////////////////////////////////////////////////////////////////


$updateset = array();

if ($event == "stopped")
{
	if (isset($self))
	{
		$criteria = new CriteriaCompo(new Criteria('torrent', $torrentid));
		$criteria->add(new Criteria('peer_id', $peer_id));
		if ($peers_handler->deleteAll($criteria))
		{
			if ($self["seeder"] == "yes")
				$torrents[0]->setVar('seeders', $torrents[0]->getVar('seeders')-1);
			else
				$torrents[0]->setVar('leechers', $torrents[0]->getVar('leechers')-1);
		}
	}
} else {
	if ($event == "completed")
		$torrents[0]->setVar('times_completed', $torrents[0]->getVar('times_completed')+1);
		
	if (isset($self))
	{
		$self->setVar('uploaded', $uploaded);
		$self->setVar('downloaded', $downloaded);
		$self->setVar('to_go', $left);
		$self->setVar('last_action', time());
		$self->setVar('seeder', $seeder);
		if ($seeder == "yes" && $self->getVar("seeder") != $seeder )
			$self->setVar('finishedat', time());
		 
		if ($peers_handler->insert($self, true) && $self->getVar("seeder") != $seeder) {
			if ($seeder == "yes") {
				$torrents[0]->setVar('seeders', $torrents[0]->getVar('seeders')+1);
				$torrents[0]->setVar('leechers', $torrents[0]->getVar('leechers')-1);
			} else {
				$torrents[0]->setVar('seeders', $torrents[0]->getVar('seeders')-1);
				$torrents[0]->setVar('leechers', $torrents[0]->getVar('leechers')+1);
			}
		}
	} else {
		if ($event != "started")
			$torrents_handler->err(sprintf(_TBDEV_ERR_TRACKER_PEERNOTFOUND, $passkey));

		if (portblacklisted($port))	{
			$torrents_handler->err(sprintf(_TBDEV_ERR_TRACKER_PORTBLACKLISTED, $port));
		} elseif ( $GLOBALS['TBDEV']['connectable_check'] ) {
			$sockres = @fsockopen($ip, $port, $errno, $errstr, 5);
			if (!$sockres)
				$connectable = "no";
			else
			{
				$connectable = "yes";
				@fclose($sockres);
			}
		} else {
			$connectable = 'yes';
		}

		$newpeer = $peers_handler->create();
		$newpeer->setVar('connectable', $connectable);
		$newpeer->setVar('torrent', $torrentid);
		$newpeer->setVar('peer_id', $peer_id);
		$newpeer->setVar('ip', $ip);
		$newpeer->setVar('port', $port);
		$newpeer->setVar('uploaded', $uploaded);
		$newpeer->setVar('downloaded', $downloaded);
		$newpeer->setVar('to_go', $left);
		$newpeer->setVar('started', time());
		$newpeer->setVar('last_action', time());
		$newpeer->setVar('seeder', $seeder);
		$newpeer->setVar('userid', $user[0]->getVar('id'));
		$newpeer->setVar('agent', $agent);
		$newpeer->setVar('passkey', $passkey);
		
		if ($peers_handler->insert($newpeer, true))
		{
			if ($seeder == "yes")
				$torrents[0]->setVar('seeders', $torrents[0]->getVar('seeders')+1);
			else
				$torrents[0]->setVar('leechers', $torrents[0]->getVar('leechers')+1);
		}
	}
}

if ($seeder == "yes")
{
	if ($torrents[0]->getVar("banned") != "yes")
		$torrents[0]->setVar('visible', 'yes');
	$torrents[0]->setVar('last_action', time()); 
}

$torrents_handler->insert($torrents[0], true);

$torrents_handler->benc_resp_raw($resp);

?>
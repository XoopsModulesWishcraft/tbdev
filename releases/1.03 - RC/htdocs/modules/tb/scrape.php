<?php
/*
+------------------------------------------------
|   TBDev.net BitTorrent Tracker PHP
|   =============================================
|   by CoLdFuSiOn
|   (c) 2003 - 2010 TBDev.Net
|   http://www.tbdev.net
|   =============================================
|   svn: http://sourceforge.net/projects/tbdevnet/
|   Licence Info: GPL
+------------------------------------------------
|   $Date: 2009-09-05 18:48:06 +0100 (Sat, 05 Sep 2009) $
|   $Revision: 199 $
|   $Author: tbdevnet $
|   $URL: https://tbdevnet.svn.sourceforge.net/svnroot/tbdevnet/trunk/TB/scrape.php $
+------------------------------------------------
*/
require_once '../../mainfile.php';
$GLOBALS['xoopsLogger']->activated = false;
require_once("include/config.php");

    if ( !isset($_GET['info_hash']) OR (strlen($_GET['info_hash']) != 20) )
      error('Invalid hash');

    $res = @$GLOBALS['xoopsDB']->queryF( "SELECT info_hash, seeders, leechers, times_completed FROM ".$GLOBALS['xoopsDB']->prefix("torrents")." WHERE " . hash_where( $_GET['info_hash']) );
    
    if( !mysql_num_rows($res) )
      error('No torrent with that hash found');
    
    $benc = 'd5:files';

    while ($row = $GLOBALS['xoopsDB']->fetchArray($res))
    {
      $benc .= 'd20:'.pack('H*', $row['info_hash'])."d8:completei{$row['seeders']}e10:downloadedi{$row['times_completed']}e10:incompletei{$row['leechers']}eee";
    }

    $benc .= 'ed5:flagsd20:min_request_intervali1800eee';

    header('Content-Type: text/plain; charset=UTF-8');
    header('Pragma: no-cache');
    print($benc);


function error($err){

    header('Content-Type: text/plain; charset=UTF-8');
    header('Pragma: no-cache');
    exit("d14:failure reason".strlen($err).":{$err}ed5:flagsd20:min_request_intervali1800eeee");
    
}

function hash_where($hash) {

    return "info_hash = '" . mysql_real_escape_string( bin2hex($hash) ) . "'";

}
?>
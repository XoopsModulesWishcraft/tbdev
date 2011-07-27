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
require_once "include/bittorrent.php";
require_once "include/user_functions.php";
require_once "include/html_functions.php";

dbconn(false);

loggedinorreturn();

    $GLOBALS['lang'] = array_merge( load_language('global'), load_language('upload') );
    
    $HTMLOUT = '';

    if ($GLOBALS['CURUSER']['class'] < UC_UPLOADER)
    {
        stderr($GLOBALS['lang']['upload_sorry'], $GLOBALS['lang']['upload_no_auth']);
    }


    $HTMLOUT .= "<div align='center'>
    <form enctype='multipart/form-data' action='takeupload.php' method='post'>
    <input type='hidden' name='MAX_FILE_SIZE' value='{$GLOBALS['TBDEV']['max_torrent_size']}' />
    <p>{$GLOBALS['lang']['upload_announce_url']} <b>{$GLOBALS['TBDEV']['announce_urls'][0]}</b></p>";


    $HTMLOUT .= "<table border='1' cellspacing='0' cellpadding='10'>
    <tr>
      <td class='heading' valign='top' align='right'>{$GLOBALS['lang']['upload_torrent']}</td>
      <td valign='top' align='left'><input type='file' name='file' size='80' /></td>
    </tr>
    <tr>
      <td class='heading' valign='top' align='right'>{$GLOBALS['lang']['upload_name']}</td>
      <td valign='top' align='left'><input type='text' name='name' size='80' /><br />({$GLOBALS['lang']['upload_filename']})</td>
    </tr>
    <tr>
      <td class='heading' valign='top' align='right'>{$GLOBALS['lang']['upload_nfo']}</td>
      <td valign='top' align='left'><input type='file' name='nfo' size='80' /><br />({$GLOBALS['lang']['upload_nfo_info']})</td>
    </tr>
    <tr>
      <td class='heading' valign='top' align='right'>{$GLOBALS['lang']['upload_description']}</td>
      <td valign='top' align='left'><textarea name='descr' rows='10' cols='80'></textarea>
      <br />({$GLOBALS['lang']['upload_html_bbcode']})</td>
    </tr>";

    $s = "<select name='type'>\n<option value='0'>({$GLOBALS['lang']['upload_choose_one']})</option>\n";

    $cats = genrelist();
    
    foreach ($cats as $row)
    {
      $s .= "<option value='{$row["id"]}'>" . htmlspecialchars($row["name"]) . "</option>\n";
    }
    
    $s .= "</select>\n";
    
    $HTMLOUT .= "<tr>
        <td class='heading' valign='top' align='right'>{$GLOBALS['lang']['upload_type']}</td>
        <td valign='top' align='left'>$s</td>
      </tr>
      <tr>
        <td align='center' colspan='2'><input type='submit' class='btn' value='{$GLOBALS['lang']['upload_submit']}' /></td>
      </tr>
    </table>
    </form>
    </div>";

////////////////////////// HTML OUTPUT //////////////////////////

    print stdhead($GLOBALS['lang']['upload_stdhead']) . $HTMLOUT . stdfoot();

?>
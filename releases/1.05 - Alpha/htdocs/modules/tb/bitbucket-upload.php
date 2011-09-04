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

$GLOBALS['lang'] = array_merge( $GLOBALS['lang'], load_language('bitbucket') );

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
	$file = $_FILES["file"];
	if (!isset($file) || $file["size"] < 1)
		stderr("{$GLOBALS['lang']['bitbucket_failed']}", "{$GLOBALS['lang']['bitbucket_not_received']}");
	if ($file["size"] > $GLOBALS['TBDEV']['bb_upload_size'])
		stderr("{$GLOBALS['lang']['bitbucket_failed']}", "{$GLOBALS['lang']['bitbucket_too_large']}");
	$filename = $file["name"];
	if (strpos($filename, "..") !== false || strpos($filename, "/") !== false)
		stderr("{$GLOBALS['lang']['bitbucket_failed']}", "{$GLOBALS['lang']['bitbucket_bad_name']}");
	$tgtfile = $GLOBALS['TBDEV']['bb_upload_path']."/$filename";
	if (file_exists($tgtfile))
		stderr("{$GLOBALS['lang']['bitbucket_failed']}", "{$GLOBALS['lang']['bitbucket_no_name']}<b>" . htmlspecialchars($filename) . "</b> {$GLOBALS['lang']['bitbucket_exists']}");

	$it = @exif_imagetype($file["tmp_name"]);
	if ($it != IMAGETYPE_GIF && $it != IMAGETYPE_JPEG && $it != IMAGETYPE_PNG)
		stderr("{$GLOBALS['lang']['bitbucket_failed']}", "{$GLOBALS['lang']['bitbucket_not_recognized']}");

	$i = strrpos($filename, ".");
	if ($i !== false)
	{
		$ext = strtolower(substr($filename, $i));
		if (($it == IMAGETYPE_GIF && $ext != ".gif") || ($it == IMAGETYPE_JPEG && $ext != ".jpg") || ($it == IMAGETYPE_PNG && $ext != ".png"))
			stderr("{$GLOBALS['lang']['bitbucket_error']}", "{$GLOBALS['lang']['bitbucket_invalid_extension']}");
	}
	else
		stderr("{$GLOBALS['lang']['bitbucket_error']}", "{$GLOBALS['lang']['bitbucket_need_extension']}");
	move_uploaded_file($file["tmp_name"], $tgtfile) or stderr("{$GLOBALS['lang']['bitbucket_error']}", "{$GLOBALS['lang']['bitbucket_internal_error2']}");
	$url = str_replace(" ", "%20", htmlspecialchars("{$GLOBALS['TBDEV']['baseurl']}/bitbucket/$filename"));
	stderr("{$GLOBALS['lang']['bitbucket_success']}", "{$GLOBALS['lang']['bitbucket_url']}<b><a href=\"$url\">$url</a></b><p><a href='bitbucket-upload.php'>{$GLOBALS['lang']['bitbucket_upload_another']}</a>.");
}
	
$xoopsOption['template_main'] = 'tb_bitbucket_upload.html';
stdhead($title, '', '', 0);
$GLOBALS['xoopsTpl']->assign('xoConfig', $GLOBALS['TBDEV']);
$GLOBALS['xoopsTpl']->assign('bb_upload_size', number_format($GLOBALS['TBDEV']['bb_upload_size']));
stdfoot();

?>
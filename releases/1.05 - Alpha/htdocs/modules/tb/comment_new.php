<?php

/*
Module: Xcenter

Version: 2.01

Description: Multilingual Content Module with tags and lists with search functions

Author: Written by Simon Roberts aka. Wishcraft (simon@chronolabs.coop)

Owner: Chronolabs

License: See /docs - GPL 2.0
*/


include '../../mainfile.php';
include 'include/functions.php';

$com_itemid = isset($_GET['com_itemid']) ? intval($_GET['com_itemid']) : 0;
if ($com_itemid > 0) {
	// Get link title
    $com_replytitle = xcenter_getPageTitle($com_itemid);
    include XOOPS_ROOT_PATH.'/include/comment_new.php';
}
?>

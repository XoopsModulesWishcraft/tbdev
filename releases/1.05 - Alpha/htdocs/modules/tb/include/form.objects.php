<?php
	xoops_load('XoopsFormLoader');
	xoops_load('pagenav');
	
	include('formselectcategory.php');
	include('formselectcountry.php');
	include('formselectlanguage.php');
	include('formselectstylesheets.php');
	include('formselecttimezone.php');

	if (file_exists($GLOBALS['xoops']->path('/modules/tag/include/formtag.php')) && $GLOBALS['xoopsModuleConfig']['support_mod_tags'])
		include_once $GLOBALS['xoops']->path('/modules/tag/include/formtag.php');
?>
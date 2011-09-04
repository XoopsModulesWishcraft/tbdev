<?php

function xoops_module_uninstall_tb(&$module) {

	xoops_loadLanguage('modinfo', 'tb');
	$criteria = new Criteria('group_type', '("'._MI_TBDEV_GROUP_TYPE_POWERUSER.'","'._MI_TBDEV_GROUP_TYPE_UPLOADER.'","'._MI_TBDEV_GROUP_TYPE_VIP.'","'._MI_TBDEV_GROUP_TYPE_MODERATOR.'","'._MI_TBDEV_GROUP_TYPE_SYSOP.'")', 'IN');
	$groups_handler =& xoops_gethandler('group');
	foreach($groups_handler->getObjects($criteria) as $group)
		$groups_handler->delete($group);
	return true;
}
	
?>
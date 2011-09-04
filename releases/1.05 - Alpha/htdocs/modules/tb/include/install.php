<?php

function xoops_module_pre_install_tb(&$module) {

	xoops_loadLanguage('modinfo', 'tb');
	
	$groups_handler =& xoops_gethandler('group');
	$criteria = new Criteria('group_type', _MI_TBDEV_GROUP_TYPE_POWERUSER);
	if (count($groups_handler->getObjects($criteria))==0) {
		$group = $groups_handler->create();
		$group->setVar('name', _MI_TBDEV_GROUP_NAME_POWERUSER);
		$group->setVar('description', _MI_TBDEV_GROUP_DESC_POWERUSER);
		$group->setVar('group_type', _MI_TBDEV_GROUP_TYPE_POWERUSER);
		$groups_handler->insert($group, true);
	}

	$groups_handler =& xoops_gethandler('group');
	$criteria = new Criteria('group_type', _MI_TBDEV_GROUP_TYPE_VIP);
	if (count($groups_handler->getObjects($criteria))==0) {
		$group = $groups_handler->create();
		$group->setVar('name', _MI_TBDEV_GROUP_NAME_VIP);
		$group->setVar('description', _MI_TBDEV_GROUP_DESC_VIP);
		$group->setVar('group_type', _MI_TBDEV_GROUP_TYPE_VIP);
		$groups_handler->insert($group, true);
	}

	$groups_handler =& xoops_gethandler('group');
	$criteria = new Criteria('group_type', _MI_TBDEV_GROUP_TYPE_UPLOADER);
	if (count($groups_handler->getObjects($criteria))==0) {
		$group = $groups_handler->create();
		$group->setVar('name', _MI_TBDEV_GROUP_NAME_UPLOADER);
		$group->setVar('description', _MI_TBDEV_GROUP_DESC_UPLOADER);
		$group->setVar('group_type', _MI_TBDEV_GROUP_TYPE_UPLOADER);
		$groups_handler->insert($group, true);
	}
	
	$groups_handler =& xoops_gethandler('group');
	$criteria = new Criteria('group_type', _MI_TBDEV_GROUP_TYPE_MODERATOR);
	if (count($groups_handler->getObjects($criteria))==0) {
		$group = $groups_handler->create();
		$group->setVar('name', _MI_TBDEV_GROUP_NAME_MODERATOR);
		$group->setVar('description', _MI_TBDEV_GROUP_DESC_MODERATOR);
		$group->setVar('group_type', _MI_TBDEV_GROUP_TYPE_MODERATOR);
		$groups_handler->insert($group, true);
	}
	
	$groups_handler =& xoops_gethandler('group');
	$criteria = new Criteria('group_type', _MI_TBDEV_GROUP_TYPE_ADMINISTRATOR);
	if (count($groups_handler->getObjects($criteria))==0) {
		$group = $groups_handler->create();
		$group->setVar('name', _MI_TBDEV_GROUP_NAME_ADMINISTRATOR);
		$group->setVar('description', _MI_TBDEV_GROUP_DESC_ADMINISTRATOR);
		$group->setVar('group_type', _MI_TBDEV_GROUP_TYPE_ADMINISTRATOR);
		$groups_handler->insert($group, true);
	}
	
	$groups_handler =& xoops_gethandler('group');
	$criteria = new Criteria('group_type', _MI_TBDEV_GROUP_TYPE_SYSOP);
	if (count($groups_handler->getObjects($criteria))==0) {
		$group = $groups_handler->create();
		$group->setVar('name', _MI_TBDEV_GROUP_NAME_SYSOP);
		$group->setVar('description', _MI_TBDEV_GROUP_DESC_SYSOP);
		$group->setVar('group_type', _MI_TBDEV_GROUP_TYPE_SYSOP);
		$groups_handler->insert($group, true);
	}
	return true;
}
	
?>
<?php

defined('XOOPS_ROOT_PATH') or die("XOOPS root path not defined");

class TbTrackers extends XoopsObject
{
    function __construct()
    {
        $this->initVar('id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('added', XOBJ_DTYPE_INT, null, false);
        $this->initVar('tracker', XOBJ_DTYPE_TXTBOX, null, false, 500);
    }

    function TbTrackers()
    {
        $this->__construct();
    }


}

class TbTrackersHandler extends XoopsPersistableObjectHandler
{

    function TbTrackersHandler(&$db)
    {
        $this->__construct($db);
    }

    function __construct(&$db)
    {
        parent::__construct($db, "tb_trackers", 'TbTrackers', "id");
    }

    function recommendTrackers($torrent_id) {
    	$trackerstotorrents_handler = xoops_getmodulehandler('trackerstotorrents', 'tb');
    	$criteria = new CriteriaCompo(new Criteria('torrent_id', $torrent_id));
    	$criteria->add(new Criteria('seeders', '0', '>'));
    	foreach($trackerstotorrents_handler->getObjects($criteria) as $object) {
    		$tracker[$object->getVar('tracker_id')] = $object->getVar('tracker_id');
    	}
    	$criteria = new Criteria('id', '('.implode(',',$tracker).')', 'IN');
    	return parent::getObjects($criteria, true);
    }
}
?>
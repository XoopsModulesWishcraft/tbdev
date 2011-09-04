<?php

defined('XOOPS_ROOT_PATH') or die("XOOPS root path not defined");

class TbTrackerstotorrents extends XoopsObject
{
    function __construct()
    {
        $this->initVar('id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('tracker_id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('torrent_id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('seeders', XOBJ_DTYPE_INT, null, false);
        $this->initVar('leechers', XOBJ_DTYPE_INT, null, false);
        $this->initVar('completed', XOBJ_DTYPE_INT, null, false);
        $this->initVar('lastchecked', XOBJ_DTYPE_INT, null, false);
    }

    function TbTrackerstotorrents()
    {
        $this->__construct();
    }


}

class TbTrackerstotorrentsHandler extends XoopsPersistableObjectHandler
{

    function TbTrackerstotorrentsHandler(&$db)
    {
        $this->__construct($db);
    }

    function __construct(&$db)
    {
        parent::__construct($db, "tb_trackers_to_torrents", 'TbTrackerstotorrents', "id");
    }

}
?>
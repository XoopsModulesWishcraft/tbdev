<?php

defined('XOOPS_ROOT_PATH') or die("XOOPS root path not defined");

class TbFriends extends XoopsObject
{
    function __construct()
    {
        $this->initVar('id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('userid', XOBJ_DTYPE_INT, null, false);
        $this->initVar('friendid', XOBJ_DTYPE_INT, null, false);
    }

    function TbFriends()
    {
        $this->__construct();
    }


}

class TbFriendsHandler extends XoopsPersistableObjectHandler
{

    function TbFriendsHandler(&$db)
    {
        $this->__construct($db);
    }

    function __construct(&$db)
    {
        parent::__construct($db, "tb_friends", 'TbFriends', "id");
    }

}
?>
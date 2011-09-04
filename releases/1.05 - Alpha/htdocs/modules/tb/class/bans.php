<?php

defined('XOOPS_ROOT_PATH') or die("XOOPS root path not defined");

class TbBans extends XoopsObject
{
    function __construct()
    {
        $this->initVar('id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('added', XOBJ_DTYPE_INT, null, false);
        $this->initVar('addedby', XOBJ_DTYPE_INT, null, false);
        $this->initVar('comment', XOBJ_DTYPE_TXTBOX, null, false, 255);
        $this->initVar('first', XOBJ_DTYPE_INT, null, false);
        $this->initVar('last', XOBJ_DTYPE_INT, null, false);
    }

    function TbBans()
    {
        $this->__construct();
    }


}

class TbBansHandler extends XoopsPersistableObjectHandler
{

    function TbBansHandler(&$db)
    {
        $this->__construct($db);
    }

    function __construct(&$db)
    {
        parent::__construct($db, "tb_bans", 'TbBans', "id");
    }

}
?>
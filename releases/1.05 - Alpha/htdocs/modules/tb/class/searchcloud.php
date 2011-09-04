<?php

defined('XOOPS_ROOT_PATH') or die("XOOPS root path not defined");

class TbSearchcloud extends XoopsObject
{
    function __construct()
    {
        $this->initVar('id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('searchedfor', XOBJ_DTYPE_TXTBOX, null, false, 50);
        $this->initVar('howmuch', XOBJ_DTYPE_INT, null, false);
    }

    function TbSearchcloud()
    {
        $this->__construct();
    }


}

class TbSearchcloudHandler extends XoopsPersistableObjectHandler
{

    function TbSearchcloudHandler(&$db)
    {
        $this->__construct($db);
    }

    function __construct(&$db)
    {
        parent::__construct($db, "tb_searchcloud", 'TbSearchcloud', "id");
    }

}
?>
<?php

defined('XOOPS_ROOT_PATH') or die("XOOPS root path not defined");

class TbSitelog extends XoopsObject
{
    function __construct()
    {
        $this->initVar('id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('added', XOBJ_DTYPE_INT, null, false);
        $this->initVar('txt', XOBJ_DTYPE_OTHER, null, false);
    }

    function TbSitelog()
    {
        $this->__construct();
    }


}

class TbSitelogHandler extends XoopsPersistableObjectHandler
{

    function TbSitelogHandler(&$db)
    {
        $this->__construct($db);
    }

    function __construct(&$db)
    {
        parent::__construct($db, "tb_sitelog", 'TbSitelog', "id");
    }

}
?>
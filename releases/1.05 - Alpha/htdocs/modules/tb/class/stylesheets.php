<?php

defined('XOOPS_ROOT_PATH') or die("XOOPS root path not defined");

class TbStylesheets extends XoopsObject
{
    function __construct()
    {
        $this->initVar('id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('uri', XOBJ_DTYPE_TXTBOX, null, false, 255);
        $this->initVar('name', XOBJ_DTYPE_TXTBOX, null, false, 64);
    }

    function TbStylesheets()
    {
        $this->__construct();
    }


}

class TbStylesheetsHandler extends XoopsPersistableObjectHandler
{

    function TbStylesheetsHandler(&$db)
    {
        $this->__construct($db);
    }

    function __construct(&$db)
    {
        parent::__construct($db, "tb_stylesheets", 'TbStylesheets', "id");
    }

}
?>
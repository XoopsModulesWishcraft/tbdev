<?php

defined('XOOPS_ROOT_PATH') or die("XOOPS root path not defined");

class TbCountries extends XoopsObject
{
    function __construct()
    {
        $this->initVar('id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('name', XOBJ_DTYPE_TXTBOX, null, false, 50);
        $this->initVar('flagpic', XOBJ_DTYPE_TXTBOX, null, false, 50);
    }

    function TbCountries()
    {
        $this->__construct();
    }


}

class TbCountriesHandler extends XoopsPersistableObjectHandler
{

    function TbCountriesHandler(&$db)
    {
        $this->__construct($db);
    }

    function __construct(&$db)
    {
        parent::__construct($db, "tb_countries", 'TbCountries', "id");
    }

}
?>
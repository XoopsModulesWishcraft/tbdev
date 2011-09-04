<?php

defined('XOOPS_ROOT_PATH') or die("XOOPS root path not defined");

class TbCategories extends XoopsObject
{
    function __construct()
    {
        $this->initVar('id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('parent_id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('name', XOBJ_DTYPE_TXTBOX, null, false, 30);
        $this->initVar('image', XOBJ_DTYPE_TXTBOX, null, false, 255);
        $this->initVar('cat_desc', XOBJ_DTYPE_TXTBOX, null, false, 255);
    }

    function TbCategories()
    {
        $this->__construct();
    }


}

class TbCategoriesHandler extends XoopsPersistableObjectHandler
{

    function TbCategoriesHandler(&$db)
    {
        $this->__construct($db);
    }

    function __construct(&$db)
    {
        parent::__construct($db, "tb_categories", 'TbCategories', "id");
    }

}
?>
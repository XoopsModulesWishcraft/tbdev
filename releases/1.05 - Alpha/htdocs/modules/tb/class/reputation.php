<?php

defined('XOOPS_ROOT_PATH') or die("XOOPS root path not defined");

class TbReputationlevel extends XoopsObject
{
    function __construct()
    {
        $this->initVar('reputationlevelid', XOBJ_DTYPE_INT, null, false);
        $this->initVar('minimumreputation', XOBJ_DTYPE_INT, null, false);
        $this->initVar('level', XOBJ_DTYPE_TXTBOX, null, false, 250);
    }

    function TbReputationlevel()
    {
        $this->__construct();
    }


}

class TbReputationlevelHandler extends XoopsPersistableObjectHandler
{

    function TbReputationlevelHandler(&$db)
    {
        $this->__construct($db);
    }

    function __construct(&$db)
    {
        parent::__construct($db, "tb_reputationlevel", 'TbReputationlevel', "reputationlevelid");
    }

}
?>
<?php

defined('XOOPS_ROOT_PATH') or die("XOOPS root path not defined");

class TbReputation extends XoopsObject
{
    function __construct()
    {
        $this->initVar('reputationid', XOBJ_DTYPE_INT, null, false);
        $this->initVar('reputation', XOBJ_DTYPE_INT, null, false);
        $this->initVar('whoadded', XOBJ_DTYPE_INT, null, false);
        $this->initVar('reason', XOBJ_DTYPE_TXTBOX, null, false, 250);
        $this->initVar('dateadd', XOBJ_DTYPE_INT, null, false);
        $this->initVar('postid', XOBJ_DTYPE_INT, null, false);
        $this->initVar('userid', XOBJ_DTYPE_INT, null, false);
    }

    function TbReputation($fields)
    {
        $this->__construct();
    }


}

class TbReputationHandler extends XoopsPersistableObjectHandler
{

    function TbReputationHandler(&$db)
    {
        $this->__construct($db);
    }

    function __construct(&$db)
    {
        parent::__construct($db, "tb_reputation", 'TbReputation', "reputationid");
    }

}
?>
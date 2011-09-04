<?php

defined('XOOPS_ROOT_PATH') or die("XOOPS root path not defined");

class TbFiles extends XoopsObject
{
    function __construct()
    {
        $this->initVar('id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('torrent', XOBJ_DTYPE_INT, null, false);
        $this->initVar('filename', XOBJ_DTYPE_TXTBOX, null, false, 255);
        $this->initVar('size', XOBJ_DTYPE_INT, null, false);
    }

    function TbFiles()
    {
        $this->__construct();
    }


}

class TbFilesHandler extends XoopsPersistableObjectHandler
{

    function TbFilesHandler(&$db)
    {
        $this->__construct($db);
    }

    function __construct(&$db)
    {
        parent::__construct($db, "tb_files", 'TbFiles', "id");
    }

}
?>
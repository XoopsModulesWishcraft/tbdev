<?php

defined('XOOPS_ROOT_PATH') or die("XOOPS root path not defined");

class TbAvps extends XoopsObject
{
    function __construct()
    {
        $this->initVar('arg', XOBJ_DTYPE_TXTBOX, null, false, 20);
        $this->initVar('value_s', XOBJ_DTYPE_OTHER, null, false);
        $this->initVar('value_i', XOBJ_DTYPE_INT, null, false);
        $this->initVar('value_u', XOBJ_DTYPE_INT, null, false);
    }

    function TbAvps()
    {
        $this->__construct();
    }


}

class TbAvpsHandler extends XoopsPersistableObjectHandler
{

    function TbAvpsHandler(&$db)
    {
        $this->__construct($db);
    }

    function __construct(&$db)
    {
        parent::__construct($db, "tb_avps", 'TbAvps', "arg");
    }

	function autoclean() {
	    $now = time();
	    $res = $GLOBALS['xoopsDB']->queryF("SELECT value_u FROM ".$GLOBALS['xoopsDB']->prefix("tb_avps")." WHERE arg = 'lastcleantime'");
	    $row = mysql_fetch_array($res);
	    if (!$row) {
	        $GLOBALS['xoopsDB']->queryF("INSERT INTO ".$GLOBALS['xoopsDB']->prefix("tb_avps")." (arg, value_u) VALUES ('lastcleantime',$now)");
	        return;
	    }
	    $ts = $row[0];
	    if ($ts + $GLOBALS['TBDEV']['autoclean_interval'] > $now)
	        return;
	    $GLOBALS['xoopsDB']->queryF("UPDATE ".$GLOBALS['xoopsDB']->prefix("tb_avps")." SET value_u=$now WHERE arg='lastcleantime' AND value_u = $ts");
	    if (!mysql_affected_rows())
	        return;
	    docleanup();
	}
}
?>
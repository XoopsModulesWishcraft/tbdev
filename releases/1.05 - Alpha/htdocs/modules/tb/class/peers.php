<?php

defined('XOOPS_ROOT_PATH') or die("XOOPS root path not defined");

class TbPeers extends XoopsObject
{
    function __construct()
    {
        $this->initVar('id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('torrent', XOBJ_DTYPE_INT, null, false);
        $this->initVar('passkey', XOBJ_DTYPE_TXTBOX, null, false, 32);
        $this->initVar('peer_id', XOBJ_DTYPE_TXTBOX, null, false, 20);
        $this->initVar('ip', XOBJ_DTYPE_TXTBOX, null, false, 60);
        $this->initVar('port', XOBJ_DTYPE_INT, null, false);
        $this->initVar('uploaded', XOBJ_DTYPE_INT, null, false);
        $this->initVar('downloaded', XOBJ_DTYPE_INT, null, false);
        $this->initVar('to_go', XOBJ_DTYPE_INT, null, false);
        $this->initVar('seeder', XOBJ_DTYPE_ENUM, 'no', false, false, false, array('yes', 'no'));
        $this->initVar('started', XOBJ_DTYPE_INT, null, false);
        $this->initVar('last_action', XOBJ_DTYPE_INT, null, false);
        $this->initVar('connectable', XOBJ_DTYPE_ENUM, 'yes', false, false, false, array('yes', 'no'));
        $this->initVar('userid', XOBJ_DTYPE_INT, null, false);
        $this->initVar('agent', XOBJ_DTYPE_TXTBOX, null, false, 60);
        $this->initVar('finishedat', XOBJ_DTYPE_INT, null, false);
        $this->initVar('downloadoffset', XOBJ_DTYPE_INT, null, false);
        $this->initVar('uploadoffset', XOBJ_DTYPE_INT, null, false);
    }

    function TbPeers()
    {
        $this->__construct();
    }


}

class TbPeerspCount extends XoopsObject
{
    function __construct()
    {
        $this->initVar('pCount', XOBJ_DTYPE_INT, null, false);
        $this->initVar('seeder', XOBJ_DTYPE_ENUM, 'no', false, false, false, array('yes', 'no'));
    }

    function TbPeerspCount()
    {
        $this->__construct();
    }


}

class TbPeersHandler extends XoopsPersistableObjectHandler
{

    function TbPeersHandler(&$db)
    {
        $this->__construct($db);
    }

    function __construct(&$db)
    {
        parent::__construct($db, "tb_peers", 'TbPeers', "id");
    }

    function getPeersbyUser($userid=0) {
    	
    	if ($userid==0)
    		$userid=$GLOBALS['CURUSER']['id'];
    		
    	$result = @$GLOBALS['xoopsDB']->queryF("SELECT seeder, COUNT(*) AS pCount FROM ".$GLOBALS['xoopsDB']->prefix("tb_peers")." WHERE userid=".$userid." GROUP BY seeder") or sqlerr(__LINE__,__FILE__);
    	$ret = array();
    	$i=0;
    	while($row = $GLOBALS['xoopsDB']->fetchArray($result)) {
    		$ret[$i] = new TbPeerspCount();
    		$ret[$i]->assignVars($row);
    		$i++;
    	}
    	return $ret;
    }
}
?>
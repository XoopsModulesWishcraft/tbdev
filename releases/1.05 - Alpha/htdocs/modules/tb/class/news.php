<?php

defined('XOOPS_ROOT_PATH') or die("XOOPS root path not defined");

class TbNews extends XoopsObject
{
    function __construct()
    {
        $this->initVar('id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('userid', XOBJ_DTYPE_INT, null, false);
        $this->initVar('added', XOBJ_DTYPE_INT, null, false);
        $this->initVar('body', XOBJ_DTYPE_OTHER, null, false);
        $this->initVar('headline', XOBJ_DTYPE_TXTBOX, null, false, 150);
    }

    function TbNews()
    {
        $this->__construct();
    }


}

class TbNewsHandler extends XoopsPersistableObjectHandler
{

    function TbNewsHandler(&$db)
    {
        $this->__construct($db);
    }

    function __construct(&$db)
    {
        parent::__construct($db, "tb_news", 'TbNews', "id");
    }

    function getCurrentNews($id_as_key=true,$limit=10) {
    	$sql = "SELECT * FROM ".$GLOBALS['xoopsDB']->prefix("tb_news")." WHERE added + ( 3600 *24 *45 ) > ".time()." ORDER BY added DESC LIMIT ".$limit;
    	$result = $GLOBALS['xoopsDB']->queryF($sql);
    	$i=0;
    	$ret = array();
    	while($row = $GLOBALS['xoopsDB']->fetchArray($result)) {
    		if ($id_as_key==true) {
    			$ret[$row['id']] = new TbNews();
    			$ret[$row['id']]->assignVars($row);
    		} else {
    			$ret[$i] = new TbNews();
    			$ret[$i]->assignVars($row);
    			$i++;
    		}
    	}
    	return $ret;
    }
}
?>
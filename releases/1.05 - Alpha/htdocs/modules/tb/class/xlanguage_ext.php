<?php


if (!defined('XOOPS_ROOT_PATH')) {
	exit();
}


class TbXlanguage_ext extends XoopsObject
{

    function TbXlanguage_ext($id = null)
    {
        $this->initVar('lang_id', XOBJ_DTYPE_INT, null, false);
		$this->initVar('weight', XOBJ_DTYPE_INT, null, false);
        $this->initVar('lang_name', XOBJ_DTYPE_TXTBOX, null, true, 255);
        $this->initVar('lang_desc', XOBJ_DTYPE_TXTBOX, null, true, 255);
        $this->initVar('lang_code', XOBJ_DTYPE_TXTBOX, null, true, 255);
        $this->initVar('lang_charset', XOBJ_DTYPE_TXTBOX, null, true, 255);
        $this->initVar('lang_image', XOBJ_DTYPE_TXTBOX, null, true, 255);
        $this->initVar('lang_base', XOBJ_DTYPE_TXTBOX, null, true, 255);

	}
}



class TbXlanguage_extHandler extends XoopsPersistableObjectHandler
{
    function __construct($db) 
    {
		$this->db = $db;
        parent::__construct($db, "xlanguage_ext", 'TbXlanguage_ext', "lang_id", "lang_name");
    }
    
    function getLanguages()
    {
    	$module_handler =& xoops_gethandler('module');
    	$xLanguage = $module_handler->getByDirname('xlanguage');
		$ret = array();
    	if (is_object($xLanguage)) {
    		if ($xLanguage->getVar('active')) {
    			foreach($this->getObjects(NULL,true) as $lang_id=>$lang)
    				$ret[$lang->getVar('lang_base')]=$lang->getVar('lang_name');
    		} else {
    			require_once(XOOPS_ROOT_PATH.'/class/xoopslists.php');
				$languages =& XoopsLists::getDirListAsArray(XOOPS_ROOT_PATH.'/language/');
				foreach($languages as $lang_id=>$lang)
					$ret[$lang] = ucfirst($lang);
    		}
    	} else {
    		require_once(XOOPS_ROOT_PATH.'/class/xoopslists.php');
			$languages =& XoopsLists::getDirListAsArray(XOOPS_ROOT_PATH.'/language/');
			foreach($languages as $lang_id=>$lang)
				$ret[$lang] = ucfirst($lang);
			
    	}
    	return $ret;
    }
}

?>
<?php

defined('XOOPS_ROOT_PATH') or die("XOOPS root path not defined");

class TbTorrents extends XoopsObject
{
    function __construct()
    {
        $this->initVar('id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('info_hash', XOBJ_DTYPE_TXTBOX, null, false, 40);
        $this->initVar('name', XOBJ_DTYPE_TXTBOX, null, false, 255);
        $this->initVar('filename', XOBJ_DTYPE_TXTBOX, null, false, 255);
        $this->initVar('save_as', XOBJ_DTYPE_TXTBOX, null, false, 255);
        $this->initVar('search_text', XOBJ_DTYPE_OTHER, null, false);
        $this->initVar('descr', XOBJ_DTYPE_OTHER, null, false);
        $this->initVar('ori_descr', XOBJ_DTYPE_OTHER, null, false);
        $this->initVar('category', XOBJ_DTYPE_INT, null, false);
        $this->initVar('size', XOBJ_DTYPE_INT, null, false);
        $this->initVar('added', XOBJ_DTYPE_INT, null, false);
        $this->initVar('type', XOBJ_DTYPE_ENUM, null, false, false, false, array('single','multi'));
        $this->initVar('numfiles', XOBJ_DTYPE_INT, null, false);
        $this->initVar('comments', XOBJ_DTYPE_INT, null, false);
        $this->initVar('views', XOBJ_DTYPE_INT, null, false);
        $this->initVar('hits', XOBJ_DTYPE_INT, null, false);
        $this->initVar('times_completed', XOBJ_DTYPE_INT, null, false);
        $this->initVar('leechers', XOBJ_DTYPE_INT, null, false);
        $this->initVar('seeders', XOBJ_DTYPE_INT, null, false);
        $this->initVar('last_action', XOBJ_DTYPE_INT, null, false);
        $this->initVar('visible', XOBJ_DTYPE_ENUM, null, false, false, false, array('yes', 'no'));
        $this->initVar('banned', XOBJ_DTYPE_ENUM, null, false, false, false, array('yes', 'no'));
        $this->initVar('owner', XOBJ_DTYPE_INT, null, false);
        $this->initVar('numratings', XOBJ_DTYPE_INT, null, false);
        $this->initVar('ratingsum', XOBJ_DTYPE_INT, null, false);
        $this->initVar('nfo', XOBJ_DTYPE_OTHER, null, false);
        $this->initVar('client_created_by', XOBJ_DTYPE_TXTBOX, null, false, 50);
    }

    function TbTorrents()
    {
        $this->__construct();
    }

    function getForm() {
    	include_once($GLOBALS['xoops']->path('/modules/tb/include/form.objects.php'));
        xoops_loadLanguage('forms', 'tb');
        
        $frmobj = array();
        $frmobj['name'] = new XoopsFormText(_TBDEV_FRM_TORRENT_NAME, 'name', 35, 255, $this->getVar('name') );
	    $frmobj['name']->setDescription(_TBDEV_FRM_TORRENT_NAME_DESC);
	    	
    	if ($this->isNew()) {
    		$form = new XoopsThemeForm(_TBDEV_FRM_NEW_TORRENT, 'torrents', $_SERVER['PHP_SELF'], 'post');
    	} else {
    		$form = new XoopsThemeForm(_TBDEV_FRM_EDIT_TORRENT, 'torrents', XOOPS_URL.'/modules/tb/takeedit.php', 'post');
    		
    		$frmobj['editnfo'] = new XoopsFormRadio(_TBDEV_FRM_TORRENT_EDITNFO, 'nfoaction', 'keep');
	    	$frmobj['editnfo']->setDescription(_TBDEV_FRM_TORRENT_EDITNFO_DESC);
	    	$frmobj['editnfo']->addOption('keep', _TBDEV_FRM_TORRENT_EDITNFO_KEEP);
	    	$frmobj['editnfo']->addOption('update', _TBDEV_FRM_TORRENT_EDITNFO_UPDATE);
	    	$frmobj['nfo'] = new XoopsFormFile(_TBDEV_FRM_TORRENT_NFO, 'nfo' );
	    	$frmobj['nfo']->setDescription(_TBDEV_FRM_TORRENT_NFO_DESC);
	    	
    	}
    	
    	$ori_desc_configs = array();
		$ori_desc_configs['name'] = 'descr';
		$ori_desc_configs['value'] = $this->getVar('ori_descr');
		$ori_desc_configs['rows'] = 35;
		$ori_desc_configs['cols'] = 60;
		$ori_desc_configs['width'] = "100%";
		$ori_desc_configs['height'] = "400px";
		$ori_desc_configs['editor'] = $GLOBALS['xoopsModuleConfig']['editor'];
		$frmobj['descr'] = new XoopsFormEditor(_TBDEV_FRM_TORRENT_DESCRIPTION, $ori_desc_configs['name'], $ori_desc_configs);
		$frmobj['descr']->setDescription(_TBDEV_FRM_TORRENT_DESCRIPTION_DESC);

		$frmobj['type'] = new TbFormSelectCategory(_TBDEV_FRM_TORRENT_TYPE, 'type', $this->getVar('category') );
    	$frmobj['type']->setDescription(_TBDEV_FRM_TORRENT_TYPE_DESC);

    	$frmobj['visible'] = new XoopsFormRadioYN(_TBDEV_FRM_TORRENT_VISIBLE, 'visible', ($this->getVar('visible')=='yes'?true:false) );
	    $frmobj['visible']->setDescription(_TBDEV_FRM_TORRENT_VISIBLE_DESC);
    	
	    if (get_user_class() >= UC_MODERATOR) {
			$frmobj['banned'] = new XoopsFormRadioYN(_TBDEV_FRM_TORRENT_BANNED, 'banned', ($this->getVar('banned')=='yes'?true:false) );
	    	$frmobj['banned']->setDescription(_TBDEV_FRM_TORRENT_BANNED_DESC);
    	}  
    
    	$frmobj['submit'] = new XoopsFormButton('', 'submit', _SUBMIT, 'submit');
    	
    	foreach($frmobj as $key => $value) {
    		$form->addElement($frmobj[$key], false);
    	}
    	
    	return $form->render();	
    }
    
    function toArray() {
    	$categories_handler = xoops_getmodulehandler('categories', 'tb');
    	$users_handler = xoops_getmodulehandler('users', 'tb');
    	    	
    	$ret = parent::toArray();
    	$category = $categories_handler->get($this->getVar('category'));
    	if (is_object($category))
    		$ret['category'] = $category->toArray();

       	$user = $users_handler->get($this->getVar('owner'));
    	if (is_object($user))
    		$ret['user'] = $category->toArray();
    		
    	$ret['wait'] = 0;
        if ($GLOBALS['CURUSER']["class"] < UC_VIP)
	    {
			$gigs = $GLOBALS['CURUSER']["uploaded"] / (1024*1024*1024);
			$ratio = (($GLOBALS['CURUSER']["downloaded"] > 0) ? ($GLOBALS['CURUSER']["uploaded"] / $GLOBALS['CURUSER']["downloaded"]) : 0);
			if ($ratio < 0.5 || $gigs < 5) $ret['wait'] = 48;
			elseif ($ratio < 0.65 || $gigs < 6.5) $ret['wait'] = 24;
			elseif ($ratio < 0.8 || $gigs < 8) $ret['wait'] = 12;
			elseif ($ratio < 0.95 || $gigs < 9.5) $ret['wait'] = 6;
			else $ret['wait'] = 0;
	    }
    	$ret['mksize']['size'] = mksize($this->getVar('size'));
    	$ret['date']['added'] = date(_DATESTRING, $this->getVar('added'));
    	$ret['date']['last_action'] = date(_DATESTRING, $this->getVar('last_action'));
    	$ret['number_format']['times_completed'] = number_format($this->getVar('times_completed'));
    	$ret['number_format']['hits'] = number_format($this->getVar('hits'));
    	$ret['number_format']['numfiles'] = number_format($this->getVar('numfiles'));
    	$ret['number_format']['comments'] = number_format($this->getVar('comments'));
    	$ret['number_format']['views'] = number_format($this->getVar('views'));
    	$ret['number_format']['leechers'] = number_format($this->getVar('leechers'));
    	$ret['number_format']['seeders'] = number_format($this->getVar('seeders'));
    	$ret['ttl'] = ((28*24) - floor((time() - $this->getVar('added')) / 3600));
    	$ret['elapsed'] = floor((time() - $this->getVar('added')) / 3600);
    	$ret['color'] = dechex(floor(127*($ret['wait'] = 0 - $ret['elapsed'])/48 + 128)*65536);
    	$ret['ratio']['value'] = $this->getVar('seeders') / $this->getVar('leechers'); 
    	if ($ret['ratio']['value'] <0 ) $ret['ratio']['value'] = 1;
    	$ret['color']['ratio'] = get_slr_color($ret['ratio']['value']);
    	$ret['color']['seeders'] = linkcolor($this->getVar('seeders'));
    	$ret['color']['leechers'] = linkcolor($this->getVar('leechers'));
    	return $ret;
    }

}

class TbTorrentsHandler extends XoopsPersistableObjectHandler
{

    function TbTorrentsHandler(&$db)
    {
        $this->__construct($db);
    }

    function __construct(&$db)
    {
        parent::__construct($db, "tb_torrents", 'TbTorrents', "id");
    }

    function benc_resp($d)
	{
		benc_resp_raw(benc(array('type' => 'dictionary', 'value' => $d)));
	}
	
	function benc_resp_raw($x)
	{
	    header( "Content-Type: text/plain" );
	    header( "Pragma: no-cache" );
	
	    if ( $_SERVER['HTTP_ACCEPT_ENCODING'] == 'gzip' )
	    {
	        header( "Content-Encoding: gzip" );
	        echo gzencode( $x, 9, FORCE_GZIP );
	    }
	    else
	        echo $x ;
	}
	
	function benc($obj) {
		if (!is_array($obj) || !isset($obj["type"]) || !isset($obj["value"]))
			return;
		$c = $obj["value"];
		switch ($obj["type"]) {
			case "string":
				return benc_str($c);
			case "integer":
				return benc_int($c);
			case "list":
				return benc_list($c);
			case "dictionary":
				return benc_dict($c);
			default:
				return;
		}
	}
	
	function benc_str($s) {
		return strlen($s) . ":$s";
	}
	
	function benc_int($i) {
		return "i" . $i . "e";
	}
	
	function benc_list($a) {
		$s = "l";
		foreach ($a as $e) {
			$s .= benc($e);
		}
		$s .= "e";
		return $s;
	}
	
	function benc_dict($d) {
		$s = "d";
		$keys = array_keys($d);
		sort($keys);
		foreach ($keys as $k) {
			$v = $d[$k];
			$s .= benc_str($k);
			$s .= benc($v);
		}
		$s .= "e";
		return $s;
	}
	    
	function err($msg)
	{
		$this->benc_resp(array('failure reason' => array('type' => 'string', 'value' => $msg)));
		exit();
	}

	function bdec_file($f, $ms) {
		$fp = fopen($f, "rb");
		if (!$fp)
			return;
		$e = fread($fp, $ms);
		fclose($fp);
		return $this->bdec($e);
	}
	
	function bdec($s) {
		if (preg_match('/^(\d+):/', $s, $m)) {
			$l = $m[1];
			$pl = strlen($l) + 1;
			$v = substr($s, $pl, $l);
			$ss = substr($s, 0, $pl + $l);
			if (strlen($v) != $l)
				return;
			return array("type" => "string", "value" => $v, "strlen" => strlen($ss), "string" => $ss);
		}
		if (preg_match('/^i(\d+)e/', $s, $m)) {
			$v = $m[1];
			$ss = "i" . $v . "e";
			if ($v === "-0")
				return;
			if ($v[0] == "0" && strlen($v) != 1)
				return;
			return array("type" => "integer", "value" => $v, "strlen" => strlen($ss), "string" => $ss);
		}
		switch ($s[0]) {
			case "l":
				return $this->bdec_list($s);
			case "d":
				return $this->bdec_dict($s);
			default:
				return;
		}
	}
	
	function bdec_list($s) {
		if ($s[0] != "l")
			return;
		$sl = strlen($s);
		$i = 1;
		$v = array();
		$ss = "l";
		for (;;) {
			if ($i >= $sl)
				return;
			if ($s[$i] == "e")
				break;
			$ret = $this->bdec(substr($s, $i));
			if (!isset($ret) || !is_array($ret))
				return;
			$v[] = $ret;
			$i += $ret["strlen"];
			$ss .= $ret["string"];
		}
		$ss .= "e";
		return array("type" => "list", "value" => $v, "strlen" => strlen($ss), "string" => $ss);
	}
	
	function bdec_dict($s) {
		if ($s[0] != "d")
			return;
		$sl = strlen($s);
		$i = 1;
		$v = array();
		$ss = "d";
		for (;;) {
			if ($i >= $sl)
				return;
			if ($s[$i] == "e")
				break;
			$ret = $this->bdec(substr($s, $i));
			if (!isset($ret) || !is_array($ret) || $ret["type"] != "string")
				return;
			$k = $ret["value"];
			$i += $ret["strlen"];
			$ss .= $ret["string"];
			if ($i >= $sl)
				return;
			$ret = $this->bdec(substr($s, $i));
			if (!isset($ret) || !is_array($ret))
				return;
			$v[$k] = $ret;
			$i += $ret["strlen"];
			$ss .= $ret["string"];
		}
		$ss .= "e";
		return array("type" => "dictionary", "value" => $v, "strlen" => strlen($ss), "string" => $ss);
	}
	
	private function getBrowseWhere($input = array(), $cats = array()) {
		
		if (empty($cats))
			$cats = genrelist();
		
		if(isset($input["search"])) {
	      	$searchstr = unesc($input["search"]);
	      	$GLOBAL['cleansearchstr'] = searchfield($searchstr);
	      	if (empty($GLOBAL['cleansearchstr']))
	        	unset($GLOBAL['cleansearchstr']);
	    }
	
	    $orderby = "ORDER BY ".$GLOBALS['xoopsDB']->prefix("tb_torrents").".id DESC";
	
	    $addparam = "";
	    $wherea = array();
	    $wherecatina = array();
	
	    if (isset($input["incldead"]) &&  $input["incldead"] == 1) {
	      	$addparam .= "incldead=1&amp;";
	      	if (!isset($GLOBALS['CURUSER']) || get_user_class() < UC_ADMINISTRATOR)
	        	$wherea[] = "banned != 'yes'";
	    } else {
		     if (isset($input["incldead"]) && $input["incldead"] == 2) {
		     	$addparam .= "incldead=2&amp;";
		        $wherea[] = "visible = 'no'";
		      } else
		        $wherea[] = "visible = 'yes'";
	    }
	    
	    $category = (isset($input["cat"])) ? (int)$input["cat"] : false;
	    $all = isset($input["all"]) ? $input["all"] : false;
	
	    if (!$all) {
	      	if (!$input && $GLOBALS['CURUSER']["notifs"]) {
	        	$all = true;
	        	foreach ($cats as $cat) {
	          		$all &= $cat['id'];
	          		if (strpos($GLOBALS['CURUSER']["notifs"], "[cat" . $cat['id'] . "]") !== false) {
	            		$wherecatina[] = $cat['id'];
	            		$addparam .= "c{$cat['id']}=1&amp;";
	          		}
	        	}
	      	} elseif ($category) {
	        	if (!is_valid_id($category))
	          		stderr("{$GLOBALS['lang']['browse_error']}", "{$GLOBALS['lang']['browse_invalid_cat']}");
	        	$wherecatina[] = $category;
	        	$addparam .= "cat=$category&amp;";
	      	} else {
	        	$all = true;
	        	foreach ($cats as $cat)	{
	          		$all &= isset($input["c{$cat['id']}"]);
	          		if (isset($input["c{$cat['id']}"])) {
	            		$wherecatina[] = $cat['id'];
	            		$addparam .= "c{$cat['id']}=1&amp;";
	          		}
	        	}
	      	}
	    }
	    
	    if ($all) {
	      	$wherecatina = array();
	      	$addparam = "";
	    }
	
	    if (count($wherecatina) > 1)
	      	$wherecatin = implode(",",$wherecatina);
	    elseif (count($wherecatina) == 1)
	      	$wherea[] = "category = $wherecatina[0]";
	
	    $wherebase = $wherea;
	    if (isset($cleansearchstr))
	    {
	      	$wherea[] = " `search_text` LIKE " . sqlesc('%'.$searchstr.'%') . " OR `ori_descr` LIKE " . sqlesc('%'.$searchstr.'%') . "";
		    $addparam .= "search=" . urlencode($searchstr) . "&amp;";
	      	$orderby = "";
	      
	        $searchcloud = sqlesc($cleansearchstr);
	        @$GLOBALS['xoopsDB']->queryF("INSERT INTO ".$GLOBALS['xoopsDB']->prefix("tb_searchcloud")." (searchedfor, howmuch) VALUES ($searchcloud, 1)
	                    ON DUPLICATE KEY UPDATE howmuch=howmuch+1");
	    }
	    $where = implode(" AND ", $wherea);
	    
	    if (isset($wherecatin))
	      $where .= ($where ? " AND " : "") . "category IN(" . $wherecatin . ")";
	
	    if ($where != "")
	      $where = "WHERE $where";
	
	    return array('where'=>$where, 'param'=>$addparam);
	
	}
	
	function getBrowseCount($input = array(), $cats = array()) {
		$where = $this->getBrowseWhere($input, $cats);
	    $res = $GLOBALS['xoopsDB']->queryF("SELECT COUNT(*) as count FROM ".$GLOBALS['xoopsDB']->prefix("tb_torrents")." ".$where['where']) or die(mysql_error());
	    $row = $GLOBALS['xoopsDB']->fetchArray($res);
	    return $row['count'];
	}
	
	function getMyTorrents($start, $limit=30, $is_as_key=true, $where = '') {
		if (empty($where)) {
			$where = "WHERE owner = " . $GLOBALS['CURUSER']["id"] . " AND banned != 'yes'";
		}
		list($count) = $GLOBALS['xoopsDB']->fetchRow($GLOBALS['xoopsDB']->queryF("SELECT count(*) as count FROM ".$GLOBALS['xoopsDB']->prefix("tb_torrents")." $where ORDER BY id DESC"));
		$result = $GLOBALS['xoopsDB']->queryF("SELECT ".$GLOBALS['xoopsDB']->prefix("tb_torrents").".* FROM ".$GLOBALS['xoopsDB']->prefix("tb_torrents")." $where ORDER BY id DESC LIMIT ".$start.','.$limit);
		$categories_handler = xoops_getmodulehandler('categories', 'tb');
    	$users_handler = xoops_getmodulehandler('users', 'tb');
        
    	xoops_load('pagenav');
      	
        $ret['pagnav']['object'] = new XoopsPageNav($count, $limit, $start, "start", "amp;limit=".$limit."");
      	$ret['pagnav']['data'] = $ret['pagnav']['object']->renderNav();
	      	
      	while($row = $GLOBALS['xoopsDB']->fetchArray($result)) {
      		if ($id_as_key == true) {
      			$ret['torrents'][$row['id']]['torrent']['object'] = new TbTorrents();
      			$ret['torrents'][$row['id']]['torrent']['object']->assignVars($row);
      			$ret['torrents'][$row['id']]['torrent']['data'] = $ret[$i]['torrent']['object']->toArray();
      			$ret['torrents'][$row['id']]['category']['object'] = $categories_handler->get($row['category']);
      			$ret['torrents'][$row['id']]['category']['data'] = $ret[$i]['category']['object']->toArray();
      			$ret['torrents'][$row['id']]['user']['object'] = $users_handler->get($row['owner']);
      			$ret['torrents'][$row['id']]['user']['data'] = $ret[$i]['user']['object']->toArray();
      			
      		} else {
      			$ret['torrents'][$i]['torrent']['object'] = new TbTorrents();
      			$ret['torrents'][$i]['torrent']['object']->assignVars($row);
      			$ret['torrents'][$i]['torrent']['data'] = $ret[$i]['torrent']['object']->toArray();
      			$ret['torrents'][$i]['category']['object'] = $categories_handler->get($row['category']);
      			$ret['torrents'][$i]['category']['data'] = $ret[$i]['category']['object']->toArray();
      			$ret['torrents'][$i]['user']['object'] = $users_handler->get($row['owner']);
      			$ret['torrents'][$i]['user']['data'] = $ret[$i]['user']['object']->toArray();	      			
      		 	$i++;
      		}
      	}
      	return $ret;
    
	}
	
	function getBrowse($input = array(), $cats = array(), $start, $limit=30, $id_as_key = true) {
		
		$where = $this->getBrowseWhere($input, $cats);    
		$count = $this->getBrowseCount($input, $cats);
		
	    if (!$count && isset($GLOBAL['cleansearchstr'])) {
		    $wherea = $wherebase;
		    $orderby = "ORDER BY id DESC";
		    $searcha = explode(" ", $GLOBAL['cleansearchstr']);
		    $sc = 0;
		    foreach ($searcha as $searchss) {
		      	if (strlen($searchss) <= 1)
		       		continue;
		       	$sc++;
		       	if ($sc > 5)
		       		break;
		       	$ssa = array();
		       	foreach (array("search_text", "ori_descr") as $sss)
		       		$ssa[] = "$sss LIKE '%" . sqlwildcardesc($searchss) . "%'";
		       	$wherea[] = "(" . implode(" OR ", $ssa) . ")";
		    }
		    if ($sc) {
		       	$where = implode(" AND ", $wherea);
		       	if ($where != "")
		       		$where = "WHERE $where";
		       	$res = $GLOBALS['xoopsDB']->queryF("SELECT COUNT(*) FROM ".$GLOBALS['xoopsDB']->prefix("tb_torrents")." $where");
		       	$row = mysql_fetch_array($res,MYSQL_NUM);
		       	$count = $row[0];
		    }
	    }
	
	    $limit = $GLOBALS['CURUSER']["torrentsperpage"];
	    if (!$limit)
	      $limit = 15;
	
	    $ret = array();
	    $i=0;
	    if ($count) {
	    	$categories_handler = xoops_getmodulehandler('categories', 'tb');
	    	$users_handler = xoops_getmodulehandler('users', 'tb');
	        
	    	xoops_load('pagenav');
	      	
	        $ret['pagnav']['object'] = new XoopsPageNav($count, $limit, $start, "start", "amp;limit=".$limit."amp;".$where['param']);
	      	$ret['pagnav']['data'] = $ret['pagnav']['object']->renderNav();
	      	
	      	$query = "SELECT ".$GLOBALS['xoopsDB']->prefix("tb_torrents").".* FROM ".$GLOBALS['xoopsDB']->prefix("tb_torrents")." $where $orderby LIMIT $start, $limit";
	      	$res = $GLOBALS['xoopsDB']->queryF($query);
	      	while($row = $GLOBALS['xoopsDB']->fetchArray($res)) {
	      		if ($id_as_key == true) {
	      			$ret['torrents'][$row['id']]['torrent']['object'] = new TbTorrents();
	      			$ret['torrents'][$row['id']]['torrent']['object']->assignVars($row);
	      			$ret['torrents'][$row['id']]['torrent']['data'] = $ret[$i]['torrent']['object']->toArray();
	      			$ret['torrents'][$row['id']]['category']['object'] = $categories_handler->get($row['category']);
	      			$ret['torrents'][$row['id']]['category']['data'] = $ret[$i]['category']['object']->toArray();
	      			$ret['torrents'][$row['id']]['user']['object'] = $users_handler->get($row['owner']);
	      			$ret['torrents'][$row['id']]['user']['data'] = $ret[$i]['user']['object']->toArray();
	      			
	      		} else {
	      			$ret['torrents'][$i]['torrent']['object'] = new TbTorrents();
	      			$ret['torrents'][$i]['torrent']['object']->assignVars($row);
	      			$ret['torrents'][$i]['torrent']['data'] = $ret[$i]['torrent']['object']->toArray();
	      			$ret['torrents'][$i]['category']['object'] = $categories_handler->get($row['category']);
	      			$ret['torrents'][$i]['category']['data'] = $ret[$i]['category']['object']->toArray();
	      			$ret['torrents'][$i]['user']['object'] = $users_handler->get($row['owner']);
	      			$ret['torrents'][$i]['user']['data'] = $ret[$i]['user']['object']->toArray();	      			
	      		 	$i++;
	      		}
	      	}
	      	return $ret;
	    } else {
	      	unset($res);
	    }
		return false;
	}
	
	private function StdDecodePeerId($id_data, $id_name){
		$version_str = "";
		for ($i=0; $i<=strlen($id_data); $i++){
			$c = $id_data[$i];
			if ($id_name=="BitTornado" || $id_name=="ABC") {
				if ($c!='-' && ctype_digit($c)) 
					$version_str .= "$c.";
				elseif ($c!='-' && ctype_alpha($c))
					$version_str .= (ord($c)-55).".";
				else 
					break;
			} elseif($id_name=="BitComet"||$id_name=="BitBuddy"||$id_name=="Lphant"||$id_name=="BitPump"||$id_name=="BitTorrent Plus! v2") {
				if ($c != '-' && ctype_alnum($c)){
					$version_str .= "$c";
					if($i==0) 
						$version_str = intval($version_str) .".";
				} else{
					$version_str .= ".";
					break;
				}
			} else {
				if ($c != '-' && ctype_alnum($c)) 
					$version_str .= "$c.";
				else
					break;
			}
		}
		$version_str = substr($version_str,0,strlen($version_str)-1);
		return "$id_name $version_str";
	}
	
	private function MainlineDecodePeerId($id_data, $id_name){
		$version_str = "";
		for ($i=0; $i<=strlen($id_data); $i++){
			$c = isset($id_data[$i]) ? $id_data[$i] : '-';
			if ($c != '-' && ctype_alnum($c)) 
				$version_str .= "$c.";
		}
		$version_str = substr($version_str,0,strlen($version_str)-1);
		return "$id_name $version_str";
	}
	
	private function DecodeVersionString ($ver_data, $id_name){
		$version_str = "";
		$version_str .= intval(ord($ver_data[0]) + 0).".";
		$version_str .= intval(ord($ver_data[1])/10 + 0);
		$version_str .= intval(ord($ver_data[1])%10 + 0);
		return "$id_name $version_str";
	}
	
	function getagent($httpagent, $peer_id="") {
	// if($peer_id!="") $peer_id=hex2bin($peer_id);
		if(substr($peer_id,0,3)=='-AX') return $this->StdDecodePeerId(substr($peer_id,4,4),"BitPump"); # AnalogX BitPump
		if(substr($peer_id,0,3)=='-BB') return $this->StdDecodePeerId(substr($peer_id,3,5),"BitBuddy"); # BitBuddy
		if(substr($peer_id,0,3)=='-BC') return $this->StdDecodePeerId(substr($peer_id,4,4),"BitComet"); # BitComet
		if(substr($peer_id,0,3)=='-BS') return $this->StdDecodePeerId(substr($peer_id,3,7),"BTSlave"); # BTSlave
		if(substr($peer_id,0,3)=='-BX') return $this->StdDecodePeerId(substr($peer_id,3,7),"BittorrentX"); # BittorrentX
		if(substr($peer_id,0,3)=='-CT') return "Ctorrent $peer_id[3].$peer_id[4].$peer_id[6]"; # CTorrent
		if(substr($peer_id,0,3)=='-KT') return $this->StdDecodePeerId(substr($peer_id,3,7),"KTorrent"); # KTorrent
		if(substr($peer_id,0,3)=='-LT') return $this->StdDecodePeerId(substr($peer_id,3,7),"libtorrent"); # libtorrent
		if(substr($peer_id,0,3)=='-LP') return $this->StdDecodePeerId(substr($peer_id,4,4),"Lphant"); # Lphant
		if(substr($peer_id,0,3)=='-MP') return $this->StdDecodePeerId(substr($peer_id,3,7),"MooPolice"); # MooPolice
		if(substr($peer_id,0,3)=='-MT') return $this->StdDecodePeerId(substr($peer_id,3,7),"Moonlight"); # MoonlightTorrent
		if(substr($peer_id,0,3)=='-PO') return $this->StdDecodePeerId(substr($peer_id,3,7),"PO Client"); #unidentified clients with versions
		if(substr($peer_id,0,3)=='-QT') return $this->StdDecodePeerId(substr($peer_id,3,7),"Qt 4 Torrent"); # Qt 4 Torrent
		if(substr($peer_id,0,3)=='-RT') return $this->StdDecodePeerId(substr($peer_id,3,7),"Retriever"); # Retriever
		if(substr($peer_id,0,3)=='-S2') return $this->StdDecodePeerId(substr($peer_id,3,7),"S2 Client"); #unidentified clients with versions
		if(substr($peer_id,0,3)=='-SB') return $this->StdDecodePeerId(substr($peer_id,3,7),"Swiftbit"); # Swiftbit
		if(substr($peer_id,0,3)=='-SN') return $this->StdDecodePeerId(substr($peer_id,3,7),"ShareNet"); # ShareNet
		if(substr($peer_id,0,3)=='-SS') return $this->StdDecodePeerId(substr($peer_id,3,7),"SwarmScope"); # SwarmScope
		if(substr($peer_id,0,3)=='-SZ') return $this->StdDecodePeerId(substr($peer_id,3,7),"Shareaza"); # Shareaza
		if(preg_match("/^RAZA ([0-9]+\.[0-9]+\.[0-9]+\.[0-9]+)/", $httpagent, $matches)) return "Shareaza $matches[1]";
		if(substr($peer_id,0,3)=='-TN') return $this->StdDecodePeerId(substr($peer_id,3,7),"Torrent.NET"); # Torrent.NET
		if(substr($peer_id,0,3)=='-TR') return $this->StdDecodePeerId(substr($peer_id,3,7),"Transmission"); # Transmission
		if(substr($peer_id,0,3)=='-TS') return $this->StdDecodePeerId(substr($peer_id,3,7),"TorrentStorm"); # Torrentstorm
		if(substr($peer_id,0,3)=='-UR') return $this->StdDecodePeerId(substr($peer_id,3,7),"UR Client"); # unidentified clients with versions
		if(substr($peer_id,0,3)=='-UT') return $this->StdDecodePeerId(substr($peer_id,3,7),"uTorrent"); # uTorrent
		if(substr($peer_id,0,3)=='-XT') return $this->StdDecodePeerId(substr($peer_id,3,7),"XanTorrent"); # XanTorrent
		if(substr($peer_id,0,3)=='-ZT') return $this->StdDecodePeerId(substr($peer_id,3,7),"ZipTorrent"); # ZipTorrent
		if(substr($peer_id,0,3)=='-bk') return $this->StdDecodePeerId(substr($peer_id,3,7),"BitKitten"); # BitKitten
		if(substr($peer_id,0,3)=='-lt') return $this->StdDecodePeerId(substr($peer_id,3,7),"libTorrent"); # libTorrent
		if(substr($peer_id,0,3)=='-pX') return $this->StdDecodePeerId(substr($peer_id,3,7),"pHoeniX"); # pHoeniX
		if(substr($peer_id,0,2)=='BG') return $this->StdDecodePeerId(substr($peer_id,2,4),"BTGetit"); # BTGetit
		if(substr($peer_id,2,2)=='BM') return $this->DecodeVersionString(substr($peer_id,0,2),"BitMagnet"); # BitMagnet
		if(substr($peer_id,0,2)=='OP') return $this->StdDecodePeerId(substr($peer_id,2,4),"Opera"); # Opera
		if(substr($peer_id,0,4)=='270-') return "GreedBT 2.7.0"; # GreedBT
		if(substr($peer_id,0,4)=='271-') return "GreedBT 2.7.1"; # GreedBT 2.7.1
		if(substr($peer_id,0,4)=='346-') return "TorrentTopia"; # TorrentTopia
		if(substr($peer_id,0,3)=='-AR') return "Arctic Torrent"; # Arctic (no way to know the version)
		if(substr($peer_id,0,3)=='-G3') return "G3 Torrent"; # G3 Torrent
		if(substr($peer_id,0,6)=='BTDWV-') return "Deadman Walking"; # Deadman Walking
		if(substr($peer_id,5,7)=='Azureus') return "Azureus 2.0.3.2"; # Azureus 2.0.3.2
		if(substr($peer_id,0,8 )=='PRC.P---') return "BitTorrent Plus! II"; # BitTorrent Plus! II
		if(substr($peer_id,0,8 )=='S587Plus') return "BitTorrent Plus!"; # BitTorrent Plus!
		if(substr($peer_id,0,7)=='martini') return "Martini Man"; # Martini Man
		if(substr($peer_id,4,6)=='btfans') return "SimpleBT"; # SimpleBT
		if(substr($peer_id,3,9)=='SimpleBT?') return "SimpleBT"; # SimpleBT
		if(preg_match("/MFC_Tear_Sample/", preg_quote($httpagent))) return "SimpleBT";
		if(substr($peer_id,0,5)=='btuga') return "BTugaXP"; # BTugaXP
		if(substr($peer_id,0,5)=='BTuga') return "BTuga"; # BTugaXP
		if(substr($peer_id,0,5)=='oernu') return "BTugaXP"; # BTugaXP
		if(substr($peer_id,0,10)=='DansClient') return "XanTorrent"; # XanTorrent
		if(substr($peer_id,0,16)=='Deadman Walking-') return "Deadman"; # Deadman client
		if(substr($peer_id,0,8 )=='XTORR302') return "TorrenTres 0.0.2"; # TorrenTres
		if(substr($peer_id,0,7)=='turbobt') return "TurboBT ".(substr($peer_id,7,5)); # TurboBT
		if(substr($peer_id,0,7)=='a00---0') return "Swarmy"; # Swarmy
		if(substr($peer_id,0,7)=='a02---0') return "Swarmy"; # Swarmy
		if(substr($peer_id,0,7)=='T00---0') return "Teeweety"; # Teeweety
		if(substr($peer_id,0,7)=='rubytor') return "Ruby Torrent v".ord($peer_id[7]); # Ruby Torrent
		if(substr($peer_id,0,5)=='Mbrst') return $this->MainlineDecodePeerId(substr($peer_id,5,5),"burst!"); # burst!
		if(substr($peer_id,0,4)=='btpd') return "BT Protocol Daemon ".(substr($peer_id,5,3)); # BT Protocol Daemon
		if(substr($peer_id,0,8 )=='XBT022--') return "BitTorrent Lite"; # BitTorrent Lite based on XBT code
		if(substr($peer_id,0,3)=='XBT') return $this->StdDecodePeerId(substr($peer_id,3,3), "XBT"); # XBT Client
		if(substr($peer_id,0,4)=='-BOW') return $this->StdDecodePeerId(substr($peer_id,4,5),"Bits on Wheels"); # Bits on Wheels
		if(substr($peer_id,1,2)=='ML') return $this->MainlineDecodePeerId(substr($peer_id,3,5),"MLDonkey"); # MLDonkey
		if(substr($peer_id,0,8 )=='AZ2500BT') return "AzureusBitTyrant 1.0/1";
		if($peer_id[0]=='A') return $this->StdDecodePeerId(substr($peer_id,1,9),"ABC"); # ABC
		if($peer_id[0]=='R') return $this->StdDecodePeerId(substr($peer_id,1,5),"Tribler"); # Tribler
		if($peer_id[0]=='M'){
			if(preg_match("/^Python/", $httpagent, $matches)) return "Spoofing BT Client"; # Spoofing BT Client
			return $this->MainlineDecodePeerId(substr($peer_id,1,7),"Mainline"); # Mainline BitTorrent with version
		}
		if($peer_id[0]=='O') return $this->StdDecodePeerId(substr($peer_id,1,9),"Osprey Permaseed"); # Osprey Permaseed
		if($peer_id[0]=='S'){
			if(preg_match("/^BitTorrent\/3.4.2/", $httpagent, $matches)) return "Spoofing BT Client"; # Spoofing BT Client
			return $this->StdDecodePeerId(substr($peer_id,1,9),"Shad0w"); # Shadow's client
		}
		if($peer_id[0]=='T'){
			if(preg_match("/^Python/", $httpagent, $matches)) return "Spoofing BT Client"; # Spoofing BT Client
			return $this->StdDecodePeerId(substr($peer_id,1,9),"BitTornado"); # BitTornado
		}
		if($peer_id[0]=='U') return $this->StdDecodePeerId(substr($peer_id,1,9),"UPnP"); # UPnP NAT Bit Torrent
		# Azureus / Localhost
		if(substr($peer_id,0,3)=='-AZ') {
			if(preg_match("/^Localhost ([0-9]+\.[0-9]+\.[0-9]+)/", $httpagent, $matches)) return "Localhost $matches[1]";
			if(preg_match("/^BitTorrent\/3.4.2/", $httpagent, $matches)) return "Spoofing BT Client"; # Spoofing BT Client
			if(preg_match("/^Python/", $httpagent, $matches)) return "Spoofing BT Client"; # Spoofing BT Client
			return $this->StdDecodePeerId(substr($peer_id,3,7),"Azureus");
		}
		if(ereg("Azureus", $peer_id)) return "Azureus 2.0.3.2";
		# BitComet/BitLord/BitVampire/Modded FUTB BitComet
		if(substr($peer_id,0,4)=='exbc' || substr($peer_id,1,3)=='UTB'){
			if(substr($peer_id,0,4)=='FUTB') return $this->DecodeVersionString(substr($peer_id,4,2),"BitComet Mod1");
			elseif(substr($peer_id,0,4)=='xUTB') return $this->DecodeVersionString(substr($peer_id,4,2),"BitComet Mod2");
			elseif(substr($peer_id,6,4)=='LORD') return $this->DecodeVersionString(substr($peer_id,4,2),"BitLord");
			elseif(substr($peer_id,6,3)=='---' && DecodeVersionString(substr($peer_id,4,2),"BitComet")=='BitComet 0.54') return "BitVampire";
			else return $this->DecodeVersionString(substr($peer_id,4,2),"BitComet");
		}
		# Rufus
		if(substr($peer_id,2,2)=='RS'){
			for ($i=0; $i<=strlen(substr($peer_id,4,9)); $i++){
			$c = $peer_id[$i+4];
			if (ctype_alnum($c) || $c == chr(0)) $rufus_chk = true;
			else break;
		}
		if ($rufus_chk) return $this->DecodeVersionString(substr($peer_id,0,2),"Rufus"); # Rufus
		}
		# BitSpirit
		if(substr($peer_id,14,6)=='HTTPBT' || substr($peer_id,16,4)=='UDP0') {
			if(substr($peer_id,2,2)=='BS') {
				if($peer_id[1]==chr(0)) return "BitSpirit v1";
				if($peer_id[1]== chr(2)) return "BitSpirit v2";
			}
			return "BitSpirit";
		}
		#BitSpirit
		if(substr($peer_id,2,2)=='BS') {
			if($peer_id[1]==chr(0)) return "BitSpirit v1";
			if($peer_id[1]==chr(2)) return "BitSpirit v2";
			return "BitSpirit";
		}
		# eXeem beta
		if(substr($peer_id,0,3)=='-eX') {
			$version_str = "";
			$version_str .= intval($peer_id[3],16).".";
			$version_str .= intval($peer_id[4],16);
			return "eXeem $version_str";
		}
		if(substr($peer_id,0,2)=='eX') return "eXeem"; # eXeem beta .21
		if(substr($peer_id,0,12)==(chr(0)*12) && $peer_id[12]==chr(97) && $peer_id[13]==chr(97)) return "Experimental 3.2.1b2"; # Experimental 3.2.1b2
		if(substr($peer_id,0,12)==(chr(0)*12) && $peer_id[12]==chr(0) && $peer_id[13]==chr(0)) return "Experimental 3.1"; # Experimental 3.1
		return "Unknown client";
	}
		
}
?>
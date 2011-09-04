<?php

defined('XOOPS_ROOT_PATH') or die("XOOPS root path not defined");

class TbUsers extends XoopsObject
{
	var $g_rep_hide = 0;
	
    function __construct()
    {
        $this->initVar('id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('uid', XOBJ_DTYPE_INT, null, false);
        $this->initVar('username', XOBJ_DTYPE_TXTBOX, null, false, 40);
        $this->initVar('passhash', XOBJ_DTYPE_TXTBOX, null, false, 32);
        $this->initVar('secret', XOBJ_DTYPE_TXTBOX, null, false, 20);
        $this->initVar('passkey', XOBJ_DTYPE_TXTBOX, null, false, 32);
        $this->initVar('email', XOBJ_DTYPE_TXTBOX, null, false, 80);
        $this->initVar('status', XOBJ_DTYPE_ENUM, null, false, false, false, array('pending','confirmed'));
        $this->initVar('added', XOBJ_DTYPE_INT, null, false);
        $this->initVar('last_login', XOBJ_DTYPE_INT, null, false);
        $this->initVar('last_access', XOBJ_DTYPE_INT, null, false);
        $this->initVar('editsecret', XOBJ_DTYPE_TXTBOX, null, false, 32);
        $this->initVar('privacy', XOBJ_DTYPE_ENUM, null, false, false, false, array('strong','normal','low'));
        $this->initVar('stylesheet', XOBJ_DTYPE_INT, null, false);
        $this->initVar('info', XOBJ_DTYPE_OTHER, null, false);
        $this->initVar('acceptpms', XOBJ_DTYPE_ENUM, null, false, false, false, array('yes','friends','no'));
        $this->initVar('ip', XOBJ_DTYPE_TXTBOX, null, false, 60);
        $this->initVar('hits', XOBJ_DTYPE_INT, null, false);
        $this->initVar('class', XOBJ_DTYPE_INT, null, false);
        $this->initVar('language', XOBJ_DTYPE_TXTBOX, null, false, 32);
        $this->initVar('avatar', XOBJ_DTYPE_TXTBOX, null, false, 100);
        $this->initVar('av_w', XOBJ_DTYPE_INT, null, false);
        $this->initVar('av_h', XOBJ_DTYPE_INT, null, false);
        $this->initVar('uploaded', XOBJ_DTYPE_INT, null, false);
        $this->initVar('downloaded', XOBJ_DTYPE_INT, null, false);
        $this->initVar('title', XOBJ_DTYPE_TXTBOX, null, false, 30);
        $this->initVar('country', XOBJ_DTYPE_INT, null, false);
        $this->initVar('notifs', XOBJ_DTYPE_ARRAY, null, false, 100);
        $this->initVar('modcomment', XOBJ_DTYPE_OTHER, null, false);
        $this->initVar('enabled', XOBJ_DTYPE_ENUM, null, false, false, false, array('yes', 'no'));
        $this->initVar('avatars', XOBJ_DTYPE_ENUM, null, false, false, false, array('yes', 'no'));
        $this->initVar('donor', XOBJ_DTYPE_ENUM, null, false, false, false, array('yes', 'no'));
        $this->initVar('warned', XOBJ_DTYPE_ENUM, null, false, false, false, array('yes', 'no'));
        $this->initVar('warneduntil', XOBJ_DTYPE_INT, null, false);
        $this->initVar('torrentsperpage', XOBJ_DTYPE_INT, null, false);
        $this->initVar('topicsperpage', XOBJ_DTYPE_INT, null, false);
        $this->initVar('postsperpage', XOBJ_DTYPE_INT, null, false);
        $this->initVar('deletepms', XOBJ_DTYPE_ENUM, null, false, false, false, array('yes', 'no'));
        $this->initVar('savepms', XOBJ_DTYPE_ENUM, null, false, false, false, array('yes', 'no'));
        $this->initVar('reputation', XOBJ_DTYPE_INT, null, false);
        $this->initVar('time_offset', XOBJ_DTYPE_TXTBOX, null, false, 5);
        $this->initVar('dst_in_use', XOBJ_DTYPE_INT, null, false);
        $this->initVar('auto_correct_dst', XOBJ_DTYPE_INT, null, false);
        
    }

    function TbUsers()
    {
        $this->__construct();
    }

    function toArray(){
    	$ret = parent::toArray();
    	$user_handler = xoops_gethandler('user');
    	$user = $user_handler->get($this->getVar('uid'));
    	if (is_object($user))
    		return array_merge($ret, $user->toArray());
    	else
    		return $ret;
    }

    function getProfileForm() {
    	include_once($GLOBALS['xoops']->path('/modules/tb/include/form.objects.php'));
        xoops_loadLanguage('forms', 'tb');
        
       	$frmobj = array();
        $frmobj['name'] = new XoopsFormText(_TBDEV_FRM_TORRENT_NAME, 'name', 35, 255, $this->getVar('name') );
	    $frmobj['name']->setDescription(_TBDEV_FRM_TORRENT_NAME_DESC);
	    	
    	if ($this->isNew()) {
    		$form = new XoopsThemeForm(_TBDEV_FRM_NEW_USER, 'profile', $_SERVER['PHP_SELF'], 'post');
    	} else {
    		$form = new XoopsThemeForm(_TBDEV_FRM_EDIT_USER, 'profile', XOOPS_URL.'/modules/tb/takeprofedit.php', 'post');
    	}
 	    
    	$frmobj['acceptpms'] = new XoopsFormRadio(_TBDEV_FRM_USER_ACCEPTPMS, 'acceptpms', $this->getVar('acceptpms'));
	    $frmobj['acceptpms']->setDescription(_TBDEV_FRM_USER_ACCEPTPMS_DESC);
	    $frmobj['acceptpms']->addOption('yes', _TBDEV_FRM_USER_ACCEPTPMS_YES);
	    $frmobj['acceptpms']->addOption('friends', _TBDEV_FRM_USER_ACCEPTPMS_FRIEND);
	    $frmobj['acceptpms']->addOption('no', _TBDEV_FRM_USER_ACCEPTPMS_NO);

	    $frmobj['deletepms'] = new XoopsFormRadio(_TBDEV_FRM_USER_DELETEPMS, 'deletepms', $this->getVar('deletepms'));
	    $frmobj['deletepms']->setDescription(_TBDEV_FRM_USER_DELETEPMS_DESC);
	    $frmobj['deletepms']->addOption('yes', _TBDEV_FRM_USER_DELETEPMS_YES);
	    
	    $frmobj['savepms'] = new XoopsFormRadio(_TBDEV_FRM_USER_SAVEPMS, 'savepms', $this->getVar('savepms'));
	    $frmobj['savepms']->setDescription(_TBDEV_FRM_USER_SAVEPMS_DESC);
	    $frmobj['savepms']->addOption('yes', _TBDEV_FRM_USER_SAVEPMS_YES);

	    $frmobj['notifs'] = new TbFormSelectCategory(_TBDEV_FRM_USER_NOTIFS, 'notifs', $this->getVar('notifs'), 10, true);
	    $frmobj['notifs']->setDescription(_TBDEV_FRM_USER_NOTIFS_DESC);
	    
	    $frmobj['language'] = new TbFormSelectLanguage(_TBDEV_FRM_USER_LANGUAGE, 'language', $this->getVar('language'));
	    $frmobj['language']->setDescription(_TBDEV_FRM_USER_LANGAUGE_DESC);
	    
	    $frmobj['stylesheet'] = new TbFormSelectStylesheets(_TBDEV_FRM_USER_STYLESHEET, 'stylesheet', $this->getVar('stylesheet'));
	    $frmobj['stylesheet']->setDescription(_TBDEV_FRM_USER_STYLESHEET_DESC);
	    
	    $frmobj['time_offset'] = new TbFormSelectTimezone(_TBDEV_FRM_USER_TIMEZONE, 'time_offset', $this->getVar('time_offset'));
	    $frmobj['time_offset']->setDescription(_TBDEV_FRM_USER_TIMEZONE_DESC);
	    
    	$info_configs = array();
		$info_configs['name'] = 'info';
		$info_configs['value'] = $this->getVar('info');
		$info_configs['rows'] = 35;
		$info_configs['cols'] = 60;
		$info_configs['width'] = "100%";
		$info_configs['height'] = "400px";
		$info_configs['editor'] = $GLOBALS['xoopsModuleConfig']['editor'];
		$frmobj['descr'] = new XoopsFormEditor(_TBDEV_FRM_USER_INFO, $info_configs['name'], $info_configs);
		$frmobj['descr']->setDescription(_TBDEV_FRM_USER_INFO_DESC);
    
		$frmobj['submit'] = new XoopsFormButton('', 'submit', _SUBMIT, 'submit');
		
		foreach($frmobj as $key => $value) {
    		$form->addElement($frmobj[$key], false);
    	}
    	
    	return $form->render();	
    }
    
}

class TbUsersHandler extends XoopsPersistableObjectHandler
{

    function TbUsersHandler(&$db)
    {
        $this->__construct($db);
    }

    function __construct(&$db)
    {
        parent::__construct($db, "tb_users", 'TbUsers', "id");
    }

    function mksecret($len=19) {
		$salt = '';
		mt_srand(time());
		for ( $i = 0; $i < $len; $i++ )
		{
			$num   = mt_rand(33, 126);
			if ( $num == '92' ) {
				$num = 93;
			}
			$salt .= chr( $num );
		}
		return $salt;
	}
	

	function make_passhash_login_key($len=60) {
		$pass = $this->mksecret( $len );
		return md5($pass);
	}
	

	function make_passhash($salt, $md5_once_password) {
		return md5( md5( $salt ) . $md5_once_password );
	}

	function make_password() {
		$pass = "";
		$unique_id 	= uniqid( mt_rand(), TRUE );
		$prefix		= mksecret();
		$unique_id .= md5( $prefix );
		usleep( mt_rand(15000,1000000) );
		mt_srand( (float)str_replace(' ', '', microtime())*1000000 );
		$new_uniqueid = uniqid( mt_rand(), TRUE );
		$final_rand = md5( $unique_id.$new_uniqueid );
		mt_srand((float)str_replace(' ', '', microtime()));
		for ($i = 0; $i < 15; $i++)
		{
			$pass .= $final_rand{ mt_rand(0, 31) };
		}
		return $pass;
	}
	
	function get_reputation($user, $mode = 0, $rep_is_on = TRUE) {
		$member_reputation = "";
		if( $rep_is_on )	{
			@include $GLOBALS['xoops']->path('/modules/tb/cache/rep_cache.php');
			// ok long winded file checking, but it's much better than file_exists
			if( ! isset( $reputations ) || ! is_array( $reputations ) || count( $reputations ) < 1)	{
				return '<span title="Cache doesn\'t exist or zero length">Reputation: Offline</span>';
			}
			
			$user->g_rep_hide = (isset( $user->g_rep_hide ) && !empty($user->g_rep_hide))? $user->g_rep_hide : 0;
	
			$max_rep = max(array_keys($reputations));
			if($user->getVar('reputation') >= $max_rep)	{
				$user_reputation = $reputations[$max_rep];
			} else {
				foreach($reputations as $y => $x) {
					if( $y > $user->getVar('reputation') ) { 
						$user_reputation = $old; 
						break;
					}
				}
				$old = $x;
			}
			
			$rep_power = $user->getVar('reputation');
			$posneg = '';
			if( $user->getVar('reputation') == 0 ) {
				$rep_img   = 'balance';
				$rep_power = $user->getVar('reputation') * -1;
			} elseif( $user->getVar('reputation') < 0 )	{
				$rep_img   = 'neg';
				$rep_img_2 = 'highneg';
				$rep_power = $user->getVar('reputation') * -1;
			} else {
				$rep_img   = 'pos';
				$rep_img_2 = 'highpos';
			}

			if( $rep_power > 500 ) {
				$rep_power = ( $rep_power - ($rep_power - 500) ) + ( ($rep_power - 500) / 2 );
			}

			$rep_bar = intval($rep_power / 100);
			if( $rep_bar > 10 )	{
				$rep_bar = 10;
			}
			if( $user['g_rep_hide'] ) {
				$posneg = 'off';
				$rep_level = 'rep_off';
			} else	{ 
				$rep_level = $user_reputation ? $user_reputation : 'rep_undefined';// just incase
				for( $i = 0; $i <= $rep_bar; $i++ )	{
					if( $i >= 5 ) {
						$posneg .= "<img src='".XOOPS_URL."/modules/tb/images/rep/reputation_$rep_img_2.gif' border='0' alt=\"Reputation Power $rep_power\n".$user->getVar('username')." $rep_level\" title=\"Reputation Power $rep_power ".$user->getVar('username')." $rep_level\" />";
					} else	{
						$posneg .= "<img src='".XOOPS_URL."/modules/tb/images/rep/reputation_$rep_img.gif' border='0' alt=\"Reputation Power $rep_power\n".$user->getVar('username')." $rep_level\" title=\"Reputation Power $rep_power ".$user->getVar('username')." $rep_level\" />";
					}
				}
			}
			
			// now decide if we in a forum or statusbar?
			if( $mode === 0 )
				return "Rep: ".$posneg . "<br /><a href='javascript:;' onclick=\"PopUp('{$GLOBALS['TBDEV']['baseurl']}/reputation.php?pid={$user['id']}','Reputation',400,241,1,1);\"><img src='".XOOPS_URL."/modules/tb/images/plus.gif' border='0' alt='Add reputation:: ".$user->getVar('username')."' title='Add reputation:: ".$user->getVar('username')."' /></a>";
			else
				return "Rep: ".$posneg;
			
		} 
		return '<span title="Set offline by admin setting">Rep System Offline</span>';
	}
	
	function get_user_icons($user, $big = false) {
	        
	    if ($big) {
	      $donorpic = "starbig.gif";
	      $warnedpic = "warnedbig.gif";
	      $disabledpic = "disabledbig.gif";
	      $style = "style='margin-left: 4pt'";
	    } else {
	      $donorpic = "star.gif";
	      $warnedpic = "warned.gif";
	      $disabledpic = "disabled.gif";
	      $style = "style=\"margin-left: 2pt\"";
	    }
	    $pics = $user->getVar("donor") == "yes" ? "<img src=\"{$GLOBALS['xoopsModuleConfig']['pic_base_url']}{$donorpic}\" alt='Donor' border='0' $style />" : "";
	    if ($arr["enabled"] == "yes")
	      $pics .= $user->getVar("warned") == "yes" ? "<img src=\"{$GLOBALS['xoopsModuleConfig']['pic_base_url']}{$warnedpic}\" alt=\"Warned\" border='0' $style />" : "";
	    else
	      $pics .= "<img src=\"{$GLOBALS['xoopsModuleConfig']['pic_base_url']}{$disabledpic}\" alt=\"Disabled\" border='0' $style />\n";
	    return $pics;
	}
	
	function get_ratio_color($ratio) {
		foreach(explode('|', $GLOBALS['xoopsModuleConfig']['ratio_colours']) as $value) {
			$details = explode('-', $value);
			if (isset($details[0])&&isset($details[1]))
				if ($ratio < $details[0]) 
					return $details[1];
		}
		return $GLOBALS['xoopsModuleConfig']['default_ratio_colour'];
	}
	
	function get_slr_color($ratio) {
		foreach(explode('|', $GLOBALS['xoopsModuleConfig']['slr_colours']) as $value) {
			$details = explode('-', $value);
			if (isset($details[0])&&isset($details[1]))
				if ($ratio < $details[0]) 
					return $details[1];
		}
		return $GLOBALS['xoopsModuleConfig']['default_slr_colour'];
	}
	
	
	function get_user_class()
	{
		if (isset($GLOBALS['CURUSER']["class"]))
	    	return $GLOBALS['CURUSER']["class"];
	    else 
	    	return UC_USER;
	}
	
	function get_user_class_name($class)
	{
		switch ($class) {
	    case UC_USER: 
	    	return _MI_TBDEV_UC_USER;
	    case UC_POWER_USER: 
	    	return _MI_TBDEV_UC_POWER_USER;
	    case UC_VIP: 
	    	return _MI_TBDEV_UC_VIP;
	    case UC_UPLOADER: 
	    	return _MI_TBDEV_UC_UPLOADER;
	    case UC_MODERATOR: 
	    	return _MI_TBDEV_UC_MODERATOR;
	    case UC_ADMINISTRATOR: 
	    	return _MI_TBDEV_UC_ADMINISTRATOR;
	    case UC_SYSOP: 
	    	return _MI_TBDEV_UC_SYSOP;
	  }
	  return "";
	}
    
	function validip($ip) {
		if (!empty($ip) && $ip == long2ip(ip2long($ip))) {
			foreach(explode('|', $GLOBALS['xoopsModuleConfig']['reserved_ips']) as $key => $value) {
				$ips = explode('-', $value);
				if (isset($ips[0])&&isset($ips[1]))
					if ((ip2long($ip) >= ip2long($ips[0])) && (ip2long($ip) <= ip2long($ips[1]))) 
						return false;
					
			}
		} else
		 	return false;
		return true;
	}

	function getip() {
	   	if (isset($_SERVER)) {
	     	if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $this->validip($_SERVER['HTTP_X_FORWARDED_FOR'])) {
	       		$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	     	} elseif (isset($_SERVER['HTTP_CLIENT_IP']) && $this->validip($_SERVER['HTTP_CLIENT_IP'])) {
	       		$ip = $_SERVER['HTTP_CLIENT_IP'];
	     	} else {
	       		$ip = $_SERVER['REMOTE_ADDR'];
	     	}
	   	} else {
	     	if (xoops_getenv('HTTP_X_FORWARDED_FOR') && $this->validip(xoops_getenv('HTTP_X_FORWARDED_FOR'))) {
	       		$ip = xoops_getenv('HTTP_X_FORWARDED_FOR');
	     	} elseif (xoops_getenv('HTTP_CLIENT_IP') && $this->validip(xoops_getenv('HTTP_CLIENT_IP'))) {
	       		$ip = xoops_getenv('HTTP_CLIENT_IP');
	     	} else {
	       		$ip = xoops_getenv('REMOTE_ADDR');
	     	}
	   	}
	
	   	return $ip;
	}
	
	function userlogin() {
	    
	    unset($GLOBALS["CURUSER"]);
	
	    $ip = $this->getip();
		$nip = ip2long($ip);
	
		@include $GLOBALS['xoops']->path('/modules/tb/cache/bans_cache.php');
		
	    if(count($bans) > 0) {
	    	foreach($bans as $k) {
	        	if($nip >= $k['first'] && $nip <= $k['last']) {
	        		header("HTTP/1.0 403 Forbidden");
	        		print "<html><body><h1>403 Forbidden</h1>Unauthorized IP address.</body></html>\n";
	        		exit();
	        	}
	      	}
	      	unset($bans);
	    }
	    
	    if ( !$GLOBALS['TBDEV']['site_online'] || !is_object($GLOBALS['xoopsUser']) ) {
	    	return;
	    }
			
		if (!get_mycookie('tbuid') && is_object($GLOBALS['xoopsUser'])) {
			$criteria = new Criteria('uid',$GLOBALS['xoopsUser']->getVar('uid'));
			$users = $this->getObjects($criteria, false);
			if (!is_object($users[0])) {
		    	$lang = array_merge( load_language('global'), load_language('takesignup') );
		    
			    if ($users_handler->getCount() >= $GLOBALS['TBDEV']['maxusers'])
			      stderr($lang['takesignup_error'], $lang['takesignup_limit']);
			      
		        $secret = $this->mksecret();
			    $wantpasshash = $this->make_passhash( $secret, $GLOBALS['xoopsUser']->getVar('pass') );
			    $editsecret = ( !$this->getCount() ? "" : $this->make_passhash_login_key() );
		
			    $users[0] = $users_handler->create();
			    $users[0]->setVars('username', $GLOBALS['xoopsUser']->getVar('uname'));
			    $users[0]->setVars('uid', $GLOBALS['xoopsUser']->getVar('uid'));
			    $users[0]->setVars('passhash', $wantpasshash);
			    $users[0]->setVars('passkey', md5($wantpasshash.$secret.XOOPS_LICENSE_KEY.microtime()));
			    $users[0]->setVars('secret', $secret);
			    $users[0]->setVars('editsecret', $editsecret);
			    $users[0]->setVars('email', $GLOBALS['xoopsUser']->getVar('email'));
			    $users[0]->setVars('status', 'confirmed');
			    $users[0]->setVars('class', ($GLOBALS['xoopsUser']->isAdmin()?UC_ADMINISTRATOR:UC_USER));
			    $users[0]->setVars('added', time());
			    $users[0]->setVars('time_offset', $time_offset);
			    $users[0]->setVars('dst_in_use', $dst_in_use['tm_isdst']);
			    $users[0]->setVars('enabled', 'yes');
		
			    if (!$users[0]= $users_handler->get($users_handler->insert($users[0], true)))  {
			    	stderr($lang['takesignup_user_error'], $lang['takesignup_fatal_error']);
			    }
		    } else {
			    $users[0]->setVars('username', $GLOBALS['xoopsUser']->getVar('uname'));
				$users[0]->setVars('passhash', $this->make_passhash( $users[0]->getVar('secret'), $GLOBALS['xoopsUser']->getVar('pass') ));
			    $users[0]->setVars('email', $GLOBALS['xoopsUser']->getVar('email'));
			    $users[0]->setVars('last_access', time());
			    $this->insert($users[0], true);	    		
	   	    }
		} else {
			$criteria = new Criteria('id', get_mycookie('tbuid'));
			$users = $this->getObjects($criteria, false);
						
		    if ($users[0]->getVar('enabled') == 'no')
		      stderr($lang['tlogin_failed'], $lang['tlogin_disabled']);
		
			$users[0]->setVars('username', $GLOBALS['xoopsUser']->getVar('uname'));
			$users[0]->setVars('passhash', $this->make_passhash( $users[0]->getVar('secret'), $GLOBALS['xoopsUser']->getVar('pass') ));
			$users[0]->setVars('email', $GLOBALS['xoopsUser']->getVar('email'));
			$users[0]->setVars('last_access', time());
			$this->insert($users[0], true);	    						
		} 
		
		if (is_object($users[0])) {
			logincookie($users[0]->getVar('id'), $users[0]->getVar('passhash'));		
			$GLOBALS["CURUSER"] = $users[0]->toArray();
			$GLOBALS["CURUSER"]['object'] = $users[0];
		} 

	}
		
}
?>
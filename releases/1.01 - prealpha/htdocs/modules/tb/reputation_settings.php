<?php
/*
+------------------------------------------------
|   TBDev.net BitTorrent Tracker PHP
|   =============================================
|   by CoLdFuSiOn
|   (c) 2003 - 2009 TBDev.Net
|   http://www.tbdev.net
|   =============================================
|   svn: http://sourceforge.net/projects/tbdevnet/
|   Licence Info: GPL
+------------------------------------------------
|   $Date$
|   $Revision$
|   $Author$
|   $URL$
+------------------------------------------------
*/
require_once "include/bittorrent.php";
require_once "include/user_functions.php";


dbconn( false );

loggedinorreturn();

	$GLOBALS['lang'] = load_language('global');
	
	if ( get_user_class() < UC_ADMINISTRATOR )
		header( "Location: {$GLOBALS['TBDEV']['baseurl']}/index.php" );
		
		
$rep_set_cache = "cache/rep_settings_cache.php";

	if ( 'POST' == $_SERVER['REQUEST_METHOD'] )
	{
	unset($_POST['submit']);
	//print_r($_POST);
	rep_cache();
	exit;
	}
	
/////////////////////////////
//	cache rep function
/////////////////////////////
function rep_cache()
	{
		
		global $rep_set_cache;
		
		$rep_out = "<"."?php\n\n\$GVARS = array(\n";
		
		foreach( $_POST as $k => $v)
		{
			$rep_out .= ($k == 'rep_undefined') ? "\t'{$k}' => '".htmlentities($v, ENT_QUOTES)."',\n" : "\t'{$k}' => ".intval($v).",\n";
		}
		
		$rep_out .= "\t'g_rep_negative' => TRUE,\n";
		$rep_out .=	"\t'g_rep_seeown' => TRUE,\n";
		$rep_out .= "\t'g_rep_use' => \$GLOBALS['CURUSER']['class'] > UC_USER ? TRUE : FALSE\n";
		$rep_out .= "\n);\n\n?".">";
		
		if( file_exists( $rep_set_cache ) && is_writable( pathinfo($rep_set_cache, PATHINFO_DIRNAME) ) )
		{
			$filenum = fopen ( $rep_set_cache, 'w' );
			ftruncate( $filenum, 0 );
			fwrite( $filenum, $rep_out );
			fclose( $filenum );
			//print '<pre>'.$rep_out.'</pre>';exit;
		}
		
		redirect('reputation_settings.php', 'Reputation Settings Have Been Updated!', 3);
	}
	
		
function get_cache_array() 
	{
		return array(	'rep_is_online' => 1,
						'rep_adminpower' => 5,
						'rep_minpost' => 50,
						'rep_default' => 10,
						'rep_userrates' => 5,
						'rep_rdpower' => 365,
						'rep_pcpower' => 1000,
						'rep_kppower' => 100,
						'rep_minrep' => 10,
						'rep_minpost' => 50,
						'rep_maxperday' => 10,
						'rep_repeat' => 20,
						'rep_undefined' => 'is off the scale',
						/*'g_rep_negative' => TRUE,
						'g_rep_seeown' => TRUE,
						'g_rep_use' => $GLOBALS['CURUSER']['class'] > UC_USER ? TRUE : FALSE*/
					);
	}
	
	
	if ( ! file_exists( $rep_set_cache ) )
	{
		$GVARS = get_cache_array();
	}
	else
	{
		require_once $rep_set_cache;
		
		if( ! is_array($GVARS) || ( count($GVARS) < 15 ) )
		{	
			$GVARS = get_cache_array();
		}
	}
	


$xoopsOption['template_main'] = 'tb_reputation_settings.html';
include $GLOBALS['xoops']->path('header.php');
$GLOBALS['xoopsTpl']->assign('xoConfig', $GLOBALS['TBDEV']);
$GLOBALS['xoopsTpl']->assign('gvars', $GLOBALS['GVARS']);
$GLOBALS['xoopsTpl']->assign('xoops_pagetitle', "Reputation Settings");
include $GLOBALS['xoops']->path('footer.php');

function redirect($url, $text, $time=2)
	{
		
		redirect_header($GLOBALS['TBDEV']['baseurl'].'/'.$url, $time, $text);
		
		$page_title  = "Admin Rep Redirection";
		$page_detail = "<em>Redirecting...</em>";
		
		$html = "<meta http-equiv='refresh' content=\"{$time}; url={$GLOBALS['TBDEV']['baseurl']}/{$url}\">
						    <div>
							<div>Redirecting</div>
							<div style='padding:8px'>
							 <div style='font-size:12px'>$text
							 <br />
							 <br />
							 <center><a href='{$GLOBALS['TBDEV']['baseurl']}/{$url}'>Click here if not redirected...</a></center>
							 </div>
							</div>
						   </div>";
		
		print $html;
		exit;
	}         
            

?>
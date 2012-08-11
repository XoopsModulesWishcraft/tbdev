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
require_once "../../mainfile.php";
require_once("include/bittorrent.php");
require_once "include/user_functions.php";
 
dbconn(false);


loggedinorreturn();

$GLOBALS['lang'] = load_language('reputation');

define('TIMENOW', time() ) ;

// mod or not?
$is_mod = ( $GLOBALS['CURUSER']['class'] >= UC_MODERATOR ) ? TRUE : FALSE;


//$GLOBALS['CURUSER']['class'] = 2;
//$rep_maxperday = 10;
//$rep_repeat = 20;
$closewindow = TRUE;

require_once "cache/rep_settings_cache.php";
//print_r($GVARS);

		if( ! $GVARS['rep_is_online'] )
		{
			exit($GLOBALS['lang']["info_reputation_offline"]);
		}

///////////////////////////////////////////////
//	Need only deal with one input value
///////////////////////////////////////////////

		if( isset( $_POST ) || isset( $_GET ) )
		{
		$input = array_merge( $_GET, $_POST );
		//print_r($input);
		//die;
		}

//$input['reputation'] = 'pos';
//$input['reason'] = 'la di da di di la';
///////////////////////////////////////////////		
//	Just added to Reputation?
///////////////////////////////////////////////
		if( isset( $input['done'] ) )
		{
			rep_output($GLOBALS['lang']["info_reputation_added"]);
		}

///////////////////////////////////////////////
//	Nope, so do something different, like check stuff
///////////////////////////////////////////////       
        $check = isset( $input['pid'] ) ? is_valid_id( $input['pid'] ) : FALSE;

		if( ! $check )
		{
			rep_output($GLOBALS['lang']["info_incorrect_access"]);
		}
		
///////////////////////////////////////////////
//	check the post actually exists!
///////////////////////////////////////////////
		$forum = $GLOBALS['xoopsDB']->queryF( "SELECT posts.topicid, posts.userid, forums.minclassread, 
								users.username, users.reputation 
								FROM posts 
								LEFT JOIN topics ON topicid = topics.id 
								LEFT JOIN forums ON topics.forumid = forums.id 
								LEFT JOIN ".$GLOBALS['xoopsDB']->prefix("tb_users")." ON posts.userid = users.id 
								WHERE posts.id ={$input['pid']}" );

		// does it or don't it?
		if( ! mysql_num_rows( $forum ) )
        {
        	rep_output($GLOBALS['lang']["info_invalid_post"]);
        }
///////////////////////////////////////////////
//	ok, lets proceed
///////////////////////////////////////////////
		$res = $GLOBALS['xoopsDB']->fetchArray( $forum ) or sqlerr(__LINE__,__FILE__);
		
		if( $GLOBALS['CURUSER']['class'] < $res['minclassread'] ) // check permissions! Dun want sneaky pests lookin!
		{
			rep_output($GLOBALS['lang']["info_wrong_permissions"]);
		}
		
///////////////////////////////////////////////
//	Does the user have memory loss? Have they already rep'd?
///////////////////////////////////////////////
		$repeat = $GLOBALS['xoopsDB']->queryF( "SELECT postid FROM ".$GLOBALS['xoopsDB']->prefix("reputation")." WHERE postid ={$input['pid']} 
						AND whoadded={$GLOBALS['CURUSER']['id']}" );

		//$repres = $GLOBALS['xoopsDB']->fetchArray( $forum ) or sqlerr(__LINE__,__FILE__);
		

		if( mysql_num_rows( $repeat) > 0 ) // blOOdy eedjit check!
		{
			rep_output($GLOBALS['lang']["info_already_added"]); // Is insane!
		}
		
///////////////////////////////////////////////
// 	Is a mod or gone over the limit?
///////////////////////////////////////////////
		if( ! $is_mod )
		{
			if( $GVARS['rep_maxperday'] >= $GVARS['rep_repeat'] )
			{
				$klimit = intval($GVARS['rep_maxperday'] + 1);
			}
			else
			{
				$klimit = intval($GVARS['rep_repeat'] + 1);
			}

///////////////////////////////////////////////
//	Some trivial flood checking
///////////////////////////////////////////////
			$flood = $GLOBALS['xoopsDB']->queryF( "SELECT dateadd, userid FROM ".$GLOBALS['xoopsDB']->prefix("reputation")." 
									WHERE whoadded = {$GLOBALS['CURUSER']['id']} 
									ORDER BY dateadd DESC
									LIMIT 0 , $klimit" );
			

			if( mysql_num_rows( $flood ) )
			{
				$i = 0;
				while( $check = $GLOBALS['xoopsDB']->fetchArray( $flood ) )
				{
					if( ( $i < $GVARS['rep_repeat'] ) && ( $check['userid'] == $GLOBALS['CURUSER']['id'] ) )//$res['userid'] ) )
					{
						rep_output($GLOBALS['lang']["info_cannot_rate_own"]);
					}
					if( ( ( $i + 1 ) == $GVARS['rep_maxperday'] ) && ( ( $check['dateadd'] + 86400 ) > TIMENOW ) )
					{
						rep_output($GLOBALS['lang']["info_daily_rep_limit_expired"]);
					}
					$i++;
				}
			}
		}

///////////////////////////////////////////////
//	Passed flood checkin, what to do now?
///////////////////////////////////////////////
		// Note: if you use another forum type, you may already have this GLOBAL available
		// So you can save a query here, else...
		$r = $GLOBALS['xoopsDB']->queryF("SELECT COUNT(*) FROM posts WHERE userid = {$GLOBALS['CURUSER']['id']}") or sqlerr();
		$a = mysql_fetch_row($r) or sqlerr();
		$GLOBALS['CURUSER']['posts'] = $a[0];
///////////////////////////////////////////////
// What's the reason for bothering me?
///////////////////////////////////////////////
		$reason = '';
		
		if( isset( $input['reason'] ) && !empty( $input['reason'] ) )
		{
			
			$reason = trim($input['reason']);

			$temp = stripslashes( $input['reason'] );
			if( ( strlen(trim($temp)) < 2 ) || ( $reason == "" ) )
			{
				rep_output($GLOBALS['lang']["info_reason_too_short"]);
			}

			if( strlen( preg_replace("/&#([0-9]+);/", "-", stripslashes( $input['reason'] ) ) ) > 250 )
			{
				rep_output( $GLOBALS['lang']["info_reason_too_long"]);
			}
		}
		
//$input['do'] = 'addrep';
//$input['reputation'] = 1;
///////////////////////////////////////////////
//	Are we adding a rep or what?
///////////////////////////////////////////////
		if( isset( $input['do'] ) && $input['do']  == 'addrep' )
		{
			if( $res['userid'] == $GLOBALS['CURUSER']['id'] ) // sneaky bastiges!
			{
				rep_output($GLOBALS['lang']["info_cannot_rate_own"]);
			}

			$score = fetch_reppower( $GLOBALS['CURUSER'], $input['reputation'] );
			$res['reputation'] += $score;

			@$GLOBALS['xoopsDB']->queryF( "UPDATE ".$GLOBALS['xoopsDB']->prefix("tb_users")." set reputation=".intval($res['reputation']). " WHERE id=" .$res['userid'] );

			$save = array( 'reputation' => $score,
						   'whoadded'   => $GLOBALS['CURUSER']['id'],
						   'reason'     => sqlesc($reason),
						   'dateadd'   => TIMENOW,
						   'postid'     => (int)$input['pid'],
						   'userid'     => $res['userid']
						);

			//print( join( ',', $save) );
			//print( join(',', array_keys($save)));
			@$GLOBALS['xoopsDB']->queryF( "INSERT INTO ".$GLOBALS['xoopsDB']->prefix("reputation")." (".join(',', array_keys($save)).") VALUES (".join( ',', $save).")" );

			header( "Location: {$GLOBALS['TBDEV']['baseurl']}/reputation.php?pid={$input['pid']}&done=1" );
		} // Move along, nothing to see here!
		else
		{
			if( $res['userid'] == $GLOBALS['CURUSER']['id'] ) // same as him!
			{
				// check for fish!
				$query = $GLOBALS['xoopsDB']->queryF( "select r.*, leftby.id as leftby_id, leftby.username as leftby_name 
										from ".$GLOBALS['xoopsDB']->prefix("reputation")." r 
										left join ".$GLOBALS['xoopsDB']->prefix("tb_users")." leftby on leftby.id=r.whoadded 
										where postid={$input['pid']} 
										order by dateadd DESC" );
										
				$reasonbits = '';
				
				if( false !== mysql_num_rows($query) )
				{
					$total = 0;
					
					while( $postrep = $GLOBALS['xoopsDB']->fetchArray($query) )
					{
						$total += $postrep['reputation'];

						if( $postrep['reputation'] > 0 )
						{
							$posneg = 'pos';
						}
						elseif( $postrep['reputation'] < 0 )
						{
							$posneg = 'neg';
						}
						else
						{
							$posneg = 'balance';
						}

						if( $GVARS['g_rep_seeown'] )
						{
							$postrep['reason'] = $postrep['reason']." <span class='desc'>{$GLOBALS['lang']["rep_let_by"]} <a href=\"{$GLOBALS['TBDEV']['baseurl']}/userdetails.php?id={$postrep['leftby_id']}\" target='_blank'>{$postrep['leftby_name']}</a></span>";
						}

						$reasonbits .= "<tr>
	<td class='row2' width='1%'><img src='./pic/rep/reputation_$posneg.gif' border='0' alt='' /></td>
	<td class='row2'>{$postrep['reason']}</td>
</tr>";
					}
///////////////////////////////////////////////
//	The negativity...oh such negativity
///////////////////////////////////////////////
					if( $total == 0 ){ $rep = $GLOBALS['lang']["rep_even"]; }
					elseif( $total > 0 && $total <= 5 ){ $rep = $GLOBALS['lang']["rep_somewhat_positive"]; }
					elseif( $total > 5 && $total <= 15 ){ $rep = $GLOBALS['lang']["rep_positive"]; }
					elseif( $total > 15 && $total <= 25 ){ $rep = $GLOBALS['lang']["rep_very_positive"]; }
					elseif( $total > 25 ){ $rep = $GLOBALS['lang']["rep_extremely_positive"]; }
					elseif( $total < 0 && $total >= -5 ){ $rep = $GLOBALS['lang']["rep_somewhat_negative"]; }
					elseif( $total < -5 && $total >= -15 ){ $rep = $GLOBALS['lang']["rep_negative"]; }
					elseif( $total < -15 && $total >= -25){ $rep = $GLOBALS['lang']["rep_very_negative"]; }
					elseif( $total < -25 ){ $rep = $GLOBALS['lang']["rep_extremely_negative"]; }
				}
				else
				{
					$rep = $GLOBALS['lang']["rep_even"]; //Ok, dunno what to do, so just make it quits!
				}
				
///////////////////////////////////////////////
//	Compile some HTML for the 'own post'/ 'user view' reputation
//	Feel free to do ya own html/css here
///////////////////////////////////////////////
				$rep_info = sprintf("".$GLOBALS['lang']["info_your_rep_on"]." <a href='{$GLOBALS['TBDEV']['baseurl']}/forums.php?action=viewtopic&amp;topicid=%d&amp;page=p%d#%d' target='_blank'>".$GLOBALS['lang']["info_this_post"]."</a> ".$GLOBALS['lang']["info_is"]." %s.", $res['topicid'], $input['pid'], $input['pid'], $rep );
				$rep_points = sprintf("".$GLOBALS['lang']["info_you_have"]." %d ".$GLOBALS['lang']["info_reputation_points"]."", $GLOBALS['CURUSER']['reputation'] );

				$html = "<tr><td class='darkrow1'>{$rep_info}</td></tr>
						<tr>
							<td class='row2'>
							<div class='tablepad'>";

					if ( $reasonbits )
					{
					
					$html .= "<fieldset class='fieldset'>
								<legend>{$GLOBALS['lang']["rep_comments"]}</legend>
								<table class='ipbtable' cellpadding='0'>
								$reasonbits
								</table>
							</fieldset><br />";
					
					}

						$html .=	"<div class='formsubtitle' align='center'><strong>{$rep_points}</strong></div>
						</div>
						</td>
					</tr>";
			}
			else
			{
				///////////////////////////////////////////////
				//	HTML/CSS for 'add reputaion'
				//	Feel free to alter HTML/CSS here
				///////////////////////////////////////////////
				$rep_text = sprintf("What do you think of %s&#39;s post?", $res['username']);
				$negativerep = ( $is_mod || $GVARS['g_rep_negative'] ) ? TRUE : FALSE;
				$closewindow = FALSE;

				$html = "<tr><td class='darkrow1'>{$GLOBALS['lang']["info_add_rep"]} <b>{$res['username']}</b></td></tr>
						<tr>
							<td class='row2'>
							<form action='reputation.php' method='post'>	
							<div class='tablepad'>
								<fieldset>
									<legend>$rep_text</legend>
									<table class='f_row' cellspacing='0'>
									<tr>
										<td>
											<div><label for='rb_reputation_pos'>
											<input type='radio' name='reputation' value='pos' id='rb_reputation_pos' checked='checked' class='radiobutton' style='margin:0px;' /> &nbsp;{$GLOBALS['lang']["rep_i_approve"]}</label></div>";
					if ( $negativerep )
					{
					$html .= "<div><label for='rb_reputation_neg'><input type='radio' name='reputation' value='neg' id='rb_reputation_neg' class='radiobutton' style='margin:0px;' /> &nbsp;{$GLOBALS['lang']["rep_i_disapprove"]}</label></div>";
					}
				$html .= "</td>
							</tr>
							<tr>
								<td>
									{$GLOBALS['lang']["rep_your_comm_on_this_post"]}<br />
									<input type='text' size='40' maxlength='250' name='reason' style='margin:0px;' />
								</td>
							</tr>
							</table>
						</fieldset>
					</div>
					<div align='center' style='margin-top:3px;'>
						<input type='hidden' name='act' value='reputation' />
						<input type='hidden' name='do' value='addrep' />
						<input type='hidden' name='pid' value='{$input['pid']}' />
						<input type='submit' value='".$GLOBALS['lang']["info_add_rep"]."' class='button' accesskey='s' />
						<input type='button' value='Close Window' class='button' accesskey='c' onclick='self.close()' />
					</div>	
					</form>	
					</td>
				</tr>";
			}

			rep_output( "", $html ); // send to spewer-outer function
		} // END
	

///////////////////////////////////////////////
//	Reputation output function
//	$msg -> string
//	$html -> string 
///////////////////////////////////////////////

	function rep_output($msg="", $html="")
	{
		$xoopsOption['template_main'] = 'tb_reputation.html';
		include $GLOBALS['xoops']->path('header.php');
		$GLOBALS['xoopsTpl']->assign('xoConfig', $GLOBALS['TBDEV']);
		$GLOBALS['xoopsTpl']->assign('msg', $msg);
		$GLOBALS['xoopsTpl']->assign('html', $html);
		$GLOBALS['xoopsTpl']->assign('closewindow', $GLOBALS['closewindow']);
		include $GLOBALS['xoops']->path('footer.php');
		exit();
		
	}

///////////////////////////////////////////////
//	Fetch Reputation function
//	$user -> array all about the user
//	$rep -> string what kind of rep this user has
///////////////////////////////////////////////

function fetch_reppower($user=array(),$rep='pos')
	{
		global $GVARS, $is_mod;
		// is the user allowed to do negative reps?
		if( ! $GVARS['g_rep_negative'] )
		{
			$rep = 'pos';
		}

		if( ! $GVARS['g_rep_use'] ) // allowed to rep at all?
		{
			$rep = 0;
		}
		elseif( $is_mod && $GVARS['rep_adminpower'] ) // is a mod and has loadsa power?
		{ //work out positive or negative admin power
			$reppower = ( $rep != 'pos' ) ? intval($GVARS['rep_adminpower'] * -1) : intval($GVARS['rep_adminpower']);
		}
		elseif( ( $user['posts'] < $GVARS['rep_minpost'] ) || ( $user['reputation'] < $GVARS['rep_minrep'] ) )
		{ // not an admin, then work out postal based power
			$reppower = 0;
		}
		else
		{ // ok failed all tests, so ratio is 1:1 but not negative, unless allowed
			$reppower = 1;

			if( $GVARS['rep_pcpower'] )
			{ // percentage power
				$reppower += intval($user['posts'] / $GVARS['rep_pcpower']);
			}

			if( $GVARS['rep_kppower'] )
			{ // rep as based upon a constant of kppower global
				$reppower += intval($user['reputation'] / $GVARS['rep_kppower']);
			}

			if( $GVARS['rep_rdpower'] )
			{ // time based power
				$reppower += intval((TIMENOW - $user['added']) / 86400 / $GVARS['rep_rdpower']);
			}

			if( $rep != 'pos' )
			{
				// Negative rep is worth half that of positive, but must be atleast 1, else it gets messy
				$reppower = intval($reppower / 2);
				$reppower = ( $reppower < 1 ) ? 1 : $reppower;
				$reppower *= -1;
			}
		}

		return $reppower;
	}

// erm, FIN
?>
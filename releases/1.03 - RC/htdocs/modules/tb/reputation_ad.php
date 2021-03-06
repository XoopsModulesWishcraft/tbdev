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
require_once '../../mainfile.php';
require_once "include/bittorrent.php";
require_once "include/user_functions.php";

dbconn( false );

loggedinorreturn();

	$GLOBALS['lang'] = load_language('global');
	
	if ( get_user_class() < UC_ADMINISTRATOR )
		header( "Location: {$GLOBALS['TBDEV']['baseurl']}/index.php" );

	$input = array_merge($_GET, $_POST);
	$input['mode'] = isset( $input['mode'] ) ? $input['mode'] : '';
	$now_date     = "";
	$reputationid = 0;
	$time_offset = 0;

		
		$a = explode(",", gmdate("Y,n,j,G,i,s", time() + $time_offset));
		$now_date = array( 'year' => $a[0], 'mon' => $a[1], 'mday' => $a[2],
								 'hours' => $a[3], 'minutes' => $a[4], 'seconds' => $a[5] );


		switch( $input['mode'] )
		{
			case 'modify':
				show_level();
				break;
			case 'add':
				show_form('new');
				break;
			case 'doadd':
				do_update('new');
				break;
			case 'edit':
				show_form('edit');
				break;
			case 'doedit':
				do_update('edit');
				break;
			case 'doupdate':
				do_update();
				break;
			case 'dodelete':
				do_delete();
				break;
			case 'list':
				view_list();
				break;
			case 'dolist':
				do_list();
				break;
			case 'editrep':
				show_form_rep('edit');
				break;
			case 'doeditrep':
				do_edit_rep();
				break;
			case 'dodelrep':
				do_delete_rep();
				break;

			default:
				show_level();
				break;
		}
	


function show_level()
	{
		$title = "User Reputation Manager - Overview";
		
		$html = "<p>On this page you can modify the minimum amount required for each ".$GLOBALS['xoopsDB']->prefix("reputation")." level. Make sure you press Update Minimum Levels to save your changes. You cannot set the same minimum amount to more than one level.<br />From here you can also choose to edit or remove any single level. Click the Edit link to modify the Level description (see Editing a Reputation Level) or click Remove to delete a level. If you remove a level or modify the minimum ".$GLOBALS['xoopsDB']->prefix("reputation")." needed to be at a level, all ".$GLOBALS['xoopsDB']->prefix("tb_users")." will be updated to reflect their new level if necessary.</p><br />";

		$query = $GLOBALS['xoopsDB']->queryF( 'SELECT * FROM  '.$GLOBALS['xoopsDB']->prefix("reputationlevel").'  ORDER BY minimumreputation ASC' );
		

		if( ! mysql_num_rows( $query ) )
		{
			do_update( 'new' );
			return;
		}
		$css = "style='font-weight: bold;color: #ffffff;background-color: #0055A4;padding: 5px;'";

		$html .= "<h2>User Reputation Manager</h2>" ;
		$html .= "<p><span class='btn'><a href='reputation_ad.php?mode=list'>View comments</a></span></p><br />";
		$html .= "<form action='reputation_ad.php' name='show_rep_form' method='post'>
				<input name='mode' value='doupdate' type='hidden' />";

		$html .= "<table cellpadding='3px'><tr>
		<td width='5%' $css>ID</td>
		<td width='60%'$css>Reputation Level</td>
		<td width='20%' $css>Minimum Reputation Level</td>
		<td width='15%' $css>Controls</td></tr>";

		

		while( $res = $GLOBALS['xoopsDB']->fetchArray( $query ) )
		{
			$html .= "<tr>\n".
					"	<td>#".$res['reputationlevelid']."</td>\n".
					"	<td>User <b>".htmlentities( $res['level'] )."</b></td>\n".
					"	<td align='center'><input type='text' name='reputation[".$res['reputationlevelid']."]' value='".$res['minimumreputation']."' size='12' /></td>\n".
					"	<td align='center'><span class='btn'><a href='reputation_ad.php?mode=edit&amp; ".$GLOBALS['xoopsDB']->prefix("reputationlevel").".id=".$res['reputationlevelid']."'>Edit</a></span>&nbsp;<span class='btn'><a href='reputation_ad.php?mode=dodelete&amp; ".$GLOBALS['xoopsDB']->prefix("reputationlevel").".id=".$res['reputationlevelid']."'>Delete</a></span></td>\n".
					"</tr>\n";
		}

		$html .= "<tr><td colspan='3' align='center'>
					<input type='submit' value='Update' accesskey='s' class='btn' /> 
					<input type='reset' value='Reset' accesskey='r' class='btn' /></td>
					<td align='center'><span class='btn'><a href='reputation_ad.php?mode=add'>Add New</a></span>
					</td></tr>";
		$html .= "</table>";

		$html .= "</form>";

		$xoopsOption['template_main'] = 'tb_reputation_ad.html';
		include $GLOBALS['xoops']->path('header.php');
		$GLOBALS['xoopsTpl']->assign('xoConfig', $GLOBALS['TBDEV']);
		$GLOBALS['xoopsTpl']->assign('xoops_pagetitle', $title);
		$GLOBALS['xoopsTpl']->assign('html', $html);
		include $GLOBALS['xoops']->path('footer.php');
		
	}

function show_form($type='edit')
	{
		global $input;
		
		$html = "This allows you to add a new ".$GLOBALS['xoopsDB']->prefix("reputation")." level or edit an existing ".$GLOBALS['xoopsDB']->prefix("reputation")." level.";

		if( $type == 'edit' )
		{
			$query = $GLOBALS['xoopsDB']->queryF( 'SELECT * FROM  '.$GLOBALS['xoopsDB']->prefix("reputationlevel").'  WHERE  '.$GLOBALS['xoopsDB']->prefix("reputationlevel").'.id='.intval($input['reputationlevelid']) ) or sqlerr(__LINE__,__FILE__);
			

			if( ! $res = $GLOBALS['xoopsDB']->fetchArray( $query ) )
			{
				stderr( "Error:", "Please specify an ID." );
			}

			$title  = "Edit Reputation Level";
			$html .= "<br /><span style='font-weight:normal;'>{$res['level']} (ID:#{$res['reputationlevelid']})</span><br />";
			$button = "Update";
			$extra  = "<input type='button' class='button' value='Back' accesskey='b' class='btn' onclick='javascript:history.back(1)' />";
			$mode   = 'doedit';
		}
		else
		{
			$title  = "Add New Reputation Level";
			$button = "Save";
			$mode   = 'doadd';
			$extra  = "<input type='button' value='Back' accesskey='b' class='btn' onclick='javascript:history.back(1)' />";
		}
		
		$css = "style='font-weight: bold;color: #ffffff;background-color: #0055A4;padding: 5px;'";
		$replevid = isset($res['reputationlevelid']) ? $res['reputationlevelid'] : '';
		$replevel = isset($res['level']) ? htmlspecialchars($res['level']) : '';
		$minrep = isset($res['minimumreputation']) ? $res['minimumreputation'] : '';
		$html .= "<form action='reputation_ad.php' name='show_rep_form' method='post'>
				<input name='".$GLOBALS['xoopsDB']->prefix("reputationlevel")."id' value='{$replevid}' type='hidden' />
				<input name='mode' value='{$mode}' type='hidden' />";

		$html .= "<h2>$title</h2><table width='500px' cellpadding='5px'><tr>
		<td width='67%' $css>&nbsp;</td>
		<td width='33%' $css>&nbsp;</td></tr>";

		$html .= "<tr><td>Level Description<div class='desctext'>This is what is displayed for the user when their ".$GLOBALS['xoopsDB']->prefix("reputation")." points are above the amount entered as the minimum.</div></td>";
		$html .= "<td><input type='text' name='level' value=\"{$replevel}\" size='35' maxlength='250' /></td></tr>";
		$html .= "<tr><td>Minimum amount of ".$GLOBALS['xoopsDB']->prefix("reputation")." points required for this level<div>This can be a positive or a negative amount. When the user's ".$GLOBALS['xoopsDB']->prefix("reputation")." points reaches this amount, the above description will be displayed.</div></td>";
		$html .= "<td><input type='text' name='minimumreputation' value=\"{$minrep}\" size='35' maxlength='10' /></td></tr>";

		$html .= "<tr><td colspan='2' align='center'><input type='submit' value='$button' accesskey='s' class='btn' /> <input type='reset' value='Reset' accesskey='r' class='btn' /> $extra</td></tr>";
		$html .= "</table>";

		$html .= "</form>";
		
		$xoopsOption['template_main'] = 'tb_reputation_ad.html';
		include $GLOBALS['xoops']->path('header.php');
		$GLOBALS['xoopsTpl']->assign('xoConfig', $GLOBALS['TBDEV']);
		$GLOBALS['xoopsTpl']->assign('xoops_pagetitle', $title);
		$GLOBALS['xoopsTpl']->assign('html', $html);
		include $GLOBALS['xoops']->path('footer.php');
		
		

	}

/////////////////////////////////////
//	Update rep function
/////////////////////////////////////
function do_update($type="")
	{
		global $input;
		
		if( $type != "" )
		{
			$level = strip_tags( $input['level'] );
			$level = trim( $level );

			if( ( strlen( $input['level'] ) < 2 ) || ( $level == "" ) )
			{
				stderr( '', 'The text you entered was too short.' );
			}

			if( strlen( $input['level'] ) > 250 )
			{
				stderr( '', 'The text entry is too long.' );
			}
			
			$level = sqlesc( $level );
			$minrep = sqlesc( intval( $input['minimumreputation'] ) );
			
			$redirect = 'Saved Reputation Level <i>'.htmlentities( $input['level'], ENT_QUOTES ).'</i> Successfully.';
		}

		// what we gonna do?
		if( $type == 'new' )
		{
			@$GLOBALS['xoopsDB']->queryF( "INSERT INTO  ".$GLOBALS['xoopsDB']->prefix("reputationlevel")."  ( minimumreputation, level ) 
							VALUES  ($minrep, $level )" );
		}
		elseif( $type == 'edit' )
		{
			$levelid = intval( $input['reputationlevelid'] );
			if( ! is_valid_id($levelid) ) stderr('', 'Not a valid try');

			// check it's a valid rep id

			$query = $GLOBALS['xoopsDB']->queryF( "SELECT  ".$GLOBALS['xoopsDB']->prefix("reputationlevel")." id FROM  ".$GLOBALS['xoopsDB']->prefix("reputationlevel")."  WHERE 
									 ".$GLOBALS['xoopsDB']->prefix("reputationlevel").".id={$levelid}" );

			if( ! mysql_num_rows( $query ) )
			{
				stderr( '', 'Not a valid ID.' );
			}

			@$GLOBALS['xoopsDB']->queryF( "UPDATE  ".$GLOBALS['xoopsDB']->prefix("reputationlevel")."  SET minimumreputation = $minrep, level = $level 
							WHERE  ".$GLOBALS['xoopsDB']->prefix("reputationlevel")." id = $levelid" );
		}
		else
		{
			$ids = $input['reputation'];
			if( is_array($ids) && count($ids) )
			{
				foreach( $ids as $k => $v )
				{
					@$GLOBALS['xoopsDB']->queryF( "UPDATE  ".$GLOBALS['xoopsDB']->prefix("reputationlevel")."  SET minimumreputation = ".intval($v)." WHERE  ".$GLOBALS['xoopsDB']->prefix("reputationlevel")." id = ".intval($k) );
				}
			}
			else
			{
				stderr( '', 'No valid ID.' );
			}

			$redirect = "Saved Reputation Level Successfully.";
		}

		rep_cache();

		redirect( 'reputation_ad.php?mode=done', $redirect );
	}


//////////////////////////////////////
//	Reputaion delete
//////////////////////////////////////
function do_delete()
	{
		global $input;
		
		if( ! isset($input['reputationlevelid']) || ! is_valid_id($input['reputationlevelid']) )
			stderr( '', 'No valid ID.' );
			
		$levelid = intval($input['reputationlevelid']);

		// check the id is valid within db

		$query = $GLOBALS['xoopsDB']->queryF( "SELECT  ".$GLOBALS['xoopsDB']->prefix("reputationlevel")." id FROM  ".$GLOBALS['xoopsDB']->prefix("reputationlevel")."  WHERE  ".$GLOBALS['xoopsDB']->prefix("reputationlevel").".id=$levelid" );
		
		if( ! mysql_num_rows( $query ) )
		{
			stderr( '', 'Rep ID doesn\'t exist' );
		}

		// if we here, we delete it!

		@$GLOBALS['xoopsDB']->queryF( "DELETE FROM  ".$GLOBALS['xoopsDB']->prefix("reputationlevel")."  WHERE  ".$GLOBALS['xoopsDB']->prefix("reputationlevel").".id=$levelid" );
		rep_cache();

		redirect( 'reputation_ad.php?mode=done', 'Reputation deleted successfully', 5 );
	}


//////////////////////////////////////
//	Reputaion edit
//////////////////////////////////////

function show_form_rep()
	{
		global $input;
		
		if( ! isset($input['reputationid']) || ! is_valid_id($input['reputationid']) )
			stderr( '', 'Nothing here by that ID.' );
		
		$title = 'User Reputation Manager';

		$query = $GLOBALS['xoopsDB']->queryF( "SELECT r.*, p.topicid, t.subject, leftfor.username as leftfor_name, 
					leftby.username as leftby_name
					FROM ".$GLOBALS['xoopsDB']->prefix("reputation")." r
					left join posts p on p.id=r.postid
					left join topics t on p.topicid=t.id
					left join ".$GLOBALS['xoopsDB']->prefix("tb_users")." leftfor on leftfor.id=r.userid
					left join ".$GLOBALS['xoopsDB']->prefix("tb_users")." leftby on leftby.id=r.whoadded
					WHERE reputationid = ".intval($input['reputationid']) );
		

		if( ! $res = $GLOBALS['xoopsDB']->fetchArray( $query ) )
		{
			stderr( '', 'Erm, it\'s not there!' );
		}
		
		$html = "<form action='reputation_ad.php' name='show_rep_form' method='post'>
				<input name='reputationid' value='{$res['reputationid']}' type='hidden' />
				<input name='oldreputation' value='{$res['reputation']}' type='hidden' />
				<input name='mode' value='doeditrep' type='hidden' />";
		

		$html .= "<h2>Edit Reputation</h2>";
		$html .= "<table cellpadding='5px'>";

		$html .= "<tr><td width='37%'>Topic</td><td width='63%'><a href='forums.php?action=viewtopic&amp;topicid={$res['topicid']}&amp;page=p{$res['postid']}#{$res['postid']}' target='_blank'>".htmlspecialchars($res['subject'])."</a></td></tr>";
		$html .= "<tr><td>Left By</td><td>{$res['leftby_name']}</td></tr>";
		$html .= "<tr><td>Left For</td><td width='63%'>{$res['leftfor_name']}</td></tr>";
		$html .= "<tr><td>Comment</td><td width='63%'><input type='text' name='reason' value='".htmlspecialchars($res['reason'])."' size='35' maxlength='250' /></td></tr>";
		$html .= "<tr><td>Reputation</td><td><input type='text' name='reputation' value='{$res['reputation']}' size='35' maxlength='10' /></td></tr>";

		$html .= "<tr><td colspan='2' align='center'><input type='submit' value='Save' accesskey='s' class='btn' /> <input type='reset' tabindex='1' value='Reset' accesskey='r' class='btn' /></td></tr>";
		$html .= "</table></form>";

		$xoopsOption['template_main'] = 'tb_reputation_ad.html';
		include $GLOBALS['xoops']->path('header.php');
		$GLOBALS['xoopsTpl']->assign('xoConfig', $GLOBALS['TBDEV']);
		$GLOBALS['xoopsTpl']->assign('xoops_pagetitle', $title);
		$GLOBALS['xoopsTpl']->assign('html', $html);
		include $GLOBALS['xoops']->path('footer.php');
		
	}


/////////////////////////////////////
//	View ".$GLOBALS['xoopsDB']->prefix("reputation")." comments function
/////////////////////////////////////

function view_list()
	{
		global $now_date, $time_offset, $input;
		
		$title = 'User Reputation Manager';
		$html =  "<h2>View Reputation Comments</h2>";
		$html .= "<p>This page allows you to search for ".$GLOBALS['xoopsDB']->prefix("reputation")." comments left by / for specific ".$GLOBALS['xoopsDB']->prefix("tb_users")." over the specified date range.</p>";

		$html .= "<form action='reputation_ad.php' name='list_form' method='post'>
				<input name='mode' value='list' type='hidden' />
				<input name='dolist' value='1' type='hidden' />";
				
		$html .= "<table width='500px' cellpadding='5px'>";
		

		$html .= "<tr><td width='20%'>Left For</td><td width='80%'><input type='text' name='leftfor' value='' size='35' maxlength='250' tabindex='1' /></td></tr>";
		$html .= "<tr><td colspan='2'><div>To limit the comments left for a specific user, enter the username here. Leave this field empty to receive comments left for every user.</div></td></tr>";

		$html .= "<tr><td>Left By</td><td><input type='text' name='leftby' value='' size='35' maxlength='250' tabindex='2' /></td></tr>";
		$html .= "<tr><td colspan='2'><div>To limit the comments left by a specific user, enter the username here. Leave this field empty to receive comments left by every user.</div></td></tr>";

		$html .= "<tr><td>Start Date</td><td>
		<div>
				<span style='padding-right:5px; float:left;'>Month<br /><select name='start[month]' tabindex='3'>".get_month_dropdown(1)."</select></span>
				<span style='padding-right:5px; float:left;'>Day<br /><input type='text' name='start[day]' value='".($now_date['mday']+1)."' size='4' maxlength='2' tabindex='3' /></span>
				<span>Year<br /><input type='text' name='start[year]' value='".$now_date['year']."' size='4' maxlength='4' tabindex='3' /></span>
			</div></td></tr>";
		
		$html .= "<tr><td class='tdrow2' colspan='2'><div class='desctext'>Select a start date for this report. Select a month, day, and year. The selected statistic must be no older than this date for it to be included in the report.</div></td></tr>";

		$html .= "<tr><td>End Date</td><td>
			<div>
				<span style='padding-right:5px; float:left;'>Month<br /><select name='end[month]' class='textinput' tabindex='4'>".get_month_dropdown()."</select></span>
				<span style='padding-right:5px; float:left;'>Day<br /><input type='text' class='textinput' name='end[day]' value='".$now_date['mday']."' size='4' maxlength='2' tabindex='4' /></span>
				<span>Year<br /><input type='text' class='textinput' name='end[year]' value='".$now_date['year']."' size='4' maxlength='4' tabindex='4' /></span>
			</div></td></tr>";
		
		$html .= "<tr><td class='tdrow2' colspan='2'><div class='desctext'>Select an end date for this report. Select a month, day, and year. The selected statistic must not be newer than this date for it to be included in the report. You can use this setting in conjunction with the 'Start Date' setting to create a window of time for this report.</div></td></tr>";

		$html .= "<tr><td colspan='2' align='center'><input type='submit' value='Search' accesskey='s' class='btn' tabindex='5' /> <input type='reset' value='Reset' accesskey='r' class='btn' tabindex='6' /></td></tr>";
		$html .= "</table></form>";
//print $html; exit;
		
		// I hate work, but someone has to do it!
		if( isset($input['dolist']) )
		{
			$links = "";
			$input['orderby'] = isset($input['orderby']) ? $input['orderby'] : '';
			//$cond = ''; //experiment
			$who = isset($input['who']) ? (int)$input['who'] : 0;
			$user = isset($input['user']) ? $input['user'] : 0;
			$first = isset($input['page']) ? intval($input['page']) : 0;
			$cond =  $who ?"r.whoadded=".sqlesc($who) : '';
			
			$start = isset($input['startstamp']) ? intval($input['startstamp']) : mktime(0, 0, 0, $input['start']['month'], $input['start']['day'], $input['start']['year']) + $time_offset;
			
			$end   = isset($input['endstamp']) ? intval($input['endstamp']) : mktime(0, 0, 0, $input['end']['month'], $input['end']['day'] + 1, $input['end']['year']) + $time_offset;

			if( ! $start )
			{
				$start = time() - (3600 * 24 * 30);
			}

			if( ! $end )
			{
				$end = time();
			}

			if( $start >= $end )
			{
				stderr( 'Time', 'Start date is after the end date.' );
			}

			if( ! empty($input['leftby']) )
			{
				$left_b = @$GLOBALS['xoopsDB']->queryF( "SELECT id FROM ".$GLOBALS['xoopsDB']->prefix("tb_users")." WHERE username = ".sqlesc($input['leftby']) );

				if( ! mysql_num_rows($left_b) )
				{
					stderr( 'DB ERROR', 'Could not find user '.htmlentities($input['leftby'], ENT_QUOTES) );
				}
				$leftby = $GLOBALS['xoopsDB']->fetchArray($left_b);
				$who  = $leftby['id'];
				$cond = "r.whoadded=".$who;
			}

			if( ! empty($input['leftfor']) )
			{
				$left_f = @$GLOBALS['xoopsDB']->queryF( "SELECT id FROM ".$GLOBALS['xoopsDB']->prefix("tb_users")." WHERE username = ".sqlesc($input['leftfor']) );

				if( ! mysql_num_rows($left_f) )
				{
					stderr( 'DB ERROR', 'Could not find user '.htmlentities($input['leftfor'], ENT_QUOTES) );
				}
				$leftfor = $GLOBALS['xoopsDB']->fetchArray($left_f);
				$user  = $leftfor['id'];
				$cond .= ($cond ? " AND" : "")." r.userid=".$user;
			}

			if( $start )
			{
				$cond .= ($cond ? " AND" : "")." r.dateadd >= $start";
			}

			if( $end )
			{
				$cond .= ($cond ? " AND" : "")." r.dateadd <= $end";
			}

			switch( $input['orderby'] )
			{
				case 'leftbyuser':
					$order = 'leftby.username';
					$orderby = 'leftbyuser';
					break;
				case 'leftforuser':
					$order = 'leftfor.username';
					$orderby = 'leftforuser';
					break;
				default:
					$order   = 'r.dateadd';
					$orderby = 'dateadd';
			}

			
			$css = "style='font-weight: bold;color: #ffffff;background-color: #0055A4;padding: 5px;'";
			$html = "<h2>Reputation Comments</h2>";
			$table_header = "<table width='80%' cellpadding='5' border='1'><tr $css>";
			$table_header .= "<td width='5%'>ID</td>";
			$table_header .= "<td width='20%'><a href='reputation_ad.php?mode=list&amp;dolist=1&amp;who=".intval($who)."&amp;user=".intval($user)."&amp;orderby=leftbyuser&amp;startstamp=$start&amp;endstamp=$end&amp;page=$first'>Left By</a></td>";
			$table_header .= "<td width='20%'><a href='reputation_ad.php?mode=list&amp;dolist=1&amp;who=".intval($who)."&amp;user=".intval($user)."&amp;orderby=leftforuser&amp;startstamp=$start&amp;endstamp=$end&amp;page=$first'>Left For</a></td>";
			$table_header .= "<td width='17%'><a href='reputation_ad.php?mode=list&amp;dolist=1&amp;who=".intval($who)."&amp;user=".intval($user)."&amp;orderby=date&amp;startstamp=$start&amp;endstamp=$end&amp;page=$first'>Date</a></td>";
			$table_header .= "<td width='5%'>Point</td>";
			$table_header .= "<td width='23%'>Reason</td>";
			$table_header .= "<td width='10%'>Controls</td></tr>";

			$html .= $table_header;

			// do the count for pager etc
			$query = $GLOBALS['xoopsDB']->queryF( "SELECT COUNT(*) AS cnt FROM ".$GLOBALS['xoopsDB']->prefix("reputation")." r WHERE $cond" );
			//print_r($input); exit;
			$total = $GLOBALS['xoopsDB']->fetchArray( $query );

			if( ! $total['cnt'] )
			{
				$html .= "<tr><td colspan='7' align='center'>No Matches Found!</td></tr>";
			}

			// do the pager thang!
			$deflimit = 10;
			$links = "<span style=\"background: #F0F5FA; border: 1px solid #072A66;padding: 1px 3px 1px 3px;\">{$total['cnt']}&nbsp;Records</span>";
			if( $total['cnt'] > $deflimit ) 
			{
			
				require_once "include/pager.php";
				
				$links = pager( 
                  array( 
                  'count'  => $total['cnt'],
                  'perpage'    => $deflimit,
                  'start_value'  => $first,
                  'url'    => "reputation_ad.php?mode=list&amp;dolist=1&amp;who=".intval($who)."&amp;user=".intval($user)."&amp;orderby=$orderby&amp;startstamp=$start&amp;endstamp=$end"
                        )
                  );
			}
			
			// mofo query!
			$query = $GLOBALS['xoopsDB']->queryF( "SELECT r.*, p.topicid, leftfor.id as leftfor_id, 
									leftfor.username as leftfor_name, leftby.id as leftby_id, 
									leftby.username as leftby_name 
									FROM ".$GLOBALS['xoopsDB']->prefix("reputation")." r 
									left join posts p on p.id=r.postid 
									left join ".$GLOBALS['xoopsDB']->prefix("tb_users")." leftfor on leftfor.id=r.userid 
									left join ".$GLOBALS['xoopsDB']->prefix("tb_users")." leftby on leftby.id=r.whoadded 
									WHERE $cond ORDER BY $order LIMIT $first,$deflimit" );
			
			if( ! mysql_num_rows( $query ) ) stderr('DB ERROR', 'Nothing here');

			while( $r = $GLOBALS['xoopsDB']->fetchArray( $query ) )
			{
				$r['dateadd'] = date( "M j, Y, g:i a", $r['dateadd'] );

				$html .= "<tr><td>#{$r['reputationid']}</td>";
				$html .= "<td><a href='userdetails.php?id={$r['leftby_id']}' target='_blank'>{$r['leftby_name']}</a></td>";
				$html .= "<td><a href='userdetails.php?id={$r['leftfor_id']}' target='_blank'>{$r['leftfor_name']}</a></td>";
				$html .= "<td>{$r['dateadd']}</td>";
				$html .= "<td align='right'>{$r['reputation']}</td>";
				$html .= "<td><a href='forums.php?action=viewtopic&amp;topicid={$r['topicid']}&amp;page=p{$r['postid']}#{$r['postid']}' target='_blank'>{$r['reason']}</a></td>";
				$html .= "<td><a href='reputation_ad.php?mode=editrep&amp;reputationid={$r['reputationid']}'><span class='btn'>Edit</span></a>&nbsp;<a href='reputation_ad.php?mode=dodelrep&amp;reputationid=".htmlspecialchars($r['reputationid'])."'><span class='btn'>Delete</span></a></td></tr>";
				
			}

			$html .= "</table>";
			$html .= "<br /><div>$links</div>";
		}

		$xoopsOption['template_main'] = 'tb_reputation_ad.html';
		include $GLOBALS['xoops']->path('header.php');
		$GLOBALS['xoopsTpl']->assign('xoConfig', $GLOBALS['TBDEV']);
		$GLOBALS['xoopsTpl']->assign('xoops_pagetitle', $title);
		$GLOBALS['xoopsTpl']->assign('html', $html);
		include $GLOBALS['xoops']->path('footer.php');
		
	}

///////////////////////////////////////////////
//	Reputation do_delete_rep function
///////////////////////////////////////////////
function do_delete_rep()
	{
		global $input;
		
		if( ! is_valid_id($input['reputationid']) )
			stderr('ERROR', 'Can\'t find ID');
		// check it's a valid ID.
		$query = $GLOBALS['xoopsDB']->queryF( "SELECT reputationid, reputation, userid FROM ".$GLOBALS['xoopsDB']->prefix("reputation")." WHERE reputationid=".intval($input['reputationid'] ) );

		if( false === ( $r = $GLOBALS['xoopsDB']->fetchArray($query) ) )
		{
			stderr( 'DELETE', 'No valid ID.' );
		}

		// do the delete
		@$GLOBALS['xoopsDB']->queryF( "DELETE FROM ".$GLOBALS['xoopsDB']->prefix("reputation")." WHERE reputationid=".intval($r['reputationid'] ) );
		@$GLOBALS['xoopsDB']->queryF( "UPDATE ".$GLOBALS['xoopsDB']->prefix("tb_users")." SET ".$GLOBALS['xoopsDB']->prefix("reputation")." = (reputation-{$r['reputation']} ) WHERE id=".intval($r['userid']) );

		redirect( "reputation_ad.php?mode=list", "Deleted Reputation Successfully", 5 );
	}


///////////////////////////////////////////////
//	Reputation do_edit_rep function
///////////////////////////////////////////////
function do_edit_rep()
	{
		global $input;
		
		if( isset($input['reason']) && ! empty($input['reason']) )
		{
			$reason = str_replace("<br />", "", $input['reason']);
			$reason = trim($reason);

			if( ( strlen(trim($reason)) < 2 ) || ( $reason == "" ) )
			{
				stderr( 'TEXT', 'The text you entered was too short.' );
			}

			if( strlen( $input['reason'] ) > 250 )
			{
				stderr( 'TEXT', 'The text entry is too long.' );
			}
		}

		$oldrep = intval($input['oldreputation']);
		$newrep = intval($input['reputation']);

		
		// valid ID?
		$query = $GLOBALS['xoopsDB']->queryF( "SELECT reputationid, reason, userid FROM ".$GLOBALS['xoopsDB']->prefix("reputation")." WHERE reputationid=".intval($input['reputationid'] ) );

		if( false === $r = $GLOBALS['xoopsDB']->fetchArray($query) )
		{
			stderr( 'INPUT', 'No ID' );
		}
/*
		if( $oldrep != $newrep )
		{
			if( $r['reason'] != $reason )
			{
				@$GLOBALS['xoopsDB']->queryF( "UPDATE ".$GLOBALS['xoopsDB']->prefix("reputation")." SET ".$GLOBALS['xoopsDB']->prefix("reputation")." = ".intval($newrep).", reason = ".sqlesc($reason).". WHERE reputationid = ".intval($r['reputationid']) );
			}

			$diff = $oldrep - $newrep;
			@$GLOBALS['xoopsDB']->queryF( "UPDATE ".$GLOBALS['xoopsDB']->prefix("tb_users")." SET ".$GLOBALS['xoopsDB']->prefix("reputation")." = (reputation-{$diff}) WHERE id=".intval($r['userid']) );
		
		}
*/
    // untested
    if( $r['reason'] != $reason || $oldrep != $newrep)
        {
        @$GLOBALS['xoopsDB']->queryF( "UPDATE ".$GLOBALS['xoopsDB']->prefix("reputation")." SET ".$GLOBALS['xoopsDB']->prefix("reputation")." = ".intval($newrep).", reason = ".sqlesc($reason)." WHERE reputationid = ".intval($r['reputationid']) );
        }
        

         if( $oldrep != $newrep )
        {

        $diff = $oldrep - $newrep;
        @$GLOBALS['xoopsDB']->queryF( "UPDATE ".$GLOBALS['xoopsDB']->prefix("tb_users")." SET ".$GLOBALS['xoopsDB']->prefix("reputation")." = (reputation-{$diff}) WHERE id=".intval($r['userid']) );
        
        } 
    
		redirect( "reputation_ad.php?mode=list", "Saved Reputation #ID{$r['reputationid']} Successfully.", 5);
	}

///////////////////////////////////////////////
//	Reputation output function
//	$msg -> string
//	$html -> string 
///////////////////////////////////////////////

function html_out( $html="", $title="" )
	{
		
		
		$xoopsOption['template_main'] = 'tb_reputation_ad.html';
		include $GLOBALS['xoops']->path('header.php');
		$GLOBALS['xoopsTpl']->assign('xoConfig', $GLOBALS['TBDEV']);
		$GLOBALS['xoopsTpl']->assign('xoops_pagetitle', $title);
		$GLOBALS['xoopsTpl']->assign('html', $html);
		include $GLOBALS['xoops']->path('footer.php');
				
		exit();
		
	}
	
	

function redirect($url, $text, $time=2)
	{
		
		redirect_header($GLOBALS['TBDEV']['baseurl'].'/'.$url, $time, $message);
		exit;
	}


/////////////////////////////
//	get_month worker function
/////////////////////////////
function get_month_dropdown($i=0)
	{
        global $now_date;
        $return = '';
        $month = array('----','January','February','March','April','May','June','July','August','September','October','November','December');
		foreach( $month as $k => $m )
		{
			$return .= "\t<option value='".$k."'";
			$return .= ( ( $k + $i ) == $now_date['mon'] ) ? " selected='selected'" : "";
			$return .= ">".$m."</option>\n";
		}

		return $return;
	}	


/////////////////////////////
//	cache rep function
/////////////////////////////
function rep_cache()
	{
		
		$query = @$GLOBALS['xoopsDB']->queryF( "SELECT * FROM  ".$GLOBALS['xoopsDB']->prefix("reputationlevel")." " );
		
		if( ! mysql_num_rows($query) )
			stderr( 'CACHE', 'No items to cache' );
		
		$rep_cache_file = "cache/rep_cache.php";
		$rep_out = "<"."?php\n\n\$reputations = array(\n";
		
		while( $row = $GLOBALS['xoopsDB']->fetchArray($query) )
		{
			$rep_out .= "\t{$row['minimumreputation']} => '{$row['level']}',\n";
		}
		
		$rep_out .= "\n);\n\n?".">";
		clearstatcache( $rep_cache_file );
		
		if( is_file( $rep_cache_file ) && is_writable( $rep_cache_file ) )
		{
			$filenum = fopen ( $rep_cache_file, 'w' );
			ftruncate( $filenum, 0 );
			fwrite( $filenum, $rep_out );
			fclose( $filenum );
		}
		
	}
?>
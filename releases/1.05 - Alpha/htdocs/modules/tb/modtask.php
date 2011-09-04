<?php
/*
+------------------------------------------------
|   TBDev.net BitTorrent Tracker PHP for XOOPS
|   =============================================
|   by CoLdFuSiOn
|   (c) 2003 - 2009 TBDev.Net
|   http://www.tbdev.net
|   =============================================
|   svn: http://sourceforge.net/projects/tbdevnet/
|   Licence Info: GPL
+------------------------------------------------
|   2011-09-05 12:20 AM AEST
|   1.05
|   Wishcraft
|   http://chronolabs.coop/
+------------------------------------------------
*/
require_once 'header.php';

loggedinorreturn();

$GLOBALS['lang'] = load_language('modtask');

if ($GLOBALS['CURUSER']['class'] < UC_MODERATOR) stderr("{$GLOBALS['lang']['modtask_user_error']}", "{$GLOBALS['lang']['modtask_try_again']}");

// Correct call to script
if ((isset($_POST['action'])) && ($_POST['action'] == "edituser"))
    {
    // Set user id
    if (isset($_POST['userid'])) $userid = $_POST['userid'];
    else stderr("{$GLOBALS['lang']['modtask_user_error']}", "{$GLOBALS['lang']['modtask_try_again']}");

    // and verify...
    if (!is_valid_id($userid)) stderr("{$GLOBALS['lang']['modtask_error']}", "{$GLOBALS['lang']['modtask_bad_id']}");

    // Fetch current user data...
    $res = $GLOBALS['xoopsDB']->queryF("SELECT * FROM ".$GLOBALS['xoopsDB']->prefix("tb_users")." WHERE id=".sqlesc($userid)) or sqlerr(__FILE__, __LINE__);
    $user = $GLOBALS['xoopsDB']->fetchArray($res) or sqlerr(__FILE__, __LINE__);
    
    //== Check to make sure your not editing someone of the same or higher class
    if ($GLOBALS['CURUSER']["class"] <= $user['class'] && ($GLOBALS['CURUSER']['id']!= $userid && $GLOBALS['CURUSER']["class"] < UC_ADMINISTRATOR))
        stderr('Error','You cannot edit someone of the same or higher class.. injecting stuff arent we? Action logged');
    
    $updateset = array();

    $modcomment = (isset($_POST['modcomment']) && $GLOBALS['CURUSER']['class'] == UC_SYSOP) ? $_POST['modcomment'] : $user['modcomment'];

    // Set class

    if ((isset($_POST['class'])) && (($class = $_POST['class']) != $user['class']))
    {
      if ($class >= UC_SYSOP || ($class >= $GLOBALS['CURUSER']['class']) || ($user['class'] >= $GLOBALS['CURUSER']['class']))
        stderr("{$GLOBALS['lang']['modtask_user_error']}", "{$GLOBALS['lang']['modtask_try_again']}");
      if (!is_valid_user_class($class) || $GLOBALS['CURUSER']["class"] <= $_POST['class']) stderr( ("Error"), "Bad class :P");

    // Notify user
    $what = ($class > $user['class'] ? "{$GLOBALS['lang']['modtask_promoted']}" : "{$GLOBALS['lang']['modtask_demoted']}");
    $msg = sqlesc(sprintf($GLOBALS['lang']['modtask_have_been'], $what)." '" . get_user_class_name($class) . "' {$GLOBALS['lang']['modtask_by']} ".$GLOBALS['CURUSER']['username']);
    $added = time();
    //$GLOBALS['xoopsDB']->queryF("INSERT INTO messages (sender, receiver, msg, added) VALUES(0, $userid, $msg, $added)") or sqlerr(__FILE__, __LINE__);

    $updateset[] = "class = ".sqlesc($class);

    $modcomment = get_date( time(), 'DATE', 1 ) . " - $what to '" . get_user_class_name($class) . "' by ".$GLOBALS['CURUSER'][username].".\n". $modcomment;
    }

    // Clear Warning - Code not called for setting warning
    if (isset($_POST['warned']) && (($warned = $_POST['warned']) != $user['warned']))
    {
    $updateset[] = "warned = " . sqlesc($warned);
    $updateset[] = "warneduntil = 0";
    if ($warned == 'no')
    {
    $modcomment = get_date( time(), 'DATE', 1 ) . "{$GLOBALS['lang']['modtask_warned']}" . $GLOBALS['CURUSER']['username'] . ".\n". $modcomment;
    $msg = sqlesc("{$GLOBALS['lang']['modtask_warned_removed']}" . $GLOBALS['CURUSER']['username'] . ".");
    $added = time();
    //$GLOBALS['xoopsDB']->queryF("INSERT INTO messages (sender, receiver, msg, added) VALUES (0, $userid, $msg, $added)") or sqlerr(__FILE__, __LINE__);
    }
    }

    // Set warning - Time based
    if (isset($_POST['warnlength']) && ($warnlength = 0 + $_POST['warnlength']))
    {
    unset($warnpm);
    if (isset($_POST['warnpm'])) $warnpm = $_POST['warnpm'];

    if ($warnlength == 255)
    {
    $modcomment = get_date( time(), 'DATE', 1 ) . "{$GLOBALS['lang']['modtask_warned_by']}" . $GLOBALS['CURUSER']['username'] . ".\n{$GLOBALS['lang']['modtask_reason']} $warnpm\n" . $modcomment;
    $msg = sqlesc("{$GLOBALS['lang']['modtask_warning_received']}".$GLOBALS['CURUSER']['username'].($warnpm ? "\n\n{$GLOBALS['lang']['modtask_reason']} $warnpm" : ""));
    $updateset[] = "warneduntil = 0";
    }
    else
    {
    $warneduntil = (time() + $warnlength * 604800);
    $dur = $warnlength . "{$GLOBALS['lang']['modtask_week']}" . ($warnlength > 1 ? "s" : "");
    $msg = sqlesc(sprintf($GLOBALS['lang']['modtask_warning_duration'], $dur).$GLOBALS['CURUSER']['username'].($warnpm ? "\n\nReason: $warnpm" : ""));
    $modcomment = get_date( time(), 'DATE', 1 ) . sprintf($GLOBALS['lang']['modtask_warned_for'], $dur) . $GLOBALS['CURUSER']['username'] . ".\n{$GLOBALS['lang']['modtask_reason']} $warnpm\n" . $modcomment;
    $updateset[] = "warneduntil = ".sqlesc($warneduntil);
    }
    $added = time();
    //$GLOBALS['xoopsDB']->queryF("INSERT INTO messages (sender, receiver, msg, added) VALUES (0, $userid, $msg, $added)") or sqlerr(__FILE__, __LINE__);
    $updateset[] = "warned = 'yes'";
    }

    // Clear donor - Code not called for setting donor
    if (isset($_POST['donor']) && (($donor = $_POST['donor']) != $user['donor']))
    {
    $updateset[] = "donor = " . sqlesc($donor);
    $updateset[] = "donerduntil = 0";
    if ($donor == 'no')
    {
    $modcomment = get_date( time(), 'DATE', 1 ) . "{$GLOBALS['lang']['modtask_donor_removed']}".$GLOBALS['CURUSER']['username'].".\n". $modcomment;
    $msg = sqlesc("{$GLOBALS['lang']['modtask_donor_expired']}");
    $added = time();
    //$GLOBALS['xoopsDB']->queryF("INSERT INTO messages (sender, receiver, msg, added) VALUES (0, $userid, $msg, $added)") or sqlerr(__FILE__, __LINE__);
    }
    }

    // Set donor - Time based
    if ((isset($_POST['donorlength'])) && ($donorlength = 0 + $_POST['donorlength']))
    {
    if ($donorlength == 255)
    {
    $modcomment = get_date( time(), 'DATE', 1 ) . "{$GLOBALS['lang']['modtask_donor_set']}" . $GLOBALS['CURUSER']['username'] . ".\n" . $modcomment;
    $msg = sqlesc("{$GLOBALS['lang']['modtask_received_donor']}".$GLOBALS['CURUSER']['username']);
    $updateset[] = "donoruntil = 0";
    }
    else
    {
    $donoruntil = (time() + $donorlength * 604800);
    $dur = $donorlength . "{$GLOBALS['lang']['modtask_week']}" . ($donorlength > 1 ? "s" : "");
    $msg = sqlesc(sprintf($GLOBALS['lang']['modtask_donor_duration'], $dur) . $GLOBALS['CURUSER']['username']);
    $modcomment = get_date( time(), 'DATE', 1 ) . sprintf($GLOBALS['lang']['modtask_donor_for'], $dur) . $GLOBALS['CURUSER']['username']."\n".$modcomment;
    $updateset[] = "donoruntil = ".sqlesc($donoruntil);
    }
    $added = time();
    //$GLOBALS['xoopsDB']->queryF("INSERT INTO messages (sender, receiver, msg, added) VALUES (0, $userid, $msg, $added)") or sqlerr(__FILE__, __LINE__);
    $updateset[] = "donor = 'yes'";
    }

    // Enable / Disable
    if ((isset($_POST['enabled'])) && (($enabled = $_POST['enabled']) != $user['enabled']))
    {
    if ($enabled == 'yes')
    $modcomment = get_date( time(), 'DATE', 1 ) . " {$GLOBALS['lang']['modtask_enabled']}" . $GLOBALS['CURUSER']['username'] . ".\n" . $modcomment;
    else
    $modcomment = get_date( time(), 'DATE', 1 ) . "{$GLOBALS['lang']['modtask_disabled']}" . $GLOBALS['CURUSER']['username'] . ".\n" . $modcomment;

    $updateset[] = "enabled = " . sqlesc($enabled);
    }
    /* If your running the forum post enable/disable, uncomment this section
    // Forum Post Enable / Disable
    if ((isset($_POST['forumpost'])) && (($forumpost = $_POST['forumpost']) != $user['forumpost']))
    {
    if ($forumpost == 'yes')
    {
    $modcomment = gmdate("Y-m-d")." - Posting enabled by ".$GLOBALS['CURUSER']['username'].".\n" . $modcomment;
    $msg = sqlesc("Your Posting rights have been given back by ".$GLOBALS['CURUSER']['username'].". You can post to forum again.");
    $added = time();
    $GLOBALS['xoopsDB']->queryF("INSERT INTO messages (sender, receiver, msg, added) VALUES (0, $userid, $msg, $added)") or sqlerr(__FILE__, __LINE__);
    }
    else
    {
    $modcomment = gmdate("Y-m-d")." - Posting disabled by ".$GLOBALS['CURUSER']['username'].".\n" . $modcomment;
    $msg = sqlesc("Your Posting rights have been removed by ".$GLOBALS['CURUSER']['username'].", Please PM ".$GLOBALS['CURUSER']['username']." for the reason why.");
    $added = time();
    $GLOBALS['xoopsDB']->queryF("INSERT INTO messages (sender, receiver, msg, added) VALUES (0, $userid, $msg, $added)") or sqlerr(__FILE__, __LINE__);
    }
    $updateset[] = "forumpost = " . sqlesc($forumpost);
    } */

    // Change Custom Title
    if ((isset($_POST['title'])) && (($title = $_POST['title']) != ($curtitle = $user['title'])))
    {
    $modcomment = get_date( time(), 'DATE', 1 ) . "{$GLOBALS['lang']['modtask_custom_title']}'".$title."' from '".$curtitle."'{$GLOBALS['lang']['modtask_by']}" . $GLOBALS['CURUSER']['username'] . ".\n" . $modcomment;

    $updateset[] = "title = " . sqlesc($title);
    }

    // The following code will place the old passkey in the mod comment and create
    // a new passkey. This is good practice as it allows usersearch to find old
    // passkeys by searching the mod comments of members.

    // Reset Passkey
    if ((isset($_POST['resetpasskey'])) && ($_POST['resetpasskey']))
    {
    $newpasskey = md5($user['username'].time().$user['passhash']);
    $modcomment = get_date( time(), 'DATE', 1 ) . "{$GLOBALS['lang']['modtask_passkey']}".sqlesc($user['passkey'])."{$GLOBALS['lang']['modtask_reset']}".sqlesc($newpasskey)."{$GLOBALS['lang']['modtask_by']}" . $GLOBALS['CURUSER']['username'] . ".\n" . $modcomment;

    $updateset[] = "passkey=".sqlesc($newpasskey);
    }

    /* This code is for use with the safe mod comment modification. If you have installed
    the safe mod comment mod, then uncomment this section...

    // Add Comment to ModComment
    if ((isset($_POST['addcomment'])) && ($addcomment = trim($_POST['addcomment'])))
    {
    $modcomment = gmdate("Y-m-d") . " - ".$addcomment." - " . $GLOBALS['CURUSER']['username'] . ".\n" . $modcomment;
    } */

    /* Uncomment the following code if you have the upload mod installed...

    // Set Upload Enable / Disable
    if ((isset($_POST['uploadpos'])) && (($uploadpos = $_POST['uploadpos']) != $user['uploadpos']))
    {
    if ($uploadpos == 'yes')
    {
    $modcomment = gmdate("Y-m-d") . " - Upload enabled by " . $GLOBALS['CURUSER']['username'] . ".\n" . $modcomment;
    $msg = sqlesc("You have been given upload rights by " . $GLOBALS['CURUSER']['username'] . ". You can now upload ".$GLOBALS['xoopsDB']->prefix("tb_torrents").".");
    $added = time();
    $GLOBALS['xoopsDB']->queryF("INSERT INTO messages (sender, receiver, msg, added) VALUES (0, $userid, $msg, $added)") or sqlerr(__FILE__, __LINE__);
    }
    elseif ($uploadpos == 'no')
    {
    $modcomment = gmdate("Y-m-d") . " - Upload disabled by " . $GLOBALS['CURUSER']['username'] . ".\n" . $modcomment;
    $msg = sqlesc("Your upload rights have been removed by " . $GLOBALS['CURUSER']['username'] . ". Please PM ".$GLOBALS['CURUSER']['username']." for the reason why.");
    $added = time();
    $GLOBALS['xoopsDB']->queryF("INSERT INTO messages (sender, receiver, msg, added) VALUES (0, $userid, $msg, $added)") or sqlerr(__FILE__, __LINE__);
    }
    else
    stderr("{$GLOBALS['lang']['modtask_user_error']}", "{$GLOBALS['lang']['modtask_try_again']}"); // Error

    $updateset[] = "uploadpos = " . sqlesc($uploadpos);
    } */

    /* Uncomment the following code if you have the download mod installed...

    // Set Download Enable / Disable
    if ((isset($_POST['downloadpos'])) && (($downloadpos = $_POST['downloadpos']) != $user['downloadpos']))
    {
    if ($downloadpos == 'yes')
    {
    $modcomment = gmdate("Y-m-d") . " - Download enabled by " . $GLOBALS['CURUSER']['username'] . ".\n" . $modcomment;
    $msg = sqlesc("Your download rights have been given back by " . $GLOBALS['CURUSER']['username'] . ". You can download ".$GLOBALS['xoopsDB']->prefix("tb_torrents")." again.");
    $added = time();
    $GLOBALS['xoopsDB']->queryF("INSERT INTO messages (sender, receiver, msg, added) VALUES (0, $userid, $msg, $added)") or sqlerr(__FILE__, __LINE__);
    }
    elseif ($downloadpos == 'no')
    {
    $modcomment = gmdate("Y-m-d") . " - Download disabled by " . $GLOBALS['CURUSER']['username'] . ".\n" . $modcomment;
    $msg = sqlesc("Your download rights have been removed by " . $GLOBALS['CURUSER']['username'] . ", Please PM ".$GLOBALS['CURUSER']['username']." for the reason why.");
    $added = time();
    $GLOBALS['xoopsDB']->queryF("INSERT INTO messages (sender, receiver, msg, added) VALUES (0, $userid, $msg, $added)") or sqlerr(__FILE__, __LINE__);
    }
    else
    stderr("{$GLOBALS['lang']['modtask_user_error']}", "{$GLOBALS['lang']['modtask_try_again']}"); // Error

    $updateset[] = "downloadpos = " . sqlesc($downloadpos);
    } */

    // Avatar Changed
    if ((isset($_POST['avatar'])) && (($avatar = $_POST['avatar']) != ($curavatar = $user['avatar'])))
    {
      
      $avatar = trim( urldecode( $avatar ) );
  
      if ( preg_match( "/^http:\/\/$/i", $avatar ) 
        or preg_match( "/[?&;]/", $avatar ) 
        or preg_match("#javascript:#is", $avatar ) 
        or !preg_match("#^https?://(?:[^<>*\"]+|[a-z0-9/\._\-!]+)$#iU", $avatar ) 
      )
      {
        $avatar='';
      }
      
      if( !empty($avatar) ) 
      {
        $img_size = @GetImageSize( $avatar );

        if($img_size == FALSE || !in_array($img_size['mime'], $GLOBALS['TBDEV']['allowed_ext']))
          stderr("{$GLOBALS['lang']['modtask_user_error']}", "{$GLOBALS['lang']['modtask_not_image']}");

        if($img_size[0] < 5 || $img_size[1] < 5)
          stderr("{$GLOBALS['lang']['modtask_user_error']}", "{$GLOBALS['lang']['modtask_image_small']}");
      
        if ( ( $img_size[0] > $GLOBALS['TBDEV']['av_img_width'] ) OR ( $img_size[1] > $GLOBALS['TBDEV']['av_img_height'] ) )
        { 
            $image = resize_image( array(
                             'max_width'  => $GLOBALS['TBDEV']['av_img_width'],
                             'max_height' => $GLOBALS['TBDEV']['av_img_height'],
                             'cur_width'  => $img_size[0],
                             'cur_height' => $img_size[1]
                        )      );
                        
          }
          else 
          {
            $image['img_width'] = $img_size[0];
            $image['img_height'] = $img_size[1];
          }
      
        $updateset[] = "av_w = " . sqlesc($image['img_width']);
        $updateset[] = "av_h = " . sqlesc($image['img_height']);
      }
      
      $modcomment = get_date( time(), 'DATE', 1 ) . "{$GLOBALS['lang']['modtask_avatar_change']}".htmlspecialchars($curavatar)."{$GLOBALS['lang']['modtask_to']}".htmlspecialchars($avatar)."{$GLOBALS['lang']['modtask_by']}" . $GLOBALS['CURUSER']['username'] . ".\n" . $modcomment;

      $updateset[] = "avatar = ".sqlesc($avatar);
    }

    /* Uncomment if you have the First Line Support mod installed...

    // Support
    if ((isset($_POST['support'])) && (($support = $_POST['support']) != $user['support']))
    {
    if ($support == 'yes')
    {
    $modcomment = gmdate("Y-m-d") . " - Promoted to FLS by " . $GLOBALS['CURUSER']['username'] . ".\n" . $modcomment;
    }
    elseif ($support == 'no')
    {
    $modcomment = gmdate("Y-m-d") . " - Demoted from FLS by " . $GLOBALS['CURUSER']['username'] . ".\n" . $modcomment;
    }
    else
    stderr("{$GLOBALS['lang']['modtask_user_error']}", "{$GLOBALS['lang']['modtask_try_again']}");

    $supportfor = $_POST['supportfor'];

    $updateset[] = "support = " . sqlesc($support);
    $updateset[] = "supportfor = ".sqlesc($supportfor);
    } */

    // Add ModComment to the update set...
    // Add ModComment... (if we changed something we update otherwise we dont include this..)
    if (($GLOBALS['CURUSER']['class'] == UC_SYSOP && ($user['modcomment'] != $_POST['modcomment'] || $modcomment!=$_POST['modcomment'])) || ($GLOBALS['CURUSER']['class']<UC_SYSOP && $modcomment != $user['modcomment']))
    $updateset[] = "modcomment = " . sqlesc($modcomment);

    //$GLOBALS['xoopsDB']->queryF("UPDATE ".$GLOBALS['xoopsDB']->prefix("tb_users")." SET " . implode(", ", $updateset) . " WHERE id=".sqlesc($userid)) or sqlerr(__FILE__, __LINE__);
    if (sizeof($updateset)>0) 
      @$GLOBALS['xoopsDB']->queryF("UPDATE ".$GLOBALS['xoopsDB']->prefix("tb_users")." SET  " . implode(", ", $updateset) . " WHERE id=$userid") or sqlerr(__FILE__, __LINE__);
   
    $returnto = $_POST["returnto"];
    header("Location: {$GLOBALS['TBDEV']['baseurl']}/$returnto");

    stderr("{$GLOBALS['lang']['modtask_user_error']}", "{$GLOBALS['lang']['modtask_try_again']}");
    }

stderr("{$GLOBALS['lang']['modtask_user_error']}", "{$GLOBALS['lang']['modtask_no_idea']}");

?>
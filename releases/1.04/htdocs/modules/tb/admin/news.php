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

if ( ! defined( 'IN_TBDEV_ADMIN' ) )
{
	print "<h1>Incorrect access</h1>You cannot access this file directly.";
	exit();
}
include('header.php');
xoops_cp_header();
loadModuleAdminMenu(13);
include_once $GLOBALS['xoops']->path( "/class/template.php" );
$GLOBALS['tbTpl'] = new XoopsTpl();
$GLOBALS['tbTpl']->assign('php_self', $_SERVER['PHP_SELF']);

xoops_loadLanguage('admin', 'tb');

//require_once "include/bittorrent.php";
require_once "include/user_functions.php";
require_once "include/bbcode_functions.php";
require_once "include/html_functions.php";

    $GLOBALS['lang'] = array_merge( $GLOBALS['lang'], load_language('ad_news') );
    
    $input = array_merge( $_GET, $_POST);

    $mode = isset($input['mode']) ? $input['mode'] : '';

    $warning = '';
    
    $HTMLOUT = '';
    
        // Update NEws dates to rejuvenate /////////////////////////////

    if('update' == $mode)
    {
      if(isset($input['news_update']) && count($input['news_update']))
      {
        foreach($input['news_update'] as $v)
        {
          if(!is_valid_id($v)) stderr("Error", "No ".$GLOBALS['xoopsDB']->prefix("tb_news")." ID");
          $newsIDS[] = $v;
        }
      }
      else
      {
        stderr("Error", "No data!");
      }
      
      $news = join(',', $newsIDS);
      
      @$GLOBALS['xoopsDB']->queryF("UPDATE ".$GLOBALS['xoopsDB']->prefix("tb_news")." set added = ".time()." WHERE id IN ($news)");
      
      if(-1 == mysql_affected_rows())
        stderr("Error", "Update failed");
      
      header("Location: {$GLOBALS['TBDEV']['baseurl']}/admin.php?action=news");
      
    }
	
    
    //   Delete News Item    //////////////////////////////////////////////////////
    if ($mode == 'delete')
    {
      $newsid = isset($input['newsid']) ? (int)$input["newsid"] : 0;
      
      if (!is_valid_id($newsid))
        stderr($GLOBALS['lang']['news_error'],sprintf($GLOBALS['lang']['news_gen_error'],1));

      $returnto = isset($input['returno']) ? htmlentities($input["returnto"]) : '';

      $sure = isset($input["sure"]) ? (int)$input['sure'] : 0;
      
      if (!$sure)
      {
        stderr($GLOBALS['lang']['news_delete_notice'],sprintf($GLOBALS['lang']['news_delete_text'],$newsid));
      }
      
      @$GLOBALS['xoopsDB']->queryF("DELETE FROM ".$GLOBALS['xoopsDB']->prefix("tb_news")." WHERE id=$newsid") or sqlerr(__FILE__, __LINE__);

      if ($returnto != "")
        header("Location: {$GLOBALS['TBDEV']['baseurl']}/admin.php?action=news");
      else
        $warning = $GLOBALS['lang']['news_delete_ok'];
    }


    //   Add News Item    /////////////////////////////////////////////////////////
    if ($mode == 'add')
    {

      $body = isset($input["body"]) ? (string)$input["body"] : 0;
      
      if ( !$body OR strlen($body) < 4 )
        stderr($GLOBALS['lang']['news_error'],$GLOBALS['lang']['news_add_body']);
      
      $body = sqlesc($body);
      
      $added = isset($input['added']) ? $input['added'] : 0;
      
      $headline = (isset($input['headline']) AND !empty($input['headline'])) ? sqlesc($input['headline']) : sqlesc('TBDev.net News');
      
      if (!$added)
        $added = time();

      @$GLOBALS['xoopsDB']->queryF("INSERT INTO ".$GLOBALS['xoopsDB']->prefix("tb_news")." (userid, added, body, headline) VALUES ({$GLOBALS['CURUSER']['id']}, $added, $body, $headline)") or sqlerr(__FILE__, __LINE__);
        
      if (mysql_affected_rows() == 1)
        $warning = $GLOBALS['lang']['news_add_ok'];
      else
        stderr($GLOBALS['lang']['news_error'],$GLOBALS['lang']['news_add_err']);
    }

    
    //   Edit News Item    ////////////////////////////////////////////////////////
    if ($mode == 'edit')
    {

      $newsid = isset($input["newsid"]) ? (int)$input["newsid"] : 0;

      if (!is_valid_id($newsid))
        stderr($GLOBALS['lang']['news_error'], sprintf($GLOBALS['lang']['news_gen_error'],2));

      $res = @$GLOBALS['xoopsDB']->queryF("SELECT * FROM ".$GLOBALS['xoopsDB']->prefix("tb_news")." WHERE id=$newsid") or sqlerr(__FILE__, __LINE__);

      if (mysql_num_rows($res) != 1)
        stderr($GLOBALS['lang']['news_error'], $GLOBALS['lang']['news_edit_nonewsid']);

      $arr = $GLOBALS['xoopsDB']->fetchArray($res);

      if ($_SERVER['REQUEST_METHOD'] == 'POST')
      {
        $body = isset($_POST['body']) ? $_POST['body'] : '';

        if ($body == "" OR strlen($_POST['body']) < 4)
          stderr($GLOBALS['lang']['news_error'], $GLOBALS['lang']['news_add_body']);

        $headline = (isset($input['headline']) AND !empty($input['headline'])) ? sqlesc($input['headline']) : sqlesc('TBDev.net News');
        
        $body = sqlesc($body);

        $editedat = time();

        @$GLOBALS['xoopsDB']->queryF("UPDATE ".$GLOBALS['xoopsDB']->prefix("tb_news")." SET body=$body, headline=$headline WHERE id=$newsid") or sqlerr(__FILE__, __LINE__);

        $returnto = isset($_POST['returnto']) ? htmlentities($_POST['returnto']) : '';

        if ($returnto != "")
          header("Location: {$GLOBALS['TBDEV']['baseurl']}/index.php");
        else
          $warning = $GLOBALS['lang']['news_edit_ok'];;
      }
      else
      {
        //$returnto = isset($_POST['returnto']) ? htmlentities($_POST['returnto']) : $GLOBALS['TBDEV']['baseurl'].'/news.php';
        $HTMLOUT .= "<h1>{$GLOBALS['lang']['news_edit_title']}</h1>
        
        <form method='post' action='admin.php?action=news'>
        
        <input type='hidden' name='newsid' value='$newsid' />
        
        <input type='hidden' name='mode' value='edit' />
        
        <table width='700px'border='1' cellspacing='0' cellpadding='10px'>
        <tr>
          <td align='center'>
            <input style='width:650px;' type='text' name='headline' size='50' value='".htmlentities($arr['headline'], ENT_QUOTES, 'UTF-8')."' />
          </td>
        </tr>
        <tr>
          <td align='center'>
            <textarea style='width:650px;' name='body' cols='55' rows='10'>" . htmlentities($arr['body'], ENT_QUOTES) . "</textarea>
          </td>
        </tr>
        <tr>
          <td align='center'>
            <input type='submit' value='Okay' class='btn' />
          </td>
        </tr>
        
        </table>
        
        </form>\n";
        
        $GLOBALS['tbTpl']->assign('html', $HTMLOUT);
	    $GLOBALS['tbTpl']->display('db:tb_cpanel_news.html');
		xoops_cp_footer();
		exit(0);
	        
      }
    }

    
    
    //   Other Actions and followup    ////////////////////////////////////////////
    $HTMLOUT .= "<h1>{$GLOBALS['lang']['news_submit_title']}</h1>\n";
    
    if (!empty($warning))
      $HTMLOUT .= "<p><font size='-3'>($warning)</font></p>";
    
    $HTMLOUT .= "<form method='post' action='admin.php?action=news'>
    <input type='hidden' name='mode' value='add' />
    <table width='750px' border='1' cellspacing='0' cellpadding='10px'>
      <tr>
        <td align='center'>
          <input  style='width:650px;' type='text' name='headline' size='50' value='' />
        </td>
      </tr>
      <tr>
        <td align='center'>
          <textarea style='width:650px;' name='body' cols='55' rows='10'></textarea>
        </td>
      </tr>
      <tr>
        <td align='center'>
          <input type='submit' value='Okay' class='btn' />
        </td>
      </tr>
    </table>
    </form><br /><br />";

    $res = @$GLOBALS['xoopsDB']->queryF("SELECT * FROM ".$GLOBALS['xoopsDB']->prefix("tb_news")." ORDER BY added DESC") or sqlerr(__FILE__, __LINE__);

    if (mysql_num_rows($res) > 0)
    {

      
      $HTMLOUT .= begin_main_frame();
      $HTMLOUT .= "<form method='post' action='admin.php?action=news'>
      <input type='hidden' name='mode' value='update' />";

      while ($arr = $GLOBALS['xoopsDB']->fetchArray($res))
      {
        $newsid = $arr["id"];
        $body = format_comment($arr["body"]);
        $headline = htmlentities($arr['headline'], ENT_QUOTES, 'UTF-8');
        $userid = $arr["userid"];
        $added = get_date( $arr['added'],'');

        $res2 = @$GLOBALS['xoopsDB']->queryF("SELECT username, donor FROM ".$GLOBALS['xoopsDB']->prefix("tb_users")." WHERE id = $userid") or sqlerr(__FILE__, __LINE__);
        $arr2 = $GLOBALS['xoopsDB']->fetchArray($res2);

        $postername = $arr2["username"];

        if ($postername == "")
          $by = "unknown[$userid]";
        else
          $by = "<a href='userdetails.php?id=$userid'><b>$postername</b></a>" .
            ($arr2["donor"] == "yes" ? "<img src=\"{$GLOBALS['TBDEV']['pic_base_url']}star.gif\" alt='Donor' />" : "");
            
        $HTMLOUT .= begin_frame();    
        $HTMLOUT .= begin_table(true);
        $HTMLOUT .= "
        <tr>
          <td class='colhead'>$headline<span style='float:right;'><input type='checkbox' name='news_update[]' value='$newsid' /></span></td>
        </tr>
        <tr>
          <td>{$added}&nbsp;&nbsp;by&nbsp$by
            <div style='float:right;'><a href='admin.php?action=news&amp;mode=edit&amp;newsid=$newsid'><span class='btn'>{$GLOBALS['lang']['news_act_edit']}</span></a>&nbsp;<a href='admin.php?action=news&amp;mode=delete&amp;newsid=$newsid'><span class='btn'>{$GLOBALS['lang']['news_act_delete']}</span></a>
            </div>
          </td>
        </tr>
        <tr valign='top'>
          <td class='comment'>$body</td>
        </tr>\n";
        
        $HTMLOUT .= end_table();
        $HTMLOUT .= end_frame();
        $HTMLOUT .= '<br />';
      }
      
       $HTMLOUT .= "<div align='right'><input name='submit' type='submit' value='Update' class='btn' /></div></form>";
      $HTMLOUT .= end_main_frame();
    }
    else
      stdmsg($GLOBALS['lang']['news_sorry'], $GLOBALS['lang']['news_nonews']);
      
	$GLOBALS['tbTpl']->assign('html', $HTMLOUT);
    $GLOBALS['tbTpl']->display('db:tb_cpanel_news.html');
	xoops_cp_footer();
	exit(0);
      
?>
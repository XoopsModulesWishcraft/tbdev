<?php

// $Author$
//  ------------------------------------------------------------------------ //
//                XOOPS - PHP Content Management System                      //
//                    Copyright (c) 2000 XOOPS.org                           //
//                       <http://www.xoops.org/>                             //
//  ------------------------------------------------------------------------ //
//  This program is free software; you can redistribute it and/or modify     //
//  it under the terms of the GNU General Public License as published by     //
//  the Free Software Foundation; either version 2 of the License, or        //
//  (at your option) any later version.                                      //
//                                                                           //
//  You may not change or alter any portion of this comment or credits       //
//  of supporting developers from this source code or any supporting         //
//  source code which is considered copyrighted (c) material of the          //
//  original comment or credit authors.                                      //
//                                                                           //
//  This program is distributed in the hope that it will be useful,          //
//  but WITHOUT ANY WARRANTY; without even the implied warranty of           //
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            //
//  GNU General Public License for more details.                             //
//                                                                           //
//  You should have received a copy of the GNU General Public License        //
//  along with this program; if not, write to the Free Software              //
//  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307 USA //
//  ------------------------------------------------------------------------ //
// Author: Simon Roberts (AKA wishcraft)                                     //
// URL: http://www.chronolabs.org.au                                         //
// Project: The XOOPS Project                                                //
// ------------------------------------------------------------------------- //

$modversion['name'] = _MI_TBDEV_NAME;
$modversion['version'] = 1.04;
$modversion['releasedate'] = "Thursday: September 1, 2011";
$modversion['description'] = _MI_TBDEV_DESCRIPTION;
$modversion['author'] = "Wishcraft";
$modversion['credits'] = "Simon Roberts (simon@chronolabs.coop)";
$modversion['help'] = "TBDEV.html";
$modversion['license'] = "GPL";
$modversion['official'] = 1;
$modversion['status']  = "RC";
$modversion['image'] = "images/tbdev_slogo.png";
$modversion['dirname'] = 'tb';

$modversion['author_realname'] = "Simon Roberts";
$modversion['author_website_url'] = "http://www.chronolabs.coop";
$modversion['author_website_name'] = "Chronolabs Cooperative";
$modversion['author_email'] = "simon@chronolabs.coop";
$modversion['demo_site_url'] = "http://xoops.demo.chronolabs.coop";
$modversion['demo_site_name'] = "Chronolabs Co-op XOOPS Demo";
$modversion['support_site_url'] = "http://www.chronolabs.org.au/forums/viewforum.php?forum=30";
$modversion['support_site_name'] = "Chronolabs";
$modversion['submit_bug'] = "http://www.chronolabs.org.au/forums/viewforum.php?forum=30";
$modversion['submit_feature'] = "http://www.chronolabs.org.au/forums/viewforum.php?forum=30";
$modversion['usenet_group'] = "sci.chronolabs";
$modversion['maillist_announcements'] = "";
$modversion['maillist_bugs'] = "";
$modversion['maillist_features'] = "";

//Search
$modversion['hasSearch'] = 0;
$modversion['search']['file'] = "include/search.inc.php";
$modversion['search']['func'] = "tbdev_search";

$modversion['hasComments'] = 1;
$modversion['comments']['itemName'] = 'id';
$modversion['comments']['pageName'] = 'details.php';

// Admin things
$modversion['hasAdmin'] = 1;
$modversion['adminindex'] = "admin.php?action=index";
$modversion['adminmenu'] = "admin/menu.php";

$modversion['onUpdate'] = "include/update.php";
//$modversion['onInstall'] = "include/install.php";
//$modversion['onUninstall'] = "include/uninstall.php";

// Sql file (must contain sql generated by phpMyAdmin or phpPgAdmin)
// All tables should not have any prefix!
$modversion['sqlfile']['mysql'] = "sql/mysql.sql";

// $modversion['sqlfile']['postgresql'] = "sql/pgsql.sql";
// Tables created by sql file (without prefix!)
$modversion['tables'][0] = "tb_avps";
$modversion['tables'][1] = "tb_bans";
$modversion['tables'][2] = "tb_blocks";
$modversion['tables'][3] = "tb_categories";
$modversion['tables'][4] = "tb_countries";
$modversion['tables'][5] = "tb_files";
$modversion['tables'][6] = "tb_friends";
$modversion['tables'][7] = "tb_news";
$modversion['tables'][8] = "tb_peers";
$modversion['tables'][9] = "tb_reputation";
$modversion['tables'][10] = "tb_reputationlevel";
$modversion['tables'][11] = "tb_searchcloud";
$modversion['tables'][12] = "tb_sitelog";
$modversion['tables'][13] = "tb_stylesheets";
$modversion['tables'][14] = "tb_torrents";
$modversion['tables'][15] = "tb_trackers";
$modversion['tables'][16] = "tb_trackers_to_torrents";
$modversion['tables'][17] = "tb_users";

// Templates
$i=0;
$i++;
$modversion['templates'][$i]['file'] = 'tb_bitbucket_upload.html';
$modversion['templates'][$i]['description'] = '';
$i++;
$modversion['templates'][$i]['file'] = 'tb_browse.html';
$modversion['templates'][$i]['description'] = '';
$i++;
$modversion['templates'][$i]['file'] = 'tb_chat.html';
$modversion['templates'][$i]['description'] = '';
$i++;
$modversion['templates'][$i]['file'] = 'tb_delete.html';
$modversion['templates'][$i]['description'] = '';
$i++;
$modversion['templates'][$i]['file'] = 'tb_details.html';
$modversion['templates'][$i]['description'] = '';
$i++;
$modversion['templates'][$i]['file'] = 'tb_edit.html';
$modversion['templates'][$i]['description'] = '';
$i++;
$modversion['templates'][$i]['file'] = 'tb_email_gateway.html';
$modversion['templates'][$i]['description'] = '';
$i++;
$modversion['templates'][$i]['file'] = 'tb_files.html';
$modversion['templates'][$i]['description'] = '';
$i++;
$modversion['templates'][$i]['file'] = 'tb_formats.html';
$modversion['templates'][$i]['description'] = '';
$i++;
$modversion['templates'][$i]['file'] = 'tb_index.html';
$modversion['templates'][$i]['description'] = '';
$i++;
$modversion['templates'][$i]['file'] = 'tb_mytorrents.html';
$modversion['templates'][$i]['description'] = '';
$i++;
$modversion['templates'][$i]['file'] = 'tb_ok.html';
$modversion['templates'][$i]['description'] = '';
$i++;
$modversion['templates'][$i]['file'] = 'tb_peerlist.html';
$modversion['templates'][$i]['description'] = '';
$i++;
$modversion['templates'][$i]['file'] = 'tb_reputation.html';
$modversion['templates'][$i]['description'] = '';
$i++;
$modversion['templates'][$i]['file'] = 'tb_reputation_ad.html';
$modversion['templates'][$i]['description'] = '';
$i++;
$modversion['templates'][$i]['file'] = 'tb_reputation_settings.html';
$modversion['templates'][$i]['description'] = '';
$i++;
$modversion['templates'][$i]['file'] = 'tb_rules.html';
$modversion['templates'][$i]['description'] = '';
$i++;
$modversion['templates'][$i]['file'] = 'tb_search.html';
$modversion['templates'][$i]['description'] = '';
$i++;
$modversion['templates'][$i]['file'] = 'tb_staff.html';
$modversion['templates'][$i]['description'] = '';
$i++;
$modversion['templates'][$i]['file'] = 'tb_takefilesearch.html';
$modversion['templates'][$i]['description'] = '';
$i++;
$modversion['templates'][$i]['file'] = 'tb_topten.html';
$modversion['templates'][$i]['description'] = '';
$i++;
$modversion['templates'][$i]['file'] = 'tb_upload.html';
$modversion['templates'][$i]['description'] = '';
$i++;
$modversion['templates'][$i]['file'] = 'tb_useragreement.html';
$modversion['templates'][$i]['description'] = '';
$i++;
$modversion['templates'][$i]['file'] = 'tb_userdetails.html';
$modversion['templates'][$i]['description'] = '';
$i++;
$modversion['templates'][$i]['file'] = 'tb_users.html';
$modversion['templates'][$i]['description'] = '';
$i++;
$modversion['templates'][$i]['file'] = 'tb_videoformats.html';
$modversion['templates'][$i]['description'] = '';
$i++;
$modversion['templates'][$i]['file'] = 'tb_viewnfo.html';
$modversion['templates'][$i]['description'] = '';
$i++;
$modversion['templates'][$i]['file'] = 'tb_cpanel_adduser.html';
$modversion['templates'][$i]['description'] = '';
$i++;
$modversion['templates'][$i]['file'] = 'tb_cpanel_bans.html';
$modversion['templates'][$i]['description'] = '';
$i++;
$modversion['templates'][$i]['file'] = 'tb_cpanel_categories.html';
$modversion['templates'][$i]['description'] = '';
$i++;
$modversion['templates'][$i]['file'] = 'tb_cpanel_delacct.html';
$modversion['templates'][$i]['description'] = '';
$i++;
$modversion['templates'][$i]['file'] = 'tb_cpanel_index.html';
$modversion['templates'][$i]['description'] = '';
$i++;
$modversion['templates'][$i]['file'] = 'tb_cpanel_log.html';
$modversion['templates'][$i]['description'] = '';
$i++;
$modversion['templates'][$i]['file'] = 'tb_cpanel_mysql_overview.html';
$modversion['templates'][$i]['description'] = '';
$i++;
$modversion['templates'][$i]['file'] = 'tb_cpanel_mysql_stats.html';
$modversion['templates'][$i]['description'] = '';
$i++;
$modversion['templates'][$i]['file'] = 'tb_cpanel_news.html';
$modversion['templates'][$i]['description'] = '';
$i++;
$modversion['templates'][$i]['file'] = 'tb_cpanel_stats.html';
$modversion['templates'][$i]['description'] = '';
$i++;
$modversion['templates'][$i]['file'] = 'tb_cpanel_testip.html';
$modversion['templates'][$i]['description'] = '';
$i++;
$modversion['templates'][$i]['file'] = 'tb_cpanel_usersearch.html';
$modversion['templates'][$i]['description'] = '';

$i=0;
$i++;
$modversion['sub'][$i]['name'] = _MI_TBDEV_MM1;
$modversion['sub'][$i]['url'] = "search.php";
$i++;
$modversion['sub'][$i]['name'] = _MI_TBDEV_MM2;
$modversion['sub'][$i]['url'] = "chat.php";
$i++;
$modversion['sub'][$i]['name'] = _MI_TBDEV_MM3;
$modversion['sub'][$i]['url'] = "topten.php";

// Menu
if (is_object($GLOBALS['xoopsUser'])) 
{ 
$i++;
$modversion['sub'][$i]['name'] = _MI_TBDEV_MM4;
$modversion['sub'][$i]['url'] = "upload.php";
$i++;
$modversion['sub'][$i]['name'] = _MI_TBDEV_MM5;
$modversion['sub'][$i]['url'] = "mytorrents.php";
}

$modversion['hasMain'] = 1;

$i = 0;
// Config items
$i++;
$modversion['config'][$i]['name'] = 'super_torrents';
$modversion['config'][$i]['title'] = '_MI_TBDEV_SUPER_TORRENTS';
$modversion['config'][$i]['description'] = '_MI_TBDEV_SUPER_TORRENTS_DESC';
$modversion['config'][$i]['formtype'] = 'yesno';
$modversion['config'][$i]['valuetype'] = 'int';
$modversion['config'][$i]['default'] = '1';

$i++;
$modversion['config'][$i]['name'] = 'time_adjust';
$modversion['config'][$i]['title'] = '_MI_TBDEV_TIME_ADJUST';
$modversion['config'][$i]['description'] = '_MI_TBDEV_TIME_ADJUST_DESC';
$modversion['config'][$i]['formtype'] = 'text';
$modversion['config'][$i]['valuetype'] = 'int';
$modversion['config'][$i]['default'] = '0';

$i++;
$modversion['config'][$i]['name'] = 'time_offset';
$modversion['config'][$i]['title'] = '_MI_TBDEV_TIME_OFFSET';
$modversion['config'][$i]['description'] = '_MI_TBDEV_TIME_OFFSET_DESC';
$modversion['config'][$i]['formtype'] = 'text';
$modversion['config'][$i]['valuetype'] = 'int';
$modversion['config'][$i]['default'] = '0';

$i++;
$modversion['config'][$i]['name'] = 'time_use_relative';
$modversion['config'][$i]['title'] = '_MI_TBDEV_TIME_USE_RELATIVE';
$modversion['config'][$i]['description'] = '_MI_TBDEV_TIME_USE_RELATIVE_DESC';
$modversion['config'][$i]['formtype'] = 'yesno';
$modversion['config'][$i]['valuetype'] = 'int';
$modversion['config'][$i]['default'] = '1';

$i++;
$modversion['config'][$i]['name'] = 'time_use_relative_format';
$modversion['config'][$i]['title'] = '_MI_TBDEV_TIME_USE_RELATIVE_FORMAT';
$modversion['config'][$i]['description'] = '_MI_TBDEV_TIME_USE_RELATIVE_FORMAT_DESC';
$modversion['config'][$i]['formtype'] = 'text';
$modversion['config'][$i]['valuetype'] = 'text';
$modversion['config'][$i]['default'] = '{--}, h:i A';

$i++;
$modversion['config'][$i]['name'] = 'time_joined';
$modversion['config'][$i]['title'] = '_MI_TBDEV_TIME_JOINED';
$modversion['config'][$i]['description'] = '_MI_TBDEV_TIME_JOINED_DESC';
$modversion['config'][$i]['formtype'] = 'text';
$modversion['config'][$i]['valuetype'] = 'text';
$modversion['config'][$i]['default'] = 'j-F y';

$i++;
$modversion['config'][$i]['name'] = 'time_short';
$modversion['config'][$i]['title'] = '_MI_TBDEV_TIME_SHORT';
$modversion['config'][$i]['description'] = '_MI_TBDEV_TIME_SHORT_DESC';
$modversion['config'][$i]['formtype'] = 'text';
$modversion['config'][$i]['valuetype'] = 'text';
$modversion['config'][$i]['default'] = 'jS F Y - h:i A';

$i++;
$modversion['config'][$i]['name'] = 'time_long';
$modversion['config'][$i]['title'] = '_MI_TBDEV_TIME_LONG';
$modversion['config'][$i]['description'] = '_MI_TBDEV_TIME_LONG_DESC';
$modversion['config'][$i]['formtype'] = 'text';
$modversion['config'][$i]['valuetype'] = 'text';
$modversion['config'][$i]['default'] = 'M j Y, h:i A';

$i++;
$modversion['config'][$i]['name'] = 'time_tiny';
$modversion['config'][$i]['title'] = '_MI_TBDEV_TIME_TINY';
$modversion['config'][$i]['description'] = '_MI_TBDEV_TIME_TINY_DESC';
$modversion['config'][$i]['formtype'] = 'text';
$modversion['config'][$i]['valuetype'] = 'text';
$modversion['config'][$i]['default'] = 'M-Y H:i A';

$i++;
$modversion['config'][$i]['name'] = 'time_date';
$modversion['config'][$i]['title'] = '_MI_TBDEV_TIME_DATE';
$modversion['config'][$i]['description'] = '_MI_TBDEV_TIME_DATE_DESC';
$modversion['config'][$i]['formtype'] = 'text';
$modversion['config'][$i]['valuetype'] = 'text';
$modversion['config'][$i]['default'] = 'yyyy-mm-dd';

$i++;
$modversion['config'][$i]['name'] = 'cookie_prefix';
$modversion['config'][$i]['title'] = '_MI_TBDEV_COOKIE_PREFIX';
$modversion['config'][$i]['description'] = '_MI_TBDEV_COOKIE_PREFIX_DESC';
$modversion['config'][$i]['formtype'] = 'text';
$modversion['config'][$i]['valuetype'] = 'text';
$modversion['config'][$i]['default'] = 'tbalpha_';

$i++;
$modversion['config'][$i]['name'] = 'cookie_path';
$modversion['config'][$i]['title'] = '_MI_TBDEV_COOKIE_PATH';
$modversion['config'][$i]['description'] = '_MI_TBDEV_COOKIE_PATH_DESC';
$modversion['config'][$i]['formtype'] = 'text';
$modversion['config'][$i]['valuetype'] = 'text';
$modversion['config'][$i]['default'] = '/';

$i++;
$modversion['config'][$i]['name'] = 'cookie_domain';
$modversion['config'][$i]['title'] = '_MI_TBDEV_COOKIE_DOMAIN';
$modversion['config'][$i]['description'] = '_MI_TBDEV_COOKIE_DOMAIN_DESC';
$modversion['config'][$i]['formtype'] = 'text';
$modversion['config'][$i]['valuetype'] = 'text';
$modversion['config'][$i]['default'] = ".".xoops_getBaseDomain(XOOPS_URL);

$i++;
$modversion['config'][$i]['name'] = 'site_online';
$modversion['config'][$i]['title'] = '_MI_TBDEV_SITE_ONLINE';
$modversion['config'][$i]['description'] = '_MI_TBDEV_SITE_ONLINE_DESC';
$modversion['config'][$i]['formtype'] = 'yesno';
$modversion['config'][$i]['valuetype'] = 'int';
$modversion['config'][$i]['default'] = '1';

$i++;
$modversion['config'][$i]['name'] = 'time_adjust';
$modversion['config'][$i]['title'] = '_MI_TBDEV_TIME_ADJUST';
$modversion['config'][$i]['description'] = '_MI_TBDEV_TIME_ADJUST_DESC';
$modversion['config'][$i]['formtype'] = 'text';
$modversion['config'][$i]['valuetype'] = 'int';
$modversion['config'][$i]['default'] = '0';

$i++;
$modversion['config'][$i]['name'] = 'tracker_post_key';
$modversion['config'][$i]['title'] = '_MI_TBDEV_TRACKER_POST_KEY';
$modversion['config'][$i]['description'] = '_MI_TBDEV_TRACKER_POST_KEY_DESC';
$modversion['config'][$i]['formtype'] = 'text';
$modversion['config'][$i]['valuetype'] = 'text';
$modversion['config'][$i]['default'] = (mt_rand(0,1)==1?chr(mt_rand(45, 200)):'').(mt_rand(0,1)==1?chr(mt_rand(45, 200)):'').(mt_rand(0,1)==1?chr(mt_rand(45, 200)):'').(mt_rand(0,1)==1?chr(mt_rand(45, 200)):'').(mt_rand(0,1)==1?chr(mt_rand(45, 200)):'').(mt_rand(0,1)==1?chr(mt_rand(45, 200)):'').(mt_rand(0,1)==1?chr(mt_rand(45, 200)):'').(mt_rand(0,1)==1?chr(mt_rand(45, 200)):'').(mt_rand(0,1)==1?chr(mt_rand(45, 200)):'').(mt_rand(0,1)==1?chr(mt_rand(45, 200)):'').(mt_rand(0,1)==1?chr(mt_rand(45, 200)):'').(mt_rand(0,1)==1?chr(mt_rand(45, 200)):'').(mt_rand(0,1)==1?chr(mt_rand(45, 200)):'').(mt_rand(0,1)==1?chr(mt_rand(45, 200)):'').(mt_rand(0,1)==1?chr(mt_rand(45, 200)):'').(mt_rand(0,1)==1?chr(mt_rand(45, 200)):'').(mt_rand(0,1)==1?chr(mt_rand(45, 200)):'').(mt_rand(0,1)==1?chr(mt_rand(45, 200)):'').(mt_rand(0,1)==1?chr(mt_rand(45, 200)):'').(mt_rand(0,1)==1?chr(mt_rand(45, 200)):'').(mt_rand(0,1)==1?chr(mt_rand(45, 200)):'').(mt_rand(0,1)==1?chr(mt_rand(45, 200)):'').(mt_rand(0,1)==1?chr(mt_rand(45, 200)):'').(mt_rand(0,1)==1?chr(mt_rand(45, 200)):'').(mt_rand(0,1)==1?chr(mt_rand(45, 200)):'').(mt_rand(0,1)==1?chr(mt_rand(45, 200)):'').(mt_rand(0,1)==1?chr(mt_rand(45, 200)):'').(mt_rand(0,1)==1?chr(mt_rand(45, 200)):'').(mt_rand(0,1)==1?chr(mt_rand(45, 200)):'').(mt_rand(0,1)==1?chr(mt_rand(45, 200)):'').(mt_rand(0,1)==1?chr(mt_rand(45, 200)):'').(mt_rand(0,1)==1?chr(mt_rand(45, 200)):'').(mt_rand(0,1)==1?chr(mt_rand(45, 200)):'').(mt_rand(0,1)==1?chr(mt_rand(45, 200)):'').(mt_rand(0,1)==1?chr(mt_rand(45, 200)):'').(mt_rand(0,1)==1?chr(mt_rand(45, 200)):'').(mt_rand(0,1)==1?chr(mt_rand(45, 200)):'').(mt_rand(0,1)==1?chr(mt_rand(45, 200)):'').(mt_rand(0,1)==1?chr(mt_rand(45, 200)):'').(mt_rand(0,1)==1?chr(mt_rand(45, 200)):'').(mt_rand(0,1)==1?chr(mt_rand(45, 200)):'').(mt_rand(0,1)==1?chr(mt_rand(45, 200)):'').(mt_rand(0,1)==1?chr(mt_rand(45, 200)):'').(mt_rand(0,1)==1?chr(mt_rand(45, 200)):'').(mt_rand(0,1)==1?chr(mt_rand(45, 200)):'').(mt_rand(0,1)==1?chr(mt_rand(45, 200)):'').(mt_rand(0,1)==1?chr(mt_rand(45, 200)):'').(mt_rand(0,1)==1?chr(mt_rand(45, 200)):'').(mt_rand(0,1)==1?chr(mt_rand(45, 200)):'').(mt_rand(0,1)==1?chr(mt_rand(45, 200)):'').(mt_rand(0,1)==1?chr(mt_rand(45, 200)):'').(mt_rand(0,1)==1?chr(mt_rand(45, 200)):'').(mt_rand(0,1)==1?chr(mt_rand(45, 200)):'').(mt_rand(0,1)==1?chr(mt_rand(45, 200)):'').(mt_rand(0,1)==1?chr(mt_rand(45, 200)):'').(mt_rand(0,1)==1?chr(mt_rand(45, 200)):'').(mt_rand(0,1)==1?chr(mt_rand(45, 200)):'').(mt_rand(0,1)==1?chr(mt_rand(45, 200)):'').(mt_rand(0,1)==1?chr(mt_rand(45, 200)):'').(mt_rand(0,1)==1?chr(mt_rand(45, 200)):'').(mt_rand(0,1)==1?chr(mt_rand(45, 200)):'').(mt_rand(0,1)==1?chr(mt_rand(45, 200)):'').(mt_rand(0,1)==1?chr(mt_rand(45, 200)):'').(mt_rand(0,1)==1?chr(mt_rand(45, 200)):'').(mt_rand(0,1)==1?chr(mt_rand(45, 200)):'').(mt_rand(0,1)==1?chr(mt_rand(45, 200)):'').(mt_rand(0,1)==1?chr(mt_rand(45, 200)):'').(mt_rand(0,1)==1?chr(mt_rand(45, 200)):'').(mt_rand(0,1)==1?chr(mt_rand(45, 200)):'').(mt_rand(0,1)==1?chr(mt_rand(45, 200)):'').(mt_rand(0,1)==1?chr(mt_rand(45, 200)):'').(mt_rand(0,1)==1?chr(mt_rand(45, 200)):'').(mt_rand(0,1)==1?chr(mt_rand(45, 200)):'').(mt_rand(0,1)==1?chr(mt_rand(45, 200)):'').(mt_rand(0,1)==1?chr(mt_rand(45, 200)):'').(mt_rand(0,1)==1?chr(mt_rand(45, 200)):'');

$i++;
$modversion['config'][$i]['name'] = 'max_torrent_size';
$modversion['config'][$i]['title'] = '_MI_TBDEV_MAX_TORRENT_SIZE';
$modversion['config'][$i]['description'] = '_MI_TBDEV_MAX_TORRENT_SIZE_DESC';
$modversion['config'][$i]['formtype'] = 'text';
$modversion['config'][$i]['valuetype'] = 'int';
$modversion['config'][$i]['default'] = '1000000';

$i++;
$modversion['config'][$i]['name'] = 'announce_interval';
$modversion['config'][$i]['title'] = '_MI_TBDEV_ANNOUNCE_INTERVAL';
$modversion['config'][$i]['description'] = '_MI_TBDEV_ANNOUNCE_INTERVAL_DESC';
$modversion['config'][$i]['formtype'] = 'text';
$modversion['config'][$i]['valuetype'] = 'int';
$modversion['config'][$i]['default'] = 60 * 30;

$i++;
$modversion['config'][$i]['name'] = 'signup_timeout';
$modversion['config'][$i]['title'] = '_MI_TBDEV_SIGNUP_TIMEOUT';
$modversion['config'][$i]['description'] = '_MI_TBDEV_SIGNUP_TIMEOUT_DESC';
$modversion['config'][$i]['formtype'] = 'text';
$modversion['config'][$i]['valuetype'] = 'int';
$modversion['config'][$i]['default'] = 86400 * 3;

$i++;
$modversion['config'][$i]['name'] = 'minvotes';
$modversion['config'][$i]['title'] = '_MI_TBDEV_MINVOTES';
$modversion['config'][$i]['description'] = '_MI_TBDEV_MINVOTES_DESC';
$modversion['config'][$i]['formtype'] = 'text';
$modversion['config'][$i]['valuetype'] = 'int';
$modversion['config'][$i]['default'] = '1';

$i++;
$modversion['config'][$i]['name'] = 'max_dead_torrent_time';
$modversion['config'][$i]['title'] = '_MI_TBDEV_MAX_DEAD_TORRENT_TIME';
$modversion['config'][$i]['description'] = '_MI_TBDEV_MAX_DEAD_TORRENT_TIME_DESC';
$modversion['config'][$i]['formtype'] = 'text';
$modversion['config'][$i]['valuetype'] = 'int';
$modversion['config'][$i]['default'] = 6 * 3600;

$i++;
$modversion['config'][$i]['name'] = 'maxusers';
$modversion['config'][$i]['title'] = '_MI_TBDEV_MAXUSERS';
$modversion['config'][$i]['description'] = '_MI_TBDEV_MAXUSERS_DESC';
$modversion['config'][$i]['formtype'] = 'text';
$modversion['config'][$i]['valuetype'] = 'int';
$modversion['config'][$i]['default'] = '5000';

$i++;
$modversion['config'][$i]['name'] = 'torrent_dir';
$modversion['config'][$i]['title'] = '_MI_TBDEV_TORRENT_DIR';
$modversion['config'][$i]['description'] = '_MI_TBDEV_TORRENT_DIR_DESC';
$modversion['config'][$i]['formtype'] = 'text';
$modversion['config'][$i]['valuetype'] = 'text';
$modversion['config'][$i]['default'] = XOOPS_UPLOAD_PATH.'/tb/torrents';

$i++;
$modversion['config'][$i]['name'] = 'announce_urlss';
$modversion['config'][$i]['title'] = '_MI_TBDEV_ANNOUNCE_URLS';
$modversion['config'][$i]['description'] = '_MI_TBDEV_ANNOUNCE_URLS_DESC';
$modversion['config'][$i]['formtype'] = 'text';
$modversion['config'][$i]['valuetype'] = 'text';
$modversion['config'][$i]['default'] = XOOPS_URL.'/modules/tb/announce.php';

$i++;
$modversion['config'][$i]['name'] = 'baseurl';
$modversion['config'][$i]['title'] = '_MI_TBDEV_BASEURL';
$modversion['config'][$i]['description'] = '_MI_TBDEV_BASEURL_DESC';
$modversion['config'][$i]['formtype'] = 'text';
$modversion['config'][$i]['valuetype'] = 'text';
$modversion['config'][$i]['default'] = XOOPS_URL.'/modules/tb';

$i++;
$modversion['config'][$i]['name'] = 'site_email';
$modversion['config'][$i]['title'] = '_MI_TBDEV_SITE_EMAIL';
$modversion['config'][$i]['description'] = '_MI_TBDEV_SITE_EMAIL_DESC';
$modversion['config'][$i]['formtype'] = 'text';
$modversion['config'][$i]['valuetype'] = 'text';
$modversion['config'][$i]['default'] = $GLOBALS['xoopsConfig']['adminemail'];

$i++;
$modversion['config'][$i]['name'] = 'site_name';
$modversion['config'][$i]['title'] = '_MI_TBDEV_SITE_NAME';
$modversion['config'][$i]['description'] = '_MI_TBDEV_SITE_NAME_DESC';
$modversion['config'][$i]['formtype'] = 'text';
$modversion['config'][$i]['valuetype'] = 'text';
$modversion['config'][$i]['default'] = $GLOBALS['xoopsConfig']['sitename'];

$i++;
$modversion['config'][$i]['name'] = 'char_set';
$modversion['config'][$i]['title'] = '_MI_TBDEV_CHAR_SET';
$modversion['config'][$i]['description'] = '_MI_TBDEV_CHAR_SET_DESC';
$modversion['config'][$i]['formtype'] = 'text';
$modversion['config'][$i]['valuetype'] = 'text';
$modversion['config'][$i]['default'] = _CHARSET;

$i++;
$modversion['config'][$i]['name'] = 'msg_alert';
$modversion['config'][$i]['title'] = '_MI_TBDEV_MSG_ALERT';
$modversion['config'][$i]['description'] = '_MI_TBDEV_MSG_ALERT_DESC';
$modversion['config'][$i]['formtype'] = 'yesno';
$modversion['config'][$i]['valuetype'] = 'int';
$modversion['config'][$i]['default'] = 0;

$i++;
$modversion['config'][$i]['name'] = 'autoclean_interval';
$modversion['config'][$i]['title'] = '_MI_TBDEV_AUTOCLEAN_INTERVAL';
$modversion['config'][$i]['description'] = '_MI_TBDEV_AUTOCLEAN_INTERVAL_DESC';
$modversion['config'][$i]['formtype'] = 'text';
$modversion['config'][$i]['valuetype'] = 'int';
$modversion['config'][$i]['default'] = '900';

$i++;
$modversion['config'][$i]['name'] = 'sql_error_log';
$modversion['config'][$i]['title'] = '_MI_TBDEV_SQL_ERROR_LOG';
$modversion['config'][$i]['description'] = '_MI_TBDEV_SQL_ERROR_LOG_DESC';
$modversion['config'][$i]['formtype'] = 'text';
$modversion['config'][$i]['valuetype'] = 'text';
$modversion['config'][$i]['default'] = XOOPS_ROOT_PATH.'/modules/tb/log/sql_err_'.date("M_D_Y").'.log';

$i++;
$modversion['config'][$i]['name'] = 'pic_base_url';
$modversion['config'][$i]['title'] = '_MI_TBDEV_PIC_BASE_URL';
$modversion['config'][$i]['description'] = '_MI_TBDEV_PIC_BASE_URL_DESC';
$modversion['config'][$i]['formtype'] = 'text';
$modversion['config'][$i]['valuetype'] = 'text';
$modversion['config'][$i]['default'] = XOOPS_URL.'/modules/tb/images/';

$i++;
$modversion['config'][$i]['name'] = 'pic_base_path';
$modversion['config'][$i]['title'] = '_MI_TBDEV_PIC_BASE_PATH';
$modversion['config'][$i]['description'] = '_MI_TBDEV_PIC_BASE_PATH_DESC';
$modversion['config'][$i]['formtype'] = 'text';
$modversion['config'][$i]['valuetype'] = 'text';
$modversion['config'][$i]['default'] = XOOPS_ROOT_PATH.'/modules/tb/images/';

$i++;
$modversion['config'][$i]['name'] = 'stylesheet';
$modversion['config'][$i]['title'] = '_MI_TBDEV_STYLESHEET';
$modversion['config'][$i]['description'] = '_MI_TBDEV_STYLESHEET_DESC';
$modversion['config'][$i]['formtype'] = 'text';
$modversion['config'][$i]['valuetype'] = 'text';
$modversion['config'][$i]['default'] = '1.css';

$i++;
$modversion['config'][$i]['name'] = 'readpost_expiry';
$modversion['config'][$i]['title'] = '_MI_TBDEV_READPOST_EXPIRTY';
$modversion['config'][$i]['description'] = '_MI_TBDEV_READPOST_EXPIRTY_DESC';
$modversion['config'][$i]['formtype'] = 'text';
$modversion['config'][$i]['valuetype'] = 'int';
$modversion['config'][$i]['default'] = 14*86400;

$i++;
$modversion['config'][$i]['name'] = 'av_img_height';
$modversion['config'][$i]['title'] = '_MI_TBDEV_AV_IMG_HEIGHT';
$modversion['config'][$i]['description'] = '_MI_TBDEV_AV_IMG_HEIGHT_DESC';
$modversion['config'][$i]['formtype'] = 'text';
$modversion['config'][$i]['valuetype'] = 'int';
$modversion['config'][$i]['default'] = '100';

$i++;
$modversion['config'][$i]['name'] = 'av_img_width';
$modversion['config'][$i]['title'] = '_MI_TBDEV_AV_IMG_WIDTH';
$modversion['config'][$i]['description'] = '_MI_TBDEV_AV_IMG_WIDTH_DESC';
$modversion['config'][$i]['formtype'] = 'text';
$modversion['config'][$i]['valuetype'] = 'int';
$modversion['config'][$i]['default'] = '100';

$i++;
$modversion['config'][$i]['name'] = 'allowed_exts';
$modversion['config'][$i]['title'] = '_MI_TBDEV_ALLOWED_EXT';
$modversion['config'][$i]['description'] = '_MI_TBDEV_ALLOWED_EXT_DESC';
$modversion['config'][$i]['formtype'] = 'text';
$modversion['config'][$i]['valuetype'] = 'text';
$modversion['config'][$i]['default'] = 'image/gif|image/png|image/jpeg';

$i++;
$modversion['config'][$i]['name'] = 'user_ratios';
$modversion['config'][$i]['title'] = '_MI_TBDEV_USER_RATIO';
$modversion['config'][$i]['description'] = '_MI_TBDEV_USER_RATIO_DESC';
$modversion['config'][$i]['formtype'] = 'text';
$modversion['config'][$i]['valuetype'] = 'int';
$modversion['config'][$i]['default'] = '0';

$i++;
$modversion['config'][$i]['name'] = 'connectable_check';
$modversion['config'][$i]['title'] = '_MI_TBDEV_CONNECTABLE_CHECK';
$modversion['config'][$i]['description'] = '_MI_TBDEV_CONNECTABLE_CHECK_DESC';
$modversion['config'][$i]['formtype'] = 'yesno';
$modversion['config'][$i]['valuetype'] = 'int';
$modversion['config'][$i]['default'] = '0';

$i++;
$modversion['config'][$i]['name'] = 'bb_upload_size';
$modversion['config'][$i]['title'] = '_MI_TBDEV_BB_UPLOAD_SIZE';
$modversion['config'][$i]['description'] = '_MI_TBDEV_BB_UPLOAD_SIZE_DESC';
$modversion['config'][$i]['formtype'] = 'text';
$modversion['config'][$i]['valuetype'] = 'int';
$modversion['config'][$i]['default'] = 256 * 1024;

$i++;
$modversion['config'][$i]['name'] = 'bb_upload_path';
$modversion['config'][$i]['title'] = '_MI_TBDEV_BB_UPLOAD_PATH';
$modversion['config'][$i]['description'] = '_MI_TBDEV_BB_UPLOAD_PATH_DESC';
$modversion['config'][$i]['formtype'] = 'text';
$modversion['config'][$i]['valuetype'] = 'text';
$modversion['config'][$i]['default'] = XOOPS_UPLOAD_PATH.'/tb/bitbucket';

$i++;
$modversion['config'][$i]['name'] = 'catsperrow';
$modversion['config'][$i]['title'] = '_MI_TBDEV_CATSPERROW';
$modversion['config'][$i]['description'] = '_MI_TBDEV_CATSPERROW_DESC';
$modversion['config'][$i]['formtype'] = 'text';
$modversion['config'][$i]['valuetype'] = 'int';
$modversion['config'][$i]['default'] = '7';

$i++;
$modversion['config'][$i]['name'] = 'htaccess';
$modversion['config'][$i]['title'] = "_MI_TBDEV_HTACCESS";
$modversion['config'][$i]['description'] = "_MI_TBDEV_HTACCESS_DESC";
$modversion['config'][$i]['formtype'] = 'yesno';
$modversion['config'][$i]['valuetype'] = 'int';
$modversion['config'][$i]['default'] = 0;

$i++;
$modversion['config'][$i]['name'] = 'basefolder';
$modversion['config'][$i]['title'] = "_MI_TBDEV_BASEFOLDER";
$modversion['config'][$i]['description'] = "_MI_TBDEV_BASEFOLDER_DESC";
$modversion['config'][$i]['formtype'] = 'text';
$modversion['config'][$i]['valuetype'] = 'text';
$modversion['config'][$i]['default'] = 'tbdev';
$i++;

$modversion['config'][$i]['name'] = 'endofurl';
$modversion['config'][$i]['title'] = "_MI_TBDEV_ENDOFURL";
$modversion['config'][$i]['description'] = "_MI_TBDEV_ENDOFURL_DESC";
$modversion['config'][$i]['formtype'] = 'text';
$modversion['config'][$i]['valuetype'] = 'text';
$modversion['config'][$i]['default'] = '.html';
$i++;

$modversion['config'][$i]['name'] = 'endofurl_rss';
$modversion['config'][$i]['title'] = "_MI_TBDEV_ENDOFURLRSS";
$modversion['config'][$i]['description'] = "_MI_TBDEV_ENDOFURLRSS_DESC";
$modversion['config'][$i]['formtype'] = 'text';
$modversion['config'][$i]['valuetype'] = 'text';
$modversion['config'][$i]['default'] = '.rss';
$i++;

$modversion['config'][$i]['name'] = 'endofurl_pdf';
$modversion['config'][$i]['title'] = "_MI_TBDEV_ENDOFURLPDF";
$modversion['config'][$i]['description'] = "_MI_TBDEV_ENDOFURLPDF_DESC";
$modversion['config'][$i]['formtype'] = 'text';
$modversion['config'][$i]['valuetype'] = 'text';
$modversion['config'][$i]['default'] = '.pdf';

$i++;
$modversion['config'][$i]['name'] = 'irc_url';
$modversion['config'][$i]['title'] = '_MI_TBDEV_IRC_URL';
$modversion['config'][$i]['description'] = '_MI_TBDEV_IRC_URL_DESC';
$modversion['config'][$i]['formtype'] = 'text';
$modversion['config'][$i]['valuetype'] = 'text';
$modversion['config'][$i]['default'] = 'efnet.port80.se';

$i++;
$modversion['config'][$i]['name'] = 'irc_channel';
$modversion['config'][$i]['title'] = '_MI_TBDEV_IRC_CHANNEL';
$modversion['config'][$i]['description'] = '_MI_TBDEV_IRC_CHANNEL_DESC';
$modversion['config'][$i]['formtype'] = 'text';
$modversion['config'][$i]['valuetype'] = 'text';
$modversion['config'][$i]['default'] = '#TBDEVNET';

?>

<?php
/**
 * Donation Module for XPayment
 *
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright       Chronolabs Co-Op http://www.chronolabs.coop/
 * @license         http://www.fsf.org/copyleft/gpl.html GNU public license
 * @package         xpayment
 * @since           1.30.0
 * @author          Simon Roberts <simon@chronolabs.coop>
 */

global $adminmenu;
$adminmenu=array();
$i=0;
$i++;
$adminmenu[$i]['title'] = _DNS_MI_ADMENU1;
$adminmenu[$i]['icon'] = 'images/admin/dashboard.png';
$adminmenu[$i]['image'] = 'images/admin/dashboard.png';
$adminmenu[$i]['link'] = "admin.php?action=index";
$i++;
$adminmenu[$i]['title'] = _DNS_MI_ADMENU2;
$adminmenu[$i]['icon'] = 'images/admin/bans.png';
$adminmenu[$i]['image'] = 'images/admin/bans.png';
$adminmenu[$i]['link'] = "admin.php?action=bans";
$i++;
$adminmenu[$i]['title'] = _DNS_MI_ADMENU3;
$adminmenu[$i]['icon'] = 'images/admin/adduser.png';
$adminmenu[$i]['image'] = 'images/admin/adduser.png';
$adminmenu[$i]['link'] = "admin.php?action=adduser";
$i++;
$adminmenu[$i]['title'] = _DNS_MI_ADMENU4;
$adminmenu[$i]['icon'] = 'images/admin/stats.png';
$adminmenu[$i]['image'] = 'images/admin/stats.png';
$adminmenu[$i]['link'] = "admin.php?action=stats";
$i++;
$adminmenu[$i]['title'] = _DNS_MI_ADMENU5;
$adminmenu[$i]['icon'] = 'images/admin/delacct.png';
$adminmenu[$i]['image'] = 'images/admin/delacct.png';
$adminmenu[$i]['link'] = "admin.php?action=delacct";
$i++;
$adminmenu[$i]['title'] = _DNS_MI_ADMENU6;
$adminmenu[$i]['icon'] = 'images/admin/testip.png';
$adminmenu[$i]['image'] = 'images/admin/testip.png';
$adminmenu[$i]['link'] = "admin.php?action=testip";
$i++;
$adminmenu[$i]['title'] = _DNS_MI_ADMENU7;
$adminmenu[$i]['icon'] = 'images/admin/usersearch.png';
$adminmenu[$i]['image'] = 'images/admin/usersearch.png';
$adminmenu[$i]['link'] = "admin.php?action=usersearch";
$i++;
$adminmenu[$i]['title'] = _DNS_MI_ADMENU8;
$adminmenu[$i]['icon'] = 'images/admin/mysql_overview.png';
$adminmenu[$i]['image'] = 'images/admin/mysql_overview.png';
$adminmenu[$i]['link'] = "admin.php?action=mysql_overview";
$i++;
$adminmenu[$i]['title'] = _DNS_MI_ADMENU9;
$adminmenu[$i]['icon'] = 'images/admin/mysql_stats.png';
$adminmenu[$i]['image'] = 'images/admin/mysql_stats.png';
$adminmenu[$i]['link'] = "admin.php?action=mysql_stats";
$i++;
$adminmenu[$i]['title'] = _DNS_MI_ADMENU10;
$adminmenu[$i]['icon'] = 'images/admin/categories.png';
$adminmenu[$i]['image'] = 'images/admin/categories.png';
$adminmenu[$i]['link'] = "admin.php?action=categories";
$i++;
$adminmenu[$i]['title'] = _DNS_MI_ADMENU11;
$adminmenu[$i]['icon'] = 'images/admin/newusers.png';
$adminmenu[$i]['image'] = 'images/admin/newusers.png';
$adminmenu[$i]['link'] = "admin.php?action=newusers";
$i++;
$adminmenu[$i]['title'] = _DNS_MI_ADMENU12;
$adminmenu[$i]['icon'] = 'images/admin/docleanup.png';
$adminmenu[$i]['image'] = 'images/admin/docleanup.png';
$adminmenu[$i]['link'] = "admin.php?action=docleanup";
$i++;
$adminmenu[$i]['title'] = _DNS_MI_ADMENU13;
$adminmenu[$i]['icon'] = 'images/admin/log.png';
$adminmenu[$i]['image'] = 'images/admin/log.png';
$adminmenu[$i]['link'] = "admin.php?action=log";
$i++;
$adminmenu[$i]['title'] = _DNS_MI_ADMENU14;
$adminmenu[$i]['icon'] = 'images/admin/news.png';
$adminmenu[$i]['image'] = 'images/admin/news.png';
$adminmenu[$i]['link'] = "admin.php?action=news";
?>
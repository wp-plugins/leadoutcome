<?php/**
Plugin Name: LeadOutcome
Version: 1.0
Plugin URI: http://wordpress.org/extend/plugins/leadoutcome
Author: Chad Horton
Author URI: http://www.leadoutcome.com
Contributors: LeadOutcome.com, Chad Horton 
Donate Link: http://www.leadoutcome.com
Tags: marketing,sales,leads,email,email marketing,crm,leadoutcome
Description: LeadOutcome provides you with every tool and a robust system for lead activity tracking and management, lead conversions, email marketing, and more
Text Domain: lo
Copyright: 2013
WordPress Versions: 3.1 and above
Tested up to: 3.5
Stable tag: Stable
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html


GNU General Public License, Free Software Foundation <http://creativecommons.org/licenses/GPL/2.0/>
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
**/
$profile_plugin_dir = basename(dirname(__FILE__));@define('LO_PLUGIN_DIR',$profile_plugin_dir);@define('LO_PLUGIN_ABS_PATH_DIR',$_SERVER['DOCUMENT_ROOT'].'/wp-content/plugins/'.LO_PLUGIN_DIR);//@define('JQUERY_UI_THEME','black-tie');//@define('JQUERY_UI_THEME','blitzer');//@define('JQUERY_UI_THEME','south-street');//@define('JQUERY_UI_THEME','smoothness');@define('JQUERY_UI_THEME','cupertino');if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])){	die('Access Denied');}global $is_IIS;# hack for some IIS installationsif ($is_IIS && @ini_get('error_log') == '') @ini_set('error_log', 'syslog');include_once('includes/config.php');include_once('includes/functions.php');include_once('includes/hooks.php');register_activation_hook(__FILE__, 'lo_plugin_activate');
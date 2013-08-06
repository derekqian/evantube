<?php
@error_reporting(0);
@ini_set('display_errors', 0);
@session_start();
//-----------------------------------------------------------------//
//
// PHPmotion Installation Wizard - Version 2
//
//-----------------------------------------------------------------//

//mysql_debug
$quickmysql_debug = 0; //set to 1 for mysql debug output

//some definitions
define('BASE_PATH',str_replace('/setup/includes', '', dirname(__file__)));
define('SETUP_PATH',str_replace('/setup/includes', '/setup', dirname(__file__)));
define('TEMPLATE_PATH',str_replace('/includes', '/template', dirname(__file__)));
define('MAIN_TEMPLATE',str_replace('/includes', '/template/template.htm', dirname(__file__)));

//include functions
include_once(dirname(__file__).'/functions.php');
include_once(dirname(__file__).'/formprocessor.php');

//list of chmod dir's
$dir_paths = array('dir1' =>'/addons',
                   'dir2' =>'/addons/customprofile',
				   'dir3' =>'/addons/customprofile/member_css',
				   'dir4' =>'/addons/customprofile/member_images',
				   'dir5' =>'/addons/albums',
				   'dir6' =>'/addons/albums/images',
				   'dir7' =>'/addons/albums/thumbs',
				   'dir8' =>'/classes',
				   'dir9' =>'/logs',
				   'dir10' =>'/pictures',
				   'dir11' =>'/temp',
				   'dir12' =>'/uploads',
				   'dir13' =>'/uploads/avi',
				   'dir14' =>'/uploads/thumbs',
				   'dir15' =>'/uploads/audio',
				   'dir16' =>'/uploads/player_thumbs',
				   'dir17' =>'/setup');


















?>
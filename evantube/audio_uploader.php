<?php
///////////////////////////////////////////////////////////////////////////////////////
// PHPmotion                                                http://www.phpmotion.com //
///////////////////////////////////////////////////////////////////////////////////////
// License: You are not to sell or distribute this software without permission       //
// Help and support please visit http://www.phpmotion.com                            //
// Copyright reserved                                                                //
///////////////////////////////////////////////////////////////////////////////////////

include_once ('classes/config.php');
include_once ('classes/sessions.php');
include_once ('classes/login_check.php');
include_once ('includes/enabled_features.php');

include 'audio_uploader_conlib.php';

$page_title			= $lang_upload_audio;
$procede			= true;
$load_javascript		= 4;
$load_javascript_bar 	= 4;

// Check if Feature is enabled
DisabledFeatureRedirect($audio); //for usage see /includes/menus.inc.php

$THIS_VERSION = "2.0";

if (isset($_GET['cmd']) && $_GET['cmd'] == 'about') {
     	kak("<u><b>UBER UPLOADER FILE UPLOAD</b></u><br>UBER UPLOADER VERSION =  <b>" . $UBER_VERSION . "</b><br>UU_FILE_UPLOAD = <b>" . $THIS_VERSION . "<b><br>\n");
}

$tmp_sid = md5(uniqid(mt_rand(), true));
$config_file = $default_config_file;
$path_to_upload_script .= '?tmp_sid=' . $tmp_sid;
$path_to_ini_status_script .= '?tmp_sid=' . $tmp_sid;

if ($MULTI_CONFIGS_ENABLED) {
    	$path_to_upload_script .= "&config_file=$config_file";
     	$path_to_ini_status_script .= "&config_file=$config_file";
}

$blk_id		= 0;
$template 		= "themes/$user_theme/templates/main_1.htm";
$inner_template1 	= "themes/$user_theme/templates/inner_upload_audio.htm";
$TBS 			= new clsTinyButStrong;

$TBS->NoErr = true;
$TBS->LoadTemplate("$template");

$TBS->Render 	= TBS_OUTPUT;
$TBS->Show();

@mysql_close();
die();

?>
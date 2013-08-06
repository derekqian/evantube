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

include 'uploader_conlib.php';

$vid				= (int) mysql_real_escape_string($_GET['vid']);
$sql 				= "SELECT indexer, title, channel, channel_id FROM videos WHERE indexer = $vid";
$query 			= @mysql_query($sql);
$result 			= @mysql_fetch_array($query);
$response_title 		= $result['title'];
$response_channel 	= $result['channel'];
$response_channel_id 	= $result['channel_id'];


// posted form
$form_submitted	= $_POST['form_submitted'];
$title		= $_POST['title'];
$description 	= $_POST['description'];
$tags 		= $_POST['tags'];

$vid_response 	= $_POST['vid_response'];
$response_id 	= $_POST['response_id'];
$channel_id 	= $_POST['channel_id'];
$channel		= $_POST['channel'];

$procede = true;

// validate form
if ( $form_submitted == 'yes' ) {

	foreach ($_POST as $key => $value) {

      	if ($key == "title" || $key == "description" || $key == "tags") {
            	if (!isset($value) || ($value == "")) {
                		$display_key	= @str_replace('_', " ", $key);
                		//$error_message	= $config['notification_error'];
                		$blk_notification = 1;
                		$error_message 	= $config['notification_error'] . " - " . $display_key . "  - $lang_required ";
                		$procede 		= false;
            	}
        	}
    	}
}

// display page with form error
if ( $procede == false && $form_submitted == 'yes' ) {

	$template		= "themes/$user_theme/templates/inner_upload_video_response_form.htm";
	$TBS 			= new clsTinyButStrong;
    	$TBS->NoErr 	= true;

    	$TBS->LoadTemplate("$template");

    	$TBS->Render	= TBS_OUTPUT;
    	$TBS->Show();

	@mysql_close();
    	die();
}

// show clean page
if ( !isset($form_submitted) || ($form_submitted == '') ) {

	$blk_id 		= 0;
	$template		= "themes/$user_theme/templates/inner_upload_video_response_form.htm";
	$TBS			= new clsTinyButStrong;
    	$TBS->NoErr 	= true;

    	$TBS->LoadTemplate("$template");

    	$TBS->Render	= TBS_OUTPUT;
    	$TBS->Show();

	@mysql_close();
    	die();
}

//================================START OF UPLOAD====================================

if ( $procede == true && $form_submitted == 'yes' ) {

	$THIS_VERSION = "2.0";

    	if (isset($_GET['cmd']) && $_GET['cmd'] == 'about') {

      	kak("<u><b>UBER UPLOADER FILE UPLOAD</b></u><br>UBER UPLOADER VERSION =  <b>" . $UBER_VERSION . "</b><br>UU_FILE_UPLOAD = <b>" . $THIS_VERSION . "<b><br>\n");
      }

      $tmp_sid = md5(uniqid(mt_rand(), true));

    	///////////////////////////////////////////////////////////////////////
    	// This is where you might set your config file eg.                  //
    	// if($_SESSION['user'] == "tom"){ $config_file = 'uu_tom_config'; } //
    	///////////////////////////////////////////////////////////////////////

    	$config_file = $default_config_file;

    	$path_to_upload_script .= '?tmp_sid=' . $tmp_sid;
    	$path_to_ini_status_script .= '?tmp_sid=' . $tmp_sid;

    	if ($MULTI_CONFIGS_ENABLED) {
      	$path_to_upload_script .= "&config_file=$config_file";
        	$path_to_ini_status_script .= "&config_file=$config_file";
   	}


    	$blk_id		= 0;
    	$template 		= "themes/$user_theme/templates/inner_upload_video_response.htm";
    	$TBS 			= new clsTinyButStrong;
    	$TBS->NoErr 	= true;// no more error message displayed.

    	$TBS->LoadTemplate("$template");

    	$TBS->Render	= TBS_OUTPUT;
    	$TBS->Show();

    	@mysql_close();
    	die();
    	//==================================END OF UPLOADER======================================
}

?>
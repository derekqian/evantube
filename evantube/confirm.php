<?php

///////////////////////////////////////////////////////////////////////////////////////
// PHPmotion                                                http://www.phpmotion.com //
///////////////////////////////////////////////////////////////////////////////////////
// License: You are not to sell or distribute this software without permission       //
// Help and support please visit http://www.phpmotion.com                            //
// Copyright reserved                                                                //
///////////////////////////////////////////////////////////////////////////////////////

include_once ('classes/config.php');

$random_code = mysql_real_escape_string($_GET['id']);

if (!isset($random_code) || $random_code == '') {
	header("Location: " . "index.php");
    	die();
}

$sql			= "SELECT user_id FROM member_profile WHERE random_code = '$random_code' AND account_status = 'new'";
$query 		= @mysql_query($sql);
$result 		= @mysql_num_rows($query);

$show_action_msg	= 0;
$show_signup	= 0;
$show_login		= 0;

if ($result != 0) {
	$show_signup	= 0;
	$show_login		= 1;
	$show_action_msg	= 1;

	// activate member and show success message
    	$sql		= "UPDATE member_profile SET account_status = 'active' WHERE random_code = '$random_code'";
    	@mysql_query($sql);

    	//$blk_notification = 1;

    	$message_type	= $config['notification_success'];
    	$error_message 	= $config['reg_confirm_complete'];

    	$action_msg		= $message_type . " - " . $error_message;

    	$template 		= "themes/$user_theme/templates/main_1.htm";
    	$inner_template1 	= "themes/$user_theme/templates/inner_signup_form.htm";

    	$TBS 			= new clsTinyButStrong;
    	$TBS->NoErr 	= true;

    	$TBS->LoadTemplate("$template");
    	$TBS->Render 	= TBS_OUTPUT;
    	$TBS->Show();

   	@mysql_close();
    	die();

} else {

	$show_signup	= 0;
	$show_login		= 1;
	$show_action_msg	= 1;

	$message_type	= $config['notification_error'];
	$error_message 	= $config['must_login'];

    	$action_msg		= $message_type . " - " . $error_message;

    	//$blk_notification = 1;

    	$template		= "themes/$user_theme/templates/main_1.htm";
    	$inner_template1 	= "themes/$user_theme/templates/inner_signup_form.htm";

    	$TBS 			= new clsTinyButStrong;
    	$TBS->NoErr 	= true;

    	$TBS->LoadTemplate("$template");
    	$TBS->Render 	= TBS_OUTPUT;
    	$TBS->Show();

    	@mysql_close();
    	die();
}

?>
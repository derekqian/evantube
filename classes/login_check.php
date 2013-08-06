<?php

///////////////////////////////////////////////////////////////////////////////////////
// PHPmotion                                                http://www.phpmotion.com //
///////////////////////////////////////////////////////////////////////////////////////
// License: You are not to sell or distribute this software without permission       //
// Help and support please visit http://www.phpmotion.com                            //
// Copyright reserved                                                                //
///////////////////////////////////////////////////////////////////////////////////////

if ($_SESSION['user_id'] == "") {

	$show_signup	= 0;
	$show_login		= 1;
	$error_message	= $config['must_login'];
	$message_type	= $config['notification_must_login'];

	$template		= "themes/$user_theme/templates/main_1.htm";
	$inner_template1	= "themes/$user_theme/templates/inner_signup_form.htm";
	$TBS			= new clsTinyButStrong ;
	$TBS->NoErr		= true;
	$TBS->LoadTemplate("$template");
	$TBS->Show();
	die();

} else {
	$loggedin = 1; //sets the top menu links in main templates (e.g. main_1.htm)
}

?>
<?php
error_reporting (0);
///////////////////////////////////////////////////////////////////////////////////////
// PHPmotion                                                http://www.phpmotion.com //
///////////////////////////////////////////////////////////////////////////////////////
// License: You are not to sell or distribute this software without permission       //
// Help and support please visit http://www.phpmotion.com                            //
// Copyright reserved                                                                //
///////////////////////////////////////////////////////////////////////////////////////

include_once ('classes/config.php');
include_once ('includes/reserved_names.php');

if ( $enable_forum == 1 ) header("Location: " . $smf_bridge_register);

$send_confirm_email = $config['enabled_features_confirmation_email'] ;

// define access for loading non display php files
define('access',true);

$ahah			= 1;
$show_register	= '';
$load_ajax		= '';
$form_submitted	= '';
$action		= '';
$new_user_name	= '';
$account_type 	= '';
$first_name		= '';
$last_name 		= '';
$email_address 	= '';
$email_address2 	= '';
$user_name 		= '';
$password 		= '';
$confirm_password = '';
$country_list	= '';
$dob_month		= '';
$dob_day		= '';
$dob_year		= '';
$zip_code		= '';
$birthday		= '';
$error_message 	= '';
$checked 		= '';

$show_register	= 1;
$load_ajax		= 1;
$procede 		= true;
$action		= mysql_real_escape_string( $_GET['action'] );
$new_user_name	= mysql_real_escape_string( $_GET['new_user_name'] );
$site_name 		= mysql_real_escape_string($config['site_name']);
$form_submitted	= mysql_real_escape_string( $_POST['form_submitted'] );
$max_len_username	= (int)$config['max_username_length'];

if ( $action == 'check_user' ) {
	$new_user_name	= trim(strtolower($new_user_name));
	if ( $new_user_name == '' ) {
		echo "<font color=\"#EE0000\" size=\"2\"><b>".$config['fill_all_fields']."</b></font>";
		die();
	}
	if ( strlen($new_user_name) < 4 ) {
		echo "<font color=\"#EE0000\" size=\"2\"><b>".$config['username_4_chars']."</b></font>";
		die();
	}
	if ( strlen($new_user_name) > $max_len_username ) {
		echo "<font color=\"#EE0000\" size=\"2\"><b>".$config['username_to_long']."</b></font>";
		die();
	}

	// check against reserved names e.g. -> admin etc... or adult words ban
	if ( in_array( $new_user_name, $reserved_names ) ) {
		echo "<font color=\"#EE0000\" size=\"2\"><b>".$config['username_not_allowed'] ."";
		die();
	}

	$sql		= "SELECT user_name FROM member_profile WHERE user_name = '$new_user_name'";
	$query	= @mysql_query($sql);
	$count	= @mysql_num_rows($query);

	if ( $count > 0 ) {
		echo "<font size=\"2\"><b>$new_user_name</b></font><font color=\"#EE0000\" size=\"2\">&nbsp;<b>=> $lang_not_available</b></font>";
	} else {
		echo "<font size=\"2\"><b>$new_user_name</b></font><font color=\"#00DD00\" size=\"2\">&nbsp;<b>=> $lang_available </b></font>";
	}

	@mysql_free_result($result);
	@mysql_close();
	die();
}

if ( $action == 'show_country' ) {
	$country_fields_all	= '';
	$show_fields		= '';
	$country_list 		= file('includes/country.list');

	foreach ( $country_list as $country_select )
	{
		$country_fields_all = $country_fields_all . $country_select;
	}

	$show_fields  = '<div style="margin-left:30px; float:left;"><b>'.$lang_country.':</b></div>';
	$show_fields .= '<div style="margin-left:95px; float:left;" id="country_list">';
	$show_fields .= '<select class="FormSpecialInput_1" id="country_list" name="country_list" style="font-size: 9pt; width:128px; height:20px; font-weight:bold; color:#444444; letter-spacing: 1; border: 1px solid #DFDCDC; background-color: #FDFDFD">';
	$show_fields .= $country_fields_all;
	$show_fields .= '</select></div>';

	echo $show_fields;
	die();
}

if ( $procede == true ) {

	$account_type 	= 'Standard';
	$first_name		= trim(mysql_real_escape_string( $_POST['first_name'] ));
	$last_name 		= trim(mysql_real_escape_string( $_POST['last_name'] ));
	$email_address 	= trim(mysql_real_escape_string( $_POST['email_address'] ));
	$email_address2 	= trim(mysql_real_escape_string( $_POST['email_address2'] ));
	$user_name 		= trim(mysql_real_escape_string( $_POST['user_name'] ));
	$password 		= trim(mysql_real_escape_string( $_POST['password'] ));
	$confirm_password = trim(mysql_real_escape_string( $_POST['confirm_password'] ));
	$country_list	= trim(mysql_real_escape_string( $_POST['country_list'] ));
	$dob_month		= (int) mysql_real_escape_string( $_POST['dob_month'] );
	$dob_day		= (int) mysql_real_escape_string( $_POST['dob_day'] );
	$dob_year		= (int) mysql_real_escape_string( $_POST['dob_year'] );
	$zip_code		= (int) mysql_real_escape_string( $_POST['zip_code'] );

	if ( $_POST['terms'] == 'yes' ) {
		$checked = 'checked=\"checked\"';
    		$procede = true;

	} else {
		$procede = false;
    		$error_message = $config['agree_to_terms'];
	}

	if ( strlen($user_name) < 4 ) {
		$error_message = $config['username_4_chars'];
		$procede = false;
	}
	if ( strlen($user_name) > $max_len_username ) {
		$error_message = $config['username_to_long'];
		$procede = false;
	}

	// check against reserved names e.g. -> admin etc... or adult words ban
	if ( in_array( $user_name, $reserved_names ) ) {
		$error_message = $config['username_not_allowed'];
		$procede = false;
	}

	// dDB birthday must be this format =>2008-09-17
	$birthday		= $dob_year .'-'. $dob_month .'-'. $dob_day;

	foreach ($_POST as $key => $value) {

      	if (!isset($value) || ($value == '')) {
            	$display_key = @str_replace('_', ' ', $key);
            	if ( $display_key == 'zip code' && $value == '' ) {
            		$value = 'none';
            	} else {
            		$error_message = $error_message . ' - ' . $display_key . '  '.$lang_required.' ';
            		$procede = false;
            	}

        	} else {

        	      if ( $key == 'email_address2' ) $key = 'email_address';

          	      if ( $key !== 'email_address'  && (!eregi("^[ _a-zA-Z0-9-]*$", $value)) ) {
            		$display_key = @str_replace('_', ' ', $key);
                		$error_message = $error_message . ' - ' . $display_key . ' '.$config['invalid_email_text'].' ';
                		$procede = false;
            	}

            	if ( $key == 'email_address' && !eregi("^[_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*(\.[a-z]{2,3})$", $value) ) {
            		$display_key = @str_replace('_', ' ', $key);
                		$error_message = $error_message . ' - ' . $display_key . ' '.$config['invalid_email_format'].' ';
                		$procede = false;
            	}

            	if ( $key == 'user_name' ) {

            		// check against reserved names e.g. -> admin etc... or adult words ban
            		if ( in_array( $user_name, $reserved_names ) ) {
            			$display_key = @str_replace('_', ' ', $key);
            			$error_message = $error_message . ' ' .$config['username_not_allowed'] . ' !';
            			$procede = false;
            		}
            	}

        	}
    	}
}

// checking if emails match
if ( $procede == true ) {
	if ( $email_address !== $email_address2 ) {
      	$error_message = ' '.$config['emails_do_not_match'];
        	$procede = false;
    	}
}

// checking if passwords match
if ( $procede == true ) {
	if ($confirm_password !== $password) {
      	$error_message = ' '.$config['password_do_not_match'];
        	$procede = false;
    	}
}

// checking if username and email is unique
if ( $procede == true ) {

	$sql = "SELECT user_name, email_address FROM member_profile";
    	$query = @mysql_query($sql);

	while ($result = (@mysql_fetch_array($query))) {

      	if ( !empty($result['user_name']) || !empty($result['email_address']) ) {

      		// case insensitive login and registration
        		$reg_user_name	= strtolower($user_name);
        		$db_user_name 	= strtolower($result['user_name']);

            	if ($db_user_name == $reg_user_name) {
                		$error_message = ' '.$config['username_is taken'];
                		$procede = false;
            	}
            	if ( $result['email_address'] == $email_address ) {
                		$error_message = $error_message . ' -'.$config['email_already_exists'];
                		$procede = false;
                	}
           }
    }
}

// if any errors display error message => [var.message_type] - [var.error_message]
if ($procede == false && $form_submitted == 'yes') {
	$message_type	= $lang_error;
    	$blk_notification = 1;
    	$show_signup = 1;

    	$template 		= "themes/$user_theme/templates/main_1.htm";
    	$inner_template1 	= "themes/$user_theme/templates/inner_signup_form.htm";
    	$TBS 			= new clsTinyButStrong;
    	$TBS->NoErr 	= true;

    	$TBS->LoadTemplate("$template");

    	$TBS->Render 	= TBS_OUTPUT;
    	$TBS->Show();

    	@mysql_close();
    	die();
}

// START => load page with form
if ( !isset($form_submitted) || ($form_submitted == '') ) {

	$show_signup 	= 1;
	$template 		= "themes/$user_theme/templates/main_1.htm";
      $inner_template1 	= "themes/$user_theme/templates/inner_signup_form.htm";
      $TBS 			= new clsTinyButStrong;
      $TBS->NoErr 	= true;

      $TBS->LoadTemplate("$template");
      $TBS->Render 	= TBS_OUTPUT;
      $TBS->Show();
      die();
}


// if no errors register user and load welcome page
if ($procede == true) {

	if ( strtolower($_POST['captext']) != strtolower($_SESSION['security_code']) ) {

		// POSSIBLE BOT ATTACK => TODO: ADD SECURITY LOG ACTIONS ETC....
		// LOADING TEMPLATE IS MAYBE USELESS SINCE THIS WOULD COME FROM A SCRIPT POST
		// MAYBE SHOULD JUST DO A DIE HERE!

		$message_type	= $lang_error;
		$blk_notification = 1;
    		$show_signup 	= 0;

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

		$random_code	= randomcode();
    		$password_email	= $password;
    		$password		= md5($password);
    		$passwordSalt 	= substr(md5(rand()), 0, 4);

    		if ( $zip_code > '' ) $country_list = 'USA';

		if ( $send_confirm_email == 'yes' ) {

			// insert new user record
			$sql = "INSERT into member_profile (email_address, user_name, password, passwordSalt, first_name, last_name, zip_code, country, birthday, account_status, account_type, date_created, random_code)
    				  VALUES ('$email_address', '$user_name', '$password', '$passwordSalt', '$first_name', '$last_name', '$zip_code', '$country_list', '$birthday', 'new', 'standard', NOW(), '$random_code')";

    			@mysql_query($sql) or die($config['error_26']);//error

    			// get new user_id
    			$sql 		= "SELECT user_id, email_address, random_code FROM member_profile WHERE random_code = '$random_code' AND email_address = '$email_address'";
    			$query 	= @mysql_query($sql);

    			while ($result = @mysql_fetch_array($query)) $user_id = $result['user_id'];

			// set new user default privacy settings
    			$sql		= "INSERT INTO privacy (videocomments, profilecomments, privatemessage, friendsinvite, newsletter, user_id, publicfavorites, publicplaylists) VALUES ('yes', 'yes', 'yes', 'yes', 'yes', '$user_id', 'yes', 'yes')";
	    		$query 	= @mysql_query($sql);

			@mysql_close();

			// send email
      		$email_template	= 'email_templates/newmember.htm';
      		$subject 		= $config['email_welcome'];
      		$to 			= $email_address;
      		$from 		= $config['notifications_from_email'];

      		//send email template to TBS for rendering of variable inside
      		$template 		= $email_template;
      		$TBS 			= new clsTinyButStrong;
      		$TBS->NoErr 	= true;

      		$TBS->LoadTemplate("$template");
      		$TBS->tbs_show(TBS_NOTHING);
      		$message 		= $TBS->Source;

      		//load postage.php
			define('access',true);
			include ('includes/postage.php');

    			// show success
    			$notification_type	= $config['notification_success'];
    			$message 			= $config['registration_success'];
    			$blk_notification 	= 0;

    			$template 			= "themes/$user_theme/templates/main_1.htm";
    			$inner_template1 		= "themes/$user_theme/templates/inner_notification.htm";
    			$TBS 				= new clsTinyButStrong;
    			$TBS->NoErr 		= true;

    			$TBS->LoadTemplate("$template");
    			$TBS->Render 		= TBS_OUTPUT;
    			$TBS->Show();

    			@mysql_close();
    			die();

		// else send confirmation email is off so we complete the regisration process and show sign in message
		} else {

			// insert new user record
			$sql = "INSERT into member_profile (email_address, user_name, password, passwordSalt, first_name, last_name, zip_code, country, birthday, account_status, account_type, date_created, random_code)
    				  VALUES ('$email_address', '$user_name', '$password', '$passwordSalt', '$first_name', '$last_name', '$zip_code', '$country_list', '$birthday', 'active', 'standard', NOW(), '$random_code')";

    			@mysql_query($sql) or die($config['error_26']);//error

    			// get new user_id
    			$sql 		= "SELECT user_id, email_address, random_code FROM member_profile WHERE random_code = '$random_code' AND email_address = '$email_address'";
    			$query 	= @mysql_query($sql);

    			while ($result = @mysql_fetch_array($query)) $user_id = $result['user_id'];

			// set new user default privacy settings
    			$sql		= "INSERT INTO privacy (videocomments, profilecomments, privatemessage, friendsinvite, newsletter, user_id, publicfavorites, publicplaylists) VALUES ('yes', 'yes', 'yes', 'yes', 'yes', '$user_id', 'yes', 'yes')";
	    		$query 	= @mysql_query($sql);

			@mysql_close();

			$show_signup	= 0;
			$show_login		= 1;
			$show_action_msg	= 1;

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
		}
	}// end captcha check if

}

?>
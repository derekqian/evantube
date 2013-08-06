<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

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

// define access for loading non display php files
define('access', true);
$flag_stop	= 0;
$referer	= mysql_real_escape_string($_SERVER['HTTP_REFERER']);

if ( $referer == '' ) header("Location: $base_url/index.php");
if ( !ereg ($_SERVER['SERVER_NAME'], $referer) ) $flag_stop++;
if ( !ereg ($base_url, $referer) ) $flag_stop++;
if ( $flag_stop == 2 ) header("Location: $base_url/index.php");

$show_form 		= 1;
$proceed 		= true;
$message_stop 	= 300;
$message_stop_t	= $message_stop/60;

// if Form Posted
if ( isset($_POST['send_message']) ) {

	$session_update_token	= $_SESSION['update_token'];
	$post_update_token	= mysql_real_escape_string( $_POST['update_token'] );

	if ( $post_update_token != $session_update_token ) {
		unset($_SESSION['update_token']);
		$_SESSION['update_token']	= '';
		$blk_notification 		= 1;
        	$message_type			= $config['word_error'];
        	$error_message 			= 'Security';
        	$proceed 				= false;
        	$show_form				= 0;
	}

	$subject					= mysql_real_escape_string($_POST['subject']);
    	$message	 				= mysql_real_escape_string($_POST['FCKeditor1']);
    	$member_username				= mysql_real_escape_string($_POST['member_username']);

    	//Check all items filled in
      if ($subject == '' || $message == '' || $member_username == '') {

      	$blk_notification 		= 1;
        	$message_type			= $config['word_error'];
        	$error_message 			= $config['fill_all_fields'];
        	$proceed 				= false;
        	unset($_SESSION['update_token']);
        	$_SESSION['update_token']	= '';
      }

    	//Check if member exists & active
    	if ($proceed == true) {
      	$sql			= "SELECT user_id, email_address FROM member_profile WHERE user_name = '$member_username' AND account_status = 'active'";
        	$query 		= @mysql_query($sql);
        	$result		= @mysql_fetch_array($query);
        	$to_id 		= $result['user_id'];
        	$members_email	= $result['email_address'];

        	if (@mysql_num_rows($query) == 0) {
            	$blk_notification 		= 1;
            	$message_type			= $config['word_error'];
            	$error_message 			= $config['error_2'];			//user could not be found
            	$proceed 				= false;
            	unset($_SESSION['update_token']);
            	$_SESSION['update_token']	= '';
       	}
	}

	//check if they are my friend
	if ( $proceed == true ) {

		$sql = "SELECT * FROM friends WHERE user_id = $user_id AND invitation_status = 'accepted' AND friends_id = $to_id OR friends_id = $user_id AND invitation_status = 'accepted' AND friends_id = $user_id";
		$query = @mysql_query($sql);

		if( @mysql_num_rows($query == 0) ) {
			$blk_notification 		= 1;
			$message_type			= $config['word_error'];
			$error_message 			= $config['member_not_a_friend'];	//not one of your friends
			$proceed 				= false;
			unset($_SESSION['update_token']);
			$_SESSION['update_token']	= '';
		}

		// flood control
		$sql		= "SELECT * from messages WHERE from_username = '$user_name' AND to_id = '$to_id'";
		$query	= mysql_query($sql);

		while ($result = @mysql_fetch_array($query)) {
			$sent_time = strtotime($result['todays_date']);
		}

		if ( time() < ($sent_time + $message_stop) ) {
			$flood_msg 				= 'Please wait ' . $message_stop_t . ' minutes before sending a new email';
			$blk_notification 		= 1;
			$message_type			= $config['word_error'];
			$error_message 			= $flood_msg;
			$proceed 				= false;
			unset($_SESSION['update_token']);
			$_SESSION['update_token']	= '';
			$show_form				= 0;
		}
	}

	//everything checks out
	if ( $proceed == true ) {

		$sql = "INSERT into messages (from_username, subject, message, todays_date, to_id) VALUES ('$user_name', '$subject', '$message', NOW(), $to_id)";
      	@mysql_query($sql);

      	// record a copy in sent items box
      	$sql = "INSERT into messages_sent (to_username, subject, message, todays_date, from_id) VALUES ('$member_username', '$subject', '$message', NOW(), $user_id)";
      	@mysql_query($sql);

		//Check for errors
      	if (@mysql_error()) {
      		$blk_notification 		= 1;
      		$message_type			= $config['word_error'];
            	$error_message 			= $config['error_26'];
            	unset($_SESSION['update_token']);
            	$_SESSION['update_token']	= '';


            } else {

			// check if receipients allow notifications if yes , send them a notification that they have an message

            	if (notification_preferences($to_id, 'privatemessage') == true) {

            		// send pm notification email to recipients registartion email addy
                		$email_template	= 'email_templates/newmessage.htm';
                		$subject 		= $config['email_new_email'];

                		// at this point we do not have any email to send to
                		$to 			= $members_email;
                		$from 		= $config['notifications_from_email'];

                		//send email template to TBS for rendering of variable inside
               		$template = $email_template;
                		$inner_template1 = "themes/$user_theme/templates/inner_email_compose.htm";

                		$TBS = new clsTinyButStrong;
                		$TBS->NoErr = true;

                		$TBS->LoadTemplate("$template");
                		$TBS->tbs_show(TBS_NOTHING);
                		$message = $TBS->Source;

                		//load postage.php
                		include ('includes/postage.php');

                		$blk_notification			= 1;
                		$message_type			= $config['word_success']; // Success
                		$error_message			= $config['error_25']; //25 == Request has been completed XXXXX=>success
                		unset($_SESSION['update_token']);
                		$_SESSION['update_token']	= '';
            	}
		}

		$_SESSION['update_token']	= '';
		$show_form				= 0;
	}

} else {

	// START SHOW MESSAGE COMPOSE FORM
	// Get to_id

      $show_form	= 1;
      $to_uid	= (int) mysql_real_escape_string($_GET['uid']);
      $message_id = (int) mysql_real_escape_string($_GET['id']);

      unset($_SESSION['update_token']);
      $_SESSION['update_token']	= '';
      $update_token			= random_token();
	$_SESSION['update_token'] 	= $update_token;

	//Check if member exists
      if ( $to_uid != "" ) {

      	$sql = "SELECT user_name FROM member_profile WHERE user_id = $to_uid AND account_status = 'active'";
            $result = @mysql_query($sql);

            //Check if members is active
            if (@mysql_num_rows($result) == 0 && $proceed == true) {
            	$blk_notification = 1;
                	$message_type	= $config['word_error'];
                	$error_message 	= $config['error_2'];//user could not be found

            } else {
            	$result = @mysql_fetch_array($result);
                	$member_username = $result['user_name'];
            }
	}

      //Get original message
      if ($message_id != '') {
      	$sql			= "SELECT message, todays_date, subject FROM messages WHERE to_id =$user_id AND indexer = $message_id";
            $query	 	= @mysql_query($sql);
            $result 		= @mysql_fetch_array($query);
            $message_date	= $result['todays_date'];
            $message 		= "\n\n\n-------------- $lang_date - $message_date ---------------\n" . $result['message'];
            $subject 		= 'RE: ' . $result['subject'];
	}

} // end post

// we should void the just posted message if error or flooding is detected
if ( isset($_POST['send_message']) && $show_form != 0 ) {
	$member_username	= '';
	$subject		= '';
	$message 		= '';
}


// FCK EDITOR________________________________________________________________________

include('fckeditor/fckeditor.php');

$sBasePath 						= "$base_url/fckeditor/";
$oFCKeditor 					= new FCKeditor('FCKeditor1');
$oFCKeditor->BasePath 				= $sBasePath;
$oFCKeditor->CustomConfigurationsPath 	= "fckconfig.js";
$oFCKeditor->ToolbarSet 			= 'Basic';
$oFCKeditor->SkinPath 				= $sBasePath + 'editor/skins/silver/';
$oFCKeditor->Value  				= "$message";
$oFCKeditor->Width  				= '560';
$oFCKeditor->Height 				= '220';
$FCKeditor1 					= $oFCKeditor->CreateHtml();
$my_edit 						= $FCKeditor1;

//___________________________________________________________________________________

//Display Results
$template		= "themes/$user_theme/templates/main_1.htm";
$inner_template1 	= "themes/$user_theme/templates/inner_email_compose.htm";
$TBS 			= new clsTinyButStrong;
$TBS->NoErr 	= true;
$TBS->LoadTemplate("$template");
$TBS->Render 	= TBS_OUTPUT;
$TBS->Show();
@mysql_close();
die();

///////////////////////////////////////////////////////////////////////////////////////////

function random_token() {
	$chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
    	srand((double)microtime() * 1000000);
    	$i = 0;
    	$token = '';
    	while ($i <= 31) {
    		$num = rand() % 62;
        	$tmp = substr($chars,$num,1);
        	$token = $token.$tmp;
        	$i++;
    	}
return $token;
}

?>

<?php
///////////////////////////////////////////////////////////////////////////////////////
// PHPmotion                                                http://www.phpmotion.com //
///////////////////////////////////////////////////////////////////////////////////////
// License: You are not to sell or distribute this software without permission       //
// Help and support please visit http://www.phpmotion.com                            //
// Copyright reserved                                                                //
///////////////////////////////////////////////////////////////////////////////////////

include_once('classes/config.php');
include_once('classes/login_check.php');
include_once('classes/sessions.php');


//get msg id or set to msg 0
$msg_id = mysql_real_escape_string($_GET['msg']);
$type = mysql_real_escape_string($_GET['type']);

///////////////////////////////////////////
//show message - incoming message
///////////////////////////////////////////

if($type == 1 && $msg_id != '' ){
$sql = "SELECT * FROM messages WHERE to_id =$user_id AND indexer = $msg_id";
$query = @mysql_query($sql);
$result = @mysql_fetch_array($query);
$message = wordwrap($result['message'], 20, " ", true);
$subject = wordwrap($result['subject'], 20, " ", true);
$email_id= $result['indexer'];
$friends_name = $result['from_username'];

// update message read
$update = "UPDATE messages SET email_read = 'yes' WHERE indexer = '$msg_id'";
$query = @mysql_query($update);

//get senders ID
$sql2 = "SELECT user_id FROM member_profile WHERE user_name = '$friends_name'";
$result2 = @mysql_fetch_array(@mysql_query($sql2));
$friends_id = $result2['user_id'];

$to_or_from = $lang_from;
$show_reply =1;
}

///////////////////////////////////////////
//show message - incoming message
///////////////////////////////////////////

if($type == 2 && $msg_id != ''){
$sql = "SELECT * FROM messages_sent WHERE from_id =$user_id AND indexer = $msg_id";
$query = @mysql_query($sql);
$result = @mysql_fetch_array($query);
$message = wordwrap($result['message'], 20, " ", true);
$subject = wordwrap($result['subject'], 20, " ", true);
$email_id= $result['indexer'];
$friends_name = $result['to_username'];

//get senders ID
$sql2 = "SELECT user_id FROM member_profile WHERE user_name = '$friends_name'";
$result2 = @mysql_fetch_array(@mysql_query($sql2));
$friends_id = $result2['user_id'];

$to_or_from = $lang_to;
}

///////////////////////////////
//show inbox if error on msg id
///////////////////////////////

if ($msg_id =='' || $type ='' || $email_id ==''){
 header("Location: ". "emailinbox.php");

}
$which_box = "emailinbox.php";
$location = "Received";
$subject = wordwrap($result['subject'], 20, " ", true);
$message = wordwrap($result['message'], 20, " ", true);

$template = "themes/$user_theme/templates/main_1.htm";
$inner_template1 = "themes/$user_theme/templates/inner_email_read.htm"; //middle of page
$inner_template2 = "themes/$user_theme/templates/inner_blank.htm"; // bottom of page
$TBS = new clsTinyButStrong ;
$TBS->NoErr = true;
$TBS->LoadTemplate("$template") ;
$TBS->Render = TBS_OUTPUT;
$TBS->Show();

@mysql_close();
die();

?>
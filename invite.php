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

// define access for loading non display php files
define('access',true);

//Get invitee's ID
//////////////////
$member_id = $_GET['uid'];
if (!$member_id || $member_id == $user_id) { //cant invite myself
    ErrorDisplay1($config['error_18']);
    die();
}


//Check if I am logged in
/////////////////////////
if ($_SESSION['user_id'] == "") {
    ErrorDisplay1($config['login_first']);
    die();
}


//Check if I have not already invited
/////////////////////////////////////
$sql = "SELECT * FROM friends WHERE user_id = $user_id AND friends_id = $member_id OR friends_id = $user_id AND user_id = $member_id";
$result = @mysql_query($sql);
if (@mysql_num_rows($result) > 0) {
    ErrorDisplay1($config['error_5']); //you have already invited ....
    die();
}


//Check if this member is active, get email, username
/////////////////////////////////////////////////////
$sql = "SELECT email_address, user_name FROM member_profile WHERE user_id = $member_id AND 	account_status = 'active'";
$query = @mysql_query($sql);
$result = @mysql_fetch_array($query);
$friends_email = $result['email_address'];
$friends_username = $result['user_name'];
if (@mysql_num_rows($query) < 0) {
    ErrorDisplay1($config['error_2']); //user could not be found or is not active ...
    die();
}

//Everything Good sofar - Invite member
////////////////////////////////////////
$invite_id = randomcode();
$sql = "INSERT INTO friends (user_id, invitation_id, friends_id, invitation_type, blocked_users, invitation_status, my_username, friends_username, todays_date) VALUES ($user_id, '$invite_id', $member_id, 'online', 'no', 'pending', '$user_name', '$friends_username', NOW())";
@mysql_query($sql);



//Send Invitee an email
///////////////////////

// check users notification setting and send system notification
if (notification_preferences($member_id, "friendsinvite") == true) {

    //get my real name for use in email
    ///////////////////////////////////

    $sql_1 = "SELECT * FROM member_profile WHERE user_id = $user_id";
    $result_1 = @mysql_fetch_array(@mysql_query($sql_1));
    $my_real_name = $result_1['first_name'];
    //if the member has not yet updated their profile to show real name then use their username and email instead
    if ($my_real_name == "") {

        $my_real_name = $user_name . ' (' . $result_1['email_address'] . ')'; //this will create something like: inmotion (me@gmail.com)
    }

    //send email --------resuable------------------------------------->>
    //>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
    $email_template = 'email_templates/invitemessage_internal.htm';
    $subject = $config['invite_email_subject'];
    $to = $friends_email;
    $from = $config['notifications_from_email'];
    $template = $email_template;
    $TBS = new clsTinyButStrong;
    $TBS->NoErr = true;
    $TBS->LoadTemplate("$template");
    $TBS->tbs_show(TBS_NOTHING);
    $message = $TBS->Source;
    //load postage.php
    include ('includes/postage.php');
    //>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>

}


//Show Success
///////////////
ErrorDisplay1($config['error_4']); //invitation sent out
die();

//END
 ?>
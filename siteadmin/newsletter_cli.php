<?php

include_once ("../classes/config.php");

@set_time_limit(0);

/////////////////////
//defaults settings /
/////////////////////
$todays_date = $config["date_format"];
$base_url = $config['site_base_url'];
$base_path = installation_paths();
$security_match = 'xTg5S3455fd';//matches that sent by newsletter.php  ****USE ONLY TEXT and NUMBERS****

//Get all server args Data
$securityvar = $_SERVER['argv'][1];
$mailgroup = $_SERVER['argv'][2];
$seconds = $_SERVER['argv'][3];
$message_type = $_SERVER['argv'][4];

//Set batch limit
$batch = 3;// the number of emails sent before a pausing (this will help not clog your mail server)set to maximum of about 50

//Send admin Notification email for start of process
/////////////////////////================================resuable=======================================
$sql = "SELECT email_address FROM member_profile WHERE user_group = 'admin'";
$result = @mysql_fetch_array(@mysql_query($sql));
$admin_email = $result['email_address'];
$send_message = $config['news_letter_start_ok'];
$send_from = $config["notifications_from_email"];
$send_from_title = $config["from_system_name"];
@mail("$admin_email", "$lang_system_notification", "\n\n  $send_message \n\n",
    "From: $send_from_title <$send_from>");
/////////////////////////================================resuable=======================================

//Get email list
////////////////
if ($security_match == $securityvar) {//avoids abuse of the emailer

    //Start
    if ($mailgroup == 'moderators') {
        $sql = "SELECT email_address, user_name FROM member_profile WHERE user_group ='standard_mod' OR user_group ='global_mod' OR user_group ='admin'";
    }
    else {
        $sql = "SELECT email_address, user_name FROM member_profile";
    }
    $query = @mysql_query($sql);
    $totals = @mysql_num_rows($query);

    //Get message
    /////////////

    $sql = "SELECT * FROM newsletter";
    $result = @mysql_fetch_array(@mysql_query($sql));

    $message_body = $result['message'];
    $subject = $result['subject'];

    //Send email one by one
    ///////////////////////
    $send_array = array();
    $count = 0;
    $batch_loop = $batch;
    while ($result = @mysql_fetch_array($query)) {
        $members_email = $result['email_address'];
        $members_username = $result['user_name'];


        //SEND EMAIL and increase counter
        $count++;
        if ($message_type == 'html') {
            $headers = "From: $send_from_title <$send_from>\n";
            $headers .= "Reply-To: $send_from\n";
            $headers .= "Return-Path: $send_from\n";
            $headers .= "MIME-Version: 1.0\n";
            $headers .= "Content-Type: text/html; charset=ISO-8859-1\n";
            mail($members_email, $subject, $message_body, $headers);
        }
        else {
            mail($members_email, $subject, $message_body, "From: $send_from_title <$send_from>");
        }

  
        //BATCH PROCESS CHECK - take a nap
        if ($count == $batch_loop) {
            $batch_loop = $batch_loop + $batch;//example 100 becomes 200 etc etc
            @sleep($seconds);
        }
        echo '.';// this seems to keep the script alive
    }

    //Send admin Notification email
    /////////////////////////
    $send_message = $config['news_letter_finish_ok'] . $totals;
    @mail("$admin_email", "$lang_system_notification", "\n\n  $send_message \n\n",
        "From: $send_from_title <$send_from>");

}
else {

    echo 'Nothing to see here';

}


?>
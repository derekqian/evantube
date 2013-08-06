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


$referer = mysql_real_escape_string($_SERVER['HTTP_REFERER']);
$todays_date = $config["date_format"];
$group_id = mysql_real_escape_string($_GET['gid']);

//check if logged in
if ($user_id == '') {
    @mysql_close();
    error_redirect(110);//"You need to be logged in. Please login or register"
    die();
}

//check if group exists
$sql = "SELECT * FROM group_profile WHERE indexer = $group_id";
$query = @mysql_query($sql);
if (@mysql_num_rows($query) == 0) {
    @mysql_close;
    error_redirect(121);//"An error has occurred. The group could not be found"
    die();
}

//check if i am already a member
$sql = "SELECT * FROM group_membership WHERE group_id = $group_id AND member_id = $user_id";
$query = @mysql_query($sql);
if (@mysql_num_rows($query) != 0) {
    @mysql_close();
    error_redirect(123);//"You are already a member OR have already selected to join this group"
    die();
}

//else proceed
//check if group is privatei.e if admin approval is needed
$sql1 = "SELECT * FROM group_profile WHERE indexer = $group_id AND public_private = 'private'";
$query1 = @mysql_query($sql1);
$approved = 'no';
if (@mysql_num_rows($query1) == 0) {
    $approved = 'yes';
}

//add this member into group membership as group admin
$sql = "INSERT into group_membership (member_id, group_admin, group_id, today_date, member_username, approved)
        		VALUES ('$user_id', 'no', $group_id, '$todays_date', '$user_name', '$approved')";
@mysql_query($sql);
@mysql_close();

if (@mysql_num_rows($query1) != 0) {
    @mysql_close();
    error_redirect(122);//"This groups requires admin approval to join. Your application has been sent out"
    die();
}
else {
    @mysql_close();
    error_redirect(120);//"Thank you, you have now been added to this group"
    die();
}
?>


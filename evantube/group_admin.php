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


////////////////////////
//groups top menu setter
if ($_SESSION['user_id'] != ""){
$loggedin = 2; //sets the top menu (groups)
}

$referer = mysql_real_escape_string($_SERVER['HTTP_REFERER']);
$todays_date = mysql_real_escape_string($config["date_format"]);

$result = array();

$group_id = mysql_real_escape_string($_GET['gid']);
$group_url = $config['site_base_url'] . '/group_home.php?gid=' . $group_id;
$admin_delete_messages = "";//only admins can delete messages
$delete_group_video = "";//only admin can delete video
$action = mysql_real_escape_string($_GET['action']);
$action_id = mysql_real_escape_string($_GET['action_id']);


//check if I am logged in
if ($user_id == "") {
    error_redirect(113);//"Please login"
    die();
}

//check if group has been specified in url
if ($group_id == "") {
    error_redirect(126);//"An error has occurred"
    die();
}
if ($action == "") { //no action set
    error_redirect(126);//"An error has occurred"
}

if ($action_id == "") { //no action id set
    error_redirect(126);//"An error has occurred"
}

//check if group exists
$sql = "SELECT * FROM group_profile WHERE indexer = $group_id";
$query = mysql_query($sql);
if (mysql_num_rows($query) == 0) {
    @mysql_close();
    
    error_redirect(126);//"An error has occurred"
    die();
}

//check if users is logged in or just visitor
if ($user_id != "") {
    //check if I am group admin to load admin features
    $sql = "SELECT * FROM group_membership WHERE group_id = $group_id AND member_id = $user_id AND group_admin = 'yes'";
    $query = @mysql_query($sql);
    $am_i_admin = @mysql_num_rows($query);
    if ($am_i_admin == 0) {
    	@mysql_close();
    error_redirect(124);//"You need to be logged in. Please login or register"
    die();
    }
}

//delete video from group video

if ($action == "del_vid") {

//SECURITY CHECK\\ -- check if group_id and video_id taly before deleting
$sql = "SELECT * FROM group_videos WHERE video_id = $action_id AND group_id =$group_id";
$query = @mysql_query($sql);
$count = @mysql_num_rows($query);

if ($count != 0){
$sql1 = "DELETE FROM group_videos WHERE video_id = $action_id AND group_id =$group_id";
mysql_query($sql1);
@mysql_close();
error_redirect(125);//"Your request has been completed"

}else{
    error_redirect(126);//"An error has occurred"    <> trying to hack system by deleting a video that does not belong to user
}
}


if ($action == "private") {

$sql1 = "UPDATE group_profile SET public_private = 'private' WHERE indexer =$group_id";
mysql_query($sql1);
@mysql_close();
echo '<p align="center"><font color="#009933" face="Arial"><b>'.$config["request_completed"].'</b></font>';
}

if ($action == "public") {

$sql1 = "UPDATE group_profile SET public_private = 'public' WHERE indexer =$group_id";
mysql_query($sql1);
@mysql_close();
echo '<p align="center"><font color="#009933" face="Arial"><b>'.$config["request_completed"].'</b></font>';
}





?>


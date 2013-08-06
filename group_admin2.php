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
$group_id = mysql_real_escape_string($_POST['gid']);
$action = mysql_real_escape_string($_POST['action']);
$action_id = mysql_real_escape_string($_POST['action_id']);


//check if I am logged in
if ($user_id == "") {
echo '<p align="center"><font color="#FF4242" face="Arial"><b>'.$config["must_login"] .'</b></font>';
    die();
}

//check if group has been specified in url
if ($group_id == "") {
echo '<p align="center"><font color="#FF4242" face="Arial"><b>'.$config['general_error'].'</b></font>';
    die();
}
if ($action == "") { //no action set
echo '<p align="center"><font color="#FF4242" face="Arial"><b>'.$config['general_error'].'</b></font>';
}

if ($action_id == "") { //no action id set
echo '<p align="center"><font color="#FF4242" face="Arial"><b>'.$config['general_error'].'</b></font>';
}

//check if group exists
$sql = "SELECT * FROM group_profile WHERE indexer = $group_id";
$query = mysql_query($sql);
if (mysql_num_rows($query) == 0) {
    @mysql_close();
echo '<p align="center"><font color="#FF4242" face="Arial"><b>'.$config['general_error'].'</b></font>';
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
echo '<p align="center"><font color="#FF4242" face="Arial"><b>'.$config['no_rights'].'</b></font>';
    die();
    }
}

//delete member from group and also members videos

if ($action == "del_member") {

$sql1 = "DELETE FROM group_videos WHERE member_id = $action_id AND group_id =$group_id";
mysql_query($sql1);
$sql1 = "DELETE FROM group_membership WHERE member_id = $action_id AND group_id =$group_id";
mysql_query($sql1);
@mysql_close();
echo '<p align="center"><font color="#009933" face="Arial"><b>'.$config["request_completed"].'</b></font>';
}






?>


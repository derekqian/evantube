<?php
///////////////////////////////////////////////////////////////////////////////////////
// PHPmotion                                                http://www.phpmotion.com //
///////////////////////////////////////////////////////////////////////////////////////
// License: You are not to sell or distribute this software without permission       //
// Help and support please visit http://www.phpmotion.com                            //
// Copyright reserved                                                                //
///////////////////////////////////////////////////////////////////////////////////////

include_once('classes/config.php');
include_once('classes/sessions.php');

$vid = mysql_real_escape_string($_POST['vid']);
$group_id = mysql_real_escape_string($_POST['group_id']);
$todays_date = $config["date_format"];

//check if being loaded from play.php

if ($vid ==""){
echo '<p align="center"><font color="#FF4242" face="Arial"><b>'.$config['general_error'].'</b></font>';
die();
}

//check if comments have been filled in

if ($group_id ==""){
echo '<p align="center"><font color="#FF4242" face="Arial"><b>'.$config['general_error'].'</b></font>';
die();
}


//check if user is logged in
if ($user_id == ""){
echo '<p align="center"><font color="#FF4242" face="Arial"><b>'.$config["must_login"].'</b></font>';
die();
}

//check if video is not already in groupd
$sql = "SELECT * FROM group_videos WHERE video_id = $vid AND group_id = $group_id";
$query = mysql_query($sql);
if(mysql_num_rows($query) != 0){
echo '<p align="center"><font color="#FF4242" face="Arial"><b>'.$config['video_has_been_added_already'].'</b></font>';
die();
}

//SECURITY\\ ensure that i am the onwer
$sql = "SELECT * FROM videos WHERE user_id = $user_id AND indexer = $vid AND approved = 'yes'";
$query = mysql_query($sql);
if(mysql_num_rows($query) == 0){
echo '<p align="center"><font color="#FF4242" face="Arial"><b>'.$config['general_error'].'</b></font>';
die();
}

//add video to group
$sql = "INSERT INTO group_videos (video_id, group_id, member_id, todays_date) VALUES ($vid, $group_id, $user_id, '$todays_date')";
@mysql_query($sql);
//check if its a private video and issue warning
$sql = "SELECT * FROM videos WHERE indexer = $vid AND public_private = 'private'";
$query = @mysql_query($sql);
if (@mysql_num_rows($query) != 0){
$private_warning = '<p align="center"><font color="#FF0000" face="Arial" size="2">'.$config['video_private_warning'].'</font></p>';
	}
echo '<p align="center"><font color="#009933" face="Arial"><b>'.$config['video_has_been_added'] .'</b></font>';
echo $private_warning;
die();

?>
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

// get video delete information from gorup main home page i.e. using POST
$vid = mysql_real_escape_string($_POST['vid']);
$group_id = mysql_real_escape_string($_POST['gid']);

// get video delete information from seemore.php i.e using GET
if ($vid =="" | $group_id ==""){
$vid = mysql_real_escape_string($_GET['vid']);
$group_id = mysql_real_escape_string($_GET['gid']);
}

$todays_date = $config["date_format"];

//check if group id is filled in

if ($group_id ==""){
echo '<p align="center"><font color="#FF4242" face="Arial"><b>'.$config['general_error'].'</b></font>';
die();
}


//check if user is logged in
if ($user_id == ""){
echo '<p align="center"><font color="#FF4242" face="Arial"><b>'.$config["must_login"].'</b></font>';
die();
}

//SECURITY\\ ensure that i am the onwer
$sql = "SELECT * FROM videos WHERE user_id = $user_id AND indexer = $vid AND approved = 'yes'";
$query = mysql_query($sql);
if(mysql_num_rows($query) == 0){
echo '<p align="center"><font color="#FF4242" face="Arial"><b>'.$config['general_error'].'</b></font>';
die();
}

//delete video to group
$sql = "DELETE FROM group_videos WHERE video_id = $vid AND group_id = $group_id";
@mysql_query($sql);
echo '<p align="center"><font color="#009933" face="Arial"><b>'.$config['video_has_been_removed'] .'</b></font>';
die();

?>
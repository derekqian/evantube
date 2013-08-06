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

$group_id = $_POST['gid'];
$comments = $_POST['comments'];
$todays_date = $config["date_format"];

//check if being loaded from play.php
if ($group_id ==""){
die();
}

//check if comments have been filled in
if ($comments ==""){
echo '<p align="center"><font color="#FF4242" face="Arial"><b>'.$config['video_comments_error_comment'].'</b></font>';
die();
}


//check if user is logged in
if ($user_id == ""){
echo '<p align="center"><font color="#FF4242" face="Arial"><b>'.$config['video_comments_login'].'</b></font>';
die();
}

//check if users is in group
$group_id = mysql_real_escape_string($group_id);
$user_id = mysql_real_escape_string($user_id);
$sql = "SELECT * FROM group_membership WHERE group_id = $group_id AND member_id = $user_id AND approved = 'yes'";
$query = @mysql_query($sql);
if (@mysql_num_rows($query) == 0) {
echo '<p align="center"><font color="#FF4242" face="Arial"><b>'.$config['groups_members_only_post'].'</b></font>';
die();
}

$user_name = mysql_real_escape_string($user_name);
$comments = mysql_real_escape_string($comments);
$todays_date = mysql_real_escape_string($todays_date);
$topic_id = 1; //to change in later versions should topics start to be used.


$sql = "INSERT into group_comments (by_id, by_username, group_id, comments, todays_date, topic_id) VALUES
                                  ($user_id, '$user_name', $group_id, '$comments', '$todays_date', $topic_id)";

@mysql_query($sql);
echo
'<p align="center"><font color="#009933" face="Arial"><b><font size="2">'.$config['video_comments_success'].'</font></b></font><font size="2">
</font></p>
<p align="center"><font size="2" face="Arial"><a href="JavaScript:location.reload(true);">
<font color="#3366CC">'.$config["view_all"].'</font></a></font></p>';

die();
?>
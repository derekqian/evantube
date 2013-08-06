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


$group_id = mysql_real_escape_string($_GET['gid']);

//get list of all group videos

//Pagination
$limit = 3;
$pagination = pagination("SELECT * FROM group_videos WHERE group_id = $group_id",
    $limit);
$set_limit = $pagination[0]['set_limit'];
$total_pages = $pagination[0]['total_pages'];
$current_page = $pagination[0]['current_page'];
$total_records = $pagination[0]['total_records'];
$next_page = $pagination[0]['next_page'];//use in html navigation (src)
$prev_page = $pagination[0]['prev_page'];//use in html navigation (src)
$nl = $pagination[0]['nl'];//use in html navigation: next>>
$pl = $pagination[0]['pl'];//use in html navigation: <<previous

//get all videos
$all_videos = array();
$sql10 = "SELECT * FROM group_videos WHERE group_id = $group_id LIMIT $set_limit, $limit";
$query10 = mysql_query($sql10);
while ($result10 = @mysql_fetch_array($query10)) {
    //collect each videos details
    $each_video = mysql_real_escape_string($result10['video_id']);
    $sql11 = "SELECT * FROM videos WHERE indexer = $each_video AND approved = 'yes'";
    $query11 = @mysql_query($sql11);
    $result11 = @mysql_fetch_array($query11);
    if (!empty($result11)) {
        $all_videos[] = $result11;
    }
}

//set condition for hidding certain blocks (more videos)
if (empty($all_videos)) {
    $show_v = 1;
}
else {
    $show_v = 2;
}

//check if I am group admin to load admin features
$delete_group_video = "";//show option to delete on each listed video
if ($user_id != "") {
    $sql = "SELECT * FROM group_membership WHERE group_id = $group_id AND member_id = $user_id AND group_admin = 'yes'";
    $query = @mysql_query($sql);
    $am_i_admin = @mysql_num_rows($query);
    if ($am_i_admin == 0) {
        $delete_group_video = "";//show option to delete on each listed video
    }
    else {
        $delete_group_video = $config["delete_general"];//show option to delete on each listed video
    }
}
;
$template = "themes/$user_theme/templates/inner_group_videos_ajax.htm";
$TBS = new clsTinyButStrong;
$TBS->NoErr = true;// no more error message displayed.
$TBS->LoadTemplate("$template");
$TBS->MergeBlock('mp', $all_videos);
$TBS->Render = TBS_OUTPUT;
$TBS->tbs_Show();
@mysql_close();
die();

?>
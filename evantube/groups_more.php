<?php

///////////////////////////////////////////////////////////////////////////////////////
// PHPmotion                                                http://www.phpmotion.com //
///////////////////////////////////////////////////////////////////////////////////////
// License: You are not to sell or distribute this software without permission       //
// Help and support please visit http://www.phpmotion.com                            //
// Copyright reserved                                                                //
///////////////////////////////////////////////////////////////////////////////////////


include_once ("classes/config.php");
include_once ('classes/login_check.php');
include_once ('classes/sessions.php');


////////////////////////
//groups top menu setter
if ($_SESSION['user_id'] != ""){
$loggedin = 2; //sets the top menu (groups)
}


$referer = mysql_real_escape_string($_SERVER['HTTP_REFERER']);
$todays_date = $config["date_format"];
$result = array();
$type = mysql_real_escape_string($_GET['type']);

if ($type != 1 ) {//general page load, also where action sent in url is not identified (i.e not 'admin' or similar_text())
    $which_type = $config["all"];//display 'ALL' in html title

    //--------------------------------------------------------------------reusable groups code -------------------------------
    //show every group that I am a member of with all its

    //Pagination
    $limit = $config['groups_main_limit'];
    $pagination = pagination("SELECT * FROM group_membership WHERE member_id = $user_id",$limit);
    $set_limit = $pagination[0]['set_limit'];
    $total_pages = $pagination[0]['total_pages'];
    $current_page = $pagination[0]['current_page'];
    $total_records = $pagination[0]['total_records'];
    $next_page = $pagination[0]['next_page'];//use in html navigation (src)
    $prev_page = $pagination[0]['prev_page'];//use in html navigation (src)
    $nl = $pagination[0]['nl'];//use in html navigation: next>>
    $pl = $pagination[0]['pl'];//use in html navigation: <<previous

    //get list of videos
    $mygroups = array();
    $sql = "SELECT * FROM group_membership WHERE member_id = $user_id";
    $query = @mysql_query($sql);

    while ($result = @mysql_fetch_array($query)) {

        //get each groups details
        $group_id = mysql_real_escape_string($result['group_id']);
        $sql1 = "SELECT * FROM group_profile WHERE indexer = $group_id";
        $query1 = @mysql_query($sql1);
        $result1 = @mysql_fetch_array($query1);

        //count the number of videos the group has
        $sql2 = "SELECT * FROM group_videos WHERE group_id = $group_id AND video_status='active'";
        $query2 = @mysql_query($sql2);
        $count_group_videos = @mysql_num_rows($query2);

        //get video picture and details  --------------------------reusable get video picture----------------
        if ($count_group_videos == 0) {
            $video_picture = 'default_no_group_video';//show place holder image2wbmp
            $group_video_id = 0;
        }
        else {
            $result2 = @mysql_fetch_array($query2);
            $group_video_id = mysql_real_escape_string($result2['video_id']);
            $sql0 = "SELECT * FROM videos WHERE indexer = $group_video_id";
            $query0 = @mysql_query($sql0);
            $result0 = @mysql_fetch_array($query0);
            $group_video_image = $result0['video_id'];

            $video_picture = $result0['video_id'];
            $group_video_id = $result0['indexer'];
        }
        $video_array = array('video_picture' => $video_picture, 'group_video_id' => $group_video_id);
        //----------------------------------------------------------------------------------------------------

        //count the number of member the group has
        $sql3 = "SELECT * FROM group_membership WHERE group_id = $group_id";
        $query3 = @mysql_query($sql3);
        $count_group_members = @mysql_num_rows($query3);

        //get groupd admin details
        $sql4 = "SELECT * FROM group_membership WHERE group_id = $group_id AND group_admin = 'yes'";
        $query4 = @mysql_query($sql4);
        $result4 = @mysql_fetch_array($query4);
        $admin_username = $result4['member_username'];
        $admin_id = $result4['member_id'];

        //form array of bits and pieces
        $group_other_info = array('group_videos' => $count_group_videos, 'group_members' =>
            $count_group_members, 'admin_username' => $admin_username, 'admin_id' => $admin_id);

        //merge arrays

        $group_array = @array_merge($result1, $group_other_info, $video_array);
        $mygroups[] = $group_array;
        //--------------------------------------------------------------------------------------------------------------------------
    }

    //set condition for hidding certain blocks (e.g "no emails to list")
    if (empty($mygroups)) {
        $show_v = 1;
    }
    else {
        $show_v = 2;
    }

    //display my groups
    $template = "themes/$user_theme/templates/main_1.htm";
    $inner_template1 = "themes/$user_theme/templates/inner_groups_main.htm";//middle of page
    $TBS = new clsTinyButStrong;
    $TBS->NoErr = true;// no more error message displayed.
    $TBS->LoadTemplate("$template");
    $TBS->MergeBlock('blkfeatured', $mygroups);
    $TBS->Render = TBS_OUTPUT;
    $TBS->Show();

    @mysql_close();
    die();
}

//show groups that I am admin for
/////////////////////////////////

if ($type == 1) {

    $which_type = $config["managed_by_me"];//display 'Groups managed by me' in html title
    //--------------------------------------------------------------------reusable groups code -------------------------------
    //show every group that I am a member of with all its

    //Pagination
    $limit = $config['groups_main_limit'];
    $pagination = pagination("SELECT * FROM group_membership WHERE member_id = $user_id AND group_admin='yes'", $limit);
    $set_limit = $pagination[0]['set_limit'];
    $total_pages = $pagination[0]['total_pages'];
    $current_page = $pagination[0]['current_page'];
    $total_records = $pagination[0]['total_records'];
    $next_page = $pagination[0]['next_page'];//use in html navigation (src)
    $prev_page = $pagination[0]['prev_page'];//use in html navigation (src)
    $nl = $pagination[0]['nl'];//use in html navigation: next>>
    $pl = $pagination[0]['pl'];//use in html navigation: <<previous

    //get list of videos
    $mygroups = array();
    $sql = "SELECT * FROM group_membership WHERE member_id = $user_id AND group_admin='yes'";
    $query = @mysql_query($sql);
    $count_my_groups = @mysql_num_rows($query);

    while ($result = mysql_fetch_array($query)) {

        //get each groups details
        $group_id = mysql_real_escape_string($result['group_id']);

        $sql1 = "SELECT * FROM group_profile WHERE indexer = $group_id";
        $query1 = @mysql_query($sql1);
        $result1 = @mysql_fetch_array($query1);

        //$indexer = mysql_real_escape_string($result1['indexer']);


        //count the number of videos the group has
        $sql2 = "SELECT * FROM group_videos WHERE group_id = $group_id AND video_status='active'";
        $query2 = @mysql_query($sql2);
        $count_group_videos = @mysql_num_rows($query2);

        //get video picture and details  --------------------------reusable get video picture----------------
        if ($count_group_videos == 0) {
            $video_picture = 'default_no_group_video';//show place holder image2wbmp
            $group_video_id = 0;
        }
        else {
            $result2 = @mysql_fetch_array($query2);
            $group_video_id = mysql_real_escape_string($result2['video_id']);
            $sql0 = "SELECT * FROM videos WHERE indexer = $group_video_id";
            $query0 = @mysql_query($sql0);
            $result0 = @mysql_fetch_array($query0);
            $group_video_image = $result0['video_id'];

            $video_picture = $result0['video_id'];
            $group_video_id = $result0['indexer'];
        }
        $video_array = array('video_picture' => $video_picture, 'group_video_id' => $group_video_id);
        //----------------------------------------------------------------------------------------------------

        //count the number of member the group has
        $sql3 = "SELECT * FROM group_membership WHERE group_id = $group_id";
        $query3 = @mysql_query($sql3);
        $count_group_members = mysql_num_rows($query3);

        //get groupd admin details
        $sql4 = "SELECT * FROM group_membership WHERE group_id = $group_id AND group_admin = 'yes'";
        $query4 = @mysql_query($sql4);
        $result4 = @mysql_fetch_array($query4);
        $admin_username = $result4['member_username'];
        $admin_id = $result4['member_id'];

        //form array of bits and pieces
        $group_other_info = array('group_videos' => $count_group_videos, 'group_members' =>
            $count_group_members, 'admin_username' => $admin_username, 'admin_id' => $admin_id);

        //merge arrays

        $group_array = @array_merge($result, $result1, $group_other_info, $video_array);
        $mygroups[] = $group_array;
        //--------------------------------------------------------------------------------------------------------------------------
    }

    //set condition for hidding certain blocks (e.g "no emails to list")
    if (empty($mygroups)) {
        $show_v = 1;
    }
    else {
        $show_v = 2;
    }

    	if ( $count_my_groups < 4 ) {
    		$page_spacer = '140px;';
    	} else {
    		$page_spacer = '0;';
    	}


    //display my groups
    $template = "themes/$user_theme/templates/main_1.htm";
    $inner_template1 = "themes/$user_theme/templates/inner_groups_main.htm";//middle of page
    $TBS = new clsTinyButStrong;
    $TBS->NoErr = true;// no more error message displayed.
    $TBS->LoadTemplate("$template");
    $TBS->MergeBlock('blkfeatured', $mygroups);
    $TBS->Render = TBS_OUTPUT;
    $TBS->Show();

    @mysql_close();
    die();
}

?>


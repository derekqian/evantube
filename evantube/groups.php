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
include_once ('includes/enabled_features.php');

//($show_private_content)? $sql_public_private = '':$sql_public_private = "AND public_private = 'public'";

$page_title = $lang_browse_groups . ' ' . $lang_on .  ' ' . $site_name;

if ( $_SESSION['user_id'] != "" ) $loggedin = 2;

$users_member_id = $_SESSION['user_id'];

$referer = mysql_real_escape_string($_SERVER['HTTP_REFERER']);
$result = array();

if ( isset($_GET['load']) ) $load	= mysql_real_escape_string($_GET['load']); else $load	= '';
if ( isset($_GET['code']) ) $codes 	= (int) mysql_real_escape_string($_GET['code']); else $codes = '';


/////////////////////////////////////////////////////////////////////////////////////////////////////////
// load users friend nto array for checking public or private types

//$friends_id	= array();

$sql			= "SELECT * FROM friends WHERE user_id = '$users_member_id' AND invitation_status = 'accepted' OR friends_id = '$users_member_id' AND invitation_status = 'accepted'";
$query 		= @mysql_query($sql);
while ($rows	= @mysql_fetch_array($query)) {
	$my_friends_id = $rows['user_id'];
	if ($my_friends_id == $users_member_id ) $my_friends_id = $rows['friends_id'];
	//echo "my id " . $users_member_id . " my friends id's " . $my_friends_id . "<br />";
}

//show main groups page, showing by default biggest groups
if ($load == '') {

    	$mygroups = array();

    	//$which_type = $config["all"];

    	//Pagination
    	$limit		= $config['groups_main_limit'];

    	//$pagination 	= pagination("SELECT * FROM group_profile WHERE $sql_pagination", $limit);

    	$pagination 	= pagination("SELECT * FROM group_profile", $limit);

    	$set_limit 		= $pagination[0]['set_limit'];
    	$total_pages 	= $pagination[0]['total_pages'];
    	$current_page 	= $pagination[0]['current_page'];
    	$total_records 	= $pagination[0]['total_records'];
    	$next_page 		= $pagination[0]['next_page'];
    	$prev_page 		= $pagination[0]['prev_page'];
    	$nl 			= $pagination[0]['nl'];
    	$pl 			= $pagination[0]['pl'];

    	//$sql = "SELECT * FROM group_profile WHERE $sql_public_private LIMIT $set_limit, $limit";

    	//select all public and private ??
    	$sql = "SELECT * FROM group_profile LIMIT $set_limit, $limit";


    	$query = @mysql_query($sql);

    	define('groups', true);

    	while ($result = @mysql_fetch_array($query)) {

      	$group_id			= $result['indexer'];
        	$sql2 			= "SELECT * FROM group_videos WHERE group_id = $group_id AND video_status='active'";
        	$query2 			= @mysql_query($sql2);
        	$count_group_videos	= @mysql_num_rows($query2);

        	$db_date			= $result['todays_date'];
        	$change_date		= dateTimeDiff($db_date);
        	$result['todays_date']	= $change_date;

        	// get rating stars
        	$id = $group_id;
        	include ('stars_include.php');
        	$stars_array = array('star1' => $star1,'star2' => $star2, 'star3' => $star3, 'star4' => $star4, 'star5' => $star5);

        	if ($count_group_videos == 0) {
            	$video_picture = 'default_no_group_video';//show place holder image2wbmp
            	$group_video_id = 0;

        	} else {
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
        	$group_other_info = array('group_videos' 	=> $count_group_videos,
        					  'group_members'	=> $count_group_members,
        					  'admin_username'=> $admin_username,
        					  'admin_id' 	=> $admin_id);
        	//merge arrays
        	$group_array = @array_merge($result, $stars_array, $group_other_info, $video_array);
        	$mygroups[] = $group_array;
    	}

    	//set condition for hidding certain blocks (e.g "no emails to list")
    	if (empty($mygroups)) {
      	$show_v = 1;
    	} else {
        	$show_v = 2;
    	}

    	//check for error codes
    	$error_code = errorcodes($codes);
    	if (!empty($error_code)) {
      	$blk_notification = $error_code['error_display'];
        	$message_type = $error_code['error_type'];
        	$error_message =$error_code['error_message'];
   	}

	////////////////////////////////////////////
	//PAGINATION PLUS >> start  -- reusable code
    	////////////////////////////////////////////
	$url = 'groups.php';
	$additional_url_variable = '?page=';

	include_once('includes/pagination.inc.php');
 	//PAGINATION PLUS >> end


    	//display my groups
    	$template		= "themes/$user_theme/templates/main_1.htm";
    	$inner_template1	= "themes/$user_theme/templates/inner_groups_main.htm";
    	$TBS 			= new clsTinyButStrong;
    	$TBS->NoErr		= true;

    	$TBS->LoadTemplate("$template");
    	$TBS->MergeBlock('blkfeatured', $mygroups);
    	$TBS->Render	= TBS_OUTPUT;
    	$TBS->Show();

    	@mysql_close();
    	die();
}

// show most RECENT
if ($load == 'new') {

	$mygroups		= array();
    	$which_type 	= $config["featured"];

    	// pagination
    	$limit 		= $config['groups_main_limit'];
    	$pagination 	= pagination("SELECT * FROM group_profile WHERE $sql_pagination ORDER BY indexer DESC", $limit);

    	$set_limit 		= $pagination[0]['set_limit'];
    	$total_pages 	= $pagination[0]['total_pages'];
    	$current_page 	= $pagination[0]['current_page'];
    	$total_records 	= $pagination[0]['total_records'];
    	$next_page 		= $pagination[0]['next_page'];
    	$prev_page 		= $pagination[0]['prev_page'];
    	$nl 			= $pagination[0]['nl'];
    	$pl 			= $pagination[0]['pl'];


    	//get list of videos
    	$sql		= "SELECT * FROM group_profile WHERE $sql_public_private ORDER BY indexer DESC LIMIT $set_limit, $limit";
    	$query	= mysql_query($sql);

    	define('groups', true);

    	while ($result = mysql_fetch_array($query)) {

      	$group_id			= $result['indexer'];
      	$sql2 			= "SELECT * FROM group_videos WHERE group_id = $group_id AND video_status = 'active'";
        	$query2 			= mysql_query($sql2);
        	$count_group_videos	= mysql_num_rows($query2);

        	$db_date			= $result['todays_date'];
        	$change_date		= dateTimeDiff($db_date);
        	$result['todays_date']	= $change_date;

        	// get rating stars
        	$id = $group_id;
        	include ('stars_include.php');
        	$stars_array = array('star1' => $star1, 'star2' => $star2, 'star3' => $star3, 'star4' => $star4, 'star5' => $star5);


        	//get video picture and details
        	if ($count_group_videos == 0) {
            	$video_picture 	= 'default_no_group_video';//show place holder image2wbmp
            	$group_video_id	= 0;

        	} else {
            	$result2 			= mysql_fetch_array($query2);
            	$group_video_id 		= $result2['video_id'];
            	$sql0 			= "SELECT * FROM videos WHERE indexer = $group_video_id";
            	$query0 			= mysql_query($sql0);
            	$result0 			= @mysql_fetch_array($query0);
            	$group_video_image	= $result0['video_id'];
            	$video_picture 		= $result0['video_id'];
            	$group_video_id		= $result0['indexer'];
        	}

        	$video_array = array('video_picture' => $video_picture, 'group_video_id' => $group_video_id);

        	//count the number of member the group has
        	$sql3 			= "SELECT * FROM group_membership WHERE group_id = $group_id";
        	$query3 			= mysql_query($sql3);
        	$count_group_members	= mysql_num_rows($query3);

        	//get groupd admin details
        	$sql4 		= "SELECT * FROM group_membership WHERE group_id = $group_id AND group_admin = 'yes'";
        	$query4 		= mysql_query($sql4);
        	$result4 		= mysql_fetch_array($query4);
        	$admin_username 	= $result4['member_username'];
        	$admin_id 		= $result4['member_id'];

        	//form array of bits and pieces
        	$group_other_info = array('group_videos' 	=> $count_group_videos,
        					  'group_members' => $count_group_members,
        					  'admin_username'=> $admin_username,
        					  'admin_id' 	=> $admin_id);

        	//merge arrays
        	$group_array	= array_merge($result, $stars_array, $group_other_info, $video_array);
        	$mygroups[] 	= $group_array;
   	}

    	//set condition for hidding certain blocks (e.g "no emails to list")
    	if (empty($mygroups)) {
      	$show_v = 1;
    	} else {
      	$show_v = 2;
    	}

    	//check for error codes
    	$codes	= $_GET['code'];//error codes from anywhere
    	$error_code	= errorcodes($codes);

    	if (!empty($error_code)) {
      	$blk_notification = $error_code['error_display'];
        	$message_type = $error_code['error_type'];
        	$error_message =$error_code['error_message'];
    	}

    	////////////////////////////////////////////
	//PAGINATION PLUS >> start  -- reusable code
    	////////////////////////////////////////////

	$url = 'groups.php';
	$additional_url_variable = '?load=new&page=';

     	include_once('includes/pagination.inc.php');

 	//PAGINATION PLUS >> end

    	//display my groups
    	$template		= "themes/$user_theme/templates/main_1.htm";
    	$inner_template1 	= "themes/$user_theme/templates/inner_groups_main.htm";
    	$inner_template2 	= "themes/$user_theme/templates/inner_blank.htm";// bottom of page
    	$TBS 			= new clsTinyButStrong;
    	$TBS->NoErr 	= true;
    	$TBS->LoadTemplate("$template");
    	$TBS->MergeBlock('blkfeatured', $mygroups);
    	$TBS->Render 	= TBS_OUTPUT;
    	$TBS->Show();

    	@mysql_close();
    	die();
}

// featured groups
if ($load == 'featured') {

	$mygroups	= array();
    	$which_type = $config["featured"];

    	//Pagination
    	$limit 		= $config['groups_main_limit'];
    	$pagination 	= pagination("SELECT * FROM group_profile WHERE featured = 'yes' $sql_public_private", $limit);
    	$set_limit 		= $pagination[0]['set_limit'];
    	$total_pages 	= $pagination[0]['total_pages'];
    	$current_page 	= $pagination[0]['current_page'];
    	$total_records 	= $pagination[0]['total_records'];
    	$next_page 		= $pagination[0]['next_page'];
    	$prev_page 		= $pagination[0]['prev_page'];
    	$nl 			= $pagination[0]['nl'];
    	$pl 			= $pagination[0]['pl'];

    	//get list of videos
    	$sql		= "SELECT * FROM group_profile WHERE featured = 'yes' $sql_public_private LIMIT $set_limit, $limit";
    	$query 	= mysql_query($sql);

    	define('groups', true);

    	while ($result = mysql_fetch_array($query)) {

      	$group_id			= $result['indexer'];//count the number of videos the group has
        	$sql2 			= "SELECT * FROM group_videos WHERE group_id = $group_id AND video_status='active'";
        	$query2 			= mysql_query($sql2);
        	$count_group_videos	= mysql_num_rows($query2);

        	$db_date			= $result['todays_date'];
        	$change_date		= dateTimeDiff($db_date);
        	$result['todays_date']	= $change_date;

        	// get rating stars
        	$id = $group_id;
        	include ('stars_include.php');
        	$stars_array = array('star1' => $star1, 'star2' => $star2, 'star3' => $star3, 'star4' => $star4, 'star5' => $star5);


        	//get video picture and details
        	if ($count_group_videos == 0) {
            	$video_picture 	= 'default_no_group_video';//show place holder image2wbmp
            	$group_video_id 	= 0;

        	} else {
            	$result2 			= mysql_fetch_array($query2);
            	$group_video_id 		= $result2['video_id'];
            	$sql0 			= "SELECT * FROM videos WHERE indexer = $group_video_id";
            	$query0 			= mysql_query($sql0);
            	$result0 			= @mysql_fetch_array($query0);
            	$group_video_image	= $result0['video_id'];
            	$video_picture 		= $result0['video_id'];
            	$group_video_id 		= $result0['indexer'];
        	}

        	$video_array = array('video_picture' => $video_picture, 'group_video_id' => $group_video_id);

        	//count the number of member the group has
        	$sql3 			= "SELECT * FROM group_membership WHERE group_id = $group_id";
        	$query3 			= mysql_query($sql3);
        	$count_group_members	= mysql_num_rows($query3);

        	//get groupd admin details
        	$sql4 		= "SELECT * FROM group_membership WHERE group_id = $group_id AND group_admin = 'yes'";
        	$query4 		= mysql_query($sql4);
        	$result4 		= mysql_fetch_array($query4);
        	$admin_username	= $result4['member_username'];
        	$admin_id 		= $result4['member_id'];

        	//form array of bits and pieces
        	$group_other_info = array('group_videos' 		=> $count_group_videos,
        					  'group_members'		=> $count_group_members,
        					  'admin_username' 	=> $admin_username,
        					  'admin_id' 		=> $admin_id);

        	//merge arrays
        	$group_array	= array_merge($result, $stars_array, $group_other_info, $video_array);
        	$mygroups[] 	= $group_array;
    	}

    	//set condition for hidding certain blocks
    	if (empty($mygroups)) {
      	$show_v = 3;
    	} else {
        	$show_v = 2;
    	}

    	//check for error codes
    	$codes = $_GET['code'];
    	$error_code = errorcodes($codes);

    	if (!empty($error_code)) {
      	$blk_notification = $error_code['error_display'];
        	$message_type = $error_code['error_type'];
        	$error_message =$error_code['error_message'];
    	}

    	if ( $total_records < 4 ) {
    		$page_spacer = '140px;';
    	} else {
    		$page_spacer = '0;';
    	}

    	////////////////////////////////////////////
	//PAGINATION PLUS >> start  -- reusable code
    	////////////////////////////////////////////
	$url = 'groups.php'; //the url to be put in links - EDIT ME
	$additional_url_variable = '?load=featured&page=';


     	include_once('includes/pagination.inc.php');

 	//PAGINATION PLUS >> end

    	//display my groups
    	$template = "themes/$user_theme/templates/main_1.htm";
    	$inner_template1 = "themes/$user_theme/templates/inner_groups_main.htm";
    	$inner_template2 = "themes/$user_theme/templates/inner_blank.htm";// bottom of page
    	$TBS = new clsTinyButStrong;
    	$TBS->NoErr = true;
    	$TBS->LoadTemplate("$template");
    	$TBS->MergeBlock('blkfeatured', $mygroups);
    	$TBS->Render = TBS_OUTPUT;
    	$TBS->Show();

    	@mysql_close();
   	 die();
}


?>


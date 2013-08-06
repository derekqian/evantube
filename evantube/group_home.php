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

if ( $_SESSION['user_id'] != '' ) $loggedin = 2;

$ahah				= 1;
$no_video_play		= 0;
$wrap_limit			= 80;
$referer 			= mysql_real_escape_string($_SERVER['HTTP_REFERER']);
$result			= array();
$group_id 			= mysql_real_escape_string($_GET['gid']);
$group_url 			= $config['site_base_url'].'/group_home.php?gid='.$group_id;
$admin_delete_messages	= '';
$delete_group_video 	= '';

//___Enabled Features Check_______________________________
include_once ('includes/enabled_features.php');

if ( $group_id == '' ) {
	@header("Location: "."groups.php");//relocate to main groups page
    	die();
}

//check if group exists
$sql = "SELECT * FROM group_profile WHERE indexer = $group_id";
$query = mysql_query($sql);
if (mysql_num_rows($query) == 0) {
    @mysql_close();
    @header("Location: "."groups.php");//relocate to main groups page
    die();
}

//check if users is logged in or just visitor
$admin_mode = 0;//for html table display options
$delete_group_video = "";//show option to delete on each listed video

if ($user_id != "") {
    //check if I am group admin to load admin features
    $sql = "SELECT * FROM group_membership WHERE group_id = $group_id AND member_id = $user_id AND group_admin = 'yes'";
    $query = @mysql_query($sql);
    $am_i_admin = @mysql_num_rows($query);
    if ($am_i_admin == 0) {
        $admin_mode = 0;//for html table display options
        $delete_group_video = "";//show option to delete on each listed video
    }
    else {
        $admin_mode = 1;//for html table display options
        $delete_group_video = $config["delete_general"];//show option to delete on each listed video
        $admin_delete_messages = $config["delete_general"];
    }
}

//check if group is public or private and see if my user_is is in group membeship
if ($user_id != "") {
    $sql = "SELECT * FROM group_profile WHERE indexer = $group_id AND public_private = 'private'";
    $query = @mysql_query($sql);
    $count = @mysql_num_rows($query);


    if ($count != 0) {
        $sql = "SELECT * FROM group_membership WHERE member_id = $user_id AND group_id = $group_id AND approved = 'yes'";
        $count = @mysql_num_rows($query);

        if ($count == 0) {
            //error_redirect(127);//"This group is marked as private, only members can view it"
        }
    }
}

//check if group is public or private (for visitors browsing)
if ($user_id == "") {
    $sql = "SELECT * FROM group_profile WHERE indexer = $group_id AND public_private = 'private'";
    $query = @mysql_query($sql);
    $count = @mysql_num_rows($query);
    if ($count != 0) {
        error_redirect(127);//"This group is marked as private, only members can view it"
    }
}

if ($action == "") {

	$mygroups = array();

    	//get each groups details
    	$sql1		= "SELECT * FROM group_profile WHERE indexer = $group_id";
    	$query1	= @mysql_query($sql1);
    	$result1	= @mysql_fetch_array($query1);

    	$group_name = $result1['group_name'];
	$page_title = $group_name;

    	//count the number of videos the group has
    	$sql2 = "SELECT * FROM group_videos WHERE group_id = $group_id AND video_status='active'";
    	$query2 = mysql_query($sql2);
    	$count_group_videos = mysql_num_rows($query2);

    	//get video picture and details  --------------------------reusable get video picture----------------
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
        	$group_video_seo = $result0['title_seo'];

        	$no_video_play = 1;
    	}

    	$video_array = array('video_picture'	=> $video_picture,
    				   'group_video_id'	=> $group_video_id,
    				   'group_video_seo'	=> $group_video_seo);

    	//count the number of member the group has
    	$sql3 = "SELECT * FROM group_membership WHERE group_id = $group_id";
    	$query3 = @mysql_query($sql3);
    	$count_group_members = mysql_num_rows($query3);

    	//get groupd admin details
   	$sql4 = "SELECT * FROM group_membership WHERE group_id = $group_id AND group_admin = 'yes'";
    	$query4 = mysql_query($sql4);
    	$result4 = mysql_fetch_array($query4);
    	$admin_username = $result4['member_username'];
    	$admin_id = $result4['member_id'];

    	//form array of bits and pieces
    	$group_other_info = array('group_videos' 		=> $count_group_videos,
    			        	  'group_members' 	=> $count_group_members,
    			        	  'admin_username'	=> $admin_username,
    			        	  'admin_id' 		=> $admin_id);

    	//merge arrays

    	$group_array = @array_merge($result1,$group_other_info,$video_array);
    	$mygroups[] = $group_array;

    	//--------------------------------------------------------------------------------------------
    	//get list of all group videos

    	//Pagination
    	$all_videos = array();
    	$limit = 12;//$config['groups_home_video_limit'];
    	$pagination = pagination("SELECT * FROM group_videos WHERE group_id = $group_id AND video_status='active'", $limit);
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
    	$sql10 = "SELECT * FROM group_videos WHERE group_id = $group_id AND video_status='active' LIMIT $set_limit, $limit";
    	$query10 = @mysql_query($sql10);

    	while($result10 = @mysql_fetch_array($query10)) {

      	//collect each videos details
        	$each_video = mysql_real_escape_string($result10['video_id']);
        	$sql11 = "SELECT * FROM videos WHERE indexer = $each_video AND approved = 'yes'";
        	$query11 = @mysql_query($sql11);
        	$result11 = @mysql_fetch_array($query11);

            if(!empty($result11)) {

    //get video star rating
    $stars_array = stars_array($each_video);//call the stars function (results returned as array)
    //merge video data with stars array
    $all_videos[] = array_merge($stars_array, $result11);
        }
    }

    //set condition for hidding certain blocks (more videos)
    if (empty($all_videos)) {
        $show_v = 1;
    }
    else {
        $show_v = 2;
    }
    //get group comments
    $limit2 = $config["comment_page_limits"];
    $pagination2 = pagination("SELECT * FROM group_comments WHERE group_id = $group_id ORDER BY indexer DESC", $limit2);
    $set_limit2 = $pagination2[0]['set_limit'];
    $total_pages2 = $pagination2[0]['total_pages'];
    $current_page2 = $pagination2[0]['current_page'];
    $total_records2 = $pagination2[0]['total_records'];
    $next_page2 = $pagination2[0]['next_page'];//use in html navigation (src)
    $prev_page2 = $pagination2[0]['prev_page'];//use in html navigation (src)
    $nl2 = $pagination2[0]['nl'];//use in html navigation: next>>
    $pl2 = $pagination2[0]['pl'];//use in html navigation: <<previous

    $result_search3 = array();
    $each_comment = array();
    $sql12 = "SELECT * FROM group_comments WHERE group_id = $group_id ORDER BY indexer DESC LIMIT $set_limit2, $limit2";
    $query12 = @mysql_query($sql12);
    $comments_count = @mysql_num_rows($query12);

    //create new array with "wrapped" comments
    while ($result1 = @mysql_fetch_array($query12)) {

        $text = $result1['comments'];
        $wrap = wordwrap($text,$wrap_limit," ",true);
        $each_comment = array('by_id' => $result1['by_id'],'indexer' => $result1['indexer'],
            'comments' => $wrap,'todays_date' => $result1['todays_date'],
            'by_username' => $result1['by_username']);

        $result_search3[] = $each_comment;
    }
    //end of comments and "wrap"

    //set condition for hidding certain blocks (e.g "no emails to list")
    if (empty($result_search3)) {
        $show_c1 = 1;
    }
    else {
        $show_c1 = 2;
    }

    //Get list of group members to add to form field

    //get my own videos
    $sql = "SELECT * FROM group_membership WHERE group_id = $group_id AND approved = 'yes'";
    $query = @mysql_query($sql);
    while ($result = @mysql_fetch_array($query)) {
        $m_title = $result['member_username'];
        $m_title = substr($m_title,0,20);//trim to fit in form field
        $field = '<option value="'.$result['member_id'].'">'.$m_title.
            '</option>';
        $member_list = $member_list.$field;
        
        //for view members profile
        $field2 = '<option value="'.$m_title.'">'.$m_title.'</option>';
        $member_list2 .= $field2;
    }

    // show all of members own videos
    // i.e. video selector form
    //----------------------------------------------------------------------------------------
    if ($user_id != "") {
        //check if user is logged in and also a member of the group
        $sql = "SELECT * FROM group_membership WHERE group_id = $group_id AND member_id = $user_id AND approved = 'yes'";
        $query = @mysql_query($sql);
        $count = @mysql_num_rows($query);
        if ($count != 0) {
            $show_v_selector = 1;//show table in html with video selection

            //get my own videos
            $sql5 = "SELECT * FROM videos WHERE user_id = $user_id AND approved = 'yes'";
            $result5 = @mysql_query($sql5);
            while ($result = @mysql_fetch_array($result5)) {
                $x_title = $result['title'];
                $x_title = substr($x_title,0,50);//trim to fit in form field
                $field = '<option value="'.$result['indexer'].'">'.$x_title.
                    '</option>';
                $my_videos = $my_videos.$field;
                $add_button = 1;//show add button in form
            }

            //get my videos i have added already to group
            $sql5 = "SELECT * FROM videos WHERE user_id = $user_id AND approved = 'yes'";
            $result5 = @mysql_query($sql5);
            while ($result = @mysql_fetch_array($result5)) {
                $x_title = $result['title'];
                $x_video_id = $result['indexer'];

                //check if it in group already
                $sql6 = "SELECT * FROM group_videos WHERE video_id = $x_video_id";
                $result6 = mysql_query($sql6);
                if (mysql_num_rows($result6) != 0) {//it its in group, add its details to form field
                    $x_title = substr($x_title,0,50);//trim to fit in form field
                    $field = '<option value="'.$x_video_id.'">'.$x_title.
                        '</option>';
                    $my_videos_already = $my_videos_already.$field;
                    $delete_button = 1;//show add button in form
                }
            }

        }
    }
    else {
        $show_v_selector = 0;//do not show table in html with video selction
    }

    //---------------------------------------------------------------------------------------------
    //show any errors/notifications
    $codes = $_GET['code'];//error codes from anywhere
    $error_code = errorcodes($codes);
    if (!empty($error_code)) {
        $blk_notification = $error_code['error_display'];
        $message_type = $error_code['error_type'];
        $error_message =$error_code['error_message'];
    }


    if ( $count_group_members < 2 ) {
    		$page_spacer = '140px;';
    	} else {
    		$page_spacer = '0;';
    	}



    //display my groups
    $template = "themes/$user_theme/templates/main_1.htm";
    $inner_template1 = "themes/$user_theme/templates/inner_group_home.htm";//middle of page
    $TBS = new clsTinyButStrong;
    $TBS->NoErr = true;// no more error message displayed.
    $TBS->LoadTemplate("$template");
    $TBS->MergeBlock('blkfeatured',$mygroups);
    $TBS->MergeBlock('mp',$all_videos);
    $TBS->MergeBlock('blk3',$result_search3);
    $TBS->Render = TBS_OUTPUT;
    $TBS->Show();

    @mysql_close();
    die();
}


?>


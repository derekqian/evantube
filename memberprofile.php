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
include_once ('classes/permissions.php');
include_once ('online.php');

// PHPmotionWiz - Display logged in member's name.
// (This code was edited following instructions provided by PHPmotionWiz.com.
// No PHPmotion core code is distributed with our mods.)
$my_user_name	= $user_name;
$my_user_id	= $user_id;
// PHPmotionWiz - End display logged in member's name

$member_id	= '';
$proceed	= true;

// PHPmotionWiz - Show message if member has no uploaded media.
// (This code was edited following instructions provided by PHPmotionWiz.com.
// No PHPmotion core code is distributed with our mods.)
$membersmedia = 0;
// PHPmotionWiz - End Nothing Found

$user = mysql_real_escape_string($_GET['user']);

if ($user) {
	$sql = mysql_query("SELECT user_id FROM member_profile WHERE user_name = '$user'");
    	$row = mysql_fetch_array($sql);
    	$member_id = $row['user_id'];
}

if ( $member_id == ''  ) {
	$proceed = false;
    	$error_height = '230px;';
}

if ( $proceed == true ) {

	//ADD VIEWTIME to member profile table
	$sql		= "UPDATE member_profile SET viewtime = NOW() WHERE user_id = $member_id";
	$query 	= @mysql_query($sql);

	// if no UID but I am logged in, then its my profile
	if( $member_id == '' && $user_id != '' ) $member_id = $user_id;

	$media = 'member_profile';

	if ($user_id == "") {
		$ajax_rating = pullRating($media,$member_id,false,true,true,'novote');
	}else{
		$ajax_rating = pullRating($media,$member_id,true,false,false,$user_id);
	}

// PHPmotionWiz - get members overall site ranking for badge display
// (This code was edited following instructions provided by PHPmotionWiz.com.
// No PHPmotion core code is distributed with our mods.)
    	$member_badge	= member_site_ranking($member_id);
// PHPmotionWiz - End Member Rank

	// Check if viewer is owner
	if( $user_id == $member_id ) {

		$show_edit_delete		= 1; 					//allow owner to manage their content (edit/delete buttons)
		$profile_menu		= 1;
		$sql_public_private 	= '';
		$viewing_user_name 	= $_SESSION['user_name'];	//$viewing_user_name needs a value here

	} else {
		$profile_menu = 2;//general menu
		// get member viewing page their user name for sub menu
		$sql 				= "SELECT user_name FROM member_profile WHERE user_id = $user_id";
		$query 			= mysql_query($sql);
		$result_u 			= @mysql_fetch_array($query);
		$viewing_user_name	= $result_u['user_name'];

		$sql_public_private 	= "AND public_private = 'public'";
	}

	// Get my Privacy Setting
	$sql = "SELECT * FROM privacy WHERE user_id = $member_id";
	$result = @mysql_fetch_array(@mysql_query($sql));

	//profile comments
	$privacy_profilecomments = $result['profilecomments'];

	//Member CCS FIle
	if (file_exists($base_path . '/addons/customprofile/member_css/' . $member_id . '.css')) {
    		$members_css = $base_url . '/addons/customprofile/member_css/' . $member_id . '.css';
	} else {
		$members_css = $base_url . '/themes/'.$user_theme.'/member_css/memberdefault.css';
	}


	// Member Actual Profile

	$sql = "SELECT * FROM member_profile WHERE user_id = $member_id";
	$query = mysql_query($sql);

	// get all values for display on members profile section
	$result = mysql_fetch_array($query);

	foreach ( $result as $key => $value ) $$key = $value;

	//$birthday from DB == yyyy-mm-dd
	if ( $birthday == '0000-00-00' ) $my_age = $lang_private;
	else $my_age = birthday ($birthday);


	// Members Online Status
	$sql = "SELECT * FROM online WHERE logged_in_id = $member_id";
	$my_online_status = @mysql_num_rows(mysql_query($sql));

	if ($my_online_status > 0) {// there is a multiple IP address issue hence use of '>'
		$online_status = '<img src="images/icon_online.gif" alt="status" width="8" height="8" border="0" align="absmiddle" />';

	} else {
		$online_status = '<img src="images/icon_offline.gif" alt="status" width="8" height="8" border="0" align="absmiddle" />';
	}

	// Update View
	$views_counter = $number_of_views + 1;
	$sql2 = "UPDATE member_profile SET number_of_views = $views_counter WHERE user_id = $member_id";
	$query2 = @mysql_query($sql2);

	//record view time in views_tracker table
	@media_views_tracker($member_id, 'member_profile');

	// Members Picture
	$sql = "SELECT * FROM pictures WHERE user_id = $member_id";
	$result1 = @mysql_query($sql);

	if (@mysql_num_rows($result1) != 0) {
    		$result1 = @mysql_fetch_array($result1);
    		$result1_existing_file = $result1['file_name'];
    		$result1_approved = $result1['approved'];

    		if ($result1_approved == "yes") {
        		// show current picture
        		$result1_mypicture = $config['site_base_url'] . '/pictures/' . $result1_existing_file;
        		$image_thumb = $base_path . '/pictures/' . $result1_existing_file;

    		} else {
        		// show place holder image  for image "awaiting approval"
        		$result1_mypicture = $config['site_base_url'] . "/themes/$user_theme/images/placeholder.gif";
        		$image_thumb = $base_path . "/themes/$user_theme/images/placeholder.gif";
    		}

	} else {
    		// show place holder for no image uploaded by user at all
    		$result1_mypicture = $config['site_base_url'] . "/themes/$user_theme/images/placeholder.gif";
    		$image_thumb = $base_path . "/themes/$user_theme/images/placeholder.gif";
	}

	//rescale thumbs
	$page_display_width = $config['members_profile_own_thumb_width'];
	$display_thumbs = show_thumb($image_thumb, $page_display_width);
	$image_width_profile = $display_thumbs[0];
	$image_height_profile = $display_thumbs[1];

	if ($image_width_profile == 0){
		$image_width_profile =100;
	}
	if ($image_height_profile == 0){
		$image_height_profile =100;
	}

	// Members Videos
	$load_media = 1;							//used in html
	$members_vids_limit = 6;					//set in both (memberprofile.php and memberprofile_ajax.php)

// PHPmotionWiz - Begin Get Random Video of Member to Play
// (This code was edited following instructions provided by PHPmotionWiz.com.
// No PHPmotion core code is distributed with our mods.)
    $sql = "SELECT * FROM videos WHERE user_id = $member_id AND approved = 'yes' AND public_private = 'public' AND video_type = 'uploaded' ORDER BY RAND()";
    $query = @mysql_query($sql);
    $result = @mysql_fetch_array($query);
    $video_play = $result['video_id'].'.flv';
    $video_thumb = $result['video_id'].'.jpg';
    $title = $result['title'];
    $indexer = $result['indexer'];
    $title_seo = $result['title_seo'];

if ( $result ) $membersmedia = 1;
// PHPmotionWiz - End Get Random Video of Member to Play

	//Pagination
	$pagination = pagination("SELECT * FROM videos WHERE user_id = $member_id AND approved = 'yes' AND public_private = 'public'", $members_vids_limit);
	$set_limit = $pagination[0]['set_limit'];
	$total_pages = $pagination[0]['total_pages'];
	$current_page = $pagination[0]['current_page'];
	$total_records = $pagination[0]['total_records'];
	$next_page = $pagination[0]['next_page'];//use in html navigation (src)
	$prev_page = $pagination[0]['prev_page'];//use in html navigation (src)
	$nl = $pagination[0]['nl'];//use in html navigation: next>>
	$pl = $pagination[0]['pl'];//use in html navigation: <<previous

	$member_videos = array();
	$sql = "SELECT * FROM videos WHERE user_id = $member_id AND approved = 'yes' $sql_public_private LIMIT $set_limit, $members_vids_limit";
	$query = @mysql_query($sql);

	if (@mysql_num_rows($query) == 0) {
    		$show_v = 1;

	} else {

    		while ($result_members_videos = @mysql_fetch_array($query)) {
      		$member_videos[] = $result_members_videos;
       		$show_v = 2;
    		}

    		////////////////////////////////////////////
    		//PAGINATION PLUS >> start  -- reusable code
    		////////////////////////////////////////////
    		$show_pages = '';
    		$url = 'javascript:void(0)" onClick="javascript:ahahscript.ahah(\'memberprofile_ajax.php';//the url to be put in links - EDIT ME
    		$ahah_pagination = "', 'Ajax-Media', '', 'GET', '', this);";//for ajax pagination
    		$additional_url_variable = '?type=1&uid=' . $member_id . '&page=';//add addtions information that goes in query string here
    		include ('includes/pagination.inc.php');
   		$show_pages_vids = $show_pages;
    		//PAGINATION PLUS >> end
	}

	// Members Friends
	$load_friends = 1;//used in html
	$friends = array();
	$sql = "SELECT * FROM friends WHERE user_id = $member_id AND invitation_status = 'accepted' OR friends_id = $member_id AND invitation_status = 'accepted'";
	$query = @mysql_query($sql);
	$my_friends_total = @mysql_num_rows($query);

	while ($result1 = @mysql_fetch_array($query)) {

    		//checking if I am the the user or the friend in the friends table. either way getting the correct "friend" id
    		$friends_id = $result1['friends_id'];

    		if ($friends_id == $member_id) {
        		$friends_id = mysql_real_escape_string($result1['user_id']);
    		}

    		// get my friends main details from member_profile table
    		$sql2 = "SELECT * FROM member_profile WHERE user_id =$friends_id";
    		$query2 = @mysql_query($sql2);
    		$result2 = @mysql_fetch_array($query2);

    		// check friends picture
    		$sql = "SELECT * FROM pictures WHERE user_id = $friends_id";
    		$query5 = @mysql_query($sql);

    		if (@mysql_num_rows($query5) > 0) {
        		$result3 = @mysql_fetch_array($query5);
        		$result3_existing_file = $result3['file_name'];
        		$result3_approved = $result3['approved'];

        		if ($result3_approved == "yes") {
            		// show picture and "change picture link"
            		$image_thumb = $config['site_base_url'] . '/pictures/' . $result3_existing_file; //url
            		$image_file = $base_path. '/pictures/' . $result3_existing_file; //full path
            		$result5 = array('friends_picture' => $image_thumb);

        		} else {

            		// show place holder image
            		$image_thumb = $config['site_base_url'] . "/themes/$user_theme/images/placeholder.gif";//url
            		$image_file = $base_path."/themes/$user_theme/images/placeholder.gif";//full path
            		$result5 = array('friends_picture' => $image_thumb);
        		}

		} else {
            	// show place holder image
            	$image_thumb = $config['site_base_url'] . "/themes/$user_theme/images/placeholder.gif";//url
            	$image_file = $base_path."/themes/$user_theme/images/placeholder.gif";//full path
            	$result5 = array('friends_picture' => $image_thumb);
        	}

		//rescale thumbs
		$page_display_width = $config['members_prof_friends_thumb_width'];
		$display_thumbs = show_thumb($image_file, $page_display_width);
		$image_width = $display_thumbs[0];
		$image_height = $display_thumbs[1];
		$thumb_sizes = array('image_width'=> $image_width, 'image_height'=> $image_width);

		$friends_merged = @array_merge($result2, $result5, $thumb_sizes);
		$friends[] = $friends_merged;
	}

	// My Stats

	//total videos
	$sql = "SELECT indexer FROM videos WHERE approved='yes' AND user_id = $member_id";
	$my_video_total = @mysql_num_rows(mysql_query($sql));

	//total Audios
	$sql = "SELECT indexer FROM audios WHERE approved='yes' AND user_id = $member_id";
	$my_audio_total = @mysql_num_rows(mysql_query($sql));

	//total images
	$sql = "SELECT indexer FROM images WHERE approved='yes' AND user_id = $member_id";
	$my_image_total = @mysql_num_rows(mysql_query($sql));

	//total blogs
	$sql = "SELECT indexer FROM blogs WHERE approved='yes' AND user_id = $member_id";
	$my_blog_total = @mysql_num_rows(mysql_query($sql));

	// Profile Comments
	$sql = "SELECT * FROM profilecomments WHERE members_id = $member_id ORDER BY indexer DESC ";
	$query = @mysql_query($sql);
	$profilecomments = array();

	while ($result = @mysql_fetch_array($query)){
		$by_id = $result['by_id'];
		$indexer_id = $result['indexer'];

		//Get posters ID
		// check friends picture
   		$sql = "SELECT * FROM pictures WHERE user_id = $by_id";
    		$query5 = @mysql_query($sql);

    		if (@mysql_num_rows($query5) > 0) {
        		$result3 = @mysql_fetch_array($query5);
        		$result3_existing_file = $result3['file_name'];
        		$result3_approved = $result3['approved'];

        			if ($result3_approved == "yes") {
            			// show picture and "change picture link"
            			$image_thumb = $config['site_base_url'] . '/pictures/' . $result3_existing_file; //url
            			$image_file = $base_path. '/pictures/' . $result3_existing_file; //full path
            			$result5 = array('friends_picture' => $image_thumb);

        			} else {
            			// show place holder image
            			$image_thumb = $config['site_base_url'] . "/themes/$user_theme/images/placeholder.gif";//url
            			$image_file = $base_path."/themes/$user_theme/images/placeholder.gif";//full path
            			$result5 = array('friends_picture' => $image_thumb);
        			}
		} else {
            	// show place holder image
            	$image_thumb = $config['site_base_url'] . "/themes/$user_theme/images/placeholder.gif";//url
           	 	$image_file = $base_path."/themes/$user_theme/images/placeholder.gif";//full path
           		$result5 = array('friends_picture' => $image_thumb);
        	}

		//rescale thumbs
		$page_display_width = $config['members_prof_friends_thumb_width'];
		$display_thumbs = show_thumb($image_file, $page_display_width);
		$image_width = $display_thumbs[0];
		$image_height = $display_thumbs[1];
		$posters_thumb = array('image_width'=> $image_width, 'image_height'=> $image_width, 'image_thumb'=> $image_thumb);

		// PERMISSIONS CHECK VIDEOS COMMENTS
		$usercheck = new LoadPermissions('', $indexer_id, 'profile_comments');
    		$usercheck->CheckPermissions();
    		$profile_comment_edit_on = $usercheck->ReturnVars('edit_on');
    		$profile_comment_delete_on = $usercheck->ReturnVars('delete_on');

    		//Edit link
    		if($profile_comment_edit_on == 1){
    			$profile_comment_edit_on = '<a href="edit/editcomments.php?type=8&id='.$indexer_id.'" title="Edit" rel="gb_page_center[800, 400]">
        		<img src="'.$base_url.'/themes/'.$user_theme.'/images/icons/edit_small.png" alt="'.$lang_edit.'" width="14" height="14" border="0" />'.$lang_edit.'</a>';

        	} else {

        		$profile_comment_edit_on = '';
    		}

    		//Delete link
    		if($profile_comment_delete_on ==1){
    			$profile_comment_delete_on = '<a href="edit/delete.php?type=6&id='.$indexer_id.'" title="Delete" rel="gb_page_center[800, 300]">
        		<img src="'.$base_url.'/themes/'.$user_theme.'/images/icons/delete_small.png" alt="'.$lang_delete_general.'" width="14" height="14" border="0" />'.$lang_delete_general.'</a>';

        	} else {
        	    	$profile_comment_delete_on = '';
		}

    		$comment_permissions = array('profile_comment_edit'	=>$profile_comment_edit_on,
				                 'profile_comment_delete'	=>$profile_comment_delete_on);

		$comments_array = array_merge($posters_thumb, $result, $comment_permissions);
		$profilecomments[] = $comments_array;
	}

	//hide or show custom profile
	if($profile_menu == 1 && $config["enabled_features_custome_profile"] == 'yes'){
		$custom_profile = 1;
	}

	// Display Results
	$template = "themes/$user_theme/templates/inner_members_header.htm";
	$inner_template = "themes/$user_theme/templates/inner_members_profile.htm";//middle of page

	$TBS = new clsTinyButStrong;
	$TBS->NoErr = true;// no more error message displayed.
	$TBS->LoadTemplate("$template");
	$TBS->MergeBlock('membersmedia', $member_videos);
	$TBS->MergeBlock('membersfavs', $member_favorites);
	$TBS->MergeBlock('membersfriends', $friends);
	$TBS->MergeBlock('profilecomments', $profilecomments);
	$TBS->Render = TBS_OUTPUT;
	$TBS->Show();
	@mysql_close();
	die();

} else {

	// show error page
	$members_css = $base_url . '/themes/'.$user_theme.'/member_css/memberdefault.css';

	//$blk_notification	= 1;

	$message_2		= 'Error';
	$message		= 'This member was not found';

	//$message_type	= $message_2;// $config['error_26'];
	//$error_message	= $message; //$config['error_11'];

	$template		= "themes/$user_theme/templates/inner_members_header.htm";

	//$inner_template = "themes/$user_theme/templates/inner_members_profile.htm";

	$inner_template 	= "themes/$user_theme/templates/inner_notification.htm";

	$TBS 			= new clsTinyButStrong;
	$TBS->NoErr 	= true;

	$TBS->LoadTemplate("$template");
	$TBS->Render	= TBS_OUTPUT;
	$TBS->Show();
	@mysql_close();
	die();


}

?>

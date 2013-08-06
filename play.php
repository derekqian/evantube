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
include_once('classes/permissions.php');

// load required javascripts used in main_1.htm
$rating_update	= 1;
$ahah			= 1;
$thickbox		= 1;
$greybox		= 1;
$load_ajax 		= 1;
$load_carousel	= 1;
// end main_i loads


$small_block_background = $config['color_tellafriend'];

$progress_bar_width	= 0;
$blk_notification 	= '';
$wrap_limit 		= 80;
$codes			= mysql_real_escape_string($_GET['code']);
$proceed			= true;
$enabled_vid_comments	= $config["enabled_features_video_comments"];
$vid 				= (int) mysql_real_escape_string($_GET['vid']);
$media 			= 'videos';
$more_user 			= 0;
$more_related		= 0;
$show_member_videos	= $config['show_member_videos'];


//________________________________________________________
//___Enabled Features Check_______________________________
include_once('includes/enabled_features.php');


if ($user_id == '') {
	$ajax_rating = pullRating($media,$vid,false,true,true,'novote');
}else{
	$ajax_rating = pullRating($media,$vid,true,false,true,$user_id);
}

//ADD TIME TO VIDEO DB VIEWTIME ROW
$sql		= "UPDATE videos SET viewtime = NOW() WHERE indexer = $vid";
$query 	= @mysql_query($sql);

//Get video details
$sql 			= "SELECT * FROM videos WHERE indexer = $vid AND approved ='yes'";
$query 		= @mysql_query($sql);
$result 		= @mysql_fetch_array($query);
$active 		= $result['approved'];
$public_private 	= $result['public_private'];
$allow_embedding	= $result['allow_embedding'];
$member_id 		= $result['user_id'];
$date_uploaded	= $result['date_uploaded'];
$num_views		= $result['number_of_views'];

//disply the "video details" tabled in inner_play.htm hidden comments; <!--[onload_19;block=tr;when [var.showdetails]=1;comm]-->
if ( !empty($result) ) {
	$showdetails = 1;
} else {
	$error_height = '430px;';
	$message_2 = $config['error_26']. ' - ' .$config['error_11'];
}

//record view time in views_tracker table
@media_views_tracker($vid, 'videos');

if ( $proceed == false ) {

	$codes = $codes_internal;
	$error_code = errorcodes($codes);

	// tmp test
	if ( $error_height ==  '' ) {
		if (!empty($error_code)) {
			$blk_notification = $error_code['error_display'];
    			$message_type = $error_code['error_type'];
    			$error_message = $error_code['error_message'];
		}
	}

	$template 		= "themes/$user_theme/templates/main_1.htm";
	$inner_template1 	= "themes/$user_theme/templates/inner_notification.htm";

	$TBS 			= new clsTinyButStrong;
	$TBS->NoErr 	= true;
	$TBS->LoadTemplate("$template");

	$TBS->Render 	= TBS_OUTPUT;
	$TBS->Show();

	@mysql_close();
	die();
}

//if all checks out proceed.
if ( $proceed == true ) {
	$title 		= $result['title'];
	$title_seo 		= $result['title_seo'];
    	$description 	= $result['description'];
   	$tags 		= $result['tags'];

   	$title		= safe_word_wrap($title);
	$title_seo		= safe_word_wrap($title_seo);
    	$description	= safe_word_wrap($description);
   	$tags			= safe_word_wrap($tags);

    	$page_title 	= $title;

    	$channel 		= $result['channel'];
    	$date_uploaded 	= $result['date_uploaded'];
    	$video_length 	= $result['video_length'];
    	$allow_comments 	= $result['allow_comments'];
    	$allow_embedding	= $result['allow_embedding'];
    	$public_private 	= $result['public_private'];
    	$number_of_views 	= (int) mysql_real_escape_string($result['number_of_views']);
    	$video_play 	= $result['video_id'] . '.flv';
    	$video_id 		= $result['indexer'];
	$video_thumb	= $result['video_id'] . '.jpg';
    	$video_url 		= $config['site_base_url'] . '/play.php?vid=' . $video_id;
    	$seo_video_url	= $config['site_base_url'] . '/videos/'.$video_id.'/'.$title_seo;

      //------------------VIDEOEMBEDER---------------------------------
      $video_type = $result['video_type'];
      $embed_id	= $result['embed_id'];

      if($video_type == 'embedded') include('addons/videoembedder/embed.php');

      if($video_type == 'mass_embedded') {
      	include('addons/massembedder/embed.php');
          	$video_type = 'embedded'; //rest to just 'embedded' - for inner_play.htm
	}
      //---------------------------------------------------------------

    	//get members personal information
    	$sql			= "SELECT user_name, last_seen, date_created FROM member_profile WHERE user_id = $member_id";
    	$query		= @mysql_query($sql);
    	$result 		= @mysql_fetch_array($query);
    	$member_username 	= $result['user_name'];

    	// last seen / active
    	$db_date	= $result['last_seen'];
    	if ( $db_date == '0000-00-00 00:00:00' ) $db_date = $result['date_created'];
    	$change_date	= dateTimeDiff($db_date);
    	$last_seen		= $change_date;

    	// get members rating and last active date
    	define('members', true);
    	$id			= $member_id;
    	include ('stars_include.php');
    	$stars_array	= array('star1' => $star1,'star2' => $star2,'star3' => $star3, 'star4' => $star4,'star5' => $star5);

    	// get members overall site ranking for badge display
    	$member_badge	= member_site_ranking($member_id);

    	//update video counter
    	$views_counter 	= $number_of_views + 1;
    	$sql 			= "UPDATE videos SET number_of_views = $views_counter WHERE indexer = $vid";
    	$query		= @mysql_query($sql);

    	//______________________________________________________________________________________________
	//______PERMISSIONS CHECK VIDEOS________________________________________________________________
    	$usercheck 		= new LoadPermissions('',$video_id,'videos');  //($user_id, $content_id, $content_type);
    	$usercheck->CheckPermissions();
    	$edit_video 	= $usercheck->ReturnVars('edit_on');
    	$delete_video 	= $usercheck->ReturnVars('delete_on');
    	//_______________________________________________________________________________________________

}

//Video tags --------------------------reusable code---------------------
if ( $tags != "" ) {
	$tags_exploded = explode(" ", $tags);
    	$i = 0;
    	while ( isset($tags_exploded[$i]) ):
      	$tags_each = '<a href="search.php?type=videos&keyword='.$tags_exploded[$i].'">'.$tags_exploded[$i].'</a>&nbsp;&nbsp;';
        	$tags_string = $tags_string . $tags_each;
        	$i++;
    	endwhile;
}
//-------------------------------------------------------------------------


//get related videos by tags
$search_tags		= mysql_real_escape_string($tags);
$search_tags_array 	= explode(' ', $search_tags);

if ( $member_id != "" ) {

	foreach ($search_tags_array as $tag_word) {
		// $sql		= "SELECT indexer FROM videos WHERE indexer != '$vid' AND approved='yes' AND public_private = 'public' AND title LIKE '%$tag_word%' LIMIT 4";

// PHPmotionWiz - Begin Related Videos SQL
// (This code was edited following instructions provided by PHPmotionWiz.com.
// No PHPmotion core code is distributed with our mods.)
        $sql = "SELECT indexer FROM videos WHERE indexer != '$vid' AND approved='yes' AND public_private = 'public' AND channel = '$channel' LIMIT 6";
// PHPmotionWiz - End Promoted Videos SQL

		$query 	= @mysql_query($sql);
		while ($result1 = @mysql_fetch_array($query)) $tmp_result_search[] = $result1['indexer'];
	}

	$tmp_related = array_unique($tmp_result_search);

	foreach ($tmp_related as $indexer){
		$sql			= "SELECT * FROM videos WHERE indexer = $indexer";
		$query 		= @mysql_query($sql);
		$row			= @mysql_fetch_array($query);
		$result_search[]	= $row;
	}
}

//print_r($result_search);

if ( $result_search ) $more_related = 1;

//get videos from same member
$result_search2 = array();

if ( $member_id != "" ) {
	$sql = "SELECT * FROM videos WHERE indexer != '$vid' AND user_id = $member_id AND approved='yes' AND public_private = 'public' ORDER BY number_of_views DESC LIMIT $show_member_videos";
    	$query = @mysql_query($sql);

    	while ($result1 = @mysql_fetch_array($query)) $result_search2[] = $result1;
}

if ( $result_search2 ) $more_user = 1;


// get member video TEXT comments settings
$text_comments 	= 0;
$sql_count 		= "SELECT * FROM videocomments WHERE video_id = $vid";
$num_query 		= @mysql_query($sql_count);
$text_comments	= mysql_num_rows($num_query);


// ajax pagination reuseable
if( empty($page) ) $page = 1;

$limit 				= $config['comment_page_limits'];
$limit_value 			= $page * $limit - ($limit);
$totalrows 				= $text_comments;
$config['ajax_page_previous'] = $config['pagination_previous'];
$config['ajax_page_next']	= $config['pagination_next'];

// PREVIOUS
if( $page != 1 ) {
	$pageprev = ($page - 1);
	$ajax_page_previous = $config['ajax_page_previous'];
} else {
	$ajax_page_previous = '';
}

// NEXT
$next_page = $totalrows - ($limit * $page);
if( ($totalrows - ($limit * $page) ) > 0) {
	$pagenext = ($page + 1);
	$ajax_page_next = "&nbsp;&nbsp;" . $config['ajax_page_next'];
	$refresh = 1;
} else {
	$ajax_page_next = '';
	$refresh = 0;
}

$result_search3 = array();
$each_comment = array();

$sql = "SELECT * FROM videocomments WHERE video_id = $vid ORDER BY indexer DESC LIMIT $limit_value, $limit";
$query = @mysql_query($sql);

$see_limit = 0;

//create new array with "wrapped" comments
while ($result1 = @mysql_fetch_array($query)) {
	$see_limit++;
	//get comentors picture (added 01-01-2008)
	$poster_id = $result1['by_id'];
	$a1_sql = "SELECT * FROM pictures WHERE user_id = $poster_id";
	$a1_result = @mysql_query($a1_sql);

	if (@mysql_num_rows($a1_result) != 0) {
		$a1_result = @mysql_fetch_array($a1_result);
		$a1_existing_file = $a1_result['file_name'];
		$a1_approved = $a1_result['approved'];

		if ($a1_approved == "yes") {
			// show picture and "change picture link"
			$poster_picture = $config['site_base_url'] . '/pictures/' . $a1_existing_file;
		} else {
			// show place holder or blank (default is blank)- uncomment or comment the option you want
			$poster_picture = $config['site_base_url'] . "/themes/$user_theme/images/placeholder.gif";
		}
	} else {
		// show place holder or blank (default is blank)- uncomment or comment the option you want
		$poster_picture = $config['site_base_url'] . "/themes/$user_theme/images/placeholder.gif";
	}

	$text = $result1['comments'];
	$wrap = htmlspecialchars_decode($text);

	$indexer_id = $result1["indexer"];
	$drop_block = "<div id=\"drop_block_$indexer_id\"></div>";


      //______________________________________________________________________________________________
	//______PERMISSIONS CHECK VIDEOS COMMENTS_______________________________________________________

    	$usercheck = new LoadPermissions('', $indexer_id, 'video_comments');
    	$usercheck->CheckPermissions();
    	$video_comment_edit_on = $usercheck->ReturnVars('edit_on');
    	$video_comment_delete_on = $usercheck->ReturnVars('delete_on');

    	//Edit link
    	if($video_comment_edit_on == 1){
    	$video_comment_edit_on = '<a href="edit/editcomments.php?type=1&id='.$indexer_id.'" title="Edit" rel="gb_page_center[700, 400]">
        <img src="'.$base_url.'/themes/'.$user_theme.'/images/icons/edit_small.png" alt="'.$lang_edit.'" width="14" height="14" border="0" />
         '.$lang_edit.'</a>';
    	}else{
    	$video_comment_edit_on = '';
    	}

    	//Delete link
    	if($video_comment_delete_on ==1){
    	$video_comment_delete_on = '<a href="edit/delete.php?type=19&id='.$indexer_id.'" title="Delete" rel="gb_page_center[700, 300]">
        <img src="'.$base_url.'/themes/'.$user_theme.'/images/icons/delete_small.png" alt="'.$lang_delete_general.'" width="14" height="14"
		border="0" /> '.$lang_delete_general.'</a>';
    	}else{
    	$video_comment_delete_on = '';
    	}
    	//_______________________________________________________________________________________________



	// reset html block to nothing
	$comment_actions_block = "";

	////////////////////////////////////////////////////////////////////////////////////
	// Now query videocomments_replys to find any REPLIES

	$each_reply = array();

	$sql_replies = "SELECT * FROM videocomments_replys WHERE videocomment_id = $indexer_id ORDER BY indexer DESC LIMIT $limit_value, $limit";

	$replies = @mysql_query($sql_replies);
	$replies_count = mysql_num_rows($replies);
	$reply_wrap = array();
	$by_username = array();
	$reply_date = array();

	$reply_block = "";

	while ($result_replies = @mysql_fetch_array($replies)) {
		$reply_text 	= $result_replies['comment_reply'];
		$reply_text		= htmlspecialchars_decode($reply_text);
		//$reply_text 	= to_bbc_code($reply_text);
		$reply_wrap 	= wordwrap($reply_text, $wrap_limit, " ", true);
		$by_username 	= $result_replies['by_username'];
		$db_date		= $result_replies['todays_date'];
		$reply_date		= date($config["date_format"], strtotime($db_date));
		$reply_id		= $result_replies['indexer'];


        	//______________________________________________________________________________________________
		//______PERMISSIONS CHECK COMMENTS______________________________________________________________

    		$usercheck = new LoadPermissions('', $reply_id, 'videocomments_replys');
    		$usercheck->CheckPermissions();
    		$reply_edit_on = $usercheck->ReturnVars('edit_on');
    		$reply_delete_on = $usercheck->ReturnVars('delete_on');

		if ( $reply_delete_on == 1 ) {
			$delete_reply_on = "<div style=\"margin-right:12px; float:right;\">
			  <a href=\"edit/delete.php?type=14&id=$reply_id\" title=\"$lang_delete_general\" rel=\"gb_page_center[800, 400]\">
              <img src=\"$base_url/themes/$user_theme/images/icons/delete_small.png\" alt=\"$lang_delete_general\" width=\"14\" height=\"14\" border=\"0\" />
			  $lang_delete_general</a>
			</div>\n";
		} else {
			$delete_reply_on = "<div style=\"margin-right:12px; float:right;\">&nbsp;</div>\n";;
		}

		if ( $reply_edit_on == 1 ) {
			$edit_reply_on = "<div style=\"margin-right:12px; float:right;\">
			  <a href=\"edit/editcomments.php?type=2&id=$reply_id\" title=\"$lang_edit\" rel=\"gb_page_center[800, 400]\">
              <img src=\"$base_url/themes/$user_theme/images/icons/edit_small.png\" alt=\"$lang_delete_general\" width=\"14\" height=\"14\" border=\"0\" />
			  $lang_edit</a>
			</div>\n";
		} else {
			$edit_reply_on = "<div style=\"margin-right:12px; float:right;\">&nbsp;</div>\n";
		}

        	//_______________________________________________________________________________________________________________


		$reply_block .= "<hr>
		<div style=\"width: 100%; background-color: $small_block_background;\" id=\"reply_block\">
		  <div>
		    <div style=\"margin-left:12px; float:left;\">$lang_reply_by:&nbsp;<b>$by_username</b></div>
		    <div style=\"margin-left:42px; float:left;\"><b>$lang_date:&nbsp;</b>$reply_date</div>\n";

		$reply_block .= $delete_reply_on . $edit_reply_on;

		$reply_block .= "</div></div>\n";

		$reply_block .="<div style=\" background-color: $small_block_background;\"><!-- end div here //-->
		  <br>
		  <br>
		  <div style=\"margin-left:12px; float:left;\">$reply_wrap</div>
		  <br>
		  <br>
		</div>\n";
	}

	$each_comment = array('by_username'			=>$result1['by_username'],
				    'indexer'			=>$result1['indexer'],
				    'comments'			=>$wrap,
				    'todays_date'			=>$result1['todays_date'],
				    'by_username'			=>$result1['by_username'],
				    'poster_picture'		=>$poster_picture,
				    'rating'			=>$result1['updated_rating'],
				    'video_comment_edit'	=>$video_comment_edit_on,
				    'video_comment_delete'	=>$video_comment_delete_on,
				    'drop_block'			=>$drop_block,
				    'reply_block'			=>$reply_block);

	$result_search3[] = $each_comment;
}

if ($user_id == "")
	$login_comment_post = $config['login_first'];
else
	$login_comment_post = '';

if ($text_comments == 0) $text_alert_msg = $config['video_comment_msg'];

// Get video responses
$result_search4 	= array();
$each_response 	= array();
$sql			= "SELECT * FROM videos WHERE response_id = $vid AND approved ='yes' AND public_private = 'public' ORDER BY indexer DESC"; //LIMIT $set_limit, $limit";
$query_r 		= @mysql_query($sql);
$vid_responses = 0;
$ajax_load	= '';

while ($result_r = @mysql_fetch_array($query_r)) {
	$vid_responses	= $vid_responses +1;
	$indexer 		= $result_r['indexer'];
	$video_id		= $result_r['video_id'];
	$title_seo		= $result_r['title_seo'];
	$ajax_load .= "{url: \"uploads/thumbs/$video_id.jpg\", title: \"$indexer/$title_seo\"},";
}

if ($user_id == "") {
	$login_post_msg = $config['login_first'];
} else {
	$login_post_msg = '<a href="response_uploader.php?vid='.$vid.'&amp;keepThis=true&amp;TB_iframe=true&amp;height=360&amp;width=600" class="thickbox">'.$config["response_post"].'</a>';
}

if ( $vid_responses == 0 ) {
	$video_alert_msg = $config['video_alert_msg']; // 'Be the first to post a video response!';
} else {
	$response_show = 1;
}

// set condition for hidding certain blocks
if (empty($result_search3)) {
    	$show_c1 = 1;
} else {
	$show_c1 = 2;
}

// get members picture (added - 01/01/2008)
$a1_sql	= "SELECT * FROM pictures WHERE user_id = $member_id";
$a1_result 	= @mysql_query($a1_sql);
if ( @mysql_num_rows($a1_result) != 0 ) {
	$a1_result = @mysql_fetch_array($a1_result);
    	$a1_existing_file = $a1_result['file_name'];
    	$a1_approved = $a1_result['approved'];

    	if ( $a1_approved == "yes" ) {
    		// show picture and "change picture link"
        	$a1_mypicture = $config['site_base_url'] . '/pictures/' . $a1_existing_file;
        	$a1_show = 1;

    	} else {
      	// show place holder image and "awaiting approval link"
        	$a1_mypicture = $config['site_base_url'] . "/themes/$user_theme/images/placeholder.gif";
        	$a1_show = 2;
    	}

} else {
	// show place holder with "upload image link"
    	$a1_mypicture = $config['site_base_url'] . "/themes/$user_theme/images/placeholder.gif";
    	$a1_show = 3;
}

// get members online status
$sql			= "SELECT * FROM online WHERE logged_in_id = $member_id";
$my_online_status = @mysql_num_rows(mysql_query($sql));

if ( $my_online_status > 0 ){ 						//there is a multiple IP address issue hence use of '>'
	$online_status = '<img src="images/icon_online.gif" alt="'.$lang_status.'" width="8" height="8" border="0" align="absmiddle" />';
} else {
	$online_status = '<img src="images/icon_offline.gif" alt="'.$lang_status.'" width="8" height="8" border="0" align="absmiddle" />';
}

//show any errors/notifications
if ($codes == "") {								//only if page is not trying to load refer error.
	$codes = $codes_internal;
}

$error_code = errorcodes($codes);
if (!empty($error_code)) {
	$blk_notification = $error_code['error_display'];
    	$message_type = $error_code['error_type'];
    	$error_message = $error_code['error_message'];
}

//////////////////////////////////////////////////////////////////
// override downloads if this video has embeding turned off!!
if ( $allow_embedding == 'no' ) $allow_download = 0;

//////////////////////////////////////////////////////////////////
// override downloads if viewer is a guest!!
if ( $user_id == '' )  $allow_download = 0;

//////////////////////////////////////////////////////////////////
// override downloads if video is not an upload!!
if($video_type == 'embedded') $allow_download = 0;


$player_token	= randomcode();

$template 		= "themes/$user_theme/templates/main_1.htm";
$inner_template1 	= "themes/$user_theme/templates/inner_play.htm";//middle of page
$TBS 			= new clsTinyButStrong;
$TBS->NoErr 	= true;
$TBS->LoadTemplate("$template");

$TBS->MergeBlock('blk1', $result_search);
$TBS->MergeBlock('blk2', $result_search2);
$TBS->MergeBlock('blk3', $result_search3);

// video responses if any
$TBS->MergeBlock('blk4', $result_search4);

$TBS->Render 	= TBS_OUTPUT;
$TBS->Show();

@mysql_close();

die();

?>

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

// load required javascripts used in main_1.htm
$swfobject 		= 0;
$rating_update	= 1;
$ahah			= 1;
$thickbox		= 1;
$greybox		= 1;
$load_ajax 		= 1;
$audio_js 		= 1;

$progress_bar_width	= 0;
$blk_notification		= '';
$small_block_background	= $config['color_tellafriend'];
$default_album_pic	= $config['default_album_pic'];
$load_ajax 			= 1;
$wrap_limit 		= 80;
$codes			= (int) mysql_real_escape_string($_GET['code']);
$proceed			= true;

$enabled_audio_comments	= $config['enabled_features_audio_comments'];
$audio			= (int) mysql_real_escape_string($_GET['audio']);
$media 			= 'audios';

if ($user_id == "") {
	$ajax_rating = pullRating($media,$audio,false,true,true,'novote');
}else{
	$ajax_rating = pullRating($media,$audio,true,false,true,$user_id);
}

//________________________________________________________
//___Enabled Features Check_______________________________
include_once ('includes/enabled_features.php');


//START-----------------------
if ( $config['allow_download'] == 'yes' ) {
	$allow_download = 1;
}

//ADD VIEWTIME
$sql		= "UPDATE audios SET playtime = NOW() WHERE indexer = $audio";
$query 	= @mysql_query($sql);

//Get audio details
$sql			= "SELECT * FROM audios WHERE indexer = $audio AND approved = 'yes'";
$query 		= @mysql_query($sql);
$result 		= @mysql_fetch_array($query);
$active 		= $result['approved'];
$public_private	= $result['public_private'];
$allow_embedding 	= $result['allow_embedding'];
$member_id 		= $result['user_id'];

if ( !empty($result) ) $showdetails = 1;

//record view time in views_tracker table
@media_views_tracker($audio, 'audios');

if ( $active != "yes" || empty($result) ) {
	$codes_internal = 111;
    	$proceed = false;
}

if ( $public_private == "private" && $user_id != $member_id ) {
	$codes_internal = 112;
    	$proceed = false;
}

if ( $allow_embedding == 'no' )
	$show_embed = 0;
else
	$show_embed = 1;//show

if ( $proceed == true ) {

// PHPmotionWiz - Begin Grab Audio Thumb
// (This code was edited following instructions provided by PHPmotionWiz.com.
// No PHPmotion core code is distributed with our mods.)
$aid = $result['album_id'];
    $aSQL = "SELECT album_picture FROM audio_albums WHERE album_id = '$aid' LIMIT 1";
    $aResult = @mysql_query($aSQL);
    $row = mysql_fetch_array($aResult);
    $ap = $row['album_picture'];
    if($ap != ""){
        $album_cover = 'addons/audio/images/album_images/'.$ap;
    }else{
        $album_cover = 'addons/audio/images/featured_mp3.png'.$ap;
    }
// PHPmotionWiz - End Grab Audio Thumb

	$title 		= $result['title'];
	$title_seo		= $result['title_seo'];
	$artist 		= $result['artist'];
	$description 	= $result['description'];
   	$tags 		= $result['tags'];
	$description	= safe_word_wrap($description);
   	$tags			= safe_word_wrap($tags);
    	$channel 		= $result['channel'];
    	$date_uploaded 	= $result['date_uploaded'];
    	$audio_length 	= $result['audio_length'];
    	$allow_comments	= $result['allow_comments'];
    	$allow_embedding	= $result['allow_embedding'];
    	$public_private 	= $result['public_private'];
    	$number_of_views 	= (int) mysql_real_escape_string($result['number_of_views']);
    	$audio_play 	= $result['audio_id'];
    	$audio_id 		= $result['indexer'];
    	$seo_audio_url	= $config['site_base_url'] . '/audio/'.$audio.'/'.$title_seo;

    	$page_title 	= $title;

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

    	//update counter
    	$views_counter 	= $number_of_views + 1;
    	$sql 			= "UPDATE audios SET number_of_views = $views_counter WHERE indexer = $audio";
    	$query		= @mysql_query($sql);

    	//______PERMISSIONS CHECK AUDIO__________________________________________________________________________
    	$usercheck 		= new LoadPermissions('',$audio,'audios');  //($user_id, $content_id, $content_type);
    	$usercheck->CheckPermissions();
    	$edit_audio 	= $usercheck->ReturnVars('edit_on');
    	$delete_audio 	= $usercheck->ReturnVars('delete_on');
    	//________________________________________________________________________________________________________

}

//audio tags --------------------------reusable code---------------------
if ( $tags != "" ) {
	$tags_exploded = explode(" ", $tags);
    	$i = 0;
    	while ( isset($tags_exploded[$i]) ):
		$tags_each = '<a href="search.php?type=audios&keyword='.$tags_exploded[$i].'">'.$tags_exploded[$i].'</a>&nbsp;&nbsp;';
		$tags_string = $tags_string . $tags_each;
        	$i++;
    	endwhile;
}
//-------------------------------------------------------------------------

//get related audios
$search_title_terms	= mysql_real_escape_string($title);
$search_tags_terms 	= mysql_real_escape_string($tags);
$result_search 		= array();

if ( $member_id != "" ) {

	// $sql1 = "SELECT * FROM audios WHERE indexer != '$audio' AND approved='yes' AND public_private = 'public' and (title like '%$search_title_terms%' or tags like '%$search_tags_terms%' or description like '%$search_tags_terms%') LIMIT 6";

// PHPmotionWiz - Begin Related Audio Fix
// (This code was edited following instructions provided by PHPmotionWiz.com.
// No PHPmotion core code is distributed with our mods.)
	$sql1 = "SELECT * FROM audios WHERE indexer != '$audio' AND approved='yes' AND public_private = 'public' AND channel = '$channel' ORDER BY indexer DESC LIMIT 12";
// PHPmotionWiz - End Related Audio Fix

	$query1 = @mysql_query($sql1);

	if(!$query1) {
		die($config['error_26']);
		@mysql_close();
		die();
	}

	while ( $result1 = @mysql_fetch_array($query1) ) {

		if ( $result1 ) $more_related = 1;

        	// get album picture if any
        	$album_id		= mysql_real_escape_string($result1['album_id']);
        	$pic_sql		= "SELECT album_picture FROM audio_albums WHERE album_id = '$album_id' LIMIT 1";
        	$album_pic_result = @mysql_query($pic_sql);
        	$row			= mysql_fetch_array( $album_pic_result );
        	$album_pic_result = $row['album_picture'];

        	if ( $album_pic_result != "" ) {
			$album_pic = 'addons/audio/images/album_images/' . $album_pic_result;
		} else {
			$album_pic = 'addons/audio/images/' . $default_album_pic;
		}

		$album_pic_array	= array('album_pic' => $album_pic);
		$merge_result	= @array_merge($result1, $album_pic_array);
		$result_search[] = $merge_result;
	}
}

if ( sizeof($result_search) == 0 ) $more_related = 0;

//get audios from same member
$result_search2 = array();
if ( $member_id != "" ) {

	$more_user = 0;

	// $sql = "SELECT * FROM audios WHERE indexer != '$audio' AND user_id = $member_id AND approved='yes' AND public_private = 'public' ORDER BY number_of_views DESC LIMIT 6";

// PHPmotionWiz - Change More by Member limit
// (This code was edited following instructions provided by PHPmotionWiz.com.
// No PHPmotion core code is distributed with our mods.)
$sql = "SELECT * FROM audios WHERE indexer != '$audio' AND user_id = $member_id AND approved='yes' AND public_private = 'public' ORDER BY number_of_views DESC LIMIT 4";
// PHPmotionWiz - End Change More by Member Limit

    	$query = @mysql_query($sql);
    	while ($result2 = @mysql_fetch_array($query)) {

    		if ( $result2 ) $more_user = 1;

        	// get album picture if any
        	$album_id		= mysql_real_escape_string($result2['album_id']);
        	$pic_sql		= "SELECT album_picture FROM audio_albums WHERE album_id = '$album_id' LIMIT 1";
        	$album_pic_result = @mysql_query($pic_sql);
        	$row			= mysql_fetch_array( $album_pic_result );
        	$album_pic_result = $row['album_picture'];

        	if ( $album_pic_result != "" ) {
			$album_pic = 'addons/audio/images/album_images/' . $album_pic_result;
		} else {
			$album_pic = 'addons/audio/images/' . $default_album_pic;
		}

		$album_pic_array	= array('album_pic' => $album_pic);
		$merge_result	= @array_merge($result2, $album_pic_array);
		$result_search2[] = $merge_result;
	}
}

// get member audio TEXT comments settings
$text_comments 	= 0;
$sql_count 		= "SELECT * FROM audiocomments WHERE audio_id = $audio";
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

$sql = "SELECT * FROM audiocomments WHERE audio_id = $audio ORDER BY indexer DESC LIMIT $limit_value, $limit";
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
	$wrap = wordwrap($text, $wrap_limit, " ", true);

	//$wrap = to_bbc_code($wrap);
	$wrap = htmlspecialchars_decode($wrap);

	$indexer_id = $result1["indexer"];
	$drop_block = "<div id=\"drop_block_$indexer_id\"></div>";


        //______________________________________________________________________________________________
		//______PERMISSIONS CHECK COMMENTS______________________________________________________________
    	$usercheck = new LoadPermissions('', $indexer_id, 'audio_comments');
    	$usercheck->CheckPermissions();
    	$audio_comment_edit_on = $usercheck->ReturnVars('edit_on');
    	$audio_comment_delete_on = $usercheck->ReturnVars('delete_on');

    	//Edit link
    	if($audio_comment_edit_on == 1){
    	$audio_comment_edit_on = '<a href="edit/editcomments.php?type=3&id='.$indexer_id.'" title="Edit" rel="gb_page_center[800, 400]">
        <img src="'.$base_url.'/themes/'.$user_theme.'/images/icons/edit_small.png" alt="'.$lang_edit.'" width="14" height="14" border="0" />
         '.$lang_edit.'</a>';
    	}else{
    	$audio_comment_edit_on = '';
    	}

    	//Delet link
    	if($audio_comment_delete_on ==1){
    	$audio_comment_delete_on = '<a href="edit/delete.php?type=8&id='.$indexer_id.'" title="Delete" rel="gb_page_center[800, 300]">
        <img src="'.$base_url.'/themes/'.$user_theme.'/images/icons/delete_small.png" alt="'.$lang_delete_general.'" width="14" height="14"
		border="0" /> '.$lang_delete_general.'</a>';
    	}else{
    	$audio_comment_delete_on = '';
    	}

    	//_______________________________________________________________________________________________


	// reset html block to nothing
	$comment_actions_block = "";

	////////////////////////////////////////////////////////////////////////////////////
	// Now query audiocomments_replys to find any REPLIES

	$each_reply = array();

	$sql_replies = "SELECT * FROM audiocomments_replys WHERE audiocomment_id = $indexer_id ORDER BY indexer DESC LIMIT $limit_value, $limit";

	$replies = @mysql_query($sql_replies);
	$replies_count = mysql_num_rows($replies);
	$reply_wrap = array();
	$by_username = array();
	$reply_date = array();

	$reply_block = "";

	while ($result_replies = @mysql_fetch_array($replies)) {
		$reply_text 	= $result_replies['comment_reply'];
		//$reply_text	= htmlspecialchars_decode($reply_text);
		$reply_text 	= to_bbc_code($reply_text);
		$reply_wrap 	= wordwrap($reply_text, $wrap_limit, " ", true);
		$by_username 	= $result_replies['by_username'];
		$db_date		= $result_replies['todays_date'];
		$reply_date		= date($config["date_format"], strtotime($db_date));
		$reply_id		= $result_replies['indexer'];

        //______________________________________________________________________________________________
		//______PERMISSIONS CHECK COMMENTS______________________________________________________________
    	$usercheck = new LoadPermissions('', $reply_id, 'audio_comments_replies');
    	$usercheck->CheckPermissions();
    	$reply_edit_on = $usercheck->ReturnVars('edit_on');
    	$reply_delete_on = $usercheck->ReturnVars('delete_on');

		if ( $reply_delete_on == 1 ) {
			$delete_reply_on = "<div style=\"margin-right:12px; float:right;\">
			  <a href=\"edit/delete.php?type=15&id=$reply_id\" title=\"Edit Reply\" rel=\"gb_page_center[800, 400]\">
              <img src=\"$base_url/themes/$user_theme/images/icons/delete_small.png\" alt=\"$lang_delete_general\" width=\"14\" height=\"14\" border=\"0\" />
			  $lang_delete_general</a>
			</div>\n";
		} else {
			$delete_reply_on = "<div style=\"margin-right:12px; float:right;\">&nbsp;</div>\n";
		}

		if ( $reply_edit_on == 1 ) {
			$edit_reply_on = "<div style=\"margin-right:12px; float:right;\">
			  <a href=\"edit/editcomments.php?type=4&id=$reply_id\" title=\"$lang_edit\" rel=\"gb_page_center[800, 400]\">
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
		    <div style=\"margin-left:12px; float:left;\">Reply By:&nbsp;<b>$by_username</b></div>
		    <div style=\"margin-left:42px; float:left;\"><b>Date:&nbsp;</b>$reply_date</div>\n";
		  //</div>\n";
		//</div>\n";

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

	$each_comment = array('by_id'				=>$result1['by_id'],
				    'indexer'			=>$result1['indexer'],
				    'comments'			=>$wrap,
				    'todays_date'			=>$result1['todays_date'],
				    'by_username'			=>$result1['by_username'],
				    'poster_picture'		=>$poster_picture,
				    'rating'			=>$result1['updated_rating'],
				    'audio_comment_edit'			=>$audio_comment_edit_on,
				    'audio_comment_delete'			=>$audio_comment_delete_on,
				    'drop_block'			=>$drop_block,
				    'reply_block'			=>$reply_block);

	$result_search3[] = $each_comment;
}

if ($user_id == "")
	$login_comment_post = $config['login_first'];
else
	$login_comment_post = '';

if ($text_comments == 0) $text_alert_msg = $config['audio_comment_msg'];

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
	$online_status = '<img src="images/icon_online.gif" alt="status" width="8" height="8" border="0" align="absmiddle" />';
} else {
	$online_status = '<img src="images/icon_offline.gif" alt="status" width="8" height="8" border="0" align="absmiddle" />';
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

$template 		= "themes/$user_theme/templates/main_1.htm";
$inner_template1 	= "themes/$user_theme/templates/inner_audio_play.htm";
$TBS 			= new clsTinyButStrong;
$TBS->NoErr 	= true;
$TBS->LoadTemplate("$template");

$TBS->MergeBlock('blk1', $result_search);
$TBS->MergeBlock('blk2', $result_search2);
$TBS->MergeBlock('blk3', $result_search3);
$TBS->Render 	= TBS_OUTPUT;
$TBS->Show();

@mysql_close();
die();

?>

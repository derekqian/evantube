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
include_once('classes/permissions.php');

// load required javascripts used in main_1.htm

$rating_update	= 1;
$ahah			= 1;
$thickbox		= 1;
$greybox		= 1;
$load_ajax 		= 1;

$referer 		= $_SERVER['HTTP_REFERER'];

$wrap_limit		= 80;
$blog_id 		= (int) mysql_real_escape_string( $_GET['id'] );
$show_v 		= 0;
$tag_cloud 		= make_tag_cloud('blogs');
$tag_cloud_block	= $tag_cloud[1];
$load_ajax 		= 1;

$small_block_background = $config['color_tellafriend'];

// this is per each template from config.inc.php advanced config settings
$page_display_small_width = $config['read_blog_member_thumb_width']; // 65

// declare media type for ratings
$media = 'blogs';

if ($user_id == "") {
	$ajax_rating = pullRating($media,$blog_id,false,true,true,'novote');
}else{
	$ajax_rating = pullRating($media,$blog_id,true,false,true,$user_id);
}

include_once ('includes/enabled_features.php');

// get blog categories
$all_categories	= array();
$sql			= "SELECT * FROM blog_categories WHERE has_blogs = 'yes' ORDER BY category_id ASC";
$query		= @mysql_query($sql);

while ( $result = @mysql_fetch_array($query) ) {

	$category_id	= $result['category_id'];
    	$sql_cnt 		= "SELECT indexer FROM blogs WHERE category_id = '$category_id' AND approved = 'yes' AND public_private = 'public' ORDER BY indexer DESC";
    	$query_cnt 		= @mysql_query($sql_cnt);
    	$count_blogs	= mysql_num_rows($query_cnt);
    	$blog_count		= array('category_count' => $count_blogs);
	$new_array		= @array_merge($result, $blog_count);
	$all_categories[] = $new_array;
}


////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Get blog details and blog story/article

$result	= array();
$sql		= "SELECT * FROM blogs WHERE indexer = $blog_id $sql_public_private AND approved ='yes'";
$query 	= @mysql_query($sql);
$blog_query	= mysql_num_rows($query);

if ($blog_query == 0 ) {

	$codes = 111;

	$error_height = '430px;';
	$message_2 = $config['error_26']. ' - ' .$config['error_11'];
}

while ( $result = @mysql_fetch_array($query) ) {

	//______________________________________________________________________________________________
	//______PERMISSIONS CHECK BLOGS________________________________________________________________
    	$usercheck 		= new LoadPermissions('',$blog_id,'blogs');
    	$usercheck->CheckPermissions();
    	$edit_blog 		= $usercheck->ReturnVars('edit_on');
    	$delete_blog 	= $usercheck->ReturnVars('delete_on');
    	//_______________________________________________________________________________________________

	$number_of_views	= mysql_real_escape_string($result['number_of_views']);
	$title 		= $result['title'];
	$poster_id		= $result['user_id'];
	$a1_sql		= "SELECT * FROM pictures WHERE user_id = $poster_id";
	$a1_result		= @mysql_query($a1_sql);

	$page_title		= $title;

	if (@mysql_num_rows($a1_result) != 0) {

		$a1_result = @mysql_fetch_array($a1_result);
		$a1_existing_file = $a1_result['file_name'];
		$a1_approved = $a1_result['approved'];

		if ($a1_approved == 'yes') {
			$poster_picture = "pictures/$a1_existing_file";
		} else {
			$poster_picture = "themes/$user_theme/images/placeholder.gif";
		}
	} else {
		$poster_picture = "themes/$user_theme/images/placeholder.gif";
	}

	// get members online status
	$sql = "SELECT * FROM online WHERE logged_in_id = $poster_id";
	$my_online_status = @mysql_num_rows(mysql_query($sql));

	if ($my_online_status > 0) {
		$online_status = '<img src="images/icon_online.gif" alt="status" title="'.$lang_online.'" border="0" width="8" height="8" style="padding-bottom: 1px; vertical-align: middle;" />';

	} else {
		$online_status = '<img src="images/icon_offline.gif" alt="status" title="'.$lang_offline.'" border="0" width="8" height="8" style="padding-bottom: 1px; vertical-align: middle;" />';
	}

	$display_thumbs		= show_thumb( $poster_picture, $page_display_small_width );
	$new_smallwidth		= $display_thumbs[0];
	$new_smallheight		= $display_thumbs[1];

	$blog_story		= $result['blog_story'];
	$blog_owner_name 	= $result['blog_owner'];

	//$wrap 	= wordwrap($blog_story, $wrap_limit, " ", true);
	//$wrap 	= htmlspecialchars_decode($wrap);

	$wrap = htmlspecialchars_decode($blog_story);


	$drop_block = "<div id=\"drop_block_$blog_id\" style=\"margin-left:1px; float:left;\"></div>";

	// we need to query blogcomments_replys to find any REPLIES

	$each_reply		= array();
	$sql_replies 	= "SELECT * FROM blog_replys WHERE blog_id = $blog_id ORDER BY indexer DESC"; // LIMIT $limit_value, $limit";
	$replies 		= @mysql_query($sql_replies);
	$replies_count	= mysql_num_rows($replies);
	$reply_wrap 	= array();
	$by_username	= array();
	$reply_date		= array();

	$reply_block 	= "";

	while ($result_replies 	= @mysql_fetch_array($replies)) {

		$reply_text 	= $result_replies['reply_body'];
		$reply_wrap 	= wordwrap($reply_text, $wrap_limit, " ", true);
		$by_username 	= $result_replies['by_username'];
		$db_date		= $result_replies['todays_date'];
		$reply_date		= date($config["date_format"], strtotime($db_date));
		$reply_id		= $result_replies['indexer'];

        	//______________________________________________________________________________________________
		//______PERMISSIONS CHECK COMMENTS______________________________________________________________
    		$usercheck = new LoadPermissions('', $reply_id, 'blog_comments');
    		$usercheck->CheckPermissions();
    		$reply_edit_on = $usercheck->ReturnVars('edit_on');
    		$reply_delete_on = $usercheck->ReturnVars('delete_on');

		if ( $reply_delete_on == 1 ) {
			$delete_reply_on = "<div style=\"margin-right:12px; float:right;\">
			  <a href=\"edit/delete.php?type=5&id=$reply_id\" title=\"$lang_delete_general\" rel=\"gb_page_center[800, 400]\">
              <img src=\"$base_url/themes/$user_theme/images/icons/delete_small.png\" alt=\"$lang_delete_general\" width=\"14\" height=\"14\" border=\"0\" />
			  $lang_delete_general</a>
			</div>\n";
		} else {
			$delete_reply_on = "<div style=\"margin-right:12px; float:right;\">&nbsp;</div>\n";;
		}

		if ( $reply_edit_on == 1 ) {
			$edit_reply_on = "<div style=\"margin-right:12px; float:right;\">
			  <a href=\"edit/editcomments.php?type=5&id=$reply_id\" title=\"$lang_edit\" rel=\"gb_page_center[800, 400]\">
              <img src=\"$base_url/themes/$user_theme/images/icons/edit_small.png\" alt=\"$lang_delete_general\" width=\"14\" height=\"14\" border=\"0\" />
			  $lang_edit</a>
			</div>\n";
		} else {
			$edit_reply_on = "<div style=\"margin-right:12px; float:right;\">&nbsp;</div>\n";
		}
        	//_______________________________________________________________________________________________________________


		$reply_block .= "<hr style=\"border-top: 1px dashed #C0C0C0; border-bottom: 0px\" />\n
					<div style=\"width: 100%; background-color: $small_block_background;\" id=\"reply_block\">\n
					  <div>\n
					    <div style=\"margin-left:12px; float:left;\">$lang_reply_by:&nbsp;<b>$by_username</b></div>\n
					    <div style=\"margin-right:22px; float:right;\"><b>$lang_date:&nbsp;</b>$reply_date</div>\n
					  </div>\n
					<div>\n";

		$reply_block .= $delete_reply_on . $edit_reply_on;

		$reply_block .="</div>\n
				    <br />\n
				    <br />\n
				    <div style=\"margin-left:32px; float:left;\">$reply_wrap</div>\n
				    <br />\n
				    <br />\n
				    </div>\n";
	}


		$each_comment = array('indexer'		=>$result['indexer'],
					    'by_username'		=>$result['blog_owner'],
					    'by_id'			=>$result['user_id'],
				          'viewtime'		=>$result['viewtime'],
				    	    'title'			=>$result['title'],
				    	    'description'		=>$result['description'],
				    	    'tags'			=>$result['tags'],
				    	    'category'		=>$result['category'],
				    	    'comments'		=>$wrap,
				          'todays_date'		=>$result['date_created'],
				          'poster_picture'	=>$poster_picture,
				          'thumb_new_width' 	=>$new_smallwidth,
				          'thumb_new_height'	=>$new_smallheight,
				          'rating'		=>$result['updated_rating'],
				          'drop_block'		=>$drop_block,
				          'reply_block'		=>$reply_block);

      	$result_search3[] = $each_comment;
}

$page_title	= ucfirst($title) . ' Blog';

// START SECURITY BLOCK
$usercheck = new LoadPermissions('', $blog_id, 'blogs');
$usercheck->CheckPermissions();

$edit_on = $usercheck->ReturnVars('edit_on');
$delete_on = $usercheck->ReturnVars('delete_on');
// END SECURITY BLOCK

//print_r($usercheck);


//update counter
$views_counter = $number_of_views + 1;
$sql = "UPDATE blogs SET number_of_views = $views_counter WHERE indexer = $blog_id";
$query = @mysql_query($sql);

//record view time in views_tracker table
@media_views_tracker($blog_id, 'blogs');

// update last read time
$sql = "UPDATE blogs SET viewtime = NOW() WHERE indexer = $blog_id";
$query = @mysql_query($sql);


// ajax pagination reuseable
$page = $_GET['page'];
$page = mysql_real_escape_string($page);

if(empty($page)) {
	$page = 1;
}

$limit 				= $config["comment_page_limits"];
$limit_value 			= $page * $limit - ($limit);
$totalrows 				= $text_comments;
$config['ajax_page_previous'] = $config["pagination_previous"];
$config['ajax_page_next'] 	= $config["pagination_next"];

// PREVIOUS
if($page != 1){
	$pageprev = ($page - 1);
	$ajax_page_previous = $config['ajax_page_previous'];

}else{
	// no previous link
	$ajax_page_previous = ''; //$config['ajax_page_previous'];
}

// NEXT
$next_page = $totalrows - ($limit * $page);

if(($totalrows - ($limit * $page)) > 0){
	$pagenext = ($page + 1);
	$ajax_page_next = "&nbsp;&nbsp;" . $config['ajax_page_next'];
}else{
	// no link
	$ajax_page_next = '';
}

$see_limit = ($limit * $page);
if ($see_limit > $totalrows) {
	$see_limit = $totalrows;
}

if (empty($result)) {
    $show_c = 1;
}
else {
    $show_c = 2;
}


//show any errors/notifications
if ($codes == "") $codes = $codes_internal;

$error_code = errorcodes($codes);

if (!empty($error_code)) {

    	$message_type 	= $error_code['error_type'];
    	$error_message 	= $error_code['error_message'];
    	$error_height 	= '230px;';

    	// display error

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

// get members overall site ranking for badge display
$member_id = $poster_id;
$member_badge	= member_site_ranking($member_id);

$div_style_height		= ($limit + $replies_count) * 160;

$blog_url 			= $base_url . "/read_blog/" . $blog_id . "/" . $result[title_seo];

$template 			= "themes/$user_theme/templates/main_1.htm";
$inner_template1 		= "themes/$user_theme/templates/inner_read_blog.htm";

$TBS 				= new clsTinyButStrong;
$TBS->NoErr 		= true;

$TBS->LoadTemplate("$template");

$TBS->MergeBlock('blk1', $all_categories);
$TBS->MergeBlock('blk3', $result_search3);

$TBS->Render		= TBS_OUTPUT;

$TBS->show();

die();

?>
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

$tag_cloud 		= make_tag_cloud('videos');
$tag_cloud_block	= $tag_cloud[1];								// $tag_cloud[1]= small $tag_cloud[0]=large

$codes_internal	= '';
$codes		= '';
$type			= '';
$sub			= '';
$codes 		= '';

// was orginal channel get code
$channel 		= mysql_real_escape_string( $_GET['cid'] );

$channel 		= $_SERVER['QUERY_STRING'];
$channel 		= mysql_real_escape_string($channel);

$channel 		= str_replace('cid=', "", $channel);
$channel 		= str_replace('%20', " ", $channel);

// for pagination we must strip &page from server query string
$chan_pages = strpos($channel, '&page=');

if ($chan_pages) $channel = substr($channel, 0, strpos($channel, '&page='));

// get the channel id / indexer so query channels table by the text name GET
$id_sql		= "SELECT channel_id FROM channels WHERE channel_name_seo = '$channel' LIMIT 1";
$id_query 		= @mysql_query($id_sql);
$row 			= mysql_fetch_array($id_query);
$id_channel_id	= $row['channel_id'];
$limit 		= (int) $config['see_more_limits'];

if ( isset($_GET['type']) ) $type 	= mysql_real_escape_string($_GET['type']);
if ( isset($_GET['sub'])  ) $sub 	= (int) mysql_real_escape_string($_GET['sub']);
if ( isset($_GET['code']) ) $codes	= (int) mysql_real_escape_string($_GET['code']);


if ( $sub > 0 ) {

	//get all categories
	$all_categories 	= array();
	$sql 			= "SELECT channel_name, channel_name_seo, channel_id FROM channels ORDER BY channel_name ASC";
	$query 		= @mysql_query($sql);

	while ($result_c = @mysql_fetch_array($query)) {

		$channel_id	= $result_c['channel_id'];
    		$sql0 	= "SELECT indexer FROM videos WHERE channel_id = '$channel_id' AND approved = 'yes' $sql_public_private ORDER BY indexer DESC";
    		$query0 	= @mysql_query($sql0);
  		$result0 	= @mysql_fetch_array($query0);
  		$count_vids	= @mysql_num_rows($query0);

    		// dont push array if empty or is empty in private views
		if ( $count_vids == '' ) {
	    		$count_videos = 0;
    			$result_c = '';
	    	} else {
    			$vid_count 		= array('vid_count' => $count_vids);
    			$new_array 		= @array_merge($result_c, $vid_count);
    			$all_categories[] = $new_array;
    		}
    	}

	// get parent channel id
	$sql_p			= "SELECT parent_channel_id, sub_channel_name, sub_channel_name_seo FROM sub_channels WHERE sub_channel_id = '$sub'";
	$query_p 			= @mysql_query($sql_p);
	$row 				= mysql_fetch_array( $query_p );
	$parent_channel_id	= $row['parent_channel_id'];
	$sub_channel_name_seo	= $row['sub_channel_name_seo'];
	$sub_channel_name		= $row['sub_channel_name'];

	// query the seleted channel
	$sql1		= "SELECT channel_id, channel_name FROM channels WHERE channel_id = '$parent_channel_id'";
	$query1 	= @mysql_query($sql1);
	$all_subs	= '';
	while ( $result = @mysql_fetch_array($query1) ) {
		$channel_id 	= $result['channel_id'];

		// find any sub-categories
		$subs_sql	= "SELECT sub_channel_id, sub_channel_name, sub_channel_name_seo FROM sub_channels WHERE has_vids = 'yes' AND parent_channel_id = '$channel_id'";
		$subs_query	= @mysql_query($subs_sql);

		while ( $result_subs = @mysql_fetch_array($subs_query) ) {
			$all_subs[]		= $result_subs;
		}
	}


	$show_div = 1;

	if ( sizeof($all_subs) == "" ) {
		$show_div = 0;
	}

	if (@mysql_num_rows($query1) == 0){
		//@mysql_close();
		//error_redirect(121);
	}

	//get all sub_category videos
	$pagination 	= pagination("SELECT * FROM videos WHERE sub_channel_id = '$sub' AND approved='yes' $sql_public_private ORDER BY indexer DESC", $limit);
	$set_limit 		= $pagination[0]['set_limit'];
	$total_pages 	= $pagination[0]['total_pages'];
	$current_page 	= $pagination[0]['current_page'];
	$total_records	= $pagination[0]['total_records'];
	$next_page 		= $pagination[0]['next_page'];					// use in html navigation (src)
	$prev_page 		= $pagination[0]['prev_page'];					// use in html navigation (src)
	$nl 			= $pagination[0]['nl'];							// use in html navigation: next>>
	$pl 			= $pagination[0]['pl'];							// use in html navigation: <<previous

	$result_featured = array();
	$sql 			= "SELECT * FROM videos WHERE sub_channel_id = '$sub' AND approved='yes' $sql_public_private ORDER BY indexer DESC LIMIT $set_limit, $limit";
	$query 		= @mysql_query($sql);

	while ( $result1 = @mysql_fetch_array($query) ) {
		//get comments info
    		$video_id		= $result1['indexer'];
    		$sql2 		= "SELECT * FROM videocomments WHERE video_id = $video_id";
    		$query2 		= @mysql_query($sql2);
    		$comments_number 	= @mysql_num_rows($query2);
    		$comments_array 	= array('comments' => $comments_number);

		//get video rating
		$stars_array 	= array();
		$stars_array 	= stars_array($video_id);

	    	//merge comments array and video array
    		$result2		= @array_merge($result1,$comments_array, $stars_array);
	    	$result_featured[]= $result2;
	}

	//show "no videso" if none found
	if ( empty($result_featured) ) {
		$show_v = 1;

	} else {
		$show_v = 2;
	}

	//PAGINATION PLUS >> start
	$url 					= 'subcategory/'.$sub;
    	$additional_url_variable 	= '/'.$sub_channel_name_seo.'/';
	@include_once ($include_base . '/includes/pagination.inc.php');
	//PAGINATION PLUS >> end

	if ( $channel_name == "" ) {
		$channel_name = $sub_channel_name;
	}

// end sub categories

} else {

	//get all categories
	$all_categories 	= array();
	$sql 			= "SELECT channel_name, channel_name_seo, channel_id FROM channels ORDER BY channel_name ASC";
	$query 		= @mysql_query($sql);

	while ($result_c = @mysql_fetch_array($query)) {

		$channel_id	= $result_c['channel_id'];
    		$sql0 	= "SELECT indexer FROM videos WHERE channel_id = '$channel_id' AND approved = 'yes' $sql_public_private ORDER BY indexer DESC";
    		$query0 	= @mysql_query($sql0);
  		$result0 	= @mysql_fetch_array($query0);
  		$count_vids	= @mysql_num_rows($query0);

	    	// dont push array if empty or is empty in private views
		if ( $count_vids == '' ) {
	    		$count_videos = 0;
    			$result_c = '';
	    	} else {
    			$vid_count 		= array('vid_count' => $count_vids);
    			$new_array 		= @array_merge($result_c, $vid_count);
    			$all_categories[] = $new_array;
    		}
	}

	// query the seleted channel
	//$sql1		= "SELECT channel_id, channel_name FROM channels WHERE channel_name_seo = '$channel'";

	$sql1		= "SELECT * FROM channels WHERE channel_id = '$id_channel_id'";

	$query1 	= @mysql_query($sql1);
	$all_subs	= '';

	while ( $result = @mysql_fetch_array($query1) ) {
		$channel_id 	= $result['channel_id'];
		$channel_name	= $result['channel_name'];

		// find any sub-categories
		$subs_sql	= "SELECT sub_channel_id, sub_channel_name, sub_channel_name_seo FROM sub_channels WHERE has_vids = 'yes' AND parent_channel_id = '$channel_id'";
		$subs_query	= @mysql_query($subs_sql);

		while ( $result_subs = @mysql_fetch_array($subs_query) ) {
			$all_subs[]		= $result_subs;
		}
	}

	$show_div = 1;

	if ( sizeof($all_subs) == "" ) {
		$show_div = 0;
	}

	if (@mysql_num_rows($query1) == 0){
		@mysql_close();
		//error_redirect(121);
	}

	//get all category videos
	$pagination 	= pagination("SELECT * FROM videos WHERE channel_id = '$channel_id' AND approved='yes' $sql_public_private ORDER BY indexer DESC", $limit);
	$set_limit 		= $pagination[0]['set_limit'];
	$total_pages 	= $pagination[0]['total_pages'];
	$current_page 	= $pagination[0]['current_page'];
	$total_records	= $pagination[0]['total_records'];
	$next_page 		= $pagination[0]['next_page'];					// use in html navigation (src)
	$prev_page 		= $pagination[0]['prev_page'];					// use in html navigation (src)
	$nl 			= $pagination[0]['nl'];							// use in html navigation: next>>
	$pl 			= $pagination[0]['pl'];							// use in html navigation: <<previous

	$result_featured = array();

	$sql 			= "SELECT * FROM videos WHERE channel_id = '$channel_id' AND approved='yes' $sql_public_private ORDER BY indexer DESC LIMIT $set_limit, $limit";
	$query 		= @mysql_query($sql);

	while ( $result1 = @mysql_fetch_array($query) ) {

		//get comments info
    		$video_id		= $result1['indexer'];
    		$sql2 		= "SELECT * FROM videocomments WHERE video_id = $video_id";
    		$query2 		= @mysql_query($sql2);
    		$comments_number 	= @mysql_num_rows($query2);
    		$comments_array 	= array('comments' => $comments_number);

		//get video rating

		$stars_array 	= array();
		$stars_array 	= stars_array($video_id);

    		//merge comments array and video array
    		$result2		= @array_merge($result1,$comments_array, $stars_array);

    		$result_featured[]= $result2;
	}

	//show "no videso" if none found
	if ( empty($result_featured) ) {
		$show_v = 1;
	} else {
		$show_v = 2;
	}

	//PAGINATION PLUS >> start

	$url 					= 'category';
    	$additional_url_variable 	= '/'.$channel.'/';

	@include_once ($include_base . '/includes/pagination.inc.php');

	//PAGINATION PLUS >> end

	//show any errors/notifications
	if ( $codes == "" ) $codes = $codes_internal;

	$error_code = errorcodes($codes);

	if ( !empty($error_code) ) {
		$blk_notification	= $error_code['error_display'];
    		$message_type	= $error_code['error_type'];
    		$error_message	= $error_code['error_message'];
	}

	$channel = str_replace ('-', " ", $channel);

} // end if sub categories or categories

$page_title		= $lang_browse_videos . ' ' . $channel . ' ' . $lang_videos . ' ' . $lang_on . ' ' . $site_name;

$template		= "themes/$user_theme/templates/main_1.htm";
$inner_template1 	= "themes/$user_theme/templates/inner_category_home.htm";		//middle of page
$TBS 			= new clsTinyButStrong;
$TBS->NoErr 	= true;										// no more error message displayed.

$TBS->LoadTemplate("$template");

$TBS->MergeBlock('blkfeatured',$result_featured);
$TBS->MergeBlock('blk1', $all_categories);
$TBS->MergeBlock('blk2', $all_subs);

$TBS->Render 	= TBS_OUTPUT;
$TBS->Show();

@mysql_close();
die();


?>
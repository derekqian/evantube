<?php
error_reporting(0);
///////////////////////////////////////////////////////////////////////////////////////
// PHPmotion                                                http://www.phpmotion.com //
///////////////////////////////////////////////////////////////////////////////////////
// License: You are not to sell or distribute this software without permission       //
// Help and support please visit http://www.phpmotion.com                            //
// Copyright reserved                                                                //
///////////////////////////////////////////////////////////////////////////////////////

include_once ('classes/config.php');
include_once ('classes/sessions.php');

$default_album_pic = $config['default_album_pic'];

///////////////////////////////////////////////////////////////////////////////////////
// changed search server method to get to get rid of cache expired error

if( ( $_SERVER['REQUEST_METHOD'] != 'GET') ) dieNice();

$queryString = strtolower($_SERVER['QUERY_STRING']);
if (strstr($queryString,'%20union%20') OR strstr($queryString,'/*')) dieNice();

$referer	= mysql_real_escape_string( $_SERVER['HTTP_REFERER'] );
if ( $referer == '' ) dieNice();
if ( !ereg ($_SERVER['SERVER_NAME'], $referer) ) $flag_stop++;
if ( !ereg ($base_url, $referer) ) $flag_stop++;
if ( $flag_stop == 2 ) dieNice();

$keywords	= $_GET['keyword'];
$keywords	= str_replace('&#039;s', "", $keywords);

$debug_log_file = 'logs/search_log.txt';

if (@file_exists($debug_log_file)) {
	$fo = @fopen($debug_log_file, 'a');
     	@fwrite($fo, $keyword);
     	@fclose($fo);

} else {
     	$fo = @fopen($debug_log_file, 'w');
     	@fwrite($fo, $keyword);
     	@fclose($fo);
}

$keyword 	= mysql_real_escape_string($keywords);
$type 	= mysql_real_escape_string( $_GET['type'] );

if ($type == '') {
	$keyword	= mysql_real_escape_string($keywords);
    	$type		= mysql_real_escape_string($_GET['type']);
}

if ($keyword == '' | $type == '' ) {
	$type		= 'videos';
	$keyword 	= 'none';
}



///////////////////////////////////////////////////////////////////////////////////////


///////////////////////////////////////////////////////////////////////////////
// if we finish or use multiple search words - e.g. => simpsons funny cars
//
// this function would return a string of cleaned words
//
// $get_keywords 	= clean_spaces($keyword, false);

// but we would then need to query each keyword
// in a sql loop and merge any results \\??//
//
///////////////////////////////////////////////////////////////////////////////

$get_type		= $type;

switch ( $get_type ) {

	case 'videos';
		$type_videos	= 1;
		$row_id		= 'video_id';
		$media_comments	= 'videocomments';
		$type_query_rows 	= 'indexer, video_id, title, title_seo, date_uploaded, user_id, video_length, approved, public_private, description, tags, number_of_views';
	break;

	case 'audios';
		$type_audios 	= 2;
		$row_id		= 'audio_id';
		$media_comments	= 'audiocomments';
		$type_query_rows 	= 'indexer, audio_id, title, title_seo, date_uploaded, user_id, audio_length, approved, public_private, description, tags, number_of_views';
	break;

	case 'images';
		$type_images	= 3;
		$row_id		= 'image_id';
		$media_comments	= 'imagecomments';
		$type_query_rows 	= 'indexer, image_id, gallery_id, title, title_seo, date_uploaded, user_id, approved, public_private, description, tags, number_of_views';
	break;

	case 'groups';
		//$type_groups	= 4;
		$row_id		= 'group_id';
		$media_comments	= 'groupcomments';
		$type_query_rows 	= 'group_name, group_name_seo, public_private, todays_date, group_description, indexer, featured';

	break;

	case 'blogs';
		$type_blogs		= 5;
		$row_id 		= 'blog_id';
		$media_comments	= 'blog_replys';
		$type_query_rows 	= 'indexer, blog_owner, user_id, title, title_seo, description, tags, category, date_created, approved, number_of_views';
	break;
}

$query_get_type	= $get_type;

if ( $query_get_type == 'groups' ) {

	$query_get_type = 'group_profile';

	$page_guery		= "SELECT indexer FROM $query_get_type WHERE (group_name like '%$keyword%' or group_description like '%$keyword%')";
	$search_query	= "SELECT $type_query_rows FROM $query_get_type WHERE (group_name like '%$keyword%' or group_description like '%$keyword%') LIMIT "; // $set_limit, $limit";

} else {
	$tag_cloud 		= make_tag_cloud( $get_type );
	$tag_cloud_block 	= $tag_cloud[1];

	$page_guery		= "SELECT indexer FROM $query_get_type WHERE approved = 'yes' AND public_private = 'public' and (title like '%$keyword%' or tags like '%$keyword%' or description like '%$keyword%')";
	$search_query	= "SELECT $type_query_rows FROM $query_get_type WHERE approved = 'yes' AND public_private = 'public' and (title like '%$keyword%' or tags like '%$keyword%' or description like '%$keyword%') LIMIT "; // $set_limit, $limit";
}

if ( $query_get_type == 'group_profile' ) {

    	$limit		= (int) mysql_real_escape_string( $config['search_page_limits'] );
    	$pagination 	= pagination($page_guery, $limit);
    	$set_limit		= $pagination[0]['set_limit'];
    	$total_pages	= $pagination[0]['total_pages'];
    	$current_page 	= $pagination[0]['current_page'];
    	$total_records 	= $pagination[0]['total_records'];
    	$next_page 		= $pagination[0]['next_page'];
    	$prev_page 		= $pagination[0]['prev_page'];
    	$nl 			= $pagination[0]['nl'];
    	$pl 			= $pagination[0]['pl'];

    	$results_of 	= $current_page * $limit;
    	$results_show 	= $set_limit + 1;

    	if ( $results_of > $total_records )	$results_of = $total_records;
    	if ( $set_limit == 0 ) 			$results_show = 1;
    	if ( $total_records == 0 ) 		$results_show = 0;

 	//define($get_type, true);

 	// TODO //
 	/*
 	add group rating to search display
 	re-write the 3 queries below using 1 JOIN query
 	*/
 	// END //

	$sql			= $search_query . $set_limit .','. $limit;
	$query 		= @mysql_query($sql);

	while ($result = @mysql_fetch_array($query)) {

    		$group_id			= $result['indexer'];
    		$sql2 			= "SELECT indexer, video_id FROM group_videos WHERE group_id = $group_id AND video_status = 'active'";
        	$query2 			= @mysql_query($sql2);
        	$count_group_videos 	= @mysql_num_rows($query2);

        	//get group video thumbnail
        	if ($count_group_videos == 0) {

            	$video_picture	= 'default_no_group_video';
            	$group_video_id 	= 0;

        	} else {

        		$result2 			= @mysql_fetch_array($query2);
            	$group_video_id 		= mysql_real_escape_string($result2['video_id']);
            	$sql0 			= "SELECT indexer, video_id FROM videos WHERE indexer = $group_video_id";
            	$query0			= @mysql_query($sql0);
            	$result0 			= @mysql_fetch_array($query0);
            	$group_video_image	= $result0['video_id'];
            	$video_picture 		= $result0['video_id'];
            	$group_video_id 		= $result0['indexer'];
        	}

        	$video_array = array('video_picture'	=> $video_picture,
        				   'group_video_id' 	=> $group_video_id);

        	$sql3				= "SELECT indexer FROM group_membership WHERE group_id = $group_id";
        	$query3			= @mysql_query($sql3);
        	$count_group_members 	= @mysql_num_rows($query3);

        	$sql4 			= "SELECT member_username, member_id FROM group_membership WHERE group_id = $group_id AND group_admin = 'yes'";
        	$query4 			= @mysql_query($sql4);
        	$result4 			= @mysql_fetch_array($query4);
        	$admin_username		= $result4['member_username'];

        	$group_other_info 	= array('group_videos' 		=> $count_group_videos,
        					  	  'group_members' 	=> $count_group_members,
        					  	  'admin_username'	=> $admin_username);

        	//merge arrays
        	$group_array	= @array_merge($result, $group_other_info, $video_array);
        	$mygroups[] 	= $group_array;
    	}
}

if ( $type == 'videos' || $type == 'audios' || $type == 'images' || $type == 'blogs'  ) {

	$limit		= (int) mysql_real_escape_string( $config['search_page_limits'] );
    	$pagination 	= pagination($page_guery, $limit);
    	$set_limit		= $pagination[0]['set_limit'];
    	$total_pages	= $pagination[0]['total_pages'];
    	$current_page 	= $pagination[0]['current_page'];
    	$total_records 	= $pagination[0]['total_records'];
    	$next_page 		= $pagination[0]['next_page'];
    	$prev_page 		= $pagination[0]['prev_page'];
    	$nl 			= $pagination[0]['nl'];
    	$pl 			= $pagination[0]['pl'];

    	$result_search 	= array();
    	$sql			= $search_query . $set_limit .','. $limit;
    	$query 		= @mysql_query($sql);

    	$results_of 	= $current_page * $limit;
    	$results_show 	= $set_limit + 1;

    	if ( $results_of > $total_records )	$results_of = $total_records;
    	if ( $set_limit == 0 ) 			$results_show = 1;
    	if ( $total_records == 0 ) 		$results_show = 0;

 	define($get_type, true);

    	while ($result1 = @mysql_fetch_array($query)) {

    		$media_id		= mysql_real_escape_string($result1['indexer']);
    		$sql2 		= "SELECT indexer FROM $media_comments WHERE $row_id = $media_id";
       	$query2 		= @mysql_query($sql2);
        	$comments_number	= @mysql_num_rows($query2);
        	$comments_array 	= array('comments' => $comments_number);

        	$id			= $media_id;

        	if ( $get_type == 'videos' ) {

        		$stars_array = stars_array($media_id);
        	} else {

        		include ('stars_include.php');
        		$stars_array	= array('star1' => $star1,'star2' => $star2,'star3' => $star3, 'star4' => $star4,'star5' => $star5);
        	}

        	// we need dynamic media image query
        	$result_image	= $get_type;

        	switch ( $result_image ) {

        		case 'audios';
        			$pic_sql		= "SELECT album_picture FROM audio_albums WHERE album_id = '$media_id' LIMIT 1";
				$album_pic_result = @mysql_query($pic_sql);
				$row			= mysql_fetch_array( $album_pic_result );
				$album_pic_result = $row['album_picture'];
				if ( $album_pic_result != "" ) {
					$album_pic = 'addons/audio/images/album_images/' . $album_pic_result;
				} else {
					$album_pic = 'addons/audio/images/' . $default_album_pic;
				}
				$result_image_array	= array('media_image' => $album_pic);
			break;

			case 'images';
        			$full_view		= $result1['image_id'];
        			$full_url		= $config['site_base_url'] . '/addons/albums/images/'.$full_view;
				$result_image_array	= array('media_image' => $full_url);
			break;

			case 'blogs';

				$blog_owner	= $result1['user_id'];
				$a1_sql	= "SELECT file_name, approved FROM pictures WHERE user_id = $blog_owner";
				$a1_result	= @mysql_query($a1_sql);

				if (@mysql_num_rows($a1_result) != 0) {
					$a1_result = @mysql_fetch_array($a1_result);
					$a1_existing_file = $a1_result['file_name'];
					$a1_approved = $a1_result['approved'];

					if ($a1_approved == "yes") {
						$poster_picture = $config['site_base_url'] . '/pictures/' . $a1_existing_file;
					} else {
						$poster_picture = $config['site_base_url'] . "/themes/$user_theme/images/placeholder.gif";
					}
				} else {
					$poster_picture = $config['site_base_url'] . "/themes/$user_theme/images/placeholder.gif";
				}

				$result_image_array	= array('media_image' => $poster_picture);
			break;

		} // end switch media images

		//merge arrays

     		if ( sizeof($result_image_array) > 0 )
     			$result2	= @array_merge($result1, $comments_array, $stars_array, $result_image_array);
        	else
        		$result2	= @array_merge($result1, $comments_array, $stars_array);

        	$result_search[] 	= $result2;
    	}

}

//PAGINATION PLUS
$url = 'search.php';
$additional_url_variable = "?keyword=$keyword&type=$type&page=";
include_once('includes/pagination.inc.php');
//PAGINATION PLUS >> end

// checking for any error codes
$codes = $_GET['code'];
$error_code = errorcodes($codes);

if (!empty($error_code)) {
	$blk_notification = $error_code['error_display'];
     	$message_type = $error_code['error_type'];
     	$error_message =$error_code['error_message'];
}

// create dynamic template words
$url_link		= $get_type;

if ( $url_link == 'images' ) $url_link = 'albums';
if ( $query_get_type == 'group_profile' ) {
	if ( $results_show == 0 ) $type_groups = '0'; else $type_groups = 4;
}

$get_type 		= ucwords($get_type);
$get_type_word	= substr($get_type, 0, -1);
$page_title		= $config['site_name'] . ' ' . $get_type_word . ' ' . $lang_search . ' ' . $lang_results . ' ' . $lang_for . ' ' . $keyword;
$template		= "themes/$user_theme/templates/main_1.htm";
$inner_template1 	= "themes/$user_theme/templates/search_results.htm";
$TBS			= new clsTinyButStrong;
$TBS->NoErr 	= true;

$TBS->LoadTemplate("$template");

if ( $query_get_type == 'group_profile' ) $TBS->MergeBlock('blkfeatured', $mygroups); else $TBS->MergeBlock('blkfeatured', $result_search);

$TBS->Render = TBS_OUTPUT;
$TBS->Show();
@mysql_close();
die();

function dieNice() {
	header("Location: index.php");
	die();
}




?>
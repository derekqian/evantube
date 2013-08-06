<?php

///////////////////////////////////////////////////////////////////////////////////////
// PHPmotion                                                http://www.phpmotion.com //
///////////////////////////////////////////////////////////////////////////////////////
// License: You are not to sell or distribute this software without permission       //
// Help and support please visit http://www.phpmotion.com                            //
// Copyright reserved                                                                //
///////////////////////////////////////////////////////////////////////////////////////

//_____new setup checks _____
if(!is_file(dirname(__FILE__) . '/classes/mysql.inc.php')) {

    //get page url
    function pageUrl() {
        $url = 'http';
        if($_SERVER["HTTPS"] == "on") {
            $url .= "s";
        }
        $url .= "://";
        if($_SERVER["SERVER_PORT"] != "80") {
            $url .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
        } else {
            $url .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
        }
        return $url;
    }

    $url = pageUrl();
    if(substr($url,-1) == '/') {
        $url = substr($url,0,-1);
    }
    @header("Location: $url/setup");
} else {


include_once ('classes/config.php');
include_once ('classes/sessions.php');
include_once ('includes/news.inc.php');
include_once ('online.php');
include_once ('popular.php');

// load required javascripts used in main_1.htm
$swfobject 			= 1;
$ahah				= 1;
$codes_internal		= '';
$codes			= '';
$page_title			= $config['site_name'];
$tag_cloud 			= make_tag_cloud('videos');
$tag_cloud_block		= $tag_cloud[0];
$proceed 			= true;
$enable_promoted 		= true;
$limit			= $config['see_more_limits'];
$featured_display 	= $config['admin_order_by'];
$default_album_pic	= $config['default_album_pic'];
$enabled_stats		= $config['enabled_features_stats'];
$tpl_ajax_height		= 166;
$ajax_mutiplier		= $limit / 4; 						//default template has 4 views accross the row - so limit = 8 / 4 = 2 rows
$ajax_height		= $tpl_ajax_height * $ajax_mutiplier;
$show_vid_channels	= $config['show_home_page_categories'];
$theme_thumbnail_width	= $config['theme_thumbnail_width'];
$theme_thumbnail_height	= $config['theme_thumbnail_height'];
$auto_play_index 		= $config['auto_play_index'];
$show_stats			= $config["enabled_features_stats"];

$referer 			= $_SERVER[HTTP_REFERER];
if ( !ereg ($base_url, $referer) ) $referer = $base_url;

if ( isset($_GET['code']) ) $codes = (int) mysql_real_escape_string($_GET['code']);



// start -----------------------//

if ( $enable_promoted == true ) {
	// Get PROMOTED videos
	$recent = array();
	$sql = "SELECT indexer, video_id, title, title_seo, date_uploaded FROM videos WHERE promoted='yes' AND public_private = 'public' AND approved='yes' ORDER BY RAND() DESC LIMIT 4";
	$query = @mysql_query($sql);

	while ($result			= @mysql_fetch_array($query))
	{
		$db_date			= $result['date_uploaded'];
		$change_date 		= dateTimeDiff($db_date);
		$result['date_uploaded'] 	= $change_date;
		$recent[] 			= $result;
	}
} else {
	// Get RECENT videos
	$recent = array();
	$sql = "SELECT indexer, video_id, title, title_seo, date_uploaded FROM videos WHERE approved='yes' AND public_private = 'public' ORDER BY indexer DESC LIMIT 4";
	$query = @mysql_query($sql);

	while ($result			= mysql_fetch_array($query))
	{
		$db_date			= $result['date_uploaded'];
		$change_date		= dateTimeDiff($db_date);
		$result['date_uploaded'] 	= $change_date;
		$recent[]			= $result;
	}
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// start Browse Videos

$browse_videos = array();

if ( $featured_display == 'random' )
	$sql = "SELECT indexer, video_id, title, title_seo, date_uploaded, user_id, video_length, featured, approved, public_private FROM videos WHERE featured = 'yes' AND approved='yes' AND public_private = 'public' ORDER BY RAND() DESC LIMIT $limit";
else
	$sql = "SELECT indexer, video_id, title, title_seo, date_uploaded, user_id, video_length, featured, approved, public_private FROM videos WHERE featured = 'yes' AND approved='yes' AND public_private = 'public' ORDER BY indexer DESC LIMIT $limit";

$query = @mysql_query($sql);

while ($result_videos = @mysql_fetch_array($query)) {

	$change_date			= dateTimeDiff($result_videos['date_uploaded']);
	$result_videos['date_uploaded'] = $change_date;
	$uploaded_by			= mysql_real_escape_string($result_videos['user_id']);
	$video_length			= mysql_real_escape_string($result_videos['video_length']);
	$video_id				= mysql_real_escape_string($result_videos['indexer']);

    	// get comments inforation IF NEEDED IN TEMPLATE
    	$sql2 				= "SELECT indexer FROM videocomments WHERE video_id = $video_id";
    	$query2 				= @mysql_query($sql2);
    	$comments_number 			= @mysql_num_rows($query2);
    	$comments_array 			= array('comments' => $comments_number);

    	// get video rating stars
    	$stars_array 			= array();
    	$stars_array 			= stars_array($video_id);

      // get video uploaded by username
      $sql_user 				= "SELECT user_name FROM member_profile WHERE user_id = '$uploaded_by'";
      $uploaded_by			= @mysql_query($sql_user);
      $by_result				= @mysql_fetch_array($uploaded_by);
	$uploaded_by_username		= $by_result['user_name'];
	$uploaded_by_array		= array('uploaded_by' => $uploaded_by_username);


      // strip off the the video duration hours if null => 00:00:00
      $video_length_hours		= substr($video_length, 0, 2);
	if ( $video_length_hours == '00' )
	$result_videos['video_length']= substr($video_length, 3, 5);

      // merge comments array and video array
      $result_all_videos		= @array_merge($result_videos, $comments_array, $stars_array, $uploaded_by_array);
    	$browse_videos[]			= $result_all_videos;
}

// end featured videos
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//get featured audios

$browse_audios = array();

if ( $featured_display == 'random' )
	$sql = "SELECT indexer, audio_id, album_id, title, title_seo, date_uploaded, audio_length, public_private, approved, user_id, featured FROM audios WHERE featured = 'yes' AND approved='yes' AND public_private = 'public' ORDER BY RAND() DESC LIMIT $limit";
else
	$sql = "SELECT indexer, audio_id, album_id, title, title_seo, date_uploaded, audio_length, public_private, approved, user_id, featured FROM audios WHERE featured = 'yes' AND approved='yes' AND public_private = 'public' ORDER BY indexer DESC LIMIT $limit";

$query = @mysql_query($sql);

define('audios', true);

while ($result_audios = @mysql_fetch_array($query)) {

	$audio_indexer			= (int) mysql_real_escape_string($result_audios['indexer']);
	$change_date			= dateTimeDiff($result_audios['date_uploaded']);
	$result_audios['date_uploaded'] = $change_date;
	$uploaded_by			= mysql_real_escape_string($result_audios['user_id']);
	$audio_length			= mysql_real_escape_string($result_audios['audio_length']);

	// lets strip off the the video duration hours if null => 00:00:00
	$audio_length_hours = substr($audio_length, 0, 2);
	if ( $audio_length_hours == '00' )
		$result_audios['audio_length'] = substr($audio_length, 3, 5);

	// get album picture if any
	$album_id		= (int) mysql_real_escape_string($result_audios['album_id']);
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

	// get rating stars
      $id = $audio_indexer;
      include ('stars_include.php');
      $stars_array = array('star1' => $star1,'star2' => $star2,'star3' => $star3, 'star4' => $star4,'star5' => $star5);

      // get uploaded by username
      $sql_user 			= "SELECT user_name FROM member_profile WHERE user_id = '$uploaded_by'";
      $uploaded_by		= @mysql_query($sql_user);
      $by_result			= @mysql_fetch_array($uploaded_by);
	$uploaded_by_username	= $by_result['user_name'];
	$uploaded_by_array	= array('uploaded_by' => $uploaded_by_username);

      //merge comments array and video array
    	$result_all_audios	= @array_merge($result_audios, $album_pic_array, $stars_array, $uploaded_by_array);
    	$browse_audios[]		= $result_all_audios;
}
// end featured audios
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//get featured blogs

$browse_blogs	= array();
$blog_limit		= 6;
if ( $featured_display == 'random' )

	$sql = "SELECT indexer, blog_owner, user_id, title, title_seo, description, date_created, featured, approved, public_private FROM blogs WHERE featured = 'yes' AND approved='yes' AND public_private = 'public' ORDER BY RAND() DESC LIMIT $blog_limit";
else

	$sql = "SELECT indexer, blog_owner, user_id, title, title_seo, description, date_created, featured, approved, public_private FROM blogs WHERE featured = 'yes' AND approved='yes' AND public_private = 'public' ORDER BY indexer DESC LIMIT $blog_limit";

$query = @mysql_query($sql);

define('blogs', true);

while ( $result_blogs = @mysql_fetch_array($query) ) {

	$blog_owner				= mysql_real_escape_string($result_blogs['blog_owner']);
	$user_id				= mysql_real_escape_string($result_blogs['user_id']);
     	$blog_id 				= mysql_real_escape_string($result_blogs['indexer']);
     	$change_date			= dateTimeDiff($result_blogs['date_created']);
 	$result_blogs[date_created]	= $change_date;

	// get blog member photo
	$a1_sql = "SELECT file_name, approved FROM pictures WHERE user_id = $user_id";
      $a1_result = @mysql_query($a1_sql);

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

	$poster_picture = array(poster_picture => $poster_picture);

	// get rating stars
      $id = $blog_id;
      include ('stars_include.php');
      $stars_array = array('star1' => $star1,'star2' => $star2,'star3' => $star3, 'star4' => $star4,'star5' => $star5);

      //merge arrays
    	$result_all_blogs		= @array_merge($result_blogs, $poster_picture, $stars_array);
    	$browse_blogs[]		= $result_all_blogs;
}

// end featured blogs
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//get featured images

$browse_images = array();

if ( $featured_display == 'random' )

	$sql = "SELECT indexer, image_id, gallery_id, user_id, title, title_seo, date_uploaded, public_private, approved, featured FROM images WHERE featured = 'yes' AND approved='yes' AND public_private = 'public' ORDER BY RAND() DESC LIMIT $limit";
else

	$sql = "SELECT indexer, image_id, gallery_id, user_id, title, title_seo, date_uploaded, public_private, approved, featured FROM images WHERE featured = 'yes' AND approved='yes' AND public_private = 'public' ORDER BY indexer DESC LIMIT $limit";

$query = @mysql_query($sql);

define('images', true);

while ( $result_images = @mysql_fetch_array($query) ) {

	$change_date			= dateTimeDiff($result_images['date_uploaded']);
	$result_images['date_uploaded'] = $change_date;
	$uploaded_by			= mysql_real_escape_string( $result_images['user_id'] );
	$image_indexer			= mysql_real_escape_string( $result_images['indexer'] );
	$image_id				= mysql_real_escape_string( $result_images['image_id'] );
	list($width, $height)		= getimagesize( "addons/albums/thumbs/$image_id" );

	// this is per each template??
	$page_display_small_width	= 120;
	$page_display_small_height	= 90;

	if ( $width < $page_display_small_width && $height < $page_display_small_height ) {
		$new_smallwidth	= $width;
     		$new_smallheight	= $height;
	}

	// this will resize our default thumbnail size e.g. 150x150 to rescale per template design var's
	if ( $width > $page_display_small_width ) {
		$large_img_ratio	= $width/$height;

		if ( $large_img_ratio > 1 ) {
           		$new_smallwidth	= $page_display_small_width;
            	$new_smallheight	= $page_display_small_width / $large_img_ratio;
            } else {
            	$new_smallheight	= $page_display_small_height;
            	$new_smallwidth	= $page_display_small_width * $large_img_ratio;
		}
	}

	if ( $height > $album_img_height ) {
		$large_img_ratio	= $width/$height;

           	if ( $large_img_ratio > 1 ) {
			$new_smallwidth	= $page_display_small_width;
			$new_smallheight	= $page_display_small_width / $large_img_ratio;
		} else {
			$new_smallheight	= $page_display_small_height;
			$new_smallwidth	= $page_display_small_width * $large_img_ratio;
		}
	}

	// added height check if image is an odd ratio/size
	if ( $new_smallheight > $page_display_small_height ) {
		$large_img_ratio	= $width/$height;

     		if ( $large_img_ratio > 1 ) {
			$new_smallheight	= $page_display_small_height;
			$new_smallwidth	= $page_display_small_height * $large_img_ratio;

		} else {
			$new_smallheight	= $page_display_small_height;
			$new_smallwidth	= $page_display_small_width * $large_img_ratio;
		}
	}

	$new_smallheight		= floor($new_smallheight);
	$new_smallwidth		= floor($new_smallwidth);
	$thumb_new_width_array	= array('thumb_new_width' => $new_smallwidth);
	$thumb_new_height_array	= array('thumb_new_height' => $new_smallheight);

    	// get comments inforation IF NEEDED IN TEMPLATE
    	$sql2 				= "SELECT indexer FROM imagecomments WHERE image_id = $image_indexer";
    	$query2 				= @mysql_query($sql2);

    	$comments_number 			= @mysql_num_rows($query2);
    	$comments_array 			= array('comments' => $comments_number);

    	// get rating stars
      $id = $image_indexer;
      include ('stars_include.php');
      $stars_array = array('star1' => $star1,'star2' => $star2,'star3' => $star3, 'star4' => $star4,'star5' => $star5);

      // get image uploaded by username
      $sql_user 				= "SELECT user_name FROM member_profile WHERE user_id = '$uploaded_by'";
      $uploaded_by			= @mysql_query($sql_user);
      $by_result				= @mysql_fetch_array($uploaded_by);
	$uploaded_by_username		= $by_result['user_name'];
	$uploaded_by_array		= array('uploaded_by' => $uploaded_by_username);

      // merge comments array and image array
      $result_all_images		= @array_merge($result_images, $comments_array, $stars_array, $uploaded_by_array, $thumb_new_width_array, $thumb_new_height_array);
    	$browse_images[]			= $result_all_images;
}

// end featured images

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// get random video to play if needed in any template designs

if ( $random_video == 'yes' ) {

	$sql			= "SELECT video_id FROM videos WHERE approved = 'yes' AND public_private = 'public' ORDER BY RAND() LIMIT 1";
	$query		= @mysql_query($sql);
	$result		= @mysql_fetch_array($query);
	$video_play		= $result['video_id'].'.flv';
	$video_thumb	= $result['video_id'].'.jpg';
}

// end random video
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

//get site stats

//////////////////////////////////////////////////// TOTALS /////////////////////////////////////////////////////////////

//////////////////////////////////////////////////// TOTALS NOT USED IN DEFAULT TEMPLATE/////////////////////////////////////////////////////////////

/*

//total videos
$sql			= "SELECT count(indexer) FROM videos WHERE approved='yes'";
$video_total 	= number_format(mysql_query($sql));

//total audios
$sql			= "SELECT count(indexer) FROM audios WHERE approved='yes'";
$audio_total 	= number_format(mysql_query($sql));

//total images
$sql 			= "SELECT count(indexer) FROM images";
$images_total 	= number_format(mysql_query($sql));

//total blogs
$sql 			= "SELECT count(indexer) FROM blogs";
$blog_total 	= number_format(mysql_query($sql));

//total comments
$sql			= "SELECT count(indexer) FROM videocomments";
$comments_total 	= number_format(mysql_query($sql));

*/
//////////////////////////////////////////////////// END TOTALS NOT USED IN DEFAULT TEMPLATE/////////////////////////////////////////////////////////////


//total members
$sql 			= "SELECT user_id FROM member_profile WHERE account_status = 'active'";
$members_total 	= number_format( @mysql_num_rows( mysql_query( $sql ) ) );


//////////////////////////////////////////////////// MEDIA ACTIONS /////////////////////////////////////////////////////////////

//played audio
$sql			= "SELECT sum(number_of_views) FROM audios";
$result 		= @mysql_query($sql);
$total_played	= number_format( @mysql_result($result, 0) );

//images_viewed
$sql			= "SELECT sum(number_of_views) FROM images";
$result 		= @mysql_query($sql);
$images_viewed	= number_format( @mysql_result($result, 0) );

//watched vids
$sql 			= "SELECT sum(number_of_views) FROM videos";
$result 		= @mysql_query($sql);
$total_watched 	= number_format( @mysql_result($result, 0) );


//////////////////////////////////////////////////// LAST MEMBER /////////////////////////////////////////////////////////////

//latest member
$sql			= "SELECT user_name, user_id FROM member_profile ORDER BY user_id desc";
$result		= @mysql_query($sql);
$row			= @mysql_fetch_row($result);
$newest_user_name = $row[0];
$newest_userid 	= $row[1];


//create a list of all users that are online
$show_online_users	= '';

$online_wrap_count 	= 0;

while ( $onlineusers = @mysql_fetch_array($onlinequery) ) {


	//form list with links to users page

	// v3 right div has room for maybe 4 names without spilling over??
	// dirty way to spit online links

	//$count_online_users

	if ($onlineusers['logged_in_username'] != 'guest'){

		$online_max_len = strlen($onlineusers['logged_in_username']);
		$online_wrap_count++;

		if ( $online_max_len < 8 ) $allowed_names = 5;
		if ( $online_max_len >= 8 ) $allowed_names = 4;

		if ( $online_wrap_count == $allowed_names ) {
			$break_add_in = '<br />';
			$online_wrap_count = 0;
		} else {
			$break_add_in = '';
		}

		$show_online_users = $show_online_users.'<a href="members/'.$onlineusers['logged_in_username'].'">'.$onlineusers['logged_in_username'].'</a>&nbsp;'.$break_add_in;
	}
}

// show any errors/notifications
if ($codes == "") $codes = $codes_internal;

$error_code = errorcodes($codes);

if (!empty($error_code)) {
	$blk_notification	= $error_code['error_display'];
    	$message_type 	= $error_code['error_type'];
    	$error_message 	= $error_code['error_message'];
}

// show login boxes or hide login boxes

$show_login_box = ( ( $_SESSION['user_id'] == '' ) ? 1 : 0 );

if (defined('SMF_INSTALLED')) {
	$show_login_box		= 0;
	//$show_smf_login_box	= 1;
	$show_smf_login_box = ( ( $_SESSION['user_id'] == '' ) ? 1 : 0 );
}

$template		= "themes/$user_theme/templates/main_1.htm";
$inner_template1 	= "themes/$user_theme/templates/inner_index.htm";
$TBS 			= new clsTinyButStrong;
$TBS->NoErr 	= true;

$TBS->LoadTemplate("$template");

$TBS->MergeBlock('mp', $recent);
$TBS->MergeBlock('blkfeatured_videos', $browse_videos);
$TBS->MergeBlock('blkfeatured_audios', $browse_audios);
$TBS->MergeBlock('blkfeatured_blogs', $browse_blogs);
$TBS->MergeBlock('blkfeatured_images', $browse_images);

$TBS->Render 	= TBS_OUTPUT;
$TBS->Show();

@mysql_close();

unset($browse_videos);
unset($browse_audios);
unset($browse_blogs);
unset($browse_images);
die();
}
?>
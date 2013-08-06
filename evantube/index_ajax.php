<?php

///////////////////////////////////////////////////////////////////////////////////////
// PHPmotion                                                http://www.phpmotion.com //
///////////////////////////////////////////////////////////////////////////////////////
// License: You are not to sell or distribute this software without permission       //
// Help and support please visit http://www.phpmotion.com                            //
// Copyright reserved                                                                //
///////////////////////////////////////////////////////////////////////////////////////

include_once ('classes/config.php');

$limit			= mysql_real_escape_string($config['see_more_limits']);
$type				= mysql_real_escape_string($_GET['type']);
$which_one			= mysql_real_escape_string($_GET['load']);
$default_album_pic	= 'featured_mp3.png';

$theme_thumbnail_width	= $config['theme_thumbnail_width'];
$theme_thumbnail_height	= $config['theme_thumbnail_height'];


//get videos
if ( $type == 'video' ) {

	$load_block = 'videos';

	if ($which_one == "featured") {

		$browse_videos = array();
    		$sql = "SELECT * FROM videos WHERE featured = 'yes' AND approved='yes' AND public_private = 'public' ORDER BY indexer DESC LIMIT $limit";
    		$query = @mysql_query($sql);

    		while ($result_videos = @mysql_fetch_array($query)) {

    			$change_date			= dateTimeDiff($result_videos['date_uploaded']);
			$result_videos[date_uploaded] = $change_date;
			$uploaded_by			= mysql_real_escape_string($result_videos['user_id']);
			$video_length			= mysql_real_escape_string($result_videos['video_length']);
			$video_id				= mysql_real_escape_string($result_videos['indexer']);

			// get comments information IF NEEDED IN TEMPLATE
			$sql2					= "SELECT * FROM videocomments WHERE video_id = $video_id";
			$query2 				= @mysql_query($sql2);
			$comments_number 			= @mysql_num_rows($query2);
			$comments_array 			= array('comments' => $comments_number);

			// get video rating stars
			$stars_array			= array();
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
			$result_videos[video_length]	= substr($video_length, 3, 5);

      		// merge comments array and video array
    			$result_all_videos		= @array_merge($result_videos, $comments_array, $stars_array, $uploaded_by_array);
    			$browse_videos[]			= $result_all_videos;
		}

    		$see_more_title = $config["featured"];
	}

	//get most views
	if ($which_one == "viewed") {

		$browse_videos = array();
    		$sql = "SELECT * FROM videos WHERE approved='yes' AND public_private = 'public' ORDER BY number_of_views DESC LIMIT $limit";
    		$query = @mysql_query($sql);

    		while ($result_videos = @mysql_fetch_array($query)) {

    			$change_date			= dateTimeDiff($result_videos['date_uploaded']);
			$result_videos[date_uploaded] = $change_date;
			$uploaded_by			= mysql_real_escape_string($result_videos['user_id']);
			$video_length			= mysql_real_escape_string($result_videos['video_length']);
			$video_id				= mysql_real_escape_string($result_videos['indexer']);

			// get comments information IF NEEDED IN TEMPLATE
			$sql2					= "SELECT * FROM videocomments WHERE video_id = $video_id";
			$query2 				= @mysql_query($sql2);
			$comments_number 			= @mysql_num_rows($query2);
			$comments_array 			= array('comments' => $comments_number);

			// get video rating stars
			$stars_array			= array();
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
			$result_videos[video_length]	= substr($video_length, 3, 5);

      		// merge comments array and video array
    			$result_all_videos		= @array_merge($result_videos, $comments_array, $stars_array, $uploaded_by_array);
    			$browse_videos[]			= $result_all_videos;
		}

		$see_more_title = $config['most_viewed'];
	}
	//------------------------------------------------------------------------------------------------------

	//get most recent  -------------------------------------reusable ---------------------------------------
	if ($which_one == "recent") {

		$browse_videos = array();
    		$sql = "SELECT * FROM videos WHERE approved='yes' AND public_private = 'public' ORDER BY indexer DESC LIMIT $limit";
    		$query = @mysql_query($sql);

    		while ($result_videos = @mysql_fetch_array($query)) {

    			$change_date			= dateTimeDiff($result_videos['date_uploaded']);
			$result_videos[date_uploaded] = $change_date;
			$uploaded_by			= mysql_real_escape_string($result_videos['user_id']);
			$video_length			= mysql_real_escape_string($result_videos['video_length']);
			$video_id				= mysql_real_escape_string($result_videos['indexer']);

			// get comments information IF NEEDED IN TEMPLATE
			$sql2					= "SELECT * FROM videocomments WHERE video_id = $video_id";
			$query2 				= @mysql_query($sql2);
			$comments_number 			= @mysql_num_rows($query2);
			$comments_array 			= array('comments' => $comments_number);

			// get video rating stars
			$stars_array			= array();
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
			$result_videos[video_length]	= substr($video_length, 3, 5);

      		// merge comments array and video array
    			$result_all_videos		= @array_merge($result_videos, $comments_array, $stars_array, $uploaded_by_array);
    			$browse_videos[]			= $result_all_videos;
		}

		$see_more_title = $config["most_recent"];
	}
	//-----------------------------------------------------------------------------------------------------

	// get most commented -------------------------reuable-------------------------------------
	// issue is when using config limit, if most commented is past the limit
	// most commented will not show on the first page !!
	// work around query all rows then output only config LIMIT \\??//

	if ($which_one == "comments") {

		$result_featured_all = array();
		$browse_videos = array();

    		$sql = "SELECT * FROM videos WHERE approved='yes' AND public_private = 'public' ORDER BY indexer DESC"; // LIMIT $limit";
    		$query = @mysql_query($sql);

    		while ($result_videos = @mysql_fetch_array($query)) {

    			$change_date			= dateTimeDiff($result_videos['date_uploaded']);
			$result_videos[date_uploaded] = $change_date;
			$uploaded_by			= mysql_real_escape_string($result_videos['user_id']);
			$video_length			= mysql_real_escape_string($result_videos['video_length']);
			$video_id				= mysql_real_escape_string($result_videos['indexer']);

			// get comments information IF NEEDED IN TEMPLATE
			$sql2					= "SELECT * FROM videocomments WHERE video_id = $video_id";
			$query2 				= @mysql_query($sql2);
			$comments_number 			= @mysql_num_rows($query2);
			$comments_array 			= array('comments' => $comments_number);

			// get video rating stars
			$stars_array			= array();
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
			$result_videos[video_length]	= substr($video_length, 3, 5);

      		// merge comments array and video array
    			$result_all_videos		= @array_merge($result_videos, $comments_array, $stars_array, $uploaded_by_array);
    			$browse_videos[]			= $result_all_videos;
		}

		//sort the final array by order of number of comments
    		function arr_keys_multisort($arr,$my_key,$sort_type) {
    			foreach ($arr as $key => $row) {
            		$arr_tmp[$key] = $row["$my_key"];
            	}
           		if ($sort_type == 'desc')
           			@array_multisort($arr_tmp,SORT_DESC,$arr);
     			else
       			@array_multisort($arr_tmp,SORT_ASC,$arr);
      	return $arr;
      	}

      	/////////////////////////////////////////////////////////////////////////////
      	// return only config limit

      	$browse_videos_all = arr_keys_multisort($browse_videos,'comments','desc');

        //Limit videos, skip this if videos in array are less than limit
        if(count($browse_videos_all) > $limit){
      	$limit_browse_videos = array();

      	for ($x=0;$x<($limit);$x++) {
			$limit_browse_videos[$x] = $browse_videos_all[$x];
		}

		$browse_videos = $limit_browse_videos;
		}else{
		$browse_videos = $browse_videos_all;
		}

		$see_more_title = $config['most_commented'];
	}
	//-----------------------------------------------------------------------------------------------------------

	//get most RATED ---------------------------------reusable --------------------------------------------
	if ($which_one == "rated") {

		$browse_videos = array();
    		$sql = "SELECT * FROM videos WHERE approved='yes' AND public_private = 'public' ORDER BY rating_total_points DESC LIMIT $limit";
    		$query = @mysql_query($sql);

    		while ($result_videos = @mysql_fetch_array($query)) {

    			$change_date			= dateTimeDiff($result_videos['date_uploaded']);
			$result_videos[date_uploaded] = $change_date;
			$uploaded_by			= mysql_real_escape_string($result_videos['user_id']);
			$video_length			= mysql_real_escape_string($result_videos['video_length']);
			$video_id				= mysql_real_escape_string($result_videos['indexer']);

			// get comments information IF NEEDED IN TEMPLATE
			$sql2					= "SELECT * FROM videocomments WHERE video_id = $video_id";
			$query2 				= @mysql_query($sql2);
			$comments_number 			= @mysql_num_rows($query2);
			$comments_array 			= array('comments' => $comments_number);

			// get video rating stars
			$stars_array			= array();
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
			$result_videos[video_length]	= substr($video_length, 3, 5);

      		// merge comments array and video array
    			$result_all_videos		= @array_merge($result_videos, $comments_array, $stars_array, $uploaded_by_array);
    			$browse_videos[]			= $result_all_videos;
		}

    		$see_more_title = 'Highest Rated'; //$config["most_viewed"];
	}

} //end videos ajax
//################################################################# END VIDEOS ########################################################################

//get audios
if ( $type == 'audio' ) {

	$load_block = 'audios';

	if ($which_one == "featured") {

		$browse_audios = array();
    		$sql = "SELECT * FROM audios WHERE featured = 'yes' AND approved='yes' AND public_private = 'public' ORDER BY indexer DESC LIMIT $limit";
    		$query = @mysql_query($sql);

    		while ($result_audios = @mysql_fetch_array($query)) {

    			$change_date			= dateTimeDiff($result_audios['date_uploaded']);
			$result_audios[date_uploaded] = $change_date;
			$uploaded_by			= mysql_real_escape_string($result_audios['user_id']);
			$audio_length			= mysql_real_escape_string($result_audios['audio_length']);

			// strip off the the video duration hours if null => 00:00:00
			$audio_length_hours = substr($audio_length, 0, 2);
			if ( $audio_length_hours == '00' )
				$result_audios[audio_length] = substr($audio_length, 3, 5);

			// get album picture if any
			$album_id		= mysql_real_escape_string($result_audios['album_id']);
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
			define('audios', true);
      		$id = mysql_real_escape_string($result_audios['indexer']);
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

    		$see_more_title = $config["featured"];
	}

	//get most views
	if ($which_one == "viewed") {

		$browse_audios = array();
    		$sql = "SELECT * FROM audios WHERE approved='yes' AND public_private = 'public' ORDER BY number_of_views DESC LIMIT $limit";
    		$query = @mysql_query($sql);

    		while ($result_audios = @mysql_fetch_array($query)) {

    			$change_date			= dateTimeDiff($result_audios['date_uploaded']);
			$result_audios[date_uploaded] = $change_date;
			$uploaded_by			= mysql_real_escape_string($result_audios['user_id']);
			$audio_length			= mysql_real_escape_string($result_audios['audio_length']);

			// strip off the the video duration hours if null => 00:00:00
			$audio_length_hours = substr($audio_length, 0, 2);
			if ( $audio_length_hours == '00' )
				$result_audios[audio_length] = substr($audio_length, 3, 5);

			// get album picture if any
			$album_id		= mysql_real_escape_string($result_audios['album_id']);
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
			define('audios', true);
      		$id = mysql_real_escape_string($result_audios['indexer']);
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

		$see_more_title = $config["most_viewed"];
	}
	//------------------------------------------------------------------------------------------------------

	//get most recent  -------------------------------------reusable ---------------------------------------
	if ($which_one == "recent") {

		$browse_audios = array();
    		$sql = "SELECT * FROM audios WHERE approved='yes' AND public_private = 'public' ORDER BY indexer DESC LIMIT $limit";
    		$query = @mysql_query($sql);

    		while ($result_audios = @mysql_fetch_array($query)) {

    			$change_date			= dateTimeDiff($result_audios['date_uploaded']);
			$result_audios[date_uploaded] = $change_date;
			$uploaded_by			= mysql_real_escape_string($result_audios['user_id']);
			$audio_length			= mysql_real_escape_string($result_audios['audio_length']);

			// strip off the the video duration hours if null => 00:00:00
			$audio_length_hours = substr($audio_length, 0, 2);
			if ( $audio_length_hours == '00' )
				$result_audios[audio_length] = substr($audio_length, 3, 5);

			// get album picture if any
			$album_id		= mysql_real_escape_string($result_audios['album_id']);
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
			define('audios', true);
      		$id = mysql_real_escape_string($result_audios['indexer']);
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

		$see_more_title = $config["most_recent"];
	}
	//-----------------------------------------------------------------------------------------------------

	// get most commented -------------------------reuable-------------------------------------
	// issue is when using config limit, if most commented is past the limit
	// most commented will not show on the first page !!
	// work around query all rows then output only config LIMIT \\??//

	if ($which_one == "comments") {

		$result_featured_all = array();
		$browse_audios = array();

    		$sql = "SELECT * FROM audios WHERE approved='yes' AND public_private = 'public' ORDER BY indexer DESC"; // LIMIT $limit";
    		$query = @mysql_query($sql);

    		while ($result_audios = @mysql_fetch_array($query)) {

    			$change_date			= dateTimeDiff($result_audios['date_uploaded']);
			$result_audios[date_uploaded] = $change_date;
			$uploaded_by			= mysql_real_escape_string($result_audios['user_id']);
			$audio_length			= mysql_real_escape_string($result_audios['audio_length']);

			// strip off the the video duration hours if null => 00:00:00
			$audio_length_hours = substr($audio_length, 0, 2);
			if ( $audio_length_hours == '00' )
				$result_audios[audio_length] = substr($audio_length, 3, 5);

			// get album picture if any
			$album_id		= mysql_real_escape_string($result_audios['album_id']);
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
			define('audios', true);
      		$id = mysql_real_escape_string($result_audios['indexer']);
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

		//sort the final array by order of number of comments
    		function arr_keys_multisort($arr,$my_key,$sort_type) {
    			foreach ($arr as $key => $row) {
            		$arr_tmp[$key] = $row["$my_key"];
            	}
           		if ($sort_type == 'desc')
           			@array_multisort($arr_tmp,SORT_DESC,$arr);
     			else
       			@array_multisort($arr_tmp,SORT_ASC,$arr);
      	return $arr;
      	}

      	/////////////////////////////////////////////////////////////////////////////
      	// return only config limit

      	$browse_audios_all = arr_keys_multisort($browse_audios,'comments','desc');

      	$limit_browse_audios = array();

      	if ( sizeof($browse_audios) < $limit ) $limit = sizeof($browse_audios);

      	for ($x=0;$x<($limit);$x++) {
			$limit_browse_audios[$x] = $browse_audios_all[$x];
		}

		$browse_audios = $limit_browse_audios;

		$see_more_title = $config['most_commented'];
	}
	//-----------------------------------------------------------------------------------------------------------

	//get most RATED ---------------------------------reusable --------------------------------------------
	if ($which_one == "rated") {

		$browse_audios = array();
    		$sql = "SELECT * FROM audios WHERE approved='yes' AND public_private = 'public' ORDER BY rating_total_points DESC LIMIT $limit";
    		$query = @mysql_query($sql);

    		while ($result_audios = @mysql_fetch_array($query)) {

    			$change_date			= dateTimeDiff($result_audios['date_uploaded']);
			$result_audios[date_uploaded] = $change_date;
			$uploaded_by			= mysql_real_escape_string($result_audios['user_id']);
			$audio_length			= mysql_real_escape_string($result_audios['audio_length']);

			// strip off the the video duration hours if null => 00:00:00
			$audio_length_hours = substr($audio_length, 0, 2);
			if ( $audio_length_hours == '00' )
				$result_audios[audio_length] = substr($audio_length, 3, 5);

			// get album picture if any
			$album_id		= mysql_real_escape_string($result_audios['album_id']);
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
			define('audios', true);
      		$id = mysql_real_escape_string($result_audios['indexer']);
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

    		$see_more_title = 'Highest Rated'; //$config["most_viewed"];
	}
	//------------------------------------------------------------------------------------------------------
} //end audios ajax
//################################################################# END AUDIOS ########################################################################

//get blogs
if ( $type == 'blog' ) {

	$load_block = 'blogs';

	if ($which_one == "featured") {

		$browse_blogs = array();
    		$sql = "SELECT * FROM blogs WHERE featured = 'yes' AND approved='yes' AND public_private = 'public' ORDER BY indexer DESC LIMIT $limit";
    		$query = @mysql_query($sql);

    		while ( $result_blogs = @mysql_fetch_array($query) ) {

    			$blog_owner		= mysql_real_escape_string($result_blogs['blog_owner']);
    			$user_id		= mysql_real_escape_string($result_blogs['user_id']);
     			$blog_id 		= mysql_real_escape_string($result_blogs['indexer']);

     			$change_date	= dateTimeDiff($result_blogs[date_created]);
 			$result_blogs[date_created] = $change_date;

			// get blog member photo
			$a1_sql = "SELECT * FROM pictures WHERE user_id = $user_id";
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
			define('blogs', true);
      		$id = $blog_id; //mysql_real_escape_string($result1['indexer']);
      		include ('stars_include.php');
      		$stars_array = array('star1' => $star1,'star2' => $star2,'star3' => $star3, 'star4' => $star4,'star5' => $star5);

      		// merge arrays
    			$result_all_blogs		= @array_merge($result_blogs, $poster_picture, $stars_array); //, $uploaded_by_array);
    			$browse_blogs[]		= $result_all_blogs;
		}

    		$see_more_title = $config["featured"];
	}

	//get most views
	if ($which_one == "viewed") {

		$browse_blogs = array();
    		$sql = "SELECT * FROM blogs WHERE approved='yes' AND public_private = 'public' ORDER BY number_of_views DESC LIMIT $limit";
    		$query = @mysql_query($sql);

    		while ( $result_blogs = @mysql_fetch_array($query) ) {

    			$blog_owner		= mysql_real_escape_string($result_blogs['blog_owner']);
    			$user_id		= mysql_real_escape_string($result_blogs['user_id']);
     			$blog_id 		= mysql_real_escape_string($result_blogs['indexer']);

     			$change_date	= dateTimeDiff($result_blogs[date_created]);
 			$result_blogs[date_created] = $change_date;

			// get blog member photo
			$a1_sql = "SELECT * FROM pictures WHERE user_id = $user_id";
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
			define('blogs', true);
      		$id = $blog_id; //mysql_real_escape_string($result1['indexer']);
      		include ('stars_include.php');
      		$stars_array = array('star1' => $star1,'star2' => $star2,'star3' => $star3, 'star4' => $star4,'star5' => $star5);

      		// merge arrays
    			$result_all_blogs		= @array_merge($result_blogs, $poster_picture, $stars_array); //, $uploaded_by_array);
    			$browse_blogs[]		= $result_all_blogs;
		}

		$see_more_title = $config["most_viewed"];
	}
	//------------------------------------------------------------------------------------------------------

	//get most recent  -------------------------------------reusable ---------------------------------------
	if ($which_one == "recent") {

		$browse_blogs = array();
    		$sql = "SELECT * FROM blogs WHERE approved='yes' AND public_private = 'public' ORDER BY indexer DESC LIMIT $limit";
    		$query = @mysql_query($sql);

    		while ( $result_blogs = @mysql_fetch_array($query) ) {

    			$blog_owner		= mysql_real_escape_string($result_blogs['blog_owner']);
    			$user_id		= mysql_real_escape_string($result_blogs['user_id']);
     			$blog_id 		= mysql_real_escape_string($result_blogs['indexer']);

     			$change_date	= dateTimeDiff($result_blogs[date_created]);
 			$result_blogs[date_created] = $change_date;

			// get blog member photo
			$a1_sql = "SELECT * FROM pictures WHERE user_id = $user_id";
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
			define('blogs', true);
      		$id = $blog_id; //mysql_real_escape_string($result1['indexer']);
      		include ('stars_include.php');
      		$stars_array = array('star1' => $star1,'star2' => $star2,'star3' => $star3, 'star4' => $star4,'star5' => $star5);

      		// merge arrays
    			$result_all_blogs		= @array_merge($result_blogs, $poster_picture, $stars_array); //, $uploaded_by_array);
    			$browse_blogs[]		= $result_all_blogs;
		}

		$see_more_title = $config["most_recent"];
	}
	//-----------------------------------------------------------------------------------------------------

	// get most commented -------------------------reuable-------------------------------------
	// issue is when using config limit, if most commented is past the limit
	// most commented will not show on the first page !!
	// work around query all rows then output only config LIMIT \\??//

	if ($which_one == "comments") {

		$result_featured_all = array();
		$browse_blogs = array();

    		$sql = "SELECT * FROM blogs WHERE approved='yes' AND public_private = 'public' ORDER BY indexer DESC"; // LIMIT $limit";
    		$query = @mysql_query($sql);

    		while ( $result_blogs = @mysql_fetch_array($query) ) {

    			$blog_owner				= mysql_real_escape_string($result_blogs['blog_owner']);
    			$user_id				= mysql_real_escape_string($result_blogs['user_id']);
     			$blog_id 				= mysql_real_escape_string($result_blogs['indexer']);
     			$change_date			= dateTimeDiff($result_blogs[date_created]);
 			$result_blogs[date_created]	= $change_date;

 			// GET NUMBER OF BLOG REPLIES
			$sql2					= "SELECT * FROM blog_replys WHERE blog_id = $blog_id";
			$query2 				= @mysql_query($sql2);
			$replies_number 			= @mysql_num_rows($query2);
			$replies_array 			= array('replies' => $replies_number);

			// get blog member photo
			$a1_sql = "SELECT * FROM pictures WHERE user_id = $user_id";
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
			define('blogs', true);
      		$id = $blog_id; //mysql_real_escape_string($result1['indexer']);
      		include ('stars_include.php');
      		$stars_array = array('star1' => $star1,'star2' => $star2,'star3' => $star3, 'star4' => $star4,'star5' => $star5);

      		// merge arrays
    			$result_all_blogs		= @array_merge($result_blogs, $replies_array, $poster_picture, $stars_array); //, $uploaded_by_array);
    			$browse_blogs[]		= $result_all_blogs;
		}

		//sort the final array by order of number of replies
    		function arr_keys_multisort($arr,$my_key,$sort_type) {
    			foreach ($arr as $key => $row) {
            		$arr_tmp[$key] = $row["$my_key"];
            	}
           		if ($sort_type == 'desc')
           			@array_multisort($arr_tmp,SORT_DESC,$arr);
     			else
       			@array_multisort($arr_tmp,SORT_ASC,$arr);
      	return $arr;
      	}

      	/////////////////////////////////////////////////////////////////////////////
      	// return only config limit

      	$browse_blogs_all = arr_keys_multisort($browse_blogs,'replies','desc');

      	$limit_browse_blogs = array();

      	if ( sizeof($browse_blogs) < $limit ) $limit = sizeof($browse_blogs);

      	for ($x=0;$x<($limit);$x++) {
			$limit_browse_blogs[$x] = $browse_blogs_all[$x];
		}

		$browse_blogs = $limit_browse_blogs;

		$see_more_title = $config['most_commented'];
	}
	//-----------------------------------------------------------------------------------------------------------

	//get most RATED ---------------------------------reusable --------------------------------------------
	if ($which_one == "rated") {

		$browse_blogs = array();
    		$sql = "SELECT * FROM blogs WHERE approved='yes' AND public_private = 'public' ORDER BY rating_total_points DESC LIMIT $limit";
    		$query = @mysql_query($sql);

    		while ( $result_blogs = @mysql_fetch_array($query) ) {

    			$blog_owner		= mysql_real_escape_string($result_blogs['blog_owner']);
    			$user_id		= mysql_real_escape_string($result_blogs['user_id']);
     			$blog_id 		= mysql_real_escape_string($result_blogs['indexer']);

     			$change_date	= dateTimeDiff($result_blogs[date_created]);
 			$result_blogs[date_created] = $change_date;

			// get blog member photo
			$a1_sql = "SELECT * FROM pictures WHERE user_id = $user_id";
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
			define('blogs', true);
      		$id = $blog_id; //mysql_real_escape_string($result1['indexer']);
      		include ('stars_include.php');
      		$stars_array = array('star1' => $star1,'star2' => $star2,'star3' => $star3, 'star4' => $star4,'star5' => $star5);

      		// merge arrays
    			$result_all_blogs		= @array_merge($result_blogs, $poster_picture, $stars_array); //, $uploaded_by_array);
    			$browse_blogs[]		= $result_all_blogs;
		}

    		$see_more_title = 'Highest Rated'; //$config["most_viewed"];
	}
	//------------------------------------------------------------------------------------------------------
} //end blogs ajax
//################################################################# END BLOGS ########################################################################

//get images
if ( $type == 'image' ) {

	$load_block = 'images';

	if ($which_one == 'featured') {

		$browse_images = array();
    		$sql = "SELECT * FROM images WHERE featured = 'yes' AND approved='yes' AND public_private = 'public' ORDER BY indexer DESC LIMIT $limit";
    		$query = @mysql_query($sql);

    		while ($result_images = @mysql_fetch_array($query)) {

    			$change_date			= dateTimeDiff($result_images['date_uploaded']);
			$result_images[date_uploaded] = $change_date;
			$uploaded_by			= mysql_real_escape_string($result_images['user_id']);
			$image_id				= mysql_real_escape_string( $result_images['image_id'] );

			list($width, $height)		= getimagesize( "addons/albums/thumbs/$image_id" );
			$page_display_small_width	= 120;
			$page_display_small_height	= 90;

			if ( $width < $page_display_small_width && $height < $page_display_small_height ) {
				$new_smallwidth	= $width;
     				$new_smallheight	= $height;
			}

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

			// get rating stars
			define('images', true);
      		$id = mysql_real_escape_string($result_images['indexer']);
      		include ('stars_include.php');
      		$stars_array = array('star1' => $star1,'star2' => $star2,'star3' => $star3, 'star4' => $star4,'star5' => $star5);

      		// get uploaded by username
      		$sql_user 			= "SELECT user_name FROM member_profile WHERE user_id = '$uploaded_by'";
      		$uploaded_by		= @mysql_query($sql_user);
      		$by_result			= @mysql_fetch_array($uploaded_by);
			$uploaded_by_username	= $by_result['user_name'];
			$uploaded_by_array	= array('uploaded_by' => $uploaded_by_username);

      		//merge comments array and video array
    			$result_all_images	= @array_merge($result_images, $stars_array, $uploaded_by_array, $thumb_new_width_array, $thumb_new_height_array);
    			$browse_images[]		= $result_all_images;
		}

    		$see_more_title = $config["featured"];
	}

	//get most views
	if ($which_one == 'viewed') {

		$browse_images = array();
    		$sql = "SELECT * FROM images WHERE approved='yes' AND public_private = 'public' ORDER BY number_of_views DESC LIMIT $limit";
    		$query = @mysql_query($sql);

    		while ($result_images = @mysql_fetch_array($query)) {

    			$change_date			= dateTimeDiff($result_images['date_uploaded']);
			$result_images[date_uploaded] = $change_date;
			$uploaded_by			= mysql_real_escape_string($result_images['user_id']);
			$image_id				= mysql_real_escape_string( $result_images['image_id'] );

			list($width, $height)		= getimagesize( "addons/albums/thumbs/$image_id" );
			$page_display_small_width	= 120;
			$page_display_small_height	= 90;

			if ( $width < $page_display_small_width && $height < $page_display_small_height ) {
				$new_smallwidth	= $width;
     				$new_smallheight	= $height;
			}

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


			// get rating stars
			define('images', true);
      		$id = mysql_real_escape_string($result_images['indexer']);
      		include ('stars_include.php');
      		$stars_array = array('star1' => $star1,'star2' => $star2,'star3' => $star3, 'star4' => $star4,'star5' => $star5);

      		// get uploaded by username
      		$sql_user 			= "SELECT user_name FROM member_profile WHERE user_id = '$uploaded_by'";
      		$uploaded_by		= @mysql_query($sql_user);
      		$by_result			= @mysql_fetch_array($uploaded_by);
			$uploaded_by_username	= $by_result['user_name'];
			$uploaded_by_array	= array('uploaded_by' => $uploaded_by_username);

      		//merge comments array and video array
    			$result_all_images	= @array_merge($result_images, $stars_array, $uploaded_by_array, $thumb_new_width_array, $thumb_new_height_array);
    			$browse_images[]		= $result_all_images;
		}

		$see_more_title = $config["most_viewed"];
	}
	//------------------------------------------------------------------------------------------------------

	//get most recent  -------------------------------------reusable ---------------------------------------
	if ($which_one == 'recent') {

		$browse_images = array();
    		$sql = "SELECT * FROM images WHERE approved='yes' AND public_private = 'public' ORDER BY indexer DESC LIMIT $limit";
    		$query = @mysql_query($sql);

    		while ($result_images = @mysql_fetch_array($query)) {

    			$change_date			= dateTimeDiff($result_images['date_uploaded']);
			$result_images[date_uploaded] = $change_date;
			$uploaded_by			= mysql_real_escape_string($result_images['user_id']);
			$image_id				= mysql_real_escape_string( $result_images['image_id'] );

			list($width, $height)		= getimagesize( "addons/albums/thumbs/$image_id" );
			$page_display_small_width	= 120; //100;
			$page_display_small_height	= 90; //76;

			if ( $width < $page_display_small_width && $height < $page_display_small_height ) {
				$new_smallwidth	= $width;
     				$new_smallheight	= $height;
			}

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


			// get rating stars
			define('images', true);
      		$id = mysql_real_escape_string($result_images['indexer']);
      		include ('stars_include.php');
      		$stars_array = array('star1' => $star1,'star2' => $star2,'star3' => $star3, 'star4' => $star4,'star5' => $star5);

      		// get uploaded by username
      		$sql_user 			= "SELECT user_name FROM member_profile WHERE user_id = '$uploaded_by'";
      		$uploaded_by		= @mysql_query($sql_user);
      		$by_result			= @mysql_fetch_array($uploaded_by);
			$uploaded_by_username	= $by_result['user_name'];
			$uploaded_by_array	= array('uploaded_by' => $uploaded_by_username);

      		//merge comments array and video array
    			$result_all_images	= @array_merge($result_images, $stars_array, $uploaded_by_array, $thumb_new_width_array, $thumb_new_height_array);
    			$browse_images[]		= $result_all_images;
		}

		$see_more_title = $config["most_recent"];
	}
	//-----------------------------------------------------------------------------------------------------

	// get most commented -------------------------reuable-------------------------------------
	// issue is when using config limit, if most commented is past the limit
	// most commented will not show on the first page !!
	// work around query all rows then output only config LIMIT \\??//

	if ($which_one == 'comments') {

		$result_featured_all = array();
		$browse_images = array();

    		$sql = "SELECT * FROM images WHERE approved='yes' AND public_private = 'public' ORDER BY indexer DESC"; // LIMIT $limit";
    		$query = @mysql_query($sql);

    		while ($result_images = @mysql_fetch_array($query)) {

    			$change_date			= dateTimeDiff($result_images['date_uploaded']);
			$result_images[date_uploaded] = $change_date;
			$uploaded_by			= mysql_real_escape_string($result_images['user_id']);
			$image_id				= mysql_real_escape_string( $result_images['image_id'] );

			list($width, $height)		= getimagesize( "addons/albums/thumbs/$image_id" );
			$page_display_small_width	= 120; //100;
			$page_display_small_height	= 90; //76;

			if ( $width < $page_display_small_width && $height < $page_display_small_height ) {
				$new_smallwidth	= $width;
     				$new_smallheight	= $height;
			}

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


			// get rating stars
			define('images', true);
      		$id = mysql_real_escape_string($result_images['indexer']);
      		include ('stars_include.php');
      		$stars_array = array('star1' => $star1,'star2' => $star2,'star3' => $star3, 'star4' => $star4,'star5' => $star5);

      		// get uploaded by username
      		$sql_user 			= "SELECT user_name FROM member_profile WHERE user_id = '$uploaded_by'";
      		$uploaded_by		= @mysql_query($sql_user);
      		$by_result			= @mysql_fetch_array($uploaded_by);
			$uploaded_by_username	= $by_result['user_name'];
			$uploaded_by_array	= array('uploaded_by' => $uploaded_by_username);

      		//merge comments array and video array
    			$result_all_images	= @array_merge($result_images, $stars_array, $uploaded_by_array, $thumb_new_width_array, $thumb_new_height_array);
    			$browse_images[]		= $result_all_images;
		}

		//sort the final array by order of number of comments
    		function arr_keys_multisort($arr,$my_key,$sort_type) {
    			foreach ($arr as $key => $row) {
            		$arr_tmp[$key] = $row["$my_key"];
            	}
           		if ($sort_type == 'desc')
           			@array_multisort($arr_tmp,SORT_DESC,$arr);
     			else
       			@array_multisort($arr_tmp,SORT_ASC,$arr);
      	return $arr;
      	}

      	/////////////////////////////////////////////////////////////////////////////
      	// return only config limit

      	$browse_images_all = arr_keys_multisort($browse_images,'comments','desc');

      	$limit_browse_images = array();

      	if ( sizeof($browse_images) < $limit ) $limit = sizeof($browse_images);

      	for ($x=0;$x<($limit);$x++) {
			$limit_browse_images[$x] = $browse_images_all[$x];
		}

		$browse_images = $limit_browse_images;

		$see_more_title = $config['most_commented'];
	}
	//-----------------------------------------------------------------------------------------------------------

	//get most RATED ---------------------------------reusable --------------------------------------------
	if ($which_one == 'rated') {

		$browse_images = array();
    		$sql = "SELECT * FROM images WHERE approved='yes' AND public_private = 'public' ORDER BY rating_total_points DESC LIMIT $limit";
    		$query = @mysql_query($sql);

    		while ($result_images = @mysql_fetch_array($query)) {

    			$change_date			= dateTimeDiff($result_images['date_uploaded']);
			$result_images[date_uploaded] = $change_date;
			$uploaded_by			= mysql_real_escape_string($result_images['user_id']);
			$image_id				= mysql_real_escape_string( $result_images['image_id'] );

			list($width, $height)		= getimagesize( "addons/albums/thumbs/$image_id" );
			$page_display_small_width	= 120; //100;
			$page_display_small_height	= 90; //76;

			if ( $width < $page_display_small_width && $height < $page_display_small_height ) {
				$new_smallwidth	= $width;
     				$new_smallheight	= $height;
			}

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


			// get rating stars
			define('images', true);
      		$id = mysql_real_escape_string($result_images['indexer']);
      		include ('stars_include.php');
      		$stars_array = array('star1' => $star1,'star2' => $star2,'star3' => $star3, 'star4' => $star4,'star5' => $star5);

      		// get uploaded by username
      		$sql_user 			= "SELECT user_name FROM member_profile WHERE user_id = '$uploaded_by'";
      		$uploaded_by		= @mysql_query($sql_user);
      		$by_result			= @mysql_fetch_array($uploaded_by);
			$uploaded_by_username	= $by_result['user_name'];
			$uploaded_by_array	= array('uploaded_by' => $uploaded_by_username);

      		//merge comments array and video array
    			$result_all_images	= @array_merge($result_images, $stars_array, $uploaded_by_array, $thumb_new_width_array, $thumb_new_height_array);
    			$browse_images[]		= $result_all_images;
		}

    		$see_more_title = 'Highest Rated'; //$config["most_viewed"];
	}
	//------------------------------------------------------------------------------------------------------
} //end images ajax
//################################################################# END IMAGES ########################################################################


$template = "themes/$user_theme/templates/inner_index_ajax.htm";
$TBS = new clsTinyButStrong;
$TBS->NoErr = true;// no more error message displayed.
$TBS->LoadTemplate("$template");
//$TBS->MergeBlock('blkfeatured',$result_featured);

$TBS->MergeBlock('blkfeatured_videos', $browse_videos);
$TBS->MergeBlock('blkfeatured_audios', $browse_audios);
$TBS->MergeBlock('blkfeatured_blogs', $browse_blogs);
$TBS->MergeBlock('blkfeatured_images', $browse_images);

$TBS->Render = TBS_OUTPUT;
$TBS->tbs_show();
@mysql_close();
die();


?>
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

$page_title			= $lang_browse_audios . ' ' . $lang_on .  ' ' . $site_name;
$audio_album		= 0;
$tag_cloud 			= make_tag_cloud('audios');
$tag_cloud_block 		= $tag_cloud[1];
$result_featured 		= array();
$all_categories 		= array();
$all_albums 		= array();
$default_album_pic	= $config['default_album_pic'];
$limit 			= (int) mysql_real_escape_string($config['see_more_limits']);

if ( isset($_GET['album']) )
	$audio_album	= (int) mysql_real_escape_string($_GET['album']);
else
	$audio_album	= '';

if ( isset($_GET['load']) )
	$which_one	= mysql_real_escape_string($_GET['load']);
else
	$which_one	= 'recent';

// get all genre
$sql = "SELECT * FROM genre WHERE has_audio = 'yes' ORDER BY channel_name";
$query = @mysql_query($sql);

while ($result = @mysql_fetch_array($query)) {

	$channel_name 	= $result['channel_name'];
    	$channel_name 	= mysql_real_escape_string($channel_name);
    	$sql0 		= "SELECT * FROM audios WHERE channel = '$channel_name' AND approved = 'yes' $sql_public_private ORDER BY indexer DESC";
    	$query0 		= @mysql_query($sql0);
    	$result0 		= @mysql_fetch_array($query0);
    	$count_audios 	= mysql_num_rows($query0);
    	$audio_count 	= array('audio_count' => $count_audios);

    	if (empty($result0)) {
      	$audio_picture = 'default_no_group_audio';//show place holder image2wbmp
        	$indexer = "";
        	$result0 = array('audio_id' => $audio_picture, 'indexer' => $indexer);
    	}

    	$new_array = @array_merge($result, $audio_count, $result0);
    	$all_categories[] = $new_array;
}

// get all albums
$sql_2 = "SELECT * FROM audio_albums WHERE has_audio = 'yes' ORDER BY album_name";
$query_2 = @mysql_query($sql_2);

while ($result_2 = @mysql_fetch_array($query_2)) {
    	$all_albums[]	= $result_2;
}

//get all audios in selected album  ----------------------------
if ( $audio_album > 0 ) {
    	$which_one	= '';
    	define('audios', true);

	$pagination		= pagination("SELECT * FROM audios WHERE album_id = '$audio_album' AND featured = 'yes' AND approved='yes' $sql_public_private ORDER BY indexer DESC", $limit);
    	$set_limit 		= $pagination[0]['set_limit'];
    	$total_pages 	= $pagination[0]['total_pages'];
    	$current_page 	= $pagination[0]['current_page'];
    	$total_records	= $pagination[0]['total_records'];
    	$next_page 		= $pagination[0]['next_page'];
    	$prev_page 		= $pagination[0]['prev_page'];
    	$nl 			= $pagination[0]['nl'];
    	$pl 			= $pagination[0]['pl'];

	$sql_0		= "SELECT audio_id, album_id, title, title_seo, artist, description, tags, date_uploaded, audio_length, indexer, user_id, number_of_views FROM audios WHERE album_id = '$audio_album' AND approved='yes' $sql_public_private ORDER BY indexer DESC LIMIT $set_limit, $limit";
    	$query_0		= @mysql_query($sql_0);

    	while ($result1 = @mysql_fetch_array($query_0)) {

        	// get album picture if any
        	$album_id		= (int) mysql_real_escape_string($result1['album_id']);
	    	$pic_sql		= "SELECT album_name, album_name_seo, album_picture FROM audio_albums WHERE album_id = '$audio_album' LIMIT 1";
		$album_pic_result = @mysql_query($pic_sql);
		$row			= mysql_fetch_array( $album_pic_result );

		$album_name		= $row['album_name'];
		$album_name_seo	= $row['album_name_seo'];
		$see_more_title 	= $album_name;

		$album_pic_result = $row['album_picture'];


		if ( $album_pic_result != "" ) {
			$album_pic = 'addons/audio/images/album_images/' . $album_pic_result;
		} else {
			$album_pic = 'addons/audio/images/' . $default_album_pic;
		}

		$album_pic_array	= array('album_pic' => $album_pic);

		//get comments inforation
        	$audio_id = mysql_real_escape_string($result1['indexer']);
        	$sql2 = "SELECT * FROM audiocomments WHERE audio_id = $audio_id";
        	$query2 = @mysql_query($sql2);
        	$comments_number = @mysql_num_rows($query2);
        	if ( $comments_number == "" ) $comments_number = 0;
        	$comments_array = array('comments' => $comments_number);


		// get rating stars
        	$id = mysql_real_escape_string($result1['indexer']);
        	include ('stars_include.php');
        	$stars_array = array('star1' => $star1,'star2' => $star2,'star3' => $star3, 'star4' => $star4,'star5' => $star5);

        	//merge arrays
        	$result2 = @array_merge($result1, $comments_array, $album_pic_array, $stars_array);
        	$result_featured[] = $result2;
    	}

    	//$see_more_title = $album_name;

    	//PAGINATION PLUS >> start  -- reusable code
     	$url 					= 'audio/album/'.$audio_album;								//the url to be put in links - EDIT ME
    	$additional_url_variable 	= '/'.$album_name_seo.'/';
    	include_once('includes/pagination.inc.php');
    	//PAGINATION PLUS >> end
}

//------------------------------------------------

//get most recent  -----------------------------------
if ( $which_one == 'recent' ) {
     	define('audios', true);

	$pagination		= pagination("SELECT indexer FROM audios WHERE approved='yes' $sql_public_private ORDER BY indexer DESC", $limit);
    	$set_limit		= $pagination[0]['set_limit'];
    	$total_pages 	= $pagination[0]['total_pages'];
    	$current_page 	= $pagination[0]['current_page'];
    	$total_records 	= $pagination[0]['total_records'];
    	$next_page 		= $pagination[0]['next_page'];//use in html navigation (src)
    	$prev_page 		= $pagination[0]['prev_page'];//use in html navigation (src)
    	$nl 			= $pagination[0]['nl'];//use in html navigation: next>>
    	$pl 			= $pagination[0]['pl'];//use in html navigation: <<previous

    	$result_featured = array();
    	$sql = "SELECT * FROM audios WHERE approved='yes' $sql_public_private ORDER BY indexer DESC LIMIT $set_limit, $limit";
    	$query = @mysql_query($sql);

    	while ($result1 = @mysql_fetch_array($query)) {

        	// get album picture if any
	    	$album_id		= $result1['album_id'];
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

        	//get comments inforation
        	$audio_id		= mysql_real_escape_string($result1['indexer']);
        	$sql2 		= "SELECT * FROM audiocomments WHERE audio_id = $audio_id";
        	$query2 		= @mysql_query($sql2);
        	$comments_number	= @mysql_num_rows($query2);

        	if ( $comments_number == '' ) $comments_number = 0;

        	$comments_array 	= array('comments' => $comments_number);

        	// get rating stars
        	$id = mysql_real_escape_string($result1['indexer']);
        	include ('stars_include.php');
        	$stars_array = array('star1' => $star1,'star2' => $star2,'star3' => $star3, 'star4' => $star4,'star5' => $star5);

 	     	//merge arrays
        	$result2 = @array_merge($result1, $comments_array, $album_pic_array, $stars_array);
        	$result_featured[] = $result2;
    	}

    	$see_more_title = $config['most_recent'];

   	//PAGINATION PLUS >> start  -- reusable code
     	$url 					= 'audios/load';								//the url to be put in links - EDIT ME
    	$additional_url_variable 	= '/recent/';
    	include_once('includes/pagination.inc.php');

    	//PAGINATION PLUS >> end
}
//------------------------------------------------------

//get featured audios ----------------------------
if ( $which_one == 'featured' ) {
     	define('audios', true);

	$pagination		= pagination("SELECT indexer FROM audios WHERE featured = 'yes' AND approved='yes' $sql_public_private ORDER BY indexer DESC", $limit);
    	$set_limit 		= $pagination[0]['set_limit'];
    	$total_pages 	= $pagination[0]['total_pages'];
    	$current_page 	= $pagination[0]['current_page'];
    	$total_records	= $pagination[0]['total_records'];
    	$next_page 		= $pagination[0]['next_page'];//use in html navigation (src)
    	$prev_page 		= $pagination[0]['prev_page'];//use in html navigation (src)
    	$nl 			= $pagination[0]['nl'];//use in html navigation: next>>
    	$pl 			= $pagination[0]['pl'];//use in html navigation: <<previous

    	$result_featured = array();
    	$sql = "SELECT * FROM audios WHERE featured = 'yes' AND approved='yes' $sql_public_private ORDER BY indexer DESC LIMIT $set_limit, $limit";
    	$query = @mysql_query($sql);

    	while ($result1 = @mysql_fetch_array($query)) {

    		// get album picture if any
	    	$album_id		= $result1['album_id'];
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

        	//get comments inforation
        	$audio_id	= mysql_real_escape_string($result1['indexer']);

        	$sql2 = "SELECT * FROM audiocomments WHERE audio_id = $audio_id";
        	$query2 = @mysql_query($sql2);
        	$comments_number = @mysql_num_rows($query2);
        	if ( $comments_number == "" ) $comments_number = 0;
        	$comments_array = array('comments' => $comments_number);

        	// get rating stars
        	$id = mysql_real_escape_string($result1['indexer']);
        	include ('stars_include.php');
        	$stars_array = array('star1' => $star1,'star2' => $star2,'star3' => $star3, 'star4' => $star4,'star5' => $star5);

 	     	//merge arrays
        	$result2 = @array_merge($result1, $comments_array, $album_pic_array, $stars_array);
        	$result_featured[] = $result2;
    	}

    	$see_more_title = $config['featured'];

    	//PAGINATION PLUS >> start  -- reusable code
     	$url 					= 'audios/load';								//the url to be put in links - EDIT ME
    	$additional_url_variable 	= '/featured/';
    	include_once ('includes/pagination.inc.php');

    	//PAGINATION PLUS >> end
}
//------------------------------------------------

//get most views ---------------------------------
if ( $which_one == 'viewed' ) {
     	define('audios', true);

	$pagination 	= pagination("SELECT indexer FROM audios WHERE approved='yes' $sql_public_private ORDER BY number_of_views DESC", $limit);
    	$set_limit 		= $pagination[0]['set_limit'];
    	$total_pages 	= $pagination[0]['total_pages'];
    	$current_page 	= $pagination[0]['current_page'];
    	$total_records	= $pagination[0]['total_records'];
    	$next_page 		= $pagination[0]['next_page'];							//use in html navigation (src)
    	$prev_page 		= $pagination[0]['prev_page'];							//use in html navigation (src)
    	$nl 			= $pagination[0]['nl'];									//use in html navigation: next>>
    	$pl 			= $pagination[0]['pl'];									//use in html navigation: <<previous

    	$result_featured = array();
    	$sql = "SELECT * FROM audios WHERE approved='yes' $sql_public_private ORDER BY number_of_views DESC LIMIT $set_limit, $limit";
    	$query = @mysql_query($sql);

    	while ($result1 = @mysql_fetch_array($query)) {

    		// get album picture if any
	    	$album_id		= $result1['album_id'];
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

    		//get comments inforation
        	$audio_id = mysql_real_escape_string($result1['indexer']);
        	$sql2 = "SELECT * FROM audiocomments WHERE audio_id = $audio_id";
        	$query2 = @mysql_query($sql2);
        	$comments_number = @mysql_num_rows($query2);
        	if ( $comments_number == "" ) $comments_number = 0;
        	$comments_array = array('comments' => $comments_number);

    		// get rating stars
        	$id = mysql_real_escape_string($result1['indexer']);
        	include ('stars_include.php');
        	$stars_array = array('star1' => $star1,'star2' => $star2,'star3' => $star3, 'star4' => $star4,'star5' => $star5);

        	//merge arrays
        	$result2 = @array_merge($result1, $comments_array, $album_pic_array, $stars_array);
        	$result_featured[] = $result2;
	}

    	$see_more_title = $config['most_played'];

    	//PAGINATION PLUS >> start  -- reusable code
     	$url 					= 'audios/load';								//the url to be put in links - EDIT ME
    	$additional_url_variable 	= '/viewed/';
    	include_once ('includes/pagination.inc.php');
    	//PAGINATION PLUS >> end
}
//-----------------------------------------------------

//get most commented ------------------------------------
if ( $which_one == 'comments' ) {
     	define('audios', true);

	$pagination 	= pagination("SELECT indexer FROM audios WHERE approved='yes' $sql_public_private ORDER BY indexer DESC", $limit);
    	$set_limit 		= $pagination[0]['set_limit'];
    	$total_pages 	= $pagination[0]['total_pages'];
    	$current_page 	= $pagination[0]['current_page'];
    	$total_records 	= $pagination[0]['total_records'];
    	$next_page 		= $pagination[0]['next_page'];//use in html navigation (src)
    	$prev_page 		= $pagination[0]['prev_page'];//use in html navigation (src)
    	$nl 			= $pagination[0]['nl'];//use in html navigation: next>>
    	$pl 			= $pagination[0]['pl'];//use in html navigation: <<previous

    	$result_featured 	= array();
    	$sql 			= "SELECT * FROM audios WHERE approved='yes' $sql_public_private ORDER BY indexer";
    	$query 		= @mysql_query($sql);

    	$aud_limit_count 	= @mysql_num_rows($query);

    	while ($result1 	= @mysql_fetch_array($query)) {

      	// get album picture if any
	    	$album_id		= $result1['album_id'];
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

      	//get comments inforation
        	$audio_id = mysql_real_escape_string($result1['indexer']);
        	$sql2 = "SELECT * FROM audiocomments WHERE audio_id = $audio_id";
        	$query2 = @mysql_query($sql2);
        	$comments_number = @mysql_num_rows($query2);

        	if ( $comments_number == "" ) $comments_number = 0;
        	$comments_array = array('comments' => $comments_number);

        	// get rating stars
        	$id = mysql_real_escape_string($result1['indexer']);
        	include ('stars_include.php');
        	$stars_array = array('star1' => $star1,'star2' => $star2,'star3' => $star3, 'star4' => $star4,'star5' => $star5);

	   	//merge arrays
        	$result2 = @array_merge($result1, $comments_array, $stars_array, $album_pic_array);
        	$result_featured[] = $result2;

    	}

    	//sort the final array by order of number of comments
    	function arr_keys_multisort($arr, $my_key, $sort_type) {

    		foreach ($arr as $key => $row) {
            	$arr_tmp[$key] = $row["$my_key"];
		}
	     	if ($sort_type == 'desc')
            	@array_multisort($arr_tmp, SORT_DESC, $arr);
        	else
            	@array_multisort($arr_tmp, SORT_ASC, $arr);

        	return $arr;
    	}

    	// return only config limit
      $result_featured_all = arr_keys_multisort($result_featured, 'comments', 'desc');

     	$limit_result_featured = array();
     	if ( sizeof($result_featured) < $limit ) $limit = sizeof($result_featured);

	$show_count = $limit + $set_limit;

	if ( $show_count > $aud_limit_count ) $show_count = $aud_limit_count;

	for ( $x = $set_limit; $x < $show_count; $x++ ) {
		$limit_result_featured[$x] = $result_featured_all[$x];
	}

    	$result_featured = $limit_result_featured;

    	$see_more_title = $config['most_commented'];

	//PAGINATION PLUS >> start  -- reusable code
     	$url 					= 'audios/load';								//the url to be put in links - EDIT ME
    	$additional_url_variable 	= '/comments/';
    	include_once('includes/pagination.inc.php');
    	//PAGINATION PLUS >> end

}
//----------------------------------------------------------

//show "no audios to show" empty arrays
if (empty($result_featured)) $show_v = 1; else $show_v = 2;


//display results
$template		= "themes/$user_theme/templates/main_1.htm";
$inner_template1 	= "themes/$user_theme/templates/inner_see_audios.htm";		//middle of page
$TBS 			= new clsTinyButStrong;
$TBS->NoErr 	= true;										// no more error message displayed.
$TBS->LoadTemplate("$template");

$TBS->MergeBlock('blkfeatured', $result_featured);

$TBS->MergeBlock('blk1', $all_categories);
$TBS->MergeBlock('blk2', $all_albums);

$TBS->Render 	= TBS_OUTPUT;
$TBS->Show();

@mysql_close();

die();

?>
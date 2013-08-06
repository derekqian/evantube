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

$page_title		= $lang_browse_videos . ' ' . $lang_on .  ' ' . $site_name;
$tag_cloud 		= make_tag_cloud('videos');
$tag_cloud_block	= $tag_cloud[1];

//get all categories
$all_categories	= array();
$sql 		= "SELECT channel_name, channel_name_seo, channel_id FROM channels ORDER BY channel_name ASC";
$query 		= @mysql_query($sql);
while ($result = @mysql_fetch_array($query)) {
	$channel_id	= $result['channel_id'];
    	$sql0 		= "SELECT indexer FROM videos WHERE channel_id = '$channel_id' AND approved = 'yes' $sql_public_private ORDER BY indexer DESC";
    	$query0 		= @mysql_query($sql0);
  	$result0 		= @mysql_fetch_array($query0);
  	$count_videos 	= @mysql_num_rows($query0);

    	// dont push array if empty or is empty in private views
    	if ( $count_videos == '' ) {

    		$count_videos = 0;
    		$result = '';

    	} else {
    	    	$vid_count	= array('vid_count' => $count_videos);
    		$new_array	= @array_merge($result, $vid_count);
    		$all_categories[] = $new_array;
    	}

}

$which_one	= mysql_real_escape_string($_GET['load']);
$result_featured 	= array();
$limit 		= (int) mysql_real_escape_string($config['see_more_limits']);

if ($which_one == '' ) $which_one = 'recent';

//get featured videos ----------------------------
if ($which_one == 'featured') {

	$pagination 	= pagination("SELECT indexer FROM videos WHERE featured = 'yes' AND approved='yes' $sql_public_private ORDER BY indexer DESC", $limit);
    	$set_limit 		= $pagination[0]['set_limit'];
    	$total_pages 	= $pagination[0]['total_pages'];
    	$current_page	= $pagination[0]['current_page'];
    	$total_records	= $pagination[0]['total_records'];
    	$next_page	= $pagination[0]['next_page'];							//use in html navigation (src)
    	$prev_page	= $pagination[0]['prev_page'];							//use in html navigation (src)
    	$nl 		= $pagination[0]['nl'];									//use in html navigation: next>>
    	$pl 		= $pagination[0]['pl'];									//use in html navigation: <<previous
	$result_featured 	= array();
    	$sql 		= "SELECT * FROM videos WHERE featured = 'yes' AND approved='yes' $sql_public_private ORDER BY indexer DESC LIMIT $set_limit, $limit";
    	$query 		= @mysql_query($sql);

    	while ($result1 = @mysql_fetch_array($query)) {

      	// get comments info
           	$video_id		= mysql_real_escape_string($result1['indexer']);
        	$sql2 		= "SELECT * FROM videocomments WHERE video_id = $video_id";
        	$query2 		= @mysql_query($sql2);
        	$comments_number	= @mysql_num_rows($query2);
        	$comments_array 	= array('comments' => $comments_number);
        	$stars_array 	= stars_array($video_id);							//call the stars function (results returned as array)

        	//merge comments array and video array
        	$result2 		= @array_merge($result1, $comments_array, $stars_array);
        	$result_featured[]= $result2;
   	}

    	$see_more_title = $config['featured'];

    	// PAGINATION PLUS >> start  -- reusable code

    	$url			= 'videos/load';								//the url to be put in links - EDIT ME
    	$additional_url_variable 	= '/featured/';							//information that goes in query string e.g. '&load=groups&friends=all'

    	@include_once ($include_base . '/includes/pagination.inc.php');
    	// PAGINATION PLUS >> end
}

//------------------------------------------------

//get most views ---------------------------------
if ($which_one == 'viewed') {

	$pagination = pagination("SELECT * FROM videos WHERE approved='yes' $sql_public_private ORDER BY number_of_views DESC",$limit);
    	$set_limit = $pagination[0]['set_limit'];
    	$total_pages = $pagination[0]['total_pages'];
    	$current_page = $pagination[0]['current_page'];
    	$total_records = $pagination[0]['total_records'];
    	$next_page = $pagination[0]['next_page'];//use in html navigation (src)
    	$prev_page = $pagination[0]['prev_page'];//use in html navigation (src)
    	$nl = $pagination[0]['nl'];//use in html navigation: next>>
    	$pl = $pagination[0]['pl'];//use in html navigation: <<previous

    	$result_featured = array();
    	$sql = "SELECT * FROM videos WHERE approved='yes' $sql_public_private ORDER BY number_of_views DESC LIMIT $set_limit, $limit";
    	$query = @mysql_query($sql);

    	while ($result1 = @mysql_fetch_array($query)) {
      	//get comments inforation
        	$video_id = mysql_real_escape_string($result1['indexer']);
        	$sql2 = "SELECT * FROM videocomments WHERE video_id = $video_id";
        	$query2 = @mysql_query($sql2);
        	$comments_number = @mysql_num_rows($query2);
        	$comments_array = array('comments' => $comments_number);

        	//get star rating
        	$stars_array = stars_array($video_id);//call the stars function (results returned as array)

        	//merge comments array and video array
        	$result2 = array_merge($result1, $comments_array, $stars_array);
        	$result_featured[] = $result2;
    	}
    	$see_more_title = $config['most_viewed'];

	//PAGINATION PLUS >> start  -- reusable code

    	$url = 'videos/load';
    	$additional_url_variable = '/viewed/';
    	include_once ($include_base . '/includes/pagination.inc.php');

    	//PAGINATION PLUS >> end
}
//-----------------------------------------------------

//get most recent  -----------------------------------
if ($which_one == 'recent') {
	$pagination = pagination("SELECT * FROM videos WHERE approved='yes' $sql_public_private ORDER BY indexer DESC", $limit);
    	$set_limit = $pagination[0]['set_limit'];
    	$total_pages = $pagination[0]['total_pages'];
    	$current_page = $pagination[0]['current_page'];
    	$total_records = $pagination[0]['total_records'];
    	$next_page = $pagination[0]['next_page'];//use in html navigation (src)
    	$prev_page = $pagination[0]['prev_page'];//use in html navigation (src)
    	$nl = $pagination[0]['nl'];//use in html navigation: next>>
    	$pl = $pagination[0]['pl'];//use in html navigation: <<previous

    	$result_featured = array();
    	$sql = "SELECT * FROM videos WHERE approved='yes' $sql_public_private ORDER BY indexer DESC LIMIT $set_limit, $limit";
    	$query = @mysql_query($sql);

    	while ($result1 = @mysql_fetch_array($query)) {

      	//get comments inforation
        	$video_id = mysql_real_escape_string($result1['indexer']);

        	$sql2 = "SELECT * FROM videocomments WHERE video_id = $video_id";
        	$query2 = @mysql_query($sql2);
        	$comments_number = @mysql_num_rows($query2);

        	$comments_array = array('comments' => $comments_number);

        	//get star rating
        	$stars_array = stars_array($video_id);//call the stars function (results returned as array)

        	//merge comments array and video array
        	$result2 = array_merge($result1, $comments_array, $stars_array);
        	$result_featured[] = $result2;
    }

	$see_more_title = $config['most_recent'];

	//PAGINATION PLUS >> start  -- reusable code

    	$url = 'videos/load';								//the url to be put in links - EDIT ME
    	$additional_url_variable = '/recent/';				//add addtions information that goes in query string here , e.g. '&load=groups&friends=all' - EDIT ME

    	@include_once ($include_base . '/includes/pagination.inc.php');
    	//PAGINATION PLUS >> end
}
//------------------------------------------------------

//get most commented ------------------------------------
if ($which_one == 'comments') {

	$pagination = pagination("SELECT * FROM videos WHERE approved='yes' $sql_public_private ORDER BY indexer DESC", $limit);
    	$set_limit = $pagination[0]['set_limit'];
    	$total_pages = $pagination[0]['total_pages'];
    	$current_page = $pagination[0]['current_page'];
    	$total_records = $pagination[0]['total_records'];
    	$next_page = $pagination[0]['next_page'];//use in html navigation (src)
    	$prev_page = $pagination[0]['prev_page'];//use in html navigation (src)
    	$nl = $pagination[0]['nl'];//use in html navigation: next>>
    	$pl = $pagination[0]['pl'];//use in html navigation: <<previous

    	$result_featured	= array();
    	$sql 			= "SELECT * FROM videos WHERE approved='yes' $sql_public_private ORDER BY indexer DESC";
    	$query 		= @mysql_query($sql);
    	$vid_limit_count 	= @mysql_num_rows($query);

    	while ($result1 	= @mysql_fetch_array($query)) {

		//get comments inforation
        	$video_id = mysql_real_escape_string($result1['indexer']);
        	$sql2 = "SELECT * FROM videocomments WHERE video_id = $video_id";
        	$query2 = @mysql_query($sql2);
        	$comments_number = @mysql_num_rows($query2);
        	$comments_array = array('comments' => $comments_number);

        	//get star rating
        	$stars_array = stars_array($video_id);//call the stars function (results returned as array)

        	//merge comments array
        	$result2 = @array_merge($result1, $comments_array, $stars_array);
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

	if ( $show_count > $vid_limit_count ) $show_count = $vid_limit_count;

	for ( $x = $set_limit; $x < $show_count; $x++ ) {
		$limit_result_featured[$x] = $result_featured_all[$x];
	}

	$result_featured = $limit_result_featured;

	$see_more_title = $config['most_commented'];

    	//PAGINATION PLUS >> start  -- reusable code
    	$url = 'videos/load';								//the url to be put in links - EDIT ME
    	$additional_url_variable = '/comments/';			//add addtions information that goes in query string here , e.g. '&load=groups&friends=all' - EDIT ME
    	@include_once ($include_base . '/includes/pagination.inc.php');
    	//PAGINATION PLUS >> end
}

//----------------------------------------------------------

$limit = $config['see_more_limits'];//lit for all the small box type layous calls

//see more group videos --------------------------------------
if ($which_one == 'groupsvideos') {

	$group_id = mysql_real_escape_string($_GET['gid']);

	//check if group id specified
	if($group_id =='' || !is_numeric($group_id)){
		$message = $config['error_11'];
	generic_error($message); //item could not be found
	}

    	//Pagination
    	$pagination = pagination("SELECT * FROM group_videos WHERE group_id = $group_id", $limit);
    	$set_limit = $pagination[0]['set_limit'];
    	$total_pages = $pagination[0]['total_pages'];
    	$current_page = $pagination[0]['current_page'];
    	$total_records = $pagination[0]['total_records'];
    	$next_page = $pagination[0]['next_page'];//use in html navigation (src)
    	$prev_page = $pagination[0]['prev_page'];//use in html navigation (src)
    	$nl = $pagination[0]['nl'];//use in html navigation: next>>
    	$pl = $pagination[0]['pl'];//use in html navigation: <<previous

    	//get all videos
    	$result_featured = array();
    	$sql10 = "SELECT * FROM group_videos WHERE group_id = $group_id LIMIT $set_limit, $limit";
    	$query10 = mysql_query($sql10);
    	while ($result10 = @mysql_fetch_array($query10)) {

      	//collect each videos details
        	$each_video = mysql_real_escape_string($result10['video_id']);
        	$sql11 = "SELECT * FROM videos WHERE indexer = $each_video AND approved = 'yes'";
        	$query11 = @mysql_query($sql11);
        	$result11 = @mysql_fetch_array($query11);

        	if (!empty($result11)) {

            	//get video star rating
            	$stars_array = stars_array($each_video);//call the stars function (results returned as array)

            	//merge video data with stars array
            	$result_featured[] = array_merge($stars_array, $result11);
        	}
    	}
    	//create name fro display on see more page
    	$see_even_more_title = $config['seemore_group_videos'] . ' - ' . $group_name;
    	$see_even_more_out_link = $base_url . '/group_home.php?gid=' . $group_id;

    	//PAGINATION PLUS >> start  -- reusable code

    	$url = "seemore.php?load=groupsvideos&gid=$group_id";						 //the url to be put in links - EDIT ME
    	$additional_url_variable = "&page="; //add addtions information that goes in query string here , e.g. '&load=groups&friends=all'

    	@include_once ($include_base . '/includes/pagination.inc.php');
    	//PAGINATION PLUS >> end

}

//-----------------------------------------------------------------

//show "no videos to show" empty arrays
if (empty($result_featured)) {
    	$show_v = 1;
} else {
	$show_v = 2;
}

//display results

$template		= "themes/$user_theme/templates/main_1.htm";
$inner_template1	= "themes/$user_theme/templates/inner_see_more.htm";
$TBS			= new clsTinyButStrong;
$TBS->NoErr 	= true;

$TBS->LoadTemplate("$template");

$TBS->MergeBlock('blkfeatured', $result_featured);
$TBS->MergeBlock('blk1', $all_categories);

$TBS->Render 	= TBS_OUTPUT;
$TBS->Show();

@mysql_close();
die();


?>
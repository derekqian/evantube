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

// load required javascripts used in main_1.htm
$ahah			= 1;

$tag_cloud 		= make_tag_cloud('audios');
$tag_cloud_block	= $tag_cloud[1];
$channel 		= $_GET['cid'];
$channel 		= mysql_real_escape_string($channel);
$limit 		= $config['see_more_limits']; //$config['search_page_limits'];
$codes 		= $_GET['code'];

$sql1		= "SELECT * FROM genre WHERE has_audio = 'yes' AND channel_name = '$channel'";
$query1 	= @mysql_query($sql1);

if ( @mysql_num_rows($query1) == 0 ) {
	@mysql_close();
	error_redirect(121);
}

// get all genre that has an audio file, we only show genre with content
$all_categories	= array();
$sql 			= "SELECT * FROM genre WHERE has_audio = 'yes' order by channel_name";
$query 		= @mysql_query($sql);

while ($result = @mysql_fetch_array($query)) {

	$channel_name	= $result['channel_name'];
	$channel_name 	= mysql_real_escape_string($channel_name);
	$sql0 		= "SELECT * FROM audios WHERE channel = '$channel_name' AND approved = 'yes' AND public_private = 'public' ORDER BY indexer DESC";
    	$query0 		= @mysql_query($sql0);
    	$count_audios 	= mysql_num_rows($query0);
    	$audio_count 	= array('audio_count' => $count_audios);
    	$new_array 		= @array_merge($result, $audio_count);
    	$all_categories[] = $new_array;
}

//get all category audios
$pagination 	= pagination("SELECT * FROM audios WHERE channel = '$channel' AND approved='yes' ORDER BY indexer DESC", $limit);
$set_limit 		= $pagination[0]['set_limit'];
$total_pages 	= $pagination[0]['total_pages'];
$current_page 	= $pagination[0]['current_page'];
$total_records	= $pagination[0]['total_records'];
$next_page 		= $pagination[0]['next_page'];						//use in html navigation (src)
$prev_page 		= $pagination[0]['prev_page'];						//use in html navigation (src)
$nl 			= $pagination[0]['nl'];								//use in html navigation: next>>
$pl 			= $pagination[0]['pl'];								//use in html navigation: <<previous

$result_featured = array();

$sql = "SELECT * FROM audios WHERE channel = '$channel' AND approved='yes' AND public_private = 'public' ORDER BY indexer DESC LIMIT $set_limit, $limit";
$query = @mysql_query($sql);

while ($result1 = @mysql_fetch_array($query)) {

	//get comments information
    	$audio_id = $result1['indexer'];
    	$sql2 = "SELECT * FROM audiocomments WHERE audio_id = $audio_id";
    	$query2 = @mysql_query($sql2);
    	$comments_number = @mysql_num_rows($query2);
    	$comments_array = array('comments' => $comments_number);

    	// get rating stars
    	define('audios', true);

      $id = mysql_real_escape_string($result1['indexer']);
      include ('stars_include.php');
      $stars_array = array('star1' => $star1,'star2' => $star2,'star3' => $star3, 'star4' => $star4,'star5' => $star5);

    	//merge comments array and audio array
    	$result2 = @array_merge($result1, $comments_array, $stars_array);
    	$result_featured[] = $result2;
}

// get all albums
$sql_2 = "SELECT * FROM audio_albums WHERE has_audio = 'yes' ORDER BY album_name";
$query_2 = @mysql_query($sql_2);

while ($result_2 = @mysql_fetch_array($query_2)) {
    	$all_albums[]	= $result_2;
}

//show "no audios" if none found
if ( empty($result_featured) ) $show_v =1; else $show_v = 2;


//PAGINATION PLUS >> start

$url = 'genre';

$additional_url_variable = '/'.$channel.'/';
include_once('includes/pagination.inc.php');

//PAGINATION PLUS >> end


if ( $codes == "" ) $codes = $codes_internal;

$error_code = errorcodes($codes);

if ( !empty($error_code) ) {
	$blk_code = $error_code['error_display'];
    	$code_type = $error_code['error_type'];
    	$code_message = $error_code['error_message'];
}

$blk_id = 0;													//html table - error block

$template		= "themes/$user_theme/templates/main_1.htm";
$inner_template1 	= "themes/$user_theme/templates/inner_genre_home.htm";
$TBS 			= new clsTinyButStrong;

$TBS->NoErr = true;
$TBS->LoadTemplate("$template");

$TBS->MergeBlock('blkfeatured',$result_featured);
$TBS->MergeBlock('blk1', $all_categories);
$TBS->MergeBlock('blk2', $all_albums);

$TBS->Render 	= TBS_OUTPUT;
$TBS->Show();

@mysql_close();

die();

?>
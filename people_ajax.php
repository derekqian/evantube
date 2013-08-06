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
include_once ('online.php');

$page_display_small_width	= $config['general_medium_thumb_width'];
$ahah					= 1;
$page_limit				= 20;
$sort_by_choice 			= mysql_real_escape_string( $_GET['sort_by'] );

//usleep(500000);

// get users online
$sql				= "SELECT * FROM online";
$onlinequery 		= @mysql_query($sql);
$count_online_users 	= @mysql_num_rows($onlinequery);
$show_pagnation		= true;
$load 			= 'all';

if ( $sort_by_choice == '' ) $sort_by_choice = 'member_name';

switch ( $sort_by_choice ) {

	case 'member_name':
		$sort_member_sql = "SELECT * FROM member_profile WHERE account_status = 'active' ORDER BY user_name ASC";
	break;

	case 'join_date';
		$sort_member_sql = "SELECT * FROM member_profile WHERE account_status = 'active' ORDER BY date_created ASC";
	break;

	case 'last_active';
		$sort_member_sql = "SELECT * FROM member_profile WHERE account_status = 'active' ORDER BY last_seen DESC";
	break;

	case 'ranking';
		$sort_member_sql = "SELECT * FROM member_profile WHERE account_status = 'active' ORDER BY updated_rating DESC";
	break;

	case 'most_comments';
		$sort_member_sql = "SELECT * FROM member_profile WHERE account_status = 'active' ORDER BY user_name ASC";
	break;

} // end switch

if ( $sort_by_choice == 'online' ) {
	if ( $count_online_users < $page_limit ) {
		$limit = $count_online_users;
		$show_pagnation = false;
	} else {
		$limit = $page_limit;
	}
} else {
	$limit = $page_limit;
}

$pagination		= pagination("$sort_member_sql", $limit);
$set_limit 		= $pagination[0]['set_limit'];
$total_pages 	= $pagination[0]['total_pages'];
$current_page 	= $pagination[0]['current_page'];
$total_records	= $pagination[0]['total_records'];
$next_page 		= $pagination[0]['next_page'];
$prev_page 		= $pagination[0]['prev_page'];
$nl 			= $pagination[0]['nl'];
$pl 			= $pagination[0]['pl'];

$sql 			= $sort_member_sql.' LIMIT '.$set_limit.', '.$limit;
$query 		= @mysql_query($sql);

$limit_count 	= @mysql_num_rows($query);

$members_full 	= array();

while ($result = mysql_fetch_array($query)) {

	if ( $sort_by_choice == 'online' ) {

		$member_id = '1';
	} else {
		$member_id 		= $result['user_id'];
	}

	//get picture information
      $sql1 = "SELECT * FROM pictures WHERE user_id = $member_id AND approved ='yes'";

      $result1 = @mysql_query($sql1);

      if (@mysql_num_rows($result1) == 0) {

      	// show place holder for no image uploaded by user at all
            $picture = $config['site_base_url'] . "/themes/$user_theme/images/placeholder.gif";
            $picture_array = array('picture' => $picture);

            $thumb_new_width_array	= array('thumb_new_width' => 80);
		$thumb_new_height_array	= array('thumb_new_height' => 80);


	} else {

      	$results = mysql_fetch_array($result1);
            $result1_existing_file = $results['file_name'];
            $result1_approved = $results['approved'];

            // show current picture
            $picture = $config['site_base_url'] . '/pictures/' . $result1_existing_file;
            $picture_array = array('picture' => $picture);

            //list($width, $height)	= getimagesize($picture);
            list($width, $height)	= getimagesize( "pictures/$result1_existing_file" );


           	if ( $width > $page_display_small_width ) {
			$large_img_ratio	= $width/$height;

			if ( $large_img_ratio > 1 ) {
           			$new_smallwidth	= $page_display_small_width;
            		$new_smallheight	= $page_display_small_width / $large_img_ratio;
            	} else {
            		$new_smallheight	= $page_display_small_width;
            		$new_smallwidth	= $page_display_small_width * $large_img_ratio;
			}
		}

		if ( $height > $page_display_small_height ) {
			$large_img_ratio	= $width/$height;

           		if ( $large_img_ratio > 1 ) {
				$new_smallwidth	= $page_display_small_width;
				$new_smallheight	= $page_display_small_width / $large_img_ratio;
			} else {
				$new_smallheight	= $page_display_small_width;
				$new_smallwidth	= $page_display_small_width * $large_img_ratio;
			}
		}

		$new_smallwidth		= floor($new_smallwidth);
		$new_smallheight		= floor($new_smallheight);
		$thumb_new_width_array	= array('thumb_new_width' => $new_smallwidth);
		$thumb_new_height_array	= array('thumb_new_height' => $new_smallheight);

	}

	// get videos by member
      $sql = "SELECT indexer FROM videos WHERE user_id = $member_id AND approved='yes'";
      $membersvideos = mysql_num_rows(mysql_query($sql));
      $membersvideos_array = array('membersvideos' => $membersvideos);

      // getmembers favs
      $sql = "SELECT indexer FROM favorites WHERE user_id = $member_id";
      $membersfavs = mysql_num_rows(mysql_query($sql));
      $membersfavs_array = array('membersfavs' => $membersfavs);

      // getmembers friends
      $sql = "SELECT * FROM friends WHERE user_id = $member_id AND invitation_status = 'accepted' OR friends_id = $member_id AND invitation_status = 'accepted'";
      $membersfriends = mysql_num_rows(mysql_query($sql));
      $membersfriends_array = array('membersfriends' => $membersfriends);



      // get last seen / active
      $sql				= "SELECT last_seen FROM member_profile WHERE account_status = 'active' WHERE user_id = $member_id";
      $db_date			= $result['last_seen'];
      $change_date 		= dateTimeDiff($db_date);
      $result['last_seen'] 	= $change_date;

      if ( $db_date == '0000-00-00 00:00:00' ) $result['last_seen'] = $lang_never;

      $last_seen_array		= array('member_last_seen' => $result['last_seen']);

      // count member_profile comments
      $sql = "SELECT indexer FROM profilecomments WHERE members_id = $member_id ORDER BY indexer DESC ";
      $member_comments = mysql_num_rows(mysql_query($sql));
      $member_comments_array = array('member_comments' => $member_comments);

      // merge arrays
      $merged_array = array_merge($membersvideos_array, $result, $membersfavs_array, $membersfriends_array, $picture_array, $thumb_new_width_array, $thumb_new_height_array, $last_seen_array, $member_comments_array);
      $members_full[] = $merged_array;

}// end while

if ( $sort_by_choice == 'most_comments' ) {

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
      $members_full_all = arr_keys_multisort($members_full, 'member_comments', 'desc');

      /*
      $limit_result_featured = array();
      if ( sizeof($members_full) < $limit ) $limit = sizeof($members_full);
      $show_count = $limit + $set_limit;
      if ( $show_count > $limit_count ) $show_count = $limit_count;
      for ( $x = $set_limit; $x < $show_count; $x++ ) {
		$limit_result_featured[$x] = $members_full_all[$x];
	}
	*/

	$members_full = $members_full_all; //$limit_result_featured;

	$show_m_c = 1;

} // end if sort by comments


if (empty($members_full)) {
	$show_v = 1;

} else {
	$show_v = 2;
}

$see_even_more_title 	= $members_username . ' - ' . $config['word_videos'];
$see_even_more_out_link	= members/$members_username;

if ( $show_pagnation ) {

	//$url = 'people_ajax.php?sort_by='.$sort_by_choice;
	//$additional_url_variable = "&amp;page=";
	//include_once('includes/pagination.inc.php');

	$hide_numbering = true; //show only <<previous  Next>>>

	$url = 'javascript:void(0)" onClick="javascript:ahahscript.ahah(\'people_ajax.php?sort_by='.$sort_by_choice;	//the url to be put in links - EDIT ME

      $ahah_pagination = "', 'ajax_sort', '', 'GET', '', this);";					//for ajax pagination

      $additional_url_variable = '&page=';								//addtions information that goes in query string here

      include ($include_base . '/includes/pagination.inc.php');

	$ablums_pagination = $show_pages;


}

$template		= "themes/$user_theme/templates/inner_see_members_ajax.htm";
$TBS 			= new clsTinyButStrong;
$TBS->NoErr		= true;

$TBS->LoadTemplate("$template");
$TBS->MergeBlock('mp', $members_full);

$TBS->Render = TBS_OUTPUT;
$TBS->tbs_show();

@mysql_close();

die();


?>
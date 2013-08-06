<?php

//Menu Loaders
///////////////
$top_menu = 'members';
$side_menu = 'members';

@include_once ("../classes/config.php");
@include_once ("includes/inc.stats.php");
@include_once ("includes/functions.php");
@include_once ('includes/login_check.php');

//defaults settings /
/////////////////////
$show_hide = 1;
$show_notification = 0;
$todays_date = $config["date_format"];
$base_url = $config['site_base_url'];
$limit = $config["admin_maximum_display"];
$result_active = array();
$dashboard_header = $lang_members;


/////////////////////
//check for order by
/////////////////////
if (isset($_POST['sort'])) {

    $order_by = $_POST['sort'];
}
else {
    $order_by = $_GET['sort'];
}

// Check what sort action is coming from the query string
/////////////////////////////////////////////////////////

$order_videos = 4;//default video sort
$order_audio = 5;//default audio sort
$order_pictures = 6;//default pictures sort

switch ($order_by) {

    case 1:
        $sort = 'user_name';
        $sort_order = 'ASC';
        $order_by_username = 2;
        break;

    case 2:
        $sort = 'user_name';
        $sort_order = 'DESC';
        $order_by_username = 1;
        break;

    case 3:
        $sort = 'videos_count';
        $sort_order = 'ASC';
        $order_videos = 4;
        break;

    case 4:
        $sort = 'videos_count';
        $sort_order = 'DESC';
        $order_videos = 3;
        break;

    case 5:
        $sort = 'audio_count';
        $sort_order = 'DESC';
        $order_audio = 6;
        break;

    case 6:
        $sort = 'audio_count';
        $sort_order = 'ASC';
        $order_audio = 5;
        break;

    case 7:
        $sort = 'picture_count';
        $sort_order = 'DESC';
        $order_pictures = 8;
        break;

    case 8:
        $sort = 'picture_count';
        $sort_order = 'ASC';
        $order_pictures = 7;
        break;

    case 9:
        $sort = 'ORDER BY flag_counter DESC';
        $order_by_flags = 10;
        break;

    case 10:
        $sort = 'ORDER BY flag_counter ASC';
        $order_by_flags = 9;
        break;

    default:
        $sort = 'user_name';
        $sort_order = 'ASC';
        $order_by_username = 2;
        $order_videos = 3;
        break;

}

////////////////
//check for type
////////////////

if (isset($_POST['type'])) {

    $type = $_POST['type'];
}
else {
    $type = $_GET['type'];
}

if ($type == 1 || $type == 2 || $type == 3 || $type == 4 || $type == 5 || $type ==
    6) {
    $show_t = $type;
}
else {
    $show_t = 1;
}

//unset search term session valiable where its not needed
if ($type == 1 || $type == 2 || $type == 3 || $type == 4 || $type == 5 || $_GET['action'] == 99) {
unset($_SESSION["search_query"]);
}



//actions (delete, approce suspend, make moderator etc)>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>

//delete member by POST
//////////////////////
if (isset($_POST['delete']) && isset($_POST['list'])) {
    foreach ($_POST['list'] as $user_id) {
        managemember($user_id,'delete');
    }
    //notifications
    $show_notification = 1;
    $message = notifications(1);
}

//delet member by GET
//////////////////////
if (isset($_GET['delid'])) {
    $user_id = mysql_real_escape_string($_GET['delid']);
    managemember($user_id,'delete');

    //notifications
    $show_notification = 1;
    $message = notifications(1);

}

//suspend member by POST
//////////////////////
if (isset($_POST['suspend']) && isset($_POST['list'])) {
    foreach ($_POST['list'] as $user_id) {
        managemember($user_id,'suspend');
    }
    //notifications
    $show_notification = 1;
    $message = notifications(1);
}

//approve member by POST
//////////////////////
if (isset($_POST['activate']) && isset($_POST['list'])) {
    foreach ($_POST['list'] as $user_id) {
        managemember($user_id,'activate');
    }
    //notifications
    $show_notification = 1;
    $message = notifications(1);
}

//make Moderator
//////////////////////
if (isset($_POST['moderator']) && isset($_POST['list'])) {
    foreach ($_POST['list'] as $user_id) {
        managemember($user_id,'moderator');
    }
    //notifications
    $show_notification = 1;
    $message = notifications(1);
}

//basic setting>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
//pagination

if ($show_t == 1) {//sorting for top menu
    $pagination = pagination("SELECT * FROM member_profile WHERE account_status = 'active' AND user_group != 'admin'", $limit);
}

if ($show_t == 2) {//sorting for top menu
    $pagination = pagination("SELECT * FROM member_profile WHERE account_status = 'new' AND user_group != 'admin'", $limit);
}

if ($show_t == 3) {//sorting for top menu
    $pagination = pagination("SELECT * FROM member_profile WHERE account_status = 'suspended' AND user_group != 'admin'", $limit);
}

if ($show_t == 4) {//sorting for top menu
    $pagination = pagination("SELECT * FROM member_profile WHERE user_group != 'admin'",$limit);
}

if ($show_t == 5) {//sorting for top menu
    $pagination = pagination("SELECT * FROM member_profile WHERE user_group = 'standard_mod' OR user_group = 'global_mod'",$limit);
}

//search option using session information
// here the search button has not been pressed
//(just using last search session)

if ( $show_t == 6 && !isset($_POST['search']) ) {		//sorting for top menu

	$search_query = $_SESSION["search_query"];
    	$sql = "SELECT * FROM member_profile WHERE $search_query  AND user_group != 'admin'";
    	$pagination = pagination("SELECT * FROM member_profile WHERE $search_term  AND user_group != 'admin'",$limit);
}


//>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>. Search section >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>

if (isset($_POST['search'])) {
	//pick up all search post vars
    foreach ($_POST as $key => $value) {
        if ($key == 'user_name' || $key == 'email_address' || $key == 'first_name' || $key ==
            'last_name') {
        $$key = $value; //for use in html form
            if ($value != '') {
                $q1 = $key.' =\''.@mysql_real_escape_string(@trim($value)).'\' AND ';
            }else{
            	$q1 = '';
            	}
        }
        $search_term = $search_term.$q1;
    }

    	$search_query = @rtrim($search_term,'AND ');



    $sql = "SELECT * FROM member_profile WHERE $search_query  AND user_group != 'admin'";
    $pagination = pagination("SELECT * FROM member_profile WHERE $search_term  AND user_group != 'admin'",$limit);

    //record search string to session for use later
    @session_start();
    @session_register("search_query");
    $_SESSION["search_query"] = $search_query;
}

//>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>. Search end >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>

$set_limit		= $pagination[0]['set_limit'];
$total_pages 	= $pagination[0]['total_pages'];
$current_page 	= $pagination[0]['current_page'];
$total_records 	= $pagination[0]['total_records'];
$next_page 		= $pagination[0]['next_page'];//use in html navigation (src)
$prev_page 		= $pagination[0]['prev_page'];//use in html navigation (src)
$nl 			= $pagination[0]['nl'];//use in html navigation: next>>
$pl 			= $pagination[0]['pl'];//use in html navigation: <<previous

if ($show_t == 1) {//sorting for top menu
    $result_sql	= "SELECT * FROM member_profile WHERE account_status = 'active' AND user_group != 'admin' LIMIT $set_limit, $limit";
    $header_title = $lang_active;//lang - "Active"
}

if ($show_t == 2) {//sorting for top menu
    $result_sql	= "SELECT * FROM member_profile WHERE account_status = 'new' AND user_group != 'admin' LIMIT $set_limit, $limit";
    $header_title = $lang_active;//lang - "Pending"
}

if ($show_t == 3) {//sorting for top menu
    $result_sql 	= "SELECT * FROM member_profile WHERE account_status = 'suspended' AND user_group != 'admin' LIMIT $set_limit, $limit";
    $header_title = $lang_suspended;//lang - "Suspended"
}

if ($show_t == 4) {//sorting for top menu
    $result_sql 	= "SELECT * FROM member_profile WHERE user_group != 'admin' LIMIT $set_limit, $limit";
    $header_title = $lang_all;//lang - "All"
}

if ($show_t == 5) {//sorting for top menu
    $result_sql 	= "SELECT * FROM member_profile WHERE user_group = 'standard_mod' OR user_group = 'global_mod' LIMIT $set_limit, $limit";
    $header_title = $lang_active;//lang - "Pending"
}

if ($show_t == 6 && !isset($_POST['search'])) {//sorting for top menu
//search option using session information
    $search_query	= $_SESSION["search_query"];
    $result_sql 	= "SELECT * FROM member_profile WHERE $search_query AND user_group != 'admin' LIMIT $set_limit, $limit";
    $header_title = 'Todays (new)';

}


//SEARCH OPTION
///////////////
if (isset($_POST['search'])) {
    $result_sql = "SELECT * FROM member_profile WHERE $search_query AND user_group != 'admin' LIMIT $set_limit, $limit";
    $header_title = 'Todays (new)';
}

$result_active = array();
$query = @mysql_query($result_sql);
while ($result1 = @mysql_fetch_array($query)) {

    //get number of videos per member
    /////////////////////////////////
    $user_id = mysql_real_escape_string($result1['user_id']);
    $sql2 = "SELECT * FROM videos WHERE user_id = $user_id";
    $query2 = @mysql_query($sql2);
    $video_count = @mysql_num_rows($query2);
    $video_array = array('videos_count' => $video_count);

    //get number of audio per member
    /////////////////////////////////////////////////////////
    $user_id = mysql_real_escape_string($result1['user_id']);
    $sql3 = "SELECT * FROM audios WHERE user_id = $user_id";
    $query3 = @mysql_query($sql3);
    $audio_count = @mysql_num_rows($query3);
    $audio_array = array('audio_count' => $audio_count);

    //get number of videos per member
    /////////////////////////////////////////////////////////

    $user_id = mysql_real_escape_string($result1['user_id']);
    $sql4 = "SELECT * FROM audios WHERE user_id = $user_id";//TODO change to pictures SQL
    $query4 = @mysql_query($sql4);
    $pictures_count = @mysql_num_rows($query4);
    $pictures_array = array('pictures_count' => $pictures_count);

    //check if user is moderator (for html dispal yes/no etc)
    /////////////////////////////////////////////////////////
    if ($result1['user_group'] == 'standard_mod' || $result1['user_group'] == 'global_mod') {
        $moderator_status_array = array('moderator_status' => $lang_yes);
        //get moderators icon
        if ($result1['user_group'] == 'standard_mod') {
            $moderator_icon_array = array('moderator_icon' =>
                '<img border="0" src="images/moderator_1.gif">');
        }
        if ($result1['user_group'] == 'global_mod') {
            $moderator_icon_array = array('moderator_icon' =>
                '<img border="0" src="images/moderator_2.gif">');
        }
    }
    else {
        $moderator_status_array = array('moderator_status' => $lang_no);
        $moderator_icon_array = array('moderator_icon' => '');
    }

    //Merge all the results into one array
    //////////////////////////////////////
    $result2 = @array_merge($result1,$video_array,$audio_array,$pictures_array,$moderator_status_array,
        $moderator_icon_array);
    $result_active[] = $result2;
}

//sort the final "combo" (merged) array by specified $sort
/////////////////////////////////////////////////////////
$result_active = arr_keys_multisort($result_active,$sort,$sort_order);


////////////////////////////////////////////
//PAGINATION PLUS >> start  -- reusable code
////////////////////////////////////////////
$url = 'members.php';

$additional_url_variable = "?type=$show_t&action=0&sort=$order_by&page=";


@include_once ($include_base . '/includes/pagination.inc.php');
//PAGINATION PLUS >> end



//Display Results >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>

//MENU LOADER
/////////////

include_once ("includes/menuloader.php");

//set condition for hidding navigation & 'no items found' block
if (empty($result_active)) {
    $show = 1;
}
else {
    $show = 2;
}

//remove 'no results found' when loading the search page
if($_GET['action'] == 99){
$lang_no_results_found = $lang_search_instructions;
}

////////////////////////////////
//display form with error message
////////////////////////////////
$show_content_menu = 1;//display top tabs as set in /includes/menuloader
$template = "templates/main.html";
$inner_template1 = "templates/inner_members.htm";//middle of page
$TBS = new clsTinyButStrong;
$TBS->NoErr = true;// no more error message displayed.
$TBS->LoadTemplate("$template");
$TBS->MergeBlock('blk1',$result_active);
$TBS->Render = TBS_OUTPUT;
$TBS->Show();
@mysql_close();
die();

?>
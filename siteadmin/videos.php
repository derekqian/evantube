<?php

include_once ("../classes/config.php");
include_once ("includes/inc.stats.php");
include_once ("includes/functions.php");
include_once ('includes/login_check.php');


//Menu Loaders
///////////////
$top_menu = 'videos';
$side_menu = 'media';
$dashboard_header = $lang_word_videos;


/////////////////////
//defaults settings /
/////////////////////
$show_hide = 1;
$show_notification = 0;
$todays_date = $config["date_format"];
$base_url = $config['site_base_url'];
$limit = $config["admin_maximum_display"];
$result_active = array();

//basic setting>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>

/////////////////////
//check for order by
/////////////////////
if (isset($_POST['sort'])) {

    $order_by = $_POST['sort'];
}
else {
    $order_by = $_GET['sort'];
}

//set default orders for (title, view, date uplaoded)

$order_by_title = 4;
$order_by_views = 5;
$order_by_date = 7;
$order_by_flags = 9;

switch ($order_by) {

    case 1:
        $sort = 'ORDER BY indexer DESC';
        break;

    case 2:
        $sort = 'ORDER BY indexer ASC';
        $order_by_title = 4;
        $order_by_views = 4;
        break;

    case 3:
        $sort = 'ORDER BY title DESC';
        $order_by_title = 4;
        break;

    case 4:
        $sort = 'ORDER BY title ASC';
        $order_by_title = 3;
        break;

    case 5:
        $sort = 'ORDER BY number_of_views DESC';
        $order_by_views = 6;
        break;

    case 6:
        $sort = 'ORDER BY number_of_views ASC';
        $order_by_views = 5;
        break;

    case 7:
        $sort = 'ORDER BY indexer DESC';
        $order_by_date = 8;
        break;

    case 8:
        $sort = 'ORDER BY indexer ASC';
        $order_by_date = 7;
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
        $sort = 'ORDER BY indexer DESC';
        $order_by = 1;
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
    6 || $type == 7) {
    $show_t = $type;
}
else {
    $show_t = 1;
}

//unset search term session valiable where its not needed
if ($type == 1 || $type == 2 || $type == 3 || $type == 5 || $type == 7 || $_GET['action'] == 99) {  //99 is in search menu (i.e. resetting the search page if link is clicked)
    unset($_SESSION["search_query_video"]);
}


//____________________________________________________________________________________________________________________________________________
//Load Categories
//____________________________________________________________________________________________________________________________________________

$sql22  = "SELECT * FROM channels ORDER BY channel_name ASC";
$query22 = mysql_query($sql22);

while($result22 = mysql_fetch_array($query22)){
$main_category_name = $result22['channel_name'];
$main_category_id = $result22['channel_id'];

//get overall videos
$sql44 = "SELECT * FROM videos WHERE channel_id = $main_category_id AND approved='yes'";
$this_video_count = @mysql_num_rows(@mysql_query($sql44));


//get sub categories
////////////////////
$sql55  = "SELECT * FROM sub_channels WHERE parent_channel_id = $main_category_id ORDER BY sub_channel_name ASC";
$query55 = mysql_query($sql55);

while($result55 = mysql_fetch_array($query55)){
$sub_category_name = $result55['sub_channel_name'];
$sub_category_id = $result55['sub_channel_id'];

$sql66 = "SELECT * FROM videos WHERE sub_channel_id = $sub_category_id AND approved='yes'";
$this_video_count = @mysql_num_rows(@mysql_query($sql66));

$list = '<option value="'.$main_category_id.'_'.$sub_category_id.'">&nbsp;'.$main_category_name.'/'.$sub_category_name.'&nbsp;('.$this_video_count.')&nbsp</option>';
$category_list = $category_list.$list;
$has_subs = 1;
}

if($has_subs != 1){
$list = '<option value="'.$main_category_id.'_0">&nbsp;'.$main_category_name.'/'.$sub_category_name.'&nbsp;('.$this_video_count.')&nbsp</option>';
$category_list = $category_list.$list;
}
$has_subs = 0;//reset for next loop#
$sub_category_name = '';
}
//____________________________________________________________________________________________________________________________________________
//Load Categories END
//____________________________________________________________________________________________________________________________________________



//actions (delete, approce etc)>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>

//delet videos
///////////////
if (isset($_POST['delete']) && isset($_POST['list'])) {
    //delete each posted video
    foreach ($_POST['list'] as $video_id) {
        managevideo($video_id,'delete');
    }
    //notifications
    $show_notification = 1;
    $message = notifications(1);
}

if (isset($_GET['delid'])) {
    //delete single get video
    $video_id = mysql_real_escape_string($_GET['delid']);
    managevideo($video_id,'delete');
    //notifications
    $show_notification = 1;
    $message = notifications(1);
}

//Approve video
if (isset($_POST['approve']) && isset($_POST['list'])) {
    //delete each posted video
    foreach ($_POST['list'] as $video_id) {
        managevideo($video_id,'approve');
    }
    //notifications
    $show_notification = 1;
    $message = notifications(1);
}

//feature video
if (isset($_POST['feature']) && isset($_POST['list'])) {
    //delete each posted video
    foreach ($_POST['list'] as $video_id) {
        managevideo($video_id,'feature');
    }
    //notifications
    $show_notification = 1;
    $message = notifications(1);
}

	//feature video
	if (isset($_POST['unfeature']) && isset($_POST['list'])) {
    		//delete each posted video
    		foreach ($_POST['list'] as $video_id) {
        		managevideo($video_id,'unfeature');
    		}
    		//notifications
    		$show_notification = 1;
    		$message = notifications(1);
	}

	//reset flags video
	if (isset($_POST['reset_flags']) && isset($_POST['list'])) {
    		//delete each posted video
    		foreach ($_POST['list'] as $video_id) {
        		managevideo($video_id,'reset_flags');
    		}
    		//notifications
    		$show_notification = 1;
    		$message = notifications(1);
	}

	//promote video
	if (isset($_POST['promote']) && isset($_POST['promote'])) {
    		//delete each posted video
    		foreach ($_POST['list'] as $video_id) {
        		managevideo($video_id,'promote');
    		}
    		//notifications
    		$show_notification = 1;
    		$message = notifications(1);
	}

	//Un-promote video
	if (isset($_POST['unpromote']) && isset($_POST['unpromote'])) {
		//delete each posted video
    		foreach ($_POST['list'] as $video_id) {
      		managevideo($video_id,'unpromote');
    		}
    		//notifications
    		$show_notification = 1;
    		$message = notifications(1);
	}


	//Display Results >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>

	//pagination first
	/////////////////
	if ($show_t == 1) {//active videos
    		$pagination = pagination("SELECT * FROM videos WHERE approved='yes' $sort",$limit);
	}
	if ($show_t == 2) {//Video approval
    		$pagination = pagination("SELECT * FROM videos WHERE approved='pending' $sort", $limit);
	}
	if ($show_t == 3) {//featured
    		$pagination = pagination("SELECT * FROM videos WHERE featured='yes' AND approved='yes' $sort", $limit);
	}
	if ($show_t == 5) {//flagged
    		$pagination = pagination("SELECT * FROM videos WHERE flag_counter > 0 AND approved='yes' $sort", $limit);
	}
	if ($show_t == 7) {//featured
    		$pagination = pagination("SELECT * FROM videos WHERE promoted='yes' AND approved='yes' $sort", $limit);
	}

	//search option using session information
	// here the search button has not been pressed
	//(just using last search session)

	if ($show_t == 4 && !isset($_POST['search'])) {//sorting for top menu
    		$search_query = $_SESSION["search_query_video"];
    		$sql = "SELECT * FROM videos WHERE $search_query";
    		$pagination = pagination("SELECT * FROM videos WHERE $search_term",$limit);
	}

	//>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>. Search section >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>

	if (isset($_POST['search'])) {

		//pick up all search post vars
    		foreach ($_POST as $key => $value) {

      	if ($key == 'title' || $key == 'user_id' || $key == 'indexer') {

      		$$key = $value;//for use in html form
            	if ($value != '') {

            		$q1 = $key.' ='.@mysql_real_escape_string(@trim($value)).' AND ';//numeric search
                		if ($key == 'title') {
                  		$q1 = $key.' LIKE\'%'.@mysql_real_escape_string(@trim($value)).'%\' AND ';//search using LIKE
                		}
                		$search_term = $search_term.$q1;
            	}
		}
	}

	$search_query = @rtrim($search_term,'AND ');

	//get only approved/active vids
	if ($search_query !=''){
		$search_query = $search_query.' AND approved = \'yes\'';
	} else {
		$search_query = 'video_id = 100000000000000000000000000000001';	//quick fix
	}

	//add category search to query
	///////////////////////////////

	if ( $_POST['categories'] != 'none_selected' ) {

		//split posted id's into channel and subchannel
		list($main_category_from, $sub_category_from) = @split('_', $_POST['categories']);

		if ( $sub_category_from == 0 ) $sub_category_from = '99999';			// if video has no sub cat default = 99999

		if($search_query == 'video_id = 100000000000000000000000000000001') { 		// from step above where no form filled

			$search_query = 'channel_id='.$main_category_from.' AND sub_channel_id ='.$sub_category_from;

		} else {
			$search_query = $search_query.' AND channel_id='.$main_category_from.' AND sub_channel_id ='.$sub_category_from;
		}
	}

	//echo "query is " . $search_query;

	$sql = "SELECT * FROM videos WHERE $search_query";
	$pagination = pagination("SELECT * FROM videos WHERE $search_term",$limit);

    	//record search string to session for use later
    	@session_start();
    	@session_register("search_query_video");
    	$_SESSION["search_query_video"] = $search_query;
}

//>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>. Search end >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>

$set_limit = $pagination[0]['set_limit'];
$total_pages = $pagination[0]['total_pages'];
$current_page = $pagination[0]['current_page'];
$total_records = $pagination[0]['total_records'];
$next_page = $pagination[0]['next_page'];//use in html navigation (src)
$prev_page = $pagination[0]['prev_page'];//use in html navigation (src)

$nl = $config['pagination_next'];
$pl = $config['pagination_previous'];

//run specific sql

if ($show_t == 1) {//active videos
    $sql = "SELECT * FROM videos WHERE approved='yes' $sort LIMIT $set_limit, $limit";
}
if ($show_t == 2) {//Video approval
    $sql = "SELECT * FROM videos WHERE approved='pending' $sort LIMIT $set_limit, $limit";
}
if ($show_t == 3) {//featured
    $sql = "SELECT * FROM videos WHERE featured='yes' AND approved='yes' $sort LIMIT $set_limit, $limit";
}

if ($show_t == 5) {//flagged
    $sql = "SELECT * FROM videos WHERE flag_counter > 0 AND approved='yes' $sort LIMIT $set_limit, $limit";
}

if ($show_t == 7) {//featured
    $sql = "SELECT * FROM videos WHERE promoted='yes' AND approved='yes' $sort LIMIT $set_limit, $limit";
}

if ($show_t == 4 && !isset($_POST['search'])) {//sorting for top menu
    //search option using session information
    $search_query = $_SESSION["search_query_video"];
    $result_sql = "SELECT * FROM videos WHERE $search_query LIMIT $set_limit, $limit";
}

//SEARCH OPTION
///////////////
if (isset($_POST['search'])) {
    $result_sql = "SELECT * FROM videos WHERE $search_query LIMIT $set_limit, $limit";
}

//////////////////////
//run which ever $sql
///////////////////////
$query = @mysql_query($sql);
$sql_results_count = 0;//reset
$sql_results_count = @mysql_num_rows($query);
while ($result1 = @mysql_fetch_array($query)) {
    $result_active[] = $result1;
}

////////////////////////////////////////////
//PAGINATION PLUS >> start  -- reusable code
////////////////////////////////////////////
$url = 'videos.php';//the url to be put in links - EDIT ME
$additional_url_variable = "?type=$show_t&action=0&sort=$order_by&page=";//add addtions information that goes in query string here , e.g. '&load=groups&friends=all' - EDIT ME

@include_once ($include_base . '/includes/pagination.inc.php');
//PAGINATION PLUS >> end

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

//MENU LOADER
/////////////

include_once ("includes/menuloader.php");

////////////////////////////////
//display form with error message
////////////////////////////////
$show_content_menu = 1;//display top tabs as set in /includes/menuloader
$template = "templates/main.html";
$inner_template1 = "templates/inner_videos.html";//middle of page
$TBS = new clsTinyButStrong;
$TBS->NoErr = true;// no more error message displayed.
$TBS->LoadTemplate("$template");
$TBS->MergeBlock('blkallvideos',$result_active);
$TBS->Render = TBS_OUTPUT;
$TBS->Show();
@mysql_close();
die();




?>
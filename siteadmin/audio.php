<?php

/**
* @author PHPmotion.com
* @copyright 2008
*/

include_once ("../classes/config.php");
include_once ("includes/inc.stats.php");
include_once ("includes/functions.php");
include_once ('includes/login_check.php');

//Menu Loaders
///////////////
$top_menu = 'audio';
$side_menu = 'media';
$dashboard_header = $lang_word_Audio;

/////////////////////
//defaults settings /
/////////////////////
$show_hide = 1;
$show_notification = 0;
$todays_date = $config["date_format"];
$base_url = $config['site_base_url'];
$limit = $config["admin_maximum_display"];
$result_active = array();
$show_news = '';//news box (disabled)
$show_content_menu = '';//top tabs on content box (disabled)

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

if ($type == 1 | $type == 2 || $type == 3 || $type == 4 || $type == 5 || $type ==
    6) {
    $show_t = $type;
}
else {
    $show_t = 1;
}

//unset search term session valiable where its not needed
if ($type == 1 || $type == 2 || $type == 3 || $type == 5 || $_GET['action'] == 99) {  //99 is in search menu (i.e. resetting the search page if link is clicked)
    unset($_SESSION["search_query_audio"]);
}

//actions (delete, approce etc)>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>

//delet audio
///////////////
if (isset($_POST['delete']) && isset($_POST['list'])) {
    //delete each posted audio
    foreach ($_POST['list'] as $audio_id) {
        manageaudio($audio_id,'delete');
    }
    //notifications
    $show_notification = 1;
    $message = notifications(1);
}

if (isset($_GET['delid'])) {
    //delete single get audio
    $audio_id = mysql_real_escape_string($_GET['delid']);
    manageaudio($audio_id,'delete');
    //notifications
    $show_notification = 1;
    $message = notifications(1);
}

//Approve audio
if (isset($_POST['approve']) && isset($_POST['list'])) {
    //delete each posted audio
    foreach ($_POST['list'] as $audio_id) {
        manageaudio($audio_id,'approve');
    }
    //notifications
    $show_notification = 1;
    $message = notifications(1);
}


//feature audio
if (isset($_POST['feature']) && isset($_POST['list'])) {
    //delete each posted audio
    foreach ($_POST['list'] as $audio_id) {
        manageaudio($audio_id,'feature');
    }
    //notifications
    $show_notification = 1;
    $message = notifications(1);
}


//Un-feature audio
if (isset($_POST['unfeature']) && isset($_POST['list'])) {
    //delete each posted audio
    foreach ($_POST['list'] as $audio_id) {
        manageaudio($audio_id,'unfeature');
    }
    //notifications
    $show_notification = 1;
    $message = notifications(1);
}

//feature audio
if (isset($_POST['reset_flags']) && isset($_POST['list'])) {
    //delete each posted audio
    foreach ($_POST['list'] as $audio_id) {
        manageaudio($audio_id,'reset_flags');
    }
    //notifications
    $show_notification = 1;
    $message = notifications(1);
}


//Display Results >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>

//pagination first
/////////////////
if ($show_t == 1) {//active audio
    $pagination = pagination("SELECT * FROM audios WHERE approved='yes' $sort",$limit);
}
if ($show_t == 2) {//audio approval
    $pagination = pagination("SELECT * FROM audios WHERE approved='pending' $sort",
        $limit);
}
if ($show_t == 3) {//featured
    $pagination = pagination("SELECT * FROM audios WHERE featured='yes' AND approved='yes' $sort",
        $limit);
}

if ($show_t == 5) {//flagges
    $pagination = pagination("SELECT * FROM audios WHERE flag_counter > 0 AND approved='yes' $sort",
        $limit);
}

//search option using session information
// here the search button has not been pressed
//(just using last search session)
if ($show_t == 4 && !isset($_POST['search'])) {//sorting for top menu
    $search_query = $_SESSION["search_query_audio"];
    $sql = "SELECT * FROM audios WHERE $search_query";
    $pagination = pagination("SELECT * FROM audios WHERE $search_term",$limit);
}

//>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>. Search section >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>

if (isset($_POST['search'])) {
    //pick up all search post vars
    foreach ($_POST as $key => $value) {

        if ($key == 'title' || $key == 'user_id' || $key == 'indexer') {
            $$key = $value;//for use in html form
            if ($value != '') {
                $q1 = $key.' ='.mysql_real_escape_string(trim($value)).' AND ';//numeric search
                if ($key == 'title') {
                    $q1 = $key.' LIKE\''.mysql_real_escape_string(trim($value)).'\' AND ';//search using LIKE
                }

                $search_term = $search_term.$q1;
            }
        }
    }
    $search_query = rtrim($search_term,'AND ');

    //get only approved/active vids
    if ($search_query !=''){
    $search_query = $search_query.' AND approved = \'yes\'';
    	}else{
    	$search_query = 'audio_id = 100000000000000000000000000000001';	//quick fix
}
    $sql = "SELECT * FROM audios WHERE $search_query";
    $pagination = pagination("SELECT * FROM audios WHERE $search_term",$limit);
    //record search string to session for use later
    @session_start();
    @session_register("search_query_audio");
    $_SESSION["search_query_audio"] = $search_query;
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

if ($show_t == 1) {//active audio
    $sql = "SELECT * FROM audios WHERE approved='yes' $sort LIMIT $set_limit, $limit";
}
if ($show_t == 2) {//audio approval
    $sql = "SELECT * FROM audios WHERE approved='pending' $sort LIMIT $set_limit, $limit";
}
if ($show_t == 3) {//featured
    $sql = "SELECT * FROM audios WHERE featured='yes' AND approved='yes' $sort LIMIT $set_limit, $limit";
}

if ($show_t == 5) {//flagged
    $sql = "SELECT * FROM audios WHERE flag_counter > 0 AND approved='yes' $sort LIMIT $set_limit, $limit";
}

if ($show_t == 4 && !isset($_POST['search'])) {//sorting for top menu
    //search option using session information
    $search_query = $_SESSION["search_query_audio"];
    $result_sql = "SELECT * FROM audios WHERE $search_query LIMIT $set_limit, $limit";
}

//SEARCH OPTION
///////////////
if (isset($_POST['search'])) {
    $result_sql = "SELECT * FROM audios WHERE $search_query LIMIT $set_limit, $limit";
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
$url = 'audio.php';//the url to be put in links - EDIT ME
//$additional_url_variable = "?type=$show_t&action=0&sort=$order_by";//add addtions information that goes in query string here , e.g. '&load=groups&friends=all' - EDIT ME
$additional_url_variable = "?type=$show_t&action=0&sort=$order_by&page=";

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
$inner_template1 = "templates/inner_audio.html";//middle of page
$TBS = new clsTinyButStrong;
$TBS->NoErr = true;// no more error message displayed.
$TBS->LoadTemplate("$template");
$TBS->MergeBlock('blkallaudio',$result_active);
$TBS->Render = TBS_OUTPUT;
$TBS->Show();
@mysql_close();
die();




?>
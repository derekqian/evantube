<?php

/**
* @author PHPmotion.com
* @copyright 2008
*/

//Menu Loaders
///////////////
$top_menu = 'flagged';
$side_menu = 'flagged';

include_once ("../classes/config.php");
include_once ("includes/inc.stats.php");
include_once ("includes/functions.php");
include_once ('includes/login_check.php');

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

    case 9:
        $sort = 'ORDER BY flag_counter DESC';
        $order_by_flags = 10;
        break;

    case 10:
        $sort = 'ORDER BY flag_counter ASC';
        $order_by_flags = 9;
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

if ($type == 1 | $type == 2 || $type == 3 || $type == 4 || $type == 5 || $type == 6) {
    $show_t = $type;
}
else {
    $show_t = 1;
}

//actions (delete, approce etc)>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>

switch ($type) {

        case 1:
        $comments = 'videocomments';
        break;

        case 2:
        $comments = 'audiocomments';
        break;
        
        case 3:
        $comments = 'blog_replys';
        break;
        
        case 4:
        $comments = 'imagecomments';
        break;
        
        case 5:
        $comments = 'group_comments';
        break;
        
        case 6:
        $comments = 'profilecomments';
        break;
}


//delet
///////////////
if (isset($_POST['delete']) && isset($_POST['list'])) {
    foreach ($_POST['list'] as $id) {
        manage_flagged_comments($id,'delete',$comments);
    }
    //notifications
    $show_notification = 1;
    $message = notifications(1);
}

if (isset($_GET['delid'])) {
    $id = mysql_real_escape_string($_GET['delid']);
    manage_flagged_comments($id,'delete',$comments);
    //notifications
    $show_notification = 1;
    $message = notifications(1);
}

//Reset Flags
if (isset($_POST['reset_flags']) && isset($_POST['list'])) {
    foreach ($_POST['list'] as $id) {
        manage_flagged_comments($id,'reset_flags',$comments);
    }
    //notifications
    $show_notification = 1;
    $message = notifications(1);
}

//Display Results >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>

//pagination first
/////////////////
if ($show_t == 1) {//videocomments
    $pagination = pagination("SELECT * FROM videocomments WHERE flag_counter > 0 $sort",
        $limit);
}

if ($show_t == 2) {//audiocomments
    $pagination = pagination("SELECT * FROM audiocomments WHERE flag_counter > 0 $sort",
        $limit);
}

if ($show_t == 3) {//blog_replys
    $pagination = pagination("SELECT * FROM blog_replys WHERE flag_counter > 0 $sort",
        $limit);
}

if ($show_t == 4) {//imagecomments
    $pagination = pagination("SELECT * FROM imagecomments WHERE flag_counter > 0 $sort",
        $limit);
}

if ($show_t == 5) {//group_comments
    $pagination = pagination("SELECT * FROM group_comments WHERE flag_counter > 0 $sort",
        $limit);
}

if ($show_t == 6) {//profilecomments
    $pagination = pagination("SELECT * FROM profilecomments WHERE flag_counter > 0 $sort",
        $limit);
}

$set_limit = $pagination[0]['set_limit'];
$total_pages = $pagination[0]['total_pages'];
$current_page = $pagination[0]['current_page'];
$total_records = $pagination[0]['total_records'];
$next_page = $pagination[0]['next_page'];//use in html navigation (src)
$prev_page = $pagination[0]['prev_page'];//use in html navigation (src)

$nl = $config['pagination_next'];
$pl = $config['pagination_previous'];

//run specific sql

if ($show_t == 1) {//videocomments
    $sql = "SELECT * FROM videocomments WHERE flag_counter > 0 $sort LIMIT $set_limit, $limit";
    $edit_type = 1; //for /edit/editcomments.php
}

if ($show_t == 2) {//audiocomments
    $sql = "SELECT * FROM audiocomments WHERE flag_counter > 0 $sort LIMIT $set_limit, $limit";
    $edit_type = 3; //for /edit/editcomments.php
}

if ($show_t == 3) {//blog_replys
    $sql = "SELECT * FROM blog_replys WHERE flag_counter > 0 $sort LIMIT $set_limit, $limit";
    $edit_type = 5; //for /edit/editcomments.php
}

if ($show_t == 4) {//imagecomments
    $sql = "SELECT * FROM imagecomments WHERE flag_counter > 0 $sort LIMIT $set_limit, $limit";
    $edit_type = 6; //for /edit/editcomments.php
}

if ($show_t == 5) {//group_comments
    $sql = "SELECT * FROM group_comments WHERE flag_counter > 0 $sort LIMIT $set_limit, $limit";
    $edit_type = 9; //for /edit/editcomments.php
}

if ($show_t == 6) {//profilecomments
    $sql = "SELECT * FROM profilecomments WHERE flag_counter > 0 $sort LIMIT $set_limit, $limit";
    $edit_type = 8; //for /edit/editcomments.php
}


//////////////////////
//run which ever $sql
///////////////////////
$query = @mysql_query($sql);
$sql_results_count = 0;//reset
$sql_results_count = @mysql_num_rows($query);
while ($result1 = @mysql_fetch_array($query)) {

//strip out html tags to make safe display
$comment_stripped = @htmlspecialchars_decode($result1['comments']);
$comment_stripped = @strip_tags($comment_stripped);

//additional array stuff
$additional_array = array('comment_stripped'=>$comment_stripped, 'edit_type'=>$edit_type);
$result1 = array_merge($result1, $additional_array);

    $result_active[] = $result1;
}

////////////////////////////////////////////
//PAGINATION PLUS >> start  -- reusable code
////////////////////////////////////////////
$url = 'flagged_comments.php';//the url to be put in links - EDIT ME
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
$inner_template1 = "templates/inner_flagged_comments.html";//middle of page
$TBS = new clsTinyButStrong;
$TBS->NoErr = true;// no more error message displayed.
$TBS->LoadTemplate("$template");
$TBS->MergeBlock('blkallflagged',$result_active);
$TBS->Render = TBS_OUTPUT;
$TBS->Show();
@mysql_close();
die();




?>
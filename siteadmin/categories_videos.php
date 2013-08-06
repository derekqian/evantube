<?php

/**
* @author PHPmotion.com
* @copyright 2008
*/

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
$inner_template1 = "templates/inner_categories_videos.html";//default
$edit_category = 0;
$edit_sub_category =0;

//default titles
$current_title1 = $lang_new_category;
$current_title2 = $lang_new_sub_category;


//____________________________________________________________________________________________________________________________________________
// Some GET navigation aid
//____________________________________________________________________________________________________________________________________________

if($_GET['id'] !='' && is_numeric($_GET['id'])){echo 'ee';

if($_GET['type'] == 2){
//if this is a query string on subcats, make up some "post" data
$_POST['load_subcategory'] = 'xyz';
$_POST['main_category'] = $_GET['id'];
}	


}



//____________________________________________________________________________________________________________________________________________
//Create New main Categories
//____________________________________________________________________________________________________________________________________________

if(isset($_POST['new_category'])){
$title = $_POST['title'];
$description = $_POST['description'];
$proceed = true;

//all filled in
if($title =='' || $description ==''){
$show_notification = 1;
$message = $config['fill_all_fields'];
$proceed = false;
}

//check if already exists
if($title != ''){
$sql = "SELECT channel_id from channels WHERE channel_name = '$title'";
if(@mysql_num_rows(@mysql_query($sql)) >0){
$show_notification =1;
$message = $config['duplicate_item'];
$proceed = false;
}
}

//add to database
if($proceed == true){
$title_seo = seo_title($title);
$sql = "INSERT INTO channels (channel_name, channel_name_seo, channel_description, date_created) VALUES ('$title', '$title_seo', '$description', NOW())";
@mysql_query($sql);
    if(mysql_error()){
    $show_notification =1;
    $message = $config["error_26"]; //error
    }else{
    $show_notification =1;
    $message = $config["error_25"]; //request success
    $title =''; //reset
	$description =''; //reset
    }
}

}



//____________________________________________________________________________________________________________________________________________
//Create New Subcategories
//____________________________________________________________________________________________________________________________________________

if(isset($_POST['new_sub_category']) && isset($_POST['main_category'])){
$title = $_POST['title'];
$description = $_POST['description'];
$proceed = true;
$parent_channel_id = $_POST['main_category'];

//all filled in
if($title =='' || $description ==''){
$show_notification = 1;
$message = $config['fill_all_fields'];
$proceed = false;
}

//check if already exists
if($title != ''){
$sql = "SELECT sub_channel_id from sub_channels WHERE sub_channel_name = '$title'";
if(@mysql_num_rows(@mysql_query($sql)) >0){
$show_notification =1;
$message = $config['duplicate_item'];
$proceed = false;
}
}

//check if this if firsr sub cat - if yes assign all videos to new sub
$sql = "SELECT sub_channel_id FROM sub_channels WHERE parent_channel_id = $parent_channel_id";
if (mysql_num_rows(mysql_query($sql)) <= 0){
$forced_move = true;
}
//add to database
if($proceed == true){
$title_seo = seo_title($title);
$sql = "INSERT INTO sub_channels (sub_channel_name, sub_channel_name_seo, sub_channel_description, parent_channel_id, date_created) VALUES ('$title', '$title_seo', '$description', $parent_channel_id, NOW())";
@mysql_query($sql);
    if(mysql_error()){
    $show_notification =1;
    $message = $config["error_26"]; //error
    }else{
    $show_notification =1;
    $message = $config["error_25"]; //request success
	$title =''; //reset
	$description =''; //reset
    }
}


//check if this if firsr sub cat - if yes assign all videos to new sub
$sql = "SELECT sub_channel_id FROM sub_channels WHERE parent_channel_id = $parent_channel_id";
$query = mysql_query($sql);
if (mysql_num_rows($query) == 1){
$result = mysql_fetch_array($query);
$temp_subchannel_id = $result['sub_channel_id'];
$sql = "UPDATE videos SET sub_channel_id = $temp_subchannel_id WHERE channel_id = $parent_channel_id";
mysql_query($sql);
}


//set some vars as if posted
$_POST['load_subcategory'] = 'xyz';
}


//____________________________________________________________________________________________________________________________________________
//Edit Main Categories
//____________________________________________________________________________________________________________________________________________

if(isset($_POST['edit_category']) || isset($_POST['actual_edit_category']) ){

//get either ID for actual edit OR ID to first load category
if (isset($_POST['actual_edit_category'])){
$channel_id = mysql_real_escape_string($_POST['main_category_on_edit']);
}else{
$channel_id = mysql_real_escape_string($_POST['main_category']);
}

$edit_category_title = 'change me';
$edit_category =1; //show edit submit button

//get real details
$sql = "SELECT * FROM channels WHERE channel_id= $channel_id"; 
$result = @mysql_fetch_array(@mysql_query($sql));
$title = $result['channel_name'];
$description = $result['channel_description'];

//set submenu title
$current_title1 = $lang_edit_main_category.' ('.$title.')';

//get posted details if any
if (isset($_POST['actual_edit_category'])){
$title = $_POST['title'];
$description = $_POST['description'];
$proceed = true;

//all filled in
if($title =='' || $description ==''){
$show_notification = 1;
$message = $config['fill_all_fields'];
$proceed = false;
}


//check if already exists
if($title != ''){
$sql = "SELECT channel_id from channels WHERE channel_name = '$title'";
$result = (mysql_fetch_array(@mysql_query($sql)));
$matched_channel_id = $result['channel_id'];
if(@mysql_num_rows(@mysql_query($sql)) >0 && $matched_channel_id != $channel_id){
$show_notification =1;
$message = $config['duplicate_item'];
$proceed = false;
}
}

//add to database
if($proceed == true){
$title_seo = seo_title($title);
$sql = "UPDATE channels SET channel_name = '$title', channel_name_seo = '$title_seo', channel_description = '$description' WHERE channel_id = $channel_id";
@mysql_query($sql);

//Update every videos
$sql = "UPDATE videos SET channel = '$title' WHERE channel_id = $channel_id";
@mysql_query($sql);

    if(mysql_error()){
    $show_notification =1;
    $message = $config["error_26"]; //error
    }else{
    $show_notification =1;
    $message = $config["error_25"]; //request success
    $title =''; //reset
	$description =''; //reset
    }
}
}
}


//____________________________________________________________________________________________________________________________________________
//Edit Sub Categories
//____________________________________________________________________________________________________________________________________________

if(isset($_POST['edit_sub_category']) || isset($_POST['actual_edit_sub_category']) ){

//get either ID for actual edit OR ID to first load category
if (isset($_POST['actual_edit_sub_category'])){
$sub_channel_id = mysql_real_escape_string($_POST['sub_category_on_edit']);
}else{
$sub_channel_id = mysql_real_escape_string($_POST['sub_category']);
}

$edit_category_title = 'change me';
$edit_sub_category =1; //show edit submit button

//get real details
$sql = "SELECT * FROM sub_channels WHERE sub_channel_id= $sub_channel_id"; 
$result = @mysql_fetch_array(@mysql_query($sql));
$title = $result['sub_channel_name'];
$description = $result['sub_channel_description'];

//set submenu title
$current_title2 = $lang_edit_sub_category.' ('.$title.')';

//get posted details if any
if (isset($_POST['actual_edit_sub_category'])){
$title = $_POST['title'];
$description = $_POST['description'];
$proceed = true;

//all filled in
if($title =='' || $description ==''){
$show_notification = 1;
$message = $config['fill_all_fields'];
$proceed = false;
}


//check if already exists
if($title != ''){
$sql = "SELECT sub_channel_id from sub_channels WHERE sub_channel_name = '$title'";
$result = (mysql_fetch_array(@mysql_query($sql)));
$matched_sub_channel_id = $result['sub_channel_id'];
if(@mysql_num_rows(@mysql_query($sql)) >0 && $matched_sub_channel_id != $sub_channel_id){
$show_notification =1;
$message = $config['duplicate_item'];
$proceed = false;
}
}

//add to database
if($proceed == true){
$title_seo = seo_title($title);
$sql = "UPDATE sub_channels SET sub_channel_name = '$title', sub_channel_name_seo = '$title_seo', sub_channel_description = '$description' WHERE sub_channel_id = $sub_channel_id";
@mysql_query($sql);

    if(mysql_error()){
    $show_notification =1;
    $message = $config["error_26"]; //error
    }else{
    $show_notification =1;
    $message = $config["error_25"]; //request success
    $title =''; //reset
	$description =''; //reset
    }
}
}

//set some vars as if posted
$_POST['load_subcategory'] = 'xyz';
}


//____________________________________________________________________________________________________________________________________________
//Deleting Main Category
//____________________________________________________________________________________________________________________________________________

if(isset($_POST['delete_main_category'])){

$del_category_id = $_POST['main_category'];

//lets 'delete' the videos
$sql = "UPDATE videos SET approved ='pendingdelete' WHERE channel_id = $del_category_id";
@mysql_query($sql);

//lets 'delete' the sub categories
$sql = "DELETE FROM channels WHERE channel_id = $del_category_id";
@mysql_query($sql);

//lets 'delete' the main categories
$sql = "DELETE FROM sub_channels WHERE parent_channel_id = $del_category_id";
@mysql_query($sql);

    $show_notification =1;
    $message = $config["error_25"]; //request success
}


//____________________________________________________________________________________________________________________________________________
//Deleting Sub Category
//____________________________________________________________________________________________________________________________________________

if(isset($_POST['delete_sub_category'])){

$del_category_id = $_POST['sub_category'];

//lets 'delete' the videos
$sql = "UPDATE videos SET approved ='pendingdelete' WHERE sub_channel_id = $del_category_id";
@mysql_query($sql);

//lets 'delete' the main categories
$sql = "DELETE FROM sub_channels WHERE sub_channel_id = $del_category_id";
@mysql_query($sql);

    $show_notification =1;
    $message = $config["error_25"]; //request success

}



//____________________________________________________________________________________________________________________________________________
//Load Main Categories
//____________________________________________________________________________________________________________________________________________

$sql  = "SELECT * FROM channels ORDER BY channel_name ASC";
$query = mysql_query($sql);
//get list
$has_category =0; //no categories found
while($result = mysql_fetch_array($query)){
$this_channel_id = $result['channel_id'];
$sql = "SELECT * FROM videos WHERE channel_id = $this_channel_id AND approved='yes'";
$this_video_count = @mysql_num_rows(@mysql_query($sql));

$list = '<option value="'.$result['channel_id'].'">&nbsp;&nbsp;&nbsp;'.$result['channel_name'].'&nbsp;('.$this_video_count.')&nbsp;&nbsp;</option>';
$category_list = $category_list.$list;
$has_category = 1; //categories found
}




//____________________________________________________________________________________________________________________________________________
//Load Subcategories
//____________________________________________________________________________________________________________________________________________
if(isset($_POST['load_subcategory']) && $_POST['main_category'] != ''){
$category_id = mysql_real_escape_string($_POST['main_category']);

//set inner template
$inner_template1 = "templates/inner_categories_videos_sub.html";//subcats

//get main category name
$sql  = "SELECT channel_name FROM channels WHERE channel_id = $category_id";
$result = mysql_fetch_array(mysql_query($sql));
$channel_display_name = $result['channel_name'];

//select all subcat for this category
$sql  = "SELECT * FROM sub_channels WHERE parent_channel_id = $category_id ORDER BY sub_channel_name ASC";
$query = mysql_query($sql);
//get list
$list ='';
$show_subcategory = 1; //main tr
$show_has_subcategory =0; //none found
while($result = mysql_fetch_array($query)){
$this_channel_id = $result['sub_channel_id'];
$sql = "SELECT * FROM videos WHERE sub_channel_id = $this_channel_id AND approved='yes'";
$this_video_count = @mysql_num_rows(@mysql_query($sql));

$list = '<option value="'.$result['sub_channel_id'].'">&nbsp;&nbsp;&nbsp;'.$result['sub_channel_name'].'&nbsp;('.$this_video_count.')&nbsp;&nbsp;</option>';
$sub_category_list = $sub_category_list.$list;
$show_subcategory = 1;
$show_has_subcategory =1;//found
}
}



////////////////////////////////
//display form with error message
////////////////////////////////
$show_content_menu = 0;//display top tabs as set in /includes/menuloader
$template = "templates/main.html";
$TBS = new clsTinyButStrong;
$TBS->NoErr = true;// no more error message displayed.
$TBS->LoadTemplate("$template");
$TBS->Render = TBS_OUTPUT;
$TBS->Show();
@mysql_close();
die();




?>
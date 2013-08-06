<?php

/**
* @author PHPmotion.com
* @copyright 2008
*/

include_once ('../classes/config.php');
include_once ('includes/inc.stats.php');
include_once ('includes/functions.php');
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

//____________________________________________________________________________________________________________________________________________
//Perfom Videos Move
//____________________________________________________________________________________________________________________________________________

if(isset($_POST['move_videos'])){

$proceed = true; //reset

//check if both FROM and TO are selected
if ($_POST['move_from'] =='' ||  $_POST['move_to'] ==''){
$show_notification = 1;
$message = $config["select_both_from_to"];
$proceed = false;
}else{

//get both from and to
list($main_category_from, $sub_category_from) = @split('_', $_POST['move_from']);
list($main_category_to, $sub_category_to) = @split('_', $_POST['move_to']);

if($main_category_from !='' && $sub_category_from != '' && $main_category_to !='' && $sub_category_to !=''){
$proceed = true;
}else{
$proceed = false;
$show_notification =1;
$message = $config["error_26"]; //error
}
}


//Update DB
///////////

if ($proceed == true){

$sql = "UPDATE videos SET channel_id = $main_category_to, sub_channel_id = $sub_category_to WHERE channel_id = $main_category_from AND sub_channel_id = $sub_category_from";
@mysql_query($sql);

    if(mysql_error()){
    $show_notification =1;
    $message = $config["error_26"]; //error
    }else{
    $show_notification =1;
    $message = $config["error_25"]; //request success
    }
}

}

//____________________________________________________________________________________________________________________________________________
//Load Categories
//____________________________________________________________________________________________________________________________________________

$sql  = "SELECT * FROM channels ORDER BY channel_name ASC";
$query = mysql_query($sql);

while($result = mysql_fetch_array($query)){
$main_category_name = $result['channel_name'];
$main_category_id = $result['channel_id'];

//get overall videos
$sql1 = "SELECT * FROM videos WHERE channel_id = $main_category_id AND approved='yes'";
$this_video_count = @mysql_num_rows(@mysql_query($sql1));


//get sub categories
////////////////////
$sql2  = "SELECT * FROM sub_channels WHERE parent_channel_id = $main_category_id ORDER BY sub_channel_name ASC";
$query2 = mysql_query($sql2);

while($result2 = mysql_fetch_array($query2)){
$sub_category_name = $result2['sub_channel_name'];
$sub_category_id = $result2['sub_channel_id'];

$sql3 = "SELECT * FROM videos WHERE sub_channel_id = $sub_category_id AND approved='yes'";
$this_video_count = @mysql_num_rows(@mysql_query($sql3));

$list = '<option value="'.$main_category_id.'_'.$sub_category_id.'">&nbsp;'.$main_category_name.'/'.$sub_category_name.'&nbsp;('.$this_video_count.')&nbsp</option>';
$category_list = $category_list.$list;
$has_subs = 1;
}

if($has_subs != 1){
$list = '<option value="'.$main_category_id.'_99999">&nbsp;'.$main_category_name.'/'.$sub_category_name.'&nbsp;('.$this_video_count.')&nbsp</option>';
$category_list = $category_list.$list;
}
$has_subs = 0;//reset for next loop#
$sub_category_name = '';
}


////////////////////////////////
//display form with error message
////////////////////////////////
$show_content_menu = 0;//display top tabs as set in /includes/menuloader
$template = "templates/main.html";
$inner_template1 = "templates/inner_categories_move_videos.html";//default
$TBS = new clsTinyButStrong;
$TBS->NoErr = true;// no more error message displayed.
$TBS->LoadTemplate("$template");
$TBS->Render = TBS_OUTPUT;
$TBS->Show();
@mysql_close();
die();

?>

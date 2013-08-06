<?php

include_once ("../classes/config.php");
include_once ("includes/inc.stats.php");
include_once ("includes/functions.php");
include_once ('includes/login_check.php');


//Menu Loaders
///////////////
$top_menu = '';
$dashboard_header = $lang_system_settings;
include_once ("includes/menuloader.php");


/////////////////////
//defaults settings /
/////////////////////
$show_hide = 1;
$show_notification = 0;
$todays_date = $config["date_format"];
$base_url = $config['site_base_url'];
$limit = $config["admin_maximum_display"];
$result_active = array();
$proceed = true;

////////////////
//check for type
////////////////

if (isset($_POST['type'])) {

    $type = $_POST['type'];
}
else {
    $type = $_GET['type'];
}

if ($type == 1 | $type == 2 || $type == 3)
{
    $show_t = $type;
}
else {
    $show_t = 1;
}

//Check Data >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>

if(isset($_POST['update'])){

    //check invalid characters
    $result = array();
    foreach ($_POST as $key => $value) {
        if (@eregi("^.*['\"\?`&\(\)].*$", $value)) {
        	$show_notification =1;
            $message = $config["invalid_email_text"];  //'You have entered an in valid character';
            $proceed = false;
        }
        if (!isset($value) || ($value == "")) {
        	$show_notification =1;
            $message = $config["fill_all_fields"]; //fill in all fields
            $proceed = false;
        }

        if ($key== 'admin_maximum_display' || $key== 'flagging_threshold_limits' || $key== 'seemore_limits_wide' ||
		$key== 'config_recent_title_length' || $key== 'maximum_size' || $key== 'search_page_limits' ||
		$key== 'see_more_limits' || $key== 'groups_home_video_limit' || $key== 'groups_main_limit' || $key== 'comment_page_limits' ||
		$key== 'video_buffer_time') {

			if(!is_numeric($value)){
			$show_notification =1;
			$message = $lang_invalid_character; //fill in all fields
            $proceed = false;
        }




    }

	}

$result[]= $_POST;

// Update Dbase
//////////////

if ($proceed == true){

    foreach ($_POST as $key => $value) {

		$$key = mysql_real_escape_string($value);
		}


$sql = "UPDATE general_settings SET

site_name ='$site_name',
site_base_url ='$site_base_url',
date_format ='$date_format',
path_to_ffmpeg ='$path_to_ffmpeg',
path_to_flvtool2 ='$path_to_flvtool2',
path_to_mencoder ='$path_to_mencoder',
path_to_php ='$path_to_php',
notifications_from_email ='$notifications_from_email',
from_system_name ='$from_system_name',
enable_audio ='$enable_audio',
log_encoder ='$log_encoder',
debug_mode ='$debug_mode',
allow_download ='$allow_download',
allow_multiple_video_comments ='$allow_multiple_video_comments',
auto_approve_videos ='$auto_approve_videos',
auto_play_index ='$auto_play_index',
auto_play ='$auto_play',
delete_avi ='$delete_avi',
delete_original ='$delete_original',
auto_approve_profile_photo ='$auto_approve_profile_photo',
admin_maximum_display =$admin_maximum_display,
flagging_threshold_limits =$flagging_threshold_limits,
seemore_limits_wide =$seemore_limits_wide,
config_recent_title_length ='$config_recent_title_length',
maximum_size =$maximum_size,
maximum_size_human_readale ='$maximum_size_human_readale',
search_page_limits =$search_page_limits,
see_more_limits =$see_more_limits,
groups_home_video_limit =$groups_home_video_limit,
groups_main_limit =$groups_main_limit,
comment_page_limits =$comment_page_limits,
video_buffer_time =$video_buffer_time";
@mysql_query($sql);

    //check for errors writing to database
    if (@mysql_error()) {
    	$show_notification =1;
        $message = $config["general_error"];
        $proceed = false;
    }else{

	//Write settings to file /classes/settings.php
	//////////////////////////////////////////////
    include_once ('configbuilder.php');
    $file = true;//resets error test
    $base_path = installation_paths();
    $myFile = $base_path . '/classes/settings.php';
    if(!is_writable($base_path . '/classes/')){ //check if dir can be written to
    	$file = false;
    }else{
    @unlink($myFile); //if writable we can delete file confidently.
    }
    $fh = @fopen($myFile, 'wb') or $file = false;
    @fwrite($fh, $final) or $file = false;
    @fclose($fh) or $file = false;
    if ($file == false) {
    	$show_notification =1;
        $message = $config["file_write_error"].'/classes'; //generic write error with folder appended to end of message
	echo $myFile;
	}else{
        $show_notification =1;
    	$message = $config["error_25"]; //request success
    	}
    }
}
}



//Display Results >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>


if ($proceed == true) {

    $result = array();
    $sql = "SELECT * FROM general_settings";
    $query = @mysql_query($sql);
    while ($result1 = @mysql_fetch_array($query)) {
        $result[] = $result1;
    }
}


////////////////////////////////
//display form with error message
////////////////////////////////
$show_content_menu = 0; //display top tabs as set in /includes/menuloader
$template = "templates/main.html";
$inner_template1 = "templates/inner_settings.htm";//middle of page
$TBS = new clsTinyButStrong;
$TBS->NoErr = true;// no more error message displayed.
$TBS->LoadTemplate("$template");
$TBS->MergeBlock('adminblk', $result);
$TBS->Render = TBS_OUTPUT;
$TBS->Show();
@mysql_close();
die();

?>
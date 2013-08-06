<?php

include_once ("../classes/config.php");
include_once ("includes/inc.stats.php");
include_once ("includes/functions.php");
include_once ('includes/login_check.php');

//Menu Loaders
///////////////
$top_menu = '';
//$side_menu = 'settings';
$dashboard_header = $lang_system_settings;


/////////////////////
//defaults settings /
/////////////////////
$show_hide = 1;
$show_notification = 0;
$todays_date = $config["date_format"];
$base_url = $config['site_base_url'];
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

if ($type == 1 )
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
	}

$result[]= $_POST;

// Update Dbase
//////////////

if ($proceed == true){

    foreach ($_POST as $key => $value) {

		$$key = mysql_real_escape_string($value);
		}


$sql = "UPDATE video_settings SET

video_watermark ='$video_watermark',
video_watermark_place ='$video_watermark_place',
video_resize ='$video_resize',
video_convert_pass ='$video_convert_pass',
video_ffmpeg_size ='$video_ffmpeg_size',
video_ffmpeg_bit_rate ='$video_ffmpeg_bit_rate',
video_ffmpeg_audio_rate ='$video_ffmpeg_audio_rate',
video_ffmpeg_high_quality ='$video_ffmpeg_high_quality',
video_ffmpeg_hq ='$video_ffmpeg_hq',
video_ffmpeg_hq_size ='$video_ffmpeg_hq_size',
video_ffmpeg_qmax ='$video_ffmpeg_qmax',
video_mencoder_vbitrate ='$video_mencoder_vbitrate',
video_mencoder_scale ='$video_mencoder_scale',
video_mencoder_srate ='$video_mencoder_srate',
video_mencoder_audio_rate ='$video_mencoder_audio_rate'";
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
	}else{
        $show_notification =1;
    	$message = $config["error_25"]; //request success
    	}
    }
}
}


//Restore Default Settings >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
if($_GET['id'] ==1){

$sql= "UPDATE video_settings SET
video_watermark = 'no',
video_watermark_place ='right_bottom',
video_resize ='yes',
video_convert_pass ='1',
video_ffmpeg_size ='560x420',
video_ffmpeg_bit_rate ='800k',
video_ffmpeg_audio_rate ='44100',
video_ffmpeg_high_quality ='no',
video_ffmpeg_hq ='-sameq',
video_ffmpeg_hq_size ='720x480',
video_ffmpeg_qmax ='3',
video_mencoder_vbitrate ='800',
video_mencoder_scale ='560:420',
video_mencoder_srate ='22050',
video_mencoder_audio_rate='56'";
@mysql_query($sql);
    $show_notification = 1;
    $message = notifications(1);

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
	}else{
        $show_notification =1;
    	$message = $config["error_25"]; //request success
    	}

}


//Display Results >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>

//MENU LOADER
/////////////

include_once ("includes/menuloader.php");

if ($proceed == true) {

    $result = array();
    $sql = "SELECT * FROM video_settings";
    $query = @mysql_query($sql);
    while ($result1 = @mysql_fetch_array($query)) {

        $result[] = $result1;
        $selected = $result1['video_watermark'];

    }
}

if ( $selected == 'yes' ) {
	$video_watermark_yes = "selected";
	$video_watermark_no = "";

} else {
	$video_watermark_yes = "";
	$video_watermark_no = "selected";
}


////////////////////////////////
//display form with error message
////////////////////////////////
$show_content_menu = 0; //display top tabs as set in /includes/menuloader
$template = "templates/main.html";
$inner_template1 = "templates/inner_video_settings.htm";//middle of page
$TBS = new clsTinyButStrong;
$TBS->NoErr = true;// no more error message displayed.
$TBS->LoadTemplate("$template");
$TBS->MergeBlock('adminblk', $result);
$TBS->Render = TBS_OUTPUT;
$TBS->Show();
@mysql_close();
die();

?>
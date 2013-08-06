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

if (isset($_POST['update'])) {

foreach($_POST as $key=>$value){

if ($key == 'update' || $key == 'type'){
//do nothing
}else{

//build sql query

$sql_part = $sql_part.$key." = '".$value."',";

}
}
$sql_part = substr($sql_part,0,-1);

//update dbase
$sql = "UPDATE features_settings SET $sql_part";
@mysql_query($sql);
//notifications
$show_notification =1;
$message = notifications(1);


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
    if(!is_writable($base_path . '/classes')){ //check if dir can be written to
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


//Restore Default Settings >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
if($_GET['id'] ==1){

$sql = "UPDATE features_settings SET 

audio ='yes',
images ='yes',
blogs ='yes',
video_comments ='yes',
blog_comments ='yes',
audio_comments ='yes',
image_comments ='yes',
profile_comments ='yes',
stats ='yes',
confirmation_email ='yes',
custome_profile ='no'";
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
	echo $myFile;
	}else{
        $show_notification =1;
    	$message = $config["error_25"]; //request success
    	}
    	
}


//Display Results >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>


if ($proceed == true) {

    $sql = "SELECT * FROM features_settings";
    $query = @mysql_query($sql);
    $result = @mysql_fetch_array($query);
   
   foreach($result as $key=>$value){
   $$key = $value;
   
   if($value == 'yes'){
   $select = $key.'_yes';
   $$select = 'selected';
   }else{
   $select = $key.'_no';
   $$select = 'selected';     
   }    
}
}

////////////////////////////////
//display form with error message
////////////////////////////////
$template = "templates/main.html";
$inner_template1 = "templates/inner_enabled_features_settings.htm";//middle of page
$TBS = new clsTinyButStrong;
$TBS->NoErr = true;// no more error message displayed.
$TBS->LoadTemplate("$template");
//$TBS->MergeBlock('adminblk', $result);
$TBS->Render = TBS_OUTPUT;
$TBS->Show();
@mysql_close();
die();

?>
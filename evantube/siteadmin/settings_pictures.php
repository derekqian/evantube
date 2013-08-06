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

if(isset($_POST['update'])){

    //check invalid characters
    $result = array();
    foreach ($_POST as $key => $value) {
        if (!is_numeric($value)) {        	
        	if($key =='type' || $key == 'update'){
        		//skip - do nothing
        	}else{
        	$show_notification =1;
            $message = $config["invalid_email_text"];  //'You have entered an in valid character';
            $proceed = false;
            }
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


$sql = "UPDATE image_settings SET 

album_pic_maxwidth ='$album_pic_maxwidth',
album_pic_maxheight ='$album_pic_maxheight',
album_pic_minwidth ='$album_pic_minwidth',
album_pic_minheight ='$album_pic_minheight',
album_pic_maxsize ='$album_pic_maxsize',
member_max_albums ='$member_max_albums',
pictures_max_per_album ='$pictures_max_per_album'";
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
}


//Restore Default Settings >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
if($_GET['id'] ==1){

$sql = "UPDATE image_settings SET 

album_pic_maxwidth ='600',
album_pic_maxheight ='600',
album_pic_minwidth ='300',
album_pic_minheight ='300',
album_pic_maxsize ='200000',
member_max_albums ='6',
pictures_max_per_album ='50'";
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

    $result = array();
    $sql = "SELECT * FROM image_settings";
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
$inner_template1 = "templates/inner_image_settings.htm";//middle of page
$TBS = new clsTinyButStrong;
$TBS->NoErr = true;// no more error message displayed.
$TBS->LoadTemplate("$template");
$TBS->MergeBlock('adminblk', $result);
$TBS->Render = TBS_OUTPUT;
$TBS->Show();
@mysql_close();
die();

?>
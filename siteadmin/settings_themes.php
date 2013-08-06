<?php
include_once ("../classes/config.php");
include_once ("includes/inc.stats.php");
include_once ("includes/functions.php");
include_once ('includes/login_check.php');


//Menu Loaders
///////////////
$top_menu = '';
$dashboard_header = $lang_themes;
include_once ("includes/menuloader.php");

/////////////////////
//defaults settings /
/////////////////////
$show_hide = 1;
$show_notification = 0;
$todays_date = $config["date_format"];
$base_url = $config['site_base_url'];
$result_active = array();
$base_path = installation_paths();

////////////////
//check for type
////////////////

if (isset($_POST['type'])) {

    $type = $_POST['type'];
} else {
    $type = $_GET['type'];
}

if ($type == 1) {
    $show_t = $type;
} else {
    $show_t = 1;
}


//Check Data >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>

if (isset($_POST['update'])) {


    $theme = $POST_['theme'];
    $language = $POST_['language'];

    if ($theme == '') $theme = 'default';

    if ($language == '') $language = 'english';

    //update dbase
    $sql = "UPDATE features_settings SET theme ='$theme', language='$language' ";
    @mysql_query($sql);
    //notifications
    $show_notification = 1;
    $message = notifications(1);

}


//Find themes>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>

$themesdir = @opendir($base_path . '/themes/');

$passed_themes = '';
$failed_themes = '';

while (($file = @readdir($themesdir)) !== false) {

	$found_path = $base_path . '/themes/' . $file;
    	$file_type = @filetype($found_path); //is it dir of file

    	if ($file != '.' && $file != '..' && $file_type == 'dir') {
		//start checking for needed dirs and files
        	$member_css = $found_path . '/member_css/memberdefault.css';

        	if( !function_exists('scandir') ) {
        		function scandir($directory, $sorting_order = 0) {
        			$dh  = opendir($directory);
        			while( false !== ($filename = readdir($dh)) ) {
            			$files[] = $filename;
        			}
       	 		if( $sorting_order == 0 ) {
            			sort($files);
        			} else {
            			rsort($files);
        			}
        		return($files);
    			}
		}

        	$member_images = @count(@scandir($found_path . '/member_images/')); //count number of images found
        	$templates = @count(@scandir($found_path . '/templates/')); //count number of templates found

        	// Get invalid theme
        	if ($templates < 40 || $member_images < 5 || !file_exists($member_css)) {
            	$failed_themes = $failed_themes . '<option value="' . $file . '">' . $file . '</option>';
            	$show_failed = 1; //display in html

        	// Get valid theme
		} else {
            	$passed_themes = $passed_themes . '<option value="' . $file . '">' . $file . '</option>';
            }
	}
}

@closedir($themesdir);

//Find Language>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>

$languagedir = @opendir($base_path . '/lang/');

while (($file = @readdir($languagedir)) !== false) {
    $found_path = $base_path . '/lang/' . $file;
    $file_type = @filetype($found_path); //is it dir of file
    $file_extension = @end(@explode('.', $file));
    $file_parts = @explode('.', $file);
    $language_name = $file_parts[0];

    if ($file != '.' && $file != '..' && $file_type == 'file' && $file_extension =='php') {
            $passed_language = $passed_language . '<option value="' . $language_name . '">' . $language_name .
                '</option>';
    }
}
@closedir($languagedir);



////////////////////////////////
//Change Theme or Language
///////////////////////////////

//update theme
if($_POST['theme'] != ''){
$new_theme = $_POST['theme'];
$sql = "UPDATE features_settings SET theme='$new_theme'";
@mysql_query($sql);


        //delete all old theme defaul images using wildcard search  
        $old_member_images = $base_path.'/addons/customprofile/member_images/MP_*.*';
        foreach (@glob($old_member_images) as $filename) {
            @unlink($filename);
        }
        
        //delete all old theme defaul images using wildcard search  
        $new_member_images = $base_path.'/themes/'.$new_theme.'/member_images/MP_*.*';
        foreach (@glob($new_member_images) as $filename) {
        $just_file = end(explode('/', $filename));
        $new_location = $base_path.'/addons/customprofile/member_images/'.$just_file;
            @copy($filename,$new_location);
        }
		        

    //check for errors writing to database
    if (@mysql_error()) {
    	$show_notification =1;
        $message = $config["general_error"];
        $proceed = false;
    }else{
	$proceed = true;
	}
}

//update theme
if($_POST['language'] != ''){
$new_language = $_POST['language'];
$sql = "UPDATE features_settings SET language='$new_language'";
@mysql_query($sql);

    //check for errors writing to database
    if (@mysql_error()) {
    	$show_notification =1;
        $message = $config["general_error"];
        $proceed = false;
    }else{
	$proceed = true;
	}
  }


////////////////////////////////
// Wrie settings file
///////////////////////////////

if ($proceed == true){
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

////////////////////////////////
// get current theme and Language
/////////////////////////////////


$sql = "SELECT theme, language FROM features_settings";
$result = @mysql_fetch_array(mysql_query($sql));

$current_theme = $result['theme'];
$current_language = $result['language'];



////////////////////////////////////////////////
// GET LATEST THEMES FROM PHPMOTIONTEMPLATES.COM
////////////////////////////////////////////////

if ($fp = fopen('http://phpmotiontemplates.com/template-store.html?page=shop.feed&category_id=4', 'r')) {

	$rsscontent = '';

   	// read line by line and store
   	while ($line = fread($fp, 1024)) {
      	$rsscontent .= $line;
   	}

   	//try and get latest thumbs
   	if (preg_match_all('/src=&quot;(.*?)&quot;/', $rsscontent, $regs)) {
		$show_thumbs = 1; //for html
		$thumb1 = $regs[1][0];
		$thumb2 = $regs[1][1];
		$thumb3 = $regs[1][2];
   	}
}



////////////////////////////////
//display form with error message
////////////////////////////////
$template = "templates/main.html";
$inner_template1 = "templates/inner_themes.htm"; //middle of page
$TBS = new clsTinyButStrong;
$TBS->NoErr = true; // no more error message displayed.
$TBS->LoadTemplate("$template");
//$TBS->MergeBlock('adminblk', $result);
$TBS->Render = TBS_OUTPUT;
$TBS->Show();
@mysql_close();
die();

?>
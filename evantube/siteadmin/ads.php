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
$dashboard_header = $lang_ads;

/////////////////////
//defaults settings /
/////////////////////
$show_hide = 1;
$show_notification = 0;
$todays_date = $config["date_format"];
$base_url = $config['site_base_url'];

//basic setting>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>


//check if form has been submitted
    if ($_POST['update_ads'] != "") {

	$ads_top = mysql_real_escape_string($_POST['ads_top']);
	$ads_bottom = mysql_real_escape_string($_POST['ads_bottom']);
	$ads_left = mysql_real_escape_string($_POST['ads_left']);
	$ads_right = mysql_real_escape_string($_POST['ads_right']);
            $sql = "UPDATE adverts SET
			ads_top = '$ads_top',
			ads_bottom = '$ads_bottom',
			ads_left = '$ads_left',
			ads_right = '$ads_right'
			WHERE preloaded = 'yes'";

            @mysql_query($sql);

            //dislay nitification
            $show_notification = 1;
            $message = $config['error_25']; //success
}




//get existing ads

$sql = "SELECT * FROM adverts WHERE preloaded = 'yes'";
$query = @mysql_query($sql);
while ($result = mysql_fetch_array($query)){

	$ads_top = $result['ads_top'];
	$ads_bottom = $result['ads_bottom'];
	$ads_left = $result['ads_left'];
	$ads_right = $result['ads_right'];
$ads_home_right = $result['ads_home_right'];

	}



//MENU LOADER
/////////////

include_once ("includes/menuloader.php");

////////////////////////////////
//display form with error message
////////////////////////////////
$template = "templates/main.html";
$inner_template1 = "templates/inner_ads.html";//middle of page
$TBS = new clsTinyButStrong;
$TBS->NoErr = true;// no more error message displayed.
$TBS->LoadTemplate("$template");
$TBS->Render = TBS_OUTPUT;
$TBS->Show();
@mysql_close();
die();



?>
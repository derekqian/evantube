<?php


include_once ("../classes/config.php");
include_once ("includes/inc.stats.php");
include_once ("includes/functions.php");
include_once ('includes/login_check.php');

//Menu Loaders
///////////////
$top_menu = '';
$side_menu = 'settings';
$dashboard_header = $lang_latest_news;

/////////////////////
//defaults settings /
/////////////////////
$show_hide = 1;
$show_list = 2;
$show_notification = 0;
$todays_date = $config["date_format"];
$base_url = $config['site_base_url'];
$base_path = installation_paths();


//Check id for get
//////////////////
$newsid = @mysql_real_escape_string($_GET['id']);
$type = @mysql_real_escape_string($_GET['type']);

if ($newsid != '' && $type != ''){
$sql = "SELECT * FROM news_flash WHERE news_id = $newsid";
$query = @mysql_query($sql);
$result1 = 	@mysql_fetch_array($query);
$publish = $result1['publish'];

//UPDATE Publish status
if ($publish == 'yes'){
$sql = "UPDATE news_flash SET publish = 'no' WHERE news_id = $newsid";
}else{
$sql = "UPDATE news_flash SET publish = 'yes' WHERE news_id = $newsid";
}
@mysql_query($sql);

if(@mysql_error()){
$show_notification =1;
$message = $config["error_26"]; //error
}else{
$show_notification =1;
$message = $config["error_25"]; //success
}
}

//DELETE new flash item
if ($newsid != '' && $type == ''){
$sql = "DELETE FROM news_flash WHERE news_id = $newsid";
$query = @mysql_query($sql);
if(@mysql_error()){
$show_notification =1;
$message = $config["error_26"]; //error
}else{
$show_notification =1;
$message = $config["error_25"]; //success
}
}



//Get All news items
////////////////////

$sql = "SELECT * FROM news_flash";
$query = @mysql_query($sql);
$result = array();
while($fetch = mysql_fetch_array($query)){
$result[] = $fetch;
$show_list = 1;
}

////////////////////////////////
//display form with error message
////////////////////////////////
$template = "templates/main.html";
$inner_template1 = "templates/inner_news.html";//middle of page
$TBS = new clsTinyButStrong;
$TBS->NoErr = true;// no more error message displayed.
$TBS->LoadTemplate("$template");
$TBS->MergeBlock('adminnews', $result);
$TBS->Render = TBS_OUTPUT;
$TBS->Show();
@mysql_close();
die();

?>